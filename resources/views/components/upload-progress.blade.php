<div class="upload-progress-container hidden space-y-4 pt-1 pb-2">
    <!-- Header: File info -->
    <div class="flex items-center gap-3 p-3 bg-slate-50 border-2 border-slate-900 rounded-xl shadow-[2px_2px_0px_0px_#0f172a]">
        <div class="w-10 h-10 rounded-xl bg-amber-200 border-[1.5px] border-slate-900 flex items-center justify-center shrink-0 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
            <x-icon name="cloud" class="w-5 h-5 text-slate-900 animate-bounce" />
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
                <span class="upload-progress-filename text-xs font-black text-slate-900 truncate font-mono">nama_file.pdf</span>
                <span class="upload-progress-percent text-xs font-black text-slate-900 font-mono shrink-0">0%</span>
            </div>
            <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium mt-0.5">
                <span class="upload-progress-bytes font-mono">0 KB / 0 KB</span>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-slate-100 border-2 border-slate-900 rounded-xl p-1 shadow-[2px_2px_0px_0px_#0f172a]">
        <div class="upload-progress-bar h-3.5 bg-gradient-to-r from-amber-400 via-emerald-400 to-emerald-500 rounded-lg transition-all duration-150 ease-out border border-slate-900" style="width: 0%"></div>
    </div>

    <!-- Status Message Box -->
    <div class="p-3 bg-amber-50 border-[1.5px] border-slate-900 rounded-xl text-xs space-y-1 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
        <div class="flex items-center gap-2 font-bold text-slate-900 upload-progress-status">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
            <span>Mengunggah file...</span>
        </div>
        <p class="text-[11px] text-slate-500">
            Harap jangan tutup modal atau browser sampai proses selesai.
        </p>
    </div>

    <!-- Error Container (Hidden by default) -->
    <div class="upload-progress-error hidden p-3 bg-rose-50 border-2 border-rose-900 rounded-xl text-xs space-y-2 text-rose-900">
        <div class="flex items-center gap-2 font-black">
            <x-icon name="alert-circle" class="w-4 h-4 text-rose-600" />
            <span>Gagal Mengunggah Berkas</span>
        </div>
        <p class="upload-progress-error-msg text-[11px] text-rose-700"></p>
        <button type="button" class="upload-progress-retry-btn neo-btn bg-white hover:bg-rose-100 text-slate-900 text-xs py-1.5 px-3 font-bold border-rose-900 cursor-pointer">
            Coba Lagi
        </button>
    </div>
</div>
