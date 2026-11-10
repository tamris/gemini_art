@extends('layouts.app')

@section('title', 'Buku Kas & Audit Keuangan Cabang')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                <span class="text-xs font-black uppercase tracking-wider text-purple-900 dark:text-purple-300">
                    Buku Kas & Mutasi Keuangan
                </span>
            </div>
            <h1 class="text-2xl font-black text-black dark:text-white">Buku Kas Harian & Audit Operasional</h1>
            <!-- <p class="text-xs font-semibold text-slate-600 mt-0.5">
                Pencatatan kas masuk DP/pelunasan/POS dan kas keluar operasional untuk mencegah kebocoran omset.
            </p> -->
        </div>
        <button type="button" 
                onclick="document.getElementById('add-expense-modal').showModal(); if(window.applySmartCashCategories) window.applySmartCashCategories();"
                class="neo-btn bg-amber-300 hover:bg-amber-400 dark:bg-amber-400 dark:hover:bg-amber-300 dark:text-slate-950 text-xs py-2 px-4 font-black text-black cursor-pointer shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000] self-start sm:self-auto flex items-center gap-1.5">
            <x-icon name="plus-circle" class="w-4 h-4" />
            <span>Catat Mutasi Kas</span>
        </button>
    </div>

    <!-- 3 Summary Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <!-- Saldo Kas Aktif Laci -->
        <div class="p-5 bg-amber-100 dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 rounded-xl shadow-[4px_4px_0px_0px_#000] dark:shadow-[4px_4px_0px_0px_#000000]">
            <div class="text-xs font-black uppercase text-slate-700 dark:text-amber-400 mb-1 flex items-center justify-between">
                <span>Saldo Kas Aktif (Fisik Laci)</span>
                <svg class="w-4 h-4 text-slate-900 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-black text-black dark:text-white tracking-tight">
                Rp {{ number_format($currentBalance, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-[11px] font-bold text-slate-700 dark:text-slate-400">
                {{ $selectedBranch ? $selectedBranch->name : 'Gabungan Seluruh Cabang' }}
            </div>
        </div>

        <!-- Total Kas Masuk -->
        <div class="p-5 bg-emerald-100 dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 rounded-xl shadow-[4px_4px_0px_0px_#000] dark:shadow-[4px_4px_0px_0px_#000000]">
            <div class="text-xs font-black uppercase text-slate-700 dark:text-emerald-400 mb-1 flex items-center justify-between">
                <span>Total Kas Masuk</span>
                <svg class="w-4 h-4 text-emerald-800 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div class="text-2xl font-black text-emerald-800 dark:text-emerald-400 tracking-tight">
                + Rp {{ number_format($totalIn, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-[11px] font-bold text-emerald-800 dark:text-slate-400">
                DP Desain, Pelunasan & Kasir POS
            </div>
        </div>

        <!-- Total Kas Keluar -->
        <div class="p-5 bg-rose-100 dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 rounded-xl shadow-[4px_4px_0px_0px_#000] dark:shadow-[4px_4px_0px_0px_#000000]">
            <div class="text-xs font-black uppercase text-slate-700 dark:text-rose-400 mb-1 flex items-center justify-between">
                <span>Total Kas Keluar</span>
                <svg class="w-4 h-4 text-rose-800 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                </svg>
            </div>
            <div class="text-2xl font-black text-rose-800 dark:text-rose-400 tracking-tight">
                - Rp {{ number_format($totalOut, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-[11px] font-bold text-rose-800 dark:text-slate-400">
                Petty cash, belanja toko & operasional
            </div>
        </div>

    </div>

    <!-- Filter Card (Instant Auto-Filter) -->
    <div class="neo-box p-4 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] relative overflow-visible z-20">
        <form method="GET" action="{{ route('cash.index') }}" data-auto-submit class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            
            @if(Auth::user()->isOwner())
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Cabang</label>
                @php
                    $cashBranchOpts = ['' => 'Semua Cabang'];
                    foreach($branches as $b) {
                        $cashBranchOpts[$b->id] = $b->code . ' - ' . $b->name;
                    }
                @endphp
                <x-neo-select name="branch_id" :options="$cashBranchOpts" :value="request('branch_id')" />
            </div>
            @endif

            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Arah Kas</label>
                <x-neo-select 
                    name="type" 
                    :value="request('type')"
                    :options="[
                        '' => 'Semua (Masuk & Keluar)',
                        'in' => 'Kas Masuk (+)',
                        'out' => 'Kas Keluar (-)',
                    ]" 
                />
            </div>

            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Kategori</label>
                <x-neo-select 
                    name="category" 
                    :value="request('category')"
                    :options="[
                        '' => 'Semua Kategori',
                        'order_dp' => 'DP Order Desain',
                        'order_settlement' => 'Pelunasan Order',
                        'retail_pos' => 'Kasir Ritel POS',
                        'petty_cash_expense' => 'Pengeluaran Operasional',
                        'order_refund' => 'Pengembalian Dana (Refund)',
                        'other_expense' => 'DLL',
                        'initial_balance' => 'Modal Kas Awal',
                    ]" 
                />
            </div>

            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Metode Bayar</label>
                <x-neo-select 
                    name="payment_method" 
                    :value="request('payment_method')"
                    :options="[
                        '' => 'Semua Metode',
                        'cash' => 'Tunai / Cash',
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer Bank',
                    ]" 
                />
            </div>

            <div class="relative" data-neo-datepicker-container data-mode="date">
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Tanggal</label>
                
                <input type="hidden" name="date" value="{{ request('date') }}">

                <!-- Trigger Input Button -->
                <button type="button" 
                        data-datepicker-toggle
                        class="w-full flex items-center justify-between bg-white dark:bg-slate-900 border-[1.5px] border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] hover:border-slate-800 dark:hover:border-slate-600 focus:outline-none cursor-pointer text-left h-[38px] transition-all">
                    <span data-datepicker-text class="truncate {{ request('date') ? 'text-slate-900 dark:text-white font-extrabold' : 'text-slate-400 dark:text-slate-500 font-medium' }}">
                        {{ request('date') ? date('d/m/Y', strtotime(request('date'))) : 'Pilih Tanggal...' }}
                    </span>
                    <x-icon name="calendar" class="w-3.5 h-3.5 text-slate-500 shrink-0 stroke-[2.5]" />
                </button>

                <!-- Popover Container (Downwards, aligned left) -->
                <div data-datepicker-popover class="hidden absolute top-[calc(100%+6px)] left-0 z-50 bg-white dark:bg-[#0c1222] border-2 border-slate-900 dark:border-slate-700 rounded-2xl shadow-[4px_4px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#000000] overflow-hidden select-none w-[235px]">
                    
                    <!-- Month Header (h-10 / 40px) -->
                    <div class="h-10 px-3 bg-amber-300 border-b-2 border-slate-900 dark:border-slate-700 flex items-center justify-between shrink-0">
                        <button type="button" data-cal-prev class="w-6 h-6 rounded-md bg-white dark:bg-slate-800 border-[1.5px] border-slate-900 dark:border-slate-700 shadow-[1px_1px_0px_0px_#0f172a] hover:bg-slate-50 flex items-center justify-center cursor-pointer active:translate-x-[0.5px] active:translate-y-[0.5px]">
                            <x-icon name="chevron-left" class="w-3.5 h-3.5 text-slate-900 dark:text-white stroke-[2.5]" />
                        </button>
                        <div class="font-black text-xs text-slate-950 uppercase tracking-wider flex items-center gap-1.5">
                            <span data-cal-month>Oktober</span>
                            <span data-cal-year>2026</span>
                        </div>
                        <button type="button" data-cal-next class="w-6 h-6 rounded-md bg-white dark:bg-slate-800 border-[1.5px] border-slate-900 dark:border-slate-700 shadow-[1px_1px_0px_0px_#0f172a] hover:bg-slate-50 flex items-center justify-center cursor-pointer active:translate-x-[0.5px] active:translate-y-[0.5px]">
                            <x-icon name="chevron-right" class="w-3.5 h-3.5 text-slate-900 dark:text-white stroke-[2.5]" />
                        </button>
                    </div>

                    <!-- Weekdays Row (h-6 / 24px) -->
                    <div class="h-6 grid grid-cols-7 text-center items-center px-2 bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-[10px] font-black uppercase text-slate-600 dark:text-slate-400 shrink-0">
                        <span>Min</span>
                        <span>Sen</span>
                        <span>Sel</span>
                        <span>Rab</span>
                        <span>Kam</span>
                        <span>Jum</span>
                        <span>Sab</span>
                    </div>

                    <!-- Days Grid (h-[204px], tight 6 rows x 7 cols) -->
                    <div class="h-[204px] p-2 bg-white dark:bg-[#0c1222] shrink-0">
                        <div data-cal-days class="grid grid-cols-7 gap-1">
                            <!-- Dynamically generated day buttons -->
                        </div>
                    </div>

                    <!-- Footer: Hapus & Hari Ini (h-10 / 40px) -->
                    <div class="h-10 flex items-center justify-between px-3 border-t-2 border-slate-900 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-bold shrink-0">
                        <button type="button" data-cal-reset class="text-xs font-black text-rose-600 dark:text-rose-400 hover:text-rose-800 hover:underline cursor-pointer">
                            Hapus
                        </button>
                        <button type="button" data-cal-today class="text-xs font-black text-slate-800 dark:text-slate-300 hover:text-slate-950 hover:underline cursor-pointer">
                            Hari ini
                        </button>
                    </div>

                </div>
            </div>

            <div>
                <a href="{{ route('cash.index') }}" class="w-full neo-btn {{ request()->anyFilled(['branch_id', 'type', 'category', 'payment_method', 'date']) ? 'bg-amber-200 dark:bg-amber-300 dark:text-slate-950 hover:bg-amber-300 text-slate-900 border-slate-800 font-black' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 text-slate-600 font-bold dark:border-slate-700' }} text-xs py-2 px-3 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] flex items-center justify-center gap-1.5 transition-all">
                    <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                    <span>Reset Filter</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Filter Results Container (Live Smooth Replacement) -->
    <div id="filter-results-container">
        <!-- Movements Table -->
        <div class="neo-box bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 font-black uppercase tracking-wider">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Cabang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">No. Order Terkait</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4">Petugas</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/20 dark:divide-slate-800">
                    @forelse($movements as $m)
                    <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-4 font-mono whitespace-nowrap text-slate-700 dark:text-slate-300">
                            {{ $m->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded border border-black dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-200 font-bold text-[10px]">
                                {{ $m->branch->code }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded border border-black text-[10px] font-black uppercase {{ $m->category_badge_class }}" @if($m->notes) title="{{ $m->notes }}" @endif>
                                {{ $m->category_label }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4 font-mono font-bold whitespace-nowrap">
                            @if($m->order)
                                <a href="{{ route('orders.show', $m->order_id) }}" class="underline text-black dark:text-white hover:text-blue-700 dark:hover:text-amber-400">
                                    {{ $m->order->order_number }}
                                </a>
                            @else
                                <span class="text-slate-400 dark:text-slate-500">-</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap font-bold uppercase text-[11px] text-slate-800 dark:text-slate-300">
                            {{ $m->payment_method }}
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-slate-600 dark:text-slate-400">
                            {{ $m->user->name }}
                        </td>

                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <span class="font-black text-sm {{ $m->type === 'in' ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                                {{ $m->type === 'in' ? '+' : '-' }} Rp {{ number_format($m->amount, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 dark:text-slate-500 font-semibold">
                            Belum ada riwayat mutasi kas yang sesuai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Counter Footer -->
        {{ $movements->links() }}
    </div>
    </div>

</div>

<!-- Modal Catat Kas Keluar / Kas Masuk Manual -->
<dialog id="add-expense-modal" closedby="any" class="p-0 bg-transparent max-w-md w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white dark:bg-[#0c1222] w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#000000] rounded-2xl border-2 border-slate-900 dark:border-slate-700 overflow-visible">
        <div class="flex items-center justify-between pb-3 border-b-2 border-black dark:border-slate-800 mb-4">
            <h3 class="font-black text-base text-black dark:text-white">Catat Mutasi Kas Manual</h3>
            <form method="dialog">
                <button class="w-8 h-8 rounded-lg border-2 border-black dark:border-slate-700 bg-rose-200 hover:bg-rose-300 dark:bg-rose-500/20 dark:hover:bg-rose-500/30 dark:text-rose-300 text-black font-black text-base flex items-center justify-center cursor-pointer">
                    ✕
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('cash.store') }}" class="space-y-4">
            @csrf

            @if(Auth::user()->isOwner())
            <div>
                <label class="block text-xs font-black uppercase text-black dark:text-slate-300 mb-1">Cabang</label>
                @php
                    $modalBranchOpts = [];
                    foreach($branches as $b) {
                        $modalBranchOpts[$b->id] = $b->code . ' - ' . $b->name;
                    }
                @endphp
                <x-neo-select name="branch_id" required :options="$modalBranchOpts" :value="Auth::user()->branch_id" />
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-black uppercase text-black dark:text-slate-300 mb-1">Jenis Mutasi</label>
                    <x-neo-select 
                        name="type" 
                        id="modal_cash_type"
                        required 
                        value="out"
                        :options="[
                            'out' => 'Kas Keluar (-)',
                            'in' => 'Kas Masuk (+)',
                        ]" 
                    />
                </div>
                <div>
                    <label id="modal_cash_category_label" class="block text-xs font-black uppercase text-black dark:text-slate-300 mb-1">Kategori Pengeluaran (-)</label>
                    <x-neo-select 
                        name="category" 
                        id="modal_cash_category"
                        required 
                        value="petty_cash_expense"
                        :options="[
                            'petty_cash_expense' => 'Pengeluaran Operasional / Belanja Toko',
                            'order_refund' => 'Pengembalian Dana (Refund)',
                            'other_expense' => 'DLL',
                            'initial_balance' => 'Modal Kas Awal / Tambah Saldo Laci',
                            'retail_pos' => 'Pemasukan Kasir Ritel POS (Non-Order)',
                        ]" 
                    />
                </div>
            </div>

            <div>
                <label for="cash_amount_display" class="block text-xs font-black uppercase text-black dark:text-slate-300 mb-1">Nominal (Rp) *</label>
                <div class="flex h-10 w-full rounded-xl overflow-hidden border-[1.5px] border-slate-300 dark:border-slate-600 bg-white dark:bg-[#0c1222] shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[2px_2px_0px_0px_#334155] focus-within:border-slate-800 dark:focus-within:border-amber-400 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] dark:focus-within:shadow-[2.5px_2.5px_0px_0px_#475569] transition-all">
                    <span id="cash_amount_prefix" class="h-full inline-flex items-center justify-center px-3.5 bg-rose-100 text-rose-900 dark:bg-rose-950/80 dark:text-rose-300 border-r-[1.5px] border-slate-300 dark:border-slate-600 text-xs font-black select-none whitespace-nowrap shrink-0 transition-colors">
                        - Rp
                    </span>
                    <input type="text" 
                           id="cash_amount_display" 
                           inputmode="numeric" 
                           placeholder="0" 
                           required 
                           autocomplete="off"
                           class="w-full h-full px-3 text-xs sm:text-sm font-bold text-slate-900 dark:text-white bg-transparent focus:outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500 tracking-wide">
                </div>
                <input type="hidden" name="amount" id="cash_amount" value="">
                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold mt-1 block">Otomatis terformat rupiah (contoh: 50.000).</span>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-black dark:text-slate-300 mb-1">Metode Pembayaran</label>
                <x-neo-select 
                    name="payment_method" 
                    required 
                    value="cash"
                    :options="[
                        'cash' => 'Tunai (Cash Laci)',
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer Bank',
                    ]" 
                />
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-black dark:text-slate-300 mb-1">Keterangan Pengeluaran / Pemasukan</label>
                <input type="text" id="modal_cash_notes" name="notes" placeholder="Misal: Beli lakban jilid & air galon cabang" required class="neo-input text-xs">
            </div>

            <div class="pt-3 border-t-2 border-black dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-expense-modal').close()" class="neo-btn bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 dark:border-slate-700 text-xs py-2 px-4 cursor-pointer font-bold">
                    Batal
                </button>
                <button type="submit" id="modal_cash_submit_btn" class="neo-btn bg-rose-400 hover:bg-rose-300 text-black text-xs py-2 px-4 font-black cursor-pointer dark:border-slate-700 transition-colors shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]">
                    <span id="modal_cash_submit_text">Catat Kas Keluar (-)</span>
                </button>
            </div>
        </form>
    </div>
</dialog>

@push('scripts')
<script>
function applySmartCashCategories() {
    const typeSelect = document.getElementById('modal_cash_type');
    const catSelect = document.getElementById('modal_cash_category');
    if (!typeSelect || !catSelect) return;

    const catWrapper = catSelect.closest('.neo-select-wrapper');
    if (!catWrapper) return;

    const catLabel = document.getElementById('modal_cash_category_label');
    const amountPrefix = document.getElementById('cash_amount_prefix');
    const notesInput = document.getElementById('modal_cash_notes');
    const submitBtn = document.getElementById('modal_cash_submit_btn');
    const submitText = document.getElementById('modal_cash_submit_text');

    const outCategories = ['petty_cash_expense', 'order_refund', 'other_expense'];
    const inCategories = ['initial_balance', 'retail_pos'];

    const type = typeSelect.value;
    const isOut = (type === 'out');
    const allowed = isOut ? outCategories : inCategories;
    const defaultVal = isOut ? 'petty_cash_expense' : 'initial_balance';

    // 1. Update Category Header Label
    if (catLabel) {
        catLabel.textContent = isOut ? 'Kategori Pengeluaran (-)' : 'Kategori Pemasukan (+)';
    }

    // 2. Update Amount Prefix Badge Styling
    if (amountPrefix) {
        if (isOut) {
            amountPrefix.textContent = '- Rp';
            amountPrefix.className = 'h-full inline-flex items-center justify-center px-3.5 bg-rose-100 text-rose-900 dark:bg-rose-950/80 dark:text-rose-300 border-r-[1.5px] border-slate-300 dark:border-slate-600 text-xs font-black select-none whitespace-nowrap shrink-0 transition-colors';
        } else {
            amountPrefix.textContent = '+ Rp';
            amountPrefix.className = 'h-full inline-flex items-center justify-center px-3.5 bg-emerald-100 text-emerald-900 dark:bg-emerald-950/80 dark:text-emerald-300 border-r-[1.5px] border-slate-300 dark:border-slate-600 text-xs font-black select-none whitespace-nowrap shrink-0 transition-colors';
        }
    }

    // 3. Update Notes Placeholder
    if (notesInput) {
        notesInput.placeholder = isOut 
            ? 'Misal: Beli lakban jilid, air galon, listrik, bensin operasional' 
            : 'Misal: Modal uang kembalian kasir shift pagi, tambahan kas laci';
    }

    // 4. Update Submit Button
    if (submitBtn && submitText) {
        if (isOut) {
            submitBtn.className = 'neo-btn bg-rose-400 hover:bg-rose-300 text-black text-xs py-2 px-4 font-black cursor-pointer dark:border-slate-700 transition-colors shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]';
            submitText.textContent = 'Catat Kas Keluar (-)';
        } else {
            submitBtn.className = 'neo-btn bg-emerald-400 hover:bg-emerald-300 text-black dark:text-slate-950 text-xs py-2 px-4 font-black cursor-pointer dark:border-slate-700 transition-colors shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]';
            submitText.textContent = 'Catat Kas Masuk (+)';
        }
    }

    // 5. Filter Custom Neo-Select Options in Dropdown Menu
    const optElements = catWrapper.querySelectorAll('.neo-select-option');
    optElements.forEach(opt => {
        const val = opt.dataset.value;
        const isAllowed = allowed.includes(val);
        opt.style.display = isAllowed ? 'flex' : 'none';
    });

    // 6. Filter Native Select Options
    const nativeOpts = catSelect.querySelectorAll('option');
    nativeOpts.forEach(opt => {
        const isAllowed = allowed.includes(opt.value);
        opt.disabled = !isAllowed;
        opt.hidden = !isAllowed;
    });

    // 7. Ensure valid selection
    if (!allowed.includes(catSelect.value)) {
        catSelect.value = defaultVal;
    }
    catSelect.dispatchEvent(new Event('change', { bubbles: true }));
}
window.applySmartCashCategories = applySmartCashCategories;

document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('modal_cash_type');
    if (typeSelect) {
        typeSelect.addEventListener('change', applySmartCashCategories);
        // Delay slightly to let neo-select finish its server hydration/binding
        setTimeout(applySmartCashCategories, 50);
    }

    const displayEl = document.getElementById('cash_amount_display');
    const hiddenEl = document.getElementById('cash_amount');
    if (!displayEl || !hiddenEl) return;

    displayEl.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace') {
            const cursor = displayEl.selectionStart;
            if (cursor > 1 && displayEl.value[cursor - 1] === '.') {
                e.preventDefault();
                const val = displayEl.value;
                const newVal = val.slice(0, cursor - 2) + val.slice(cursor - 1);
                displayEl.value = newVal;
                displayEl.setSelectionRange(cursor - 2, cursor - 2);
                displayEl.dispatchEvent(new Event('input'));
            }
        }
    });

    displayEl.addEventListener('input', function() {
        const cursor = displayEl.selectionStart;
        const oldVal = displayEl.value;
        const rawDigits = oldVal.replace(/\D/g, '');

        if (!rawDigits) {
            displayEl.value = '';
            hiddenEl.value = '';
        } else {
            const num = parseInt(rawDigits, 10);
            const formatted = num.toLocaleString('id-ID');
            displayEl.value = formatted;
            hiddenEl.value = num;

            const diff = formatted.length - oldVal.length;
            let newCursor = cursor + diff;
            if (newCursor < 0) newCursor = 0;
            if (newCursor > formatted.length) newCursor = formatted.length;
            displayEl.setSelectionRange(newCursor, newCursor);
        }
    });
});
</script>
@endpush
@endsection
