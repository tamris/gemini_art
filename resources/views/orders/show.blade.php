@extends('layouts.app')

@section('title', 'Detail Order ' . $order->order_number)

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumbs & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-[1.5px] border-slate-800 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-xl font-black text-slate-900 dark:text-white">{{ $order->order_number }}</span>
                <span class="px-2 py-0.5 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 bg-slate-200 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-bold shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                    {{ $order->branch->code }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full border-[1.5px] border-slate-800 text-xs font-black shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] {{ $order->status_color_class }}">
                    {{ $order->status_label }}
                </span>
                <span class="px-2 py-0.5 rounded-full border-[1.5px] border-slate-800 text-xs font-black shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] {{ $order->payment_status_badge_class }}">
                    {{ $order->payment_status_label }}
                </span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">
                Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} oleh {{ $order->creator->name }}
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="neo-btn bg-amber-300 hover:bg-amber-400 dark:text-slate-950 text-xs py-2 px-3.5 text-slate-900 flex items-center gap-1.5 font-black shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Cetak Struk</span>
            </a>
            <a href="{{ route('orders.invoice', $order->id) }}" target="_blank" class="neo-btn bg-emerald-400 hover:bg-emerald-300 dark:text-slate-950 text-xs py-2 px-3.5 text-slate-900 flex items-center gap-1.5 font-black shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]">
                <x-icon name="file-text" class="w-4 h-4" />
                <span>Cetak Invoice A4</span>
            </a>
        </div>
    </div>

    <!-- Main Grid: 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Details & Assets (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Customer & Job Details Card -->
            <div class="neo-box p-6 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b-[1.5px] border-slate-800 dark:border-slate-800">
                    <h2 class="font-black text-base text-slate-900 dark:text-white flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-sky-200 border-[1.5px] border-slate-800 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                            <x-icon name="file-text" class="w-4 h-4" />
                        </div>
                        <span>Rincian Pesanan</span>
                    </h2>
                    <span class="text-xs font-bold uppercase px-2 py-0.5 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-200 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                        {{ $order->order_type === 'design_printing' ? 'Desain Kustom & Cetak' : 'Kasir POS Ritel' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                        <span class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 block mb-0.5">Nama Customer</span>
                        <div class="font-black text-sm text-slate-900 dark:text-white">{{ $order->customer_name }}</div>
                        @if($order->customer_phone)
                            <div class="font-bold text-slate-600 dark:text-slate-300 mt-1">WA: {{ $order->customer_phone }}</div>
                        @endif
                    </div>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                        <span class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 block mb-0.5">Cabang & Kasir</span>
                        <div class="font-black text-sm text-slate-900 dark:text-white">{{ $order->branch->name }}</div>
                        <div class="font-bold text-slate-600 dark:text-slate-300 mt-1">Kasir: {{ $order->creator->name }}</div>
                    </div>
                </div>

                <!-- If Design Order: Show Design Job Specs -->
                @if($order->order_type === 'design_printing' && $order->designJob)
                <div class="pt-3 border-t-[1.5px] border-slate-800 dark:border-slate-800 space-y-3">
                    <h3 class="font-black text-sm text-slate-900 dark:text-white">Spesifikasi Pengerjaan Desain:</h3>
                    
                    <div class="p-4 bg-amber-50 dark:bg-slate-900 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl space-y-2.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                        <div class="font-black text-base text-slate-900 dark:text-white">
                            {{ $order->designJob->title }}
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 block">Dimensi</span>
                                <span class="font-extrabold text-slate-900 dark:text-white">{{ $order->designJob->dimensions ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 block">Bahan</span>
                                <span class="font-extrabold text-slate-900 dark:text-white">{{ $order->designJob->material_notes ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 block">Jumlah Cetak</span>
                                <span class="font-black text-amber-900 dark:text-amber-400 bg-amber-200/80 dark:bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-300 dark:border-amber-400/30 font-mono inline-block">
                                    {{ $order->items->first()?->qty ?? 1 }} Pcs
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 block">Deadline</span>
                                <span class="font-extrabold {{ $order->designJob->deadline_at && $order->designJob->deadline_at->isPast() ? 'text-rose-700 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">
                                    {{ $order->designJob->deadline_at ? $order->designJob->deadline_at->format('d M Y H:i') : '-' }}
                                </span>
                            </div>
                        </div>

                        @if($order->designJob->brief_notes)
                        <div class="pt-2 border-t border-slate-800/20 dark:border-slate-800 text-xs">
                            <span class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 block mb-1">Catatan Brief Desain:</span>
                            <div class="p-2.5 bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 font-medium whitespace-pre-line">
                                {{ $order->designJob->brief_notes }}
                            </div>
                        </div>
                        @endif

                        @if($order->designJob->revision_notes)
                        <div class="pt-2 border-t border-slate-800/20 dark:border-slate-800 text-xs">
                            <div class="flex items-center gap-1.5 font-black text-rose-900 dark:text-rose-400 mb-1 text-[10px] uppercase">
                                <x-icon name="alert-circle" class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400" />
                                <span>Catatan Revisi Pelanggan (Revisi ke-{{ $order->designJob->revision_count ?? 1 }}):</span>
                            </div>
                            <div class="p-2.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-500/40 rounded-lg text-rose-950 dark:text-rose-200 font-bold whitespace-pre-line">
                                {{ $order->designJob->revision_notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($order->isCanceled())
                <div class="p-3.5 bg-rose-50 dark:bg-rose-950/30 border-[1.5px] border-rose-400 dark:border-rose-500/40 rounded-xl text-xs space-y-1.5 shadow-[1.5px_1.5px_0px_0px_#f43f5e] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                    <div class="font-black text-rose-950 dark:text-rose-300 flex items-center gap-1.5 text-xs uppercase">
                        <x-icon name="ban" class="w-4 h-4 text-rose-700 dark:text-rose-400" />
                        <span>Pesanan Dibatalkan</span>
                    </div>
                    <div class="text-rose-900 dark:text-rose-200 font-semibold"><span class="font-bold">Alasan:</span> {{ $order->cancellation_reason ?? 'Tidak ada catatan alasan' }}</div>
                    @if($order->cancellation_type === 'refund')
                        <div class="text-[11px] font-black text-emerald-900 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-950/50 px-2.5 py-1 rounded-md border border-emerald-300 dark:border-emerald-600 inline-flex items-center gap-1">
                            <x-icon name="check" class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                            <span>DP Rp {{ number_format($order->paid_amount, 0, ',', '.') }} telah di-refund ke pelanggan (Kas Keluar)</span>
                        </div>
                    @elseif($order->cancellation_type === 'forfeit')
                        <div class="text-[11px] font-bold text-slate-800 dark:text-slate-300 bg-slate-200 dark:bg-slate-800 px-2.5 py-1 rounded-md border border-slate-400 dark:border-slate-700 inline-flex items-center gap-1">
                            <x-icon name="info" class="w-3.5 h-3.5 text-slate-600 dark:text-slate-400" />
                            <span>DP Hangus (Non-refundable)</span>
                        </div>
                    @endif
                </div>
                @endif

                <!-- If Multi-items or Retail POS: Show Order Items -->
                @if($order->items->count() > 1 || ($order->order_type === 'retail_pos' && $order->items->count()))
                <div class="pt-3 border-t-[1.5px] border-slate-800 dark:border-slate-800">
                    <h3 class="font-black text-sm text-slate-900 dark:text-white mb-2 flex items-center gap-1.5">
                        <x-icon name="list" class="w-4 h-4 text-slate-700 dark:text-slate-300" />
                        <span>{{ $order->order_type === 'retail_pos' ? 'Item Belanja POS:' : 'Rincian Item & Alat Tambahan:' }}</span>
                    </h3>
                    <div class="overflow-x-auto border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl overflow-hidden shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-100 dark:bg-slate-900 border-b-[1.5px] border-slate-800 dark:border-slate-800 text-slate-900 dark:text-slate-200 font-black uppercase text-[10px]">
                                    <th class="py-2 px-3 text-left">Item Layanan / Barang</th>
                                    <th class="py-2 px-3 text-center">Qty</th>
                                    <th class="py-2 px-3 text-right">Harga Satuan</th>
                                    <th class="py-2 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/20 dark:divide-slate-800 bg-white dark:bg-[#0c1222]">
                                @foreach($order->items as $idx => $item)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/60 transition-colors">
                                    <td class="py-2.5 px-3">
                                        <div class="font-black text-slate-900 dark:text-white">{{ $item->item_name }}</div>
                                        @if($idx === 0 && $order->designJob)
                                            <span class="text-[10px] font-bold text-sky-700 dark:text-sky-400">Layanan Desain & Cetak Utama</span>
                                        @elseif($order->designJob)
                                            <span class="text-[10px] font-bold text-purple-700 dark:text-purple-400">Alat / Tambahan</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-bold text-slate-900 dark:text-white">{{ $item->qty }}</td>
                                    <td class="py-2.5 px-3 text-right font-medium text-slate-700 dark:text-slate-300">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-right font-black text-slate-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
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
            <div class="neo-box p-6 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b-[1.5px] border-slate-800 dark:border-slate-800">
                    <div>
                        <h2 class="font-black text-base text-slate-900 dark:text-white flex items-center gap-2.5">
                            <div class="w-7 h-7 bg-emerald-300 border-[1.5px] border-slate-800 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                                <x-icon name="cloud" class="w-4 h-4" />
                            </div>
                            <span>File Arsip Desain & Cetak</span>
                        </h2>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5 block">
                            Total {{ $order->designJob->assets->count() }} berkas terarsip di cloud storage
                        </span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        @php
                            $finalPrintAssets = $order->designJob ? $order->designJob->assets->where('file_type', 'final_print') : collect();
                        @endphp
                        @if($finalPrintAssets->count() > 0)
                        <button type="button" 
                                onclick="dispatchAllAssets()"
                                title="Kirim Semua Berkas Siap Cetak ke Percetakan Rekanan (WhatsApp / Email / Link)"
                                class="neo-btn bg-amber-300 hover:bg-amber-400 text-slate-950 text-xs py-1.5 px-3 font-black flex items-center gap-1.5 cursor-pointer shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:border-slate-800 dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                            <x-icon name="send" class="w-3.5 h-3.5 text-slate-900" />
                            <span>Kirim Berkas (All)</span>
                        </button>
                        @endif

                        <button type="button" 
                                onclick="if(window.resetUploadForm) window.resetUploadForm(document.querySelector('#upload-modal form')); document.getElementById('upload-modal').showModal()"
                                class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs py-1.5 px-3 font-black flex items-center gap-1.5 cursor-pointer shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:border-slate-800 dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                            <x-icon name="plus" class="w-3.5 h-3.5 stroke-[2.5]" />
                            <span>Unggah File</span>
                        </button>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($order->designJob->assets as $asset)
                    <div class="p-3.5 bg-slate-50/80 dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/80 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000] transition-all">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl border-[1.5px] border-slate-800 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-900 dark:text-white shrink-0 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                                @if($asset->file_type === 'final_print')
                                    <x-icon name="printer" class="w-5 h-5 text-emerald-700 dark:text-emerald-400" />
                                @elseif($asset->file_type === 'preview_proof')
                                    <x-icon name="eye" class="w-5 h-5 text-sky-700 dark:text-sky-400" />
                                @else
                                    <x-icon name="file-text" class="w-5 h-5 text-amber-700 dark:text-amber-400" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase px-2 py-0.5 rounded-md border-[1.5px] border-slate-800 dark:border-slate-700 shadow-[1px_1px_0px_0px_#0f172a] {{ $asset->file_type === 'final_print' ? 'bg-emerald-300 text-slate-950' : ($asset->file_type === 'preview_proof' ? 'bg-sky-200 text-slate-950' : 'bg-amber-200 text-slate-950') }}">
                                        {{ $asset->file_type_badge }}
                                    </span>
                                    <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 font-mono bg-white dark:bg-slate-800 px-1.5 py-0.2 rounded border border-slate-200 dark:border-slate-700">
                                        {{ $asset->formatted_size }}
                                    </span>
                                </div>
                                <div class="font-mono font-bold text-xs text-slate-900 dark:text-white truncate mt-1 max-w-[320px] sm:max-w-md" title="{{ $asset->file_name }}">
                                    {{ $asset->file_name }}
                                </div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                    Diunggah {{ $asset->created_at->format('d M Y, H:i') }} oleh {{ $asset->uploader->name ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                            @if($asset->file_type === 'final_print')
                            <!-- Kirim ke Percetakan Button -->
                            <button type="button" 
                                    onclick="openVendorDispatchModal({
                                        assetId: {{ $asset->id }},
                                        fileName: '{{ addslashes($asset->file_name) }}',
                                        orderNumber: '{{ addslashes($order->order_number) }}',
                                        jobTitle: '{{ addslashes($order->designJob->title ?? $order->customer_name) }}',
                                        dimensions: '{{ addslashes($order->designJob->dimensions ?? '') }}',
                                        materialNotes: '{{ addslashes($order->designJob->material_notes ?? '') }}',
                                        briefNotes: '{{ addslashes($order->designJob->brief_notes ?? '') }}',
                                        qty: {{ $order->items->first()?->qty ?? 1 }},
                                        shareUrl: '{{ $asset->share_url }}'
                                    })"
                                    title="Kirim Berkas ke Percetakan Rekanan (WA / Email / Link)"
                                    class="w-8 h-8 rounded-lg border-[1.5px] border-slate-900 dark:border-slate-700 bg-amber-300 hover:bg-amber-400 text-slate-950 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none flex items-center justify-center shrink-0 cursor-pointer transition-all">
                                <x-icon name="send" class="w-4 h-4 text-slate-900" />
                            </button>
                            @endif

                            <!-- Preview Button (Eye Icon) -->
                            <button type="button" 
                                    onclick="openFilePreview('{{ route('archive.preview', $asset->id) }}', '{{ addslashes($asset->file_name) }}', '{{ $asset->formatted_size }}', '{{ $asset->file_type_badge }}', '{{ str_ends_with(strtolower($asset->file_name), '.pdf') ? 'pdf' : ($asset->is_image ? 'image' : 'other') }}', '{{ route('archive.download', $asset->id) }}')"
                                    title="Pratinjau File (Preview)"
                                    class="w-8 h-8 rounded-lg border-[1.5px] border-slate-900 dark:border-slate-700 bg-sky-200 hover:bg-sky-300 dark:bg-sky-500/20 dark:hover:bg-sky-500/30 dark:border-sky-500/50 dark:text-sky-300 text-slate-900 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none flex items-center justify-center shrink-0 cursor-pointer transition-all">
                                <x-icon name="eye" class="w-4 h-4 text-slate-900 dark:text-sky-300" />
                            </button>

                            <!-- Download Button -->
                            <a href="{{ route('archive.download', $asset->id) }}" 
                               title="Download File" 
                               class="w-8 h-8 rounded-lg border-[1.5px] border-slate-900 dark:border-slate-700 bg-emerald-300 hover:bg-emerald-400 dark:bg-emerald-500/20 dark:hover:bg-emerald-500/30 dark:border-emerald-500/50 dark:text-emerald-300 text-slate-950 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none flex items-center justify-center shrink-0 transition-all">
                                <x-icon name="download" class="w-4 h-4 text-slate-950 dark:text-emerald-300" />
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center border-2 border-dashed border-slate-300 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-400 dark:text-slate-500">
                        Belum ada file desain atau cetak yang diunggah untuk tiket ini.
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Cash Movements Timeline For This Order -->
            <div class="neo-box p-6 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-3">
                <h2 class="font-black text-base text-slate-900 dark:text-white pb-2 border-b-[1.5px] border-slate-800 dark:border-slate-800 flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-purple-200 border-[1.5px] border-slate-800 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                        <x-icon name="credit-card" class="w-4 h-4" />
                    </div>
                    <span>Histori Mutasi Kas Terkait Order Ini</span>
                </h2>

                <div class="space-y-2">
                    @forelse($order->cashMovements as $mv)
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl flex items-center justify-between text-xs shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-black uppercase text-[10px] px-1.5 py-0.2 rounded border border-slate-800 {{ $mv->category_badge_class }}">
                                    {{ $mv->category_label }}
                                </span>
                                <span class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400">
                                    via {{ $mv->payment_method }}
                                </span>
                            </div>
                            <div class="font-semibold text-slate-700 dark:text-slate-300 mt-1">{{ $mv->notes ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                Kasir: {{ $mv->user->name }} &bull; {{ $mv->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-black text-emerald-700 dark:text-emerald-400">
                                + Rp {{ number_format($mv->amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-slate-400 dark:text-slate-500 font-semibold">
                        Belum ada mutasi kas tercatat untuk order ini.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Status Controls & Settlement (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Financial Card & Settlement Form (SOP 4) -->
            <div id="settle" class="neo-box p-6 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
                <h2 class="font-black text-base text-slate-900 dark:text-white pb-2 border-b-[1.5px] border-slate-800 dark:border-slate-800">
                    Status Pembayaran
                </h2>

                <div class="p-4 bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl space-y-2 text-xs shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Total Tagihan:</span>
                        <span class="font-extrabold text-slate-900 dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Sudah Dibayar (DP):</span>
                        <span class="font-extrabold text-emerald-700 dark:text-emerald-400">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-300 dark:border-slate-800 pt-2 text-sm">
                        <span class="font-black text-rose-700 dark:text-rose-400">Sisa Tagihan:</span>
                        <span class="font-black text-rose-700 dark:text-rose-400">Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($order->isCanceled())
                <div class="p-3 bg-rose-100 dark:bg-rose-950/40 border-[1.5px] border-slate-800 dark:border-rose-500/40 rounded-xl text-center text-xs font-black text-rose-950 dark:text-rose-300 flex items-center justify-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                    <x-icon name="ban" class="w-4 h-4 text-rose-800 dark:text-rose-400" />
                    <span>Pesanan Dibatalkan - Transaksi Ditutup</span>
                </div>
                @elseif($order->remaining_amount > 0)
                <!-- Pelunasan Form -->
                <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/20 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl space-y-3 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                    <p class="text-[11px] font-semibold text-slate-600 dark:text-slate-400 leading-relaxed">
                        Customer mengambil pesanan. Masukkan metode pembayaran pelunasan untuk menandai order selesai & mencatat kas masuk.
                    </p>

                    <form method="POST" action="{{ route('orders.settle', $order->id) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-900 dark:text-white mb-1">Metode Pelunasan</label>
                            <x-neo-select 
                                name="payment_method" 
                                required 
                                value="cash"
                                :options="[
                                    'cash' => 'Tunai (Cash Laci)',
                                    'qris' => 'QRIS Statis/Dinamis',
                                    'transfer' => 'Transfer Bank',
                                ]" 
                            />
                        </div>
                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-900 dark:text-white mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="notes" placeholder="Misal: Pelunasan saat ambil barang" class="neo-input text-xs py-1.5">
                        </div>
                        <button type="submit" class="w-full neo-btn bg-emerald-400 hover:bg-emerald-300 dark:text-slate-950 text-xs py-2.5 font-black text-slate-950 flex items-center justify-center gap-1.5 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a] dark:border-slate-800 dark:shadow-[2px_2px_0px_0px_#000000]">
                            <x-icon name="check" class="w-4 h-4 stroke-[2.5]" />
                            <span>Terima Pelunasan Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
                        </button>
                    </form>
                </div>
                @else
                <div class="p-3 bg-emerald-100 dark:bg-emerald-950/40 border-[1.5px] border-slate-800 dark:border-emerald-600 rounded-xl text-center text-xs font-black text-emerald-950 dark:text-emerald-300 flex items-center justify-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                    <x-icon name="check-circle" class="w-4 h-4 text-emerald-800 dark:text-emerald-400" />
                    <span>Tagihan Telah Lunas Penuh</span>
                </div>
                @endif
            </div>

            <!-- Work Status Control Card -->
            <div class="neo-box p-6 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
                <div class="flex items-center justify-between pb-2 border-b-[1.5px] border-slate-800 dark:border-slate-800">
                    <h2 class="font-black text-base text-slate-900 dark:text-white">
                        Kelola Status Pekerjaan
                    </h2>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full border-[1.5px] border-slate-800 shadow-[1px_1px_0px_0px_#0f172a] {{ $order->status_color_class }}">
                        {{ $order->status_label }}
                    </span>
                </div>

                @if($order->isCompleted())
                <!-- State Guard: Completed Order is Locked -->
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border-2 border-slate-800 dark:border-slate-700 rounded-xl space-y-3 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]">
                    <div class="flex items-center gap-2 text-emerald-950 dark:text-emerald-200 font-black text-sm">
                        <div class="w-6 h-6 rounded-md bg-emerald-300 border-[1.5px] border-slate-800 flex items-center justify-center shrink-0">
                            <x-icon name="lock" class="w-3.5 h-3.5 text-slate-900" />
                        </div>
                        <span>Status Selesai (Terkunci)</span>
                    </div>
                    <p class="text-xs font-medium text-emerald-900 dark:text-emerald-300 leading-relaxed">
                        Pesanan ini telah selesai dan diambil oleh pelanggan. Status tiket dikunci untuk menjaga integritas data operasional & buku kas.
                    </p>
                    <div class="text-[11px] font-bold text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                        <x-icon name="clock" class="w-3.5 h-3.5" />
                        <span>Selesai: {{ $order->completed_at ? $order->completed_at->format('d M Y H:i') : $order->updated_at->format('d M Y H:i') }}</span>
                    </div>

                    @if(Auth::user()->isOwner())
                    <div class="pt-2 border-t border-emerald-200 dark:border-slate-800">
                        <button type="button" onclick="document.getElementById('reopen-modal').showModal()" class="w-full neo-btn bg-amber-200 hover:bg-amber-300 dark:text-slate-950 text-xs py-2 font-black text-slate-900 flex items-center justify-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:border-slate-800 dark:shadow-[1.5px_1.5px_0px_0px_#000000] cursor-pointer">
                            <x-icon name="unlock" class="w-3.5 h-3.5" />
                            <span>Buka Kembali Tiket (Khusus Owner)</span>
                        </button>
                    </div>
                    @endif
                </div>

                @elseif($order->isCanceled())
                <!-- State Guard: Canceled Order is Locked -->
                <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border-2 border-slate-800 dark:border-slate-700 rounded-xl space-y-3 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]">
                    <div class="flex items-center gap-2 text-rose-950 dark:text-rose-200 font-black text-sm">
                        <div class="w-6 h-6 rounded-md bg-rose-300 border-[1.5px] border-slate-800 flex items-center justify-center shrink-0">
                            <x-icon name="ban" class="w-3.5 h-3.5 text-slate-900" />
                        </div>
                        <span>Pesanan Dibatalkan (Terkunci)</span>
                    </div>
                    <p class="text-xs font-semibold text-rose-900 dark:text-rose-300 leading-relaxed">
                        <span class="font-black">Alasan:</span> {{ $order->cancellation_reason ?? 'Tidak ada alasan dicatat' }}
                    </p>
                    @if($order->cancellation_type === 'refund')
                    <div class="p-2 bg-white/90 dark:bg-slate-900 border border-rose-300 dark:border-rose-500/40 rounded-lg text-xs font-bold text-rose-950 dark:text-rose-200 flex items-center gap-1.5">
                        <x-icon name="check" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        <span>DP Rp {{ number_format($order->paid_amount, 0, ',', '.') }} telah di-refund via Kas Keluar Laci.</span>
                    </div>
                    @elseif($order->cancellation_type === 'forfeit')
                    <div class="p-2 bg-white/90 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-300 flex items-center gap-1.5">
                        <x-icon name="info" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        <span>DP Rp {{ number_format($order->paid_amount, 0, ',', '.') }} dinyatakan Hangus (Non-refundable).</span>
                    </div>
                    @endif

                    @if(Auth::user()->isOwner())
                    <div class="pt-2 border-t border-rose-200 dark:border-slate-800">
                        <button type="button" onclick="document.getElementById('reopen-modal').showModal()" class="w-full neo-btn bg-amber-200 hover:bg-amber-300 dark:text-slate-950 text-xs py-2 font-black text-slate-900 flex items-center justify-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:border-slate-800 dark:shadow-[1.5px_1.5px_0px_0px_#000000] cursor-pointer">
                            <x-icon name="unlock" class="w-3.5 h-3.5" />
                            <span>Buka Kembali Tiket (Khusus Owner)</span>
                        </button>
                    </div>
                    @endif
                </div>

                @else
                <!-- Active Order Status Workflow Form -->
                <form method="POST" action="{{ route('orders.status', $order->id) }}" id="status-change-form" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-black uppercase text-slate-900 dark:text-white mb-1">Pilih Status Baru</label>
                        <x-neo-select 
                            name="work_status" 
                            id="work_status_select" 
                            onchange="handleWorkStatusChange(this.value)"
                            :value="$order->work_status"
                            :options="[
                                'draft' => 'Draft / Antrean',
                                'in_progress' => 'Proses Pengerjaan',
                                'revision' => 'Revisi (Perlu Perbaikan Desain)',
                                'ready_to_print' => 'Siap Cetak (Produksi)',
                                'completed' => 'Selesai / Diambil Customer',
                                'canceled' => 'Batalkan Pesanan',
                            ]" 
                        />
                    </div>

                    <!-- Contextual Field: Revision Notes -->
                    <div id="revision-context-box" class="{{ $order->work_status === 'revision' ? '' : 'hidden' }} p-3.5 bg-rose-50 dark:bg-rose-950/30 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl space-y-2 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                        <div class="flex items-center gap-1.5 text-xs font-black text-rose-950 dark:text-rose-300">
                            <x-icon name="edit-3" class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                            <span>Catatan Revisi Pelanggan (Wajib):</span>
                        </div>
                        <textarea name="revision_notes" id="revision_notes_input" rows="3" placeholder="Tuliskan detail perubahan dari customer (misal: warna background ganti maroon, besarkan ukuran font nomor HP)..." class="neo-input text-xs w-full leading-relaxed">{{ old('revision_notes', $order->designJob->revision_notes ?? '') }}</textarea>
                        <p class="text-[10px] font-bold text-rose-800 dark:text-rose-400">
                            💡 Tiket akan langsung meloncat ke antrean teratas di Workdesk Desainer dengan badge Revisi Prioritas.
                        </p>
                    </div>

                    <!-- Contextual Field: Cancellation Details -->
                    <div id="canceled-context-box" class="hidden p-3.5 bg-rose-50 dark:bg-rose-950/30 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl space-y-3 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                        <div class="flex items-center gap-1.5 text-xs font-black text-rose-950 dark:text-rose-300">
                            <x-icon name="alert-triangle" class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                            <span>Detail Pembatalan Pesanan:</span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1">Alasan Pembatalan (Wajib)</label>
                            <input type="text" name="cancellation_reason" id="cancellation_reason_input" placeholder="Misal: Customer membatalkan acara / salah cetak" class="neo-input text-xs py-1.5">
                        </div>

                        @if($order->paid_amount > 0)
                        <div class="p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg space-y-2 text-xs">
                            <span class="block text-[10px] font-black uppercase text-slate-900 dark:text-white">
                                Perlakuan DP Masuk (Rp {{ number_format($order->paid_amount, 0, ',', '.') }}):
                            </span>
                            <div class="space-y-1.5">
                                <label class="flex items-start gap-2 cursor-pointer font-bold text-slate-800 dark:text-slate-300">
                                    <input type="radio" name="cancellation_type" value="refund" checked class="mt-0.5 text-rose-600 focus:ring-rose-400" onchange="toggleRefundMethod(true)">
                                    <span>Refund ke Pelanggan <span class="text-rose-600 dark:text-rose-400 font-extrabold">(Catat Kas Keluar Laci)</span></span>
                                </label>
                                <label class="flex items-start gap-2 cursor-pointer font-bold text-slate-800 dark:text-slate-300">
                                    <input type="radio" name="cancellation_type" value="forfeit" class="mt-0.5 text-slate-600 focus:ring-slate-400" onchange="toggleRefundMethod(false)">
                                    <span>DP Hangus <span class="text-slate-500 dark:text-slate-400 font-normal">(Tidak dikembalikan)</span></span>
                                </label>
                            </div>
                            <div id="refund-method-select" class="pt-2 border-t border-slate-200 dark:border-slate-800">
                                <label class="block text-[10px] font-black uppercase text-slate-700 dark:text-slate-300 mb-1">Metode Pengembalian Uang</label>
                                <x-neo-select 
                                    name="refund_method" 
                                    value="cash"
                                    :options="[
                                        'cash' => 'Kas Tunai Laci (Cash)',
                                        'transfer' => 'Transfer Bank',
                                    ]" 
                                />
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Contextual Field: Unpaid Warning on Completed -->
                    @if($order->remaining_amount > 0)
                    <div id="unpaid-warning-box" class="hidden p-3 bg-amber-100 dark:bg-amber-950/40 border-[1.5px] border-amber-600 dark:border-amber-500/50 rounded-xl text-xs space-y-1 shadow-[1.5px_1.5px_0px_0px_#d97706] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                        <div class="font-black text-amber-950 dark:text-amber-300 flex items-center gap-1.5">
                            <x-icon name="alert-circle" class="w-4 h-4 text-amber-700 dark:text-amber-400" />
                            <span>Perhatian: Tagihan Belum Lunas!</span>
                        </div>
                        <p class="text-amber-900 dark:text-amber-200 font-medium leading-relaxed">
                            Pesanan ini masih memiliki sisa tagihan <strong>Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</strong>. Silakan klik tombol <strong>"Terima Pelunasan"</strong> di kartu Status Pembayaran di atas untuk sekaligus melunasi dan menyelesaikan pesanan.
                        </p>
                    </div>
                    @endif

                    <button type="submit" id="btn-update-status" class="w-full neo-btn bg-amber-300 hover:bg-amber-400 dark:text-slate-950 text-xs py-2.5 font-black text-slate-900 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a] dark:border-slate-800 dark:shadow-[2px_2px_0px_0px_#000000] flex items-center justify-center gap-1.5">
                        <x-icon name="refresh-cw" class="w-3.5 h-3.5" />
                        <span>Perbarui Status Tiket</span>
                    </button>
                </form>
                @endif
            </div>

        </div>

    </div>

</div>

<!-- Upload Asset Modal for this order -->
@if($order->designJob)
<dialog id="upload-modal" closedby="any" class="p-0 bg-transparent max-w-md w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white dark:bg-[#0c1222] w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#000000] rounded-2xl border-2 border-slate-900 dark:border-slate-700 overflow-visible">
        <div class="flex items-center justify-between pb-3 border-b-[1.5px] border-slate-800 dark:border-slate-800 mb-4">
            <h3 class="font-black text-base text-slate-900 dark:text-white">Unggah File Tiket {{ $order->order_number }}</h3>
            <form method="dialog">
                <button class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 bg-rose-200 dark:bg-rose-900/40 hover:bg-rose-300 dark:hover:bg-rose-800/60 dark:text-rose-300 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                    ✕
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('workdesk.upload', $order->designJob->id) }}" enctype="multipart/form-data" data-ajax-upload class="space-y-4">
            @csrf

            <x-upload-progress />

            <div class="upload-fields-container space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase text-slate-900 dark:text-white mb-1">Tipe File</label>
                    <x-neo-select 
                        name="file_type" 
                        required 
                        value="final_print"
                        :options="[
                            'final_print' => 'File Cetak Siap Produksi (Status → Siap Cetak)',
                            'preview_proof' => 'Preview Proofing ke Pelanggan',
                            'brief_ref' => 'Referensi Brief / Lampiran',
                        ]" 
                    />
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-slate-900 dark:text-white mb-1">Pilih File</label>
                    <input type="file" name="files[]" multiple required class="neo-input text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-[1.5px] file:border-slate-800 file:bg-amber-300 file:text-xs file:font-black cursor-pointer">
                    <div class="selected-files-preview mt-2 hidden text-xs bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl p-2.5 space-y-1.5 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                        <!-- List file dirender via JS -->
                    </div>
                </div>

                <div class="pt-3 border-t-[1.5px] border-slate-800 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('upload-modal').close()" class="neo-btn bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 dark:border-slate-700 text-xs py-2 px-4 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="neo-btn bg-emerald-400 hover:bg-emerald-300 dark:text-slate-950 text-xs py-2 px-4 font-black flex items-center gap-1.5 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a] dark:border-slate-800 dark:shadow-[2px_2px_0px_0px_#000000]">
                        <x-icon name="upload" class="w-3.5 h-3.5" />
                        <span>Unggah & Simpan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</dialog>
@endif

<!-- Owner Reopen Modal -->
@if(Auth::user()->isOwner() && $order->isLocked())
<dialog id="reopen-modal" closedby="any" class="p-0 bg-transparent max-w-md w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white dark:bg-[#0c1222] w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#000000] rounded-2xl border-2 border-slate-900 dark:border-slate-700 overflow-visible">
        <div class="flex items-center justify-between pb-3 border-b-[1.5px] border-slate-800 dark:border-slate-800 mb-4">
            <h3 class="font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-7 h-7 bg-amber-200 border-[1.5px] border-slate-800 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                    <x-icon name="unlock" class="w-4 h-4 text-slate-900" />
                </div>
                <span>Buka Kembali Tiket {{ $order->order_number }}</span>
            </h3>
            <form method="dialog">
                <button class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 bg-rose-200 dark:bg-rose-900/40 hover:bg-rose-300 dark:hover:bg-rose-800/60 dark:text-rose-300 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                    ✕
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('orders.status', $order->id) }}" class="space-y-4">
            @csrf
            <input type="hidden" name="work_status" value="reopen">
            
            <div>
                <label class="block text-xs font-black uppercase text-slate-900 dark:text-white mb-1">Target Status Pembukaan</label>
                <x-neo-select 
                    name="target_status" 
                    required 
                    value="in_progress"
                    :options="[
                        'in_progress' => 'Proses Pengerjaan (Aktif Kembali)',
                        'revision' => 'Revisi (Masuk Antrean Perbaikan Desain)',
                    ]" 
                />
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-slate-900 dark:text-white mb-1">Alasan Membuka Kembali Tiket (Wajib)</label>
                <textarea name="reopen_reason" required rows="3" placeholder="Tuliskan alasan mengapa tiket yang sudah selesai/dibatalkan ini dibuka kembali..." class="neo-input text-xs w-full leading-relaxed"></textarea>
            </div>

            <div class="pt-3 border-t-[1.5px] border-slate-800 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('reopen-modal').close()" class="neo-btn bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 dark:border-slate-700 text-xs py-2 px-4 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="neo-btn bg-amber-300 hover:bg-amber-400 dark:text-slate-950 text-xs py-2 px-4 font-black flex items-center gap-1.5 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a] dark:border-slate-800 dark:shadow-[2px_2px_0px_0px_#000000]">
                    <x-icon name="unlock" class="w-3.5 h-3.5" />
                    <span>Konfirmasi Buka Tiket</span>
                </button>
            </div>
        </form>
    </div>
</dialog>
@endif

<script>
function handleWorkStatusChange(val) {
    const revBox = document.getElementById('revision-context-box');
    const cancelBox = document.getElementById('canceled-context-box');
    const unpaidBox = document.getElementById('unpaid-warning-box');
    const btn = document.getElementById('btn-update-status');
    const remainingAmount = {{ (float) $order->remaining_amount }};

    if (revBox) revBox.classList.toggle('hidden', val !== 'revision');
    if (cancelBox) cancelBox.classList.toggle('hidden', val !== 'canceled');

    if (val === 'completed' && remainingAmount > 0) {
        if (unpaidBox) unpaidBox.classList.remove('hidden');
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('hover:bg-amber-400');
        }
    } else {
        if (unpaidBox) unpaidBox.classList.add('hidden');
        if (btn) {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.classList.add('hover:bg-amber-400');
        }
    }

    const revInput = document.getElementById('revision_notes_input');
    if (revInput) revInput.required = (val === 'revision');

    const cancelInput = document.getElementById('cancellation_reason_input');
    if (cancelInput) cancelInput.required = (val === 'canceled');
}

function toggleRefundMethod(isRefund) {
    const sel = document.getElementById('refund-method-select');
    if (sel) sel.classList.toggle('hidden', !isRefund);
}

function dispatchAllAssets() {
    if (typeof openVendorDispatchModal !== 'function') return;
    openVendorDispatchModal({
        isAll: true,
        jobId: {{ $order->designJob ? $order->designJob->id : 'null' }},
        orderNumber: '{{ addslashes($order->order_number) }}',
        jobTitle: '{{ addslashes($order->designJob->title ?? $order->customer_name) }}',
        dimensions: '{{ addslashes($order->designJob->dimensions ?? '') }}',
        materialNotes: '{{ addslashes($order->designJob->material_notes ?? '') }}',
        briefNotes: '{{ addslashes($order->designJob->brief_notes ?? '') }}',
        qty: {{ $order->items->first()?->qty ?? 1 }},
        shareUrl: '{{ $order->designJob && $order->designJob->assets->where('file_type', 'final_print')->isNotEmpty() ? $order->designJob->assets->where('file_type', 'final_print')->first()->share_url : '' }}',
        files: [
            @if($order->designJob)
            @foreach($order->designJob->assets->where('file_type', 'final_print') as $ast)
            {
                id: {{ $ast->id }},
                name: '{{ addslashes($ast->file_name) }}',
                size: '{{ $ast->formatted_size }}',
                typeBadge: '{{ addslashes($ast->file_type_badge) }}',
                shareUrl: '{{ $ast->share_url }}'
            },
            @endforeach
            @endif
        ]
    });
}
</script>
@endsection
