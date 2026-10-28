@extends('layouts.app')

@section('title', 'Daftar Order & Transaksi')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-black">Manajemen Order & Antrean Tiket</h1>
            <!-- <p class="text-xs font-semibold text-slate-600 mt-0.5">
                Semua pesanan desain kustom dan transaksi kasir ritel cepat di cabang.
            </p> -->
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <a href="{{ route('orders.create-design') }}" class="neo-btn bg-sky-400 hover:bg-sky-300 text-xs py-2 px-3 text-black font-black flex items-center justify-center gap-1.5">
                <x-icon name="plus" class="w-4 h-4" stroke-width="2.5" />
                <span>Order Baru</span>
            </a>
            <a href="{{ route('pos.index') }}" class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-3 text-black font-black flex items-center justify-center gap-1.5">
                <x-icon name="shopping-cart" class="w-4 h-4" />
                <span>Kasir POS</span>
            </a>
        </div>
    </div>

    <!-- Filter Card (Soft Neo-Brutalism Instant Filter) -->
    <div class="neo-box p-4 bg-white relative overflow-visible z-20">
        <form method="GET" action="{{ route('orders.index') }}" data-auto-submit class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Cari Order / Customer</label>
                <div class="flex items-center rounded-xl border-[1.5px] border-slate-300 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] focus-within:border-slate-800 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] bg-white transition-all overflow-hidden">
                    <button type="submit" aria-label="Cari" class="pl-3 pr-2 text-slate-400 hover:text-slate-800 flex items-center justify-center shrink-0 cursor-pointer transition-colors">
                        <x-icon name="search" class="w-3.5 h-3.5" />
                    </button>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="No. Order (ORD-...), Nama, No. HP... (Tekan Enter)"
                           class="w-full py-2 pr-3 text-xs font-bold text-slate-800 placeholder:text-slate-400 outline-none bg-transparent">
                </div>
            </div>

            <!-- Branch Filter (for owner) -->
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

            <!-- Work Status Filter -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Status Kerja</label>
                <select name="work_status" class="neo-select">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('work_status') == 'draft' ? 'selected' : '' }}>Draft / Antrean</option>
                    <option value="in_progress" {{ request('work_status') == 'in_progress' ? 'selected' : '' }}>Proses Pengerjaan</option>
                    <option value="revision" {{ request('work_status') == 'revision' ? 'selected' : '' }}>Revisi</option>
                    <option value="ready_to_print" {{ request('work_status') == 'ready_to_print' ? 'selected' : '' }}>Siap Cetak</option>
                    <option value="completed" {{ request('work_status') == 'completed' ? 'selected' : '' }}>Selesai / Diambil</option>
                    <option value="canceled" {{ request('work_status') == 'canceled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Payment Status Filter -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Status Bayar</label>
                <select name="payment_status" class="neo-select">
                    <option value="">Semua</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="partial_dp" {{ request('payment_status') == 'partial_dp' ? 'selected' : '' }}>DP (Sebagian)</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                </select>
            </div>

            <!-- Reset Action -->
            <div>
                <a href="{{ route('orders.index') }}" 
                   title="Reset semua filter"
                   class="w-full neo-btn {{ request()->anyFilled(['search', 'branch_id', 'work_status', 'payment_status']) ? 'bg-amber-200 hover:bg-amber-300 text-slate-900 border-slate-800 font-black' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold' }} text-xs py-2 px-3 flex items-center justify-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] transition-all">
                    <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                    <span>Reset Filter</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Filter Results Container (Live Smooth Replacement) -->
    <div id="filter-results-container">
        <!-- 1. Desktop & Tablet Orders Table (hidden on mobile) -->
        <div class="hidden sm:block neo-box bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[720px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-800 font-black uppercase tracking-wider">
                        <th class="py-3 px-4">No. Order</th>
                        <th class="py-3 px-4">Cabang</th>
                        <th class="py-3 px-4">Customer & Rincian</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Status Kerja</th>
                        <th class="py-3 px-4">Status Bayar</th>
                        <th class="py-3 px-4 text-right">Tagihan & Sisa</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/20">
                    @forelse($orders as $order)
                    <tr class="hover:bg-amber-50/40 transition-colors">
                        
                        <!-- Order Number & Date -->
                        <td class="py-3.5 px-4 font-mono font-bold text-black whitespace-nowrap">
                            <a href="{{ route('orders.show', $order->id) }}" class="underline hover:text-blue-700">
                                {{ $order->order_number }}
                            </a>
                            <div class="text-[10px] text-slate-500 font-sans font-normal mt-0.5">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </div>
                        </td>

                        <!-- Branch -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded border border-black bg-slate-100 font-bold text-[10px]">
                                {{ $order->branch->code }}
                            </span>
                        </td>

                        <!-- Customer & Title -->
                        <td class="py-3.5 px-4">
                            <div class="font-extrabold text-sm text-black">{{ $order->customer_name }}</div>
                            @if($order->customer_phone)
                                <div class="text-[11px] text-slate-500 font-medium">WA: {{ $order->customer_phone }}</div>
                            @endif
                            <div class="text-xs text-slate-700 font-semibold mt-0.5 truncate max-w-xs">
                                {{ $order->order_type === 'design_printing' ? ($order->designJob->title ?? 'Desain Kustom') : 'Transaksi Kasir POS Ritel' }}
                            </div>
                        </td>

                        <!-- Type -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded border border-black text-[10px] font-black {{ $order->order_type === 'design_printing' ? 'bg-sky-200' : 'bg-amber-200' }}">
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
                            <div class="font-extrabold text-black">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </div>
                            @if($order->remaining_amount > 0)
                                <div class="text-[11px] font-bold text-rose-600">
                                    Sisa: Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}
                                </div>
                            @else
                                <div class="text-[10px] font-bold text-emerald-700 flex items-center justify-end gap-1">
                                    <x-icon name="check" class="w-3 h-3 text-emerald-600" stroke-width="2.5" />
                                    <span>Lunas</span>
                                </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('orders.show', $order->id) }}" 
                                   class="px-2.5 py-1 rounded-lg border-2 border-black bg-white hover:bg-slate-100 font-bold text-xs shadow-[1px_1px_0px_0px_#000]">
                                    Detail
                                </a>
                                <a href="{{ route('orders.receipt', $order->id) }}" 
                                   target="_blank"
                                   class="px-2.5 py-1 rounded-lg border-2 border-black bg-slate-100 hover:bg-slate-200 font-bold text-xs shadow-[1px_1px_0px_0px_#000]">
                                    Struk
                                </a>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400 font-semibold">
                            Tidak ditemukan order yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

        <!-- 2. Mobile Native Order Cards (visible only on mobile) -->
        <div class="sm:hidden space-y-3">
            @forelse($orders as $order)
            <div class="p-3.5 bg-white border-[1.5px] border-slate-900 rounded-xl shadow-[2px_2px_0px_0px_#0f172a] space-y-2.5">
                <!-- Top Row: Cabang + No Order + Tanggal -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="px-1.5 py-0.5 rounded border border-black bg-slate-100 font-bold text-[10px] shrink-0">
                            {{ $order->branch->code }}
                        </span>
                        <a href="{{ route('orders.show', $order->id) }}" class="font-mono font-bold text-xs underline text-black hover:text-blue-700 truncate">
                            {{ $order->order_number }}
                        </a>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium shrink-0">
                        {{ $order->created_at->format('d/m/Y') }}
                    </span>
                </div>

                <!-- Customer & Items -->
                <div class="bg-slate-50/80 p-2.5 rounded-lg border border-slate-200 text-xs">
                    <div class="font-bold text-slate-900">{{ $order->customer_name }}</div>
                    <div class="text-[11px] text-slate-600 mt-0.5 leading-snug">
                        {{ $order->order_type === 'design_printing' ? ($order->designJob->title ?? 'Desain Kustom') : 'Transaksi Kasir POS Ritel' }}
                    </div>
                </div>

                <!-- Status Badges & Total -->
                <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                    <div class="flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-full border border-black text-[9px] font-black {{ $order->status_color_class }}">
                            {{ $order->status_label }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full border border-black text-[9px] font-black {{ $order->payment_status_color_class }}">
                            {{ $order->payment_status_label }}
                        </span>
                    </div>
                    <div class="text-right">
                        <div class="font-black text-sm text-black">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </div>
                        @if($order->remaining_amount > 0)
                            <div class="text-[10px] font-bold text-rose-600">
                                Sisa: Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons: Detail & Struk -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('orders.show', $order->id) }}" 
                       class="py-1.5 text-center rounded-lg border-[1.5px] border-black bg-white hover:bg-slate-100 font-bold text-xs shadow-[1px_1px_0px_0px_#000]">
                        Lihat Detail
                    </a>
                    <a href="{{ route('orders.receipt', $order->id) }}" 
                       target="_blank"
                       class="py-1.5 text-center rounded-lg border-[1.5px] border-black bg-amber-100 hover:bg-amber-200 font-bold text-xs shadow-[1px_1px_0px_0px_#000]">
                        Cetak Struk
                    </a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-400 font-semibold text-xs bg-white border-[1.5px] border-dashed border-slate-300 rounded-xl">
                Tidak ditemukan order yang sesuai dengan kriteria filter.
            </div>
            @endforelse
        </div>

        <!-- Pagination & Counter Footer -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
