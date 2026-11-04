<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAddonItemsTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $owner;
    private User $designer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Gemini Art - Cabang Keturen',
            'code' => 'KTR',
            'address' => 'Jl. Veteran No. 10',
            'is_active' => true,
        ]);

        $this->owner = User::create([
            'name' => 'Budi Pratama (Owner)',
            'email' => 'owner@geminiart.test',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'branch_id' => null,
            'is_active' => true,
        ]);

        $this->designer = User::create([
            'name' => 'Amanda Designer',
            'email' => 'amanda@geminiart.test',
            'password' => bcrypt('password'),
            'role' => 'designer',
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);
    }

    public function test_can_create_design_order_with_addon_items(): void
    {
        $payload = [
            'branch_id' => $this->branch->id,
            'customer_name' => 'Kopi Senja Bahagia',
            'customer_phone' => '081234567890',
            'title' => 'X-Banner Promo Grand Opening',
            'dimensions' => '60 x 160 cm',
            'material_notes' => 'Bahan Albatros Doff',
            'brief_notes' => 'Warna dominan cokelat krem.',
            'designer_id' => $this->designer->id,
            'main_price' => '75000',
            'total_amount' => '120000', // 75k + 35k + 10k
            'paid_amount' => '60000', // DP 50%
            'payment_method' => 'qris',
            'items' => [
                [
                    'name' => 'Rangka X-Banner Fiber Standar',
                    'qty' => 1,
                    'price' => '35000',
                ],
                [
                    'name' => 'Mata Ayam Tambahan (8 Titik)',
                    'qty' => 1,
                    'price' => '10000',
                ],
            ],
        ];

        $response = $this->actingAs($this->owner)->post(route('orders.store-design'), $payload);
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order->id));
        $this->assertEquals('Kopi Senja Bahagia', $order->customer_name);
        $this->assertEquals(120000, (float) $order->total_amount);
        $this->assertEquals(60000, (float) $order->paid_amount);
        $this->assertEquals('partial_dp', $order->payment_status);

        // Verify OrderItems: 1 main service + 2 addons = 3 total items
        $this->assertCount(3, $order->items);

        $mainItem = $order->items->first();
        $this->assertEquals('X-Banner Promo Grand Opening', $mainItem->item_name);
        $this->assertEquals(1, $mainItem->qty);
        $this->assertEquals(75000, (float) $mainItem->unit_price);
        $this->assertEquals(75000, (float) $mainItem->subtotal);

        $addon1 = $order->items->get(1);
        $this->assertEquals('Rangka X-Banner Fiber Standar', $addon1->item_name);
        $this->assertEquals(1, $addon1->qty);
        $this->assertEquals(35000, (float) $addon1->unit_price);
        $this->assertEquals(35000, (float) $addon1->subtotal);

        $addon2 = $order->items->get(2);
        $this->assertEquals('Mata Ayam Tambahan (8 Titik)', $addon2->item_name);
        $this->assertEquals(10000, (float) $addon2->subtotal);

        // Verify CashMovement
        $cashMovement = CashMovement::where('order_id', $order->id)->first();
        $this->assertNotNull($cashMovement);
        $this->assertEquals(60000, (float) $cashMovement->amount);
        $this->assertEquals('qris', $cashMovement->payment_method);
        $this->assertEquals('order_dp', $cashMovement->category);
    }

    public function test_invoice_renders_all_addon_items_properly(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->owner->id,
            'order_number' => 'ORD-KTR-202611-9999',
            'customer_name' => 'Resto Ayam Bakar',
            'customer_phone' => '081299998888',
            'order_type' => 'design_printing',
            'total_amount' => 150000,
            'paid_amount' => 150000,
            'payment_status' => 'paid',
            'work_status' => 'in_progress',
        ]);

        $order->designJob()->create([
            'title' => 'Spanduk Menu Ayam Bakar 3x1',
            'dimensions' => '3 x 1 Meter',
            'material_notes' => 'Flexi Korea 380gr',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'item_name' => 'Spanduk Menu Ayam Bakar 3x1',
            'qty' => 1,
            'unit_price' => 100000,
            'subtotal' => 100000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'item_name' => 'Selongsong Bambu Atas Bawah',
            'qty' => 2,
            'unit_price' => 15000,
            'subtotal' => 30000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'item_name' => 'Jasa Antar & Pasang Lokasi',
            'qty' => 1,
            'unit_price' => 20000,
            'subtotal' => 20000,
        ]);

        $response = $this->actingAs($this->owner)->get(route('orders.invoice', $order->id));
        $response->assertStatus(200);

        // Verify all items are rendered on the invoice
        $response->assertSee('Spanduk Menu Ayam Bakar 3x1');
        $response->assertSee('Selongsong Bambu Atas Bawah');
        $response->assertSee('Jasa Antar &amp; Pasang Lokasi', false);
        $response->assertSee('Layanan Utama');
        $response->assertSee('Alat / Tambahan');
        $response->assertSee('150.000');
    }

    public function test_order_without_addons_still_works_backward_compatible(): void
    {
        $payload = [
            'branch_id' => $this->branch->id,
            'customer_name' => 'Pelanggan Brosur Biasa',
            'title' => 'Brosur A5 1 Rim',
            'total_amount' => '85000',
            'paid_amount' => '85000',
            'payment_method' => 'cash',
        ];

        $response = $this->actingAs($this->owner)->post(route('orders.store-design'), $payload);
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order->id));
        $this->assertCount(1, $order->items);
        $this->assertEquals(85000, (float) $order->total_amount);

        // Invoice should render cleanly
        $invResponse = $this->actingAs($this->owner)->get(route('orders.invoice', $order->id));
        $invResponse->assertStatus(200);
        $invResponse->assertSee('Brosur A5 1 Rim');
        $invResponse->assertSee('85.000');
    }

    public function test_can_create_design_order_with_custom_print_qty_and_unit(): void
    {
        $payload = [
            'branch_id' => $this->branch->id,
            'customer_name' => 'Kedai Kopi Melati',
            'customer_phone' => '085642501934',
            'title' => 'Spanduk Warung Outdoor 3x1',
            'dimensions' => '3 x 1 meter',
            'material_notes' => 'Flexi Korea 380gr',
            'main_qty' => 3,
            'main_unit' => 'Lembar',
            'main_unit_price' => '75000',
            'main_price' => '225000',
            'total_amount' => '225000',
            'paid_amount' => '225000',
            'payment_method' => 'cash',
        ];

        $response = $this->actingAs($this->owner)->post(route('orders.store-design'), $payload);
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order->id));

        $mainItem = $order->items->first();
        $this->assertEquals('Spanduk Warung Outdoor 3x1', $mainItem->item_name);
        $this->assertEquals(3, $mainItem->qty);
        $this->assertEquals('Lembar', $mainItem->unit);
        $this->assertEquals(75000, (float) $mainItem->unit_price);
        $this->assertEquals(225000, (float) $mainItem->subtotal);

        // Verify invoice renders quantity and prices
        $invResponse = $this->actingAs($this->owner)->get(route('orders.invoice', $order->id));
        $invResponse->assertStatus(200);
        $invResponse->assertSee('Spanduk Warung Outdoor 3x1');
        $invResponse->assertSee('75.000');
        $invResponse->assertSee('225.000');
    }
}
