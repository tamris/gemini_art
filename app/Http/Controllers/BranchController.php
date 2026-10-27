<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            abort(403, 'Hanya Owner yang dapat mengelola cabang.');
        }

        $branches = Branch::withCount(['users', 'orders', 'cashMovements'])->get();

        foreach ($branches as $branch) {
            $branch->cash_balance = $branch->activeCashBalance();
        }

        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|uppercase|unique:branches,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        Branch::create($data);

        return back()->with('success', "Cabang {$data['name']} ({$data['code']}) berhasil ditambahkan!");
    }

    public function update(Request $request, Branch $branch)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            abort(403, 'Hanya Owner yang dapat mengedit cabang.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:10', 'uppercase', Rule::unique('branches', 'code')->ignore($branch->id)],
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        $branch->update($data);

        return back()->with('success', "Data Cabang {$branch->name} ({$branch->code}) berhasil diperbarui!");
    }
}
