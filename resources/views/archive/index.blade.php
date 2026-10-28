@extends('layouts.app')

@section('title', 'Pusat Arsip Desain Terpusat')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-[1.5px] border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pusat Arsip Desain Terpusat</h1>
            <p class="text-xs font-semibold text-slate-500 mt-1">
                Katalog berkas cetak siap produksi dan riwayat proofing lintas cabang.
            </p>
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
        <div class="inline-flex items-center divide-x-[1.5px] divide-slate-800 bg-white border-[1.5px] border-slate-800 rounded-xl shadow-[2px_2px_0px_0px_#0f172a] text-xs self-start sm:self-auto overflow-hidden shrink-0">
            <div class="flex items-center gap-2 px-3.5 py-2 bg-white">
                <x-icon name="file" class="w-3.5 h-3.5 text-slate-500" />
                <span class="text-[11px] font-bold text-slate-500">Total File:</span>
                <span class="font-black text-slate-900 font-mono">{{ $totalCount }}</span>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-2 bg-amber-100/70">
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
                           placeholder="Nama file, No. Order (ORD-...), Customer..."
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($assets as $asset)
        <div class="neo-box p-4 bg-white flex flex-col justify-between hover:translate-y-[-2px] transition-transform">
            <div>
                <!-- Top Badge & Size -->
                <div class="flex items-center justify-between pb-2.5 border-b-[1.5px] border-slate-200 mb-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg border-[1.5px] border-slate-800 text-[10px] font-black uppercase shadow-[1px_1px_0px_0px_#0f172a] {{ $asset->file_type === 'final_print' ? 'bg-emerald-300 text-slate-950' : ($asset->file_type === 'preview_proof' ? 'bg-sky-200 text-slate-950' : 'bg-amber-200 text-slate-950') }}">
                        @if($asset->file_type === 'final_print')
                            <x-icon name="printer" class="w-3 h-3 text-slate-950" />
                        @elseif($asset->file_type === 'preview_proof')
                            <x-icon name="eye" class="w-3 h-3 text-slate-950" />
                        @else
                            <x-icon name="file-text" class="w-3 h-3 text-slate-950" />
                        @endif
                        <span>{{ $asset->file_type_badge }}</span>
                    </span>
                    <span class="text-xs font-bold text-slate-600 font-mono bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                        {{ $asset->formatted_size }}
                    </span>
                </div>

                <!-- File Name with Document Icon -->
                <div class="flex items-start gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 border border-slate-300 flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon name="file" class="w-4 h-4 text-slate-600" />
                    </div>
                    <h3 class="font-mono font-bold text-xs text-slate-900 break-words line-clamp-2 leading-relaxed" title="{{ $asset->file_name }}">
                        {{ $asset->file_name }}
                    </h3>
                </div>

                <!-- Metadata Card -->
                <div class="p-3 bg-slate-50/80 border-[1.5px] border-slate-200 rounded-xl text-xs space-y-1.5 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">No. Order:</span>
                        <a href="{{ route('orders.show', $asset->designJob->order_id ?? 0) }}" class="font-mono font-black text-blue-700 underline hover:text-blue-900">
                            {{ $asset->designJob->order->order_number ?? '-' }}
                        </a>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Customer:</span>
                        <span class="font-bold text-slate-900 truncate max-w-[160px]">{{ $asset->designJob->order->customer_name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Cabang:</span>
                        <span class="font-semibold text-slate-700 truncate max-w-[160px]">{{ $asset->designJob->order->branch->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-200/70 text-slate-500">
                        <span>Diunggah:</span>
                        <span class="font-medium">{{ $asset->uploader->name ?? '-' }} &bull; {{ $asset->created_at->format('d M') }}</span>
                    </div>
                </div>

            </div>

            <!-- Action Buttons: Clean 2-Tier Structure -->
            <div class="pt-3 border-t-[1.5px] border-slate-200 space-y-2">
                <!-- Primary Action Row -->
                <div class="flex items-center gap-2">
                    @if($asset->file_type === 'final_print')
                    <button type="button" 
                            onclick="openVendorDispatchModal({
                                assetId: {{ $asset->id }},
                                fileName: '{{ addslashes($asset->file_name) }}',
                                orderNumber: '{{ addslashes($asset->designJob->order->order_number ?? '') }}',
                                jobTitle: '{{ addslashes($asset->designJob->title ?? $asset->designJob->order->customer_name ?? '') }}',
                                dimensions: '{{ addslashes($asset->designJob->dimensions ?? '') }}',
                                materialNotes: '{{ addslashes($asset->designJob->material_notes ?? '') }}',
                                briefNotes: '{{ addslashes($asset->designJob->brief_notes ?? '') }}',
                                shareUrl: '{{ $asset->share_url }}'
                            })"
                            title="Kirim ke Rekanan Percetakan"
                            class="flex-1 neo-btn bg-amber-200 hover:bg-amber-300 text-slate-900 text-xs py-1.5 px-2.5 font-black shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center justify-center gap-1.5 cursor-pointer">
                        <x-icon name="send" class="w-3.5 h-3.5 text-slate-800" />
                        <span>Kirim Percetakan</span>
                    </button>
                    @endif

                    <a href="{{ route('archive.download', $asset->id) }}" 
                       title="Download File" 
                       class="{{ $asset->file_type === 'final_print' ? 'flex-1' : 'w-full' }} neo-btn bg-emerald-300 hover:bg-emerald-400 text-slate-950 text-xs py-1.5 px-2.5 font-black shadow-[1.5px_1.5px_0px_0px_#0f172a] flex items-center justify-center gap-1.5">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        <span>Download</span>
                    </a>
                </div>

                <!-- Secondary Action Links -->
                <div class="flex items-center justify-between px-1 pt-0.5 text-xs">
                    <button type="button" 
                            onclick="openFilePreview('{{ route('archive.preview', $asset->id) }}', '{{ addslashes($asset->file_name) }}', '{{ $asset->formatted_size }}', '{{ $asset->file_type_badge }}', '{{ str_ends_with(strtolower($asset->file_name), '.pdf') ? 'pdf' : ($asset->is_image ? 'image' : 'other') }}', '{{ route('archive.download', $asset->id) }}')"
                            class="text-slate-600 hover:text-slate-950 font-bold flex items-center gap-1 cursor-pointer transition-colors">
                        <x-icon name="eye" class="w-3.5 h-3.5 text-slate-500" />
                        <span>Pratinjau</span>
                    </button>

                    <a href="{{ route('orders.show', $asset->designJob->order_id ?? 0) }}" 
                       class="text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1 hover:underline transition-colors">
                        <span>Buka Tiket &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center neo-box bg-white">
            <div class="w-12 h-12 bg-amber-100 border-[1.5px] border-slate-900 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-[2px_2px_0px_0px_#0f172a]">
                <x-icon name="file" class="w-6 h-6 text-amber-800" />
            </div>
            <h3 class="font-black text-lg text-slate-900 mb-1">Belum Ada File Desain di Arsip</h3>
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
