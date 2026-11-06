@extends('layouts.app')

@section('title', 'Daftar Order & Transaksi')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-black dark:text-white">
                @if(Auth::user()->isOwner())
                    Manajemen Order & Antrean Tiket
                @else
                    Daftar Order & Antrean &bull; {{ Auth::user()->branch?->clean_name }}
                @endif
            </h1>
        </div>
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 sm:gap-3">
            <a href="{{ route('orders.create-design') }}" class="neo-btn bg-sky-400 hover:bg-sky-300 dark:text-slate-950 text-xs py-2 px-3.5 text-black font-black flex items-center gap-2 dark:border-slate-800 dark:shadow-[2px_2px_0px_0px_#000000]">
                <x-icon name="plus" class="w-4 h-4" stroke-width="2.5" />
                <span>Order Desain Baru</span>
            </a>
            <a href="{{ route('pos.index') }}" class="neo-btn bg-amber-300 hover:bg-amber-400 dark:text-slate-950 text-xs py-2 px-3.5 text-black font-black flex items-center gap-2 dark:border-slate-800 dark:shadow-[2px_2px_0px_0px_#000000]">
                <x-icon name="shopping-cart" class="w-4 h-4" />
                <span>Kasir POS Ritel</span>
            </a>
        </div>
    </div>

    <!-- Filter Card (Soft Neo-Brutalism Instant Filter) -->
    <div class="neo-box p-4 bg-white dark:bg-[#0c1222] relative overflow-visible z-20 border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000]">
        <form method="GET" action="{{ route('orders.index') }}" data-auto-submit class="grid grid-cols-1 sm:grid-cols-2 {{ Auth::user()->isOwner() ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-3 items-end">
            
            <!-- Search -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Cari Order / Customer</label>
                <div class="flex items-center h-10 rounded-xl border-[1.5px] border-slate-300 dark:border-slate-700 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] focus-within:border-slate-800 dark:focus-within:border-amber-400 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] dark:focus-within:shadow-[2.5px_2.5px_0px_0px_#000000] bg-white dark:bg-slate-900 transition-all overflow-hidden">
                    <button type="submit" aria-label="Cari" class="h-full pl-3 pr-2 text-slate-400 hover:text-slate-800 dark:hover:text-white flex items-center justify-center shrink-0 cursor-pointer transition-colors">
                        <x-icon name="search" class="w-3.5 h-3.5" />
                    </button>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="No. Order, nama, HP..."
                           autocomplete="off"
                           title="Ketik untuk mencari langsung otomatis"
                           class="w-full h-full pr-2 text-xs font-bold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 outline-none bg-transparent">
                    <button type="button" 
                            data-clear-search
                            title="Hapus pencarian" 
                            class="{{ request('search') ? '' : 'hidden' }} h-full pr-3 pl-1 text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 flex items-center justify-center shrink-0 cursor-pointer transition-colors">
                        <x-icon name="x" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            <!-- Branch Filter (for owner) -->
            @if(Auth::user()->isOwner())
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Cabang</label>
                @php
                    $branchOptions = ['' => 'Semua Cabang'];
                    foreach($branches as $b) {
                        $branchOptions[$b->id] = $b->code . ' - ' . $b->name;
                    }
                @endphp
                <x-neo-select name="branch_id" :options="$branchOptions" :value="request('branch_id')" />
            </div>
            @endif

            <!-- Work Status Filter -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Status Kerja</label>
                <x-neo-select 
                    name="work_status" 
                    :value="request('work_status')"
                    :options="[
                        '' => 'Semua Status',
                        'draft' => 'Draft / Antrean',
                        'in_progress' => 'Proses Pengerjaan',
                        'revision' => 'Revisi',
                        'ready_to_print' => 'Siap Cetak',
                        'completed' => 'Selesai / Diambil',
                        'canceled' => 'Dibatalkan',
                    ]" 
                />
            </div>

            <!-- Payment Status Filter -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Status Bayar</label>
                <x-neo-select 
                    name="payment_status" 
                    :value="request('payment_status')"
                    :options="[
                        '' => 'Semua',
                        'paid' => 'Lunas',
                        'partial_dp' => 'DP (Sebagian)',
                        'unpaid' => 'Belum Bayar',
                    ]" 
                />
            </div>

            <!-- Reset Action -->
            <div>
                <a href="{{ route('orders.index') }}" 
                   title="Reset semua filter"
                   class="w-full h-10 neo-btn {{ request()->anyFilled(['search', 'branch_id', 'work_status', 'payment_status']) ? 'bg-amber-200 hover:bg-amber-300 text-slate-900 dark:bg-amber-300 dark:text-slate-950 border-slate-800 font-black' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:border-slate-700 font-bold' }} text-xs px-3 flex items-center justify-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] transition-all">
                    <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                    <span>Reset Filter</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Filter Results Container (Live Smooth Replacement) -->
    <div id="filter-results-container">
        <!-- Orders Table (Soft Neo-Brutalism) -->
        <div class="neo-box bg-white dark:bg-[#0c1222] overflow-hidden border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 font-black uppercase tracking-wider">
                        <th class="py-3 px-4">No. Order</th>
                        @if(Auth::user()->isOwner())
                        <th class="py-3 px-4">Cabang</th>
                        @endif
                        <th class="py-3 px-4">Customer & Rincian</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Status Kerja</th>
                        <th class="py-3 px-4">Status Bayar</th>
                        <th class="py-3 px-4 text-right">Tagihan & Sisa</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/20 dark:divide-slate-800">
                    @forelse($orders as $order)
                    <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                        
                        <!-- Order Number & Date -->
                        <td class="py-3.5 px-4 font-mono font-bold text-black dark:text-white whitespace-nowrap">
                            <a href="{{ route('orders.show', $order->id) }}" class="underline hover:text-blue-700 dark:hover:text-amber-400">
                                {{ $order->order_number }}
                            </a>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-sans font-normal mt-0.5">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </div>
                        </td>

                        <!-- Branch (Khusus Owner) -->
                        @if(Auth::user()->isOwner())
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded border border-black dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-200 font-bold text-[10px]">
                                {{ $order->branch->code }}
                            </span>
                        </td>
                        @endif

                        <!-- Customer & Title -->
                        <td class="py-3.5 px-4">
                            <div class="font-extrabold text-sm text-black dark:text-white">{{ $order->customer_name }}</div>
                            @if($order->customer_phone)
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">WA: {{ $order->customer_phone }}</div>
                            @endif
                            <div class="text-xs text-slate-700 dark:text-slate-300 font-semibold mt-0.5 truncate max-w-xs">
                                {{ $order->order_type === 'design_printing' ? ($order->designJob->title ?? 'Desain Kustom') : 'Transaksi Kasir POS Ritel' }}
                            </div>
                        </td>

                        <!-- Type -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded border border-black text-[10px] font-black {{ $order->order_type === 'design_printing' ? 'bg-sky-200 text-slate-950' : 'bg-amber-200 text-slate-950' }}">
                                {{ $order->order_type === 'design_printing' ? 'DESAIN/PRINT' : 'RITEL POS' }}
                            </span>
                        </td>

                        <!-- Work Status -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full border border-black text-[10px] font-black {{ $order->status_color_class }}">
                                {{ $order->status_label }}
                            </span>
                        </td>

                        <!-- Payment Status -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-full border border-black text-[10px] font-black {{ $order->payment_status_badge_class }}">
                                {{ $order->payment_status_label }}
                            </span>
                        </td>

                        <!-- Total & Remaining -->
                        <td class="py-3.5 px-4 text-right whitespace-nowrap font-sans">
                            <div class="font-extrabold text-black dark:text-white">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </div>
                            @if($order->remaining_amount > 0)
                                <div class="text-[11px] font-bold text-rose-600 dark:text-rose-400">
                                    Sisa: Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}
                                </div>
                            @else
                                <div class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 flex items-center justify-end gap-1">
                                    <x-icon name="check" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" stroke-width="2.5" />
                                    <span>Lunas</span>
                                </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('orders.show', $order->id) }}" 
                                   title="Lihat Detail Order"
                                   aria-label="Detail Order"
                                   class="w-7 h-7 rounded-lg border-[1.5px] border-black dark:border-slate-700 bg-white hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-black dark:text-white shadow-[1px_1px_0px_0px_#000] dark:shadow-[1px_1px_0px_0px_#000000] flex items-center justify-center transition-all cursor-pointer">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('orders.receipt', $order->id) }}" 
                                   target="_blank"
                                   title="Cetak Struk Kasir Thermal"
                                   aria-label="Cetak Struk"
                                   class="w-7 h-7 rounded-lg border-[1.5px] border-black dark:border-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-black dark:text-white shadow-[1px_1px_0px_0px_#000] dark:shadow-[1px_1px_0px_0px_#000000] flex items-center justify-center transition-all cursor-pointer">
                                    <x-icon name="printer" class="w-3.5 h-3.5 text-slate-700 dark:text-slate-300" />
                                </a>
                                <a href="{{ route('orders.invoice', $order->id) }}" 
                                   target="_blank"
                                   title="Cetak Invoice Formal A4"
                                   aria-label="Cetak Invoice"
                                   class="w-7 h-7 rounded-lg border-[1.5px] border-black dark:border-slate-700 bg-amber-300 hover:bg-amber-400 text-black dark:text-slate-950 shadow-[1px_1px_0px_0px_#000] dark:shadow-[1px_1px_0px_0px_#000000] flex items-center justify-center transition-all cursor-pointer">
                                    <x-icon name="file-text" class="w-3.5 h-3.5 text-slate-950" />
                                </a>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->isOwner() ? '8' : '7' }}" class="py-12 text-center text-slate-400 dark:text-slate-500 font-semibold">
                            Tidak ditemukan order yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Counter Footer -->
        {{ $orders->links() }}
    </div>
    </div>

</div>
@endsection
