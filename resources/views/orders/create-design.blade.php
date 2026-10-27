@extends('layouts.app')

@section('title', 'Buat Tiket Order Desain Baru')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black">
        <div>
            <!-- <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                <span class="text-xs font-black uppercase tracking-wider text-sky-800">
                    SOP Tahap 1: Penerimaan Order & Komitmen Biaya
                </span>
            </div> -->
            <h1 class="text-2xl font-black text-black">Input Tiket Order Desain & Cetak</h1>
            <!-- <p class="text-xs font-semibold text-slate-600 mt-0.5">
                Data spesifikasi pengerjaan, deadline, dan komitmen DP/Lunas yang otomatis tercatat ke buku kas cabang.
            </p> -->
        </div>
        <a href="{{ route('orders.index') }}" class="neo-btn bg-slate-200 hover:bg-slate-300 text-xs py-2 px-3.5 flex items-center gap-1.5 self-start sm:self-center">
            <x-icon name="arrow-left" class="w-3.5 h-3.5" stroke-width="2.5" />
            <span>Kembali ke Daftar Order</span>
        </a>
    </div>

    <!-- Main Split Form: Left Specs (8 cols), Right Payment & Actions (4 cols) -->
    <form method="POST" action="{{ route('orders.store-design') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Column: Specs & Customer Data (8 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Card 1: Data Pelanggan & Cabang Pelaksana -->
                <div class="neo-box p-5 bg-white space-y-4">
                    <h2 class="text-sm font-black text-slate-800 pb-3 border-b border-slate-200 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-amber-200 border-[1.5px] border-slate-800 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                                <x-icon name="user" class="w-4 h-4" />
                            </div>
                            <span>1. Data Pelanggan & Cabang Pelaksana</span>
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Wajib Diisi</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        @if(Auth::user()->isOwner())
                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Pilih Cabang Pelaksana *</label>
                            <select name="branch_id" required class="neo-select">
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->code }} - {{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Cabang Pelaksana</label>
                            <input type="text" disabled value="{{ Auth::user()->branch->name ?? 'Cabang Saya' }}" class="neo-input text-xs bg-slate-100 cursor-not-allowed">
                        </div>
                        @endif

                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Nama Customer / Instansi *</label>
                            <input type="text" 
                                   name="customer_name" 
                                   value="{{ old('customer_name') }}" 
                                   required 
                                   placeholder="Contoh: Kopi Nusantara / Bpk. Hendra"
                                   class="neo-input text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">No. WhatsApp / Telepon Customer</label>
                            <input type="text" 
                                   name="customer_phone" 
                                   value="{{ old('customer_phone') }}" 
                                   placeholder="Contoh: 081234567890"
                                   class="neo-input text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Tugaskan ke Desainer (Opsional)</label>
                            <select name="designer_id" class="neo-select">
                                <option value="">-- Masuk Antrean Umum Cabang (Draft) --</option>
                                @foreach($designers as $d)
                                    <option value="{{ $d->id }}" {{ old('designer_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }} ({{ $d->branch ? $d->branch->code : 'Pusat' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Card 2: Spesifikasi Pengerjaan & Brief Desain -->
                <div class="neo-box p-5 bg-white space-y-4">
                    <h2 class="text-sm font-black text-slate-800 pb-3 border-b border-slate-200 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-200 border-[1.5px] border-slate-800 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                                <x-icon name="palette" class="w-4 h-4" />
                            </div>
                            <span>2. Spesifikasi Desain & Bahan Produksi</span>
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Detail Teknis</span>
                    </h2>

                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Judul / Nama Desain *</label>
                            <input type="text" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required 
                                   placeholder="Contoh: Spanduk Warung Makan Soto Lamongan / Kartu Nama Eksekutif"
                                   class="neo-input text-xs">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase text-black mb-1">Dimensi / Ukuran Jadi</label>
                                <input type="text" 
                                       name="dimensions" 
                                       value="{{ old('dimensions') }}" 
                                       placeholder="Contoh: 3 x 1 meter / A4 Lipat 3 / 9x5.5 cm"
                                       class="neo-input text-xs">
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase text-black mb-1">Bahan & Spesifikasi Finishing</label>
                                <input type="text" 
                                       name="material_notes" 
                                       value="{{ old('material_notes') }}" 
                                       placeholder="Contoh: Flexi Korea 380gr + Mata Ayam / Art Carton 260gr Doff"
                                       class="neo-input text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="deadline_at" class="block text-xs font-black uppercase text-black mb-1">
                                    Tenggat Waktu Selesai (Deadline)
                                </label>
                                <input type="datetime-local" 
                                       id="deadline_at"
                                       name="deadline_at" 
                                       value="{{ old('deadline_at', date('Y-m-d\TH:i', strtotime('+1 day 17:00'))) }}" 
                                       class="neo-input text-xs font-bold h-10 py-2">
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="brief_file" class="block text-xs font-black uppercase text-black cursor-pointer">
                                        File Acuan / Referensi Customer
                                    </label>
                                    <span class="text-[10px] font-bold text-slate-500">Opsional (Sketsa/Logo)</span>
                                </div>
                                <div class="relative">
                                    <input type="file" 
                                           id="brief_file" 
                                           name="brief_file" 
                                           class="sr-only" 
                                           onchange="handleBriefFileChange(this)">
                                    <label for="brief_file" 
                                           class="flex items-center justify-between h-10 px-3 bg-white border-[1.5px] border-slate-300 rounded-xl shadow-[1.5px_1.5px_0px_0px_#e2e8f0] hover:border-slate-800 hover:shadow-[2px_2px_0px_0px_#0f172a] cursor-pointer transition-all select-none">
                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                            <x-icon name="file" class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                            <span id="brief_file_name" class="text-xs text-slate-400 font-medium truncate pr-1">
                                                Pilih file acuan customer...
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <button type="button" 
                                                    id="brief_file_clear" 
                                                    onclick="clearBriefFile(event)" 
                                                    title="Hapus file"
                                                    class="hidden text-slate-400 hover:text-rose-600 font-black text-xs px-1">
                                                ✕
                                            </button>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-200 hover:bg-amber-300 border border-slate-700 rounded-lg text-[10px] font-black uppercase text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                                                <x-icon name="upload" class="w-3 h-3" />
                                                <span>Pilih File</span>
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Catatan Brief Lengkap Dari Customer</label>
                            <textarea name="brief_notes" 
                                      rows="3" 
                                      placeholder="Tuliskan warna dominan, teks utama, kontak yang dicantumkan, logo, atau referensi gaya desain yang diinginkan customer..."
                                      class="neo-input text-xs">{{ old('brief_notes') }}</textarea>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right Column: Payment & Commitments (4 cols, Sticky) -->
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

                <!-- Card 3: Biaya & Pembayaran Awal -->
                <div class="neo-box p-5 bg-amber-50/60 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-amber-200/80">
                        <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                            <div class="w-7 h-7 bg-emerald-200 border-[1.5px] border-slate-800 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                                <x-icon name="wallet" class="w-4 h-4" />
                            </div>
                            <span>3. Biaya & Kas Awal</span>
                        </h2>
                        <span class="text-[10px] font-black text-emerald-950 bg-emerald-100 border border-emerald-300 px-2 py-0.5 rounded">
                            SOP Kasir
                        </span>
                    </div>

                    <div class="space-y-4">
                        <!-- Total Biaya Order -->
                        <div>
                            <label for="total_amount_display" class="block text-xs font-black uppercase text-black mb-1">
                                Total Biaya Order *
                            </label>
                            <div class="flex rounded-xl overflow-hidden border-[1.5px] border-slate-300 bg-white shadow-[1.5px_1.5px_0px_0px_#e2e8f0] focus-within:border-slate-800 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] transition-all">
                                <span class="inline-flex items-center px-3.5 bg-amber-200 border-r-[1.5px] border-slate-300 text-xs font-black text-slate-800 select-none">
                                    Rp
                                </span>
                                <input type="text" 
                                       id="total_amount_display" 
                                       inputmode="numeric"
                                       value="{{ old('total_amount') ? number_format((float) old('total_amount'), 0, ',', '.') : '' }}" 
                                       placeholder="0"
                                       required 
                                       autocomplete="off"
                                       class="w-full px-3 py-2 text-base font-black text-black bg-transparent focus:outline-none placeholder:text-slate-300 tracking-wide">
                            </div>
                            <input type="hidden" 
                                   id="total_amount" 
                                   name="total_amount" 
                                   value="{{ old('total_amount', '') }}">
                            <span class="text-[10px] text-slate-500 font-semibold mt-1 block">Otomatis terformat rupiah (contoh: 250.000).</span>
                        </div>

                        <!-- Nominal Dibayar di Awal -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="paid_amount_display" class="block text-xs font-black uppercase text-black">
                                    Nominal Dibayar di Awal *
                                </label>
                                <span class="text-[10px] font-bold text-slate-500">Min. DP / Lunas</span>
                            </div>
                            <div class="flex rounded-xl overflow-hidden border-[1.5px] border-slate-300 bg-white shadow-[1.5px_1.5px_0px_0px_#e2e8f0] focus-within:border-slate-800 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] transition-all">
                                <span class="inline-flex items-center px-3.5 bg-emerald-200 border-r-[1.5px] border-slate-300 text-xs font-black text-slate-800 select-none">
                                    Rp
                                </span>
                                <input type="text" 
                                       id="paid_amount_display" 
                                       inputmode="numeric"
                                       value="{{ (old('paid_amount') !== null && old('paid_amount') !== '' && old('paid_amount') > 0) ? number_format((float) old('paid_amount'), 0, ',', '.') : '' }}" 
                                       placeholder="0"
                                       required 
                                       autocomplete="off"
                                       class="w-full px-3 py-2 text-base font-black text-black bg-transparent focus:outline-none placeholder:text-slate-300 tracking-wide">
                            </div>
                            <input type="hidden" 
                                   id="paid_amount" 
                                   name="paid_amount" 
                                   value="{{ old('paid_amount', 0) }}">

                            <!-- Quick preset buttons for cashier -->
                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                <button type="button" 
                                        onclick="setPaidPreset('full')" 
                                        class="px-2.5 py-1 text-[10px] font-black uppercase bg-emerald-100 hover:bg-emerald-200 text-emerald-950 border border-slate-300 hover:border-slate-700 rounded shadow-[1px_1px_0px_0px_#e2e8f0] transition-all active:translate-y-0.5 cursor-pointer">
                                    Lunas 100%
                                </button>
                                <button type="button" 
                                        onclick="setPaidPreset('half')" 
                                        class="px-2.5 py-1 text-[10px] font-black uppercase bg-amber-100 hover:bg-amber-200 text-amber-950 border border-slate-300 hover:border-slate-700 rounded shadow-[1px_1px_0px_0px_#e2e8f0] transition-all active:translate-y-0.5 cursor-pointer">
                                    DP 50%
                                </button>
                                <button type="button" 
                                        onclick="setPaidPreset(50000)" 
                                        class="px-2.5 py-1 text-[10px] font-bold bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 hover:border-slate-700 rounded shadow-[1px_1px_0px_0px_#e2e8f0] transition-all active:translate-y-0.5 cursor-pointer">
                                    DP 50rb
                                </button>
                                <button type="button" 
                                        onclick="setPaidPreset(100000)" 
                                        class="px-2.5 py-1 text-[10px] font-bold bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 hover:border-slate-700 rounded shadow-[1px_1px_0px_0px_#e2e8f0] transition-all active:translate-y-0.5 cursor-pointer">
                                    DP 100rb
                                </button>
                                <button type="button" 
                                        onclick="setPaidPreset(0)" 
                                        class="px-2.5 py-1 text-[10px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-300 hover:border-slate-700 rounded shadow-[1px_1px_0px_0px_#e2e8f0] transition-all active:translate-y-0.5 cursor-pointer">
                                    Rp 0 (Belum Bayar)
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-black mb-1">Metode Pembayaran *</label>
                            <select name="payment_method" required class="neo-select">
                                <option value="cash">Tunai (Cash Laci)</option>
                                <option value="qris">QRIS Statis/Dinamis</option>
                                <option value="transfer">Transfer Bank Cabang</option>
                            </select>
                        </div>
                    </div>

                    <!-- Real-time calculation summary -->
                    <div class="p-3.5 bg-white border border-amber-200/90 rounded-xl space-y-2.5 shadow-sm">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-bold">Sisa Tagihan:</span>
                            <span id="remaining-text" class="text-base font-black text-rose-600">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-xs">
                            <span class="text-slate-500 font-medium">Status Order:</span>
                            <span id="status-badge" class="px-2.5 py-0.5 rounded-full border border-rose-300 font-black uppercase text-[10px] bg-rose-100 text-rose-800 shadow-[1px_1px_0px_0px_#0f172a]">
                                BELUM BAYAR
                            </span>
                        </div>
                    </div>

                    <!-- SOP Info Note -->
                    <div class="text-[11px] font-medium text-slate-700 bg-white/80 p-2.5 rounded-lg border border-amber-200/90 flex items-start gap-2">
                        <x-icon name="info" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                        <span>Pembayaran awal otomatis tercatat di <strong>Buku Kas</strong> cabang hari ini dan tiket langsung masuk antrean workdesk.</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 pt-2 border-t border-amber-200/80">
                        <button type="submit" class="w-full neo-btn bg-emerald-400 hover:bg-emerald-300 text-slate-900 font-black text-sm py-3 px-4 shadow-[2.5px_2.5px_0px_0px_#0f172a] flex items-center justify-center gap-2 cursor-pointer">
                            <x-icon name="check" class="w-4 h-4" stroke-width="2.5" />
                            <span>Terbitkan Tiket & Catat Kas</span>
                        </button>

                        <a href="{{ route('orders.index') }}" class="w-full neo-btn bg-slate-100 hover:bg-slate-200 text-xs py-2 text-center block font-bold text-slate-600 shadow-[1.5px_1.5px_0px_0px_#e2e8f0]">
                            Batal
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>
function setupRupiahMask(displayId, hiddenId) {
    const displayEl = document.getElementById(displayId);
    const hiddenEl = document.getElementById(hiddenId);
    if (!displayEl || !hiddenEl) return;

    displayEl.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace') {
            const cursor = displayEl.selectionStart;
            // If deleting right after a dot separator (e.g. "250.|000"), remove preceding digit
            if (cursor > 1 && displayEl.value[cursor - 1] === '.') {
                e.preventDefault();
                const val = displayEl.value;
                const newVal = val.slice(0, cursor - 2) + val.slice(cursor - 1);
                displayEl.value = newVal;
                displayEl.setSelectionRange(cursor - 2, cursor - 2);
                displayEl.dispatchEvent(new Event('input'));
            }
        }
    });

    displayEl.addEventListener('input', function() {
        const cursor = displayEl.selectionStart;
        const oldVal = displayEl.value;
        const rawDigits = oldVal.replace(/\D/g, '');

        if (!rawDigits) {
            displayEl.value = '';
            hiddenEl.value = '0';
        } else {
            const num = parseInt(rawDigits, 10);
            const formatted = num.toLocaleString('id-ID');
            displayEl.value = formatted;
            hiddenEl.value = num;

            // Maintain cursor position relative to added/removed dot separators
            const diff = formatted.length - oldVal.length;
            let newCursor = cursor + diff;
            if (newCursor < 0) newCursor = 0;
            if (newCursor > formatted.length) newCursor = formatted.length;
            displayEl.setSelectionRange(newCursor, newCursor);
        }

        calculateRemaining();
    });
}

function setPaidPreset(preset) {
    const total = parseFloat(document.getElementById('total_amount').value) || 0;
    const paidHidden = document.getElementById('paid_amount');
    const paidDisplay = document.getElementById('paid_amount_display');
    if (!paidHidden || !paidDisplay) return;

    let target = 0;
    if (preset === 'full') {
        target = total;
    } else if (preset === 'half') {
        target = Math.round(total * 0.5);
    } else if (typeof preset === 'number') {
        target = preset;
    }

    paidHidden.value = target;
    paidDisplay.value = target > 0 ? target.toLocaleString('id-ID') : '0';
    calculateRemaining();
}

function calculateRemaining() {
    const totalEl = document.getElementById('total_amount');
    const paidEl = document.getElementById('paid_amount');
    if (!totalEl || !paidEl) return;

    const total = parseFloat(totalEl.value) || 0;
    const paid = parseFloat(paidEl.value) || 0;
    const remaining = Math.max(0, total - paid);

    const remainingEl = document.getElementById('remaining-text');
    if (remainingEl) {
        remainingEl.innerText = 'Rp ' + remaining.toLocaleString('id-ID');
        if (remaining === 0 && total > 0) {
            remainingEl.className = 'text-base font-black text-emerald-600';
        } else {
            remainingEl.className = 'text-base font-black text-rose-600';
        }
    }

    const badge = document.getElementById('status-badge');
    if (badge) {
        if (paid >= total && total > 0) {
            badge.innerText = 'LUNAS PENUH';
            badge.className = 'px-2.5 py-0.5 rounded-full border border-emerald-400 font-black uppercase text-[10px] bg-emerald-100 text-emerald-900 shadow-[1px_1px_0px_0px_#0f172a]';
        } else if (paid > 0) {
            badge.innerText = 'DP (SEBAGIAN)';
            badge.className = 'px-2.5 py-0.5 rounded-full border border-yellow-400 font-black uppercase text-[10px] bg-yellow-100 text-yellow-900 shadow-[1px_1px_0px_0px_#0f172a]';
        } else {
            badge.innerText = 'BELUM BAYAR';
            badge.className = 'px-2.5 py-0.5 rounded-full border border-rose-300 font-black uppercase text-[10px] bg-rose-100 text-rose-800 shadow-[1px_1px_0px_0px_#0f172a]';
        }
    }
}

function handleBriefFileChange(input) {
    const label = document.getElementById('brief_file_name');
    const clearBtn = document.getElementById('brief_file_clear');
    if (input.files && input.files[0]) {
        label.innerText = input.files[0].name;
        label.className = 'text-xs text-black font-black truncate pr-1';
        if (clearBtn) clearBtn.classList.remove('hidden');
    } else {
        label.innerText = 'Pilih file acuan customer...';
        label.className = 'text-xs text-slate-400 font-medium truncate pr-1';
        if (clearBtn) clearBtn.classList.add('hidden');
    }
}

function clearBriefFile(event) {
    event.preventDefault();
    event.stopPropagation();
    const input = document.getElementById('brief_file');
    input.value = '';
    const label = document.getElementById('brief_file_name');
    const clearBtn = document.getElementById('brief_file_clear');
    label.innerText = 'Pilih file acuan customer...';
    label.className = 'text-xs text-slate-400 font-medium truncate pr-1';
    if (clearBtn) clearBtn.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    setupRupiahMask('total_amount_display', 'total_amount');
    setupRupiahMask('paid_amount_display', 'paid_amount');
    calculateRemaining();
});
</script>
@endpush
@endsection
