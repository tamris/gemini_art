<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of all staff members with filters.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->isOwner(), 403, 'Akses terbatas hanya untuk Owner.');

        $query = User::with('branch');

        // Filter by branch
        if ($request->filled('branch_id')) {
            if ($request->branch_id === 'central') {
                $query->whereNull('branch_id');
            } else {
                $query->where('branch_id', $request->branch_id);
            }
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status (active / inactive)
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->is_active);
        }

        // Filter by search query (name, email, phone)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('branch_id')
                       ->orderBy('role')
                       ->orderBy('name')
                       ->paginate(10)
                       ->withQueryString();

        $branches = Branch::orderBy('name')->get();

        // Operational stats
        $totalStaff = User::count();
        $totalCashiers = User::where('role', 'cashier')->where('is_active', true)->count();
        $totalDesigners = User::where('role', 'designer')->where('is_active', true)->count();
        $totalActive = User::where('is_active', true)->count();

        return view('users.index', compact(
            'users',
            'branches',
            'totalStaff',
            'totalCashiers',
            'totalDesigners',
            'totalActive'
        ));
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->isOwner(), 403, 'Akses terbatas hanya untuk Owner.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:owner,cashier,designer'],
            'branch_id' => [
                'nullable',
                'exists:branches,id',
                Rule::requiredIf(fn () => $request->role !== 'owner'),
            ],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama lengkap staf wajib diisi.',
            'email.required' => 'Email login wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'role.required' => 'Peran / Role staf wajib dipilih.',
            'branch_id.required_if' => 'Cabang penempatan wajib dipilih untuk Kasir dan Desainer.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'branch_id' => $validated['role'] === 'owner' ? null : $validated['branch_id'],
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Staf baru \"{$user->name}\" ({$user->role_label}) berhasil didaftarkan!");
    }

    /**
     * Update an existing staff member's information and branch placement.
     */
    public function update(Request $request, User $user)
    {
        abort_unless(Auth::user()->isOwner(), 403, 'Akses terbatas hanya untuk Owner.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:owner,cashier,designer'],
            'branch_id' => [
                'nullable',
                'exists:branches,id',
                Rule::requiredIf(fn () => $request->role !== 'owner'),
            ],
        ], [
            'name.required' => 'Nama lengkap staf wajib diisi.',
            'email.required' => 'Email login wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'branch_id.required_if' => 'Cabang penempatan wajib dipilih untuk Kasir dan Desainer.',
        ]);

        // Prevent self-demotion if owner
        $role = $validated['role'];
        if ($user->id === Auth::id() && $user->role === 'owner') {
            $role = 'owner';
        }

        $user->update([
            'branch_id' => $role === 'owner' ? null : $validated['branch_id'],
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'],
            'role' => $role,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Data staf \"{$user->name}\" berhasil diperbarui!");
    }

    /**
     * Reset a staff member's password directly (Admin Quick Reset).
     */
    public function resetPassword(Request $request, User $user)
    {
        abort_unless(Auth::user()->isOwner(), 403, 'Akses terbatas hanya untuk Owner.');

        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Password untuk staf \"{$user->name}\" berhasil diperbarui.");
    }

    /**
     * Toggle a staff member's active status (deactivate/activate).
     */
    public function toggleStatus(User $user)
    {
        abort_unless(Auth::user()->isOwner(), 403, 'Akses terbatas hanya untuk Owner.');

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        $statusMsg = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';
        return redirect()->route('users.index')
            ->with('success', "Akun staf \"{$user->name}\" berhasil {$statusMsg}.");
    }
}
