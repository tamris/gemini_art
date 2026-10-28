@extends('layouts.app')

@section('title', 'Pusat Arsip Desain Terpusat (Cloud Storage)')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black">
        <div>
            <!-- <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                <span class="text-xs font-black uppercase tracking-wider text-amber-900">
                    SOP Tahap 3 & 6: Sinkronisasi Arsip File Cetak Cloud (S3/R2)
                </span>
            </div> -->
            <h1 class="text-2xl font-black text-black">Pusat Arsip File Desain Terpusat</h1>
            <!-- <p class="text-xs font-semibold text-slate-600 mt-0.5">
                Penyimpanan terstruktur multi-cabang tanpa rekap manual email. File cetak langsung siap diakses Owner dan tim produksi.
            </p> -->
        </div>

        @php
            if ($totalBytes >= 1073741824) {
                $fmtTotal = number_format($totalBytes / 1073741824, 2) . ' GB';
            } elseif ($totalBytes >= 1048576) {
                $fmtTotal = number_format($totalBytes / 1048576, 2) . ' MB';
            } else {
                $fmtTotal = number_format($totalBytes / 1024, 2) . ' KB';
            }
        @endphp

        <!-- Storage Stat Capsule (Compact & Space-Efficient) -->
        <div class="inline-flex items-center divide-x-2 divide-slate-900 bg-white border-2 border-slate-900 rounded-xl shadow-[2px_2px_0px_0px_#0f172a] text-xs self-start sm:self-auto overflow-hidden shrink-0">
            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white">
                <x-icon name="file" class="w-3.5 h-3.5 text-slate-500" />
                <span class="text-[11px] font-bold text-slate-500">Total File:</span>
                <span class="font-black text-slate-900 font-mono">{{ $totalCount }}</span>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-100">
                <x-icon name="hard-drive" class="w-3.5 h-3.5 text-amber-800" />
                <span class="text-[11px] font-bold text-amber-900">Storage Terpakai:</span>
                <span class="font-black text-amber-950 font-mono">{{ $fmtTotal }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Card (Instant Auto-Filter) -->
    <div class="neo-box p-4 bg-white relative overflow-visible z-20">
        <form method="GET" action="{{ route('archive.index') }}" data-auto-submit class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Cari File / Order</label>
                <div class="flex items-center rounded-xl border-[1.5px] border-slate-300 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] focus-within:border-slate-800 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] bg-white transition-all overflow-hidden">
                    <button type="submit" aria-label="Cari" class="pl-3 pr-2 text-slate-400 hover:text-slate-800 flex items-center justify-center shrink-0 cursor-pointer transition-colors">
                        <x-icon name="search" class="w-3.5 h-3.5" />
                    </button>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Nama file, No. Order (ORD-...), Customer... (Tekan Enter)"
                           class="w-full py-2 pr-3 text-xs font-bold text-slate-800 placeholder:text-slate-400 outline-none bg-transparent">
                </div>
            </div>

            @if(Auth::user()->isOwner())
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Cabang</label>
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

            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Tipe File</label>
                <select name="file_type" class="neo-select">
                    <option value="">Semua Tipe File</option>
                    <option value="final_print" {{ request('file_type') == 'final_print' ? 'selected' : '' }}>File Cetak Final (Print Ready)</option>
                    <option value="preview_proof" {{ request('file_type') == 'preview_proof' ? 'selected' : '' }}>Preview / Proofing</option>
                    <option value="brief_ref" {{ request('file_type') == 'brief_ref' ? 'selected' : '' }}>Referensi Brief</option>
                </select>
            </div>

            <div>
                <a href="{{ route('archive.index') }}" class="w-full neo-btn {{ request()->anyFilled(['search', 'branch_id', 'file_type']) ? 'bg-amber-200 hover:bg-amber-300 text-slate-900 border-slate-800 font-black' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold' }} text-xs py-2 px-3 shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center justify-center gap-1.5 transition-all">
                    <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                    <span>Reset Filter</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Filter Results Container (Live Smooth Replacement) -->
    <div id="filter-results-container" class="space-y-6">
        <!-- Assets Gallery / Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($assets as $asset)
        <div class="neo-box p-5 bg-white flex flex-col justify-between hover:translate-y-[-2px] transition-transform">
            <div>
                <!-- Top Badge & Size -->
                <div class="flex items-center justify-between pb-3 border-b-2 border-black mb-3">
                    <span class="px-2 py-0.5 rounded border border-black text-[10px] font-black uppercase {{ $asset->file_type === 'final_print' ? 'bg-emerald-300' : ($asset->file_type === 'preview_proof' ? 'bg-sky-200' : 'bg-amber-200') }}">
                        {{ $asset->file_type_badge }}
                    </span>
                    <span class="text-xs font-bold text-slate-500 font-mono">{{ $asset->formatted_size }}</span>
                </div>

                <!-- File Name & Order Number -->
                <h3 class="font-mono font-bold text-xs text-black break-all mb-2 leading-relaxed">
                    {{ $asset->file_name }}
                </h3>

                <div class="p-3 bg-slate-50 border-2 border-black rounded-xl text-xs space-y-1 mb-3">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Order:</span>
                        <a href="{{ route('orders.show', $asset->designJob->order_id ?? 0) }}" class="font-mono font-black underline hover:text-blue-700">
                            {{ $asset->designJob->order->order_number ?? '-' }}
                        </a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Cabang:</span>
                        <span class="font-bold">{{ $asset->designJob->order->branch->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Customer:</span>
                        <span class="font-bold text-black">{{ $asset->designJob->order->customer_name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Uploader:</span>
                        <span class="font-semibold">{{ $asset->uploader->name ?? '-' }}</span>
                    </div>
                </div>

            </div>

            <!-- Action Buttons: Preview Eye + Download + Tiket -->
            <div class="pt-3 border-t-2 border-slate-900 flex items-center gap-2">
                <button type="button" 
                        onclick="openFilePreview('{{ route('archive.preview', $asset->id) }}', '{{ addslashes($asset->file_name) }}', '{{ $asset->formatted_size }}', '{{ $asset->file_type_badge }}', '{{ str_ends_with(strtolower($asset->file_name), '.pdf') ? 'pdf' : ($asset->is_image ? 'image' : 'other') }}', '{{ route('archive.download', $asset->id) }}')"
                        title="Pratinjau File (Preview)"
                        class="w-9 h-9 rounded-xl border-[1.5px] border-slate-900 bg-sky-200 hover:bg-sky-300 text-slate-900 shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none flex items-center justify-center shrink-0 cursor-pointer transition-all">
                    <svg class="w-4 h-4 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
                <a href="{{ route('archive.download', $asset->id) }}" title="Download File" class="flex-1 neo-btn bg-emerald-300 hover:bg-emerald-400 text-xs py-2 text-center font-black text-slate-950 flex items-center justify-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                    <x-icon name="download" class="w-3.5 h-3.5" />
                    <span>Download</span>
                </a>
                <a href="{{ route('orders.show', $asset->designJob->order_id ?? 0) }}" title="Lihat Detail Tiket" class="neo-btn bg-white hover:bg-slate-100 text-xs py-2 px-3 font-bold text-slate-800 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                    Tiket
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center neo-box bg-white">
            <div class="w-12 h-12 bg-amber-100 border-2 border-black rounded-xl flex items-center justify-center mx-auto mb-3 shadow-[2px_2px_0px_0px_#000]">
                <svg class="w-6 h-6 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
            </div>
            <h3 class="font-black text-lg text-black mb-1">Belum Ada File Desain di Arsip</h3>
            <p class="text-xs font-medium text-slate-500">
                File siap cetak yang diunggah desainer dari workdesk akan otomatis tampil di sini.
            </p>
        </div>
        @endforelse
    </div>

    <div class="neo-box overflow-hidden bg-white shadow-[2px_2px_0px_0px_#0f172a]">
        {{ $assets->links() }}
    </div>
    </div>

</div>
@endsection
