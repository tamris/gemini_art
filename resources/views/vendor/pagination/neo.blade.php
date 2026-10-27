@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 bg-slate-50 border-t border-slate-200">
        
        <!-- Left: Summary Counter -->
        <div class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
            <x-icon name="clipboard-list" class="w-3.5 h-3.5 text-slate-400" />
            <span>
                Menampilkan 
                <span class="font-black text-slate-900">{{ $paginator->firstItem() ?? 0 }}</span>
                sampai 
                <span class="font-black text-slate-900">{{ $paginator->lastItem() ?? 0 }}</span>
                dari 
                <span class="font-black text-slate-900">{{ $paginator->total() }}</span> 
                total data
            </span>
        </div>

        <!-- Right: Pagination Buttons or Single Page Indicator -->
        @if ($paginator->hasPages())
            <div class="flex items-center gap-1.5 flex-wrap">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" class="px-2.5 py-1 text-xs font-bold text-slate-400 bg-slate-100 border border-slate-200 rounded-lg cursor-not-allowed select-none">
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-2.5 py-1 text-xs font-black text-slate-900 bg-white hover:bg-slate-100 border-[1.5px] border-slate-800 rounded-lg shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all">
                        Sebelumnya
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true" class="px-2 py-1 text-xs font-black text-slate-400 select-none">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="w-7 h-7 flex items-center justify-center text-xs font-black text-slate-900 bg-amber-300 border-[1.5px] border-slate-800 rounded-lg shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-7 h-7 flex items-center justify-center text-xs font-bold text-slate-700 bg-white hover:bg-amber-100 border-[1.5px] border-slate-800 rounded-lg shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-2.5 py-1 text-xs font-black text-slate-900 bg-white hover:bg-slate-100 border-[1.5px] border-slate-800 rounded-lg shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all">
                        Selanjutnya
                    </a>
                @else
                    <span aria-disabled="true" class="px-2.5 py-1 text-xs font-bold text-slate-400 bg-slate-100 border border-slate-200 rounded-lg cursor-not-allowed select-none">
                        Selanjutnya
                    </span>
                @endif
            </div>
        @else
            {{-- Single Page Indicator --}}
            <div class="flex items-center gap-1.5">
                <span class="w-6 h-6 flex items-center justify-center text-xs font-black text-slate-900 bg-amber-200 border-[1.5px] border-slate-800 rounded-md shadow-[1px_1px_0px_0px_#0f172a]">
                    1
                </span>
                <span class="text-[11px] font-bold text-slate-500">
                    Halaman 1 dari 1
                </span>
            </div>
        @endif

    </nav>
@endif
