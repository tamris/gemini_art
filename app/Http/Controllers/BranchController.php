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

        if ($request->filled('code')) {
            $request->merge(['code' => strtoupper(trim((string) $request->code))]);
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:branches,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama cabang wajib diisi.',
            'name.max' => 'Nama cabang maksimal 100 karakter.',
            'code.required' => 'Kode cabang wajib diisi.',
            'code.max' => 'Kode cabang maksimal 10 karakter.',
            'code.unique' => 'Kode cabang sudah digunakan oleh cabang lain.',
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

        if ($request->filled('code')) {
            $request->merge(['code' => strtoupper(trim((string) $request->code))]);
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:10', Rule::unique('branches', 'code')->ignore($branch->id)],
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama cabang wajib diisi.',
            'name.max' => 'Nama cabang maksimal 100 karakter.',
            'code.required' => 'Kode cabang wajib diisi.',
            'code.max' => 'Kode cabang maksimal 10 karakter.',
            'code.unique' => 'Kode cabang sudah digunakan oleh cabang lain.',
        ]);

        $branch->update($data);

        return back()->with('success', "Data Cabang {$branch->name} ({$branch->code}) berhasil diperbarui!");
    }
}
