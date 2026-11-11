@extends('layouts.app')

@section('title', 'Buat Tiket Order Desain Baru')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-black dark:text-white">Input Tiket Order Desain & Cetak</h1>
        </div>
    </div>

    <!-- Main Split Form: Left Specs (8 cols), Right Payment & Actions (4 cols) -->
    <form method="POST" action="{{ route('orders.store-design') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Column: Specs & Customer Data (8 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Card 1: Data Pelanggan & Cabang Pelaksana -->
                <div class="neo-box p-5 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
                    <h2 class="text-sm font-black text-slate-800 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-amber-200 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                                <x-icon name="user" class="w-4 h-4" />
                            </div>
                            <span>1. Data Pelanggan</span>
                        </span>
                        <span class="text-[10px] font-black text-amber-950 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-400/40 px-2 py-0.5 rounded">
                            Wajib Diisi
                        </span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        @if(Auth::user()->isOwner())
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                Cabang Pelaksana <span class="text-rose-500">*</span>
                            </label>
                            @php
                                $createBranchOpts = [];
                                foreach($branches as $b) {
                                    $createBranchOpts[$b->id] = $b->code . ' - ' . $b->name;
                                }
                            @endphp
                            <x-neo-select name="branch_id" :options="$createBranchOpts" :value="old('branch_id', Auth::user()->branch_id)" required />
                        </div>
                        @else
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                Cabang Pelaksana
                            </label>
                            <input type="text" disabled value="{{ Auth::user()->branch->name ?? 'Cabang Saya' }}" class="w-full h-10 neo-input text-xs bg-slate-100 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 cursor-not-allowed">
                        </div>
                        @endif

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                Nama Customer / Instansi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="customer_name" 
                                   value="{{ old('customer_name') }}" 
                                   required 
                                   placeholder="Misal: Bpk. Hendra / Kopi Nusantara"
                                   class="w-full h-10 neo-input text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                No. WhatsApp / Telepon
                            </label>
                            <input type="tel" 
                                   id="customer_phone"
                                   name="customer_phone" 
                                   data-phone-mask
                                   value="{{ old('customer_phone') }}" 
                                   placeholder="0823-2323-2323"
                                   maxlength="16"
                                   autocomplete="tel"
                                   class="w-full h-10 neo-input text-xs font-bold tracking-wider">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                Tugaskan ke Desainer
                            </label>
                            @php
                                $designerOpts = ['' => 'Antrean Umum Cabang (Draft)'];
                                foreach($designers as $d) {
                                    $designerOpts[$d->id] = $d->name . ' (' . ($d->branch ? $d->branch->code : 'Pusat') . ')';
                                }
                            @endphp
                            <x-neo-select name="designer_id" :options="$designerOpts" :value="old('designer_id', '')" />
                        </div>

                    </div>
                </div>

                <!-- Card 2: Spesifikasi Pengerjaan & Brief Desain -->
                <div class="neo-box p-5 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
                    <h2 class="text-sm font-black text-slate-800 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-200 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                                <x-icon name="palette" class="w-4 h-4" />
                            </div>
                            <span>2. Spesifikasi & Brief Desain</span>
                        </span>
                        <span class="text-[10px] font-black text-blue-950 dark:text-sky-300 bg-blue-100 dark:bg-sky-950/40 border border-blue-300 dark:border-sky-400/40 px-2 py-0.5 rounded">
                            Detail Teknis
                        </span>
                    </h2>

                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                Judul / Nama Desain <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required 
                                   placeholder="Misal: Spanduk Warung, Kartu Nama, Brosur..."
                                   class="w-full h-10 neo-input text-xs">
                        </div>

                        <!-- Mode Hitung Produk (Kalkulator Meteran vs Satuan) -->
                        <div class="p-3.5 bg-amber-50/70 dark:bg-slate-900/70 border-[1.5px] border-amber-300 dark:border-slate-700 rounded-xl space-y-3 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                            <input type="hidden" id="calc_mode" name="calc_mode" value="{{ old('calc_mode', 'meter') }}">
                            <input type="hidden" id="main_unit" name="main_unit" value="{{ old('main_unit', 'Meter') }}">

                            <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-200 tracking-wider flex items-center gap-1.5">
                                <x-icon name="calculator" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                                <span>Mode Hitung Otomatis:</span>
                            </label>

                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" 
                                        id="btn-mode-meter"
                                        onclick="setCalcMode('meter')"
                                        class="py-2 px-3 rounded-lg border-2 border-slate-900 dark:border-slate-700 font-black text-xs flex items-center justify-center gap-1.5 cursor-pointer transition-all bg-amber-300 text-slate-950 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                                    <x-icon name="maximize" class="w-3.5 h-3.5" />
                                    <span>Cetak Meteran</span>
                                </button>
                                <button type="button" 
                                        id="btn-mode-pcs"
                                        onclick="setCalcMode('pcs')"
                                        class="py-2 px-3 rounded-lg border-[1.5px] border-slate-300 dark:border-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 cursor-pointer transition-all bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-800">
                                    <x-icon name="box" class="w-3.5 h-3.5" />
                                    <span>Cetak Satuan</span>
                                </button>
                            </div>

                            <!-- Panel Mode Meteran: Input Panjang & Lebar Otomatis -->
                            <div id="panel-mode-meter" class="space-y-3 pt-2.5 border-t border-amber-200 dark:border-slate-800">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                    <div>
                                        <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider truncate">
                                            Panjang (Meter)
                                        </label>
                                        <input type="number" 
                                               id="calc_length" 
                                               step="0.05" 
                                               min="0" 
                                               value="{{ old('calc_length', '') }}" 
                                               placeholder="0" 
                                               oninput="handleMeterDimensionChange()"
                                               class="w-full h-10 neo-input text-xs font-black bg-white dark:bg-slate-950 text-center">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider truncate">
                                            Lebar / Tinggi (Meter)
                                        </label>
                                        <input type="number" 
                                               id="calc_width" 
                                               step="0.05" 
                                               min="0" 
                                               value="{{ old('calc_width', '') }}" 
                                               placeholder="0" 
                                               oninput="handleMeterDimensionChange()"
                                               class="w-full h-10 neo-input text-xs font-black bg-white dark:bg-slate-950 text-center">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider truncate">
                                            Total Luas Hitung
                                        </label>
                                        <div class="w-full h-10 px-3.5 rounded-xl border-[1.5px] border-slate-300 dark:border-slate-700 bg-amber-50/90 dark:bg-amber-950/40 flex items-center justify-between shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
                                            <span id="display-area-single" class="text-xs font-black text-slate-900 dark:text-amber-200">0.00 m²</span>
                                            <span id="display-area-total" class="text-[10px] font-bold text-amber-700 dark:text-amber-400"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="grid-dimensions-material" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div id="wrapper-dimensions" class="hidden">
                                <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                    Dimensi / Catatan Ukuran
                                </label>
                                <input type="text" 
                                       id="dimensions_input"
                                       name="dimensions" 
                                       value="{{ old('dimensions', '') }}" 
                                       placeholder="Misal: A4, 9x5.5 cm, Brosur Folio"
                                       class="w-full h-10 neo-input text-xs">
                            </div>

                            <div id="wrapper-material" class="sm:col-span-2">
                                <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                    Bahan & Finishing
                                </label>
                                <input type="text" 
                                       id="material_notes_input"
                                       name="material_notes" 
                                       value="{{ old('material_notes') }}" 
                                       placeholder="Misal: Flexi Korea 380gr, Art Carton 260gr"
                                       class="w-full h-10 neo-input text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Jumlah (Qty) -->
                            <div>
                                <label for="main_qty" class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                    Jumlah Cetak (Qty) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" 
                                       id="main_qty" 
                                       name="main_qty" 
                                       value="{{ old('main_qty', 1) }}" 
                                       min="1" 
                                       required
                                       placeholder="1"
                                       oninput="handleMainQtyChange()"
                                       class="w-full h-10 neo-input text-xs font-black bg-white dark:bg-slate-900">
                            </div>

                            <div class="relative" data-neo-datepicker-container>
                                <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                    Tenggat Waktu Selesai (Deadline) <span class="text-rose-500">*</span>
                                </label>

                                <input type="hidden" 
                                       id="deadline_at_hidden"
                                       name="deadline_at" 
                                       value="{{ old('deadline_at', date('Y-m-d H:i', strtotime('+1 day 17:00'))) }}">

                                <!-- Trigger Input Button -->
                                <button type="button" 
                                        data-datepicker-toggle
                                        class="w-full flex items-center justify-between neo-input text-xs font-bold h-10 px-3 bg-white dark:bg-slate-900 border-[1.5px] border-slate-300 dark:border-slate-700 hover:border-slate-800 dark:hover:border-amber-400 transition-all cursor-pointer shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] hover:shadow-[2px_2px_0px_0px_#0f172a] select-none text-left">
                                    <span data-datepicker-text class="font-extrabold text-xs text-slate-900 dark:text-white">
                                        {{ date('d/m/Y H.i', strtotime('+1 day 17:00')) }}
                                    </span>
                                    <x-icon name="calendar" class="w-4 h-4 text-slate-700 dark:text-slate-400 stroke-[2.5]" />
                                </button>

                                <!-- Popover Positioned ABOVE the Input (Identical to Native Layout, in Soft Neo-Brutalism) -->
                                <div data-datepicker-popover class="hidden absolute bottom-full mb-2 left-0 z-50 bg-white dark:bg-[#0c1222] border-2 border-slate-900 dark:border-slate-700 rounded-2xl shadow-[4px_4px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#000000] overflow-hidden select-none flex flex-row w-[360px]">
                                    
                                    <!-- Left Pane: Calendar (235px) -->
                                    <div class="w-[235px] flex flex-col bg-white dark:bg-[#0c1222] shrink-0">
                                        <!-- Month Header (h-10 / 40px) -->
                                        <div class="h-10 px-3 bg-amber-300 border-b-2 border-slate-900 dark:border-slate-700 flex items-center justify-between shrink-0">
                                            <button type="button" data-cal-prev class="w-6 h-6 rounded-md bg-white dark:bg-slate-800 border-[1.5px] border-slate-900 dark:border-slate-700 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] hover:bg-slate-50 flex items-center justify-center cursor-pointer active:translate-x-[0.5px] active:translate-y-[0.5px]">
                                                <x-icon name="chevron-left" class="w-3.5 h-3.5 text-slate-900 dark:text-white stroke-[2.5]" />
                                            </button>
                                            <div class="font-black text-xs text-slate-950 uppercase tracking-wider flex items-center gap-1.5">
                                                <span data-cal-month>Oktober</span>
                                                <span data-cal-year>2026</span>
                                            </div>
                                            <button type="button" data-cal-next class="w-6 h-6 rounded-md bg-white dark:bg-slate-800 border-[1.5px] border-slate-900 dark:border-slate-700 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] hover:bg-slate-50 flex items-center justify-center cursor-pointer active:translate-x-[0.5px] active:translate-y-[0.5px]">
                                                <x-icon name="chevron-right" class="w-3.5 h-3.5 text-slate-900 dark:text-white stroke-[2.5]" />
                                            </button>
                                        </div>

                                        <!-- Weekdays Row (h-6 / 24px) -->
                                        <div class="h-6 grid grid-cols-7 text-center items-center px-2 bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-[10px] font-black uppercase text-slate-600 dark:text-slate-400 shrink-0">
                                            <span>Min</span>
                                            <span>Sen</span>
                                            <span>Sel</span>
                                            <span>Rab</span>
                                            <span>Kam</span>
                                            <span>Jum</span>
                                            <span>Sab</span>
                                        </div>

                                        <!-- Days Grid (h-[204px], tight 6 rows x 7 cols) -->
                                        <div class="h-[204px] p-2 bg-white dark:bg-[#0c1222] shrink-0">
                                            <div data-cal-days class="grid grid-cols-7 gap-1">
                                                <!-- Dynamically generated day buttons -->
                                            </div>
                                        </div>

                                        <!-- Left Pane Footer: Reset & Hari Ini (h-10 / 40px) -->
                                        <div class="h-10 flex items-center justify-between px-3 border-t-2 border-slate-900 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-bold shrink-0">
                                            <button type="button" data-cal-reset class="text-xs font-black text-rose-600 hover:text-rose-800 dark:text-rose-400 hover:underline cursor-pointer">
                                                Reset
                                            </button>
                                            <button type="button" data-cal-today class="text-xs font-black text-slate-800 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white hover:underline cursor-pointer">
                                                Hari ini
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Right Pane: Time Columns (125px) -->
                                    <div class="w-[125px] border-l-2 border-slate-900 dark:border-slate-700 bg-white dark:bg-[#0c1222] flex flex-col shrink-0">
                                        <!-- Header Jam & Menit (h-10 / 40px) -->
                                        <div class="h-10 px-2 bg-amber-300 border-b-2 border-slate-900 dark:border-slate-700 flex items-center justify-center shrink-0">
                                            <div class="grid grid-cols-2 w-full text-center text-[10px] font-black uppercase text-slate-950 tracking-wider">
                                                <span>Jam</span>
                                                <span class="border-l border-slate-900/40">Menit</span>
                                            </div>
                                        </div>

                                        <!-- Subheader Jam & Menit (h-6 / 24px) -->
                                        <div class="h-6 bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-center shrink-0">
                                            <div class="grid grid-cols-2 w-full text-center text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase">
                                                <span>08-23</span>
                                                <span class="border-l border-slate-200 dark:border-slate-800">00-55</span>
                                            </div>
                                        </div>

                                        <!-- Two Vertical Scroll Columns (h-[204px]) -->
                                        <div class="h-[204px] flex bg-white dark:bg-[#0c1222] shrink-0">
                                            <!-- Hours column -->
                                            <div data-cal-hours class="w-1/2 h-full overflow-y-auto neo-time-scroll p-1 flex flex-col items-center space-y-0.5">
                                                <!-- Hours 08..23 -->
                                            </div>
                                            <!-- Minutes column -->
                                            <div data-cal-minutes class="w-1/2 h-full overflow-y-auto neo-time-scroll p-1 border-l border-slate-200 dark:border-slate-800 flex flex-col items-center space-y-0.5">
                                                <!-- Minutes 00..55 -->
                                            </div>
                                        </div>

                                        <!-- Footer: Selesai Button (h-10 / 40px) -->
                                        <div class="h-10 p-1.5 border-t-2 border-slate-900 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center shrink-0">
                                            <button type="button" data-cal-apply class="w-full h-7 neo-btn bg-emerald-400 hover:bg-emerald-300 text-xs font-black text-slate-950 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] rounded-lg cursor-pointer">
                                                Terapkan
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- <div class="sm:col-span-2">
                                <div class="flex items-center justify-between mb-1">
                                    <label for="brief_file" class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 tracking-wider cursor-pointer">
                                        File Acuan / Sketsa
                                    </label>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">Opsional (Sketsa/Logo)</span>
                                </div>
                                <div class="relative">
                                    <input type="file" 
                                           id="brief_file" 
                                           name="brief_file" 
                                           class="sr-only" 
                                           onchange="handleBriefFileChange(this)">
                                    <label for="brief_file" 
                                           class="flex items-center justify-between h-10 px-3 bg-white dark:bg-slate-900 border-[1.5px] border-slate-300 dark:border-slate-700 rounded-xl shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] hover:border-slate-800 dark:hover:border-amber-400 hover:shadow-[2px_2px_0px_0px_#0f172a] cursor-pointer transition-all select-none">
                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                            <x-icon name="file" class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                            <span id="brief_file_name" class="text-xs text-slate-400 dark:text-slate-500 font-medium truncate pr-1">
                                                Pilih file sketsa/logo...
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
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-200 hover:bg-amber-300 dark:bg-amber-300 dark:text-slate-950 border border-slate-700 rounded-lg text-[10px] font-black uppercase text-slate-900 shadow-[1px_1px_0px_0px_#0f172a]">
                                                <x-icon name="upload" class="w-3 h-3" />
                                                <span>Pilih File</span>
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </div> -->
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                Catatan Brief Desain
                            </label>
                            <textarea name="brief_notes" 
                                      rows="3" 
                                      placeholder="Tuliskan warna dominan, teks utama, kontak yang dicantumkan, logo, atau referensi gaya desain..."
                                      class="neo-input text-xs">{{ old('brief_notes') }}</textarea>
                        </div>

                    </div>
                </div>

                <!-- Card 3: Alat & Layanan Tambahan (Add-on Opsional) -->
                <div class="neo-box p-4 sm:p-5 bg-white dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-purple-200 dark:bg-purple-900/50 border-[1.5px] border-slate-800 dark:border-purple-500/50 flex items-center justify-center text-purple-950 dark:text-purple-300 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] shrink-0">
                                <x-icon name="plus-circle" class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-sm font-black text-slate-900 dark:text-white truncate">
                                    3. Alat & Layanan Tambahan
                                </h2>
                                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 truncate">
                                    Add-on display, finishing & jasa pasang
                                </p>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="addExtraItemRow()" 
                                class="neo-btn bg-purple-300 hover:bg-purple-400 dark:bg-purple-400 dark:hover:bg-purple-300 text-slate-950 text-xs py-1.5 px-3 font-black shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000] flex items-center gap-1.5 cursor-pointer shrink-0 border border-slate-900">
                            <x-icon name="plus" class="w-3.5 h-3.5 stroke-[2.5]" />
                            <span class="hidden sm:inline">Tambah Item</span>
                            <span class="sm:hidden">Tambah</span>
                        </button>
                    </div>

                    <!-- Items List Container -->
                    <div id="extra-items-container" class="space-y-2.5">
                        <div id="no-extra-items-msg" class="p-3.5 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-center text-xs text-slate-400 dark:text-slate-500 font-semibold bg-slate-50/50 dark:bg-slate-900/40">
                            <span class="text-slate-600 dark:text-slate-400 font-bold block mb-0.5">Belum ada alat atau item tambahan.</span>
                            <span>Klik tombol <strong>+ Tambah Item</strong> di atas jika customer membeli display, finishing, atau layanan ekstra.</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Payment & Commitments (4 cols, Sticky) -->
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

                <!-- Card 4: Biaya & Pembayaran Awal -->
                <div class="neo-box p-5 bg-amber-50/60 dark:bg-[#0c1222] border-2 border-black dark:border-slate-700 shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#000000] space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-amber-200/80 dark:border-slate-800">
                        <h2 class="text-sm font-black text-slate-800 dark:text-white flex items-center gap-2">
                            <div class="w-7 h-7 bg-emerald-200 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-lg flex items-center justify-center text-slate-900 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                                <x-icon name="wallet" class="w-4 h-4" />
                            </div>
                            <span>4. Biaya & Kas Awal</span>
                        </h2>
                        <!-- <span class="text-[10px] font-black text-emerald-950 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-400/40 px-2 py-0.5 rounded">
                            SOP Kasir
                        </span> -->
                    </div>

                    <div class="space-y-4">
                        <!-- 1. BIAYA JASA DESAIN (BAGIAN ATAS SESUAI REQUEST) -->
                        <div class="space-y-2 p-3.5 bg-sky-50/70 dark:bg-sky-950/30 border-[1.5px] border-sky-300 dark:border-sky-800/60 rounded-xl">
                            <div class="flex items-center justify-between">
                                <label for="design_fee_display" class="block text-xs font-black uppercase text-sky-950 dark:text-sky-300 tracking-wider flex items-center gap-1.5">
                                    <x-icon name="palette" class="w-3.5 h-3.5 text-sky-600 dark:text-sky-400" />
                                    <span>Biaya Jasa Desain</span>
                                </label>
                               
                            </div>

                            <div class="flex h-10 rounded-xl overflow-hidden border-[1.5px] border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] focus-within:border-sky-600 dark:focus-within:border-sky-400 transition-all">
                                <span class="h-full inline-flex items-center justify-center px-3.5 bg-sky-100 dark:bg-sky-900/60 text-sky-900 dark:text-sky-300 border-r-[1.5px] border-slate-300 dark:border-slate-700 text-xs font-black select-none whitespace-nowrap shrink-0">
                                    Rp
                                </span>
                                <input type="text" 
                                       id="design_fee_display" 
                                       inputmode="numeric"
                                       value="{{ old('design_fee') !== null ? (old('design_fee') == 0 ? '0' : number_format((float) old('design_fee'), 0, ',', '.')) : '0' }}" 
                                       placeholder="0"
                                       oninput="handleDesignFeeInput(this)"
                                       autocomplete="off"
                                       class="w-full h-full px-3 text-xs sm:text-sm font-bold text-slate-900 dark:text-white bg-transparent focus:outline-none placeholder:text-slate-300 dark:placeholder:text-slate-500 tracking-wide">
                            </div>
                            <input type="hidden" id="design_fee" name="design_fee" value="{{ old('design_fee', '0') }}">
                        </div>

                        <!-- 2. BIAYA CETAK & PRODUKSI (DARI KALKULATOR) -->
                        <div class="space-y-3 p-3.5 bg-slate-50 dark:bg-slate-900/60 border-[1.5px] border-slate-300 dark:border-slate-700 rounded-xl">
                            <div class="flex items-center justify-between pb-1 border-b border-slate-200 dark:border-slate-800">
                                <span class="text-[11px] font-black uppercase text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                    <x-icon name="printer" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                                    <span>Biaya Cetak & Modal Bahan</span>
                                </span>
                                <!-- <span id="print-calc-summary-badge" class="text-[10px] font-bold text-slate-500 dark:text-slate-400">
                                    0.00 m²
                                </span> -->
                            </div>

                            <!-- Tarif Cetak per Meter / per Pcs -->
                            <div>
                                <label for="main_unit_price_display" id="label-unit-price" class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                    Tarif Cetak per Meter (m²) <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex rounded-xl overflow-hidden border-[1.5px] border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] focus-within:border-slate-800 dark:focus-within:border-amber-400 transition-all">
                                    <span class="inline-flex items-center px-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-r-[1.5px] border-slate-300 dark:border-slate-700 text-xs font-black select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           id="main_unit_price_display" 
                                           inputmode="numeric"
                                           value="{{ old('main_unit_price') ? number_format((float) old('main_unit_price'), 0, ',', '.') : '' }}" 
                                           placeholder="0"
                                           oninput="handleMainUnitPriceInput()"
                                           autocomplete="off"
                                           class="w-full px-3 py-2 text-sm font-bold text-slate-900 dark:text-white bg-transparent focus:outline-none placeholder:text-slate-300 dark:placeholder:text-slate-500 tracking-wide">
                                </div>
                                <input type="hidden" 
                                       id="main_unit_price" 
                                       name="main_unit_price" 
                                       value="{{ old('main_unit_price', '0') }}">
                            </div>

                            <!-- Subtotal Cetak (Bisa Di-adjust jika nego cetak) -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="print_subtotal_display" class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 tracking-wider">
                                        Subtotal Biaya Cetak
                                    </label>
                                    <!-- <span class="text-[9px] font-bold text-slate-500 dark:text-slate-400">
                                        Bisa di-adjust jika nego
                                    </span> -->
                                </div>
                                <div class="flex rounded-xl overflow-hidden border-[1.5px] border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] focus-within:border-slate-800 dark:focus-within:border-amber-400 transition-all">
                                    <span class="inline-flex items-center px-3.5 bg-slate-200/90 dark:bg-slate-700/80 text-slate-700 dark:text-slate-300 border-r-[1.5px] border-slate-300 dark:border-slate-700 text-xs font-black select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           id="print_subtotal_display" 
                                           inputmode="numeric"
                                           value="{{ old('print_subtotal') ? number_format((float) old('print_subtotal'), 0, ',', '.') : '0' }}" 
                                           placeholder="0"
                                           oninput="handlePrintSubtotalManualInput(this)"
                                           class="w-full px-3 py-2 text-base font-black text-slate-900 dark:text-white bg-transparent focus:outline-none tracking-wide">
                                </div>
                                <input type="hidden" 
                                       id="print_subtotal" 
                                       name="print_subtotal" 
                                       value="{{ old('print_subtotal', '0') }}">
                                <span id="subtotal-calc-hint" class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold mt-1 block">
                                    Silakan masukkan ukuran panjang & lebar atau pilih bahan banner di sebelah kiri.
                                </span>
                            </div>

                            <!-- Modal Cetak / HPP Cetak (Auto-Hitung & Bisa Di-Adjust) -->
                            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 space-y-2">
                                <div class="flex items-center justify-between mb-0.5">
                                    <label for="main_cost_price_display" id="label-cost-price" class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 tracking-wider">
                                        Tarif Modal Cetak
                                    </label>
                                    <span class="text-[10px] font-black text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800/80 px-1.5 py-0.5 rounded">
                                        Opsional
                                    </span>
                                </div>
                                <div class="flex rounded-xl overflow-hidden border-[1.5px] border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] focus-within:border-slate-800 dark:focus-within:border-amber-400 transition-all">
                                    <span class="inline-flex items-center px-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-r-[1.5px] border-slate-300 dark:border-slate-700 text-xs font-black select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           id="main_cost_price_display" 
                                           inputmode="numeric"
                                           value="{{ old('main_cost_price', '14500') ? number_format((float) old('main_cost_price', 14500), 0, ',', '.') : '14.500' }}" 
                                           placeholder="14.500 (Boleh kosong)"
                                           oninput="handleCostPriceInput(this)"
                                           autocomplete="off"
                                           class="w-full px-3 py-2 text-sm font-bold text-slate-900 dark:text-white bg-transparent focus:outline-none placeholder:text-slate-300 dark:placeholder:text-slate-500 tracking-wide">
                                </div>
                                <input type="hidden" 
                                       id="main_cost_price" 
                                       name="main_cost_price" 
                                       value="{{ old('main_cost_price', '0') }}">
                               
                            </div>
                        </div>

                        <!-- Hidden input main_price disubmit ke server -->
                        <input type="hidden" 
                               id="main_price" 
                               name="main_price" 
                               value="{{ old('main_price', old('total_amount', '0')) }}">

                        <!-- Subtotal Alat & Item Tambahan (Appears if items exist) -->
                        <div id="extra-summary-box" class="hidden p-3 bg-purple-50/80 dark:bg-purple-950/30 border-[1.5px] border-purple-300 dark:border-purple-800/60 rounded-xl space-y-1">
                            <div class="flex items-center justify-between text-xs font-bold text-purple-900 dark:text-purple-300">
                                <span>Alat & Item Tambahan:</span>
                                <span id="extra-total-display" class="font-black text-sm text-purple-950 dark:text-purple-200">+ Rp 0</span>
                            </div>
                            <div class="text-[10px] text-purple-700 dark:text-purple-400 font-medium">
                                Otomatis diakumulasikan ke total tagihan invoice.
                            </div>
                        </div>

                        <!-- Total Biaya Order (Grand Total) -->
                        <div>
                            <label for="total_amount_display" class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                Total Tagihan Order <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex rounded-xl overflow-hidden border-2 border-slate-900 dark:border-amber-400/50 bg-amber-100/50 dark:bg-slate-900 shadow-[2px_2px_0px_0px_#0f172a] dark:shadow-[2px_2px_0px_0px_#000000] transition-all">
                                <span class="inline-flex items-center px-3.5 bg-amber-300 dark:bg-amber-400 border-r-2 border-slate-900 dark:border-slate-800 text-xs font-black text-slate-950 select-none">
                                    Rp
                                </span>
                                <input type="text" 
                                       id="total_amount_display" 
                                       inputmode="numeric"
                                       value="{{ old('total_amount') ? number_format((float) old('total_amount'), 0, ',', '.') : '' }}" 
                                       placeholder="0"
                                       required 
                                       readonly
                                       autocomplete="off"
                                       class="w-full px-3 py-2 text-base font-black text-slate-950 dark:text-amber-400 bg-transparent focus:outline-none tracking-wide cursor-default">
                            </div>
                            <input type="hidden" 
                                   id="total_amount" 
                                   name="total_amount" 
                                   value="{{ old('total_amount', '') }}">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold mt-1 block">Akumulasi Desain + Cetak + Alat/Item Tambahan.</span>
                        </div>

                        <!-- Nominal Dibayar di Awal -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="paid_amount_display" class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 tracking-wider">
                                    Nominal Dibayar di Awal <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Min. DP / Lunas</span>
                            </div>
                            <div class="flex rounded-xl overflow-hidden border-[1.5px] border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000] focus-within:border-slate-800 dark:focus-within:border-amber-400 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] transition-all">
                                <span class="inline-flex items-center px-3.5 bg-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400 border-r-[1.5px] border-slate-300 dark:border-slate-700 text-xs font-black text-slate-800 select-none">
                                    Rp
                                </span>
                                <input type="text" 
                                       id="paid_amount_display" 
                                       inputmode="numeric"
                                       value="{{ (old('paid_amount') !== null && old('paid_amount') !== '') ? number_format((float) old('paid_amount'), 0, ',', '.') : '0' }}" 
                                       placeholder="0"
                                       required 
                                       autocomplete="off"
                                       class="w-full px-3 py-2 text-base font-black text-black dark:text-white bg-transparent focus:outline-none placeholder:text-slate-300 dark:placeholder:text-slate-500 tracking-wide">
                            </div>
                            <input type="hidden" 
                                   id="paid_amount" 
                                   name="paid_amount" 
                                   value="{{ old('paid_amount', 0) }}">

                            <!-- Quick preset buttons for cashier -->
                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                <button type="button" 
                                        onclick="setPaidPreset('full')" 
                                        class="px-2.5 py-1 text-[10px] font-black uppercase bg-emerald-100 hover:bg-emerald-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-emerald-400 text-emerald-950 border border-slate-300 dark:border-slate-700 rounded shadow-[1px_1px_0px_0px_#e2e8f0] dark:shadow-[1px_1px_0px_0px_#000000] transition-all active:translate-y-0.5 cursor-pointer">
                                    Lunas 100%
                                </button>
                                <button type="button" 
                                        onclick="setPaidPreset('half')" 
                                        class="px-2.5 py-1 text-[10px] font-black uppercase bg-amber-100 hover:bg-amber-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-amber-400 text-amber-950 border border-slate-300 dark:border-slate-700 rounded shadow-[1px_1px_0px_0px_#e2e8f0] dark:shadow-[1px_1px_0px_0px_#000000] transition-all active:translate-y-0.5 cursor-pointer">
                                    DP 50%
                                </button>
                                <button type="button" 
                                        onclick="setPaidPreset(50000)" 
                                        class="px-2.5 py-1 text-[10px] font-bold bg-white hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 text-slate-700 border border-slate-300 dark:border-slate-700 rounded shadow-[1px_1px_0px_0px_#e2e8f0] dark:shadow-[1px_1px_0px_0px_#000000] transition-all active:translate-y-0.5 cursor-pointer">
                                    DP 50rb
                                </button>
                                <button type="button" 
                                        onclick="setPaidPreset(100000)" 
                                        class="px-2.5 py-1 text-[10px] font-bold bg-white hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 text-slate-700 border border-slate-300 dark:border-slate-700 rounded shadow-[1px_1px_0px_0px_#e2e8f0] dark:shadow-[1px_1px_0px_0px_#000000] transition-all active:translate-y-0.5 cursor-pointer">
                                    DP 100rb
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 dark:text-slate-300 mb-1 tracking-wider">
                                Metode Pembayaran <span class="text-rose-500">*</span>
                            </label>
                            <x-neo-select 
                                name="payment_method" 
                                required
                                :value="old('payment_method', 'cash')"
                                :options="[
                                    'cash' => 'Tunai (Cash Laci)',
                                    'qris' => 'QRIS Statis/Dinamis',
                                    'transfer' => 'Transfer Bank Cabang',
                                ]" 
                            />
                        </div>
                    </div>

                    <!-- Real-time calculation summary -->
                    <div class="p-3.5 bg-white dark:bg-slate-900 border border-amber-200/90 dark:border-slate-800 rounded-xl space-y-2.5 shadow-sm">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400 font-bold">Sisa Tagihan:</span>
                            <span id="remaining-text" class="text-base font-black text-rose-600 dark:text-rose-400">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800 text-xs">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Status Order:</span>
                            <span id="status-badge" class="px-2.5 py-0.5 rounded-full border border-rose-300 dark:border-rose-500/50 font-black uppercase text-[10px] bg-rose-100 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]">
                                BELUM BAYAR
                            </span>
                        </div>
                    </div>

                    <!-- SOP Info Note -->
                    <div class="text-[11px] font-medium text-slate-700 dark:text-slate-300 bg-white/80 dark:bg-slate-900/80 p-2.5 rounded-lg border border-amber-200/90 dark:border-slate-800 flex items-start gap-2">
                        <x-icon name="info" class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                        <span>Pembayaran awal otomatis tercatat di <strong>Buku Kas</strong> cabang hari ini dan tiket langsung masuk antrean workdesk.</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 pt-2 border-t border-amber-200/80 dark:border-slate-800">
                        <button type="submit" class="w-full neo-btn bg-emerald-400 hover:bg-emerald-300 text-slate-900 font-black text-sm py-3 px-4 shadow-[2.5px_2.5px_0px_0px_#0f172a] dark:shadow-[2.5px_2.5px_0px_0px_#000000] flex items-center justify-center gap-2 cursor-pointer">
                            <x-icon name="check" class="w-4 h-4" stroke-width="2.5" />
                            <span>Terbitkan Tiket & Catat Kas</span>
                        </button>

                        <a href="{{ route('orders.index') }}" class="w-full neo-btn bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs py-2 text-center block font-bold text-slate-600 dark:text-slate-300 dark:border-slate-700 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[1.5px_1.5px_0px_0px_#000000]">
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
let extraItemIndex = 0;

function escapeHtml(str) {
    if (!str) return '';
    return str.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function addExtraItemRow(name = '', qty = 1, price = 0) {
    const container = document.getElementById('extra-items-container');
    const noMsg = document.getElementById('no-extra-items-msg');
    if (!container) return;

    if (noMsg) noMsg.classList.add('hidden');

    const idx = extraItemIndex++;
    const row = document.createElement('div');
    row.className = 'extra-item-row p-3 bg-slate-50 dark:bg-slate-900 border-[1.5px] border-slate-800 dark:border-slate-700 rounded-xl flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]';
    row.dataset.index = idx;

    const formattedPrice = price > 0 ? price.toLocaleString('id-ID') : '';
    const initialSubtotal = (qty * price) > 0 ? 'Rp ' + (qty * price).toLocaleString('id-ID') : 'Rp 0';

    row.innerHTML = `
        <div class="flex-1 min-w-0">
            <label class="block text-[10px] font-black uppercase text-slate-600 dark:text-slate-400 mb-0.5">Nama Item / Alat / Layanan</label>
            <input type="text" 
                   name="items[${idx}][name]" 
                   value="${escapeHtml(name)}" 
                   placeholder="Contoh: Rangka X-Banner Fiber, Mata Ayam, Jasa Pasang..." 
                   required 
                   class="w-full neo-input text-xs h-9 bg-white dark:bg-[#0c1222] dark:text-white">
        </div>
        <div class="grid grid-cols-12 sm:flex sm:items-center gap-2 sm:gap-3 w-full sm:w-auto items-end">
            <div class="col-span-3 sm:w-20 shrink-0">
                <label class="block text-[10px] font-black uppercase text-slate-600 dark:text-slate-400 mb-0.5">Qty</label>
                <input type="number" 
                       name="items[${idx}][qty]" 
                       value="${qty}" 
                       min="1" 
                       oninput="handleRowChange(this)" 
                       class="w-full neo-input text-xs h-9 bg-white dark:bg-[#0c1222] dark:text-white text-center font-bold">
            </div>
            <div class="col-span-5 sm:w-36 shrink-0">
                <label class="block text-[10px] font-black uppercase text-slate-600 dark:text-slate-400 mb-0.5">Harga Satuan</label>
                <div class="relative">
                    <input type="text" 
                           inputmode="numeric" 
                           placeholder="0" 
                           value="${formattedPrice}"
                           oninput="handleItemPriceInput(this)" 
                           class="w-full neo-input text-xs h-9 bg-white dark:bg-[#0c1222] dark:text-white font-bold item-price-display">
                    <input type="hidden" 
                           name="items[${idx}][price]" 
                           value="${price}" 
                           class="item-price-raw">
                </div>
            </div>
            <div class="col-span-3 sm:w-28 shrink-0 text-left sm:text-right">
                <label class="block text-[10px] font-black uppercase text-slate-600 dark:text-slate-400 mb-0.5">Subtotal</label>
                <div class="h-9 flex items-center sm:justify-end font-black text-slate-900 dark:text-white text-xs item-subtotal-text truncate">
                    ${initialSubtotal}
                </div>
            </div>
            <div class="col-span-1 shrink-0 flex items-center justify-end">
                <button type="button" 
                        onclick="removeExtraItemRow(this)" 
                        title="Hapus baris item" 
                        class="w-9 h-9 rounded-lg border-[1.5px] border-slate-800 dark:border-slate-700 bg-rose-200 hover:bg-rose-300 dark:bg-rose-500/20 dark:hover:bg-rose-500/30 dark:border-rose-500/50 dark:text-rose-300 font-black text-xs flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000] active:translate-x-[0.5px] active:translate-y-[0.5px]">
                    ✕
                </button>
            </div>
        </div>
    `;

    container.appendChild(row);
    recalculateAllTotals();
}

function removeExtraItemRow(btn) {
    const row = btn.closest('.extra-item-row');
    if (row) {
        row.remove();
        recalculateAllTotals();
    }
}

function handleRowChange(input) {
    recalculateAllTotals();
}

function handleItemPriceInput(displayEl) {
    const row = displayEl.closest('.extra-item-row');
    const rawInput = row.querySelector('.item-price-raw');
    
    let rawDigits = displayEl.value.replace(/\D/g, '');
    if (!rawDigits) {
        displayEl.value = '';
        rawInput.value = '0';
    } else {
        const num = parseInt(rawDigits, 10);
        displayEl.value = num.toLocaleString('id-ID');
        rawInput.value = num;
    }
    recalculateAllTotals();
}

function recalculateAllTotals() {
    const mainPriceHidden = document.getElementById('main_price');
    const mainPrice = parseFloat(mainPriceHidden ? mainPriceHidden.value : 0) || 0;

    let extraTotal = 0;
    const rows = document.querySelectorAll('.extra-item-row');
    rows.forEach(row => {
        const qtyEl = row.querySelector('input[name*="[qty]"]');
        const priceEl = row.querySelector('.item-price-raw');
        const qty = parseInt(qtyEl ? qtyEl.value : 1, 10) || 1;
        const price = parseFloat(priceEl ? priceEl.value : 0) || 0;
        const subtotal = qty * price;
        extraTotal += subtotal;

        const subtotalTextEl = row.querySelector('.item-subtotal-text');
        if (subtotalTextEl) subtotalTextEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    });

    const extraBox = document.getElementById('extra-summary-box');
    const extraTotalDisplay = document.getElementById('extra-total-display');
    if (extraTotalDisplay) extraTotalDisplay.innerText = '+ Rp ' + extraTotal.toLocaleString('id-ID');
    if (extraBox) {
        if (extraTotal > 0 || rows.length > 0) {
            extraBox.classList.remove('hidden');
        } else {
            extraBox.classList.add('hidden');
        }
    }

    const noItemsMsg = document.getElementById('no-extra-items-msg');
    if (noItemsMsg) {
        if (rows.length > 0) {
            noItemsMsg.classList.add('hidden');
        } else {
            noItemsMsg.classList.remove('hidden');
        }
    }

    const grandTotal = mainPrice + extraTotal;
    const totalHidden = document.getElementById('total_amount');
    const totalDisplay = document.getElementById('total_amount_display');
    if (totalHidden) totalHidden.value = grandTotal;
    if (totalDisplay) totalDisplay.value = grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '';

    calculateRemaining();
}

function handleDesignFeeInput(displayEl) {
    let rawDigits = displayEl.value.replace(/\D/g, '');
    const feeHidden = document.getElementById('design_fee');
    let amount = 0;
    if (!rawDigits) {
        displayEl.value = '';
        if (feeHidden) feeHidden.value = '0';
    } else {
        amount = parseInt(rawDigits, 10);
        displayEl.value = amount.toLocaleString('id-ID');
        if (feeHidden) feeHidden.value = amount;
    }

    updateAllMainCalculations();
}

function handlePrintSubtotalManualInput(displayEl) {
    let rawDigits = displayEl.value.replace(/\D/g, '');
    const printSubtotalHidden = document.getElementById('print_subtotal');
    const subtotalHint = document.getElementById('subtotal-calc-hint');

    let amount = 0;
    if (!rawDigits) {
        displayEl.value = '';
        if (printSubtotalHidden) printSubtotalHidden.value = '0';
    } else {
        amount = parseInt(rawDigits, 10);
        displayEl.value = amount.toLocaleString('id-ID');
        if (printSubtotalHidden) printSubtotalHidden.value = amount;
    }

    if (subtotalHint) {
        subtotalHint.innerHTML = '<span class="text-amber-600 dark:text-amber-400 font-bold">★ Subtotal cetak disesuaikan manual (Nego deal).</span>';
    }

    updateAllMainCalculations();
}

function updateAllMainCalculations() {
    const feeHidden = document.getElementById('design_fee');
    const printSubtotalHidden = document.getElementById('print_subtotal');
    const mainPriceHidden = document.getElementById('main_price');
    const costPriceHidden = document.getElementById('main_cost_price');
    const qtyInput = document.getElementById('main_qty');

    const designFee = parseFloat(feeHidden ? feeHidden.value : 0) || 0;
    const printSubtotal = parseFloat(printSubtotalHidden ? printSubtotalHidden.value : 0) || 0;
    const mainSubtotal = designFee + printSubtotal;
    const qty = parseInt(qtyInput ? qtyInput.value : 1, 10) || 1;

    if (mainPriceHidden) mainPriceHidden.value = mainSubtotal;

    // Update summary texts
    const summaryTotalEl = document.getElementById('main-subtotal-summary-text');
    const summaryDesignEl = document.getElementById('summary-design-fee');
    const summaryPrintEl = document.getElementById('summary-print-subtotal');

    if (summaryTotalEl) summaryTotalEl.innerText = 'Rp ' + mainSubtotal.toLocaleString('id-ID');
    if (summaryDesignEl) summaryDesignEl.innerText = 'Rp ' + designFee.toLocaleString('id-ID');
    if (summaryPrintEl) summaryPrintEl.innerText = 'Rp ' + printSubtotal.toLocaleString('id-ID');

    // Cost calculation (from Cetak)
    let costPerItem = costPriceHidden && costPriceHidden.value !== '' ? parseFloat(costPriceHidden.value) : null;
    let totalCost = (costPerItem !== null && !isNaN(costPerItem) && costPerItem > 0) ? (costPerItem * qty) : null;

    updateLiveProfit(mainSubtotal, totalCost);
    recalculateAllTotals();
}

function setCalcMode(mode) {
    const calcModeHidden = document.getElementById('calc_mode');
    const mainUnitHidden = document.getElementById('main_unit');
    const btnMeter = document.getElementById('btn-mode-meter');
    const btnPcs = document.getElementById('btn-mode-pcs');
    const panelMeter = document.getElementById('panel-mode-meter');
    const labelUnitPrice = document.getElementById('label-unit-price');
    const labelCostPrice = document.getElementById('label-cost-price');
    const wrapperDimensions = document.getElementById('wrapper-dimensions');
    const wrapperMaterial = document.getElementById('wrapper-material');

    if (calcModeHidden) calcModeHidden.value = mode;

    if (mode === 'meter') {
        if (mainUnitHidden) mainUnitHidden.value = 'Meter';
        if (panelMeter) panelMeter.classList.remove('hidden');
        if (wrapperDimensions) wrapperDimensions.classList.add('hidden');
        if (wrapperMaterial) {
            wrapperMaterial.classList.remove('sm:col-span-1');
            wrapperMaterial.classList.add('sm:col-span-2');
        }

        if (btnMeter) {
            btnMeter.className = 'py-2 px-3 rounded-lg border-2 border-slate-900 dark:border-slate-700 font-black text-xs flex items-center justify-center gap-1.5 cursor-pointer transition-all bg-amber-300 text-slate-950 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]';
        }
        if (btnPcs) {
            btnPcs.className = 'py-2 px-3 rounded-lg border-[1.5px] border-slate-300 dark:border-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 cursor-pointer transition-all bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-800';
        }
        if (labelUnitPrice) {
            labelUnitPrice.innerHTML = 'Tarif Cetak per Meter<span class="text-rose-500">*</span>';
        }
        if (labelCostPrice) {
            labelCostPrice.innerText = 'Tarif Modal Cetak';
        }
        handleMeterDimensionChange();
    } else {
        if (mainUnitHidden) mainUnitHidden.value = 'Pcs';
        if (panelMeter) panelMeter.classList.add('hidden');
        if (wrapperDimensions) wrapperDimensions.classList.remove('hidden');
        if (wrapperMaterial) {
            wrapperMaterial.classList.remove('sm:col-span-2');
            wrapperMaterial.classList.add('sm:col-span-1');
        }

        if (btnPcs) {
            btnPcs.className = 'py-2 px-3 rounded-lg border-2 border-slate-900 dark:border-slate-700 font-black text-xs flex items-center justify-center gap-1.5 cursor-pointer transition-all bg-amber-300 text-slate-950 shadow-[1.5px_1.5px_0px_0px_#0f172a] dark:shadow-[1.5px_1.5px_0px_0px_#000000]';
        }
        if (btnMeter) {
            btnMeter.className = 'py-2 px-3 rounded-lg border-[1.5px] border-slate-300 dark:border-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 cursor-pointer transition-all bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-slate-800';
        }
        if (labelUnitPrice) {
            labelUnitPrice.innerHTML = 'Harga Cetak Satuan (Pcs) <span class="text-rose-500">*</span>';
        }
        if (labelCostPrice) {
            labelCostPrice.innerText = 'Modal Cetak Satuan / HPP (Pcs)';
        }
        handlePcsChange();
    }
}

function handleMeterDimensionChange() {
    const lenInput = document.getElementById('calc_length');
    const widthInput = document.getElementById('calc_width');
    const qtyInput = document.getElementById('main_qty');
    const dimInput = document.getElementById('dimensions_input');

    const length = parseFloat(lenInput ? lenInput.value : 0) || 0;
    const width = parseFloat(widthInput ? widthInput.value : 0) || 0;
    const qty = parseInt(qtyInput ? qtyInput.value : 1, 10) || 1;

    const areaSingle = Math.round(length * width * 100) / 100;
    const areaTotal = Math.round(areaSingle * qty * 100) / 100;

    const areaSingleEl = document.getElementById('display-area-single');
    const areaTotalEl = document.getElementById('display-area-total');
    if (areaSingleEl) areaSingleEl.innerText = areaSingle > 0 ? areaSingle.toFixed(2) + ' m²' : '0.00 m²';
    if (areaTotalEl) {
        if (areaTotal > 0 && qty > 1) {
            areaTotalEl.innerText = `(${qty} Pcs = ${areaTotal.toFixed(2)} m²)`;
        } else {
            areaTotalEl.innerText = '';
        }
    }

    const printSummaryBadge = document.getElementById('print-calc-summary-badge');
    if (printSummaryBadge) {
        printSummaryBadge.innerText = areaTotal > 0 ? `${areaTotal.toFixed(2)} m² (${qty} Pcs)` : '0.00 m²';
    }

    if (dimInput) {
        if (length > 0 && width > 0) {
            dimInput.value = `${length} x ${width} meter (${areaSingle.toFixed(2)} m²)`;
        } else {
            dimInput.value = '';
        }
    }

    const unitPriceHidden = document.getElementById('main_unit_price');
    const costPriceDisplay = document.getElementById('main_cost_price_display');
    const costPriceHidden = document.getElementById('main_cost_price');
    const printSubtotalDisplay = document.getElementById('print_subtotal_display');
    const printSubtotalHidden = document.getElementById('print_subtotal');
    const subtotalHint = document.getElementById('subtotal-calc-hint');
    const costHint = document.getElementById('cost-calc-hint');

    const meterSaleRate = parseFloat(unitPriceHidden ? unitPriceHidden.value : 0) || 0;
    const printSubtotal = Math.round(areaTotal * meterSaleRate);

    if (printSubtotalHidden) printSubtotalHidden.value = printSubtotal;
    if (printSubtotalDisplay) printSubtotalDisplay.value = printSubtotal > 0 ? printSubtotal.toLocaleString('id-ID') : '0';

    if (subtotalHint) {
        if (length > 0 && width > 0 && meterSaleRate > 0) {
            subtotalHint.innerHTML = `Hasil cetak: Total ${areaTotal.toFixed(2)} m² &times; Rp ${meterSaleRate.toLocaleString('id-ID')} / m² = <strong>Rp ${printSubtotal.toLocaleString('id-ID')}</strong>.`;
        } else {
            subtotalHint.innerText = 'Silakan masukkan ukuran panjang & lebar atau pilih bahan banner di sebelah kiri.';
        }
    }

    let rawCost = costPriceDisplay ? costPriceDisplay.value.replace(/\D/g, '') : '';
    let totalCost = null;
    if (rawCost && length > 0 && width > 0) {
        const meterCostRate = parseInt(rawCost, 10);
        totalCost = Math.round(areaTotal * meterCostRate);
        const costPerItem = Math.round(totalCost / qty);
        if (costPriceHidden) costPriceHidden.value = costPerItem;

        if (costHint) {
            costHint.innerHTML = `Estimasi modal cetak: Total ${areaTotal.toFixed(2)} m² &times; Rp ${meterCostRate.toLocaleString('id-ID')} = <strong>Rp ${totalCost.toLocaleString('id-ID')}</strong>. Boleh dikosongkan jika belum tahu.`;
        }
    } else {
        if (costPriceHidden) costPriceHidden.value = '';
        if (costHint) {
            if (rawCost) {
                const meterCostRate = parseInt(rawCost, 10);
                costHint.innerText = `Tarif acuan modal: Rp ${meterCostRate.toLocaleString('id-ID')} / m². Terhitung otomatis setelah ukuran diisi. Boleh dikosongkan.`;
            } else {
                costHint.innerText = 'Boleh dikosongkan jika nota vendor/maklon belum keluar. Owner bisa melengkapinya nanti.';
            }
        }
    }

    updateAllMainCalculations();
}

function handlePcsChange() {
    const qtyInput = document.getElementById('main_qty');
    const qty = parseInt(qtyInput ? qtyInput.value : 1, 10) || 1;

    const unitPriceDisplay = document.getElementById('main_unit_price_display');
    const unitPriceHidden = document.getElementById('main_unit_price');
    const costPriceDisplay = document.getElementById('main_cost_price_display');
    const costPriceHidden = document.getElementById('main_cost_price');
    const printSubtotalDisplay = document.getElementById('print_subtotal_display');
    const printSubtotalHidden = document.getElementById('print_subtotal');
    const subtotalHint = document.getElementById('subtotal-calc-hint');
    const costHint = document.getElementById('cost-calc-hint');

    let rawPrice = unitPriceDisplay ? unitPriceDisplay.value.replace(/\D/g, '') : '';
    const unitPrice = rawPrice ? parseInt(rawPrice, 10) : 0;
    if (unitPriceHidden) unitPriceHidden.value = unitPrice;

    const printSubtotal = qty * unitPrice;
    if (printSubtotalHidden) printSubtotalHidden.value = printSubtotal;
    if (printSubtotalDisplay) printSubtotalDisplay.value = printSubtotal > 0 ? printSubtotal.toLocaleString('id-ID') : '0';

    if (subtotalHint) {
        if (unitPrice > 0) {
            subtotalHint.innerHTML = `Hasil cetak: ${qty} Pcs &times; Rp ${unitPrice.toLocaleString('id-ID')} = <strong>Rp ${printSubtotal.toLocaleString('id-ID')}</strong>.`;
        } else {
            subtotalHint.innerText = 'Silakan masukkan harga cetak satuan di atas.';
        }
    }

    let rawCost = costPriceDisplay ? costPriceDisplay.value.replace(/\D/g, '') : '';
    let totalCost = null;
    if (rawCost && unitPrice > 0) {
        const costPerPcs = parseInt(rawCost, 10);
        totalCost = qty * costPerPcs;
        if (costPriceHidden) costPriceHidden.value = costPerPcs;

        if (costHint) {
            costHint.innerHTML = `Estimasi modal cetak: ${qty} Pcs &times; Rp ${costPerPcs.toLocaleString('id-ID')} = <strong>Rp ${totalCost.toLocaleString('id-ID')}</strong>. Boleh dikosongkan jika belum tahu.`;
        }
    } else {
        if (costPriceHidden) costPriceHidden.value = '';
        if (costHint) {
            costHint.innerText = 'Boleh dikosongkan jika nota vendor/maklon belum keluar. Owner bisa melengkapinya nanti.';
        }
    }

    updateAllMainCalculations();
}

function applyBannerPreset(matName, saleRate, costRate) {
    const matInput = document.getElementById('material_notes_input');
    if (matInput) matInput.value = matName;

    const unitPriceDisplay = document.getElementById('main_unit_price_display');
    const unitPriceHidden = document.getElementById('main_unit_price');
    const costPriceDisplay = document.getElementById('main_cost_price_display');
    const costPriceHidden = document.getElementById('main_cost_price');

    if (unitPriceDisplay) unitPriceDisplay.value = saleRate.toLocaleString('id-ID');
    if (unitPriceHidden) unitPriceHidden.value = saleRate;

    if (costPriceDisplay) costPriceDisplay.value = costRate.toLocaleString('id-ID');
    if (costPriceHidden) costPriceHidden.value = costRate;

    setCalcMode('meter');
}

function handleMainQtyChange() {
    const calcModeHidden = document.getElementById('calc_mode');
    const mode = calcModeHidden ? calcModeHidden.value : 'meter';
    if (mode === 'meter') {
        handleMeterDimensionChange();
    } else {
        handlePcsChange();
    }
}

function handleMainUnitPriceInput() {
    const displayEl = document.getElementById('main_unit_price_display');
    const hiddenEl = document.getElementById('main_unit_price');
    let rawDigits = displayEl.value.replace(/\D/g, '');
    let price = 0;
    if (!rawDigits) {
        displayEl.value = '';
        hiddenEl.value = '0';
    } else {
        price = parseInt(rawDigits, 10);
        displayEl.value = price.toLocaleString('id-ID');
        hiddenEl.value = price;
    }

    const calcModeHidden = document.getElementById('calc_mode');
    const mode = calcModeHidden ? calcModeHidden.value : 'meter';
    if (mode === 'meter') {
        handleMeterDimensionChange();
    } else {
        handlePcsChange();
    }
}

function handleCostPriceInput(inputEl) {
    let rawDigits = inputEl.value.replace(/\D/g, '');
    if (!rawDigits) {
        inputEl.value = '';
    } else {
        const num = parseInt(rawDigits, 10);
        inputEl.value = num.toLocaleString('id-ID');
    }

    const calcModeHidden = document.getElementById('calc_mode');
    const mode = calcModeHidden ? calcModeHidden.value : 'meter';
    if (mode === 'meter') {
        handleMeterDimensionChange();
    } else {
        handlePcsChange();
    }
}

function updateLiveProfit(subtotal, totalCost) {
    const profitTextEl = document.getElementById('live-profit-text');
    if (!profitTextEl) return;

    if (totalCost === null || isNaN(totalCost) || totalCost <= 0) {
        profitTextEl.innerHTML = '<span class="text-slate-400 dark:text-slate-500 font-medium italic">Belum diinput (opsional)</span>';
        return;
    }

    const profit = Math.round(subtotal - totalCost);
    const margin = subtotal > 0 ? ((profit / subtotal) * 100).toFixed(1) : '0';

    if (profit >= 0) {
        profitTextEl.className = 'font-black text-emerald-700 dark:text-emerald-400';
        profitTextEl.innerText = `Rp ${profit.toLocaleString('id-ID')} (${margin}%)`;
    } else {
        profitTextEl.className = 'font-black text-rose-600 dark:text-rose-400';
        profitTextEl.innerText = `-Rp ${Math.abs(profit).toLocaleString('id-ID')} (${margin}%)`;
    }
}

function setupRupiahMask(displayId, hiddenId) {
    const displayEl = document.getElementById(displayId);
    const hiddenEl = document.getElementById(hiddenId);
    if (!displayEl || !hiddenEl) return;

    displayEl.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace') {
            const cursor = displayEl.selectionStart;
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

            const diff = formatted.length - oldVal.length;
            let newCursor = cursor + diff;
            if (newCursor < 0) newCursor = 0;
            if (newCursor > formatted.length) newCursor = formatted.length;
            displayEl.setSelectionRange(newCursor, newCursor);
        }

        if (hiddenId === 'main_price') {
            recalculateAllTotals();
        } else {
            calculateRemaining();
        }
    });

    displayEl.addEventListener('focus', function() {
        if (displayEl.value === '0') {
            displayEl.select();
        }
    });

    displayEl.addEventListener('blur', function() {
        if (displayEl.value === '' && hiddenId === 'paid_amount') {
            displayEl.value = '0';
            hiddenEl.value = '0';
            calculateRemaining();
        }
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
            remainingEl.className = 'text-base font-black text-emerald-600 dark:text-emerald-400';
        } else {
            remainingEl.className = 'text-base font-black text-rose-600 dark:text-rose-400';
        }
    }

    const badge = document.getElementById('status-badge');
    if (badge) {
        if (paid >= total && total > 0) {
            badge.innerText = 'LUNAS PENUH';
            badge.className = 'px-2.5 py-0.5 rounded-full border border-emerald-400 dark:border-emerald-500/50 font-black uppercase text-[10px] bg-emerald-100 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]';
        } else if (paid > 0) {
            badge.innerText = 'DP (SEBAGIAN)';
            badge.className = 'px-2.5 py-0.5 rounded-full border border-yellow-400 dark:border-amber-500/50 font-black uppercase text-[10px] bg-yellow-100 dark:bg-amber-950/40 text-yellow-900 dark:text-amber-300 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]';
        } else {
            badge.innerText = 'BELUM BAYAR';
            badge.className = 'px-2.5 py-0.5 rounded-full border border-rose-300 dark:border-rose-500/50 font-black uppercase text-[10px] bg-rose-100 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]';
        }
    }
}

function handleBriefFileChange(input) {
    const label = document.getElementById('brief_file_name');
    const clearBtn = document.getElementById('brief_file_clear');
    if (input.files && input.files[0]) {
        label.innerText = input.files[0].name;
        label.className = 'text-xs text-black dark:text-white font-black truncate pr-1';
        if (clearBtn) clearBtn.classList.remove('hidden');
    } else {
        label.innerText = 'Pilih file acuan customer...';
        label.className = 'text-xs text-slate-400 dark:text-slate-500 font-medium truncate pr-1';
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
    label.className = 'text-xs text-slate-400 dark:text-slate-500 font-medium truncate pr-1';
    if (clearBtn) clearBtn.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    setupRupiahMask('paid_amount_display', 'paid_amount');
    
    const initialMode = (document.getElementById('calc_mode')?.value) || 'meter';
    setCalcMode(initialMode);

    @if(old('items'))
        @foreach(old('items') as $oldIt)
            addExtraItemRow("{{ addslashes($oldIt['name'] ?? '') }}", {{ (int)($oldIt['qty'] ?? 1) }}, {{ (float)($oldIt['price'] ?? 0) }});
        @endforeach
    @endif

    recalculateAllTotals();
});
</script>
@endpush
@endsection
