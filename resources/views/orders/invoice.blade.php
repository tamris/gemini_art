<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} - Gemini Art</title>
    @vite(['resources/css/app.css'])
    <style>
        /* CSS Variables & Global Typography */
        :root {
            --inv-primary: #0b2559;
            --inv-primary-dark: #07193d;
            --inv-secondary: #173873;
            --inv-text: #0f172a;
            --inv-muted: #475569;
        }

        @media print {
            .no-print { 
                display: none !important; 
            }
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                width: 210mm !important;
                height: 297mm !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .invoice-sheet {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                max-width: 210mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        /* Screen A4 Dimensions standard */
        .invoice-sheet {
            position: relative;
            width: 210mm;
            height: 297mm;
            min-height: 297mm;
            max-height: 297mm;
            background: #ffffff;
            box-sizing: border-box;
            overflow: hidden;
            color: var(--inv-text);
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }

        .invoice-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: fill;
            pointer-events: none;
            z-index: 0;
        }

        .invoice-body {
            position: relative;
            z-index: 10;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            padding-top: 72mm;
            padding-left: 18mm;
            padding-right: 18mm;
            padding-bottom: 34mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header & Meta */
        .inv-header-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 14px;
        }
        .inv-bill-to-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--inv-primary);
            margin-bottom: 2px;
        }
        .inv-customer-name {
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.01em;
            color: var(--inv-primary);
            line-height: 1.2;
            margin-bottom: 6px;
        }
        .inv-customer-info {
            font-size: 11px;
            color: #334155;
            line-height: 1.5;
        }
        .inv-info-row {
            display: flex;
            gap: 8px;
        }
        .inv-info-lbl {
            font-weight: 700;
            width: 52px;
            color: #475569;
        }
        .inv-info-val {
            font-weight: 500;
            color: #0f172a;
        }

        .inv-meta-right {
            text-align: right;
            padding-top: 8px;
        }
        .inv-number-row {
            font-size: 12px;
            color: #334155;
            font-weight: 600;
        }
        .inv-number-val {
            font-weight: 900;
            color: var(--inv-primary);
            font-family: monospace;
            font-size: 13px;
            margin-left: 4px;
        }
        .inv-date-val {
            font-size: 12px;
            font-weight: 900;
            color: var(--inv-primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 4px;
        }
        .inv-status-pill {
            display: inline-block;
            margin-top: 6px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 2px 10px;
            border-radius: 9999px;
        }
        .status-paid {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .status-dp {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .status-unpaid {
            background-color: #fff1f2;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }

        /* Table */
        .inv-table-wrap {
            width: 100%;
            margin-bottom: 10px;
        }
        .inv-table-head {
            background-color: var(--inv-primary);
            color: #ffffff;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            height: 32px;
            padding: 0 16px;
            font-size: 11px;
            font-weight: 700;
        }
        .inv-col-no { width: 36px; text-align: center; }
        .inv-col-desc { flex: 1; padding: 0 12px; text-align: left; }
        .inv-col-qty { width: 50px; text-align: center; }
        .inv-col-price { width: 95px; text-align: right; }
        .inv-col-total { width: 110px; text-align: right; }

        .inv-row {
            display: flex;
            align-items: flex-start;
            padding: 6px 16px;
            min-height: 29px;
            font-size: 11px;
            box-sizing: border-box;
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
        }
        .inv-row-item {
            font-weight: 700;
            color: #0f172a;
        }
        .inv-row-subtext {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 1px;
        }
        .inv-tag-main {
            display: inline-block;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            background-color: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
            padding: 0px 5px;
            border-radius: 4px;
            margin-left: 6px;
            vertical-align: middle;
        }
        .inv-tag-addon {
            display: inline-block;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            background-color: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 0px 5px;
            border-radius: 4px;
            margin-left: 6px;
            vertical-align: middle;
        }

        /* Capsules (DP & Total) */
        .inv-capsule-list {
            display: flex;
            flex-direction: column;
            gap: 7px;
            width: 100%;
            margin-top: 4px;
        }
        .inv-capsule-row {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .inv-capsule-lbl {
            width: 135px;
            height: 31px;
            background-color: var(--inv-primary);
            color: #ffffff;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .inv-capsule-val {
            flex: 1;
            height: 31px;
            background-color: var(--inv-primary);
            color: #ffffff;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 800;
            font-family: monospace;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 20px;
        }
        .inv-capsule-lbl-sisa {
            background-color: var(--inv-secondary);
        }
        .inv-capsule-val-sisa {
            background-color: var(--inv-secondary);
            color: #fde047;
        }

        /* Footer Section */
        .inv-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 8px;
        }
        .inv-bank-title {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--inv-primary);
            margin-bottom: 5px;
        }
        .inv-bank-list {
            font-family: monospace;
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.55;
        }
        .inv-bank-name {
            font-family: 'Instrument Sans', sans-serif;
            font-weight: 900;
            color: var(--inv-primary);
            margin-left: 4px;
        }
        .inv-bank-owner {
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            margin-top: 3px;
        }
        .inv-bank-owner strong {
            color: var(--inv-primary);
            font-weight: 900;
        }

        .inv-cs-block {
            text-align: center;
            width: 170px;
            flex-shrink: 0;
            padding-bottom: 2px;
        }
        .inv-cs-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--inv-primary);
            margin-bottom: 2px;
        }
        .inv-signature-wrap {
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .inv-signature-line {
            width: 135px;
            border-bottom: 2px solid var(--inv-primary);
            margin: 0 auto;
        }
        .inv-cashier-name {
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            margin-top: 3px;
        }

        @media screen and (max-width: 768px) {
            .invoice-sheet {
                width: 100%;
                height: auto;
                min-height: auto;
                max-height: none;
            }
        }
    </style>
</head>
<body class="bg-slate-200 text-[#0f172a] font-sans min-h-screen py-6 px-2 sm:px-4 flex flex-col items-center antialiased">

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

        // Build item list for invoice grid
        $itemsList = collect();
        if ($order->items && $order->items->count() > 0) {
            foreach ($order->items as $idx => $it) {
                $subtext = '';
                $badge = '';
                $isMain = ($idx === 0 && $order->designJob);
                $isAddon = ($idx > 0 && $order->designJob);

                if ($isMain) {
                    $badge = 'Layanan Utama';
                    $parts = [];
                    if ($order->designJob->dimensions) {
                        $parts[] = 'Ukuran: ' . $order->designJob->dimensions;
                    }
                    if ($order->designJob->material_notes) {
                        $parts[] = 'Bahan: ' . $order->designJob->material_notes;
                    }
                    if ((float)($order->designJob->design_fee ?? 0) > 0) {
                        $parts[] = 'Jasa Desain: Rp ' . number_format($order->designJob->design_fee, 0, ',', '.') . ' • Cetak: Rp ' . number_format(max(0, $it->subtotal - $order->designJob->design_fee), 0, ',', '.');
                    }
                    $subtext = implode(' • ', $parts);
                } elseif ($isAddon) {
                    $badge = 'Alat / Tambahan';
                }

                $itemsList->push([
                    'name' => $it->item_name,
                    'qty' => $it->qty,
                    'price' => $it->unit_price,
                    'total' => $it->subtotal,
                    'badge' => $badge,
                    'subtext' => $subtext,
                ]);
            }
        } elseif ($order->designJob) {
            $parts = [];
            if ($order->designJob->dimensions) {
                $parts[] = 'Ukuran: ' . $order->designJob->dimensions;
            }
            if ($order->designJob->material_notes) {
                $parts[] = 'Bahan: ' . $order->designJob->material_notes;
            }
            $itemsList->push([
                'name' => $order->designJob->title,
                'qty' => 1,
                'price' => $order->total_amount,
                'total' => $order->total_amount,
                'badge' => 'Layanan Utama',
                'subtext' => implode(' • ', $parts),
            ]);
        } else {
            $itemsList->push([
                'name' => 'Pesanan Percetakan',
                'qty' => 1,
                'price' => $order->total_amount,
                'total' => $order->total_amount,
                'badge' => '',
                'subtext' => '',
            ]);
        }
    @endphp

    <!-- Screen-Only Minimalist Action Bar -->
    <div class="no-print sticky top-3 z-50 w-full max-w-[210mm] bg-white/95 backdrop-blur-md border border-slate-300 rounded-xl shadow-lg px-4 py-2.5 mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.show', $order->id) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1 transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" />
                <span>Detail Order</span>
            </a>
            <span class="text-slate-300">|</span>
            <span class="text-xs font-semibold text-slate-600">
                Template Resmi: <strong>Gemini Art A4 Corporate</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            @if(!empty($waPhone))
            <a href="https://wa.me/{{ $waPhone }}?text={{ $waMessage }}" 
               target="_blank"
               class="px-3 py-1.5 rounded-lg border border-emerald-400 bg-emerald-50 hover:bg-emerald-100 font-bold text-xs text-emerald-800 flex items-center gap-1.5 transition-colors">
                <x-icon name="send" class="w-3.5 h-3.5 text-emerald-600" />
                <span>Kirim WhatsApp</span>
            </a>
            @endif

            <button onclick="window.print()" 
                    class="px-3.5 py-1.5 rounded-lg bg-[#0b2559] hover:bg-[#07193d] text-white font-bold text-xs flex items-center gap-1.5 shadow transition-colors cursor-pointer">
                <x-icon name="printer" class="w-3.5 h-3.5 text-white" />
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Official A4 Invoice Sheet with Authentic Letterhead -->
    <div class="invoice-sheet shadow-2xl rounded-sm select-text">
        
        @php
            $bgPath = public_path('images/invoice-bg.jpg');
            $bgSrc = file_exists($bgPath) 
                ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($bgPath)) 
                : asset('images/invoice-bg.jpg');
        @endphp

        <!-- Official Blank Letterhead Background (Image 1 from Client) -->
        <img src="{{ $bgSrc }}" 
             alt="Gemini Art Official Letterhead" 
             class="invoice-bg">

        <!-- Dynamic Content Layer: Overlaid exactly inside the printable white body canvas -->
        <div class="invoice-body">
            
            <div>
                <!-- Top Meta & Bill To Section (Exact Match to Client Reference) -->
                <div class="inv-header-meta">
                    
                    <!-- Left: Bill To -->
                    <div style="flex: 1; min-width: 0;">
                        <div class="inv-bill-to-label">
                            BILL TO
                        </div>
                        <h1 class="inv-customer-name">
                            {{ $order->customer_name }}
                        </h1>
                        <div class="inv-customer-info">
                            <div class="inv-info-row">
                                <span class="inv-info-lbl">Alamat :</span>
                                <span class="inv-info-val">{{ $order->customer_address ?? ($order->branch->address ?? 'Tegal, Jawa Tengah') }}</span>
                            </div>
                            <div class="inv-info-row">
                                <span class="inv-info-lbl">Nomor :</span>
                                <span class="inv-info-val">{{ $order->customer_phone ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Invoice Metadata -->
                    <div class="inv-meta-right">
                        <div class="inv-number-row">
                            Invoice Number : <span class="inv-number-val">{{ $invNumber }}</span>
                        </div>
                        <div class="inv-date-val">
                            {{ $order->created_at->translatedFormat('d F Y') }}
                        </div>
                        <div>
                            @if($order->payment_status === 'paid')
                                <span class="inv-status-pill status-paid">LUNAS / FULL PAID</span>
                            @elseif($order->payment_status === 'partial_dp')
                                <span class="inv-status-pill status-dp">DP DITERIMA</span>
                            @else
                                <span class="inv-status-pill status-unpaid">BELUM BAYAR / UNPAID</span>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Main Items Table (Client's Pill Rounded Capsule Header + 5-Row Structured Grid) -->
                <div class="inv-table-wrap">
                    <!-- True Rounded Capsule Header Pill -->
                    <div class="inv-table-head">
                        <div class="inv-col-no">No</div>
                        <div class="inv-col-desc">Deskripsi</div>
                        <div class="inv-col-qty">Qty</div>
                        <div class="inv-col-price">Harga</div>
                        <div class="inv-col-total">Total</div>
                    </div>

                    <!-- Fixed 5-Row Grid (Matches Client Reference with Clean Spacing) -->
                    <div style="margin-top: 2px;">
                        @php
                            $totalRows = max(5, $itemsList->count());
                        @endphp
                        @for($i = 0; $i < $totalRows; $i++)
                            @php $item = $itemsList->get($i); @endphp
                            <div class="inv-row">
                                <div class="inv-col-no" style="font-weight: 600; color: #64748b;">
                                    {{ $i + 1 }}
                                </div>
                                <div class="inv-col-desc">
                                    @if($item)
                                        <div>
                                            <span class="inv-row-item">{{ $item['name'] }}</span>
                                            @if($item['badge'] === 'Layanan Utama')
                                                <span class="inv-tag-main">Layanan Utama</span>
                                            @elseif($item['badge'] === 'Alat / Tambahan')
                                                <span class="inv-tag-addon">Alat / Tambahan</span>
                                            @endif
                                        </div>
                                        @if(!empty($item['subtext']))
                                            <div class="inv-row-subtext">{{ $item['subtext'] }}</div>
                                        @endif
                                    @endif
                                </div>
                                <div class="inv-col-qty" style="font-weight: 700; color: #0f172a;">
                                    {{ $item ? $item['qty'] : '' }}
                                </div>
                                <div class="inv-col-price" style="font-weight: 600; color: #334155;">
                                    {{ $item ? 'Rp ' . number_format($item['price'], 0, ',', '.') : '' }}
                                </div>
                                <div class="inv-col-total" style="font-weight: 800; color: var(--inv-primary);">
                                    {{ $item ? 'Rp ' . number_format($item['total'], 0, ',', '.') : '' }}
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- DP & Total Capsule Pills (Exact Style of Client Reference) -->
                <div class="inv-capsule-list">
                    <!-- Row 1: DP -->
                    <div class="inv-capsule-row">
                        <div class="inv-capsule-lbl">
                            DP
                        </div>
                        <div class="inv-capsule-val">
                            Rp {{ number_format($order->paid_amount, 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Row 2: Sisa (Shown if remaining balance exists) -->
                    @if($order->remaining_amount > 0)
                    <div class="inv-capsule-row">
                        <div class="inv-capsule-lbl inv-capsule-lbl-sisa">
                            Sisa Tagihan
                        </div>
                        <div class="inv-capsule-val inv-capsule-val-sisa">
                            Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}
                        </div>
                    </div>
                    @endif

                    <!-- Row 3: Total -->
                    <div class="inv-capsule-row">
                        <div class="inv-capsule-lbl">
                            Total
                        </div>
                        <div class="inv-capsule-val">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer: Payment Accounts (Left) & Customer Service Signature (Right) -->
            <div class="inv-footer">
                
                <!-- Left: Informasi Pembayaran (6 Bank Accounts a.n. Kusmoro) -->
                <div>
                    <div class="inv-bank-title">
                        INFORMASI PEMBAYARAN
                    </div>
                    <div class="inv-bank-list">
                        <div>2158030121 <span class="inv-bank-name">JATENG</span></div>
                        <div>1346499316 <span class="inv-bank-name">BNI</span></div>
                        <div>3600111883 <span class="inv-bank-name">BCA</span></div>
                        <div>7242154961 <span class="inv-bank-name">BSI</span></div>
                        <div>344301033710538 <span class="inv-bank-name">BRI</span></div>
                        <div>1390024837860 <span class="inv-bank-name">MANDIRI</span></div>
                    </div>
                    <div class="inv-bank-owner">
                        Atas Nama : <strong>KUSMORO</strong>
                    </div>
                </div>

                <!-- Right: CS Signature Block -->
                <div class="inv-cs-block">
                    <div class="inv-cs-title">
                        CS
                    </div>
                    <!-- Stylized CS Signature SVG matching template sample -->
                    <div class="inv-signature-wrap">
                        <svg width="128" height="46" viewBox="0 0 160 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M 28 50 C 25 35, 38 18, 52 38 C 62 55, 70 32, 85 24 C 95 18, 106 28, 112 46 C 118 58, 128 42, 138 32" 
                                  stroke="#0b2559" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M 42 42 C 55 52, 72 50, 84 44" 
                                  stroke="#0b2559" stroke-width="2.2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <!-- Signature Underline -->
                    <div class="inv-signature-line"></div>
                    <div class="inv-cashier-name">
                        {{ $order->creator->name ?? 'Kasir Gemini Art' }}
                    </div>
                </div>

            </div>

        </div>

    </div>

</body>
</html>
