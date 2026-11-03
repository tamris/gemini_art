@extends('layouts.app')

@section('title', 'Laporan Keuangan & Analisis Laba Rugi')

@section('content')
<style>
    @media print {
        .no-print, aside, header, footer, #sidebar-backdrop { display: none !important; }
        body { background: white !important; padding: 0 !important; color: #0f172a !important; }
        .main-content, main, .print-container { padding: 0 !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        .neo-box, .print-card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; border-radius: 8px !important; }
        .print-header { display: block !important; }
        @page { size: A4 portrait; margin: 12mm 14mm; }
    }
    .print-header { display: none; }
</style>

<div class="print-container space-y-6">

    <!-- Print-Only Formal Header -->
    <div class="print-header pb-4 mb-4 border-b-2 border-slate-900">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-900 text-white font-black text-lg flex items-center justify-center">
                    GA
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-950 uppercase tracking-tight">GEMINI ART MULTI-CABANG</h1>
                    <p class="text-xs text-slate-600 font-medium">Laporan Keuangan & Rekapitulasi Laba Rugi Operasional</p>
                </div>
            </div>
            <div class="text-right text-xs text-slate-600">
                <div>Periode: <strong>{{ $periodLabel }}</strong></div>
                <div>Cabang: <strong>{{ $selectedBranch ? $selectedBranch->name : 'Konsolidasi Seluruh Cabang' }}</strong></div>
                <div>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Header Section (Screen Only) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-[11px] font-black uppercase tracking-wider text-emerald-900 dark:text-emerald-400">
                    Financial Analytics & Profit/Loss
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Laporan Keuangan & Laba Rugi</h1>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" 
                    class="neo-btn bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 dark:border-slate-700 text-xs py-2 px-4 font-black text-white flex items-center gap-2 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]">
                <x-icon name="printer" class="w-4 h-4 text-amber-300" />
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar (Screen Only - Auto Instant Filter) -->
    <div class="no-print neo-box p-4 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] relative overflow-visible z-20">
        <form method="GET" action="{{ route('reports.finance') }}" data-auto-submit class="flex flex-wrap items-end gap-3">
            
            <!-- Filter Cabang (Owner Only) -->
            @if(Auth::user()->isOwner())
            <div class="w-full sm:w-60">
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Cabang</label>
                <select name="branch_id" class="neo-select w-full">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>
                            {{ $b->code }} - {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Filter Periode Cepat -->
            <div class="w-full sm:w-56">
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Rentang Waktu</label>
                <select name="period" id="period-selector" onchange="toggleCustomDates(this.value);" class="neo-select w-full">
                    <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Pilih Rentang Tanggal Bebas...</option>
                </select>
            </div>

            <!-- Custom Date Range Pickers (Bespoke Soft Neo-Datepicker) -->
            <div id="custom-date-container" class="{{ $period === 'custom' ? 'flex' : 'hidden' }} flex-wrap items-end gap-3 w-full sm:w-auto">
                
                <!-- Dari Tanggal -->
                <div class="relative w-full sm:w-44" data-neo-datepicker-container data-mode="date" data-placeholder="Dari Tanggal...">
                    <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Dari</label>
                    <input type="hidden" name="date_from" value="{{ request('date_from', $startDate->format('Y-m-d')) }}">

                    <button type="button" 
                            data-datepicker-toggle
                            class="w-full flex items-center justify-between bg-white dark:bg-slate-900 border-[1.5px] border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] hover:border-slate-800 dark:hover:border-slate-600 focus:outline-none cursor-pointer text-left h-[40px] transition-all">
                        <span data-datepicker-text class="truncate {{ request('date_from') ? 'text-slate-900 dark:text-white font-extrabold' : 'text-slate-400 dark:text-slate-500 font-medium' }}">
                            {{ request('date_from') ? date('d/m/Y', strtotime(request('date_from'))) : 'Dari Tanggal...' }}
                        </span>
                        <x-icon name="calendar" class="w-3.5 h-3.5 text-slate-500 shrink-0 stroke-[2.5]" />
                    </button>

                    <!-- Popover -->
                    <div data-datepicker-popover class="hidden absolute top-[calc(100%+6px)] left-0 z-50 bg-white dark:bg-[#0c1222] border-2 border-slate-900 dark:border-slate-700 rounded-2xl shadow-[4px_4px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#000000] overflow-hidden select-none w-[235px]">
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
                        <div class="h-6 grid grid-cols-7 text-center items-center px-2 bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-[10px] font-black uppercase text-slate-600 dark:text-slate-400 shrink-0">
                            <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                        </div>
                        <div class="h-[204px] p-2 bg-white dark:bg-[#0c1222] shrink-0">
                            <div data-cal-days class="grid grid-cols-7 gap-1"></div>
                        </div>
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

                <!-- Sampai Tanggal -->
                <div class="relative w-full sm:w-44" data-neo-datepicker-container data-mode="date" data-placeholder="Sampai Tanggal...">
                    <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Sampai</label>
                    <input type="hidden" name="date_to" value="{{ request('date_to', $endDate->format('Y-m-d')) }}">

                    <button type="button" 
                            data-datepicker-toggle
                            class="w-full flex items-center justify-between bg-white dark:bg-slate-900 border-[1.5px] border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] hover:border-slate-800 dark:hover:border-slate-600 focus:outline-none cursor-pointer text-left h-[40px] transition-all">
                        <span data-datepicker-text class="truncate {{ request('date_to') ? 'text-slate-900 dark:text-white font-extrabold' : 'text-slate-400 dark:text-slate-500 font-medium' }}">
                            {{ request('date_to') ? date('d/m/Y', strtotime(request('date_to'))) : 'Sampai Tanggal...' }}
                        </span>
                        <x-icon name="calendar" class="w-3.5 h-3.5 text-slate-500 shrink-0 stroke-[2.5]" />
                    </button>

                    <!-- Popover -->
                    <div data-datepicker-popover class="hidden absolute top-[calc(100%+6px)] left-0 z-50 bg-white dark:bg-[#0c1222] border-2 border-slate-900 dark:border-slate-700 rounded-2xl shadow-[4px_4px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#000000] overflow-hidden select-none w-[235px]">
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
                        <div class="h-6 grid grid-cols-7 text-center items-center px-2 bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-[10px] font-black uppercase text-slate-600 dark:text-slate-400 shrink-0">
                            <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                        </div>
                        <div class="h-[204px] p-2 bg-white dark:bg-[#0c1222] shrink-0">
                            <div data-cal-days class="grid grid-cols-7 gap-1"></div>
                        </div>
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

            </div>

            <!-- Reset Button (Compact) -->
            <div class="w-auto">
                <a href="{{ route('reports.finance') }}" 
                   title="Reset Filter ke Default" 
                   class="neo-btn {{ request()->anyFilled(['branch_id', 'date_from', 'date_to']) || (request('period') && request('period') !== 'this_month') ? 'bg-amber-200 dark:bg-amber-300 dark:text-slate-950 hover:bg-amber-300 text-slate-900 border-slate-800 font-black' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 text-slate-600 font-bold dark:border-slate-700' }} text-xs py-2 px-3.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] flex items-center justify-center gap-1.5 transition-all h-[40px]">
                    <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                    <span>Reset</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Live Filter Results Container (Smooth Seamless Replacement) -->
    <div id="filter-results-container" class="space-y-6">

        <!-- Active Filter Ribbon -->
        <div class="px-4 py-2.5 rounded-xl border-[1.5px] border-slate-800 dark:border-slate-700 bg-amber-50 dark:bg-amber-950/30 text-xs font-bold text-slate-800 dark:text-amber-300 flex flex-wrap items-center justify-between gap-2 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md border border-slate-800 dark:border-slate-700 bg-white dark:bg-slate-800 font-black text-[10px] uppercase text-slate-900 dark:text-white">
                    {{ $selectedBranch ? $selectedBranch->code : 'PUSAT' }}
                </span>
                <span>Cakupan: <strong>{{ $selectedBranch ? $selectedBranch->name : 'Seluruh Cabang (Konsolidasi)' }}</strong></span>
                <span class="text-slate-400">&bull;</span>
                <span>Periode: <strong>{{ $periodLabel }}</strong></span>
            </div>
            <div class="text-[11px] text-slate-600 dark:text-slate-400">
                Rentang Data: {{ $startDate->translatedFormat('d M Y') }} - {{ $endDate->translatedFormat('d M Y') }}
            </div>
        </div>

        <!-- 4 Executive KPI Cards (2x2 Grid on Mobile for Instant Owner Visibility) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
            
            <!-- Total Pendapatan Operasional (Omset) -->
            <div class="p-3.5 sm:p-5 bg-emerald-100 dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 rounded-2xl shadow-[3px_3px_0px_0px_#000] sm:shadow-[4px_4px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] print-card flex flex-col justify-between">
                <div>
                    <div class="text-[10px] sm:text-xs font-black uppercase text-slate-700 dark:text-emerald-400 mb-1 sm:mb-1.5 flex items-center justify-between">
                        <span class="truncate">Total Omset Usaha</span>
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-emerald-300 dark:bg-emerald-500/20 border border-black dark:border-emerald-500/50 flex items-center justify-center shrink-0">
                            <x-icon name="trending-up" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-950 dark:text-emerald-300" />
                        </div>
                    </div>
                    <div class="text-base sm:text-2xl font-black text-emerald-950 dark:text-white tracking-tight leading-tight">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-2 text-[10px] sm:text-[11px] font-bold text-emerald-800 dark:text-emerald-400 truncate">
                    <span class="hidden sm:inline">Cetak: Rp {{ number_format($orderRevenue, 0, ',', '.') }} &bull; POS: Rp {{ number_format($retailPosRevenue, 0, ',', '.') }}</span>
                    <span class="sm:hidden">Ctk: {{ number_format($orderRevenue / 1000, 0) }}k &bull; POS: {{ number_format($retailPosRevenue / 1000, 0) }}k</span>
                </div>
            </div>

            <!-- Total Beban Operasional -->
            <div class="p-3.5 sm:p-5 bg-rose-100 dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 rounded-2xl shadow-[3px_3px_0px_0px_#000] sm:shadow-[4px_4px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] print-card flex flex-col justify-between">
                <div>
                    <div class="text-[10px] sm:text-xs font-black uppercase text-slate-700 dark:text-rose-400 mb-1 sm:mb-1.5 flex items-center justify-between">
                        <span class="truncate">Total Beban Operasional</span>
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-rose-300 dark:bg-rose-500/20 border border-black dark:border-rose-500/50 flex items-center justify-center shrink-0">
                            <x-icon name="trending-down" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-rose-950 dark:text-rose-300" />
                        </div>
                    </div>
                    <div class="text-base sm:text-2xl font-black text-rose-950 dark:text-white tracking-tight leading-tight">
                        Rp {{ number_format($totalOperatingExpense, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-2 text-[10px] sm:text-[11px] font-bold text-rose-800 dark:text-rose-400 truncate">
                    Petty cash & belanja
                </div>
            </div>

            <!-- Estimasi Laba Bersih Operasional -->
            <div class="p-3.5 sm:p-5 {{ $netOperatingProfit >= 0 ? 'bg-cyan-100 dark:bg-[#0c1222]' : 'bg-rose-200 dark:bg-[#0c1222]' }} border-2 border-black dark:border-slate-700 rounded-2xl shadow-[3px_3px_0px_0px_#000] sm:shadow-[4px_4px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] print-card flex flex-col justify-between">
                <div>
                    <div class="text-[10px] sm:text-xs font-black uppercase text-slate-700 dark:text-sky-400 mb-1 sm:mb-1.5 flex items-center justify-between">
                        <span class="truncate">Laba Bersih Operasional</span>
                        <span class="px-1.5 py-0.5 rounded-full border border-black dark:border-slate-700 text-[9px] sm:text-[10px] font-black shrink-0 {{ $netOperatingProfit >= 0 ? 'bg-cyan-300 text-cyan-950 dark:bg-cyan-950/60 dark:text-cyan-300' : 'bg-rose-300 text-rose-950 dark:bg-rose-950/60 dark:text-rose-300' }}">
                            {{ round($profitMargin, 1) }}%
                        </span>
                    </div>
                    <div class="text-base sm:text-2xl font-black {{ $netOperatingProfit >= 0 ? 'text-cyan-950 dark:text-white' : 'text-rose-900 dark:text-rose-400' }} tracking-tight leading-tight">
                        {{ $netOperatingProfit >= 0 ? '+' : '' }} Rp {{ number_format($netOperatingProfit, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-2 text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-400 truncate">
                    Omset &minus; beban
                </div>
            </div>

            <!-- Piutang Pelanggan (Outstanding) -->
            <div class="p-3.5 sm:p-5 bg-amber-100 dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 rounded-2xl shadow-[3px_3px_0px_0px_#000] sm:shadow-[4px_4px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] print-card flex flex-col justify-between">
                <div>
                    <div class="text-[10px] sm:text-xs font-black uppercase text-slate-700 dark:text-amber-400 mb-1 sm:mb-1.5 flex items-center justify-between">
                        <span class="truncate">Piutang Tertahan</span>
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-amber-300 dark:bg-amber-500/20 border border-black dark:border-amber-500/50 flex items-center justify-center shrink-0">
                            <x-icon name="clock" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-950 dark:text-amber-300" />
                        </div>
                    </div>
                    <div class="text-base sm:text-2xl font-black text-amber-950 dark:text-white tracking-tight leading-tight">
                        Rp {{ number_format($totalReceivables, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-2 text-[10px] sm:text-[11px] font-bold text-amber-800 dark:text-slate-400 truncate">
                    {{ $unpaidOrdersCount }} pesanan aktif
                </div>
            </div>

        </div>

    <!-- 2 Column Financial Breakdown: P&L vs Payment Split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Laporan Laba Rugi Detail (7 cols) -->
        <div class="lg:col-span-7 neo-box p-4 sm:p-6 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-5 print-card">
            
            <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 bg-amber-300 flex items-center justify-center font-black text-slate-900">
                        <x-icon name="file-text" class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900 dark:text-white">Rincian Laba & Rugi (P&L)</h2>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Kompilasi penerimaan dan pengeluaran operasional</p>
                    </div>
                </div>
                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-lg border border-slate-800 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-200">
                    Akuntansi Kas
                </span>
            </div>

            <!-- Revenue Section -->
            <div class="space-y-2 text-xs">
                <div class="font-black text-emerald-800 dark:text-emerald-400 uppercase text-[11px] tracking-wider flex items-center justify-between">
                    <span>1. PENDAPATAN OPERASIONAL</span>
                    <span>NOMINAL</span>
                </div>

                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 space-y-2">
                    <div class="flex justify-between items-center text-slate-700 dark:text-slate-300">
                        <span class="font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Omset Order Desain & Cetak (DP & Pelunasan)
                        </span>
                        <span class="font-black text-slate-900 dark:text-white">Rp {{ number_format($orderRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-700 dark:text-slate-300">
                        <span class="font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            Omset Kasir Ritel POS (ATK, Fotokopi, Merchandise)
                        </span>
                        <span class="font-black text-slate-900 dark:text-white">Rp {{ number_format($retailPosRevenue, 0, ',', '.') }}</span>
                    </div>
                    @if($otherIncome > 0)
                    <div class="flex justify-between items-center text-slate-500 dark:text-slate-400 text-[11px]">
                        <span>Pemasukan Lainnya / Modal Kas Awal</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Rp {{ number_format($otherIncome, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="flex justify-between items-center px-3 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700 font-black text-emerald-950 dark:text-emerald-300">
                    <span>TOTAL OMSET / PENDAPATAN</span>
                    <span class="text-sm">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Expense Section -->
            <div class="space-y-2 text-xs pt-2">
                <div class="font-black text-rose-800 dark:text-rose-400 uppercase text-[11px] tracking-wider flex items-center justify-between">
                    <span>2. BEBAN & PENGELUARAN OPERASIONAL</span>
                    <span>NOMINAL</span>
                </div>

                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 space-y-2">
                    <div class="flex justify-between items-center text-slate-700 dark:text-slate-300">
                        <span class="font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Biaya Operasional Toko (Petty Cash, Listrik, Bahan)
                        </span>
                        <span class="font-black text-rose-700 dark:text-rose-400">Rp {{ number_format($operationalExpense, 0, ',', '.') }}</span>
                    </div>
                    @if($refundExpense > 0)
                    <div class="flex justify-between items-center text-slate-700 dark:text-slate-300">
                        <span class="font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Pengembalian Dana Pelanggan (Refund DP Order)
                        </span>
                        <span class="font-black text-rose-700 dark:text-rose-400">Rp {{ number_format($refundExpense, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($otherExpense > 0)
                    <div class="flex justify-between items-center text-slate-500 dark:text-slate-400 text-[11px]">
                        <span>Kas Keluar Lainnya</span>
                        <span class="font-semibold text-rose-700 dark:text-rose-400">Rp {{ number_format($otherExpense, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="flex justify-between items-center px-3 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700 font-black text-rose-950 dark:text-rose-300">
                    <span>TOTAL BEBAN OPERASIONAL</span>
                    <span class="text-sm">- Rp {{ number_format($totalOperatingExpense, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Net Calculation Summary Box -->
            <div class="pt-2 border-t-2 border-slate-900 dark:border-slate-800">
                <div class="p-4 rounded-xl border-2 border-black dark:border-slate-700 {{ $netOperatingProfit >= 0 ? 'bg-cyan-100 dark:bg-cyan-950/40' : 'bg-rose-100 dark:bg-rose-950/40' }} shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] flex justify-between items-center">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider block text-slate-700 dark:text-slate-300">HASIL OPERASIONAL BERSIH</span>
                        <span class="text-sm font-black text-slate-900 dark:text-white">
                            {{ $netOperatingProfit >= 0 ? 'SURPLUS / LABA BERSIH' : 'DEFISIT / RUGI OPERASIONAL' }}
                        </span>
                    </div>
                    <div class="text-xl font-black {{ $netOperatingProfit >= 0 ? 'text-emerald-950 dark:text-emerald-400' : 'text-rose-950 dark:text-rose-400' }}">
                        {{ $netOperatingProfit >= 0 ? '+' : '' }} Rp {{ number_format($netOperatingProfit, 0, ',', '.') }}
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Payment Method Breakdown (5 cols) -->
        <div class="lg:col-span-5">
            
            <!-- Saluran Pembayaran Card -->
            <div class="neo-box p-4 sm:p-6 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4 print-card">
                <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 bg-sky-300 flex items-center justify-center font-black text-slate-900">
                            <x-icon name="credit-card" class="w-4 h-4" />
                        </div>
                        <div>
                            <h2 class="text-base font-black text-slate-900 dark:text-white">Saluran Pembayaran</h2>
                            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Pemisahan fisik laci vs uang bank digital</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    @foreach($paymentSplit as $key => $split)
                    <div class="p-3 rounded-xl border-[1.5px] border-slate-800 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 space-y-2">
                        <div class="flex justify-between items-center font-bold">
                            <span class="text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                @if($key === 'cash')
                                    <x-icon name="coins" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                @elseif($key === 'qris')
                                    <x-icon name="qr-code" class="w-4 h-4 text-cyan-600 dark:text-cyan-400" />
                                @else
                                    <x-icon name="credit-card" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                @endif
                                <span>{{ $split['label'] }}</span>
                            </span>
                            <span class="font-black text-slate-950 dark:text-white">
                                Rp {{ number_format($split['amount'], 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Progress Bar Percentage -->
                        <div class="w-full bg-slate-200 dark:bg-slate-800 h-2 rounded-full overflow-hidden border border-slate-400 dark:border-slate-700">
                            <div class="h-full {{ $key === 'cash' ? 'bg-amber-400' : ($key === 'qris' ? 'bg-cyan-500' : 'bg-purple-500') }}" 
                                 style="width: {{ $split['percentage'] }}%"></div>
                        </div>

                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-bold flex justify-between">
                            <span>Porsi terhadap total kas masuk</span>
                            <span>{{ $split['percentage'] }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-300 dark:border-amber-700 text-[11px] font-semibold text-amber-900 dark:text-amber-300 leading-relaxed">
                    💡 <strong>Tips Audit:</strong> Pastikan nominal kas <em>Tunai</em> cocok dengan uang di laci fisik kasir cabang sebelum tutup buku.
                </div>
            </div>

        </div>

    </div>

    <!-- Multi-Branch Comparison Table (Shown if consolidating all branches / Owner) -->
    @if(!$branchId && count($branchComparison) > 0)
    <div class="neo-box p-4 sm:p-6 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4 print-card">
        <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 bg-purple-300 flex items-center justify-center font-black text-slate-900">
                    <x-icon name="building-2" class="w-4 h-4" />
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Performa Finansial Antar Cabang</h2>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Perbandingan omset, efisiensi beban, laba, dan kas laci</p>
                </div>
            </div>
            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-lg border border-slate-800 dark:border-slate-700 bg-purple-100 dark:bg-purple-950/50 text-purple-900 dark:text-purple-300">
                Multi-Branch Leaderboard
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-900 border-b-2 border-slate-900 dark:border-slate-800 font-black text-slate-800 dark:text-slate-200 uppercase text-[11px]">
                        <th class="py-3 px-4">Cabang</th>
                        <th class="py-3 px-4 text-right">Kas Masuk (Omset)</th>
                        <th class="py-3 px-4 text-right">Beban Operasional</th>
                        <th class="py-3 px-4 text-right">Laba Bersih Cabang</th>
                        <th class="py-3 px-4 text-right">Saldo Kas Laci</th>
                        <th class="py-3 px-4 text-right">Piutang Aktif</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @foreach($branchComparison as $comp)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="py-3.5 px-4">
                            <div class="font-black text-slate-900 dark:text-white text-sm flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-800 border border-slate-400 dark:border-slate-700 font-mono text-[10px] text-slate-900 dark:text-slate-200">
                                    {{ $comp['branch']->code }}
                                </span>
                                <span>{{ $comp['branch']->name }}</span>
                            </div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $comp['branch']->address }}</div>
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-emerald-800 dark:text-emerald-400">
                            Rp {{ number_format($comp['revenue'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-rose-800 dark:text-rose-400">
                            Rp {{ number_format($comp['expense'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-black {{ $comp['profit'] >= 0 ? 'text-cyan-900 dark:text-cyan-400' : 'text-rose-900 dark:text-rose-400' }}">
                            {{ $comp['profit'] >= 0 ? '+' : '' }} Rp {{ number_format($comp['profit'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-900 dark:text-white">
                            Rp {{ number_format($comp['cash_balance'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-amber-900 dark:text-amber-400">
                            Rp {{ number_format($comp['receivables'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Top Outstanding Receivables (Daftar Piutang yang Perlu Ditagih) -->
    <div class="neo-box p-4 sm:p-6 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4 print-card">
        <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 bg-amber-300 flex items-center justify-center font-black text-slate-900">
                    <x-icon name="alert-circle" class="w-4 h-4" />
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Tagihan & Piutang Pelanggan Belum Lunas</h2>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Daftar order aktif yang masih memiliki sisa pembayaran</p>
                </div>
            </div>
            <span class="text-[11px] font-black px-2.5 py-0.5 rounded-full border border-slate-900 dark:border-slate-700 bg-amber-100 dark:bg-amber-950/50 text-amber-950 dark:text-amber-300">
                Total Piutang: Rp {{ number_format($totalReceivables, 0, ',', '.') }}
            </span>
        </div>

        @if($topUnpaidOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-900 border-b-2 border-slate-900 dark:border-slate-800 font-black text-slate-800 dark:text-slate-200 uppercase text-[11px]">
                        <th class="py-3 px-4">No. Order & Customer</th>
                        <th class="py-3 px-4">Cabang</th>
                        <th class="py-3 px-4">Pesanan</th>
                        <th class="py-3 px-4 text-right">Total Biaya</th>
                        <th class="py-3 px-4 text-right">Telah Dibayar</th>
                        <th class="py-3 px-4 text-right">Sisa Tagihan</th>
                        <th class="no-print py-3 px-4 text-center">Aksi / Tagih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @foreach($topUnpaidOrders as $unpaid)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="py-3 px-4">
                            <div class="font-black text-slate-900 dark:text-white">{{ $unpaid->customer_name }}</div>
                            <div class="text-[10px] font-mono text-slate-500 dark:text-slate-400">{{ $unpaid->order_number }}</div>
                            @if($unpaid->customer_phone)
                            <div class="text-[10px] text-slate-600 dark:text-slate-400 font-semibold">{{ $unpaid->customer_phone }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-[10px] font-bold text-slate-900 dark:text-slate-200">
                                {{ $unpaid->branch->code }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 dark:text-white block truncate max-w-[200px]">
                                {{ $unpaid->designJob ? $unpaid->designJob->title : 'Pesanan Ritel POS' }}
                            </span>
                            <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                Status: {{ $unpaid->status_label }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-slate-800 dark:text-white">
                            Rp {{ number_format($unpaid->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-700 dark:text-emerald-400">
                            Rp {{ number_format($unpaid->paid_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-right font-black text-rose-700 dark:text-rose-400">
                            Rp {{ number_format($unpaid->remaining_amount, 0, ',', '.') }}
                        </td>
                        <td class="no-print py-3 px-4 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('orders.show', $unpaid->id) }}" 
                                   title="Lihat Detail Order"
                                   aria-label="Detail Order"
                                   class="w-7 h-7 rounded-lg border border-slate-800 dark:border-slate-700 bg-white hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-900 dark:text-white shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] flex items-center justify-center transition-all cursor-pointer">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('orders.invoice', $unpaid->id) }}" 
                                   target="_blank"
                                   title="Cetak Invoice Formal A4"
                                   aria-label="Cetak Invoice"
                                   class="w-7 h-7 rounded-lg border border-slate-800 dark:border-slate-700 bg-amber-300 hover:bg-amber-400 text-slate-950 dark:text-slate-950 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] flex items-center justify-center transition-all cursor-pointer">
                                    <x-icon name="file-text" class="w-3.5 h-3.5 text-slate-950" />
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-8 text-center text-slate-400 dark:text-slate-500 font-semibold text-xs">
            🎉 Tidak ada tagihan atau piutang yang tertahan untuk kriteria saat ini. Semua pesanan telah lunas!
        </div>
        @endif
    </div>

    </div> <!-- Close filter-results-container -->

    <!-- Print-Only Signature Section -->
    <div class="print-header mt-12 pt-6 border-t-2 border-slate-900">
        <div class="grid grid-cols-2 gap-12 text-xs text-center">
            <div class="space-y-16">
                <span class="font-bold text-slate-600 block">Dibuat & Diverifikasi Oleh:</span>
                <div class="border-t border-slate-400 w-48 mx-auto pt-1 font-black text-slate-900">
                    {{ Auth::user()->name }}
                    <span class="block text-[10px] text-slate-500 font-normal">{{ Auth::user()->role_label }}</span>
                </div>
            </div>
            <div class="space-y-16">
                <span class="font-bold text-slate-600 block">Disetujui Oleh:</span>
                <div class="border-t border-slate-400 w-48 mx-auto pt-1 font-black text-slate-900">
                    Manajemen / Owner
                    <span class="block text-[10px] text-slate-500 font-normal">Gemini Art Multi-Cabang</span>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function toggleCustomDates(value) {
        const container = document.getElementById('custom-date-container');
        if (!container) return;
        if (value === 'custom') {
            container.classList.remove('hidden');
            container.classList.add('flex');
            if (window.refreshNeoDatePicker) {
                window.refreshNeoDatePicker();
            }
        } else {
            container.classList.add('hidden');
            container.classList.remove('flex');
            const fromInput = container.querySelector('input[name="date_from"]');
            const toInput = container.querySelector('input[name="date_to"]');
            if (fromInput) fromInput.value = '';
            if (toInput) toInput.value = '';
        }
    }
</script>
@endsection

