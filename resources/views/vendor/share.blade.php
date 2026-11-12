<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>Berkas Cetak #{{ $order->order_number }} - Gemini Art</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = savedTheme === 'dark' || (!savedTheme && systemPrefersDark);
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <style>
        html.dark { background-color: #0b0f19; color: #f1f5f9; color-scheme: dark; }
        html:not(.dark) { background-color: #F8FAFC; color: #0f172a; color-scheme: light; }
        .preload, .preload * { transition: none !important; -webkit-transition: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="preload bg-[#F8FAFC] dark:bg-[#0b0f19] text-slate-900 dark:text-slate-100 font-sans min-h-screen flex flex-col justify-between antialiased selection:bg-amber-300 selection:text-black">

    <!-- Top Simple Bar -->
    <header class="bg-white dark:bg-[#0c1222] border-b-2 border-slate-900 dark:border-slate-800 px-4 py-3 shadow-[0px_2px_0px_0px_#0f172a] dark:shadow-[0px_2px_0px_0px_#000000]">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-200 dark:bg-amber-300 border-2 border-slate-900 dark:border-slate-700 rounded-xl shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] flex items-center justify-center font-black text-sm text-slate-900">
                    GA
                </div>
                <div>
                    <h1 class="text-sm font-black text-slate-900 dark:text-white leading-tight">GEMINI ART</h1>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Portal Produksi Rekanan Percetakan</p>
                </div>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 dark:bg-emerald-950/40 border-[1.5px] border-slate-900 dark:border-emerald-500/50 rounded-full text-xs font-black text-emerald-900 dark:text-emerald-300 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                <span class="w-2 h-2 rounded-full bg-emerald-600 dark:bg-emerald-400 animate-pulse"></span>
                <span>Berkas Siap Cetak</span>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-4xl w-full mx-auto p-4 sm:p-6 space-y-6">

        <!-- Top Banner Order Info -->
        <div class="neo-box p-6 bg-amber-100/70 dark:bg-[#0c1222] border-2 border-slate-900 dark:border-slate-700 rounded-2xl shadow-[4px_4px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#000000]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-[11px] font-black uppercase text-amber-900 dark:text-amber-400 tracking-wider block">
                        Instruksi Cetak Dari Gemini Art &bull; {{ $order->branch->clean_name ?? 'Pusat' }}
                    </span>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white font-mono mt-0.5">
                        {{ $order->order_number }}
                    </h2>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">
                        Pekerjaan: <span class="font-black text-slate-900 dark:text-white">{{ $job->title ?: $order->customer_name }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            onclick="copySpecsToClipboard()" 
                            id="copy-specs-btn"
                            class="neo-btn bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs py-2 px-3 font-bold text-slate-900 dark:text-white dark:border-slate-700 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] flex items-center gap-1.5 cursor-pointer">
                        <x-icon name="copy" class="w-3.5 h-3.5" />
                        <span id="copy-btn-text">Salin Spesifikasi</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Spesifikasi Produksi & Bahan -->
        <div class="neo-box p-5 sm:p-6 bg-white dark:bg-[#0c1222] border-2 border-slate-900 dark:border-slate-700 rounded-2xl shadow-[3px_3px_0px_0px_#0f172a] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
            <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900 dark:border-slate-800">
                <h3 class="font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <x-icon name="printer" class="w-5 h-5 text-slate-800 dark:text-amber-400" />
                    <span>Spesifikasi Produksi & Bahan</span>
                </h3>
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 hidden sm:inline-block">Format: Print-Ready Original</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 text-xs">
                <div class="p-3.5 bg-amber-100/90 dark:bg-amber-950/40 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                    <span class="text-[10px] font-black text-amber-950 dark:text-amber-400 uppercase tracking-wider block">Jumlah Cetak (Qty)</span>
                    <span class="text-base font-black text-amber-950 dark:text-amber-300 font-mono">
                        {{ $order->items->first()?->qty ?? 1 }} Pcs
                    </span>
                </div>

                <div class="p-3.5 bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Ukuran / Dimensi</span>
                    <span class="text-sm font-black text-slate-900 dark:text-white font-mono">
                        {{ $job->clean_dimensions ?: 'Sesuai file asli' }}
                    </span>
                </div>

                <div class="p-3.5 bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Material / Bahan</span>
                    <span class="text-sm font-black text-slate-900 dark:text-white">
                        {{ $job->material_notes ?: 'Standar Percetakan' }}
                    </span>
                </div>

                <div class="p-3.5 {{ $job->deadline_at ? 'bg-rose-50/80 dark:bg-rose-950/30' : 'bg-slate-50 dark:bg-slate-900' }} border-[1.5px] border-slate-900 dark:border-slate-700 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                    <span class="text-[10px] font-bold {{ $job->deadline_at ? 'text-rose-900 dark:text-rose-400' : 'text-slate-500 dark:text-slate-400' }} uppercase tracking-wider block">Deadline Produksi</span>
                    <span class="text-sm font-black {{ $job->deadline_at ? 'text-rose-950 dark:text-rose-300' : 'text-slate-600 dark:text-slate-400' }}">
                        {{ $job->deadline_at ? $job->deadline_at->translatedFormat('d M Y, H:i') : 'Fleksibel' }}
                    </span>
                </div>

                <div class="p-3.5 bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-xl space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] sm:col-span-2 lg:col-span-4">
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Catatan Finishing / Brief</span>
                    <p class="text-xs font-semibold text-slate-900 dark:text-slate-200 leading-relaxed whitespace-pre-line">
                        {{ $job->brief_notes ?: 'Tidak ada catatan khusus.' }}
                    </p>
                </div>
            </div>

            @php
                $extraAccessories = $order->items->slice(1);
            @endphp
            @if($extraAccessories->isNotEmpty())
            <div class="p-3.5 bg-purple-50/90 dark:bg-purple-950/30 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-xl space-y-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                <span class="text-[10px] font-black text-purple-900 dark:text-purple-300 uppercase tracking-wider block">Alat / Perlengkapan Display Terkait Order:</span>
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach($extraAccessories as $ext)
                        <span class="px-2.5 py-1 rounded-lg border-[1.5px] border-slate-900 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-black text-purple-950 dark:text-purple-300 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                            📦 {{ $ext->item_name }} &bull; {{ $ext->qty }} Pcs
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="pt-1 text-slate-500 dark:text-slate-400 text-[11px] font-medium flex items-center gap-2">
                <x-icon name="info" class="w-4 h-4 text-slate-400 dark:text-slate-500 shrink-0" />
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
        <div class="neo-box p-5 sm:p-6 bg-white dark:bg-[#0c1222] border-2 border-slate-900 dark:border-slate-700 rounded-2xl shadow-[3px_3px_0px_0px_#0f172a] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b-2 border-slate-900 dark:border-slate-800 gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-emerald-300 dark:bg-emerald-400 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                        <x-icon name="file-check" class="w-4 h-4 text-slate-900" />
                    </div>
                    <div>
                        <h3 class="font-black text-base text-slate-900 dark:text-white">Berkas Cetak Siap Produksi</h3>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Tersedia {{ $allFinalPrintAssets->count() }} berkas master siap diproses mesin cetak</p>
                    </div>
                </div>

                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium font-mono">
                    Cloud Storage Direct Access
                </div>
            </div>

            <!-- List of All Final Print Files -->
            <div class="space-y-3">
                @foreach($allFinalPrintAssets as $index => $ast)
                <div class="p-4 bg-slate-50 dark:bg-slate-900 border-2 border-slate-900 dark:border-slate-700 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000] hover:bg-amber-50/30 dark:hover:bg-slate-800/50 transition-all">
                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                        <div class="w-12 h-12 rounded-xl bg-amber-200 dark:bg-slate-800 border-2 border-slate-900 dark:border-slate-700 flex items-center justify-center text-slate-900 shrink-0 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                            @if(str_ends_with(strtolower($ast->file_name), '.pdf'))
                                <x-icon name="printer" class="w-6 h-6 text-emerald-800 dark:text-emerald-400" />
                            @elseif($ast->is_image)
                                <x-icon name="eye" class="w-6 h-6 text-sky-800 dark:text-sky-400" />
                            @else
                                <x-icon name="file-text" class="w-6 h-6 text-amber-800 dark:text-amber-400" />
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="px-2 py-0.5 rounded-md border border-slate-900 dark:border-emerald-500/50 bg-emerald-300 dark:bg-emerald-950/40 text-slate-950 dark:text-emerald-300 font-black text-[10px] uppercase shadow-[0.5px_0.5px_0px_0px_#0f172a] dark:shadow-[0.5px_0.5px_0px_0px_#000000]">
                                    Siap Cetak
                                </span>
                                <span class="font-mono text-xs font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-300 dark:border-slate-700">
                                    {{ $ast->formatted_size }}
                                </span>
                            </div>
                            <div class="font-mono font-black text-sm text-slate-900 dark:text-white truncate" title="{{ $ast->file_name }}">
                                {{ $ast->file_name }}
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                        @if($ast->is_image || str_ends_with(strtolower($ast->file_name), '.pdf'))
                        <a href="{{ route('vendor.share.preview', $ast->share_token) }}" 
                           target="_blank" 
                           class="neo-btn bg-sky-200 hover:bg-sky-300 dark:bg-sky-400 dark:hover:bg-sky-300 dark:text-slate-950 text-xs py-2 px-3.5 font-black text-slate-900 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] flex items-center gap-1.5 cursor-pointer dark:border-slate-800">
                            <x-icon name="eye" class="w-3.5 h-3.5" />
                            <span>Pratinjau</span>
                        </a>
                        @endif

                        <a href="{{ route('vendor.share.download', $ast->share_token) }}" 
                           class="neo-btn bg-emerald-400 hover:bg-emerald-300 active:translate-x-[1px] active:translate-y-[1px] text-xs py-2 px-4 font-black text-slate-950 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000] flex items-center gap-1.5 cursor-pointer dark:border-slate-800">
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
    <footer class="bg-white dark:bg-[#0c1222] border-t-2 border-slate-900 dark:border-slate-800 py-4 px-4 text-center text-xs text-slate-500 dark:text-slate-400 font-medium mt-10">
        &copy; {{ date('Y') }} <strong>Gemini Art</strong> &bull; Sistem ERP Percetakan & Studio Desain Multi-Cabang.
    </footer>

    <script>
    function copySpecsToClipboard() {
        let text = `ORDER CETAK: {{ $order->order_number }}\n`;
        text += `Pekerjaan: {{ $job->title ?: $order->customer_name }}\n`;
        text += `Jumlah Cetak: {{ $order->items->first()?->qty ?? 1 }} Pcs\n`;
        text += `Ukuran: {{ $job->clean_dimensions ?: 'Sesuai file asli' }}\n`;
        text += `Bahan: {{ $job->material_notes ?: 'Standar' }}\n`;
        @if($job->deadline_at)
        text += `Deadline: {{ $job->deadline_at->translatedFormat('d M Y, H:i') }} WIB\n`;
        @endif
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

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            document.body.classList.remove('preload');
        });
    });
    </script>
</body>
</html>

