<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignAsset;
use App\Models\DesignJob;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        if ($user->isOwner()) {
            // Global Multi-Cabang view
            $branches = Branch::withCount([
                'orders as active_orders_count' => function ($q) {
                    $q->whereIn('work_status', ['draft', 'in_progress', 'revision', 'ready_to_print']);
                }
            ])->get();

            // Calculate metrics for each branch
            foreach ($branches as $branch) {
                $branch->today_turnover = CashMovement::where('branch_id', $branch->id)
                    ->where('type', 'in')
                    ->whereDate('created_at', $today)
                    ->sum('amount');

                $branch->cash_balance = $branch->activeCashBalance();
            }

            $totalTurnoverToday = CashMovement::where('type', 'in')
                ->whereDate('created_at', $today)
                ->sum('amount');

            $totalTurnoverMonth = CashMovement::where('type', 'in')
                ->where('created_at', '>=', $startOfMonth)
                ->sum('amount');

            $totalCashIn = CashMovement::where('type', 'in')->sum('amount');
            $totalCashOut = CashMovement::where('type', 'out')->sum('amount');
            $totalCashBalance = $totalCashIn - $totalCashOut;

            $activeDesignsCount = Order::where('order_type', 'design_printing')
                ->whereIn('work_status', ['draft', 'in_progress', 'revision', 'ready_to_print'])
                ->count();

            $readyToPrintCount = Order::where('work_status', 'ready_to_print')->count();

            $recentOrders = Order::with(['branch', 'creator', 'designJob'])
                ->latest()
                ->take(6)
                ->get();

            $recentAssets = DesignAsset::with(['designJob.order.branch', 'uploader'])
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard.owner', compact(
                'branches',
                'totalTurnoverToday',
                'totalTurnoverMonth',
                'totalCashBalance',
                'activeDesignsCount',
                'readyToPrintCount',
                'recentOrders',
                'recentAssets'
            ));
        }

        if ($user->isCashier()) {
            $branch = $user->branch;

            $todayTurnover = CashMovement::where('branch_id', $branch->id)
                ->where('type', 'in')
                ->whereDate('created_at', $today)
                ->sum('amount');

            $cashBalance = $branch->activeCashBalance();

            // Orders awaiting pickup or settlement
            $readyOrders = Order::where('branch_id', $branch->id)
                ->where('work_status', 'ready_to_print')
                ->with(['designJob'])
                ->latest()
                ->get();

            // In progress design orders
            $inProgressOrders = Order::where('branch_id', $branch->id)
                ->whereIn('work_status', ['draft', 'in_progress', 'revision'])
                ->with(['designJob'])
                ->latest()
                ->take(5)
                ->get();

            // Today's cash movements in this branch
            $todayMovements = CashMovement::where('branch_id', $branch->id)
                ->whereDate('created_at', $today)
                ->with('user')
                ->latest()
                ->get();

            return view('dashboard.cashier', compact(
                'branch',
                'todayTurnover',
                'cashBalance',
                'readyOrders',
                'inProgressOrders',
                'todayMovements'
            ));
        }

        // Designer View
        $branch = $user->branch;

        $inProgressJobs = DesignJob::whereHas('order', function ($q) use ($branch) {
            $q->where('branch_id', $branch->id)->whereIn('work_status', ['draft', 'in_progress', 'revision']);
        })->with(['order.creator', 'assets'])
            ->orderBy('deadline_at', 'asc')
            ->get();

        $readyJobs = DesignJob::whereHas('order', function ($q) use ($branch) {
            $q->where('branch_id', $branch->id)->where('work_status', 'ready_to_print');
        })->with(['order', 'assets'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.designer', compact('branch', 'inProgressJobs', 'readyJobs'));
    }
}
