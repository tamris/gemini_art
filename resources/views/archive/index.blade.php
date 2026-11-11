@extends('layouts.app')

@section('title', 'Pusat Arsip Desain Terpusat')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-[1.5px] border-slate-800 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                @if(Auth::user()->isOwner())
                    Pusat Arsip Desain Terpusat
                @else
                    Arsip Desain &bull; {{ Auth::user()->branch?->clean_name }}
                @endif
            </h1>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">
                @if(Auth::user()->isOwner())
                    Katalog berkas cetak siap produksi dan riwayat proofing lintas cabang.
                @else
                    Katalog berkas cetak siap produksi dan riwayat proofing Cabang {{ Auth::user()->branch?->clean_name }}.
                @endif
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
        <div class="inline-flex items-center divide-x-[1.5px] divide-slate-800 dark:divide-slate-700 bg-white dark:bg-[#0c1222] border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000] text-xs self-start sm:self-auto overflow-hidden shrink-0">
            <div class="flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-[#0c1222]">
                <x-icon name="file" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Total File:</span>
                <span class="font-black text-slate-900 dark:text-white font-mono">{{ $totalCount }}</span>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-2 bg-amber-100/70 dark:bg-amber-950/40">
                <x-icon name="hard-drive" class="w-3.5 h-3.5 text-amber-800 dark:text-amber-400" />
                <span class="text-[11px] font-bold text-amber-900 dark:text-amber-400">Storage Terpakai:</span>
                <span class="font-black text-amber-950 dark:text-white font-mono">{{ $fmtTotal }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Card (Instant Auto-Filter) -->
    <div class="neo-box p-4 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] relative overflow-visible z-20">
        <form method="GET" action="{{ route('archive.index') }}" data-auto-submit class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Cari File / Order</label>
                <div class="flex items-center h-10 rounded-xl border-[1.5px] border-slate-300 dark:border-slate-700 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] focus-within:border-slate-800 dark:focus-within:border-amber-400 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] bg-white dark:bg-slate-900 transition-all overflow-hidden">
                    <button type="submit" aria-label="Cari" class="h-full pl-3 pr-2 text-slate-400 dark:text-slate-500 hover:text-slate-800 dark:hover:text-amber-400 flex items-center justify-center shrink-0 cursor-pointer transition-colors">
                        <x-icon name="search" class="w-3.5 h-3.5" />
                    </button>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Nama file, No. Order (ORD-...), Customer..."
                           autocomplete="off"
                           class="w-full h-full pr-2 text-xs font-bold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 outline-none bg-transparent">
                    <button type="button" 
                            data-clear-search
                            title="Hapus pencarian" 
                            class="{{ request('search') ? '' : 'hidden' }} h-full pr-3 pl-1 text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 flex items-center justify-center shrink-0 cursor-pointer transition-colors">
                        <x-icon name="x" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            @if(Auth::user()->isOwner())
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Cabang</label>
                @php
                    $archiveBranchOpts = ['' => 'Semua Cabang'];
                    foreach($branches as $b) {
                        $archiveBranchOpts[$b->id] = $b->code . ' - ' . $b->name;
                    }
                @endphp
                <x-neo-select name="branch_id" :options="$archiveBranchOpts" :value="request('branch_id')" />
            </div>
            @endif

            <div>
                <label class="block text-[11px] font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">Tipe File</label>
                <x-neo-select 
                    name="file_type" 
                    :value="request('file_type')"
                    :options="[
                        '' => 'Semua Tipe File',
                        'final_print' => 'File Cetak Final (Print Ready)',
                        'preview_proof' => 'Preview / Proofing',
                        'brief_ref' => 'Referensi Brief',
                    ]" 
                />
            </div>

            <div>
                <a href="{{ route('archive.index') }}" class="w-full h-10 neo-btn {{ request()->anyFilled(['search', 'branch_id', 'file_type']) ? 'bg-amber-200 dark:bg-amber-300 dark:text-slate-950 hover:bg-amber-300 text-slate-900 border-slate-800 font-black' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 text-slate-600 font-bold dark:border-slate-700' }} text-xs px-3 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] flex items-center justify-center gap-1.5 transition-all">
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
        <div class="neo-box p-4 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] flex flex-col justify-between hover:translate-y-[-2px] transition-transform">
            <div>
                <!-- Top Badge & Size -->
                <div class="flex items-center justify-between pb-2.5 border-b-[1.5px] border-slate-200 dark:border-slate-800 mb-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 text-[10px] font-black uppercase shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] {{ $asset->file_type === 'final_print' ? 'bg-emerald-300 text-slate-950' : ($asset->file_type === 'preview_proof' ? 'bg-sky-200 text-slate-950' : 'bg-amber-200 text-slate-950') }}">
                        @if($asset->file_type === 'final_print')
                            <x-icon name="printer" class="w-3 h-3 text-slate-950" />
                        @elseif($asset->file_type === 'preview_proof')
                            <x-icon name="eye" class="w-3 h-3 text-slate-950" />
                        @else
                            <x-icon name="file-text" class="w-3 h-3 text-slate-950" />
                        @endif
                        <span>{{ $asset->file_type_badge }}</span>
                    </span>
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300 font-mono bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700">
                        {{ $asset->formatted_size }}
                    </span>
                </div>

                <!-- File Name with Document Icon -->
                <div class="flex items-start gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon name="file" class="w-4 h-4 text-slate-600 dark:text-slate-300" />
                    </div>
                    <h3 class="font-mono font-bold text-xs text-slate-900 dark:text-white break-words line-clamp-2 leading-relaxed" title="{{ $asset->file_name }}">
                        {{ $asset->file_name }}
                    </h3>
                </div>

                <!-- Metadata Card -->
                <div class="p-3 bg-slate-50/80 dark:bg-slate-900/90 border-[1.5px] border-slate-200 dark:border-slate-800 rounded-xl text-xs space-y-1.5 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">No. Order:</span>
                        <a href="{{ route('orders.show', $asset->designJob->order_id ?? 0) }}" class="font-mono font-black text-blue-700 dark:text-sky-400 underline hover:text-blue-900 dark:hover:text-amber-400">
                            {{ $asset->designJob->order->order_number ?? '-' }}
                        </a>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Customer:</span>
                        <span class="font-bold text-slate-900 dark:text-white truncate max-w-[160px]">{{ $asset->designJob->order->customer_name ?? '-' }}</span>
                    </div>
                    @if(Auth::user()->isOwner())
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Cabang:</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[160px]">{{ $asset->designJob->order->branch->name ?? '-' }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-200/70 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                        <span>Diunggah:</span>
                        <span class="font-medium">{{ $asset->uploader->name ?? '-' }} &bull; {{ $asset->created_at->format('d M') }}</span>
                    </div>
                </div>

            </div>

            <!-- Action Buttons: Clean 2-Tier Structure -->
            <div class="pt-3 border-t-[1.5px] border-slate-200 dark:border-slate-800 space-y-2">
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
                            class="flex-1 neo-btn bg-amber-200 hover:bg-amber-300 text-slate-900 dark:text-slate-950 text-xs py-1.5 px-2.5 font-black shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] dark:border-slate-700 flex items-center justify-center gap-1.5 cursor-pointer">
                        <x-icon name="send" class="w-3.5 h-3.5 text-slate-800 dark:text-slate-950" />
                        <span>Kirim Percetakan</span>
                    </button>
                    @endif

                    <a href="{{ route('archive.download', $asset->id) }}" 
                       title="Download File" 
                       class="{{ $asset->file_type === 'final_print' ? 'flex-1' : 'w-full' }} neo-btn bg-emerald-300 hover:bg-emerald-400 text-slate-950 text-xs py-1.5 px-2.5 font-black shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] dark:border-slate-700 flex items-center justify-center gap-1.5">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        <span>Download</span>
                    </a>
                </div>

                <!-- Secondary Action Links -->
                <div class="flex items-center justify-between gap-2 px-1 pt-0.5 text-xs">
                    <div class="flex items-center gap-3">
                        <button type="button" 
                                onclick="openFilePreview('{{ route('archive.preview', $asset->id) }}', '{{ addslashes($asset->file_name) }}', '{{ $asset->formatted_size }}', '{{ $asset->file_type_badge }}', '{{ str_ends_with(strtolower($asset->file_name), '.pdf') ? 'pdf' : ($asset->is_image ? 'image' : 'other') }}', '{{ route('archive.download', $asset->id) }}')"
                                class="text-slate-600 dark:text-slate-400 hover:text-slate-950 dark:hover:text-white font-bold flex items-center gap-1 cursor-pointer transition-colors">
                            <x-icon name="eye" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
                            <span>Pratinjau</span>
                        </button>

                        @if($asset->designJob?->order && !$asset->designJob->order->isLocked())
                        @php
                            $archiveOrder = $asset->designJob->order;
                            $archiveFinalCount = $asset->designJob->assets->where('file_type', 'final_print')->count();
                            $archiveWillRevert = $asset->file_type === 'final_print'
                                && $archiveOrder->work_status === 'ready_to_print'
                                && $archiveFinalCount <= 1;
                        @endphp
                        <button type="button"
                                onclick="openDeleteAssetModal({
                                    action: '{{ route('archive.destroy', $asset->id) }}',
                                    fileName: '{{ addslashes($asset->file_name) }}',
                                    fileSize: '{{ $asset->formatted_size }}',
                                    willRevertStatus: {{ $archiveWillRevert ? 'true' : 'false' }}
                                })"
                                class="text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 font-bold flex items-center gap-1 cursor-pointer transition-colors">
                            <x-icon name="trash2" class="w-3.5 h-3.5" />
                            <span>Hapus</span>
                        </button>
                        @endif
                    </div>

                    <a href="{{ route('orders.show', $asset->designJob->order_id ?? 0) }}" 
                       class="text-blue-600 dark:text-sky-400 hover:text-blue-800 dark:hover:text-sky-300 font-bold flex items-center gap-1 hover:underline transition-colors shrink-0">
                        <span>Buka Tiket &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center neo-box bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000]">
            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-950/40 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]">
                <x-icon name="file" class="w-6 h-6 text-amber-800 dark:text-amber-400" />
            </div>
            <h3 class="font-black text-lg text-slate-900 dark:text-white mb-1">Belum Ada File Desain di Arsip</h3>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                File siap cetak yang diunggah desainer dari workdesk akan otomatis tampil di sini.
            </p>
        </div>
        @endforelse
    </div>

    <div class="neo-box overflow-hidden bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000]">
        {{ $assets->links() }}
    </div>
    </div>

</div>
@endsection
