@extends('layouts.app')

@section('title', 'Dashboard Owner Multi-Cabang')

@section('content')
<div class="space-y-8">
    
    <!-- Top Welcome & Global Status Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 bg-purple-100 border-2 border-black rounded-xl shadow-[3px_3px_0px_0px_#000]">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-600 animate-pulse"></span>
                <span class="text-[11px] font-black uppercase tracking-wider text-purple-900">Owner Command Center</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black text-black">Monitor Omset & Operasional Multi-Cabang</h1>
            <!-- <p class="text-xs font-semibold text-slate-700 mt-0.5">
                Data real-time gabungan seluruh cabang dan sinkronisasi arsip desain terpusat.
            </p> -->
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('orders.create-design') }}" class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-3.5 text-black flex items-center gap-1.5">
                <x-icon name="plus" class="w-3.5 h-3.5" stroke-width="2.5" />
                <span>Order Desain</span>
            </a>
            <a href="{{ route('cash.index') }}" class="neo-btn bg-white hover:bg-slate-100 text-xs py-2 px-3.5 text-black flex items-center gap-1.5">
                <x-icon name="wallet" class="w-3.5 h-3.5" />
                <span>Audit Buku Kas</span>
            </a>
        </div>
    </div>

    <!-- 5 Global Metric Stat Cards (Neo-Brutalism Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        
        <!-- Omset Hari Ini -->
        <div class="p-4 bg-white border-2 border-black rounded-xl shadow-[3px_3px_0px_0px_#000] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-500 mb-1 flex items-center justify-between">
                    <span>Omset Hari Ini (Gabungan)</span>
                    <x-icon name="trending-up" class="w-4 h-4 text-emerald-600" />
                </div>
                <div class="text-xl font-black text-black tracking-tight">
                    Rp {{ number_format($totalTurnoverToday, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-200 text-[10px] font-bold text-emerald-700 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Real-time gabungan cabang</span>
            </div>
        </div>

        <!-- Omset Bulan Ini -->
        <div class="p-4 bg-white border-2 border-black rounded-xl shadow-[3px_3px_0px_0px_#000] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-500 mb-1 flex items-center justify-between">
                    <span>Omset Bulan Ini</span>
                    <x-icon name="calendar" class="w-4 h-4 text-blue-600" />
                </div>
                <div class="text-xl font-black text-black tracking-tight">
                    Rp {{ number_format($totalTurnoverMonth, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-200 text-[10px] font-bold text-slate-600">
                Periode {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('MMMM Y') }}
            </div>
        </div>

        <!-- Total Kas Aktif -->
        <div class="p-4 bg-amber-50 border-2 border-black rounded-xl shadow-[3px_3px_0px_0px_#000] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-700 mb-1 flex items-center justify-between">
                    <span>Saldo Kas Aktif</span>
                    <x-icon name="wallet" class="w-4 h-4 text-slate-900" />
                </div>
                <div class="text-xl font-black text-black tracking-tight">
                    Rp {{ number_format($totalCashBalance, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-amber-200 text-[10px] font-bold text-slate-700">
                Total fisik laci kasir
            </div>
        </div>

        <!-- Order Desain Aktif -->
        <div class="p-4 bg-sky-50 border-2 border-black rounded-xl shadow-[3px_3px_0px_0px_#000] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-700 mb-1 flex items-center justify-between">
                    <span>Antrean Desain</span>
                    <x-icon name="palette" class="w-4 h-4 text-sky-800" />
                </div>
                <div class="text-xl font-black text-black tracking-tight">
                    {{ $activeDesignsCount }} Tiket
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-sky-200 text-[10px] font-bold text-blue-700">
                Draft, In Progress & Revisi
            </div>
        </div>

        <!-- Siap Cetak -->
        <div class="p-4 bg-emerald-50 border-2 border-black rounded-xl shadow-[3px_3px_0px_0px_#000] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-700 mb-1 flex items-center justify-between">
                    <span>Siap Cetak</span>
                    <x-icon name="printer" class="w-4 h-4 text-emerald-800" />
                </div>
                <div class="text-xl font-black text-black tracking-tight">
                    {{ $readyToPrintCount }} Tiket
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-emerald-200 text-[10px] font-bold text-emerald-800">
                File final terunggah
            </div>
        </div>

    </div>

    <!-- Comparative Branch Breakdown Cards -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-black flex items-center gap-2.5">
                <div class="w-7 h-7 bg-yellow-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                    <x-icon name="building-2" class="w-4 h-4" />
                </div>
                <span>Performa Komparasi Antar Cabang</span>
            </h2>
            <a href="{{ route('branches.index') }}" class="text-xs font-extrabold text-blue-600 hover:underline">
                Kelola Cabang →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($branches as $branch)
            <div class="neo-box p-6 bg-white hover:bg-slate-50 transition-colors">
                <div class="flex items-start justify-between pb-4 border-b-2 border-black mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black px-2 py-0.5 rounded bg-black text-white">
                                {{ $branch->code }}
                            </span>
                            <h3 class="font-black text-lg text-black">{{ $branch->name }}</h3>
                        </div>
                        <p class="text-xs font-medium text-slate-500 mt-1">{{ $branch->address ?? 'Alamat belum diatur' }}</p>
                    </div>
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 border border-black px-2 py-1 rounded">
                        {{ $branch->phone ?? '-' }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="p-3 bg-slate-100 border-2 border-black rounded-lg">
                        <div class="text-[10px] font-black uppercase text-slate-500">Omset Hari Ini</div>
                        <div class="text-base font-black text-black mt-0.5">
                            Rp {{ number_format($branch->today_turnover, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-3 bg-slate-100 border-2 border-black rounded-lg">
                        <div class="text-[10px] font-black uppercase text-slate-500">Saldo Kas Laci</div>
                        <div class="text-base font-black text-black mt-0.5">
                            Rp {{ number_format($branch->cash_balance, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-3 bg-slate-100 border-2 border-black rounded-lg">
                        <div class="text-[10px] font-black uppercase text-slate-500">Order Berjalan</div>
                        <div class="text-base font-black text-black mt-0.5">
                            {{ $branch->active_orders_count }} Order
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                    <a href="{{ route('orders.index', ['branch_id' => $branch->id]) }}" class="text-xs font-bold text-black hover:underline px-2 py-1">
                        Lihat Order
                    </a>
                    <a href="{{ route('cash.index', ['branch_id' => $branch->id]) }}" class="text-xs font-bold text-black hover:underline px-2 py-1">
                        Buku Kas Cabang
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Two-Column Split: Recent Orders & Recent Design Uploads -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Recent Orders Across All Branches (7 cols) -->
        <div class="lg:col-span-7 neo-box p-6 bg-white">
            <div class="flex items-center justify-between pb-4 border-b-2 border-black mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-sky-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                        <x-icon name="clipboard-list" class="w-4 h-4" />
                    </div>
                    <h2 class="font-black text-base text-black">Aktivitas Order Terkini (Semua Cabang)</h2>
                </div>
                <a href="{{ route('orders.index') }}" class="text-xs font-extrabold text-blue-600 hover:underline">
                    Semua Order ({{ \App\Models\Order::count() }}) →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b-2 border-black bg-slate-100 text-black uppercase font-black">
                            <th class="py-2.5 px-3">No. Order</th>
                            <th class="py-2.5 px-3">Cabang</th>
                            <th class="py-2.5 px-3">Customer</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/20">
                        @forelse($recentOrders as $ord)
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="py-3 px-3 font-mono font-bold text-black">
                                <a href="{{ route('orders.show', $ord->id) }}" class="underline hover:text-blue-700">
                                    {{ $ord->order_number }}
                                </a>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-1.5 py-0.5 rounded border border-black bg-slate-200 text-[10px] font-bold">
                                    {{ $ord->branch->code }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-800">
                                {{ $ord->customer_name }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-full border border-black text-[10px] font-black {{ $ord->status_color_class }}">
                                    {{ $ord->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-bold text-right text-black">
                                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400 font-semibold">
                                Belum ada order tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Recent Central Cloud Archives (5 cols) -->
        <div class="lg:col-span-5 neo-box p-6 bg-white">
            <div class="flex items-center justify-between pb-4 border-b-2 border-black mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-emerald-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                        <x-icon name="archive" class="w-4 h-4" />
                    </div>
                    <h2 class="font-black text-base text-black">File Desain Siap Cetak Masuk</h2>
                </div>
                <a href="{{ route('archive.index') }}" class="text-xs font-extrabold text-blue-600 hover:underline">
                    Buka Arsip →
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentAssets as $ast)
                <div class="p-3 bg-slate-50 border-2 border-black rounded-xl flex items-center justify-between gap-3 shadow-[2px_2px_0px_0px_#000]">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-black px-1.5 py-0.2 rounded border border-black {{ $ast->file_type === 'final_print' ? 'bg-emerald-300' : 'bg-yellow-200' }}">
                                {{ $ast->file_type_badge }}
                            </span>
                            <span class="text-xs font-bold text-slate-500 font-mono">
                                {{ $ast->designJob->order->order_number ?? '' }}
                            </span>
                        </div>
                        <div class="text-xs font-black text-black truncate mt-1">
                            {{ $ast->file_name }}
                        </div>
                        <div class="text-[10px] text-slate-500 font-semibold mt-0.5">
                            {{ $ast->designJob->order->branch->name ?? '' }} &bull; {{ $ast->formatted_size }}
                        </div>
                    </div>
                    <a href="{{ route('archive.download', $ast->id) }}" class="neo-btn bg-white hover:bg-slate-100 text-xs py-1.5 px-3 whitespace-nowrap flex items-center gap-1.5">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        <span>Download</span>
                    </a>
                </div>
                @empty
                <div class="p-8 text-center border-2 border-dashed border-slate-300 rounded-xl text-xs font-semibold text-slate-400">
                    Belum ada aset file desain yang diunggah desainer.
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
