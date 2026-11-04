<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branchA;
    protected Branch $branchB;
    protected User $owner;
    protected User $cashierA;

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
    }

    public function test_owner_can_view_consolidated_financial_report(): void
    {
        // 1. Order Revenue at Branch A
        CashMovement::create([
            'branch_id' => $this->branchA->id,
            'user_id' => $this->cashierA->id,
            'type' => 'in',
            'amount' => 500000,
            'payment_method' => 'cash',
            'category' => 'order_dp',
            'notes' => 'DP Order Spanduk',
        ]);

        // 2. POS Retail Revenue at Branch B
        CashMovement::create([
            'branch_id' => $this->branchB->id,
            'user_id' => $this->owner->id,
            'type' => 'in',
            'amount' => 150000,
            'payment_method' => 'qris',
            'category' => 'retail_pos',
            'notes' => 'Penjualan ATK',
        ]);

        // 3. Operational Expense at Branch A
        CashMovement::create([
            'branch_id' => $this->branchA->id,
            'user_id' => $this->cashierA->id,
            'type' => 'out',
            'amount' => 100000,
            'payment_method' => 'cash',
            'category' => 'petty_cash_expense',
            'notes' => 'Beli Kertas HVS & Tinta',
        ]);

        // 4. Outstanding Order with Unpaid Balance
        $order = Order::create([
            'branch_id' => $this->branchA->id,
            'user_id' => $this->cashierA->id,
            'order_number' => 'ORD-KTR-202611-0001',
            'customer_name' => 'Bpk Hendra',
            'customer_phone' => '081234567890',
            'order_type' => 'design_printing',
            'total_amount' => 800000,
            'paid_amount' => 300000,
            'payment_status' => 'partial_dp',
            'work_status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->owner)->get(route('reports.finance'));

        $response->assertOk();
        $response->assertSee('Laporan Keuangan');
        $response->assertSee('Total Omset Usaha');
        $response->assertSee('650.000'); // 500k + 150k
        $response->assertSee('100.000'); // Beban operasional
        $response->assertSee('550.000'); // Laba Bersih
        $response->assertSee('500.000'); // Piutang (800k - 300k)
        $response->assertSee('Performa Finansial Antar Cabang');
        $response->assertSee('Bpk Hendra');
    }

    public function test_filter_by_branch_calculates_isolated_metrics(): void
    {
        // Branch A
        CashMovement::create([
            'branch_id' => $this->branchA->id,
            'user_id' => $this->cashierA->id,
            'type' => 'in',
            'amount' => 250000,
            'payment_method' => 'cash',
            'category' => 'retail_pos',
            'notes' => 'POS Branch A',
        ]);

        // Branch B
        CashMovement::create([
            'branch_id' => $this->branchB->id,
            'user_id' => $this->owner->id,
            'type' => 'in',
            'amount' => 400000,
            'payment_method' => 'transfer',
            'category' => 'order_settlement',
            'notes' => 'Pelunasan Branch B',
        ]);

        $response = $this->actingAs($this->owner)->get(route('reports.finance', [
            'branch_id' => $this->branchA->id,
        ]));

        $response->assertOk();
        $response->assertSee('250.000');
        $response->assertDontSee('400.000');
    }

    public function test_cashier_forbidden_from_financial_reports(): void
    {
        $response = $this->actingAs($this->cashierA)->get(route('reports.finance'));
        $response->assertForbidden();
    }

    public function test_period_filter_custom_date(): void
    {
        $oldDate = Carbon::now()->subMonths(3);

        $movement = CashMovement::create([
            'branch_id' => $this->branchA->id,
            'user_id' => $this->cashierA->id,
            'type' => 'in',
            'amount' => 999000,
            'payment_method' => 'cash',
            'category' => 'retail_pos',
            'notes' => 'Transaksi Lama',
        ]);
        \Illuminate\Support\Facades\DB::table('cash_movements')
            ->where('id', $movement->id)
            ->update(['created_at' => $oldDate->format('Y-m-d H:i:s')]);

        // Query today only (should NOT include the 3-month-old transaction in today's revenue)
        $response = $this->actingAs($this->owner)->get(route('reports.finance', [
            'period' => 'today',
        ]));

        $response->assertOk();
        $response->assertViewHas('totalRevenue', 0.0);

        // Query custom date including oldDate
        $responseCustom = $this->actingAs($this->owner)->get(route('reports.finance', [
            'period' => 'custom',
            'date_from' => $oldDate->copy()->subDay()->format('Y-m-d'),
            'date_to' => $oldDate->copy()->addDay()->format('Y-m-d'),
        ]));

        $responseCustom->assertOk();
        $responseCustom->assertViewHas('totalRevenue', 999000.0);
        $responseCustom->assertSee('999.000');
    }
}
