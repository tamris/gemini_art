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

                @if(Auth::user()->isOwner())
                <!-- Kelola Cabang -->
                <a href="{{ route('branches.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-[1.5px] transition-all {{ request()->routeIs('branches.*') ? 'bg-rose-300 border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] text-slate-900' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900' }}">
                    <div class="w-7 h-7 rounded-lg border-[1.5px] border-slate-800 flex items-center justify-center text-slate-900 shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('branches.*') ? 'bg-white shadow-[1px_1px_0px_0px_#0f172a]' : 'bg-rose-200' }}">
                        <x-icon name="building-2" class="w-4 h-4" />
                    </div>
                    <span>Kelola Cabang</span>
                </a>
                @endif
            </div>

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
            <div class="mb-6 p-4 bg-emerald-50 border-[1.5px] border-emerald-600/80 rounded-xl shadow-[2px_2px_0px_0px_#059669] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-400 border-[1.5px] border-slate-800 shadow-[1px_1px_0px_0px_#0f172a] flex items-center justify-center text-slate-900">
                        <x-icon name="check-circle-2" class="w-5 h-5 text-slate-900" stroke-width="2.5" />
                    </div>
                    <p class="font-bold text-sm text-emerald-950">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="font-bold text-slate-600 hover:text-slate-900 px-2 cursor-pointer">✕</button>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-6 p-4 bg-sky-50 border-[1.5px] border-sky-600/80 rounded-xl shadow-[2px_2px_0px_0px_#0284c7] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-sky-300 border-[1.5px] border-slate-800 shadow-[1px_1px_0px_0px_#0f172a] flex items-center justify-center text-slate-900">
                        <x-icon name="info" class="w-5 h-5 text-slate-900" stroke-width="2.5" />
                    </div>
                    <p class="font-bold text-sm text-sky-950">{{ session('info') }}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="font-bold text-slate-600 hover:text-slate-900 px-2 cursor-pointer">✕</button>
            </div>
            @endif

            @if(session('error') || $errors->any())
            <div class="mb-6 p-4 bg-rose-50 border-[1.5px] border-rose-500/80 rounded-xl shadow-[2px_2px_0px_0px_#e11d48]">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-400 border-[1.5px] border-slate-800 shadow-[1px_1px_0px_0px_#0f172a] flex items-center justify-center text-white">
                        <x-icon name="alert-triangle" class="w-5 h-5 text-white" stroke-width="2.5" />
                    </div>
                    <p class="font-bold text-sm text-rose-950">{{ session('error') ?? 'Terjadi kesalahan pada input data:' }}</p>
                </div>
                @if($errors->any())
                <ul class="list-disc list-inside text-xs font-semibold text-rose-900 pl-11 space-y-1">
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
        <footer class="bg-white border-t-[1.5px] border-slate-800 py-4 mt-auto">
            <div class="w-full px-4 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-semibold text-slate-500">
                <div>
                    <strong>Gemini Art Multi-Cabang</strong> &bull; ERP Percetakan & Studio Desain
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1.5">
                        <x-icon name="cloud" class="w-3.5 h-3.5 text-blue-600" />
                        Cloud Storage: <strong class="text-blue-700">Active</strong>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <x-icon name="hard-drive" class="w-3.5 h-3.5 text-emerald-600" />
                        Database: <strong class="text-emerald-700">SQLite Connected</strong>
                    </span>
                </div>
            </div>
        </footer>

    </div>

    <!-- Quick Role Switcher Modal (Accessible Native <dialog>) -->
    <dialog id="quick-switch-modal" closedby="any" class="p-0 bg-transparent max-w-lg w-full m-auto">
        <div class="neo-box p-6 bg-white max-w-md w-full mx-auto">
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

    <!-- Mobile Sidebar Toggle Script -->
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
    </script>

    @stack('scripts')
</body>
</html>
