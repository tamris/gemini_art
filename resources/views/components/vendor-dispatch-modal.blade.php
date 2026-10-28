<!-- Vendor Dispatch Modal (Soft Neo-Brutalism) -->
<dialog id="vendor-dispatch-modal" closedby="any" class="p-0 bg-transparent max-w-lg w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white w-full mx-auto shadow-[5px_5px_0px_0px_#0f172a] rounded-2xl border-2 border-slate-900 overflow-visible">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b-2 border-slate-900 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-300 border-[1.5px] border-slate-900 flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                    <x-icon name="send" class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900">Kirim ke Rekanan Percetakan</h3>
                    <p class="text-[11px] font-bold text-slate-500 font-mono" id="dispatch-modal-subtitle">ORD-...</p>
                </div>
            </div>
            <button type="button" 
                    onclick="closeVendorDispatchModal()"
                    aria-label="Tutup" 
                    class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-rose-200 hover:bg-rose-300 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px]">
                ✕
            </button>
        </div>

        <!-- File & Spec Summary Box -->
        <div class="p-3 bg-slate-50 border-[1.5px] border-slate-900 rounded-xl text-xs space-y-1 mb-4 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
            <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">Berkas Siap Cetak:</span>
                <span class="font-mono font-black text-slate-900 truncate max-w-[240px]" id="dispatch-file-name">-</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">Ukuran & Bahan:</span>
                <span class="font-bold text-slate-800" id="dispatch-file-specs">-</span>
            </div>
        </div>

        <!-- Mode Navigation Tabs -->
        <div class="flex border-2 border-slate-900 rounded-xl overflow-hidden mb-4 p-1 bg-slate-100 gap-1 text-xs font-black shadow-[1.5px_1.5px_0px_0px_#0f172a]">
            <button type="button" 
                    id="tab-btn-wa" 
                    onclick="switchDispatchTab('wa')" 
                    class="flex-1 py-1.5 px-2 rounded-lg bg-emerald-400 text-slate-950 border border-slate-900 shadow-[1px_1px_0px_0px_#0f172a] flex items-center justify-center gap-1.5 cursor-pointer transition-all">
                <x-icon name="phone" class="w-3.5 h-3.5" />
                <span>WhatsApp</span>
            </button>
            <button type="button" 
                    id="tab-btn-email" 
                    onclick="switchDispatchTab('email')" 
                    class="flex-1 py-1.5 px-2 rounded-lg text-slate-600 hover:text-slate-900 flex items-center justify-center gap-1.5 cursor-pointer transition-all">
                <x-icon name="mail" class="w-3.5 h-3.5" />
                <span>Email Vendor</span>
            </button>
            <button type="button" 
                    id="tab-btn-link" 
                    onclick="switchDispatchTab('link')" 
                    class="flex-1 py-1.5 px-2 rounded-lg text-slate-600 hover:text-slate-900 flex items-center justify-center gap-1.5 cursor-pointer transition-all">
                <x-icon name="share2" class="w-3.5 h-3.5" />
                <span>Salin Link</span>
            </button>
        </div>

        <!-- TAB 1: WhatsApp Dispatch -->
        <div id="dispatch-tab-wa" class="space-y-4">
            <div>
                <label class="block text-xs font-black uppercase text-slate-900 mb-1">
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

            <div class="pt-3 border-t-[1.5px] border-slate-200 flex items-center justify-end gap-2">
                <button type="button" 
                        onclick="closeVendorDispatchModal()" 
                        class="neo-btn bg-slate-200 text-xs py-2 px-4 cursor-pointer font-bold">
                    Batal
                </button>
                <button type="button" 
                        onclick="sendDispatchWhatsApp()" 
                        class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-5 font-black text-slate-950 shadow-[2px_2px_0px_0px_#0f172a] flex items-center gap-2 cursor-pointer">
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
                    <label class="block text-xs font-black uppercase text-slate-900 mb-1">
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
                    <label class="block text-xs font-black uppercase text-slate-900 mb-1">
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

                <div class="pt-3 border-t-[1.5px] border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <a href="#" 
                       id="dispatch-email-preview-link" 
                       target="_blank" 
                       title="Buka pratinjau format email di tab baru"
                       class="neo-btn bg-sky-100 hover:bg-sky-200 text-slate-800 text-xs py-2 px-3 font-bold border-slate-900 shadow-[1.5px_1.5px_0px_0px_#0f172a] inline-flex items-center justify-center gap-1.5 transition-all">
                        <x-icon name="eye" class="w-3.5 h-3.5 text-slate-700" />
                        <span>Pratinjau Email</span>
                    </a>

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" 
                                onclick="closeVendorDispatchModal()" 
                                class="neo-btn bg-slate-200 hover:bg-slate-300 text-xs py-2 px-4 cursor-pointer font-bold text-slate-700">
                            Batal
                        </button>
                        <button type="submit" 
                                id="dispatch-email-submit-btn"
                                class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-5 font-black text-slate-950 shadow-[2px_2px_0px_0px_#0f172a] flex items-center gap-2 cursor-pointer">
                            <x-icon name="send" class="w-3.5 h-3.5" />
                            <span id="dispatch-email-submit-text">Kirim Email</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB 3: Share Direct Public Link -->
        <div id="dispatch-tab-link" class="hidden space-y-4">
            <div>
                <label class="block text-xs font-black uppercase text-slate-900 mb-1">
                    Link Portal Berkas Siap Cetak (Publik & Tanpa Login)
                </label>
                <div class="flex items-center gap-2">
                    <input type="text" 
                           id="dispatch-public-url" 
                           readonly 
                           class="neo-input text-xs font-mono font-bold w-full py-2 px-3 bg-slate-50 select-all">
                    <button type="button" 
                            onclick="copyPublicUrlToClipboard()" 
                            id="btn-copy-public-url"
                            class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-4 font-black text-slate-950 shrink-0 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                        Salin
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-1 leading-relaxed">
                    Link ini aman dan dapat dibuka oleh siapa saja (rekanan percetakan) untuk mengunduh berkas resolusi penuh dan melihat spesifikasi cetak tanpa harus login.
                </p>
            </div>

            <div class="pt-3 border-t-[1.5px] border-slate-200 flex items-center justify-end">
                <button type="button" 
                        onclick="closeVendorDispatchModal()" 
                        class="neo-btn bg-slate-200 hover:bg-slate-300 text-xs py-2 px-5 cursor-pointer font-bold text-slate-700">
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
    document.getElementById('dispatch-file-name').innerText = data.fileName || '-';
    document.getElementById('dispatch-file-specs').innerText = (data.dimensions ? data.dimensions + ' • ' : '') + (data.materialNotes || 'Standar');
    document.getElementById('dispatch-public-url').value = data.shareUrl || '';

    // Set form action for email
    const form = document.getElementById('dispatch-email-form');
    if (form && data.assetId) {
        form.action = '/vendor/dispatch/' + data.assetId + '/email';
    }

    // Set preview email link
    const previewLink = document.getElementById('dispatch-email-preview-link');
    if (previewLink && data.assetId) {
        previewLink.href = '/vendor/dispatch/' + data.assetId + '/preview-email';
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
    if (d.dimensions) msg += `Ukuran: ${d.dimensions}\n`;
    if (d.materialNotes) msg += `Bahan & Finishing: ${d.materialNotes}\n`;
    if (d.briefNotes) msg += `Catatan: ${d.briefNotes}\n`;
    msg += `\n*Nama Berkas Siap Cetak:*\n${d.fileName || '-'}\n`;
    msg += `\n*Link Download Berkas (Resolusi Penuh):*\n${d.shareUrl || ''}\n\n`;
    msg += `_Mohon segera diproses cetak. Terima kasih!_`;
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
    const url = document.getElementById('dispatch-public-url').value;
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('btn-copy-public-url');
        btn.innerText = 'Tersalin!';
        setTimeout(() => btn.innerText = 'Salin', 2000);
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

