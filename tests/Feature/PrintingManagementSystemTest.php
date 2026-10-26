<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignAsset;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrintingManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_login_page_renders_with_demo_accounts(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('GEMINI ART');
        $response->assertSee('Budi Pratama (Owner)');
        $response->assertSee('Siti Kasir Solo');
        $response->assertSee('Rian Desainer Solo');
    }

    public function test_owner_dashboard_shows_multi_branch_data(): void
    {
        $owner = User::where('role', 'owner')->first();
        $response = $this->actingAs($owner)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Owner Command Center');
        $response->assertSee('Cabang Solo (CBA)');
        $response->assertSee('Cabang Jogja (CBB)');
        $response->assertSee('Omset Hari Ini (Gabungan)');
    }

    public function test_cashier_dashboard_shows_branch_cash_and_ready_orders(): void
    {
        $cashier = User::where('email', 'kasir.solo@geminiart.test')->first();
        $response = $this->actingAs($cashier)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Saldo Kas Laci Cabang');
        $response->assertSee('Siap Diambil / Pelunasan');
    }

    public function test_create_design_order_and_automatic_cash_movement(): void
    {
        $cashier = User::where('email', 'kasir.solo@geminiart.test')->first();
        $branch = $cashier->branch;

        $response = $this->actingAs($cashier)->post(route('orders.store-design'), [
            'customer_name' => 'Resto Padang Minang Saiyo',
            'customer_phone' => '08129998881',
            'title' => 'Neon Box Acrylic 2 Sisi 100x60cm',
            'dimensions' => '100x60 cm',
            'material_notes' => 'Akrilik 3mm + Lampu LED',
            'brief_notes' => 'Logo rumah gadang warna merah emas.',
            'total_amount' => 850000,
            'paid_amount' => 400000,
            'payment_method' => 'qris',
        ]);

        $order = Order::where('customer_name', 'Resto Padang Minang Saiyo')->first();
        $this->assertNotNull($order);
        $this->assertEquals('design_printing', $order->order_type);
        $this->assertEquals(850000, (float) $order->total_amount);
        $this->assertEquals(400000, (float) $order->paid_amount);
        $this->assertEquals('partial_dp', $order->payment_status);
        $this->assertEquals('in_progress', $order->work_status);
        $this->assertStringStartsWith("ORD-{$branch->code}-", $order->order_number);

        // Verify Cash Movement was automatically recorded
        $movement = CashMovement::where('order_id', $order->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals('in', $movement->type);
        $this->assertEquals(400000, (float) $movement->amount);
        $this->assertEquals('order_dp', $movement->category);
        $this->assertEquals('qris', $movement->payment_method);

        $response->assertRedirect(route('orders.show', $order->id));
    }

    public function test_designer_upload_print_asset_automates_ready_to_print_status(): void
    {
        $designer = User::where('email', 'desain.solo@geminiart.test')->first();
        $order = Order::where('branch_id', $designer->branch_id)
            ->where('work_status', 'in_progress')
            ->first();
        $job = $order->designJob;

        $dummyFile = UploadedFile::fake()->create('spanduk_kopi_print_ready.pdf', 5000, 'application/pdf');

        $response = $this->actingAs($designer)->post(route('workdesk.upload', $job->id), [
            'file' => $dummyFile,
            'file_type' => 'final_print',
        ]);

        $response->assertSessionHas('success');
        $order->refresh();

        // SOP: status must automatically become ready_to_print
        $this->assertEquals('ready_to_print', $order->work_status);

        $asset = DesignAsset::where('design_job_id', $job->id)
            ->where('file_type', 'final_print')
            ->first();
        $this->assertNotNull($asset);
        $this->assertStringContainsString('print_', $asset->file_name);
        Storage::disk('public')->assertExists($asset->file_path);
    }

    public function test_cashier_settlement_updates_to_paid_and_completed(): void
    {
        $cashier = User::where('email', 'kasir.solo@geminiart.test')->first();
        $order = Order::where('branch_id', $cashier->branch_id)
            ->where('payment_status', 'partial_dp')
            ->first();

        $remaining = $order->remaining_amount;
        $this->assertGreaterThan(0, $remaining);

        $response = $this->actingAs($cashier)->post(route('orders.settle', $order->id), [
            'payment_method' => 'cash',
            'notes' => 'Pelunasan customer saat ambil spanduk',
        ]);

        $response->assertSessionHas('success');
        $order->refresh();

        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('completed', $order->work_status);
        $this->assertEquals(0, $order->remaining_amount);

        // Verify settlement cash movement
        $settlementMv = CashMovement::where('order_id', $order->id)
            ->where('category', 'order_settlement')
            ->latest('id')
            ->first();
        $this->assertNotNull($settlementMv);
        $this->assertEquals($remaining, (float) $settlementMv->amount);
    }

    public function test_fast_retail_pos_transaction(): void
    {
        $cashier = User::where('email', 'kasir.solo@geminiart.test')->first();

        $response = $this->actingAs($cashier)->post(route('pos.checkout'), [
            'customer_name' => 'Bpk. Ahmad (Walk-In)',
            'payment_method' => 'cash',
            'items' => [
                ['name' => 'Fotokopi A4 HVS', 'qty' => 50, 'price' => 300],
                ['name' => 'Jilid Spiral Kawat', 'qty' => 2, 'price' => 15000],
            ],
        ]);

        $order = Order::where('customer_name', 'Bpk. Ahmad (Walk-In)')->first();
        $this->assertNotNull($order);
        $this->assertEquals('retail_pos', $order->order_type);
        $this->assertEquals(45000, (float) $order->total_amount); // 50*300 + 2*15000 = 15000 + 30000 = 45000
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('completed', $order->work_status);
        $this->assertCount(2, $order->items);

        $response->assertRedirect(route('orders.show', $order->id));
    }

    public function test_cash_movement_manual_petty_cash_expense(): void
    {
        $cashier = User::where('email', 'kasir.solo@geminiart.test')->first();

        $response = $this->actingAs($cashier)->post(route('cash.store'), [
            'type' => 'out',
            'category' => 'petty_cash_expense',
            'amount' => 25000,
            'payment_method' => 'cash',
            'notes' => 'Beli staples & isi cutter cabang',
        ]);

        $response->assertSessionHas('success');

        $mv = CashMovement::where('notes', 'Beli staples & isi cutter cabang')->first();
        $this->assertNotNull($mv);
        $this->assertEquals('out', $mv->type);
        $this->assertEquals(25000, (float) $mv->amount);
    }
}
