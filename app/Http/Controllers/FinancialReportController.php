<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Laporan Keuangan & Laba Rugi khusus diakses oleh Owner
        if (!$user->isOwner()) {
            abort(403, 'Akses Laporan Keuangan & Laba Rugi hanya diizinkan untuk Owner.');
        }

        $branches = Branch::all();

        // 1. Branch Authorization & Selection
        if ($request->filled('branch_id')) {
            $branchId = (int) $request->branch_id;
            $selectedBranch = Branch::find($branchId);
        } else {
            $branchId = null; // All branches consolidated
            $selectedBranch = null;
        }

        // 2. Period Filter Determination
        $period = $request->input('period', 'this_month');
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $periodLabel = 'Hari Ini (' . $startDate->translatedFormat('d M Y') . ')';
                break;

            case 'this_week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $periodLabel = 'Minggu Ini (' . $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y') . ')';
                break;

            case 'last_month':
                $startDate = $now->copy()->subMonth()->startOfMonth();
                $endDate = $now->copy()->subMonth()->endOfMonth();
                $periodLabel = 'Bulan Lalu (' . $startDate->translatedFormat('F Y') . ')';
                break;

            case 'this_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                $periodLabel = 'Tahun Ini (' . $startDate->translatedFormat('Y') . ')';
                break;

            case 'custom':
                $startDate = $request->filled('date_from') 
                    ? Carbon::parse($request->date_from)->startOfDay() 
                    : $now->copy()->startOfMonth();
                $endDate = $request->filled('date_to') 
                    ? Carbon::parse($request->date_to)->endOfDay() 
                    : $now->copy()->endOfDay();
                $periodLabel = $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
                break;

            case 'this_month':
            default:
                $period = 'this_month';
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $periodLabel = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
                break;
        }

        // 3. Query Cash Movements for Revenue & Expense Calculations
        $baseMovementQuery = CashMovement::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($branchId) {
            $baseMovementQuery->where('branch_id', $branchId);
        }

        // Income breakdown
        $orderRevenue = (float) (clone $baseMovementQuery)
            ->where('type', 'in')
            ->whereIn('category', ['order_dp', 'order_settlement'])
            ->sum('amount');

        $retailPosRevenue = (float) (clone $baseMovementQuery)
            ->where('type', 'in')
            ->where('category', 'retail_pos')
            ->sum('amount');

        $otherIncome = (float) (clone $baseMovementQuery)
            ->where('type', 'in')
            ->whereNotIn('category', ['order_dp', 'order_settlement', 'retail_pos'])
            ->sum('amount');

        $totalRevenue = $orderRevenue + $retailPosRevenue;
        $totalCashIn = (float) (clone $baseMovementQuery)->where('type', 'in')->sum('amount');

        // Expense breakdown
        $operationalExpense = (float) (clone $baseMovementQuery)
            ->where('type', 'out')
            ->where('category', 'petty_cash_expense')
            ->sum('amount');

        $refundExpense = (float) (clone $baseMovementQuery)
            ->where('type', 'out')
            ->where('category', 'order_refund')
            ->sum('amount');

        $otherExpense = (float) (clone $baseMovementQuery)
            ->where('type', 'out')
            ->whereNotIn('category', ['petty_cash_expense', 'order_refund'])
            ->sum('amount');

        $totalOperatingExpense = $operationalExpense + $refundExpense + $otherExpense;
        $totalCashOut = (float) (clone $baseMovementQuery)->where('type', 'out')->sum('amount');

        // Profit calculations
        $netOperatingProfit = $totalRevenue - $totalOperatingExpense;
        $netCashFlow = $totalCashIn - $totalCashOut;
        $profitMargin = $totalRevenue > 0 ? ($netOperatingProfit / $totalRevenue) * 100 : 0;

        // 4. Payment Channel Breakdown (Kas Masuk per Saluran)
        $cashInCash = (float) (clone $baseMovementQuery)->where('type', 'in')->where('payment_method', 'cash')->sum('amount');
        $cashInQris = (float) (clone $baseMovementQuery)->where('type', 'in')->where('payment_method', 'qris')->sum('amount');
        $cashInTransfer = (float) (clone $baseMovementQuery)->where('type', 'in')->where('payment_method', 'transfer')->sum('amount');

        $paymentSplit = [
            'cash' => [
                'label' => 'Tunai (Kas Laci)',
                'amount' => $cashInCash,
                'percentage' => $totalCashIn > 0 ? round(($cashInCash / $totalCashIn) * 100, 1) : 0,
            ],
            'qris' => [
                'label' => 'QRIS Dinamis',
                'amount' => $cashInQris,
                'percentage' => $totalCashIn > 0 ? round(($cashInQris / $totalCashIn) * 100, 1) : 0,
            ],
            'transfer' => [
                'label' => 'Transfer Bank',
                'amount' => $cashInTransfer,
                'percentage' => $totalCashIn > 0 ? round(($cashInTransfer / $totalCashIn) * 100, 1) : 0,
            ],
        ];

        // 5. Outstanding Receivables (Laporan Piutang Pelanggan)
        $receivablesQuery = Order::query()
            ->where('work_status', '!=', 'canceled')
            ->whereIn('payment_status', ['partial_dp', 'unpaid'])
            ->whereRaw('total_amount > paid_amount');

        if ($branchId) {
            $receivablesQuery->where('branch_id', $branchId);
        }

        $totalReceivables = (float) (clone $receivablesQuery)->sum(DB::raw('total_amount - paid_amount'));
        $unpaidOrdersCount = (int) (clone $receivablesQuery)->count();
        $topUnpaidOrders = (clone $receivablesQuery)
            ->with(['branch', 'creator', 'designJob'])
            ->orderByRaw('(total_amount - paid_amount) DESC')
            ->take(8)
            ->get();

        // 6. Multi-Branch Comparison (Khusus Owner / Konsolidasi)
        $branchComparison = [];
        if (!$branchId) {
            foreach ($branches as $b) {
                $bIn = (float) CashMovement::where('branch_id', $b->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('type', 'in')
                    ->sum('amount');

                $bOut = (float) CashMovement::where('branch_id', $b->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('type', 'out')
                    ->sum('amount');

                $bReceivables = (float) Order::where('branch_id', $b->id)
                    ->where('work_status', '!=', 'canceled')
                    ->whereIn('payment_status', ['partial_dp', 'unpaid'])
                    ->whereRaw('total_amount > paid_amount')
                    ->sum(DB::raw('total_amount - paid_amount'));

                $branchComparison[] = [
                    'branch' => $b,
                    'revenue' => $bIn,
                    'expense' => $bOut,
                    'profit' => $bIn - $bOut,
                    'cash_balance' => $b->activeCashBalance(),
                    'receivables' => $bReceivables,
                ];
            }
        }

        // 7. Recent Transactions in Period
        $recentTransactions = (clone $baseMovementQuery)
            ->with(['branch', 'user', 'order'])
            ->latest()
            ->take(15)
            ->get();

        return view('reports.finance', compact(
            'branches',
            'selectedBranch',
            'branchId',
            'period',
            'startDate',
            'endDate',
            'periodLabel',
            'orderRevenue',
            'retailPosRevenue',
            'otherIncome',
            'totalRevenue',
            'totalCashIn',
            'operationalExpense',
            'refundExpense',
            'otherExpense',
            'totalOperatingExpense',
            'totalCashOut',
            'netOperatingProfit',
            'netCashFlow',
            'profitMargin',
            'paymentSplit',
            'totalReceivables',
            'unpaidOrdersCount',
            'topUnpaidOrders',
            'branchComparison',
            'recentTransactions'
        ));
    }
}

