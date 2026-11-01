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

        <!-- Spesifikasi Produksi & Bahan -->
        <div class="neo-box p-5 sm:p-6 bg-white border-2 border-slate-900 rounded-2xl shadow-[3px_3px_0px_0px_#0f172a] space-y-4">
            <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900">
                <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                    <x-icon name="printer" class="w-5 h-5 text-slate-800" />
                    <span>Spesifikasi Produksi & Bahan</span>
                </h3>
                <span class="text-[11px] font-bold text-slate-500 hidden sm:inline-block">Format: Print-Ready Original</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 text-xs">
                <div class="p-3.5 bg-slate-50 border-[1.5px] border-slate-900 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Ukuran / Dimensi</span>
                    <span class="text-sm font-black text-slate-900 font-mono">
                        {{ $job->dimensions ?: 'Sesuai file asli' }}
                    </span>
                </div>

                <div class="p-3.5 bg-slate-50 border-[1.5px] border-slate-900 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Material / Bahan</span>
                    <span class="text-sm font-black text-slate-900">
                        {{ $job->material_notes ?: 'Standar Percetakan' }}
                    </span>
                </div>

                <div class="p-3.5 bg-amber-50/80 border-[1.5px] border-slate-900 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a] sm:col-span-2 lg:col-span-1">
                    <span class="text-[10px] font-bold text-amber-900 uppercase tracking-wider block">Catatan Finishing / Brief</span>
                    <p class="text-xs font-semibold text-slate-900 leading-relaxed whitespace-pre-line">
                        {{ $job->brief_notes ?: 'Tidak ada catatan khusus.' }}
                    </p>
                </div>
            </div>

            <div class="pt-1 text-slate-500 text-[11px] font-medium flex items-center gap-2">
                <x-icon name="info" class="w-4 h-4 text-slate-400 shrink-0" />
                <span>File cetak telah disiapkan oleh tim desainer Gemini Art dalam format siap cetak (resolusi asli tanpa kompresi).</span>
            </div>
        </div>

        <!-- Berkas Cetak Siap Produksi (Daftar File Bersih & Rapi) -->
        @php
            $allFinalPrintAssets = $job->assets->where('file_type', 'final_print');
            if ($allFinalPrintAssets->isEmpty()) {
                $allFinalPrintAssets = collect([$asset]);
            }
        @endphp
        <div class="neo-box p-5 sm:p-6 bg-white border-2 border-slate-900 rounded-2xl shadow-[3px_3px_0px_0px_#0f172a] space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b-2 border-slate-900 gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-emerald-300 border-[1.5px] border-slate-900 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                        <x-icon name="file-check" class="w-4 h-4 text-slate-900" />
                    </div>
                    <div>
                        <h3 class="font-black text-base text-slate-900">Berkas Cetak Siap Produksi</h3>
                        <p class="text-[11px] font-bold text-slate-500">Tersedia {{ $allFinalPrintAssets->count() }} berkas master siap diproses mesin cetak</p>
                    </div>
                </div>

                <div class="text-[11px] text-slate-500 font-medium font-mono">
                    Cloud Storage Direct Access
                </div>
            </div>

            <!-- List of All Final Print Files -->
            <div class="space-y-3">
                @foreach($allFinalPrintAssets as $index => $ast)
                <div class="p-4 bg-slate-50 border-2 border-slate-900 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-[2px_2px_0px_0px_#0f172a] hover:bg-amber-50/30 transition-all">
                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                        <div class="w-12 h-12 rounded-xl bg-amber-200 border-2 border-slate-900 flex items-center justify-center text-slate-900 shrink-0 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                            @if(str_ends_with(strtolower($ast->file_name), '.pdf'))
                                <x-icon name="printer" class="w-6 h-6 text-emerald-800" />
                            @elseif($ast->is_image)
                                <x-icon name="eye" class="w-6 h-6 text-sky-800" />
                            @else
                                <x-icon name="file-text" class="w-6 h-6 text-amber-800" />
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="px-2 py-0.5 rounded-md border border-slate-900 bg-emerald-300 text-slate-950 font-black text-[10px] uppercase shadow-[0.5px_0.5px_0px_0px_#0f172a]">
                                    Siap Cetak
                                </span>
                                <span class="font-mono text-xs font-bold text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-300">
                                    {{ $ast->formatted_size }}
                                </span>
                            </div>
                            <div class="font-mono font-black text-sm text-slate-900 truncate" title="{{ $ast->file_name }}">
                                {{ $ast->file_name }}
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                        @if($ast->is_image || str_ends_with(strtolower($ast->file_name), '.pdf'))
                        <a href="{{ route('vendor.share.preview', $ast->share_token) }}" 
                           target="_blank" 
                           class="neo-btn bg-sky-200 hover:bg-sky-300 text-xs py-2 px-3.5 font-black text-slate-900 shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center gap-1.5 cursor-pointer">
                            <x-icon name="eye" class="w-3.5 h-3.5" />
                            <span>Pratinjau</span>
                        </a>
                        @endif

                        <a href="{{ route('vendor.share.download', $ast->share_token) }}" 
                           class="neo-btn bg-emerald-400 hover:bg-emerald-300 active:translate-x-[1px] active:translate-y-[1px] text-xs py-2 px-4 font-black text-slate-950 shadow-[2px_2px_0px_0px_#0f172a] flex items-center gap-1.5 cursor-pointer">
                            <x-icon name="download" class="w-3.5 h-3.5 stroke-[2.5]" />
                            <span>Download Berkas</span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t-2 border-slate-900 py-4 px-4 text-center text-xs text-slate-500 font-medium mt-10">
        &copy; {{ date('Y') }} <strong>Gemini Art</strong> &bull; Sistem ERP Percetakan & Studio Desain Multi-Cabang.
    </footer>

    <script>
    function copySpecsToClipboard() {
        let text = `ORDER CETAK: {{ $order->order_number }}\n`;
        text += `Pekerjaan: {{ $job->title ?: $order->customer_name }}\n`;
        text += `Ukuran: {{ $job->dimensions ?: 'Sesuai file asli' }}\n`;
        text += `Bahan: {{ $job->material_notes ?: 'Standar' }}\n`;
        @if(!empty($job->brief_notes))
        text += `Catatan: {{ $job->brief_notes }}\n`;
        @endif
        text += `\nDaftar Berkas Siap Cetak:\n`;
        @foreach($allFinalPrintAssets as $i => $ast)
        text += `{{ $i + 1 }}. {{ $ast->file_name }} ({{ $ast->formatted_size }})\nDownload: {{ route('vendor.share.download', $ast->share_token) }}\n`;
        @endforeach

        navigator.clipboard.writeText(text.trim()).then(() => {
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

