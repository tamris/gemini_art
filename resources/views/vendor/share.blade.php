<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkas Cetak #{{ $order->order_number }} - Gemini Art</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] text-slate-900 font-sans min-h-screen flex flex-col justify-between antialiased selection:bg-amber-300 selection:text-black">

    <!-- Top Simple Bar -->
    <header class="bg-white border-b-2 border-slate-900 px-4 py-3 shadow-[0px_2px_0px_0px_#0f172a]">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-200 border-2 border-slate-900 rounded-xl shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center justify-center font-black text-sm text-slate-900">
                    GA
                </div>
                <div>
                    <h1 class="text-sm font-black text-slate-900 leading-tight">GEMINI ART</h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Portal Produksi Rekanan Percetakan</p>
                </div>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 border-[1.5px] border-slate-900 rounded-full text-xs font-black text-emerald-900 shadow-[1px_1px_0px_0px_#0f172a]">
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                <span>Berkas Siap Cetak</span>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-4xl w-full mx-auto p-4 sm:p-6 space-y-6">

        <!-- Top Banner Order Info -->
        <div class="neo-box p-6 bg-amber-100/70 border-2 border-slate-900 rounded-2xl shadow-[4px_4px_0px_0px_#0f172a]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-[11px] font-black uppercase text-amber-900 tracking-wider block">
                        Instruksi Cetak Dari Gemini Art &bull; {{ $order->branch->clean_name ?? 'Pusat' }}
                    </span>
                    <h2 class="text-2xl font-black text-slate-900 font-mono mt-0.5">
                        {{ $order->order_number }}
                    </h2>
                    <p class="text-xs font-bold text-slate-700 mt-1">
                        Pekerjaan: <span class="font-black text-slate-900">{{ $job->title ?: $order->customer_name }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            onclick="copySpecsToClipboard()" 
                            id="copy-specs-btn"
                            class="neo-btn bg-white hover:bg-slate-50 text-xs py-2 px-3 font-bold text-slate-900 shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center gap-1.5">
                        <x-icon name="copy" class="w-3.5 h-3.5" />
                        <span id="copy-btn-text">Salin Spesifikasi</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Left: Technical Print Specifications (2 Cols) -->
            <div class="md:col-span-2 space-y-6">
                <div class="neo-box p-6 bg-white border-2 border-slate-900 rounded-2xl shadow-[3px_3px_0px_0px_#0f172a] space-y-4">
                    <h3 class="font-black text-base text-slate-900 flex items-center gap-2 pb-3 border-b-2 border-slate-900">
                        <x-icon name="printer" class="w-5 h-5 text-slate-800" />
                        <span>Spesifikasi Produksi & Bahan</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3.5 bg-slate-50 border-[1.5px] border-slate-900 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Ukuran / Dimensi</span>
                            <span class="text-sm font-black text-slate-900 font-mono">
                                {{ $job->dimensions ?: 'Sesuai file asli' }}
                            </span>
                        </div>

                        <div class="p-3.5 bg-slate-50 border-[1.5px] border-slate-900 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Material / Bahan</span>
                            <span class="text-sm font-black text-slate-900">
                                {{ $job->material_notes ?: 'Standar Percetakan' }}
                            </span>
                        </div>
                    </div>

                    @if(!empty($job->brief_notes))
                    <div class="p-4 bg-amber-50 border-[1.5px] border-slate-900 rounded-xl space-y-1">
                        <span class="text-[11px] font-bold text-amber-900 uppercase tracking-wider block">Catatan Khusus Finishing / Brief</span>
                        <p class="text-xs font-semibold text-amber-950 leading-relaxed whitespace-pre-line">
                            {{ $job->brief_notes }}
                        </p>
                    </div>
                    @endif

                    <div class="pt-2 text-slate-500 text-[11px] font-medium flex items-center gap-2">
                        <x-icon name="info" class="w-4 h-4 text-slate-400 shrink-0" />
                        <span>File cetak telah disiapkan oleh tim desainer Gemini Art dalam format siap cetak (print-ready).</span>
                    </div>
                </div>
            </div>

            <!-- Right: Download Action Card (1 Col) -->
            <div class="space-y-4">
                <div class="neo-box p-6 bg-white border-2 border-slate-900 rounded-2xl shadow-[3px_3px_0px_0px_#0f172a] space-y-5 text-center">
                    
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 border-2 border-slate-900 flex items-center justify-center mx-auto shadow-[2px_2px_0px_0px_#0f172a]">
                        <x-icon name="file-check" class="w-8 h-8 text-emerald-800" />
                    </div>

                    <div class="space-y-1 text-left bg-slate-50 p-3 rounded-xl border border-slate-300">
                        <div class="text-[10px] font-black uppercase text-slate-500">Nama Berkas:</div>
                        <div class="text-xs font-black text-slate-900 font-mono break-all leading-tight">
                            {{ $asset->file_name }}
                        </div>
                        <div class="text-[11px] font-bold text-slate-500 font-mono pt-1">
                            Ukuran: {{ $asset->formatted_size }}
                        </div>
                    </div>

                    <!-- Direct Download Button -->
                    <a href="{{ route('vendor.share.download', $asset->share_token) }}" 
                       class="w-full neo-btn bg-emerald-400 hover:bg-emerald-300 active:translate-x-[2px] active:translate-y-[2px] text-sm py-3 px-4 font-black text-slate-950 shadow-[3px_3px_0px_0px_#0f172a] flex items-center justify-center gap-2 uppercase tracking-wide">
                        <x-icon name="download" class="w-4 h-4 stroke-[2.5]" />
                        <span>Download Berkas</span>
                    </a>

                    <!-- Preview Option if supported -->
                    @if($asset->is_image || str_ends_with(strtolower($asset->file_name), '.pdf'))
                    <a href="{{ route('vendor.share.preview', $asset->share_token) }}" 
                       target="_blank" 
                       class="w-full neo-btn bg-sky-200 hover:bg-sky-300 text-xs py-2 px-3 font-bold text-slate-900 shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center justify-center gap-1.5">
                        <x-icon name="eye" class="w-3.5 h-3.5" />
                        <span>Buka Pratinjau di Tab Baru</span>
                    </a>
                    @endif

                    <div class="text-[10px] text-slate-500 font-medium">
                        Unduhan langsung aman tanpa batas kuota atau masa kedaluwarsa pendek.
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t-2 border-slate-900 py-4 px-4 text-center text-xs text-slate-500 font-medium mt-10">
        &copy; {{ date('Y') }} <strong>Gemini Art</strong> &bull; Sistem ERP Percetakan & Studio Desain Multi-Cabang.
    </footer>

    <script>
    function copySpecsToClipboard() {
        const text = `ORDER CETAK: {{ $order->order_number }}
Pekerjaan: {{ $job->title ?: $order->customer_name }}
Ukuran: {{ $job->dimensions ?: 'Sesuai file' }}
Bahan: {{ $job->material_notes ?: 'Standar' }}
@if(!empty($job->brief_notes))Catatan: {{ $job->brief_notes }}
@endif
Download Berkas: {{ route('vendor.share.download', $asset->share_token) }}`;

        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('copy-btn-text');
            const original = btn.innerText;
            btn.innerText = 'Tersalin!';
            setTimeout(() => {
                btn.innerText = original;
            }, 2000);
        });
    }
    </script>
</body>
</html>

