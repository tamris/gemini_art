<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk / SPK {{ $order->order_number }} - Gemini Art</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .receipt-box { box-shadow: none !important; border: 1px solid #000 !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-black font-sans min-h-screen p-4 flex flex-col items-center justify-center">

    <div class="no-print mb-4 flex items-center gap-3">
        <button onclick="window.print()" class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-4 font-black flex items-center gap-2 cursor-pointer">
            <x-icon name="printer" class="w-4 h-4" />
            <span>Cetak Struk / Simpan PDF</span>
        </button>
        <button onclick="window.close()" class="neo-btn bg-slate-200 hover:bg-slate-300 text-xs py-2 px-4 flex items-center gap-1.5 cursor-pointer">
            <x-icon name="x" class="w-3.5 h-3.5" />
            <span>Tutup Tab</span>
        </button>
    </div>

    <!-- Receipt Container -->
    <div class="receipt-box max-w-md w-full bg-white border-2 border-black rounded-2xl p-6 shadow-[6px_6px_0px_0px_#000] text-xs">
        
        <!-- Header -->
        <div class="text-center pb-4 border-b-2 border-black border-dashed">
            <h1 class="text-xl font-black tracking-tight text-black">GEMINI ART</h1>
            <p class="font-bold text-[11px] text-slate-700">Percetakan & Studio Desain</p>
            <p class="text-[10px] text-slate-500 mt-1 font-medium">{{ $order->branch->name }}</p>
            <p class="text-[10px] text-slate-500">{{ $order->branch->address }} &bull; Telp: {{ $order->branch->phone ?? '-' }}</p>
        </div>

        <!-- Meta -->
        <div class="py-3 border-b-2 border-black border-dashed space-y-1">
            <div class="flex justify-between">
                <span class="text-slate-500 font-medium">No. Order:</span>
                <span class="font-mono font-black">{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Tanggal:</span>
                <span class="font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Kasir:</span>
                <span class="font-semibold">{{ $order->creator->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Customer:</span>
                <span class="font-black">{{ $order->customer_name }}</span>
            </div>
            @if($order->customer_phone)
            <div class="flex justify-between">
                <span class="text-slate-500 font-medium">WhatsApp:</span>
                <span class="font-semibold">{{ $order->customer_phone }}</span>
            </div>
            @endif
        </div>

        <!-- Order Content -->
        <div class="py-3 border-b-2 border-black border-dashed space-y-2">
            @if($order->order_type === 'design_printing' && $order->designJob)
                <div>
                    <span class="font-black text-sm block mb-1">{{ $order->designJob->title }}</span>
                    <div class="text-[11px] text-slate-600 space-y-0.5">
                        @if($order->designJob->dimensions)
                            <div>&bull; Ukuran: <strong>{{ $order->designJob->dimensions }}</strong></div>
                        @endif
                        @if($order->designJob->material_notes)
                            <div>&bull; Bahan: <strong>{{ $order->designJob->material_notes }}</strong></div>
                        @endif
                        @if($order->designJob->deadline_at)
                            <div>&bull; Deadline: <strong>{{ $order->designJob->deadline_at->format('d/m/Y H:i') }}</strong></div>
                        @endif
                    </div>
                </div>
            @else
                <!-- POS Items -->
                <div class="space-y-1.5">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="font-bold">{{ $item->item_name }}</span>
                            <div class="text-[10px] text-slate-500">{{ $item->qty }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                        </div>
                        <span class="font-black">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Payment Calculations -->
        <div class="py-3 border-b-2 border-black border-dashed space-y-1.5">
            <div class="flex justify-between text-sm">
                <span class="font-black">TOTAL BIAYA:</span>
                <span class="font-black">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-600 font-medium">Telah Dibayar:</span>
                <span class="font-bold text-emerald-800">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs pt-1 border-t border-slate-200">
                <span class="font-black text-rose-700">SISA TAGIHAN:</span>
                <span class="font-black text-rose-700">Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-[11px] pt-1">
                <span class="text-slate-500 font-medium">Status Pembayaran:</span>
                <span class="font-black uppercase">{{ $order->payment_status_label }}</span>
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="pt-4 text-center text-[10px] text-slate-500 space-y-1">
            <p class="font-bold text-black">Terima Kasih Atas Kepercayaan Anda!</p>
            <p>Harap bawa struk / bukti ini saat pengambilan barang cetak.</p>
            <p class="font-mono text-[9px] pt-1">Sistem Otomasi Gemini Art Multi-Cabang</p>
        </div>

    </div>

</body>
</html>
