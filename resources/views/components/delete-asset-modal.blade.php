<!-- Delete Asset Confirmation Modal (Soft Neo-Brutalism) -->
<dialog id="delete-asset-modal" closedby="any" class="p-0 bg-transparent max-w-md w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white dark:bg-[#0c1222] w-full mx-auto shadow-[5px_5px_0px_0px_#0f172a] dark:shadow-[5px_5px_0px_0px_#000000] rounded-2xl border-2 border-slate-900 dark:border-slate-700 overflow-visible">
        <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900 dark:border-slate-800 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-rose-300 dark:bg-rose-500/20 border-[1.5px] border-slate-900 dark:border-rose-500/50 flex items-center justify-center text-rose-950 dark:text-rose-300 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                    <x-icon name="trash2" class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900 dark:text-white">Hapus Berkas Arsip?</h3>
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400">File akan dihapus permanen dari penyimpanan</p>
                </div>
            </div>
            <button type="button"
                    onclick="closeDeleteAssetModal()"
                    aria-label="Tutup"
                    class="w-8 h-8 rounded-lg border-2 border-slate-900 dark:border-slate-700 bg-rose-200 hover:bg-rose-300 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 dark:border-rose-800 dark:text-rose-400 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] active:translate-x-[1px] active:translate-y-[1px]">
                ✕
            </button>
        </div>

        <form id="delete-asset-form" method="POST" action="" onsubmit="handleDeleteAssetSubmit(this)" class="space-y-4">
            @csrf
            @method('DELETE')

            <div class="p-3.5 bg-rose-50/80 dark:bg-rose-950/30 border-[1.5px] border-slate-900 dark:border-rose-800/60 rounded-xl space-y-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-900 dark:text-rose-400">Nama Berkas</span>
                    <span id="delete-asset-size" class="font-mono text-[10px] font-bold bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded border border-rose-300 dark:border-slate-700">-</span>
                </div>
                <div id="delete-asset-filename" class="font-mono font-black text-xs text-slate-900 dark:text-white break-all">-</div>
            </div>

            <div id="delete-asset-revert-warning" class="hidden p-3 bg-amber-100/80 dark:bg-amber-950/40 border-[1.5px] border-amber-700 dark:border-amber-700/70 rounded-xl text-xs text-amber-950 dark:text-amber-300 font-bold flex items-start gap-2">
                <x-icon name="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5 text-amber-700 dark:text-amber-400" />
                <span>Ini adalah berkas Cetak Final terakhir. Setelah dihapus, status tiket akan otomatis dikembalikan ke <strong>Proses Desain</strong>.</span>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                Tindakan ini akan menghapus file fisik dari server hosting untuk mengosongkan ruang penyimpanan dan tidak dapat dibatalkan.
            </p>

            <div class="pt-3 border-t-[1.5px] border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                <button type="button"
                        onclick="closeDeleteAssetModal()"
                        class="neo-btn bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 dark:border-slate-700 text-xs py-2 px-4 cursor-pointer font-bold">
                    Batal
                </button>
                <button type="submit"
                        id="delete-asset-submit-btn"
                        class="neo-btn bg-rose-400 hover:bg-rose-500 dark:bg-rose-600 dark:hover:bg-rose-500 text-slate-950 dark:text-white text-xs py-2 px-4 font-black flex items-center gap-1.5 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a] dark:border-slate-700 dark:shadow-[2px_2px_0px_0px_#000000]">
                    <x-icon name="trash2" class="w-3.5 h-3.5" />
                    <span id="delete-asset-submit-text">Ya, Hapus Permanen</span>
                </button>
            </div>
        </form>
    </div>
</dialog>

<script>
function openDeleteAssetModal(data) {
    const d = data || {};
    const modal = document.getElementById('delete-asset-modal');
    const form = document.getElementById('delete-asset-form');
    const nameEl = document.getElementById('delete-asset-filename');
    const sizeEl = document.getElementById('delete-asset-size');
    const warnEl = document.getElementById('delete-asset-revert-warning');
    const btn = document.getElementById('delete-asset-submit-btn');
    const btnText = document.getElementById('delete-asset-submit-text');

    if (form) form.action = d.action || '';
    if (nameEl) nameEl.innerText = d.fileName || '-';
    if (sizeEl) sizeEl.innerText = d.fileSize || '-';
    if (warnEl) warnEl.classList.toggle('hidden', !d.willRevertStatus);
    if (btn) btn.disabled = false;
    if (btnText) btnText.innerText = 'Ya, Hapus Permanen';

    if (modal) modal.showModal();
}

function closeDeleteAssetModal() {
    const modal = document.getElementById('delete-asset-modal');
    if (modal) modal.close();
}

function handleDeleteAssetSubmit(form) {
    const btn = document.getElementById('delete-asset-submit-btn');
    const btnText = document.getElementById('delete-asset-submit-text');
    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-wait');
    }
    if (btnText) {
        btnText.innerText = 'Menghapus...';
    }
}

window.openDeleteAssetModal = openDeleteAssetModal;
window.closeDeleteAssetModal = closeDeleteAssetModal;
</script>

