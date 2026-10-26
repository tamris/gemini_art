@extends('layouts.app')

@section('title', 'Detail Order ' . $order->order_number)

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumbs & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}" class="neo-btn bg-white hover:bg-slate-100 text-xs py-1.5 px-3 flex items-center gap-1.5">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" stroke-width="2.5" />
                <span>Kembali</span>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xl font-black text-black">{{ $order->order_number }}</span>
                    <span class="px-2 py-0.5 rounded border border-black bg-slate-200 text-xs font-bold">
                        {{ $order->branch->code }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full border border-black text-xs font-black {{ $order->status_color_class }}">
                        {{ $order->status_label }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full border border-black text-xs font-black {{ $order->payment_status_badge_class }}">
                        {{ $order->payment_status_label }}
                    </span>
                </div>
                <div class="text-xs text-slate-500 font-semibold mt-0.5">
                    Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} oleh {{ $order->creator->name }}
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-4 text-black flex items-center gap-2 font-black">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Cetak Struk / SPK</span>
            </a>
        </div>
    </div>

    <!-- Main Grid: 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Details & Assets (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Customer & Job Details Card -->
            <div class="neo-box p-6 bg-white space-y-4">
                <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                    <h2 class="font-black text-base text-black flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-sky-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                            <x-icon name="file-text" class="w-4 h-4" />
                        </div>
                        <span>Rincian Pesanan</span>
                    </h2>
                    <span class="text-xs font-bold uppercase px-2 py-0.5 rounded border border-black bg-slate-100">
                        {{ $order->order_type === 'design_printing' ? 'Desain Kustom & Cetak' : 'Kasir POS Ritel' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-slate-50 border-2 border-black rounded-xl">
                        <span class="text-[10px] font-black uppercase text-slate-500 block mb-0.5">Nama Customer</span>
                        <div class="font-black text-sm text-black">{{ $order->customer_name }}</div>
                        @if($order->customer_phone)
                            <div class="font-bold text-slate-600 mt-1">WA: {{ $order->customer_phone }}</div>
                        @endif
                    </div>

                    <div class="p-3 bg-slate-50 border-2 border-black rounded-xl">
                        <span class="text-[10px] font-black uppercase text-slate-500 block mb-0.5">Cabang & Kasir</span>
                        <div class="font-black text-sm text-black">{{ $order->branch->name }}</div>
                        <div class="font-bold text-slate-600 mt-1">Kasir: {{ $order->creator->name }}</div>
                    </div>
                </div>

                <!-- If Design Order: Show Design Job Specs -->
                @if($order->order_type === 'design_printing' && $order->designJob)
                <div class="pt-3 border-t-2 border-black space-y-3">
                    <h3 class="font-black text-sm text-black">Spesifikasi Pengerjaan Desain:</h3>
                    
                    <div class="p-4 bg-amber-50 border-2 border-black rounded-xl space-y-2.5">
                        <div class="font-black text-base text-black">
                            {{ $order->designJob->title }}
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] font-black uppercase text-slate-500 block">Dimensi</span>
                                <span class="font-extrabold text-black">{{ $order->designJob->dimensions ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase text-slate-500 block">Bahan</span>
                                <span class="font-extrabold text-black">{{ $order->designJob->material_notes ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase text-slate-500 block">Deadline</span>
                                <span class="font-extrabold {{ $order->designJob->deadline_at && $order->designJob->deadline_at->isPast() ? 'text-rose-700' : 'text-black' }}">
                                    {{ $order->designJob->deadline_at ? $order->designJob->deadline_at->format('d M Y H:i') : '-' }}
                                </span>
                            </div>
                        </div>

                        @if($order->designJob->brief_notes)
                        <div class="pt-2 border-t border-black/20 text-xs">
                            <span class="text-[10px] font-black uppercase text-slate-500 block mb-1">Catatan Brief Desain:</span>
                            <div class="p-2.5 bg-white border border-black rounded-lg text-slate-800 font-medium whitespace-pre-line">
                                {{ $order->designJob->brief_notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- If Retail POS: Show Order Items -->
                @if($order->order_type === 'retail_pos' && $order->items->count())
                <div class="pt-3 border-t-2 border-black">
                    <h3 class="font-black text-sm text-black mb-2">Item Belanja POS:</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-100 border-b-2 border-black text-black font-black uppercase">
                                    <th class="py-2 px-3 text-left">Item Layanan / Barang</th>
                                    <th class="py-2 px-3 text-center">Qty</th>
                                    <th class="py-2 px-3 text-right">Harga Satuan</th>
                                    <th class="py-2 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/20">
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="py-2 px-3 font-bold">{{ $item->item_name }}</td>
                                    <td class="py-2 px-3 text-center font-bold">{{ $item->qty }}</td>
                                    <td class="py-2 px-3 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-black">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>

            <!-- Design Assets Section (SOP 3 & Cloud Storage Sync) -->
            @if($order->order_type === 'design_printing' && $order->designJob)
            <div class="neo-box p-6 bg-white space-y-4">
                <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                    <div>
                        <h2 class="font-black text-base text-black flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-emerald-400 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                            </div>
                            <span>File Arsip Desain & Cetak</span>
                        </h2>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Struktur path: <code>designs/{{ $order->branch->code }}/{{ date('Y-m') }}/{{ $order->order_number }}/...</code>
                        </p>
                    </div>

                    <button type="button" 
                            onclick="document.getElementById('upload-modal').showModal()"
                            class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-1.5 px-3 font-bold flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Unggah File Baru</span>
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse($order->designJob->assets as $asset)
                    <div class="p-3.5 bg-slate-50 border-2 border-black rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-[2px_2px_0px_0px_#000]">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg border-2 border-black bg-white flex items-center justify-center text-black shrink-0 shadow-[1px_1px_0px_0px_#000]">
                                @if($asset->file_type === 'final_print')
                                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                @elseif($asset->file_type === 'preview_proof')
                                    <svg class="w-5 h-5 text-sky-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black uppercase px-1.5 py-0.2 rounded border border-black {{ $asset->file_type === 'final_print' ? 'bg-emerald-300' : ($asset->file_type === 'preview_proof' ? 'bg-sky-200' : 'bg-amber-200') }}">
                                        {{ $asset->file_type_badge }}
                                    </span>
                                    <span class="text-[11px] font-bold text-slate-500 font-mono">{{ $asset->formatted_size }}</span>
                                </div>
                                <div class="font-black text-xs text-black truncate mt-0.5">{{ $asset->file_name }}</div>
                                <div class="text-[10px] text-slate-500 font-medium">
                                    Diunggah {{ $asset->created_at->format('d M H:i') }} oleh {{ $asset->uploader->name ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('archive.download', $asset->id) }}" class="neo-btn bg-white hover:bg-slate-100 text-xs py-1.5 px-3 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>Download</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center border-2 border-dashed border-slate-300 rounded-xl text-xs font-semibold text-slate-400">
                        Belum ada file desain atau cetak yang diunggah untuk tiket ini.
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Cash Movements Timeline For This Order -->
            <div class="neo-box p-6 bg-white space-y-3">
                <h2 class="font-black text-base text-black pb-2 border-b-2 border-black flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-purple-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span>Histori Mutasi Kas Terkait Order Ini</span>
                </h2>

                <div class="space-y-2">
                    @forelse($order->cashMovements as $mv)
                    <div class="p-3 bg-slate-50 border-2 border-black rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-black uppercase text-[10px] px-1.5 py-0.2 rounded border border-black {{ $mv->category_badge_class }}">
                                    {{ $mv->category_label }}
                                </span>
                                <span class="text-[10px] font-bold uppercase text-slate-500">
                                    via {{ $mv->payment_method }}
                                </span>
                            </div>
                            <div class="font-semibold text-slate-700 mt-1">{{ $mv->notes ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400 font-medium">
                                Kasir: {{ $mv->user->name }} &bull; {{ $mv->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-black text-emerald-700">
                                + Rp {{ number_format($mv->amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-slate-400 font-semibold">
                        Belum ada mutasi kas tercatat untuk order ini.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Status Controls & Settlement (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Financial Card & Settlement Form (SOP 4) -->
            <div id="settle" class="neo-box p-6 bg-white space-y-4">
                <h2 class="font-black text-base text-black pb-2 border-b-2 border-black">
                    Status Pembayaran
                </h2>

                <div class="p-4 bg-slate-50 border-2 border-black rounded-xl space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Total Tagihan:</span>
                        <span class="font-extrabold text-black">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Sudah Dibayar (DP):</span>
                        <span class="font-extrabold text-emerald-700">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-t border-black/20 pt-2 text-sm">
                        <span class="font-black text-rose-700">Sisa Tagihan:</span>
                        <span class="font-black text-rose-700">Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($order->remaining_amount > 0)
                <!-- Pelunasan Form (SOP 4: Customer Ambil Pesanan & Kasir Catat Pelunasan) -->
                <div class="p-4 bg-emerald-50 border-2 border-black rounded-xl space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <span class="text-xs font-black uppercase tracking-wider text-emerald-900">
                            SOP 4: Catat Pelunasan Sisa
                        </span>
                    </div>
                    <p class="text-[11px] font-semibold text-slate-600">
                        Customer mengambil pesanan. Masukkan metode pembayaran pelunasan untuk menandai order selesai & mencatat kas masuk.
                    </p>

                    <form method="POST" action="{{ route('orders.settle', $order->id) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-black uppercase text-black mb-1">Metode Pelunasan</label>
                            <select name="payment_method" required class="neo-input text-xs font-bold py-1.5">
                                <option value="cash">Tunai (Cash Laci)</option>
                                <option value="qris">QRIS Statis/Dinamis</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black uppercase text-black mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="notes" placeholder="Misal: Pelunasan saat ambil barang" class="neo-input text-xs py-1.5">
                        </div>
                        <button type="submit" class="w-full neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2.5 font-black text-black flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Terima Pelunasan Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
                        </button>
                    </form>
                </div>
                @else
                <div class="p-3 bg-emerald-100 border-2 border-black rounded-xl text-center text-xs font-black text-emerald-900 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Tagihan Telah Lunas Penuh</span>
                </div>
                @endif
            </div>

            <!-- Work Status Control Card -->
            <div class="neo-box p-6 bg-white space-y-3">
                <h2 class="font-black text-base text-black pb-2 border-b-2 border-black">
                    Kelola Status Pekerjaan
                </h2>

                <form method="POST" action="{{ route('orders.status', $order->id) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-black uppercase text-black mb-1">Ubah Status Tiket</label>
                        <select name="work_status" class="neo-input text-xs font-bold">
                            <option value="draft" {{ $order->work_status === 'draft' ? 'selected' : '' }}>Draft / Antrean</option>
                            <option value="in_progress" {{ $order->work_status === 'in_progress' ? 'selected' : '' }}>Proses Pengerjaan</option>
                            <option value="revision" {{ $order->work_status === 'revision' ? 'selected' : '' }}>Revisi</option>
                            <option value="ready_to_print" {{ $order->work_status === 'ready_to_print' ? 'selected' : '' }}>Siap Cetak</option>
                            <option value="completed" {{ $order->work_status === 'completed' ? 'selected' : '' }}>Selesai / Diambil Customer</option>
                            <option value="canceled" {{ $order->work_status === 'canceled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 font-black text-black cursor-pointer">
                        Perbarui Status Tiket
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>

<!-- Upload Asset Modal for this order -->
@if($order->designJob)
<dialog id="upload-modal" closedby="any" class="p-0 bg-transparent max-w-lg w-full m-auto">
    <div class="neo-box p-6 bg-white max-w-md w-full mx-auto">
        <div class="flex items-center justify-between pb-3 border-b-2 border-black mb-4">
            <h3 class="font-black text-base text-black">Unggah File Tiket {{ $order->order_number }}</h3>
            <form method="dialog">
                <button class="w-8 h-8 rounded-lg border-2 border-black bg-rose-200 hover:bg-rose-300 font-black text-base flex items-center justify-center cursor-pointer">
                    ✕
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('workdesk.upload', $order->designJob->id) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Tipe File</label>
                <select name="file_type" required class="neo-input text-xs font-bold">
                    <option value="final_print" selected>File Cetak Siap Produksi (Status → Siap Cetak)</option>
                    <option value="preview_proof">Preview Proofing ke Pelanggan</option>
                    <option value="brief_ref">Referensi Brief / Lampiran</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Pilih File</label>
                <input type="file" name="file" required class="neo-input text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded file:border file:border-black file:bg-amber-300 file:text-xs file:font-black">
            </div>

            <div class="pt-3 border-t-2 border-black flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('upload-modal').close()" class="neo-btn bg-slate-200 text-xs py-2 px-4 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-4 font-black flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Unggah & Simpan</span>
                </button>
            </div>
        </form>
    </div>
</dialog>
@endif
@endsection
