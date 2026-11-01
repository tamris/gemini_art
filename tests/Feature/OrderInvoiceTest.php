<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branchA;
    protected Branch $branchB;
    protected User $owner;
    protected User $cashierA;
    protected User $cashierB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branchA = Branch::create([
            'name' => 'Gemini Art - Cabang Keturen',
            'code' => 'KTR',
            'address' => 'Jl. Sultan Hasanudin, Tegal',
            'phone' => '082323232323',
        ]);

        $this->branchB = Branch::create([
            'name' => 'Gemini Art - Cabang Kramat',
            'code' => 'KRT',
            'address' => 'Jl. Raya Kramat, Tegal',
            'phone' => '085700001111',
        ]);

        $this->owner = User::create([
            'branch_id' => null,
            'name' => 'Budi Pratama (Owner)',
            'email' => 'owner@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $this->cashierA = User::create([
            'branch_id' => $this->branchA->id,
            'name' => 'Siti Kasir',
            'email' => 'kasirA@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);

        $this->cashierB = User::create([
            'branch_id' => $this->branchB->id,
            'name' => 'Rina Kasir',
            'email' => 'kasirB@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);
    }

    public function test_can_render_official_a4_invoice_with_full_details(): void
    {
        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'user_id' => $this->cashierA->id,
            'order_number' => 'ORD-KTR-202610-0001',
            'customer_name' => 'PT Sinar Jaya Abadi',
            'customer_phone' => '081234567890',
            'order_type' => 'design_printing',
            'total_amount' => 500000,
            'paid_amount' => 200000,
            'payment_status' => 'partial_dp',
            'work_status' => 'in_progress',
        ]);

        DesignJob::create([
            'order_id' => $order->id,
            'designer_id' => null,
            'title' => 'Cetak Banner Spanduk HUT 50M',
            'dimensions' => '3 x 1 meter',
            'material_notes' => 'Flexi Korea 380gr, Mata Ayam 4 Sudut',
            'brief_notes' => 'Warna dominan merah putih, logo HD di pojok atas',
            'deadline_at' => now()->addDays(2),
        ]);

        $response = $this->actingAs($this->cashierA)->get(route('orders.invoice', $order->id));

        $response->assertOk();
        $response->assertSee('PT Sinar Jaya Abadi');
        $response->assertSee('INV-KTR-202610-0001');
        $response->assertSee('Cetak Banner Spanduk HUT 50M');
        $response->assertSee('3 x 1 meter');
        $response->assertSee('Flexi Korea 380gr');
        $response->assertSee('500.000');
        $response->assertSee('200.000');
        $response->assertSee('300.000');
        $response->assertSee('DP DITERIMA');
        $response->assertSee('wa.me/6281234567890');
    }

    public function test_owner_can_access_invoices_across_all_branches(): void
    {
        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'user_id' => $this->cashierA->id,
            'order_number' => 'ORD-KTR-202610-0002',
            'customer_name' => 'Bpk Hendra',
            'order_type' => 'design_printing',
            'total_amount' => 150000,
            'paid_amount' => 150000,
            'payment_status' => 'paid',
            'work_status' => 'completed',
        ]);

        $response = $this->actingAs($this->owner)->get(route('orders.invoice', $order->id));
        $response->assertOk();
        $response->assertSee('LUNAS / FULL PAID');
    }

    public function test_cashier_cannot_access_invoice_from_different_branch(): void
    {
        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'user_id' => $this->cashierA->id,
            'order_number' => 'ORD-KTR-202610-0003',
            'customer_name' => 'Toko Anugrah',
            'order_type' => 'design_printing',
            'total_amount' => 200000,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'work_status' => 'draft',
        ]);

        $response = $this->actingAs($this->cashierB)->get(route('orders.invoice', $order->id));
        $response->assertForbidden();
    }
}

