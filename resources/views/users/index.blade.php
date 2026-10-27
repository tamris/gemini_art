@extends('layouts.app')

@section('title', 'Kelola Tim & Staf Percetakan')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                <span class="text-xs font-black uppercase tracking-wider text-indigo-900">
                    Manajemen Akun & Akses Staf Multi-Cabang
                </span>
            </div>
            <h1 class="text-2xl font-black text-black">Kelola Tim & Staf Percetakan</h1>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="button" 
                    onclick="document.getElementById('add-user-modal').showModal()"
                    class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-4 font-black text-black flex items-center gap-1.5 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a]">
                <x-icon name="user-plus" class="w-4 h-4" />
                <span>+ Tambah Staf Baru</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="neo-box p-4 bg-white">
            <span class="text-[10px] font-black uppercase text-slate-500 block mb-1">Total Staf Terdaftar</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-black">{{ $totalStaff }}</span>
                <span class="text-xs font-bold text-slate-500">Orang</span>
            </div>
        </div>

        <div class="neo-box p-4 bg-emerald-50">
            <span class="text-[10px] font-black uppercase text-emerald-800 block mb-1">Kasir Aktif (POS)</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-950">{{ $totalCashiers }}</span>
                <span class="text-xs font-bold text-emerald-800">Orang</span>
            </div>
        </div>

        <div class="neo-box p-4 bg-blue-50">
            <span class="text-[10px] font-black uppercase text-blue-800 block mb-1">Desainer Aktif</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-blue-950">{{ $totalDesigners }}</span>
                <span class="text-xs font-bold text-blue-800">Orang</span>
            </div>
        </div>

        <div class="neo-box p-4 bg-amber-50">
            <span class="text-[10px] font-black uppercase text-amber-800 block mb-1">Total Akun Aktif</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-950">{{ $totalActive }}</span>
                <span class="text-xs font-bold text-amber-800">/ {{ $totalStaff }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Card (Instant Auto-Filter with Neo-Select) -->
    <div class="neo-box p-4 bg-white relative overflow-visible z-20">
        <form method="GET" action="{{ route('users.index') }}" data-auto-submit class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            
            <!-- Search input -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Cari Staf</label>
                <div class="flex items-center rounded-xl border-[1.5px] border-slate-300 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] focus-within:border-slate-800 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] bg-white transition-all overflow-hidden">
                    <button type="submit" aria-label="Cari" class="pl-3 pr-2 text-slate-400 hover:text-slate-800 flex items-center justify-center shrink-0 cursor-pointer transition-colors">
                        <x-icon name="search" class="w-3.5 h-3.5" />
                    </button>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Nama staf, email, nomor HP/WA... (Tekan Enter)"
                           class="w-full py-2 pr-3 text-xs font-bold text-slate-800 placeholder:text-slate-400 outline-none bg-transparent">
                </div>
            </div>

            <!-- Branch filter -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Cabang Penempatan</label>
                <select name="branch_id" class="neo-select">
                    <option value="">Semua Cabang</option>
                    <option value="central" {{ request('branch_id') === 'central' ? 'selected' : '' }}>Pusat (Akses Global / Owner)</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->code }} - {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Role filter -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Peran / Role</label>
                <select name="role" class="neo-select">
                    <option value="">Semua Role</option>
                    <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Kasir (POS & Kas)</option>
                    <option value="designer" {{ request('role') === 'designer' ? 'selected' : '' }}>Desainer (Workdesk)</option>
                    <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner (Super Admin)</option>
                </select>
            </div>

            <!-- Reset Filter Action -->
            <div>
                <a href="{{ route('users.index') }}" 
                   title="Reset semua filter"
                   class="w-full neo-btn {{ request()->anyFilled(['search', 'branch_id', 'role', 'is_active']) ? 'bg-amber-200 hover:bg-amber-300 text-slate-900 border-slate-800 font-black' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold' }} text-xs py-2 px-3 flex items-center justify-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] transition-all">
                    <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                    <span>Reset Filter</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Filter Results Live Container -->
    <div id="filter-results-container">
        <div class="neo-box bg-white overflow-hidden shadow-[3px_3px_0px_0px_#0f172a]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-800 font-black uppercase tracking-wider">
                            <th class="py-3 px-4">Nama Staf & Kontak</th>
                            <th class="py-3 px-4">Peran / Role</th>
                            <th class="py-3 px-4">Cabang Penempatan</th>
                            <th class="py-3 px-4">Status Akun</th>
                            <th class="py-3 px-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/10">
                        @forelse($users as $u)
                        <tr class="hover:bg-amber-50/40 transition-colors {{ ! $u->is_active ? 'bg-slate-50/60 opacity-75' : '' }}">
                            
                            <!-- Name & Contacts -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl border-[1.5px] border-slate-800 flex items-center justify-center font-black text-xs shrink-0 shadow-[1px_1px_0px_0px_#0f172a] {{ $u->avatar_color_class }}">
                                        {{ $u->initials }}
                                    </div>
                                    <div>
                                        <div class="font-black text-sm text-black flex items-center gap-1.5">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === Auth::id())
                                                <span class="text-[9px] font-black uppercase bg-amber-300 border border-slate-800 px-1 rounded shadow-none">Saya</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-mono text-slate-600 mt-0.5">
                                            {{ $u->email }}
                                        </div>
                                        @if($u->phone)
                                            <div class="text-[10px] text-slate-500 font-bold mt-0.5 flex items-center gap-1">
                                                <x-icon name="phone" class="w-2.5 h-2.5" />
                                                <span>{{ $u->phone }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge (Clean, simple & without icons) -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md border text-xs font-bold {{ $u->role_badge_class }}">
                                    {{ $u->role_label }}
                                </span>
                            </td>

                            <!-- Branch Placement -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($u->branch)
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-black text-xs px-2 py-0.5 rounded border border-black bg-slate-100 shadow-[1px_1px_0px_0px_#000]">
                                            {{ $u->branch->code }}
                                        </span>
                                        <span class="font-bold text-xs text-black">
                                            {{ $u->branch->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-purple-900 bg-purple-50 border border-purple-300 px-2.5 py-1 rounded-lg inline-flex items-center gap-1">
                                        <x-icon name="building-2" class="w-3 h-3" />
                                        <span>Pusat (Akses Global Seluruh Cabang)</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full border border-emerald-800 bg-emerald-100 text-emerald-950 font-black text-[10px]">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full border border-slate-500 bg-slate-200 text-slate-700 font-bold text-[10px]">
                                        <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                        <span>Dinonaktifkan</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions (Icon-only compact buttons) -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <!-- Edit & Mutasi Button -->
                                    <button type="button" 
                                            onclick="document.getElementById('edit-user-modal-{{ $u->id }}').showModal()"
                                            title="Edit data staf & mutasi cabang"
                                            aria-label="Edit Staf"
                                            class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 bg-white hover:bg-slate-100 text-slate-800 shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none flex items-center justify-center transition-all cursor-pointer">
                                        <x-icon name="pencil" class="w-3.5 h-3.5" />
                                    </button>

                                    <!-- Reset Password Button -->
                                    <button type="button" 
                                            onclick="document.getElementById('reset-password-modal-{{ $u->id }}').showModal()"
                                            title="Reset password staf ini"
                                            aria-label="Reset Password"
                                            class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 bg-amber-200 hover:bg-amber-300 text-slate-900 shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none flex items-center justify-center transition-all cursor-pointer">
                                        <x-icon name="key" class="w-3.5 h-3.5" />
                                    </button>

                                    <!-- Toggle Active / Inactive Status Trigger -->
                                    @if($u->id !== Auth::id())
                                        <button type="button" 
                                                onclick="document.getElementById('toggle-status-modal-{{ $u->id }}').showModal()"
                                                title="{{ $u->is_active ? 'Nonaktifkan akun staf ini' : 'Aktifkan kembali akun staf ini' }}"
                                                aria-label="{{ $u->is_active ? 'Nonaktifkan akun' : 'Aktifkan kembali akun' }}"
                                                class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 {{ $u->is_active ? 'bg-rose-100 hover:bg-rose-200 text-rose-900' : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-950' }} shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none flex items-center justify-center transition-all cursor-pointer">
                                            @if($u->is_active)
                                                <x-icon name="user-x" class="w-3.5 h-3.5" />
                                            @else
                                                <x-icon name="user-check" class="w-3.5 h-3.5" />
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <x-icon name="users" class="w-8 h-8 mx-auto text-slate-400" />
                                    <p class="font-bold text-sm text-slate-800">Tidak ada data staf yang cocok</p>
                                    <p class="text-xs text-slate-500">Coba ubah kata kunci pencarian atau bersihkan filter di atas.</p>
                                    <a href="{{ route('users.index') }}" class="inline-block mt-2 neo-btn bg-slate-200 text-xs py-1.5 px-3">
                                        Bersihkan Filter
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination & Data Counter Footer -->
            {{ $users->links() }}
        </div>
    </div>

</div>

<!-- Modal 1: Tambah Staf Baru -->
<dialog id="add-user-modal" closedby="any" class="p-0 bg-transparent max-w-lg w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] rounded-2xl border-2 border-slate-900 overflow-visible">
        <div class="flex items-center justify-between pb-3.5 border-b-2 border-slate-900 mb-5">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                <h3 class="font-black text-lg text-slate-900 leading-normal">Tambah Staf Karyawan Baru</h3>
            </div>
            <form method="dialog">
                <button type="submit" aria-label="Tutup" class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-rose-200 hover:bg-rose-300 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none">
                    ✕
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-black uppercase text-slate-800 mb-1">Nama Lengkap Staf *</label>
                <input type="text" name="name" required placeholder="Contoh: Tri Wahyuni" class="neo-input text-xs font-bold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-black uppercase text-slate-800 mb-1">Email Login *</label>
                    <input type="email" name="email" required placeholder="staf@geminiart.test" class="neo-input text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-slate-800 mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="phone" placeholder="081234567890" class="neo-input text-xs font-medium">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-black uppercase text-slate-800 mb-1">Peran / Role *</label>
                    <select name="role" required class="neo-select" onchange="toggleBranchRequirement(this, 'add-branch-wrapper')">
                        <option value="cashier" selected>Kasir</option>
                        <option value="designer">Desainer</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <div id="add-branch-wrapper">
                    <label class="block text-xs font-black uppercase text-slate-800 mb-1">Cabang Penempatan *</label>
                    <select name="branch_id" class="neo-select">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->code }} - {{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-slate-800 mb-1">Password Awal *</label>
                <input type="text" name="password" required value="password" minlength="6" class="neo-input text-xs font-mono font-bold">
                <span class="text-[10px] text-slate-500 font-medium block mt-1">Default diisi "password", staf dapat langsung menggunakannya untuk login.</span>
            </div>

            <div class="pt-4 border-t-2 border-slate-900 flex items-center justify-end gap-2.5">
                <button type="button" onclick="document.getElementById('add-user-modal').close()" class="neo-btn bg-slate-100 hover:bg-slate-200 text-xs py-2 px-4 cursor-pointer font-bold text-slate-700">
                    Batal
                </button>
                <button type="submit" class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-4 font-black text-slate-900 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a]">
                    Simpan Staf Baru
                </button>
            </div>
        </form>
    </div>
</dialog>

<!-- Modals per User: Edit & Reset Password -->
@foreach($users as $u)
<!-- Modal Edit / Mutasi -->
<dialog id="edit-user-modal-{{ $u->id }}" closedby="any" class="p-0 bg-transparent max-w-lg w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] rounded-2xl border-2 border-slate-900 overflow-visible">
        <div class="flex items-center justify-between pb-3.5 border-b-2 border-slate-900 mb-5">
            <div class="min-w-0 pr-2">
                <h3 class="font-black text-lg text-slate-900 leading-normal">Edit Staf & Mutasi Cabang</h3>
                <span class="text-xs font-mono text-slate-500 font-bold block mt-0.5 truncate">{{ $u->name }} &bull; {{ $u->email }}</span>
            </div>
            <form method="dialog">
                <button type="submit" aria-label="Tutup" class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-rose-200 hover:bg-rose-300 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none">
                    ✕
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('users.update', $u->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-black uppercase text-slate-800 mb-1">Nama Lengkap Staf *</label>
                <input type="text" name="name" value="{{ $u->name }}" required class="neo-input text-xs font-bold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-black uppercase text-slate-800 mb-1">Email Login *</label>
                    <input type="email" name="email" value="{{ $u->email }}" required class="neo-input text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-slate-800 mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="phone" value="{{ $u->phone }}" placeholder="081234567890" class="neo-input text-xs font-medium">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-black uppercase text-slate-800 mb-1">Peran / Role *</label>
                    <select name="role" required class="neo-select" onchange="toggleBranchRequirement(this, 'edit-branch-wrapper-{{ $u->id }}')">
                        <option value="cashier" {{ $u->role === 'cashier' ? 'selected' : '' }}>Kasir</option>
                        <option value="designer" {{ $u->role === 'designer' ? 'selected' : '' }}>Desainer</option>
                        @if($u->role === 'owner' || Auth::user()->isOwner())
                            <option value="owner" {{ $u->role === 'owner' ? 'selected' : '' }}>Owner</option>
                        @endif
                    </select>
                </div>
                <div id="edit-branch-wrapper-{{ $u->id }}" class="{{ $u->role === 'owner' ? 'opacity-40 pointer-events-none' : '' }}">
                    <label class="block text-xs font-black uppercase text-slate-800 mb-1">Mutasi Cabang *</label>
                    <select name="branch_id" class="neo-select">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $u->branch_id == $b->id ? 'selected' : '' }}>
                                {{ $b->code }} - {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t-2 border-slate-900 flex items-center justify-end gap-2.5">
                <button type="button" onclick="document.getElementById('edit-user-modal-{{ $u->id }}').close()" class="neo-btn bg-slate-100 hover:bg-slate-200 text-xs py-2 px-4 cursor-pointer font-bold text-slate-700">
                    Batal
                </button>
                <button type="submit" class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-4 font-black text-slate-900 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</dialog>

<!-- Modal Reset Password -->
<dialog id="reset-password-modal-{{ $u->id }}" closedby="any" class="p-0 bg-transparent max-w-sm w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] rounded-2xl border-2 border-slate-900 overflow-visible">
        <div class="flex items-center justify-between pb-3.5 border-b-2 border-slate-900 mb-5">
            <div class="min-w-0 pr-2">
                <h3 class="font-black text-lg text-slate-900 leading-normal">Reset Password Staf</h3>
                <span class="text-xs font-mono text-slate-500 font-bold block mt-0.5 truncate">{{ $u->name }}</span>
            </div>
            <form method="dialog">
                <button type="submit" aria-label="Tutup" class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-rose-200 hover:bg-rose-300 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none">
                    ✕
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('users.reset-password', $u->id) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-black uppercase text-slate-800 mb-1">Password Baru *</label>
                <input type="text" name="new_password" required minlength="6" placeholder="Masukkan password baru..." class="neo-input text-xs font-mono font-bold">
                <span class="text-[10px] text-slate-500 font-medium block mt-1">Minimal 6 karakter. Staf dapat langsung login menggunakan password ini.</span>
            </div>

            <div class="pt-4 border-t-2 border-slate-900 flex items-center justify-end gap-2.5">
                <button type="button" onclick="document.getElementById('reset-password-modal-{{ $u->id }}').close()" class="neo-btn bg-slate-100 hover:bg-slate-200 text-xs py-2 px-4 cursor-pointer font-bold text-slate-700">
                    Batal
                </button>
                <button type="submit" class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-4 font-black text-slate-900 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a]">
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>
</dialog>

@if($u->id !== Auth::id())
<!-- Modal Konfirmasi Status Akun Staf -->
<dialog id="toggle-status-modal-{{ $u->id }}" closedby="any" class="p-0 bg-transparent max-w-sm w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] rounded-2xl border-2 border-slate-900 overflow-visible">
        
        <div class="flex items-start gap-3.5 mb-4">
            <div class="w-10 h-10 rounded-xl border-2 border-slate-900 {{ $u->is_active ? 'bg-rose-200 text-rose-950' : 'bg-emerald-200 text-emerald-950' }} flex items-center justify-center shrink-0 shadow-[2px_2px_0px_0px_#0f172a]">
                @if($u->is_active)
                    <x-icon name="user-x" class="w-5 h-5" />
                @else
                    <x-icon name="user-check" class="w-5 h-5" />
                @endif
            </div>
            <div class="min-w-0">
                <h3 class="font-black text-base text-slate-900 leading-tight">
                    {{ $u->is_active ? 'Nonaktifkan Akun Staf?' : 'Aktifkan Kembali Akun Staf?' }}
                </h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    @if($u->is_active)
                        Akun <strong class="text-slate-900">{{ $u->name }}</strong> tidak akan dapat login atau memproses transaksi kasir sampai diaktifkan kembali.
                    @else
                        Akun <strong class="text-slate-900">{{ $u->name }}</strong> akan dapat login dan bertugas kembali di cabang penempatan.
                    @endif
                </p>
            </div>
        </div>

        <div class="pt-3 border-t-2 border-slate-900 flex items-center justify-end gap-2.5">
            <button type="button" 
                    onclick="document.getElementById('toggle-status-modal-{{ $u->id }}').close()" 
                    class="neo-btn bg-slate-100 hover:bg-slate-200 text-xs py-2 px-4 cursor-pointer font-bold text-slate-700">
                Batal
            </button>
            <form method="POST" action="{{ route('users.toggle-status', $u->id) }}" class="inline">
                @csrf
                <button type="submit" 
                        class="neo-btn {{ $u->is_active ? 'bg-rose-300 hover:bg-rose-400 text-slate-950' : 'bg-emerald-300 hover:bg-emerald-400 text-slate-950' }} text-xs py-2 px-4 font-black cursor-pointer shadow-[2px_2px_0px_0px_#0f172a]">
                    {{ $u->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}
                </button>
            </form>
        </div>

    </div>
</dialog>
@endif
@endforeach

@push('scripts')
<script>
function toggleBranchRequirement(roleSelect, wrapperId) {
    const wrapper = document.getElementById(wrapperId);
    if (!wrapper) return;
    if (roleSelect.value === 'owner') {
        wrapper.classList.add('opacity-40', 'pointer-events-none');
    } else {
        wrapper.classList.remove('opacity-40', 'pointer-events-none');
    }
}
</script>
@endpush
@endsection
