@extends('layouts.app')

@section('title', 'Dashboard Kasir - ' . ($branch->name ?? 'Cabang'))

@section('content')
<div class="space-y-8">

    <!-- Top Branch Status Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-emerald-200 border-2 border-black rounded-2xl shadow-[4px_4px_0px_0px_#000]">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                <span class="text-xs font-black uppercase tracking-wider text-emerald-900">
                    Front Office & Kasir {{ $branch->code ?? '' }}
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-black">{{ $branch->name ?? 'Cabang' }}</h1>
            <!-- <p class="text-xs md:text-sm font-semibold text-slate-700 mt-1">
                Pencatatan order desain kustom, transaksi kasir ritel cepat (fotokopi/ATK), dan serah terima pesanan.
            </p> -->
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('orders.create-design') }}" class="neo-btn bg-sky-400 hover:bg-sky-300 text-black text-xs py-2.5 px-4 flex items-center gap-2">
                <x-icon name="plus" class="w-4 h-4" stroke-width="2.5" />
                <span>Order Desain Baru</span>
            </a>
            <a href="{{ route('pos.index') }}" class="neo-btn bg-amber-300 hover:bg-amber-400 text-black text-xs py-2.5 px-4 flex items-center gap-2">
                <x-icon name="shopping-cart" class="w-4 h-4" />
                <span>Kasir Ritel POS (FC/ATK)</span>
            </a>
        </div>
    </div>

    <!-- 4 Key Cashier Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Saldo Kas Fisik Laci Cabang -->
        <div class="p-5 bg-amber-100 border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000]">
            <div class="text-xs font-black uppercase text-slate-700 mb-1 flex items-center justify-between">
                <span>Saldo Kas Laci Cabang</span>
                <x-icon name="wallet" class="w-4 h-4 text-slate-900" />
            </div>
            <div class="text-2xl font-black text-black tracking-tight">
                Rp {{ number_format($cashBalance, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-[11px] font-bold text-slate-700">
                Fisik kas laci saat ini
            </div>
        </div>

        <!-- Omset Masuk Hari Ini -->
        <div class="p-5 bg-white border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000]">
            <div class="text-xs font-black uppercase text-slate-500 mb-1 flex items-center justify-between">
                <span>Omset Masuk Hari Ini</span>
                <x-icon name="trending-up" class="w-4 h-4 text-emerald-600" />
            </div>
            <div class="text-2xl font-black text-emerald-700 tracking-tight">
                Rp {{ number_format($todayTurnover, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-[11px] font-bold text-slate-500">
                Total pembayaran hari ini
            </div>
        </div>

        <!-- Order Siap Diambil / Pelunasan -->
        <div class="p-5 bg-emerald-100 border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000]">
            <div class="text-xs font-black uppercase text-slate-700 mb-1 flex items-center justify-between">
                <span>Siap Diambil / Pelunasan</span>
                <x-icon name="check-circle-2" class="w-4 h-4 text-emerald-800" />
            </div>
            <div class="text-2xl font-black text-black tracking-tight">
                {{ $readyOrders->count() }} Pesanan
            </div>
            <div class="mt-2 text-[11px] font-bold text-emerald-800">
                File cetak selesai pengerjaan
            </div>
        </div>

        <!-- Antrean Desain Sedang Dikerjakan -->
        <div class="p-5 bg-sky-100 border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000]">
            <div class="text-xs font-black uppercase text-slate-700 mb-1 flex items-center justify-between">
                <span>Antrean Desain Cabang</span>
                <x-icon name="clock" class="w-4 h-4 text-blue-800" />
            </div>
            <div class="text-2xl font-black text-black tracking-tight">
                {{ $inProgressOrders->count() }} Tiket
            </div>
            <div class="mt-2 text-[11px] font-bold text-blue-800">
                Di workdesk desainer
            </div>
        </div>

    </div>

    <!-- Urgent Section: Pesanan Siap Cetak & Menunggu Pelunasan (SOP 4) -->
    <div class="neo-box p-6 bg-white">
        <div class="flex items-center justify-between pb-4 border-b-2 border-black mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-emerald-400 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                    <x-icon name="check-check" class="w-4 h-4" stroke-width="2.5" />
                </div>
                <h2 class="font-black text-base text-black">Antrean Siap Serah Terima & Pelunasan Pelanggan</h2>
            </div>
            <span class="text-xs font-bold text-slate-500">
                SOP Tahap 4: Customer ambil pesanan & kasir catat pelunasan
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($readyOrders as $ord)
            <div class="p-4 bg-emerald-50 border-2 border-black rounded-xl shadow-[3px_3px_0px_0px_#000] flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="font-mono text-xs font-bold bg-white border border-black px-1.5 py-0.5 rounded">
                            {{ $ord->order_number }}
                        </span>
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full border border-black {{ $ord->payment_status_badge_class }}">
                            {{ $ord->payment_status_label }}
                        </span>
                    </div>

                    <h3 class="font-black text-base text-black mb-1">
                        {{ $ord->customer_name }}
                    </h3>
                    <div class="text-xs text-slate-600 font-semibold mb-2">
                        {{ $ord->designJob->title ?? 'Order Cetak' }}
                    </div>

                    <div class="p-2.5 bg-white border-2 border-black rounded-lg mb-3 space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Biaya:</span>
                            <span class="font-bold">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sudah Masuk (DP):</span>
                            <span class="font-bold text-emerald-700">Rp {{ number_format($ord->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-1">
                            <span class="font-bold text-rose-700">Sisa Tagihan:</span>
                            <span class="font-black text-rose-700">Rp {{ number_format($ord->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-black/20">
                    <a href="{{ route('orders.show', $ord->id) }}" class="flex-1 neo-btn bg-white hover:bg-slate-100 text-xs py-2 text-center">
                        Detail Order
                    </a>
                    @if($ord->remaining_amount > 0)
                    <a href="{{ route('orders.show', $ord->id) }}#settle" class="flex-1 neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 text-center font-black">
                        Pelunasan →
                    </a>
                    @else
                    <form method="POST" action="{{ route('orders.status', $ord->id) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="work_status" value="completed">
                        <button type="submit" class="w-full neo-btn bg-slate-200 hover:bg-slate-300 text-xs py-2 cursor-pointer">
                            Serah Terima
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full p-8 text-center border-2 border-dashed border-slate-300 rounded-xl text-xs font-semibold text-slate-400">
                Tidak ada pesanan siap cetak yang menunggu pelunasan saat ini.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Split: In-progress Design Orders & Today's Cash Movement Log -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Orders Sedang Dikerjakan Desainer (6 cols) -->
        <div class="lg:col-span-6 neo-box p-6 bg-white">
            <div class="flex items-center justify-between pb-4 border-b-2 border-black mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-blue-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                        <x-icon name="layers" class="w-4 h-4" />
                    </div>
                    <h2 class="font-black text-base text-black">Antrean Pengerjaan Desain Cabang</h2>
                </div>
                <a href="{{ route('workdesk.index') }}" class="text-xs font-extrabold text-blue-600 hover:underline">
                    Buka Workdesk →
                </a>
            </div>

            <div class="space-y-3">
                @forelse($inProgressOrders as $ord)
                <div class="p-3 bg-slate-50 border-2 border-black rounded-xl flex items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold">{{ $ord->order_number }}</span>
                            <span class="text-[10px] font-black uppercase px-2 py-0.2 rounded-full border border-black {{ $ord->status_color_class }}">
                                {{ $ord->status_label }}
                            </span>
                        </div>
                        <div class="font-extrabold text-xs text-black mt-1">
                            {{ $ord->customer_name }} - {{ $ord->designJob->title ?? 'Desain Kustom' }}
                        </div>
                        <div class="text-[11px] text-slate-500 font-medium">
                            Deadline: {{ $ord->designJob && $ord->designJob->deadline_at ? $ord->designJob->deadline_at->format('d M H:i') : '-' }}
                        </div>
                    </div>
                    <a href="{{ route('orders.show', $ord->id) }}" class="neo-btn bg-white hover:bg-slate-100 text-xs py-1.5 px-3 whitespace-nowrap">
                        Lihat
                    </a>
                </div>
                @empty
                <div class="p-6 text-center text-xs font-semibold text-slate-400">
                    Tidak ada antrean pengerjaan desain saat ini.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Today's Cash Movement Log (6 cols) -->
        <div class="lg:col-span-6 neo-box p-6 bg-white">
            <div class="flex items-center justify-between pb-4 border-b-2 border-black mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-purple-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                        <x-icon name="wallet" class="w-4 h-4" />
                    </div>
                    <h2 class="font-black text-base text-black">Mutasi Kas Cabang Hari Ini</h2>
                </div>
                <a href="{{ route('cash.index') }}" class="text-xs font-extrabold text-blue-600 hover:underline">
                    Buku Kas Lengkap →
                </a>
            </div>

            <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
                @forelse($todayMovements as $mv)
                <div class="p-2.5 bg-slate-50 border-2 border-black rounded-xl flex items-center justify-between text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase px-1.5 py-0.2 rounded border border-black {{ $mv->category_badge_class }}">
                                {{ $mv->category_label }}
                            </span>
                            <span class="text-[10px] font-bold uppercase text-slate-500">
                                via {{ $mv->payment_method }}
                            </span>
                        </div>
                        <div class="text-xs font-semibold text-slate-800 mt-1">
                            {{ $mv->notes ?? '-' }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-black text-sm {{ $mv->type === 'in' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $mv->type === 'in' ? '+' : '-' }} Rp {{ number_format($mv->amount, 0, ',', '.') }}
                        </div>
                        <div class="text-[10px] text-slate-400 font-medium">
                            {{ $mv->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-xs font-semibold text-slate-400">
                    Belum ada transaksi kas cabang hari ini.
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
