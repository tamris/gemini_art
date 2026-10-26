<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashMovementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = CashMovement::with(['branch', 'user', 'order'])->latest();

        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
            $selectedBranch = $user->branch;
        } else {
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
                $selectedBranch = Branch::find($request->branch_id);
            } else {
                $selectedBranch = null; // All branches
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $movements = $query->paginate(20)->withQueryString();

        // Calculate summary stats
        $statsQuery = CashMovement::query();
        if (!$user->isOwner()) {
            $statsQuery->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $statsQuery->where('branch_id', $request->branch_id);
        }

        $totalIn = (float) (clone $statsQuery)->where('type', 'in')->sum('amount');
        $totalOut = (float) (clone $statsQuery)->where('type', 'out')->sum('amount');
        $currentBalance = $totalIn - $totalOut;

        $branches = Branch::all();

        return view('cash.index', compact('movements', 'branches', 'selectedBranch', 'totalIn', 'totalOut', 'currentBalance'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,qris,transfer',
            'category' => 'required|in:petty_cash_expense,initial_balance,order_settlement,order_dp,retail_pos',
            'notes' => 'required|string|max:255',
        ];

        if ($user->isOwner()) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $data = $request->validate($rules);
        $branchId = $user->isOwner() ? $data['branch_id'] : $user->branch_id;

        CashMovement::create([
            'branch_id' => $branchId,
            'user_id' => $user->id,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'category' => $data['category'],
            'notes' => $data['notes'],
        ]);

        $actionText = $data['type'] === 'in' ? 'Kas Masuk' : 'Kas Keluar';
        return back()->with('success', "Transaksi {$actionText} sebesar Rp " . number_format($data['amount'], 0, ',', '.') . " berhasil dicatat.");
    }
}
