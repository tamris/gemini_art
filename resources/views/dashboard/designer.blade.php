@extends('layouts.app')

@section('title', 'Workdesk Desainer - ' . ($branch->name ?? 'Cabang'))

@section('content')
<div class="space-y-8">

    <!-- Top Designer Status Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-blue-200 border-2 border-black rounded-2xl shadow-[4px_4px_0px_0px_#000]">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                <span class="text-xs font-black uppercase tracking-wider text-blue-900">
                    Workdesk Desain & Pra-Cetak ({{ $branch->code ?? '' }})
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-black">Antrean Pengerjaan & Upload File Cetak</h1>
            <!-- <p class="text-xs md:text-sm font-semibold text-slate-700 mt-1">
                Kerjakan brief desain, sesuaikan dimensi & bahan, lalu unggah file cetak final (PDF/TIFF/JPG) untuk otomatisasi arsip cloud.
            </p> -->
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('workdesk.index') }}" class="neo-btn bg-white hover:bg-slate-100 text-xs py-2.5 px-4 text-black flex items-center gap-2">
                <x-icon name="layers" class="w-4 h-4" />
                <span>Workdesk Kanban Lengkap</span>
            </a>
            <a href="{{ route('archive.index') }}" class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2.5 px-4 text-black flex items-center gap-2">
                <x-icon name="archive" class="w-4 h-4" />
                <span>Arsip File Selesai</span>
            </a>
        </div>
    </div>

    <!-- Active Design Jobs in Progress (Workdesk Cards) -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-black flex items-center gap-2.5">
                <div class="w-7 h-7 bg-amber-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                    <x-icon name="clock" class="w-4 h-4" />
                </div>
                <span>Tiket Desain Perlu Dikerjakan ({{ $inProgressJobs->count() }})</span>
            </h2>
            <span class="text-xs font-bold text-slate-500">
                Prioritas berdasarkan deadline terdekat
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($inProgressJobs as $job)
            <div class="neo-box p-5 bg-white flex flex-col justify-between hover:translate-y-[-2px] transition-transform">
                <div>
                    <!-- Header with Order Number and Status Badge -->
                    <div class="flex items-center justify-between gap-2 pb-3 border-b-2 border-black mb-3">
                        <span class="font-mono text-xs font-bold bg-slate-100 border border-black px-2 py-0.5 rounded">
                            {{ $job->order->order_number }}
                        </span>
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full border border-black {{ $job->order->status_color_class }}">
                            {{ $job->order->status_label }}
                        </span>
                    </div>

                    <!-- Job Title & Customer -->
                    <h3 class="font-black text-lg text-black mb-1 leading-snug">
                        {{ $job->title }}
                    </h3>
                    <div class="text-xs font-bold text-slate-600 mb-3">
                        Customer: <span class="text-black">{{ $job->order->customer_name }}</span>
                    </div>

                    <!-- Specs: Dimensions & Material -->
                    <div class="grid grid-cols-2 gap-2 mb-3 text-xs">
                        <div class="p-2 bg-slate-50 border-2 border-black rounded-lg">
                            <span class="text-[10px] font-black uppercase text-slate-500 block">Dimensi / Ukuran</span>
                            <span class="font-bold text-black">{{ $job->dimensions ?? '-' }}</span>
                        </div>
                        <div class="p-2 bg-slate-50 border-2 border-black rounded-lg">
                            <span class="text-[10px] font-black uppercase text-slate-500 block">Bahan & Finishing</span>
                            <span class="font-bold text-black">{{ $job->material_notes ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Brief Notes -->
                    @if($job->brief_notes)
                    <div class="p-3 bg-amber-50 border-2 border-black rounded-lg text-xs mb-3">
                        <span class="font-black text-amber-900 block mb-1">Catatan Brief Kasir:</span>
                        <p class="text-slate-800 line-clamp-3 font-medium">{{ $job->brief_notes }}</p>
                    </div>
                    @endif

                    <!-- Deadline -->
                    <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-4 px-1">
                        <span>Deadline:</span>
                        <span class="{{ $job->deadline_at && $job->deadline_at->isPast() ? 'text-rose-600 font-black' : 'text-slate-900' }}">
                            {{ $job->deadline_at ? $job->deadline_at->format('d M Y H:i') : 'Tidak ditentukan' }}
                        </span>
                    </div>
                </div>

                <!-- Action Button: Upload Final Print Asset (Direct Dialog Trigger) -->
                <div class="pt-3 border-t-2 border-black space-y-2">
                    <button type="button" 
                            onclick="openUploadDialog('{{ $job->id }}', '{{ $job->order->order_number }}', '{{ addslashes($job->title) }}')"
                            class="w-full neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2.5 text-black font-black flex items-center justify-center gap-2 cursor-pointer">
                        <x-icon name="upload-cloud" class="w-4 h-4" />
                        <span>Upload File Siap Cetak (Final)</span>
                    </button>
                    <a href="{{ route('orders.show', $job->order->id) }}" class="block text-center text-xs font-bold text-slate-600 hover:underline py-1">
                        Lihat Rincian Tiket & File Brief →
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full p-12 text-center neo-box bg-white">
                <div class="w-12 h-12 bg-emerald-100 border-2 border-black rounded-xl flex items-center justify-center mx-auto mb-3 shadow-[2px_2px_0px_0px_#000]">
                    <x-icon name="check-circle-2" class="w-6 h-6 text-emerald-700" stroke-width="2.5" />
                </div>
                <h3 class="font-black text-lg text-black mb-1">Semua Tiket Selesai Dikerjakan!</h3>
                <p class="text-xs font-medium text-slate-500">
                    Tidak ada antrean desain aktif di cabang ini saat ini. Tiket baru dari kasir akan otomatis tampil di sini.
                </p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Ready to Print (Already Uploaded) History Section -->
    <div class="neo-box p-6 bg-white">
        <div class="flex items-center justify-between pb-4 border-b-2 border-black mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-emerald-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                    <x-icon name="printer" class="w-4 h-4" />
                </div>
                <h2 class="font-black text-base text-black">File Cetak Siap Produksi (Telah Diunggah)</h2>
            </div>
            <a href="{{ route('archive.index') }}" class="text-xs font-extrabold text-blue-600 hover:underline">
                Buka Arsip Cloud →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($readyJobs as $job)
            <div class="p-3.5 bg-slate-50 border-2 border-black rounded-xl flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold">{{ $job->order->order_number }}</span>
                        <span class="text-[10px] font-black uppercase px-2 py-0.2 rounded-full border border-black bg-emerald-400">
                            Siap Cetak
                        </span>
                    </div>
                    <div class="font-bold text-xs text-black mt-1">{{ $job->title }}</div>
                    <div class="text-[11px] text-slate-500 font-medium">Customer: {{ $job->order->customer_name }}</div>
                </div>
                <a href="{{ route('orders.show', $job->order->id) }}" class="neo-btn bg-white hover:bg-slate-100 text-xs py-1.5 px-3">
                    Buka
                </a>
            </div>
            @empty
            <div class="col-span-full text-center py-6 text-xs text-slate-400 font-semibold">
                Belum ada tiket berstatus Siap Cetak.
            </div>
            @endforelse
        </div>
    </div>

</div>

<!-- Upload Dialog Modal for Designers (Native HTML <dialog>) -->
<dialog id="upload-dialog" closedby="any" class="p-0 bg-transparent max-w-md w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] rounded-2xl border-2 border-slate-900 overflow-visible">
        <div class="flex items-center justify-between pb-3 border-b-2 border-black mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-emerald-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                    <x-icon name="upload-cloud" class="w-4 h-4" />
                </div>
                <h3 class="font-black text-base text-black">Upload File Cetak Final</h3>
            </div>
            <form method="dialog">
                <button class="w-8 h-8 rounded-lg border-2 border-black bg-rose-200 hover:bg-rose-300 active:translate-x-[1px] active:translate-y-[1px] font-black text-base flex items-center justify-center cursor-pointer">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </form>
        </div>

        <div class="mb-4 p-3 bg-slate-50 border-2 border-black rounded-xl text-xs">
            <div class="font-mono font-bold text-black" id="modal-order-number">-</div>
            <div class="font-bold text-slate-700 mt-0.5" id="modal-job-title">-</div>
        </div>

        <form id="upload-form" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Tipe File Yang Diunggah</label>
                <select name="file_type" required class="neo-select">
                    <option value="final_print" selected>File Cetak Siap Produksi (Otomatis Ubah Status ke Siap Cetak)</option>
                    <option value="preview_proof">Preview / Proofing ke Customer</option>
                    <option value="brief_ref">Referensi / Dokumen Brief Tambahan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-black mb-1">Pilih File (PDF, TIFF, JPG, CDR, PSD, ZIP)</label>
                <input type="file" name="file" required class="neo-input text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-2 file:border-black file:bg-amber-300 file:font-black file:text-xs">
                <p class="text-[11px] text-slate-500 font-semibold mt-1">
                    Sistem otomatis mengarsipkan ke cloud path: <code>designs/{branch}/{YYYY-MM}/{order}/...</code>
                </p>
            </div>

            <div class="pt-3 border-t-2 border-black flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('upload-dialog').close()" class="neo-btn bg-slate-200 text-xs py-2 px-4 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-4 font-black flex items-center gap-2 cursor-pointer">
                    <x-icon name="upload-cloud" class="w-3.5 h-3.5" />
                    <span>Unggah & Sinkronkan</span>
                </button>
            </div>
        </form>
    </div>
</dialog>

@push('scripts')
<script>
function openUploadDialog(jobId, orderNumber, jobTitle) {
    document.getElementById('modal-order-number').innerText = orderNumber;
    document.getElementById('modal-job-title').innerText = jobTitle;
    document.getElementById('upload-form').action = "/workdesk/" + jobId + "/upload";
    document.getElementById('upload-dialog').showModal();
}
</script>
@endpush
@endsection
