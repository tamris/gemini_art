<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Manajemen Percetakan') - Gemini Art</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] text-slate-900 font-sans min-h-screen flex antialiased selection:bg-amber-300 selection:text-black">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebar-backdrop" 
         onclick="toggleSidebar()" 
         class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden backdrop-blur-xs transition-opacity"></div>

    <!-- Left Sidebar (Soft Neo-Brutalism) -->
    <aside id="main-sidebar" 
           class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r-[1.5px] border-slate-800 flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
        
        <!-- Top: Brand Header -->
        <div class="h-20 px-5 border-b-[1.5px] border-slate-800 bg-white flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group text-decoration-none">
                <div class="w-11 h-11 bg-amber-200 border-[1.5px] border-slate-800 rounded-xl shadow-[2px_2px_0px_0px_#0f172a] flex items-center justify-center font-black text-xl group-hover:translate-x-[1px] group-hover:translate-y-[1px] transition-transform text-slate-900">
                    GA
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-black text-lg tracking-tight text-slate-900">GEMINI ART</span>
                    </div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Studio & Percetakan
                    </p>
                </div>
            </a>

            <!-- Mobile Close Button -->
            <button type="button" 
                    onclick="toggleSidebar()" 
                    class="lg:hidden p-1.5 rounded-lg border-[1.5px] border-slate-800 bg-rose-200 hover:bg-rose-300 font-black text-sm cursor-pointer shadow-[1px_1px_0px_0px_#0f172a]">
                <x-icon name="x" class="w-4 h-4 text-black" stroke-width="2.5" />
            </button>
        </div>

        <!-- Middle: Navigation Menus -->
        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            
            <!-- Group 1: Ringkasan & Order -->
            <div class="space-y-1.5">
                <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400">
                    Menu Utama
                </div>

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('dashboard') ? 'bg-amber-300 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('dashboard') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-amber-100' }}">
                        <x-icon name="layout-dashboard" class="w-4 h-4" />
                    </div>
                    <span>Dashboard</span>
                </a>

                <!-- Semua Order -->
                <a href="{{ route('orders.index') }}" 
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? 'bg-amber-300 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-amber-100' }}">
                            <x-icon name="clipboard-list" class="w-4 h-4" />
                        </div>
                        <span>Semua Order</span>
                    </div>
                    <span class="text-[10px] font-extrabold bg-white border border-slate-400 px-1.5 py-0.2 rounded shadow-[1px_1px_0px_0px_#e2e8f0]">
                        {{ \App\Models\Order::count() }}
                    </span>
                </a>
            </div>

            <!-- Group 2: Operasional Kasir & Workdesk -->
            <div class="space-y-1.5">
                <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400">
                    Operasional & Kasir
                </div>

                <!-- Order Desain Baru -->
                <a href="{{ route('orders.create-design') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('orders.create-design') ? 'bg-sky-300 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('orders.create-design') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-sky-200' }}">
                        <x-icon name="palette" class="w-4 h-4" />
                    </div>
                    <span>Order Desain Baru</span>
                </a>

                <!-- Kasir POS Ritel -->
                <a href="{{ route('pos.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('pos.*') ? 'bg-emerald-300 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('pos.*') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-emerald-200' }}">
                        <x-icon name="shopping-cart" class="w-4 h-4" />
                    </div>
                    <span>Kasir POS Ritel (FC/ATK)</span>
                </a>

                <!-- Workdesk Desainer -->
                <a href="{{ route('workdesk.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('workdesk.*') ? 'bg-blue-300 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('workdesk.*') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-blue-200' }}">
                        <x-icon name="layers" class="w-4 h-4" />
                    </div>
                    <span>Workdesk Desainer</span>
                </a>
            </div>

            <!-- Group 3: Finansial & Arsip Cloud -->
            <div class="space-y-1.5">
                <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400">
                    Keuangan & Arsip
                </div>

                <!-- Buku Kas & Mutasi -->
                <a href="{{ route('cash.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('cash.*') ? 'bg-purple-300 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('cash.*') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-purple-200' }}">
                        <x-icon name="wallet" class="w-4 h-4" />
                    </div>
                    <span>Buku Kas & Mutasi</span>
                </a>

                <!-- Arsip Cloud (S3/R2) -->
                <a href="{{ route('archive.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('archive.*') ? 'bg-yellow-200 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('archive.*') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-amber-200' }}">
                        <x-icon name="archive" class="w-4 h-4" />
                    </div>
                    <span>Arsip Cloud</span>
                </a>
            </div>

            <!-- Group 4: Administrasi & Cabang (Khusus Owner) -->
            @if(Auth::user()->isOwner())
            <div class="space-y-1.5 pt-1">
                <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400">
                    Administrasi & Cabang
                </div>

                <!-- Kelola Cabang -->
                <a href="{{ route('branches.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('branches.*') ? 'bg-rose-300 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('branches.*') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-rose-200' }}">
                        <x-icon name="building-2" class="w-4 h-4" />
                    </div>
                    <span>Kelola Cabang</span>
                </a>

                <!-- Kelola Staf / Karyawan -->
                <a href="{{ route('users.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('users.*') ? 'bg-indigo-300 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('users.*') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-indigo-200' }}">
                        <x-icon name="users" class="w-4 h-4" />
                    </div>
                    <span>Kelola Staf</span>
                </a>
            </div>
            @endif

        </div>

        <!-- Bottom: Realtime Branch Drawer Widget & User Account Card -->
        <div class="p-4 border-t-[1.5px] border-slate-800 bg-slate-50 space-y-3">
            
            <!-- Realtime Branch Drawer Widget -->
            @php
                $user = Auth::user();
                $isOwner = $user->isOwner();
                if ($isOwner) {
                    $totalIn = \App\Models\CashMovement::where('type', 'in')->sum('amount');
                    $totalOut = \App\Models\CashMovement::where('type', 'out')->sum('amount');
                    $activeBalance = $totalIn - $totalOut;
                    $branchLabel = "PUSAT (SEMUA CABANG)";
                } else {
                    $activeBalance = $user->branch ? $user->branch->activeCashBalance() : 0;
                    $branchLabel = $user->branch ? ($user->branch->code . ' - ' . $user->branch->name) : 'CABANG';
                }
            @endphp
            <div class="p-3 bg-white border-[1.5px] border-slate-800 rounded-xl shadow-[2px_2px_0px_0px_#0f172a] text-xs">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[9px] font-black uppercase text-slate-500 truncate max-w-[130px]">
                        {{ $branchLabel }}
                    </span>
                    <span class="w-2 h-2 rounded-full {{ $isOwner ? 'bg-purple-600' : 'bg-emerald-500' }}"></span>
                </div>
                <div class="text-[10px] text-slate-600 font-bold flex items-center gap-1.5">
                    <x-icon name="coins" class="w-3.5 h-3.5 text-slate-500" />
                    Saldo Kas Laci:
                </div>
                <div class="text-sm font-black text-slate-900">
                    Rp {{ number_format($activeBalance, 0, ',', '.') }}
                </div>
            </div>

            <!-- User Info Pill & Quick Switch / Logout -->
            <div class="p-2.5 bg-white border-[1.5px] border-slate-800 rounded-xl shadow-[2px_2px_0px_0px_#0f172a] flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 bg-amber-200 flex items-center justify-center font-black text-xs shrink-0 text-slate-900">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-black text-slate-900 truncate leading-tight">{{ $user->name }}</div>
                        <div class="text-[10px] font-bold uppercase text-slate-500">{{ $user->role }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <!-- Switch button -->
                    <button type="button" 
                            onclick="document.getElementById('quick-switch-modal').showModal()"
                            title="Ganti Akun Role"
                            class="p-1.5 bg-amber-300 hover:bg-amber-400 border border-slate-800 rounded-lg text-slate-900 shadow-[1px_1px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] cursor-pointer">
                        <x-icon name="refresh-cw" class="w-3.5 h-3.5" />
                    </button>

                    <!-- Logout button -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                title="Keluar"
                                class="p-1.5 bg-rose-200 hover:bg-rose-300 border border-slate-800 rounded-lg text-slate-900 shadow-[1px_1px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] cursor-pointer">
                            <x-icon name="log-out" class="w-3.5 h-3.5" />
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </aside>

    <!-- Main Content Wrapper (Shifted right by w-72 on desktop) -->
    <div class="flex-1 lg:pl-72 flex flex-col min-h-screen min-w-0">
        
        <!-- Top Minimal Utility Bar -->
        <header class="h-20 bg-white border-b-[1.5px] border-slate-800 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between">
            
            <!-- Left: Mobile Toggle & Page Title -->
            <div class="flex items-center gap-3">
                <button type="button" 
                        onclick="toggleSidebar()" 
                        class="lg:hidden p-2 rounded-xl border-[1.5px] border-slate-800 bg-amber-300 hover:bg-amber-400 active:translate-x-[1px] active:translate-y-[1px] shadow-[1.5px_1.5px_0px_0px_#0f172a] cursor-pointer">
                    <x-icon name="menu" class="w-5 h-5 text-slate-900" stroke-width="2.5" />
                </button>

                <div>
                    <h2 class="font-black text-sm sm:text-base text-slate-900 tracking-tight flex items-center gap-2">
                        <span>@yield('title', 'Gemini Art')</span>
                    </h2>
                </div>
            </div>

            <!-- Right: Indonesian Date Only -->
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 bg-slate-100 border-[1.5px] border-slate-700 px-3 py-1.5 rounded-xl shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                    <x-icon name="calendar" class="w-3.5 h-3.5 text-slate-500" />
                    <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </span>
            </div>

        </header>

        <!-- Main Workspace Area (Full-Width Fluid Responsive) -->
        <main class="flex-1 w-full px-4 sm:px-8 py-6">

            <!-- Flash Notifications -->
            @if(session('success'))
            <div class="mb-5 p-3 bg-emerald-100 border-[1.5px] border-slate-900 rounded-xl shadow-[2px_2px_0px_0px_#0f172a] flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-emerald-400 border border-slate-900 shadow-[1px_1px_0px_0px_#0f172a] flex items-center justify-center text-slate-950 shrink-0">
                        <x-icon name="check-circle-2" class="w-4 h-4" stroke-width="2.5" />
                    </div>
                    <p class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 rounded-md border border-slate-900 bg-white hover:bg-slate-100 font-black text-xs text-slate-700 flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a] active:translate-x-[0.5px] active:translate-y-[0.5px]">✕</button>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-5 p-3 bg-sky-100 border-[1.5px] border-slate-900 rounded-xl shadow-[2px_2px_0px_0px_#0f172a] flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-sky-300 border border-slate-900 shadow-[1px_1px_0px_0px_#0f172a] flex items-center justify-center text-slate-950 shrink-0">
                        <x-icon name="info" class="w-4 h-4" stroke-width="2.5" />
                    </div>
                    <p class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">{{ session('info') }}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 rounded-md border border-slate-900 bg-white hover:bg-slate-100 font-black text-xs text-slate-700 flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a] active:translate-x-[0.5px] active:translate-y-[0.5px]">✕</button>
            </div>
            @endif

            @if(session('error') || $errors->any())
            <div class="mb-5 p-3.5 bg-rose-100 border-[1.5px] border-slate-900 rounded-xl shadow-[2px_2px_0px_0px_#0f172a]">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-rose-400 border border-slate-900 shadow-[1px_1px_0px_0px_#0f172a] flex items-center justify-center text-white shrink-0">
                            <x-icon name="alert-triangle" class="w-4 h-4 text-white" stroke-width="2.5" />
                        </div>
                        <p class="font-black text-xs sm:text-sm text-slate-900">{{ session('error') ?? 'Terjadi kesalahan pada input data:' }}</p>
                    </div>
                    <button type="button" onclick="this.closest('.mb-5').remove()" class="w-6 h-6 rounded-md border border-slate-900 bg-white hover:bg-slate-100 font-black text-xs text-slate-700 flex items-center justify-center cursor-pointer shadow-[1px_1px_0px_0px_#0f172a] active:translate-x-[0.5px] active:translate-y-[0.5px]">✕</button>
                </div>
                @if($errors->any())
                <ul class="list-disc list-inside text-xs font-bold text-rose-950 pl-9 space-y-1">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endif

            <!-- Content Slot -->
            @yield('content')

        </main>

        <!-- Minimal Footer -->
        <footer class="bg-white border-t-[1.5px] border-slate-800 py-3.5 mt-auto">
            <div class="w-full px-4 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-1 text-xs font-semibold text-slate-500 text-center sm:text-left">
                <div>
                    <strong>Gemini Art Multi-Cabang</strong> &bull; ERP Percetakan & Studio Desain
                </div>
                <div class="text-[11px] text-slate-400">
                    &copy; {{ date('Y') }} Gemini Art. Hak cipta dilindungi.
                </div>
            </div>
        </footer>

    </div>

    <!-- Quick Role Switcher Modal (Accessible Native <dialog>) -->
    <dialog id="quick-switch-modal" closedby="any" class="p-0 bg-transparent max-w-md w-full m-auto border-none outline-none overflow-visible">
        <div class="neo-box p-6 bg-white w-full mx-auto shadow-[4px_4px_0px_0px_#0f172a] rounded-2xl border-2 border-slate-900 overflow-visible">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-amber-300 border-[1.5px] border-slate-800 shadow-[1px_1px_0px_0px_#0f172a] rounded-lg flex items-center justify-center text-slate-900">
                        <x-icon name="refresh-cw" class="w-4 h-4" />
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900">Beralih Akun (Role Switcher)</h3>
                </div>
                <form method="dialog">
                    <button class="w-8 h-8 rounded-lg border-[1.5px] border-slate-800 shadow-[1.5px_1.5px_0px_0px_#0f172a] bg-rose-100 hover:bg-rose-200 font-bold text-slate-700 text-base flex items-center justify-center cursor-pointer">
                        ✕
                    </button>
                </form>
            </div>
            <p class="text-xs font-medium text-slate-500 mb-4">Pilih peran akun untuk menguji alur kasir cabang, workdesk desainer, atau command center owner:</p>
            
            <div class="space-y-2.5">
                @php
                    $allUsers = \App\Models\User::with('branch')->get();
                @endphp
                @foreach($allUsers as $u)
                <form method="POST" action="{{ route('quick-login') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $u->id }}">
                    <button type="submit" 
                            class="w-full text-left p-3 rounded-xl border-[1.5px] border-slate-800 {{ $u->id === Auth::id() ? 'bg-amber-100/90 shadow-[2.5px_2.5px_0px_0px_#0f172a]' : 'bg-slate-50 hover:bg-amber-50 shadow-[1.5px_1.5px_0px_0px_#0f172a]' }} active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                {{ $u->name }}
                                @if($u->id === Auth::id())
                                    <span class="text-[10px] bg-emerald-400 border-[1.5px] border-slate-800 px-1.5 py-0.2 rounded font-black uppercase shadow-[1px_1px_0px_0px_#0f172a]">Aktif</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 font-medium">
                                {{ $u->branch ? $u->branch->name : 'Multi-Cabang (Pusat)' }}
                            </div>
                        </div>
                        <span class="text-xs font-bold uppercase px-2 py-1 rounded-md border-[1.5px] border-slate-800 shadow-[1px_1px_0px_0px_#0f172a] {{ $u->role === 'owner' ? 'bg-purple-200 text-purple-900' : ($u->role === 'cashier' ? 'bg-emerald-200 text-emerald-900' : 'bg-blue-200 text-blue-900') }}">
                            {{ $u->role }}
                        </span>
                    </button>
                </form>
                @endforeach
            </div>

            <div class="mt-5 pt-3 border-t border-slate-200 text-right">
                <form method="dialog">
                    <button type="submit" class="neo-btn bg-slate-200 hover:bg-slate-300 text-xs py-2 px-4 cursor-pointer">Tutup</button>
                </form>
            </div>
        </div>
    </dialog>

    <!-- Global Vendor Dispatch Modal -->
    <x-vendor-dispatch-modal />

    <!-- Global File Preview Modal (Soft Neo-Brutalism Lightbox) -->
    <dialog id="file-preview-modal" closedby="any" class="p-0 bg-transparent max-w-4xl w-full m-auto border-none outline-none overflow-visible">
        <div class="neo-box p-5 bg-white w-full mx-auto shadow-[6px_6px_0px_0px_#0f172a] rounded-2xl border-2 border-slate-900 overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3.5 border-b-2 border-slate-900 gap-3 shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span id="preview-badge" class="text-[10px] font-black uppercase px-2 py-0.5 rounded border border-slate-900 bg-amber-200 shrink-0">
                        File Arsip
                    </span>
                    <div class="min-w-0">
                        <h3 id="preview-filename" class="font-black font-mono text-sm sm:text-base text-slate-900 truncate">
                            filename.ext
                        </h3>
                        <span id="preview-filesize" class="text-xs font-mono text-slate-500 font-bold block mt-0.5">
                            0 KB
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <!-- Open in new tab button -->
                    <a id="preview-tab-btn" href="#" target="_blank" title="Buka di Tab Baru" class="neo-btn bg-sky-100 hover:bg-sky-200 text-slate-900 text-xs py-1.5 px-3 flex items-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                        <x-icon name="external-link" class="w-3.5 h-3.5" />
                        <span class="hidden sm:inline">Tab Baru</span>
                    </a>

                    <!-- Direct Download button -->
                    <a id="preview-download-btn" href="#" title="Download Berkas" class="neo-btn bg-amber-300 hover:bg-amber-400 text-slate-900 text-xs py-1.5 px-3 flex items-center gap-1.5 shadow-[1.5px_1.5px_0px_0px_#0f172a]">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        <span class="hidden sm:inline">Download</span>
                    </a>

                    <!-- Close button -->
                    <button type="button" onclick="closeFilePreview()" aria-label="Tutup" class="w-8 h-8 rounded-lg border-2 border-slate-900 bg-rose-200 hover:bg-rose-300 font-black text-sm flex items-center justify-center cursor-pointer shadow-[1.5px_1.5px_0px_0px_#0f172a] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Modal Body (Preview Content Area) -->
            <div class="flex-1 overflow-auto p-4 bg-slate-950/5 flex items-center justify-center min-h-[350px] relative">
                
                <!-- Loading State (Soft Neo-Brutalism Spinner) -->
                <div id="preview-loader" class="flex flex-col items-center justify-center p-8 space-y-3.5 text-center">
                    <div class="relative w-12 h-12 flex items-center justify-center">
                        <div class="w-12 h-12 rounded-full border-4 border-slate-200 border-t-amber-400 border-r-slate-800 animate-spin"></div>
                        <x-icon name="cloud" class="w-5 h-5 text-slate-800 absolute" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-black text-slate-900 tracking-wide">Mengunduh Pratinjau dari Cloud Storage...</p>
                        <p class="text-[10px] font-bold text-slate-500 font-mono" id="preview-loader-filename">Memuat berkas...</p>
                    </div>
                </div>

                <!-- Error State -->
                <div id="preview-container-error" class="hidden text-center p-8 max-w-md mx-auto space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-rose-100 border-2 border-slate-900 flex items-center justify-center mx-auto shadow-[2.5px_2.5px_0px_0px_#0f172a]">
                        <x-icon name="alert-triangle" class="w-7 h-7 text-rose-600" />
                    </div>
                    <div>
                        <h4 class="font-black text-base text-slate-900">Gagal Memuat Pratinjau</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Terjadi kendala saat memuat berkas langsung di browser. Anda tetap dapat membukanya melalui tombol tab baru atau download langsung.
                        </p>
                    </div>
                    <div class="flex items-center justify-center gap-2 pt-1">
                        <a id="preview-error-tab-btn" href="#" target="_blank" class="neo-btn bg-sky-200 hover:bg-sky-300 text-xs py-2 px-3.5 font-bold text-slate-900">
                            Buka di Tab Baru
                        </a>
                        <a id="preview-error-download-btn" href="#" class="neo-btn bg-amber-300 hover:bg-amber-400 text-xs py-2 px-3.5 font-black text-slate-900">
                            Download File
                        </a>
                    </div>
                </div>

                <!-- Image Viewer -->
                <div id="preview-container-img" class="hidden w-full h-full flex items-center justify-center">
                    <img id="preview-img-element" src="" alt="Pratinjau File" class="max-h-[65vh] max-w-full object-contain rounded-xl border-2 border-slate-900 shadow-[3px_3px_0px_0px_#0f172a] bg-white transition-opacity duration-300 opacity-0">
                </div>

                <!-- PDF Viewer -->
                <div id="preview-container-pdf" class="hidden w-full h-full">
                    <iframe id="preview-pdf-element" src="" class="w-full h-[65vh] rounded-xl border-2 border-slate-900 bg-white"></iframe>
                </div>

                <!-- Non-previewable / Vector / RAW File Fallback -->
                <div id="preview-container-fallback" class="hidden text-center p-8 max-w-md mx-auto space-y-3">
                    <div class="w-16 h-16 rounded-2xl bg-amber-100 border-2 border-slate-900 flex items-center justify-center mx-auto shadow-[2.5px_2.5px_0px_0px_#0f172a]">
                        <x-icon name="file" class="w-8 h-8 text-amber-800" />
                    </div>
                    <div>
                        <h4 class="font-black text-base text-slate-900">Format File Khusus Produksi</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Format berkas ini (<span id="preview-fallback-ext" class="font-mono font-bold uppercase text-slate-900">FILE</span>) adalah file mentah percetakan. Gunakan tombol download di atas untuk membukanya pada software CorelDRAW, Photoshop, atau Illustrator.
                        </p>
                    </div>
                    <a id="preview-fallback-download" href="#" class="inline-flex neo-btn bg-emerald-300 hover:bg-emerald-400 text-xs py-2 px-4 font-black text-slate-950 gap-2">
                        <x-icon name="download" class="w-4 h-4" />
                        <span>Download Berkas Ini</span>
                    </a>
                </div>
            </div>

        </div>
    </dialog>

    <!-- Mobile Sidebar Toggle Script & Global File Preview Scripts -->
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('main-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const isOpen = !sidebar.classList.contains('-translate-x-full');

        if (isOpen) {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        } else {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        }
    }

    function openFilePreview(url, fileName, fileSize, badgeText, fileKind, downloadUrl) {
        const modal = document.getElementById('file-preview-modal');
        if (!modal) return;

        document.getElementById('preview-filename').textContent = fileName;
        document.getElementById('preview-filesize').textContent = fileSize;
        document.getElementById('preview-badge').textContent = badgeText;
        document.getElementById('preview-tab-btn').href = url;
        document.getElementById('preview-download-btn').href = downloadUrl;
        document.getElementById('preview-fallback-download').href = downloadUrl;

        const errorTabBtn = document.getElementById('preview-error-tab-btn');
        const errorDownloadBtn = document.getElementById('preview-error-download-btn');
        if (errorTabBtn) errorTabBtn.href = url;
        if (errorDownloadBtn) errorDownloadBtn.href = downloadUrl;

        const loader = document.getElementById('preview-loader');
        const loaderFilename = document.getElementById('preview-loader-filename');
        if (loaderFilename) loaderFilename.textContent = fileName + ' (' + fileSize + ')';

        const imgContainer = document.getElementById('preview-container-img');
        const pdfContainer = document.getElementById('preview-container-pdf');
        const fallbackContainer = document.getElementById('preview-container-fallback');
        const errorContainer = document.getElementById('preview-container-error');
        const imgEl = document.getElementById('preview-img-element');
        const pdfEl = document.getElementById('preview-pdf-element');

        // Reset display states
        loader.classList.remove('hidden');
        imgContainer.classList.add('hidden');
        pdfContainer.classList.add('hidden');
        fallbackContainer.classList.add('hidden');
        if (errorContainer) errorContainer.classList.add('hidden');

        if (fileKind === 'image') {
            imgEl.classList.add('opacity-0');
            imgEl.classList.remove('opacity-100');

            imgEl.onload = function() {
                loader.classList.add('hidden');
                imgContainer.classList.remove('hidden');
                setTimeout(() => {
                    imgEl.classList.remove('opacity-0');
                    imgEl.classList.add('opacity-100');
                }, 50);
            };

            imgEl.onerror = function() {
                loader.classList.add('hidden');
                if (errorContainer) errorContainer.classList.remove('hidden');
            };

            imgEl.src = url;
        } else if (fileKind === 'pdf') {
            pdfEl.onload = function() {
                loader.classList.add('hidden');
                pdfContainer.classList.remove('hidden');
            };
            pdfEl.onerror = function() {
                loader.classList.add('hidden');
                if (errorContainer) errorContainer.classList.remove('hidden');
            };
            pdfEl.src = url;
        } else {
            loader.classList.add('hidden');
            const ext = fileName.split('.').pop() || 'FILE';
            document.getElementById('preview-fallback-ext').textContent = ext;
            fallbackContainer.classList.remove('hidden');
        }

        modal.showModal();
    }

    function closeFilePreview() {
        const modal = document.getElementById('file-preview-modal');
        if (!modal) return;
        modal.close();
        const imgEl = document.getElementById('preview-img-element');
        const pdfEl = document.getElementById('preview-pdf-element');
        if (imgEl) {
            imgEl.onload = null;
            imgEl.onerror = null;
            imgEl.src = '';
            imgEl.classList.add('opacity-0');
        }
        if (pdfEl) {
            pdfEl.onload = null;
            pdfEl.onerror = null;
            pdfEl.src = '';
        }
    }

    window.openFilePreview = openFilePreview;
    window.closeFilePreview = closeFilePreview;

    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('file-preview-modal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeFilePreview();
                }
            });
            modal.addEventListener('cancel', () => {
                closeFilePreview();
            });
        }

        initAjaxUploads();
    });

    function resetUploadForm(form) {
        if (!form) return;
        const fieldsContainer = form.querySelector('.upload-fields-container');
        const progressContainer = form.querySelector('.upload-progress-container');
        const barEl = form.querySelector('.upload-progress-bar');
        const percentEl = form.querySelector('.upload-progress-percent');
        const bytesEl = form.querySelector('.upload-progress-bytes');
        const errorContainer = form.querySelector('.upload-progress-error');
        const statusEl = form.querySelector('.upload-progress-status');

        if (fieldsContainer) fieldsContainer.classList.remove('hidden');
        if (progressContainer) progressContainer.classList.add('hidden');
        if (errorContainer) errorContainer.classList.add('hidden');
        if (barEl) barEl.style.width = '0%';
        if (percentEl) percentEl.innerText = '0%';
        if (bytesEl) bytesEl.innerText = '0 KB / 0 KB';
        if (statusEl) {
            statusEl.innerHTML = `
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                <span>Mengunggah file ke cloud storage...</span>
            `;
        }
    }
    window.resetUploadForm = resetUploadForm;

    function initAjaxUploads() {
        if (window.__ajaxUploadDelegated) return;
        window.__ajaxUploadDelegated = true;

        document.addEventListener('submit', function(e) {
            const form = e.target.closest('form[data-ajax-upload]');
            if (!form) return;

            const fileInput = form.querySelector('input[type="file"]');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                return; // Let browser trigger native required validation
            }

            e.preventDefault();

            const file = fileInput.files[0];
            const modal = form.closest('dialog');
            const closeBtns = modal ? modal.querySelectorAll('form[method="dialog"] button, button[onclick*="close"]') : [];
            const fieldsContainer = form.querySelector('.upload-fields-container') || form;
            const progressContainer = form.querySelector('.upload-progress-container');

            if (!progressContainer) {
                form.submit();
                return;
            }

            closeBtns.forEach(btn => btn.style.pointerEvents = 'none');

            fieldsContainer.classList.add('hidden');
            progressContainer.classList.remove('hidden');

            const fileNameEl = progressContainer.querySelector('.upload-progress-filename');
            const percentEl = progressContainer.querySelector('.upload-progress-percent');
            const bytesEl = progressContainer.querySelector('.upload-progress-bytes');
            const barEl = progressContainer.querySelector('.upload-progress-bar');
            const statusEl = progressContainer.querySelector('.upload-progress-status');
            const errorContainer = progressContainer.querySelector('.upload-progress-error');
            const errorMsgEl = progressContainer.querySelector('.upload-progress-error-msg');
            const retryBtn = progressContainer.querySelector('.upload-progress-retry-btn');

            if (errorContainer) errorContainer.classList.add('hidden');
            if (fileNameEl) fileNameEl.innerText = file.name;

            const formatBytes = (bytes) => {
                if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
                return (bytes / 1024).toFixed(1) + ' KB';
            };

            const totalFormatted = formatBytes(file.size);
            if (bytesEl) bytesEl.innerText = `0 KB / ${totalFormatted}`;
            if (percentEl) percentEl.innerText = '0%';
            if (barEl) barEl.style.width = '0%';

            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', (event) => {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    if (barEl) barEl.style.width = percent + '%';
                    if (percentEl) percentEl.innerText = percent + '%';
                    if (bytesEl) bytesEl.innerText = `${formatBytes(event.loaded)} / ${totalFormatted}`;

                    if (percent >= 100 && statusEl) {
                        statusEl.innerHTML = `
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>Menyimpan berkas & memperbarui tiket...</span>
                        `;
                    }
                }
            });

            xhr.addEventListener('load', () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    if (modal) {
                        modal.close();
                    }
                    window.location.reload();
                } else {
                    let errMsg = 'Terjadi kesalahan saat mengunggah (HTTP ' + xhr.status + ').';
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.message) errMsg = res.message;
                        if (res.errors) errMsg = Object.values(res.errors).flat().join(', ');
                    } catch(e) {}
                    showError(errMsg);
                }
            });

            xhr.addEventListener('error', () => {
                showError('Koneksi internet terputus atau gagal menghubungi server storage.');
            });

            function showError(msg) {
                closeBtns.forEach(btn => btn.style.pointerEvents = 'auto');
                if (errorContainer && errorMsgEl) {
                    errorMsgEl.innerText = msg;
                    errorContainer.classList.remove('hidden');
                }
                if (statusEl) {
                    statusEl.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span class="text-rose-900 font-bold">Proses terhenti karena kesalahan.</span>
                    `;
                }
                if (retryBtn) {
                    retryBtn.onclick = () => {
                        resetUploadForm(form);
                    };
                }
            }

            xhr.open('POST', form.action);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfMeta.content);
            }
            xhr.send(formData);
        });

        // Auto reset when dialog closes
        document.querySelectorAll('dialog').forEach(modal => {
            modal.addEventListener('close', () => {
                const form = modal.querySelector('form[data-ajax-upload]');
                if (form) resetUploadForm(form);
            });
        });
    }
    window.initAjaxUploads = initAjaxUploads;
    </script>

    @stack('scripts')
</body>
</html>
