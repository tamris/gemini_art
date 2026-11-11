@extends('layouts.app')

@section('title', 'Mode Maintenance Server')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-[1.5px] border-slate-800 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full {{ $isMaintenanceActive ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                <span class="text-xs font-black uppercase tracking-wider {{ $isMaintenanceActive ? 'text-amber-800 dark:text-amber-400' : 'text-emerald-800 dark:text-emerald-400' }}">
                    Kontrol Sistem &amp; Server
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-950 dark:text-white">Mode Maintenance Aplikasi</h1>
        </div>

        <a href="{{ route('maintenance.preview') }}"
           target="_blank"
           rel="noopener noreferrer"
           class="neo-btn bg-white hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs py-2.5 px-4 font-black text-slate-900 dark:text-white border-[1.5px] border-slate-800 dark:border-slate-500 flex items-center justify-center gap-2 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#334155]">
            <x-icon name="eye" class="w-4 h-4" />
            <span>Pratinjau Halaman 503</span>
            <x-icon name="external-link" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
        </a>
    </div>

    <!-- Live Status Card -->
    @if($isMaintenanceActive)
    <div class="neo-box p-5 sm:p-6 bg-amber-100/90 dark:bg-amber-500/10 border-[1.5px] border-slate-900 dark:border-amber-500/60 shadow-[3px_3px_0px_0px_#0f172a] dark:shadow-[3.5px_3.5px_0px_0px_#334155] space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-amber-300 border-[1.5px] border-slate-900 shadow-[2px_2px_0px_0px_#0f172a] flex items-center justify-center text-slate-950 shrink-0">
                    <x-icon name="wrench" class="w-6 h-6" stroke-width="2.5" />
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-300 text-slate-950 border border-slate-900 text-[10px] font-black uppercase tracking-wider">
                            Maintenance Aktif • HTTP 503
                        </span>
                        @if(!empty($meta['activated_at']))
                        <span class="text-xs font-bold text-slate-700 dark:text-amber-300">
                            Sejak {{ $meta['activated_at'] }}
                        </span>
                        @endif
                    </div>
                    <h2 class="text-lg font-black text-slate-950 dark:text-white mt-1.5">
                        Pengunjung &amp; Staf Sedang Melihat Halaman Maintenance
                    </h2>
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-1">
                        Hanya browser dengan akses bypass rahasia yang bisa masuk ke sistem selama pemeliharaan berjalan.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('maintenance.up') }}" class="shrink-0">
                @csrf
                <button type="submit"
                        class="w-full lg:w-auto neo-btn bg-emerald-400 hover:bg-emerald-500 text-slate-950 font-black text-xs sm:text-sm py-3 px-5 border-[1.5px] border-slate-900 shadow-[2.5px_2.5px_0px_0px_#0f172a] flex items-center justify-center gap-2 cursor-pointer">
                    <x-icon name="check-circle-2" class="w-4 h-4" stroke-width="2.5" />
                    <span>Matikan Maintenance (Online)</span>
                </button>
            </form>
        </div>

        @if($bypassUrl)
        <div class="p-4 bg-white dark:bg-[#0c1222] border-[1.5px] border-slate-900 dark:border-slate-600 rounded-xl space-y-2">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                    <x-icon name="key" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                    Link Bypass Rahasia Owner (Perangkat Lain / HP)
                </span>
                <span id="copy-bypass-feedback" class="hidden text-[11px] font-black text-emerald-700 dark:text-emerald-400">
                    Tersalin!
                </span>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text"
                       id="bypass-url-input"
                       readonly
                       value="{{ $bypassUrl }}"
                       class="flex-1 px-3 py-2 rounded-lg border-[1.5px] border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 font-mono text-xs font-bold text-slate-900 dark:text-amber-300 select-all outline-none">
                <button type="button"
                        onclick="copyMaintenanceText('bypass-url-input', 'copy-bypass-feedback')"
                        class="neo-btn bg-amber-300 hover:bg-amber-400 text-slate-950 font-black text-xs px-4 py-2 border-[1.5px] border-slate-900 shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
                    <x-icon name="copy" class="w-3.5 h-3.5" />
                    <span>Salin Link</span>
                </button>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="neo-box p-5 bg-emerald-50/80 dark:bg-emerald-500/10 border-[1.5px] border-slate-900 dark:border-emerald-500/50 shadow-[3px_3px_0px_0px_#0f172a] dark:shadow-[3.5px_3.5px_0px_0px_#334155] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-300 border-[1.5px] border-slate-900 shadow-[2px_2px_0px_0px_#0f172a] flex items-center justify-center text-slate-950 shrink-0">
                <x-icon name="shield-check" class="w-6 h-6" stroke-width="2.5" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-300 text-slate-950 border border-slate-900 text-[10px] font-black uppercase tracking-wider">
                        Sistem Online • Normal
                    </span>
                </div>
                <p class="text-sm font-black text-slate-900 dark:text-white mt-1">
                    Aplikasi beroperasi normal untuk seluruh kasir, desainer, &amp; portal vendor.
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Konfigurasi Maintenance -->
        <div class="lg:col-span-2 neo-box p-5 sm:p-6 bg-white dark:bg-[#0c1222]">
            <div class="pb-3.5 mb-5 border-b-[1.5px] border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-950 dark:text-white">
                        {{ $isMaintenanceActive ? 'Perbarui Pesan & Pengaturan 503' : 'Aktifkan Mode Maintenance' }}
                    </h2>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                        Menggunakan sistem resmi Laravel (`503 Service Unavailable`) dengan tampilan pra-render anti-error saat update server.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('maintenance.down') }}" class="space-y-4">
                @csrf

                <!-- Pesan Maintenance -->
                <div>
                    <label for="maintenance-message" class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-300 mb-1.5">
                        Pesan untuk Staf &amp; Pengunjung
                    </label>
                    <textarea id="maintenance-message"
                              name="message"
                              rows="3"
                              maxlength="500"
                              placeholder="Sistem sedang dalam proses perbaikan &amp; pemeliharaan server. Data transaksi, order desain, dan arsip file Anda tetap aman tersimpan."
                              class="w-full px-3.5 py-2.5 rounded-xl border-[1.5px] border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-slate-900 dark:focus:border-amber-400 outline-none">{{ old('message', $meta['message'] ?? '') }}</textarea>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1">
                        Kosongkan untuk menggunakan pesan default yang ramah &amp; informatif.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Estimasi Menit -->
                    <div>
                        <label for="maintenance-eta" class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-300 mb-1.5">
                            Estimasi Waktu (Menit)
                        </label>
                        <input type="number"
                               id="maintenance-eta"
                               name="eta_minutes"
                               min="1"
                               max="1440"
                               value="{{ old('eta_minutes', $meta['eta_minutes'] ?? '') }}"
                               placeholder="Contoh: 15"
                               class="w-full px-3.5 py-2.5 rounded-xl border-[1.5px] border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-bold text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-slate-900 dark:focus:border-amber-400 outline-none">
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach([10 => '10 mnt', 15 => '15 mnt', 30 => '30 mnt', 60 => '1 jam'] as $mins => $label)
                            <button type="button"
                                    onclick="document.getElementById('maintenance-eta').value = '{{ $mins }}'"
                                    class="px-2 py-0.5 rounded-md border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 hover:bg-amber-200 dark:hover:bg-amber-500/20 text-[11px] font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kode Bypass Secret -->
                    <div>
                        <label for="maintenance-secret" class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-300 mb-1.5">
                            Kode Bypass Rahasia (Owner)
                        </label>
                        <input type="text"
                               id="maintenance-secret"
                               name="secret"
                               minlength="6"
                               maxlength="50"
                               pattern="[A-Za-z0-9_-]+"
                               value="{{ old('secret', $suggestedSecret) }}"
                               placeholder="contoh: gemini-maint-01"
                               class="w-full px-3.5 py-2.5 rounded-xl border-[1.5px] border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 font-mono text-sm font-bold text-slate-900 dark:text-amber-300 placeholder:text-slate-400 focus:border-slate-900 dark:focus:border-amber-400 outline-none">
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1.5">
                            Hanya huruf, angka, <code class="font-mono">-</code> atau <code class="font-mono">_</code> (minimal 6 karakter).
                        </p>
                    </div>
                </div>

                <!-- Safety Info Box -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-900/70 border-[1.5px] border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-start gap-2.5">
                    <x-icon name="info" class="w-4 h-4 text-sky-600 dark:text-sky-400 shrink-0 mt-0.5" />
                    <span>
                        Saat diaktifkan, kasir/desainer &amp; link publik otomatis melihat halaman <strong>503 Maintenance</strong>, sementara sesi browser Owner langsung mendapat izin bypass untuk mengecek hasil perbaikan di server.
                    </span>
                </div>

                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <button type="submit"
                            class="neo-btn bg-amber-300 hover:bg-amber-400 dark:bg-amber-400 dark:hover:bg-amber-300 text-slate-950 font-black text-sm py-3 px-5 border-[1.5px] border-slate-900 shadow-[2.5px_2.5px_0px_0px_#0f172a] dark:shadow-[2.5px_2.5px_0px_0px_#000000] flex items-center gap-2 cursor-pointer">
                        <x-icon name="wrench" class="w-4 h-4" stroke-width="2.5" />
                        <span>{{ $isMaintenanceActive ? 'Perbarui & Render Ulang Halaman 503' : 'Aktifkan Mode Maintenance Sekarang' }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Panduan SSH / Terminal Server -->
        <div class="neo-box p-5 sm:p-6 bg-white dark:bg-[#0c1222] space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b-[1.5px] border-slate-200 dark:border-slate-800">
                <div class="w-8 h-8 rounded-lg bg-slate-900 dark:bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-300 shrink-0">
                    <x-icon name="terminal" class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-950 dark:text-white">Perintah SSH Server</h3>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Alternatif via Terminal</p>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-black text-slate-700 dark:text-slate-300">1. Aktifkan sebelum perbaikan:</span>
                        <span id="copy-cli-down-feedback" class="hidden text-[10px] font-black text-emerald-700 dark:text-emerald-400">Tersalin!</span>
                    </div>
                    <div class="relative">
                        <input id="cli-down-cmd"
                               type="text"
                               readonly
                               value='php artisan down --render="errors::503" --secret="{{ $suggestedSecret }}" --refresh=60'
                               class="w-full pr-14 pl-3 py-2 rounded-lg border-[1.5px] border-slate-300 dark:border-slate-700 bg-slate-900 text-amber-300 font-mono text-[11px] outline-none">
                        <button type="button"
                                onclick="copyMaintenanceText('cli-down-cmd', 'copy-cli-down-feedback')"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-[10px] font-bold text-white cursor-pointer">
                            Salin
                        </button>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-black text-slate-700 dark:text-slate-300">2. Online-kan setelah selesai:</span>
                        <span id="copy-cli-up-feedback" class="hidden text-[10px] font-black text-emerald-700 dark:text-emerald-400">Tersalin!</span>
                    </div>
                    <div class="relative">
                        <input id="cli-up-cmd"
                               type="text"
                               readonly
                               value="php artisan up"
                               class="w-full pr-14 pl-3 py-2 rounded-lg border-[1.5px] border-slate-300 dark:border-slate-700 bg-slate-900 text-emerald-300 font-mono text-[11px] outline-none">
                        <button type="button"
                                onclick="copyMaintenanceText('cli-up-cmd', 'copy-cli-up-feedback')"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-[10px] font-bold text-white cursor-pointer">
                            Salin
                        </button>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/40 text-[11px] font-semibold text-amber-950 dark:text-amber-200 leading-relaxed">
                    Tips: Pakai flag <code class="font-mono font-bold">--render="errors::503"</code> supaya walau Anda sedang menjalankan <code class="font-mono">composer install</code> atau <code class="font-mono">npm run build</code> di server, halaman maintenance tetap tampil aman tanpa error 500.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyMaintenanceText(inputId, feedbackId) {
    const input = document.getElementById(inputId);
    const feedback = document.getElementById(feedbackId);
    if (!input) return;

    const showFeedback = () => {
        if (!feedback) return;
        feedback.classList.remove('hidden');
        setTimeout(() => feedback.classList.add('hidden'), 1800);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value).then(showFeedback).catch(() => {
            input.select();
            document.execCommand('copy');
            showFeedback();
        });
    } else {
        input.select();
        document.execCommand('copy');
        showFeedback();
    }
}
</script>
@endsection

