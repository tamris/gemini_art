@extends('layouts.app')

@section('title', 'Dashboard Owner Multi-Cabang')

@section('content')
<div class="space-y-8">
    
    <!-- Top Welcome & Global Status Header -->
    <!-- <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 bg-purple-100 dark:bg-[#121124] border-2 border-black dark:border-slate-500 rounded-xl shadow-[3px_3px_0px_0px_#000] dark:shadow-[3.5px_3.5px_0px_0px_#334155]">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-600 dark:bg-purple-400 animate-pulse"></span>
                <span class="text-[11px] font-black uppercase tracking-wider text-purple-900 dark:text-purple-300">Owner Command Center</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black text-black dark:text-white">Monitor Omset & Operasional Multi-Cabang</h1>
            <p class="text-xs font-semibold text-slate-700 mt-0.5">
                Data real-time gabungan seluruh cabang dan sinkronisasi arsip desain terpusat.
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('orders.create-design') }}" class="neo-btn bg-amber-300 hover:bg-amber-400 dark:bg-amber-300 dark:text-slate-950 text-xs py-2 px-3.5 text-black flex items-center gap-1.5 dark:border-slate-500 dark:shadow-[2.5px_2.5px_0px_0px_#334155]">
                <x-icon name="plus" class="w-3.5 h-3.5" stroke-width="2.5" />
                <span>Order Desain</span>
            </a>
            <a href="{{ route('cash.index') }}" class="neo-btn bg-white hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-white dark:border-slate-500 text-xs py-2 px-3.5 text-black flex items-center gap-1.5 dark:shadow-[2.5px_2.5px_0px_0px_#334155]">
                <x-icon name="wallet" class="w-3.5 h-3.5" />
                <span>Audit Buku Kas</span>
            </a>
        </div>
    </div> -->

    <!-- 5 Global Metric Stat Cards (Neo-Brutalism Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        
        <!-- Omset Hari Ini -->
        <div class="p-4 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-500 rounded-xl shadow-[3px_3px_0px_0px_#000] dark:shadow-[3.5px_3.5px_0px_0px_#334155] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1 flex items-center justify-between">
                    <span>Omset Hari Ini (Gabungan)</span>
                    <x-icon name="trending-up" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div class="text-xl font-black text-black dark:text-white tracking-tight">
                    Rp {{ number_format($totalTurnoverToday, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-800 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Real-time gabungan cabang</span>
            </div>
        </div>

        <!-- Omset Bulan Ini -->
        <div class="p-4 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-500 rounded-xl shadow-[3px_3px_0px_0px_#000] dark:shadow-[3.5px_3.5px_0px_0px_#334155] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1 flex items-center justify-between">
                    <span>Omset Bulan Ini</span>
                    <x-icon name="calendar" class="w-4 h-4 text-blue-600 dark:text-sky-400" />
                </div>
                <div class="text-xl font-black text-black dark:text-white tracking-tight">
                    Rp {{ number_format($totalTurnoverMonth, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-400">
                Periode {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('MMMM Y') }}
            </div>
        </div>

        <!-- Total Kas Aktif -->
        <div class="p-4 bg-amber-50 dark:bg-[#0c1222] border-2 border-black dark:border-slate-500 rounded-xl shadow-[3px_3px_0px_0px_#000] dark:shadow-[3.5px_3.5px_0px_0px_#334155] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                    <span>Saldo Kas Aktif</span>
                    <x-icon name="wallet" class="w-4 h-4 text-slate-900 dark:text-slate-300" />
                </div>
                <div class="text-xl font-black text-black dark:text-white tracking-tight">
                    Rp {{ number_format($totalCashBalance, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-amber-200 dark:border-slate-800 text-[10px] font-bold text-slate-700 dark:text-slate-400">
                Total fisik laci kasir
            </div>
        </div>

        <!-- Order Desain Aktif -->
        <div class="p-4 bg-sky-50 dark:bg-[#0c1222] border-2 border-black dark:border-slate-500 rounded-xl shadow-[3px_3px_0px_0px_#000] dark:shadow-[3.5px_3.5px_0px_0px_#334155] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                    <span>Antrean Desain</span>
                    <x-icon name="palette" class="w-4 h-4 text-sky-800 dark:text-sky-400" />
                </div>
                <div class="text-xl font-black text-black dark:text-white tracking-tight">
                    {{ $activeDesignsCount }} Tiket
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-sky-200 dark:border-slate-800 text-[10px] font-bold text-blue-700 dark:text-sky-400">
                Draft, In Progress & Revisi
            </div>
        </div>

        <!-- Siap Cetak -->
        <div class="p-4 bg-emerald-50 dark:bg-[#0c1222] border-2 border-black dark:border-slate-500 rounded-xl shadow-[3px_3px_0px_0px_#000] dark:shadow-[3.5px_3.5px_0px_0px_#334155] flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-black uppercase text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                    <span>Siap Cetak</span>
                    <x-icon name="printer" class="w-4 h-4 text-emerald-800 dark:text-emerald-400" />
                </div>
                <div class="text-xl font-black text-black dark:text-white tracking-tight">
                    {{ $readyToPrintCount }} Tiket
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-emerald-200 dark:border-slate-800 text-[10px] font-bold text-emerald-800 dark:text-emerald-400">
                File final terunggah
            </div>
        </div>

    </div>

    <!-- Comparative Branch Breakdown Cards -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-black dark:text-white flex items-center gap-2.5">
                <div class="w-7 h-7 bg-yellow-300 dark:bg-amber-300 border-2 border-black dark:border-slate-500 rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000] dark:shadow-[1.5px_1.5px_0px_0px_#334155]">
                    <x-icon name="building-2" class="w-4 h-4" />
                </div>
                <span>Performa Komparasi Antar Cabang</span>
            </h2>
            <a href="{{ route('branches.index') }}" class="text-xs font-extrabold text-blue-600 dark:text-sky-400 hover:underline">
                Kelola Cabang →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($branches as $branch)
            <div class="neo-box p-6 bg-white dark:bg-[#0c1222] hover:bg-slate-50 dark:hover:bg-slate-900/60 transition-colors border-2 border-black dark:border-slate-500 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3.5px_3.5px_0px_0px_#334155]">
                <div class="flex items-start justify-between pb-4 border-b-2 border-black dark:border-slate-800 mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black px-2 py-0.5 rounded bg-black dark:bg-slate-800 text-white dark:text-slate-100 border border-black dark:border-slate-500">
                                {{ $branch->code }}
                            </span>
                            <h3 class="font-black text-lg text-black dark:text-white">{{ $branch->name }}</h3>
                        </div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">{{ $branch->address ?? 'Alamat belum diatur' }}</p>
                    </div>
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border border-black dark:border-slate-500 px-2 py-1 rounded">
                        {{ $branch->phone ?? '-' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3 mb-4">
                    <div class="p-3 bg-slate-100 dark:bg-slate-900 border-2 border-black dark:border-slate-600 rounded-lg shadow-[1.5px_1.5px_0px_0px_#000] dark:shadow-[2px_2px_0px_0px_#334155]">
                        <div class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Omset Hari Ini</div>
                        <div class="text-sm sm:text-base font-black text-black dark:text-white mt-0.5 truncate">
                            Rp {{ number_format($branch->today_turnover, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-3 bg-slate-100 dark:bg-slate-900 border-2 border-black dark:border-slate-600 rounded-lg shadow-[1.5px_1.5px_0px_0px_#000] dark:shadow-[2px_2px_0px_0px_#334155]">
                        <div class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Saldo Kas Laci</div>
                        <div class="text-sm sm:text-base font-black text-black dark:text-white mt-0.5 truncate">
                            Rp {{ number_format($branch->cash_balance, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-3 bg-slate-100 dark:bg-slate-900 border-2 border-black dark:border-slate-600 rounded-lg shadow-[1.5px_1.5px_0px_0px_#000] dark:shadow-[2px_2px_0px_0px_#334155]">
                        <div class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400">Order Berjalan</div>
                        <div class="text-sm sm:text-base font-black text-black dark:text-white mt-0.5 truncate">
                            {{ $branch->active_orders_count }} Order
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('orders.index', ['branch_id' => $branch->id]) }}" class="text-xs font-bold text-black dark:text-slate-300 hover:text-blue-600 dark:hover:text-white hover:underline px-2 py-1">
                        Lihat Order
                    </a>
                    <a href="{{ route('cash.index', ['branch_id' => $branch->id]) }}" class="text-xs font-bold text-black dark:text-slate-300 hover:text-blue-600 dark:hover:text-white hover:underline px-2 py-1">
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
        <div class="lg:col-span-7 neo-box p-6 bg-white dark:bg-[#0c1222] flex flex-col justify-between border-2 border-black dark:border-slate-500 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3.5px_3.5px_0px_0px_#334155]">
            <div>
                <div class="flex items-center justify-between pb-4 border-b-2 border-black dark:border-slate-800 mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-sky-300 border-2 border-black dark:border-slate-500 rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000] dark:shadow-[1.5px_1.5px_0px_0px_#334155]">
                            <x-icon name="clipboard-list" class="w-4 h-4" />
                        </div>
                        <h2 class="font-black text-base text-black dark:text-white">Aktivitas Order Terkini (Semua Cabang)</h2>
                    </div>
                    <a href="{{ route('orders.index') }}" class="text-xs font-extrabold text-blue-600 dark:text-sky-400 hover:underline">
                        Semua Order ({{ \App\Models\Order::count() }}) →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b-2 border-black dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-black dark:text-slate-200 uppercase font-black">
                                <th class="py-2.5 px-3">No. Order</th>
                                <th class="py-2.5 px-3">Cabang</th>
                                <th class="py-2.5 px-3">Customer & Rincian</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/20 dark:divide-slate-800">
                            @forelse($recentOrders as $ord)
                            <tr class="hover:bg-amber-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-2.5 px-3 font-mono font-bold text-black dark:text-white whitespace-nowrap">
                                    <a href="{{ route('orders.show', $ord->id) }}" class="underline hover:text-blue-700 dark:hover:text-white">
                                        {{ $ord->order_number }}
                                    </a>
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    <span class="px-1.5 py-0.5 rounded border border-black dark:border-slate-500 bg-slate-200 dark:bg-slate-800 text-slate-900 dark:text-slate-200 text-[10px] font-bold">
                                        {{ $ord->branch->code }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="font-black text-slate-900 dark:text-white leading-tight">
                                        {{ $ord->customer_name }}
                                    </div>
                                    @if($ord->designJob && $ord->designJob->title)
                                    <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 truncate max-w-[180px] sm:max-w-[220px]" title="{{ $ord->designJob->title }}">
                                        {{ $ord->designJob->title }}
                                    </div>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full border border-black dark:border-slate-500 text-[10px] font-black {{ $ord->status_color_class }}">
                                        {{ $ord->status_label }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 font-bold text-right text-black dark:text-white whitespace-nowrap">
                                    Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400 dark:text-slate-500 font-semibold">
                                    Belum ada order tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mini Footer Counter -->
            <div class="flex items-center justify-between pt-3 border-t-2 border-black/10 dark:border-slate-800 mt-3 text-xs font-bold text-slate-500 dark:text-slate-400">
                <span class="text-[11px]">Menampilkan <strong>{{ $recentOrders->count() }}</strong> aktivitas order terbaru</span>
                <a href="{{ route('orders.index') }}" class="text-blue-600 dark:text-sky-400 hover:text-blue-800 dark:hover:text-sky-300 hover:underline font-black text-xs flex items-center gap-1">
                    <span>Lihat Semua Order</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <!-- Right: Recent Central Cloud Archives (5 cols) -->
        <div class="lg:col-span-5 neo-box p-6 bg-white dark:bg-[#0c1222] flex flex-col justify-between border-2 border-black dark:border-slate-500 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3.5px_3.5px_0px_0px_#334155]">
            <div>
                <div class="flex items-center justify-between pb-4 border-b-2 border-black dark:border-slate-800 mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-emerald-300 border-2 border-black dark:border-slate-500 rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000] dark:shadow-[1.5px_1.5px_0px_0px_#334155]">
                            <x-icon name="archive" class="w-4 h-4" />
                        </div>
                        <h2 class="font-black text-base text-black dark:text-white">File Desain Siap Cetak Masuk</h2>
                    </div>
                    <a href="{{ route('archive.index') }}" class="text-xs font-extrabold text-blue-600 dark:text-sky-400 hover:underline">
                        Buka Arsip →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentAssets as $ast)
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 border-2 border-black dark:border-slate-600 rounded-xl flex items-center justify-between gap-3 shadow-[2px_2px_0px_0px_#000] dark:shadow-[2.5px_2.5px_0px_0px_#334155]">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-black px-1.5 py-0.2 rounded border border-black dark:border-slate-500 {{ $ast->file_type === 'final_print' ? 'bg-emerald-300 text-slate-950' : 'bg-yellow-200 text-slate-950' }}">
                                    {{ $ast->file_type_badge }}
                                </span>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 font-mono">
                                    {{ $ast->designJob->order->order_number ?? '' }}
                                </span>
                            </div>
                            <div class="text-xs font-black text-black dark:text-white truncate mt-1">
                                {{ $ast->file_name }}
                            </div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold mt-0.5">
                                {{ $ast->designJob->order->branch->name ?? '' }} &bull; {{ $ast->formatted_size }}
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" 
                                    onclick="openFilePreview('{{ route('archive.preview', $ast->id) }}', '{{ addslashes($ast->file_name) }}', '{{ $ast->formatted_size }}', '{{ $ast->file_type_badge }}', '{{ str_ends_with(strtolower($ast->file_name), '.pdf') ? 'pdf' : ($ast->is_image ? 'image' : 'other') }}', '{{ route('archive.download', $ast->id) }}')" 
                                    title="Pratinjau Desain" 
                                    class="w-8 h-8 rounded-lg border-2 border-black dark:border-slate-500 bg-sky-200 hover:bg-sky-300 dark:bg-sky-500/20 dark:hover:bg-sky-500/30 dark:text-sky-300 text-slate-900 flex items-center justify-center shadow-[1.5px_1.5px_0px_0px_#000] dark:shadow-[1.5px_1.5px_0px_0px_#334155] active:translate-x-[1px] active:translate-y-[1px] cursor-pointer transition-all">
                                <x-icon name="eye" class="w-4 h-4 stroke-[2.2]" />
                            </button>
                            <a href="{{ route('archive.download', $ast->id) }}" 
                               title="Download Berkas" 
                               class="w-8 h-8 rounded-lg border-2 border-black dark:border-slate-500 bg-emerald-300 hover:bg-emerald-400 dark:bg-emerald-500/20 dark:hover:bg-emerald-500/30 dark:text-emerald-300 text-slate-950 flex items-center justify-center shadow-[1.5px_1.5px_0px_0px_#000] dark:shadow-[1.5px_1.5px_0px_0px_#334155] active:translate-x-[1px] active:translate-y-[1px] cursor-pointer transition-all">
                                <x-icon name="download" class="w-4 h-4 stroke-[2.5]" />
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center border-2 border-dashed border-slate-300 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-400 dark:text-slate-500">
                        Belum ada aset file desain yang diunggah desainer.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Mini Footer Counter -->
            <div class="flex items-center justify-between pt-3 border-t-2 border-black/10 dark:border-slate-800 mt-3 text-xs font-bold text-slate-500 dark:text-slate-400">
                <span class="text-[11px]">Menampilkan <strong>{{ $recentAssets->count() }}</strong> berkas cetak siap produksi</span>
                <a href="{{ route('archive.index') }}" class="text-blue-600 dark:text-sky-400 hover:text-blue-800 dark:hover:text-sky-300 hover:underline font-black text-xs flex items-center gap-1">
                    <span>Semua Arsip Cloud</span>
                    <span>→</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
