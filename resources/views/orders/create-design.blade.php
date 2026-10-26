@extends('layouts.app')

@section('title', 'Buat Tiket Order Desain Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b-2 border-black">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                <span class="text-xs font-black uppercase tracking-wider text-sky-800">
                    SOP Tahap 1: Penerimaan Order & Komitmen Biaya
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-black">Input Tiket Order Desain & Cetak</h1>
            <p class="text-xs font-semibold text-slate-600 mt-1">
                Data spesifikasi pengerjaan, deadline, dan pembayaran awal (DP/Lunas) yang otomatis tercatat ke buku kas cabang.
            </p>
        </div>
        <a href="{{ route('orders.index') }}" class="neo-btn bg-slate-200 hover:bg-slate-300 text-xs py-2 px-3 flex items-center gap-1.5">
            <x-icon name="arrow-left" class="w-3.5 h-3.5" stroke-width="2.5" />
            <span>Kembali</span>
        </a>
    </div>

    <form method="POST" action="{{ route('orders.store-design') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Card 1: Lokasi & Data Pelanggan -->
        <div class="neo-box p-6 bg-white space-y-4">
            <h2 class="text-base font-black text-black pb-2 border-b-2 border-black flex items-center gap-2.5">
                <div class="w-7 h-7 bg-amber-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                    <x-icon name="user" class="w-4 h-4" />
                </div>
                <span>1. Data Pelanggan & Cabang Pelaksana</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                @if(Auth::user()->isOwner())
                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Pilih Cabang Pelaksana *</label>
                    <select name="branch_id" required class="neo-input text-xs font-bold">
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
                    <select name="designer_id" class="neo-input text-xs font-medium">
                        <option value="">-- Masuk Antrean Umum Cabang (Draft/In Progress) --</option>
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
        <div class="neo-box p-6 bg-white space-y-4">
            <h2 class="text-base font-black text-black pb-2 border-b-2 border-black flex items-center gap-2.5">
                <div class="w-7 h-7 bg-blue-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                    <x-icon name="palette" class="w-4 h-4" />
                </div>
                <span>2. Spesifikasi Desain & Bahan Produksi</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase text-black mb-1">Judul / Nama Desain *</label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title') }}" 
                           required 
                           placeholder="Contoh: Spanduk Warung Makan Soto Lamongan / Kartu Nama Eksekutif"
                           class="neo-input text-xs">
                </div>

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

                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase text-black mb-1">Catatan Brief Lengkap Dari Customer</label>
                    <textarea name="brief_notes" 
                              rows="3" 
                              placeholder="Tuliskan warna dominan, teks utama, kontak yang dicantumkan, logo, atau referensi gaya desain yang diinginkan customer..."
                              class="neo-input text-xs">{{ old('brief_notes') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Tenggat Waktu Selesai (Deadline)</label>
                    <input type="datetime-local" 
                           name="deadline_at" 
                           value="{{ old('deadline_at', date('Y-m-d\TH:i', strtotime('+1 day 17:00'))) }}" 
                           class="neo-input text-xs font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">File Acuan / Referensi Customer (Opsional)</label>
                    <input type="file" 
                           name="brief_file" 
                           class="neo-input text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded file:border file:border-black file:bg-amber-200 file:text-xs file:font-bold">
                    <span class="text-[11px] text-slate-500 font-medium">Bisa gambar sketsa, dokumen brief, atau logo dari customer.</span>
                </div>

            </div>
        </div>

        <!-- Card 3: Komitmen Biaya & Pembayaran Awal (SOP: Kas Langsung Tercatat) -->
        <div class="neo-box p-6 bg-amber-50 border-2 border-black space-y-4">
            <h2 class="text-base font-black text-black pb-2 border-b-2 border-black flex items-center justify-between">
                <span class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-emerald-400 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                        <x-icon name="wallet" class="w-4 h-4" />
                    </div>
                    <span>3. Komitmen Biaya & Pembayaran di Awal (Wajib DP / Lunas)</span>
                </span>
                <span class="text-xs font-black text-emerald-800 bg-emerald-200 border border-black px-2 py-0.5 rounded">
                    SOP Wajib Kasir
                </span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Total Biaya Order (Rp) *</label>
                    <input type="number" 
                           id="total_amount" 
                           name="total_amount" 
                           value="{{ old('total_amount', 250000) }}" 
                           min="1000" 
                           required 
                           oninput="calculateRemaining()"
                           class="neo-input text-sm font-black">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Nominal Dibayar di Awal (Rp) *</label>
                    <input type="number" 
                           id="paid_amount" 
                           name="paid_amount" 
                           value="{{ old('paid_amount', 100000) }}" 
                           min="0" 
                           required 
                           oninput="calculateRemaining()"
                           class="neo-input text-sm font-black">
                    <span class="text-[10px] font-bold text-slate-500">Minimal DP atau Lunas penuh.</span>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-black mb-1">Metode Pembayaran *</label>
                    <select name="payment_method" required class="neo-input text-xs font-bold">
                        <option value="cash">Tunai (Cash Laci)</option>
                        <option value="qris">QRIS Statis/Dinamis</option>
                        <option value="transfer">Transfer Bank Cabang</option>
                    </select>
                </div>

            </div>

            <!-- Real-time calculation badge -->
            <div class="p-3 bg-white border-2 border-black rounded-xl flex items-center justify-between text-xs">
                <div>
                    <span class="text-slate-500 font-medium">Sisa Tagihan yang Harus Dilunasi Saat Pengambilan:</span>
                    <div id="remaining-text" class="text-base font-black text-rose-700">Rp 150.000</div>
                </div>
                <div id="status-badge" class="px-2.5 py-1 rounded-full border border-black font-black uppercase text-[10px] bg-yellow-300">
                    Status: DP (Sebagian)
                </div>
            </div>

            <div class="text-[11px] font-semibold text-slate-600 bg-white p-2.5 rounded-lg border border-black flex items-center gap-2">
                <x-icon name="info" class="w-4 h-4 text-amber-500 shrink-0" />
                <span><strong>Catatan SOP:</strong> Pembayaran ini otomatis tercatat di <code>cash_movements</code> cabang hari ini dan tiket pesanan langsung masuk ke workdesk antrean desainer dengan status <code>in_progress</code>.</span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('orders.index') }}" class="neo-btn bg-slate-200 hover:bg-slate-300 text-xs py-3 px-5">
                Batal
            </a>
            <button type="submit" class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-black font-black text-sm py-3 px-6 shadow-[4px_4px_0px_0px_#000] flex items-center gap-2 cursor-pointer">
                <x-icon name="check" class="w-4 h-4" stroke-width="2.5" />
                <span>Terbitkan Tiket Order & Catat Kas Masuk</span>
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
function calculateRemaining() {
    const total = parseFloat(document.getElementById('total_amount').value) || 0;
    const paid = parseFloat(document.getElementById('paid_amount').value) || 0;
    const remaining = Math.max(0, total - paid);
    
    document.getElementById('remaining-text').innerText = 'Rp ' + remaining.toLocaleString('id-ID');
    
    const badge = document.getElementById('status-badge');
    if (paid >= total && total > 0) {
        badge.innerText = 'Status: LUNAS PENUH';
        badge.className = 'px-2.5 py-1 rounded-full border border-black font-black uppercase text-[10px] bg-emerald-400';
    } else if (paid > 0) {
        badge.innerText = 'Status: DP (SEBAGIAN)';
        badge.className = 'px-2.5 py-1 rounded-full border border-black font-black uppercase text-[10px] bg-yellow-300';
    } else {
        badge.innerText = 'Status: BELUM BAYAR';
        badge.className = 'px-2.5 py-1 rounded-full border border-black font-black uppercase text-[10px] bg-rose-300';
    }
}
document.addEventListener('DOMContentLoaded', calculateRemaining);
</script>
@endpush
@endsection
