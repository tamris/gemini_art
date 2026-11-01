<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} - Gemini Art</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { 
                background: white !important; 
                padding: 0 !important;
                margin: 0 !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .invoice-sheet { 
                box-shadow: none !important; 
                border: none !important; 
                max-width: 100% !important; 
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
            }
            @page {
                size: A4 portrait;
                margin: 14mm 16mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen py-8 px-4 sm:px-6 flex flex-col items-center antialiased">

    @php
        $rawPhone = $order->customer_phone ?? '';
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $waPhone = '62' . substr($cleanPhone, 1);
        } else {
            $waPhone = $cleanPhone;
        }

        $invNumber = 'INV-' . str_replace('ORD-', '', $order->order_number);
        $itemName = $order->designJob ? $order->designJob->title : ($order->items->first()?->item_name ?? 'Pesanan Percetakan');
        
        $waMessage = "Halo Kak {$order->customer_name},%0A%0ABerikut informasi *Invoice Resmi* dari *Gemini Art Studio & Percetakan*:%0A"
            . "------------------------------------%0A"
            . "• *No. Invoice:* {$invNumber}%0A"
            . "• *Cabang:* {$order->branch->name}%0A"
            . "• *Pesanan:* {$itemName}%0A"
            . "• *Total Biaya:* Rp " . number_format($order->total_amount, 0, ',', '.') . "%0A"
            . "• *Telah Dibayar:* Rp " . number_format($order->paid_amount, 0, ',', '.') . "%0A"
            . "• *Sisa Tagihan:* Rp " . number_format($order->remaining_amount, 0, ',', '.') . "%0A"
            . "• *Status:* " . strtoupper($order->payment_status_label) . "%0A"
            . "------------------------------------%0A"
            . "Terima kasih atas kepercayaannya mencetak bersama kami! 🙏";
    @endphp

    <!-- Screen-Only Minimalist Action Bar -->
    <div class="no-print sticky top-4 z-50 w-full max-w-4xl bg-white/90 backdrop-blur-md border border-slate-200/80 rounded-xl shadow-sm px-4 py-3 mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('orders.show', $order->id) }}" 
               class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 font-semibold text-xs text-slate-700 flex items-center gap-1.5 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5 text-slate-500" />
                <span>Kembali</span>
            </a>
            <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
            <span class="text-xs font-medium text-slate-500 hidden sm:inline">
                Format Cetak: <strong>Standar A4 Formal</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            @if(!empty($waPhone))
            <a href="https://wa.me/{{ $waPhone }}?text={{ $waMessage }}" 
               target="_blank"
               class="px-3 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 font-semibold text-xs text-emerald-800 flex items-center gap-1.5 transition-colors">
                <x-icon name="send" class="w-3.5 h-3.5 text-emerald-600" />
                <span>Kirim WhatsApp</span>
            </a>
            @endif

            <button onclick="window.print()" 
                    class="px-3.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs flex items-center gap-1.5 shadow-sm transition-colors cursor-pointer">
                <x-icon name="printer" class="w-3.5 h-3.5 text-slate-200" />
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Professional A4 Sheet (Exact Clean Corporate Layout) -->
    <div class="invoice-sheet w-full max-w-4xl bg-white border border-slate-200/90 rounded-xl shadow-md p-8 sm:p-14 text-slate-800 flex flex-col justify-between min-h-[1050px]">
        
        <div>
            <!-- Header: Clean Modern Studio Branding & Document Type -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-8 border-b border-slate-200 gap-6">
                <!-- Brand / Logo Area -->
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xl tracking-tight shrink-0 shadow-sm">
                        GA
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-bold tracking-tight text-slate-900">GEMINI ART</h1>
                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                Studio &bull; Percetakan
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Solusi Desain Grafis, Digital Printing & Offset</p>
                    </div>
                </div>

                <!-- Document Identifier & Status -->
                <div class="flex flex-col sm:items-end">
                    <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">FAKTUR TAGIHAN</span>
                    <span class="text-2xl font-semibold tracking-tight text-slate-900 font-mono">
                        {{ $invNumber }}
                    </span>
                    
                    <!-- Clean Corporate Status Badge -->
                    <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold
                        @if($order->payment_status === 'paid')
                            bg-emerald-50 text-emerald-700 border border-emerald-200
                        @elseif($order->payment_status === 'partial_dp')
                            bg-amber-50 text-amber-700 border border-amber-200
                        @else
                            bg-rose-50 text-rose-700 border border-rose-200
                        @endif">
                        <span class="w-1.5 h-1.5 rounded-full 
                            {{ $order->payment_status === 'paid' ? 'bg-emerald-500' : ($order->payment_status === 'partial_dp' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                        <span>
                            @if($order->payment_status === 'paid')
                                LUNAS / FULL PAID
                            @elseif($order->payment_status === 'partial_dp')
                                DP DITERIMA &bull; SISA TAGIHAN
                            @else
                                BELUM BAYAR &bull; UNPAID
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Two Details Boxes (Reference Image Style: FROM & TO) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
                
                <!-- Box 1: Your Details / FROM -->
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1.5 text-xs">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                        DARI / PENYEDIA JASA:
                    </span>
                    <h3 class="font-bold text-sm text-slate-900">Gemini Art - Cabang {{ $order->branch->clean_name }}</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $order->branch->address }}</p>
                    <div class="pt-2 text-slate-500 flex items-center justify-between border-t border-slate-200/80">
                        <span>Telp/WhatsApp: <strong>{{ $order->branch->phone ?? '0823-2323-2323' }}</strong></span>
                        <span>Kasir: {{ $order->creator->name }}</span>
                    </div>
                </div>

                <!-- Box 2: Client's Details / TO -->
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1.5 text-xs">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                        KEPADA / TAGIHAN UNTUK:
                    </span>
                    <h3 class="font-bold text-sm text-slate-900">{{ $order->customer_name }}</h3>
                    <p class="text-slate-600">
                        @if($order->customer_phone)
                            No. Kontak / WA: <strong>{{ $order->customer_phone }}</strong>
                        @else
                            Pelanggan Umum
                        @endif
                    </p>
                    <div class="pt-2 text-slate-500 flex items-center justify-between border-t border-slate-200/80">
                        <span>Jenis Pesanan:</span>
                        <span class="font-semibold text-slate-800">
                            {{ $order->order_type === 'design_printing' ? 'Desain Kustom & Cetak' : 'Kasir Ritel POS' }}
                        </span>
                    </div>
                </div>

            </div>

            <!-- Quick Metadata Row -->
            <div class="flex flex-wrap items-center justify-between py-3 px-4 rounded-lg bg-slate-50 border border-slate-200/60 text-xs text-slate-600 mb-6 gap-3">
                <div>
                    <span class="text-slate-400">Tanggal Terbit:</span>
                    <strong class="text-slate-800 ml-1">{{ $order->created_at->translatedFormat('d F Y') }}</strong>
                </div>
                <div>
                    <span class="text-slate-400">Tenggat / Deadline:</span>
                    <strong class="text-slate-800 ml-1">
                        {{ $order->designJob?->deadline_at ? $order->designJob->deadline_at->translatedFormat('d F Y, H:i') : 'Selesai' }}
                    </strong>
                </div>
                <div>
                    <span class="text-slate-400">Status Pengerjaan:</span>
                    <strong class="text-slate-800 ml-1">{{ $order->status_label }}</strong>
                </div>
                <div>
                    <span class="text-slate-400">Metode Pembayaran:</span>
                    <strong class="text-slate-800 ml-1 uppercase">{{ $order->cashMovements->first()?->payment_method ?? 'TUNAI' }}</strong>
                </div>
            </div>

            <!-- Line Items Table (Clean Minimalist Professional Grid) -->
            <div class="overflow-hidden border border-slate-200 rounded-xl mb-6">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-[10px] tracking-wider">
                            <th class="py-3 px-4 w-10 text-center">No</th>
                            <th class="py-3 px-4">Deskripsi Layanan & Spesifikasi</th>
                            <th class="py-3 px-4 w-24 text-center">Qty</th>
                            <th class="py-3 px-4 w-32 text-right">Harga Satuan</th>
                            <th class="py-3 px-4 w-36 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @if($order->order_type === 'design_printing' && $order->designJob)
                        <tr>
                            <td class="py-4 px-4 text-center font-medium text-slate-400">1</td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $order->designJob->title }}</div>
                                <div class="mt-1 space-y-0.5 text-xs text-slate-500 leading-relaxed">
                                    @if($order->designJob->dimensions)
                                        <div>Ukuran Jadi: <strong class="text-slate-700">{{ $order->designJob->dimensions }}</strong></div>
                                    @endif
                                    @if($order->designJob->material_notes)
                                        <div>Bahan & Finishing: <strong class="text-slate-700">{{ $order->designJob->material_notes }}</strong></div>
                                    @endif
                                    @if($order->designJob->brief_notes)
                                        <div class="italic text-[11px] text-slate-400">Catatan: {{ $order->designJob->brief_notes }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center font-medium">1 Paket</td>
                            <td class="py-4 px-4 text-right font-medium">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 text-right font-bold text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @else
                            @foreach($order->items as $idx => $item)
                            <tr>
                                <td class="py-3 px-4 text-center font-medium text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $item->item_name }}</td>
                                <td class="py-3 px-4 text-center">{{ $item->qty }}</td>
                                <td class="py-3 px-4 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Bottom Section: Payment Info (Left) & Invoice Summary (Right, Like Image) -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-6 items-start">
                
                <!-- Left: Bank Transfer Details (7 cols) -->
                <div class="sm:col-span-7 space-y-3 text-xs">
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                            Petunjuk Pembayaran Resmi:
                        </span>
                        <div class="text-xs text-slate-700 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Bank Central Asia (BCA):</span>
                                <strong class="font-mono text-slate-900">012-3456-789</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Bank Mandiri:</span>
                                <strong class="font-mono text-slate-900">137-00-1234567-8</strong>
                            </div>
                            <div class="text-[11px] text-slate-500 pt-1">
                                Atas Nama: <strong>Gemini Art Studio & Percetakan</strong>
                            </div>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        * Harap simpan invoice ini sebagai bukti sah pengambilan barang cetak di workshop cabang. Pembayaran via transfer mohon mengirimkan bukti transfer ke WhatsApp admin cabang.
                    </p>
                </div>

                <!-- Right: Invoice Summary Card (5 cols, Structured Like Reference Image) -->
                <div class="sm:col-span-5">
                    <div class="rounded-xl border border-slate-200 overflow-hidden text-xs">
                        <!-- Card Header -->
                        <div class="bg-slate-100/80 px-4 py-2.5 font-bold text-slate-700 border-b border-slate-200 text-center uppercase tracking-wider text-[11px]">
                            Invoice Summary
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 bg-white space-y-2">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Subtotal:</span>
                                <span class="font-semibold text-slate-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center text-slate-600">
                                <span>Uang Muka / DP Dibayar:</span>
                                <span class="font-semibold text-emerald-700">- Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
                            </div>

                            <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline">
                                <span class="font-bold text-sm text-slate-900">Sisa Tagihan:</span>
                                <span class="font-bold text-base {{ $order->remaining_amount <= 0 ? 'text-emerald-700' : 'text-slate-950' }}">
                                    Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}
                                </span>
                            </div>

                            @if($order->remaining_amount <= 0)
                            <div class="mt-1 text-center py-1 rounded bg-emerald-50 text-emerald-800 text-[10px] font-bold uppercase tracking-wider">
                                Pembayaran Lunas
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Professional Signatures & Document Footer -->
        <div class="mt-14 pt-6 border-t border-slate-200">
            <div class="grid grid-cols-2 gap-8 text-xs text-center">
                <!-- Client Signature -->
                <div class="space-y-14">
                    <span class="text-slate-500 block font-medium">Tanda Terima Pelanggan</span>
                    <div class="border-t border-slate-300 w-44 mx-auto pt-1 font-bold text-slate-800">
                        {{ $order->customer_name }}
                    </div>
                </div>

                <!-- Studio Signature -->
                <div class="space-y-14">
                    <span class="text-slate-500 block font-medium">Hormat Kami, Gemini Art</span>
                    <div class="border-t border-slate-300 w-44 mx-auto pt-1 font-bold text-slate-800">
                        {{ $order->creator->name }}
                        <span class="block text-[10px] text-slate-400 font-normal">Petugas Cabang</span>
                    </div>
                </div>
            </div>

            <!-- Micro Print Note -->
            <div class="text-center text-[10px] text-slate-400 pt-6 mt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-1">
                <span>Gemini Art Multi-Cabang &bull; Faktur Resmi Transaksi Cetak & Desain</span>
                <span class="font-mono text-[9px]">ID Order: {{ $order->order_number }}</span>
            </div>
        </div>

    </div>

</body>
</html>

