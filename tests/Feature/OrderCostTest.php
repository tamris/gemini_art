<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCostTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $cashier;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Cabang Sudirman',
            'code' => 'SDR',
            'address' => 'Jl. Jend Sudirman No. 12',
            'phone' => '081234567890',
        ]);

        $this->owner = User::create([
            'name' => 'Pak Owner',
            'email' => 'owner@geminiart.test',
            'role' => 'owner',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->cashier = User::create([
            'name' => 'Mbak Kasir',
            'email' => 'kasir@geminiart.test',
            'role' => 'cashier',
            'branch_id' => $this->branch->id,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_cashier_can_create_order_without_cost_price_leaving_it_nullable(): void
    {
        $response = $this->actingAs($this->cashier)->post(route('orders.store-design'), [
            'customer_name' => 'Budi Brosur',
            'customer_phone' => '08111222333',
            'title' => 'Cetak Brosur A4 150gsm',
            'main_qty' => 500,
            'main_unit' => 'Lembar',
            'main_unit_price' => '1.000',
            'main_price' => '500.000',
            'main_cost_price' => '', // Dikosongkan karena vendor belum rilis nota
            'total_amount' => '500.000',
            'paid_amount' => '250.000',
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();

        $order = Order::where('customer_name', 'Budi Brosur')->first();
        $this->assertNotNull($order);
        $this->assertNull($order->total_cost);
        $this->assertFalse($order->is_cost_complete);
        $this->assertNull($order->gross_profit);

        $item = $order->items->first();
        $this->assertNotNull($item);
        $this->assertNull($item->cost_price);
        $this->assertNull($item->total_cost);
    }

    public function test_cashier_can_create_order_with_cost_price_and_it_calculates_total_cost(): void
    {
        $response = $this->actingAs($this->cashier)->post(route('orders.store-design'), [
            'customer_name' => 'Siti Banner',
            'customer_phone' => '08222333444',
            'title' => 'Spanduk Outdoor 3x1m Flexi 280g',
            'main_qty' => 3,
            'main_unit' => 'Meter',
            'main_unit_price' => '25.000',
            'main_cost_price' => '15.000', // Diketahui modalnya
            'main_price' => '75.000',
            'total_amount' => '75.000',
            'paid_amount' => '75.000',
            'payment_method' => 'qris',
        ]);

        $response->assertRedirect();

        $order = Order::where('customer_name', 'Siti Banner')->first();
        $this->assertNotNull($order);
        $this->assertEquals(45000.00, (float) $order->total_cost); // 3 * 15.000
        $this->assertTrue($order->is_cost_complete);
        $this->assertEquals(30000.00, (float) $order->gross_profit); // 75.000 - 45.000
        $this->assertEquals(40.0, (float) $order->gross_profit_margin); // 30k / 75k * 100

        $item = $order->items->first();
        $this->assertEquals(15000.00, (float) $item->cost_price);
        $this->assertEquals(45000.00, (float) $item->total_cost);
        $this->assertEquals(30000.00, (float) $item->profit);
    }

    public function test_cashier_can_create_banner_order_with_negotiated_subtotal_and_cost(): void
    {
        // Simulasi banner 3x1m (3 m²), tarif jual standar 20k (harus 60k), tapi nego jadi 50k.
        // Modal Flexi 280g adalah 14.500/m² -> Total modal = 3 * 14.500 = 43.500.
        $response = $this->actingAs($this->cashier)->post(route('orders.store-design'), [
            'customer_name' => 'Pak Joko Pecel Lele',
            'customer_phone' => '081299887766',
            'title' => 'Spanduk Pecel Lele 3x1m',
            'dimensions' => '3 x 1 meter (3.00 m²)',
            'material_notes' => 'Flexi Standar 280gr',
            'main_qty' => 1,
            'main_unit' => 'Meter',
            'main_unit_price' => '50.000',
            'main_cost_price' => '43.500',
            'main_price' => '50.000',
            'total_amount' => '50.000',
            'paid_amount' => '50.000',
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();

        $order = Order::where('customer_name', 'Pak Joko Pecel Lele')->first();
        $this->assertNotNull($order);
        $this->assertEquals(50000.00, (float) $order->total_amount);
        $this->assertEquals(43500.00, (float) $order->total_cost);
        $this->assertTrue($order->is_cost_complete);
        $this->assertEquals(6500.00, (float) $order->gross_profit); // 50.000 - 43.500
        $this->assertEquals(13.0, (float) $order->gross_profit_margin);

        $this->assertEquals('3 x 1 meter (3.00 m²)', $order->designJob->dimensions);
        $this->assertEquals('Flexi Standar 280gr', $order->designJob->material_notes);
    }

    public function test_cashier_can_create_order_with_separate_design_fee_and_printing_cost(): void
    {
        // Jasa Desain: 15.000 (modal 0)
        // Cetak Spanduk 3x1m Flexi: Biaya Cetak 60.000, Modal Cetak 43.500
        // Total Tagihan: 15k + 60k = 75.000
        // Laba Kotor: 75k - 43.5k = 31.500 (42.0%)
        $response = $this->actingAs($this->cashier)->post(route('orders.store-design'), [
            'customer_name' => 'Kedai Kopi Mantap',
            'customer_phone' => '085544332211',
            'title' => 'Banner Kedai Kopi 3x1m',
            'design_fee' => '15.000',
            'dimensions' => '3 x 1 meter (3.00 m²)',
            'material_notes' => 'Flexi Standar 280gr',
            'main_qty' => 1,
            'main_unit' => 'Meter',
            'main_unit_price' => '20.000',
            'main_cost_price' => '43.500',
            'main_price' => '75.000', // 15k desain + 60k cetak
            'total_amount' => '75.000',
            'paid_amount' => '75.000',
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();

        $order = Order::where('customer_name', 'Kedai Kopi Mantap')->first();
        $this->assertNotNull($order);
        $this->assertEquals(75000.00, (float) $order->total_amount);
        $this->assertEquals(43500.00, (float) $order->total_cost);
        $this->assertTrue($order->is_cost_complete);
        $this->assertEquals(31500.00, (float) $order->gross_profit); // 75k - 43.5k
        $this->assertEquals(42.0, (float) $order->gross_profit_margin);

        $this->assertEquals(15000.00, (float) $order->designJob->design_fee);
    }

    public function test_owner_can_update_order_cost_afterwards(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'SDR-2611-001',
            'customer_name' => 'PT Maju Makmur',
            'order_type' => 'design_printing',
            'total_amount' => 100000,
            'total_cost' => null,
            'paid_amount' => 100000,
            'payment_status' => 'paid',
            'work_status' => 'in_progress',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'item_name' => 'Brosur Lipat Tiga',
            'qty' => 2,
            'unit_price' => 50000,
            'cost_price' => null,
            'subtotal' => 100000,
        ]);

        $this->assertNull($order->total_cost);
        $this->assertFalse($order->is_cost_complete);

        // Owner melengkapi HPP saat nota vendor masuk
        $response = $this->actingAs($this->owner)->post(route('orders.cost', $order->id), [
            'items' => [
                [
                    'id' => $item->id,
                    'cost_price' => '30.000',
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $item->refresh();

        $this->assertEquals(30000.00, (float) $item->cost_price);
        $this->assertEquals(60000.00, (float) $order->total_cost); // 2 * 30k
        $this->assertTrue($order->is_cost_complete);
        $this->assertEquals(40000.00, (float) $order->gross_profit);
        $this->assertEquals(40.0, (float) $order->gross_profit_margin);
    }

    public function test_historical_cost_snapshot_is_preserved_when_settings_change(): void
    {
        // 1. Set default acuan modal banner = 40.000
        Setting::set('banner_default_cost', 40000, 'Modal Default Banner');

        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'SDR-2611-002',
            'customer_name' => 'Toko Lawas',
            'order_type' => 'design_printing',
            'total_amount' => 60000,
            'total_cost' => 40000,
            'paid_amount' => 60000,
            'payment_status' => 'paid',
            'work_status' => 'completed',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'item_name' => 'Banner Toko 1x1m',
            'qty' => 1,
            'unit_price' => 60000,
            'cost_price' => 40000,
            'subtotal' => 60000,
        ]);

        // 2. Bulan depan owner menaikkan acuan modal default menjadi 45.000
        Setting::set('banner_default_cost', 45000);

        // 3. Order historis yang sudah tersimpan TIDAK boleh berubah sama sekali
        $order->refresh();
        $item->refresh();

        $this->assertEquals(40000.00, (float) $item->cost_price, 'Snapshot modal item lama tidak boleh berubah');
        $this->assertEquals(40000.00, (float) $order->total_cost, 'Total modal order lama tidak boleh terpengaruh perubahan setting');
    }

    public function test_orders_index_filters_by_cost_status(): void
    {
        $orderMissing = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'SDR-2611-901',
            'customer_name' => 'Order Belum HPP',
            'order_type' => 'design_printing',
            'total_amount' => 50000,
            'total_cost' => null,
            'paid_amount' => 50000,
            'payment_status' => 'paid',
            'work_status' => 'in_progress',
        ]);

        $orderComplete = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'SDR-2611-902',
            'customer_name' => 'Order Sudah Lengkap HPP',
            'order_type' => 'design_printing',
            'total_amount' => 50000,
            'total_cost' => 30000,
            'paid_amount' => 50000,
            'payment_status' => 'paid',
            'work_status' => 'in_progress',
        ]);

        // Filter missing HPP
        $resMissing = $this->actingAs($this->owner)->get(route('orders.index', ['cost_status' => 'missing']));
        $resMissing->assertOk();
        $resMissing->assertSee('Order Belum HPP');
        $resMissing->assertDontSee('Order Sudah Lengkap HPP');

        // Filter complete HPP
        $resComplete = $this->actingAs($this->owner)->get(route('orders.index', ['cost_status' => 'complete']));
        $resComplete->assertOk();
        $resComplete->assertSee('Order Sudah Lengkap HPP');
        $resComplete->assertDontSee('Order Belum HPP');
    }
}
