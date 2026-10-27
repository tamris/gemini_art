@extends('layouts.app')

@section('title', 'Buku Kas & Audit Keuangan Cabang')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                <span class="text-xs font-black uppercase tracking-wider text-purple-900">
                    Buku Kas & Mutasi Keuangan
                </span>
            </div>
            <h1 class="text-2xl font-black text-black">Buku Kas Harian & Audit Operasional</h1>
            <!-- <p class="text-xs font-semibold text-slate-600 mt-0.5">
                Pencatatan kas masuk DP/pelunasan/POS dan kas keluar operasional untuk mencegah kebocoran omset.
            </p> -->
        </div>
        <button type="button" 
                onclick="document.getElementById('add-expense-modal').showModal()"
                class="neo-btn bg-rose-300 hover:bg-rose-400 text-xs py-2 px-4 font-black text-black flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
            </svg>
            <span>Catat Kas Keluar (Petty Cash)</span>
        </button>
    </div>

    <!-- 3 Summary Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <!-- Saldo Kas Aktif Laci -->
        <div class="p-5 bg-amber-100 border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000]">
            <div class="text-xs font-black uppercase text-slate-700 mb-1 flex items-center justify-between">
                <span>Saldo Kas Aktif (Fisik Laci)</span>
                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-black text-black tracking-tight">
                Rp {{ number_format($currentBalance, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-[11px] font-bold text-slate-700">
                {{ $selectedBranch ? $selectedBranch->name : 'Gabungan Seluruh Cabang' }}
            </div>
        </div>

        <!-- Total Kas Masuk -->
        <div class="p-5 bg-emerald-100 border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000]">
            <div class="text-xs font-black uppercase text-slate-700 mb-1 flex items-center justify-between">
                <span>Total Kas Masuk</span>
                <svg class="w-4 h-4 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div class="text-2xl font-black text-emerald-800 tracking-tight">
                + Rp {{ number_format($totalIn, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-[11px] font-bold text-emerald-800">
                DP Desain, Pelunasan & Kasir POS
            </div>
        </div>

        <!-- Total Kas Keluar -->
        <div class="p-5 bg-rose-100 border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000]">
            <div class="text-xs font-black uppercase text-slate-700 mb-1 flex items-center justify-between">
                <span>Total Kas Keluar</span>
                <svg class="w-4 h-4 text-rose-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                </svg>
            </div>
            <div class="text-2xl font-black text-rose-800 tracking-tight">
                - Rp {{ number_format($totalOut, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-[11px] font-bold text-rose-800">
                Petty cash, belanja toko & operasional
            </div>
        </div>

    </div>

    <!-- Filter Card (Instant Auto-Filter) -->
    <div class="neo-box p-4 bg-white relative overflow-visible z-20">
        <form method="GET" action="{{ route('cash.index') }}" data-auto-submit class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            
            @if(Auth::user()->isOwner())
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Cabang</label>
                <select name="branch_id" class="neo-select">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->code }} - {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Arah Kas</label>
                <select name="type" class="neo-select">
                    <option value="">Semua (Masuk & Keluar)</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Kas Masuk (+)</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Kas Keluar (-)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Kategori</label>
                <select name="category" class="neo-select">
                    <option value="">Semua Kategori</option>
                    <option value="order_dp" {{ request('category') == 'order_dp' ? 'selected' : '' }}>DP Order Desain</option>
                    <option value="order_settlement" {{ request('category') == 'order_settlement' ? 'selected' : '' }}>Pelunasan Order</option>
                    <option value="retail_pos" {{ request('category') == 'retail_pos' ? 'selected' : '' }}>Kasir Ritel POS</option>
                    <option value="petty_cash_expense" {{ request('category') == 'petty_cash_expense' ? 'selected' : '' }}>Pengeluaran Operasional</option>
                    <option value="initial_balance" {{ request('category') == 'initial_balance' ? 'selected' : '' }}>Modal Kas Awal</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Metode Bayar</label>
                <select name="payment_method" class="neo-select">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai / Cash</option>
                    <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="neo-input text-xs py-2 font-bold cursor-pointer">
            </div>

            <div>
                <a href="{{ route('cash.index') }}" class="w-full neo-btn {{ request()->anyFilled(['branch_id', 'type', 'category', 'payment_method', 'date']) ? 'bg-amber-200 hover:bg-amber-300 text-slate-900 border-slate-800 font-black' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold' }} text-xs py-2 px-3 shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center justify-center gap-1.5 transition-all">
                    <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                    <span>Reset Filter</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Filter Results Container (Live Smooth Replacement) -->
    <div id="filter-results-container">
        <!-- Movements Table -->
        <div class="neo-box bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-800 font-black uppercase tracking-wider">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Cabang</th>
                        <th class="py-3 px-4">Kategori & Keterangan</th>
                        <th class="py-3 px-4">No. Order Terkait</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4">Petugas</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/20">
                    @forelse($movements as $m)
                    <tr class="hover:bg-amber-50/40 transition-colors">
                        <td class="py-3.5 px-4 font-mono whitespace-nowrap">
                            {{ $m->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded border border-black bg-slate-100 font-bold text-[10px]">
                                {{ $m->branch->code }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.2 rounded border border-black text-[10px] font-black uppercase {{ $m->category_badge_class }}">
                                    {{ $m->category_label }}
                                </span>
                            </div>
                            <div class="font-semibold text-slate-800 text-xs">
                                {{ $m->notes ?? '-' }}
                            </div>
                        </td>

                        <td class="py-3.5 px-4 font-mono font-bold whitespace-nowrap">
                            @if($m->order)
                                <a href="{{ route('orders.show', $m->order_id) }}" class="underline hover:text-blue-700">
                                    {{ $m->order->order_number }}
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap font-bold uppercase text-[11px]">
                            {{ $m->payment_method }}
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-slate-600">
                            {{ $m->user->name }}
                        </td>

                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <span class="font-black text-sm {{ $m->type === 'in' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $m->type === 'in' ? '+' : '-' }} Rp {{ number_format($m->amount, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 font-semibold">
                            Belum ada riwayat mutasi kas yang sesuai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $movements->links() }}
        </div>
        @endif
    </div>
    </div>

</div>

<!-- Modal Catat Kas Keluar / Kas Masuk Manual -->
<dialog id="add-expense-modal" closedby="any" class="p-0 bg-transparent max-w-lg w-full m-auto">
    <div class="neo-box p-6 bg-white max-w-md w-full mx-auto">
        <div class="flex items-center justify-between pb-3 border-b-2 border-black mb-4">
            <h3 class="font-black text-base text-black">Catat Mutasi Kas Manual</h3>
            <form method="dialog">
                <button class="w-8 h-8 rounded-lg border-2 border-black bg-rose-200 hover:bg-rose-300 font-black text-base flex items-center justify-center cursor-pointer">
                    ✕
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('cash.store') }}" class="space-y-4">
            @csrf

            @if(Auth::user()->isOwner())
            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Cabang</label>
                <select name="branch_id" required class="neo-select">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->code }} - {{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Jenis Mutasi</label>
                    <select name="type" required class="neo-select">
                        <option value="out">Kas Keluar (-)</option>
                        <option value="in">Kas Masuk (+)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Kategori</label>
                    <select name="category" required class="neo-select">
                        <option value="petty_cash_expense">Pengeluaran Operasional / Petty Cash</option>
                        <option value="initial_balance">Modal Kas Awal</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="cash_amount_display" class="block text-xs font-black uppercase text-black mb-1">Nominal (Rp) *</label>
                <div class="flex rounded-xl overflow-hidden border-2 border-black bg-white shadow-[3px_3px_0px_0px_#000] focus-within:shadow-[4px_4px_0px_0px_#000] focus-within:ring-2 focus-within:ring-black transition-all">
                    <span class="inline-flex items-center px-3.5 bg-amber-300 border-r-2 border-black text-xs font-black text-black select-none">
                        Rp
                    </span>
                    <input type="text" 
                           id="cash_amount_display" 
                           inputmode="numeric"
                           placeholder="0" 
                           required 
                           autocomplete="off"
                           class="w-full px-3 py-2 text-sm font-black text-black bg-transparent focus:outline-none placeholder:text-slate-300 tracking-wide">
                </div>
                <input type="hidden" name="amount" id="cash_amount" value="">
                <span class="text-[10px] text-slate-500 font-semibold mt-1 block">Otomatis terformat rupiah (contoh: 50.000).</span>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Metode Pembayaran</label>
                <select name="payment_method" required class="neo-select">
                    <option value="cash">Tunai (Cash Laci)</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Keterangan Pengeluaran / Pemasukan</label>
                <input type="text" name="notes" placeholder="Misal: Beli lakban jilid & air galon cabang" required class="neo-input text-xs">
            </div>

            <div class="pt-3 border-t-2 border-black flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-expense-modal').close()" class="neo-btn bg-slate-200 text-xs py-2 px-4 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-4 font-black cursor-pointer">
                    Simpan Mutasi Kas
                </button>
            </div>
        </form>
    </div>
</dialog>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
