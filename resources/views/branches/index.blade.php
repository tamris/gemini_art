@extends('layouts.app')

@section('title', 'Kelola Cabang Percetakan')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                <span class="text-xs font-black uppercase tracking-wider text-rose-900">
                    Konfigurasi Multi-Cabang
                </span>
            </div>
            <h1 class="text-2xl font-black text-black">Daftar Cabang & Outlet Percetakan</h1>
            <p class="text-xs font-semibold text-slate-600 mt-0.5">
                Kelola kode cabang (contoh: CBA, CBB) untuk otomatisasi penomoran order dan struktur path arsip cloud.
            </p>
        </div>
        <button type="button" 
                onclick="document.getElementById('add-branch-modal').showModal()"
                class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-4 font-black text-black flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Cabang Baru</span>
        </button>
    </div>

    <!-- Branch Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($branches as $b)
        <div class="neo-box p-6 bg-white space-y-4">
            <div class="flex items-start justify-between pb-3 border-b-2 border-black">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded bg-black text-white font-mono font-black text-sm">
                            {{ $b->code }}
                        </span>
                        <h2 class="font-black text-lg text-black">{{ $b->name }}</h2>
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-1">{{ $b->address ?? 'Alamat belum diatur' }}</p>
                </div>
                <span class="text-xs font-bold text-slate-700 bg-slate-100 border border-black px-2 py-1 rounded">
                    {{ $b->phone ?? '-' }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="p-3 bg-slate-50 border-2 border-black rounded-lg text-center">
                    <span class="text-[10px] font-black uppercase text-slate-500 block">Karyawan</span>
                    <span class="text-base font-black text-black">{{ $b->users_count }} Orang</span>
                </div>
                <div class="p-3 bg-slate-50 border-2 border-black rounded-lg text-center">
                    <span class="text-[10px] font-black uppercase text-slate-500 block">Total Order</span>
                    <span class="text-base font-black text-black">{{ $b->orders_count }} Order</span>
                </div>
                <div class="p-3 bg-amber-100 border-2 border-black rounded-lg text-center">
                    <span class="text-[10px] font-black uppercase text-slate-700 block">Kas Aktif</span>
                    <span class="text-xs font-black text-black">Rp {{ number_format($b->cash_balance, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Path S3/R2 Cloud: <code>designs/{{ $b->code }}/...</code></span>
                <a href="{{ route('orders.index', ['branch_id' => $b->id]) }}" class="font-bold text-blue-700 hover:underline">
                    Filter Order Cabang Ini →
                </a>
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- Add Branch Modal -->
<dialog id="add-branch-modal" closedby="any" class="p-0 bg-transparent max-w-lg w-full m-auto">
    <div class="neo-box p-6 bg-white max-w-md w-full mx-auto">
        <div class="flex items-center justify-between pb-3 border-b-2 border-black mb-4">
            <h3 class="font-black text-base text-black">Tambah Cabang Baru</h3>
            <form method="dialog">
                <button class="w-8 h-8 rounded-lg border-2 border-black bg-rose-200 hover:bg-rose-300 font-black text-base flex items-center justify-center cursor-pointer">
                    ✕
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('branches.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Nama Cabang</label>
                <input type="text" name="name" required placeholder="Contoh: Gemini Art - Cabang Semarang" class="neo-input text-xs font-bold">
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Kode Cabang (Maks 10 Huruf, Unik)</label>
                <input type="text" name="code" required placeholder="Contoh: CBC" maxlength="10" class="neo-input text-xs font-mono font-bold uppercase">
                <span class="text-[10px] text-slate-500 font-medium">Digunakan untuk format order (e.g. ORD-CBC-...) dan direktori storage.</span>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="2" placeholder="Jl. Pandanaran No. 10..." class="neo-input text-xs"></textarea>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">No. Telepon / WhatsApp Cabang</label>
                <input type="text" name="phone" placeholder="0812-..." class="neo-input text-xs">
            </div>

            <div class="pt-3 border-t-2 border-black flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-branch-modal').close()" class="neo-btn bg-slate-200 text-xs py-2 px-4 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-4 font-black flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Cabang</span>
                </button>
            </div>
        </form>
    </div>
</dialog>
@endsection
