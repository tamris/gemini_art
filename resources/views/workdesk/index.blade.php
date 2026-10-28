@extends('layouts.app')

@section('title', 'Workdesk Desainer & Pra-Cetak')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-[1.5px] border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Workdesk Antrean Desainer</h1>
            <p class="text-xs font-semibold text-slate-500 mt-1">
                Pengerjaan aset desain, penyesuaian spesifikasi cetak, dan upload file final siap produksi.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('archive.index') }}" class="neo-btn bg-amber-200 hover:bg-amber-300 text-xs py-2 px-3.5 text-slate-900 flex items-center gap-2 font-black shadow-[2px_2px_0px_0px_#0f172a]">
                <x-icon name="archive" class="w-4 h-4" />
                <span>Arsip Desain</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar (Instant Auto-Filter) -->
    <div class="neo-box p-4 bg-white relative overflow-visible z-20">
        <form method="GET" action="{{ route('workdesk.index') }}" data-auto-submit class="flex flex-wrap items-center gap-3">
            
            @if(Auth::user()->isOwner())
            <div class="min-w-[190px]">
                <select name="branch_id" class="neo-select">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->code }} - {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="min-w-[190px]">
                <select name="status" class="neo-select">
                    <option value="">Semua Status Aktif</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Proses Pengerjaan</option>
                    <option value="revision" {{ request('status') == 'revision' ? 'selected' : '' }}>Revisi</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft / Antrean</option>
                    <option value="ready_to_print" {{ request('status') == 'ready_to_print' ? 'selected' : '' }}>Siap Cetak</option>
                </select>
            </div>

            <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer bg-white border-[1.5px] border-slate-300 rounded-xl px-3 py-2 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] hover:border-slate-800 transition-all select-none">
                <input type="checkbox" name="my_jobs" value="1" {{ request('my_jobs') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-400 text-amber-500 focus:ring-amber-400 cursor-pointer">
                <span>Hanya Tiket Saya</span>
            </label>

            <a href="{{ route('workdesk.index') }}" class="neo-btn {{ request()->anyFilled(['branch_id', 'status', 'my_jobs']) ? 'bg-amber-200 hover:bg-amber-300 text-slate-900 border-slate-800 font-black' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold' }} text-xs py-2 px-3 shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center gap-1.5 transition-all">
                <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                <span>Reset Filter</span>
            </a>
        </form>
    </div>

    <!-- Filter Results Container (Live Smooth Replacement) -->
    <div id="filter-results-container" class="space-y-6">
        <!-- Jobs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($jobs as $job)
        <div class="neo-box p-5 bg-white flex flex-col justify-between hover:translate-y-[-2px] transition-transform {{ $job->order->work_status === 'revision' ? 'ring-2 ring-rose-500 shadow-[4px_4px_0px_0px_#f43f5e]' : '' }}">
            <div>
                <!-- Top Status Bar -->
                <div class="flex items-center justify-between pb-3 border-b-[1.5px] border-slate-200 mb-3">
                    <div class="flex items-center gap-1.5">
                        <span class="font-mono text-xs font-bold bg-slate-100 border-[1.5px] border-slate-800 px-2 py-0.5 rounded-lg shadow-[1px_1px_0px_0px_#0f172a]">
                            {{ $job->order->order_number }}
                        </span>
                        <span class="text-[10px] font-black px-1.5 py-0.5 rounded-md border-[1.5px] border-slate-800 bg-slate-200">
                            {{ $job->order->branch->code }}
                        </span>
                    </div>
                    @if($job->order->work_status === 'revision')
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full border-[1.5px] border-slate-900 bg-rose-400 text-slate-950 flex items-center gap-1 shadow-[1px_1px_0px_0px_#0f172a]">
                            <x-icon name="alert-triangle" class="w-3 h-3 text-slate-950" />
                            <span>Revisi (Ke-{{ $job->revision_count ?? 1 }})</span>
                        </span>
                    @else
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full border-[1.5px] border-slate-800 shadow-[1px_1px_0px_0px_#0f172a] {{ $job->order->status_color_class }}">
                            {{ $job->order->status_label }}
                        </span>
                    @endif
                </div>

                <!-- Title & Customer -->
                <h3 class="font-black text-base text-slate-900 mb-1 leading-snug">
                    {{ $job->title }}
                </h3>
                <div class="text-xs font-semibold text-slate-600 mb-3">
                    Customer: <span class="font-bold text-slate-900">{{ $job->order->customer_name }}</span>
                    @if($job->order->customer_phone)
                        &bull; {{ $job->order->customer_phone }}
                    @endif
                </div>

                <!-- Specs Grid -->
                <div class="grid grid-cols-2 gap-2 mb-3 text-xs">
                    <div class="p-2.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl">
                        <span class="text-[10px] font-black uppercase text-slate-500 block mb-0.5">Dimensi</span>
                        <span class="font-extrabold text-slate-900 font-mono">{{ $job->dimensions ?? '-' }}</span>
                    </div>
                    <div class="p-2.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl">
                        <span class="text-[10px] font-black uppercase text-slate-500 block mb-0.5">Bahan</span>
                        <span class="font-bold text-slate-900">{{ $job->material_notes ?? '-' }}</span>
                    </div>
                </div>

                <!-- Revision Notes Snippet (Priority for Designer) -->
                @if($job->revision_notes)
                <div class="p-3 bg-rose-50 border-[1.5px] border-rose-300 rounded-xl text-xs mb-3 shadow-[1.5px_1.5px_0px_0px_#fecdd3]">
                    <div class="flex items-center gap-1.5 font-black text-rose-900 mb-1 text-[10px] uppercase tracking-wide">
                        <x-icon name="alert-circle" class="w-3.5 h-3.5 text-rose-600" />
                        <span>Catatan Revisi Pelanggan (Prioritas):</span>
                    </div>
                    <p class="text-rose-950 font-bold leading-relaxed">{{ $job->revision_notes }}</p>
                </div>
                @endif

                <!-- Brief Notes Snippet -->
                @if($job->brief_notes)
                <div class="p-3 bg-amber-50 border-[1.5px] border-amber-300 rounded-xl text-xs mb-3">
                    <span class="font-black text-amber-900 block mb-0.5 text-[10px] uppercase tracking-wide">Brief Catatan:</span>
                    <p class="text-amber-950 line-clamp-3 font-medium leading-relaxed">{{ $job->brief_notes }}</p>
                </div>
                @endif

                <!-- Uploaded Assets Badge -->
                <div class="mb-3 text-xs flex items-center justify-between px-1">
                    <span class="text-slate-500 font-medium">File Terunggah:</span>
                    <span class="font-bold text-slate-900 bg-slate-100 border border-slate-300 px-2 py-0.5 rounded-lg">
                        {{ $job->assets->count() }} File
                    </span>
                </div>

                <!-- Deadline & Assigned Designer -->
                <div class="p-2.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl text-xs space-y-1 mb-4">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Deadline:</span>
                        <span class="font-black {{ $job->deadline_at && $job->deadline_at->isPast() ? 'text-rose-600' : 'text-slate-900' }}">
                            {{ $job->deadline_at ? $job->deadline_at->format('d M H:i') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Desainer:</span>
                        <span class="font-bold text-slate-800">
                            {{ $job->designer ? $job->designer->name : 'Belum Ditugaskan' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="pt-3 border-t-[1.5px] border-slate-200 space-y-2">
                @if(!$job->designer_id || $job->designer_id !== Auth::id())
                <form method="POST" action="{{ route('workdesk.claim', $job->id) }}">
                    @csrf
                    <button type="submit" class="w-full neo-btn bg-sky-200 hover:bg-sky-300 text-xs py-2 font-black text-slate-900 flex items-center justify-center gap-2 cursor-pointer shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                        <x-icon name="user" class="w-3.5 h-3.5" />
                        <span>Ambil Alih Tiket Ini</span>
                    </button>
                </form>
                @endif

                <button type="button" 
                        onclick="openUploadDialog('{{ $job->id }}', '{{ $job->order->order_number }}', '{{ addslashes($job->title) }}')"
                        class="w-full neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 font-black text-slate-950 flex items-center justify-center gap-2 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a]">
                    <x-icon name="upload" class="w-4 h-4" />
                    <span>Upload File Cetak Final</span>
                </button>

                <a href="{{ route('orders.show', $job->order->id) }}" class="block text-center text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline py-1 transition-colors">
                    Detail Tiket & File Brief &rarr;
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center neo-box bg-white">
            <div class="w-12 h-12 bg-emerald-100 border-[1.5px] border-slate-900 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-[2px_2px_0px_0px_#0f172a]">
                <x-icon name="check-circle" class="w-6 h-6 text-emerald-700" stroke-width="2.5" />
            </div>
            <h3 class="font-black text-lg text-slate-900 mb-1">Tidak Ada Antrean Desain</h3>
            <p class="text-xs font-medium text-slate-500">
                Semua tiket pengerjaan selesai atau tidak sesuai filter.
            </p>
        </div>
        @endforelse
    </div>

    <div class="neo-box overflow-hidden bg-white shadow-[2px_2px_0px_0px_#0f172a]">
        {{ $jobs->links() }}
    </div>
    </div>

</div>

<!-- Upload Dialog Modal -->
<dialog id="upload-dialog" closedby="any" class="p-0 bg-transparent max-w-md w-full m-auto border-none outline-none overflow-visible">
    <div class="neo-box p-6 bg-white w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] rounded-2xl border-2 border-slate-900 overflow-visible">
        <div class="flex items-center justify-between pb-3 border-b-[1.5px] border-slate-800 mb-4">
            <h3 class="font-black text-base text-slate-900">Upload File Cetak Final</h3>
            <form method="dialog">
                <button class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 bg-rose-200 hover:bg-rose-300 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a]">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </form>
        </div>

        <div class="mb-4 p-3 bg-slate-50 border-[1.5px] border-slate-800 rounded-xl text-xs shadow-[1.5px_1.5px_0px_0px_#0f172a]">
            <div class="font-mono font-bold text-slate-900" id="modal-order-number">-</div>
            <div class="font-bold text-slate-700 mt-0.5" id="modal-job-title">-</div>
        </div>

        <form id="upload-form" method="POST" action="" enctype="multipart/form-data" data-ajax-upload class="space-y-4">
            @csrf

            <x-upload-progress />

            <div class="upload-fields-container space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase text-slate-900 mb-1">Tipe File</label>
                    <select name="file_type" required class="neo-select">
                        <option value="final_print" selected>File Cetak Siap Produksi (Otomatis Ubah Status ke Siap Cetak)</option>
                        <option value="preview_proof">Preview Proofing ke Pelanggan</option>
                        <option value="brief_ref">Referensi Brief Tambahan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-slate-900 mb-1">Pilih File (PDF, TIFF, JPG, CDR, PSD, ZIP)</label>
                    <input type="file" name="file" required class="neo-input text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-[1.5px] file:border-slate-800 file:bg-amber-300 file:text-xs file:font-black">
                    <p class="text-[11px] text-slate-500 font-medium mt-1">
                        Aset otomatis tersimpan aman di cloud storage.
                    </p>
                </div>

                <div class="pt-3 border-t-[1.5px] border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('upload-dialog').close()" class="neo-btn bg-slate-200 text-xs py-2 px-4 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs py-2 px-4 font-black flex items-center gap-2 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a]">
                        <x-icon name="upload" class="w-3.5 h-3.5" />
                        <span>Unggah & Sinkronkan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</dialog>

@push('scripts')
<script>
function openUploadDialog(jobId, orderNumber, jobTitle) {
    const form = document.getElementById('upload-form');
    if (window.resetUploadForm) window.resetUploadForm(form);
    document.getElementById('modal-order-number').innerText = orderNumber;
    document.getElementById('modal-job-title').innerText = jobTitle;
    form.action = "/workdesk/" + jobId + "/upload";
    document.getElementById('upload-dialog').showModal();
}
</script>
@endpush
@endsection
