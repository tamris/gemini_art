<!-- Vendor Dispatch Modal (Soft Neo-Brutalism) -->
<dialog id="vendor-dispatch-modal" closedby="any" class="p-0 bg-transparent max-w-lg w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white dark:bg-[#0c1222] w-full mx-auto shadow-[5px_5px_0px_0px_#0f172a] dark:shadow-[5px_5px_0px_0px_#000000] rounded-2xl border-2 border-slate-900 dark:border-slate-700 overflow-visible">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900 dark:border-slate-800 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-300 dark:bg-emerald-400 border-[1.5px] border-slate-900 dark:border-slate-700 flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                    <x-icon name="send" class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900 dark:text-white">Kirim ke Rekanan Percetakan</h3>
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 font-mono" id="dispatch-modal-subtitle">ORD-...</p>
                </div>
            </div>
            <button type="button" 
                    onclick="closeVendorDispatchModal()"
                    aria-label="Tutup" 
                    class="w-8 h-8 rounded-lg border-2 border-slate-900 dark:border-slate-700 bg-rose-200 hover:bg-rose-300 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 dark:border-rose-800 dark:text-rose-400 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] active:translate-x-[1px] active:translate-y-[1px]">
                ✕
            </button>
        </div>

        <!-- File & Spec Summary Box -->
        <div class="p-3 bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-xl text-xs space-y-1 mb-4 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
            <div class="flex items-center justify-between">
                <span class="text-slate-500 dark:text-slate-400 font-medium">Berkas Siap Cetak:</span>
                <span class="font-mono font-black text-slate-900 dark:text-white truncate max-w-[240px]" id="dispatch-file-name">-</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500 dark:text-slate-400 font-medium">Ukuran & Bahan:</span>
                <span class="font-bold text-slate-800 dark:text-slate-200" id="dispatch-file-specs">-</span>
            </div>
            <div id="dispatch-files-list-box" class="hidden pt-2 border-t border-slate-200 dark:border-slate-800 mt-2 space-y-1 max-h-32 overflow-y-auto"></div>
        </div>

        <!-- Mode Navigation Tabs -->
        <div class="flex border-2 border-slate-900 dark:border-slate-700 rounded-xl overflow-hidden mb-4 p-1 bg-slate-100 dark:bg-slate-900 gap-1 text-xs font-black shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
            <button type="button" 
                    id="tab-btn-wa" 
                    onclick="switchDispatchTab('wa')" 
                    class="flex-1 py-1.5 px-2 rounded-lg bg-emerald-400 text-slate-950 border border-slate-900 dark:border-slate-800 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] flex items-center justify-center gap-1.5 cursor-pointer transition-all">
                <x-icon name="phone" class="w-3.5 h-3.5" />
                <span>WhatsApp</span>
            </button>
            <button type="button" 
                    id="tab-btn-email" 
                    onclick="switchDispatchTab('email')" 
                    class="flex-1 py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center justify-center gap-1.5 cursor-pointer transition-all">
                <x-icon name="mail" class="w-3.5 h-3.5" />
                <span>Email Vendor</span>
            </button>
            <button type="button" 
                    id="tab-btn-link" 
                    onclick="switchDispatchTab('link')" 
                    class="flex-1 py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center justify-center gap-1.5 cursor-pointer transition-all">
                <x-icon name="share2" class="w-3.5 h-3.5" />
                <span>Salin Link</span>
            </button>
        </div>

        <!-- TAB 1: WhatsApp Dispatch -->
        <div id="dispatch-tab-wa" class="space-y-4">
            <div>
                <label class="block text-xs font-black uppercase text-slate-900 dark:text-slate-300 mb-1">
                    Nomor WhatsApp Percetakan Rekanan
                </label>
                <div class="relative">
                    <input type="text" 
                           id="dispatch-wa-phone" 
                           placeholder="Contoh: 08123456789 atau 628123456789" 
                           class="neo-input text-xs font-mono font-bold w-full pl-3 pr-3 py-2.5">
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-1.5 leading-relaxed">
                    Nomor otomatis diformat ke standar (+62). Spesifikasi ukuran, bahan, dan link download resolusi penuh otomatis terlampir saat chat WhatsApp terbuka.
                </p>
            </div>

            <div class="pt-3 border-t-[1.5px] border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                <button type="button" 
                        onclick="closeVendorDispatchModal()" 
                        class="neo-btn bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:border-slate-700 text-xs py-2 px-4 cursor-pointer font-bold">
                    Batal
                </button>
                <button type="button" 
                        onclick="sendDispatchWhatsApp()" 
                        class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-5 font-black text-slate-950 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000] flex items-center gap-2 cursor-pointer">
                    <x-icon name="send" class="w-3.5 h-3.5" />
                    <span>Buka & Kirim WhatsApp</span>
                </button>
            </div>
        </div>

        <!-- TAB 2: Email Dispatch -->
        <div id="dispatch-tab-email" class="hidden space-y-4">
            <form id="dispatch-email-form" method="POST" action="" onsubmit="handleDispatchEmailSubmit(event)" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-black uppercase text-slate-900 dark:text-slate-300 mb-1">
                        Alamat Email Percetakan Rekanan
                    </label>
                    <input type="email" 
                           name="vendor_email" 
                           id="dispatch-vendor-email" 
                           required 
                           placeholder="vendor@percetakan.com" 
                           class="neo-input text-xs font-bold w-full py-2 px-3">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-slate-900 dark:text-slate-300 mb-1">
                        Catatan Tambahan untuk Operator (Opsional)
                    </label>
                    <textarea name="notes" 
                              id="dispatch-email-notes" 
                              rows="2" 
                              placeholder="Misal: Mohon warna merahnya dipekatkan sedikit ya mas..." 
                              class="neo-input text-xs font-medium w-full py-2 px-3 resize-none"></textarea>
                </div>

                <!-- Alert state for email result -->
                <div id="dispatch-email-alert" class="hidden p-3 rounded-xl border-2 text-xs font-bold"></div>

                <div class="pt-3 border-t-[1.5px] border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" 
                            onclick="closeVendorDispatchModal()" 
                            class="neo-btn bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:border-slate-700 text-xs py-2 px-4 cursor-pointer font-bold">
                        Batal
                    </button>
                    <button type="submit" 
                            id="dispatch-email-submit-btn"
                            class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-5 font-black text-slate-950 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000] flex items-center gap-2 cursor-pointer">
                        <x-icon name="send" class="w-3.5 h-3.5" />
                        <span id="dispatch-email-submit-text">Kirim Email</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 3: Share Direct Public Link -->
        <div id="dispatch-tab-link" class="hidden space-y-4">
            <div>
                <label class="block text-xs font-black uppercase text-slate-900 dark:text-slate-300 mb-1.5">
                    Link Portal Berkas Siap Cetak (Publik & Tanpa Login)
                </label>

                <div class="flex items-center gap-2">
                    <input type="text" 
                           id="dispatch-public-url" 
                           readonly 
                           class="neo-input text-xs font-mono font-bold w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-900 select-all"
                           placeholder="https://...">
                    <button type="button" 
                            onclick="copyPublicUrlToClipboard()" 
                            id="btn-copy-public-url"
                            class="neo-btn bg-amber-300 hover:bg-amber-400 dark:bg-amber-400 dark:hover:bg-amber-300 text-xs py-2.5 px-5 font-black text-slate-950 shrink-0 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] cursor-pointer flex items-center gap-1.5">
                        <x-icon name="copy" class="w-3.5 h-3.5" />
                        <span id="btn-copy-public-url-text">Salin Link</span>
                    </button>
                </div>

             
            </div>

            <div class="pt-3 border-t-[1.5px] border-slate-200 dark:border-slate-800 flex items-center justify-end">
                <button type="button" 
                        onclick="closeVendorDispatchModal()" 
                        class="neo-btn bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs py-2 px-5 cursor-pointer font-bold text-slate-700 dark:text-slate-300 dark:border-slate-700">
                    Tutup
                </button>
            </div>
        </div>

    </div>
</dialog>

<script>
let currentDispatchData = {};

function openVendorDispatchModal(data) {
    currentDispatchData = data || {};
    
    // Fill header & info
    document.getElementById('dispatch-modal-subtitle').innerText = data.orderNumber || '-';
    document.getElementById('dispatch-file-specs').innerText = (data.dimensions ? data.dimensions + ' • ' : '') + (data.materialNotes || 'Standar');

    const filesListBox = document.getElementById('dispatch-files-list-box');
    const form = document.getElementById('dispatch-email-form');

    // Always set the single public portal URL:
    const portalUrl = data.shareUrl || (data.files && data.files.length > 0 ? data.files[0].shareUrl : '');
    const publicUrlInput = document.getElementById('dispatch-public-url');
    if (publicUrlInput) {
        publicUrlInput.value = portalUrl;
    }

    if (data.isAll && data.files && data.files.length > 0) {
        document.getElementById('dispatch-file-name').innerText = `Semua Berkas (${data.files.length} File)`;

        if (filesListBox) {
            let listHtml = '';
            data.files.forEach((f, i) => {
                listHtml += `
                    <div class="flex items-center justify-between text-[11px] font-mono py-1 border-b border-slate-200 dark:border-slate-800 last:border-0">
                        <span class="truncate max-w-[240px] text-slate-800 dark:text-slate-200 font-bold" title="${f.name}">${i + 1}. ${f.name}</span>
                        <span class="text-slate-500 shrink-0 text-[10px] ml-2">${f.size}</span>
                    </div>
                `;
            });
            filesListBox.innerHTML = listHtml;
            filesListBox.classList.remove('hidden');
        }

        if (form && data.jobId) {
            form.action = '/vendor/dispatch-all/' + data.jobId + '/email';
        }
    } else {
        document.getElementById('dispatch-file-name').innerText = data.fileName || '-';

        if (filesListBox) {
            filesListBox.innerHTML = '';
            filesListBox.classList.add('hidden');
        }

        if (form && data.assetId) {
            form.action = '/vendor/dispatch/' + data.assetId + '/email';
        }
    }

    // Always reset form fields to clean state
    resetDispatchModalFields();

    // Default to WA tab
    switchDispatchTab('wa');

    // Show modal
    const modal = document.getElementById('vendor-dispatch-modal');
    if (modal) modal.showModal();
}

function resetDispatchModalFields() {
    const phone = document.getElementById('dispatch-wa-phone');
    if (phone) phone.value = '';

    const email = document.getElementById('dispatch-vendor-email');
    if (email) email.value = '';

    const notes = document.getElementById('dispatch-email-notes');
    if (notes) notes.value = '';

    const alertBox = document.getElementById('dispatch-email-alert');
    if (alertBox) {
        alertBox.className = 'hidden';
        alertBox.innerText = '';
    }
}

function closeVendorDispatchModal() {
    const modal = document.getElementById('vendor-dispatch-modal');
    if (modal) modal.close();
    resetDispatchModalFields();
}

function switchDispatchTab(tab) {
    const tabs = ['wa', 'email', 'link'];
    tabs.forEach(t => {
        const el = document.getElementById('dispatch-tab-' + t);
        const btn = document.getElementById('tab-btn-' + t);
        if (t === tab) {
            el.classList.remove('hidden');
            btn.className = 'flex-1 py-1.5 px-2 rounded-lg bg-emerald-400 text-slate-950 border border-slate-900 shadow-[1px_1px_0px_0px_#0f172a] flex items-center justify-center gap-1.5 cursor-pointer transition-all font-black';
        } else {
            el.classList.add('hidden');
            btn.className = 'flex-1 py-1.5 px-2 rounded-lg text-slate-600 hover:text-slate-900 flex items-center justify-center gap-1.5 cursor-pointer transition-all font-bold';
        }
    });
}

function generateWaText() {
    const d = currentDispatchData;
    let msg = `*ORDER CETAK GEMINI ART*\n`;
    msg += `No. Tiket: ${d.orderNumber || '-'}\n`;
    if (d.jobTitle) msg += `Pekerjaan: ${d.jobTitle}\n`;
    if (d.qty) msg += `Jumlah Cetak: ${d.qty} Pcs\n`;
    if (d.dimensions) msg += `Ukuran: ${d.dimensions}\n`;
    if (d.materialNotes) msg += `Bahan & Finishing: ${d.materialNotes}\n`;
    if (d.briefNotes) msg += `Catatan: ${d.briefNotes}\n`;

    const portalUrl = d.shareUrl || (d.files && d.files.length > 0 ? d.files[0].shareUrl : '');

    if (d.isAll && d.files && d.files.length > 0) {
        msg += `\n*Daftar Berkas Siap Cetak (${d.files.length} File):*\n`;
        d.files.forEach((f, i) => {
            msg += `${i + 1}. ${f.name} (${f.size})\n`;
        });
        msg += `\n*Link Portal Cetak (Download Resolusi Penuh):*\n${portalUrl}\n`;
    } else {
        msg += `\n*Nama Berkas Siap Cetak:*\n${d.fileName || '-'}\n`;
        msg += `\n*Link Download Berkas (Resolusi Penuh):*\n${portalUrl}\n`;
    }

    msg += `\n_Link portal di atas dapat langsung dibuka tanpa harus login. Terima kasih!_`;
    return msg;
}

function sendDispatchWhatsApp() {
    const phoneInput = document.getElementById('dispatch-wa-phone');
    let phone = phoneInput ? phoneInput.value.trim() : '';

    if (phone) {
        // Format to standard: replace 08 with 628, strip non-digits
        phone = phone.replace(/[^0-9]/g, '');
        if (phone.startsWith('0')) {
            phone = '62' + phone.substring(1);
        }
    }

    const text = encodeURIComponent(generateWaText());
    const waUrl = phone ? `https://api.whatsapp.com/send?phone=${phone}&text=${text}` : `https://api.whatsapp.com/send?text=${text}`;
    window.open(waUrl, '_blank');

    // Immediately clear field and close modal
    closeVendorDispatchModal();
}

function copyPublicUrlToClipboard() {
    const input = document.getElementById('dispatch-public-url');
    if (!input || !input.value) return;
    navigator.clipboard.writeText(input.value).then(() => {
        const textSpan = document.getElementById('btn-copy-public-url-text');
        const orig = textSpan ? textSpan.innerText : 'Salin Link';
        if (textSpan) textSpan.innerText = 'Tersalin! ✓';
        setTimeout(() => {
            if (textSpan) textSpan.innerText = orig;
        }, 2000);
    });
}

function handleDispatchEmailSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const emailInput = document.getElementById('dispatch-vendor-email');
    const notesInput = document.getElementById('dispatch-email-notes');
    const submitBtn = document.getElementById('dispatch-email-submit-btn');
    const submitText = document.getElementById('dispatch-email-submit-text');
    const alertBox = document.getElementById('dispatch-email-alert');

    if (!emailInput || !emailInput.value) return;

    submitBtn.disabled = true;
    submitText.innerText = 'Mengirim email...';
    alertBox.className = 'hidden';

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || form.querySelector('input[name="_token"]')?.value
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        submitText.innerText = 'Kirim Email ke Percetakan';

        if (data.success) {
            alertBox.className = 'p-3 rounded-xl border-2 border-emerald-900 bg-emerald-100 text-emerald-950 text-xs font-bold block';
            alertBox.innerText = '✓ ' + data.message;

            // Clear input fields immediately
            if (emailInput) emailInput.value = '';
            if (notesInput) notesInput.value = '';

            setTimeout(() => {
                closeVendorDispatchModal();
                if (document.getElementById('vendor-email-status-header')) {
                    window.location.reload();
                }
            }, 1200);
        } else {
            alertBox.className = 'p-3 rounded-xl border-2 border-rose-900 bg-rose-100 text-rose-950 text-xs font-bold block';
            alertBox.innerText = '✕ ' + (data.message || 'Gagal mengirim email.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitText.innerText = 'Kirim Email ke Percetakan';
        alertBox.className = 'p-3 rounded-xl border-2 border-rose-900 bg-rose-100 text-rose-950 text-xs font-bold block';
        alertBox.innerText = '✕ Terjadi kendala saat mengirim email.';
    });
}

window.openVendorDispatchModal = openVendorDispatchModal;
window.closeVendorDispatchModal = closeVendorDispatchModal;
</script>

