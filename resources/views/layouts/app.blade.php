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
           class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r-2 border-black flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
        
        <!-- Top: Brand Header -->
        <div class="h-20 px-5 border-b-2 border-black bg-white flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group text-decoration-none">
                <div class="w-11 h-11 bg-amber-300 border-2 border-black rounded-xl shadow-[3px_3px_0px_0px_#000] flex items-center justify-center font-black text-xl group-hover:translate-x-[1px] group-hover:translate-y-[1px] transition-transform text-black">
                    GA
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-black text-lg tracking-tight text-black">GEMINI ART</span>
                    </div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Studio & Percetakan
                    </p>
                </div>
            </a>

            <!-- Mobile Close Button -->
            <button type="button" 
                    onclick="toggleSidebar()" 
                    class="lg:hidden p-1.5 rounded-lg border-2 border-black bg-rose-200 hover:bg-rose-300 font-black text-sm cursor-pointer">
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
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-2 transition-all {{ request()->routeIs('dashboard') ? 'bg-amber-300 border-black shadow-[3px_3px_0px_0px_#000]' : 'border-transparent text-slate-700 hover:border-black hover:bg-slate-100' }}">
                    <div class="w-7 h-7 rounded-lg border-2 border-black flex items-center justify-center text-black shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('dashboard') ? 'bg-white shadow-[1px_1px_0px_0px_#000]' : 'bg-amber-100' }}">
                        <x-icon name="layout-dashboard" class="w-4 h-4" />
                    </div>
                    <span>Dashboard</span>
                </a>

                <!-- Semua Order -->
                <a href="{{ route('orders.index') }}" 
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-xs font-black border-2 transition-all {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? 'bg-amber-300 border-black shadow-[3px_3px_0px_0px_#000]' : 'border-transparent text-slate-700 hover:border-black hover:bg-slate-100' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg border-2 border-black flex items-center justify-center text-black shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? 'bg-white shadow-[1px_1px_0px_0px_#000]' : 'bg-amber-100' }}">
                            <x-icon name="clipboard-list" class="w-4 h-4" />
                        </div>
                        <span>Semua Order</span>
                    </div>
                    <span class="text-[10px] font-extrabold bg-white border border-black px-1.5 py-0.2 rounded shadow-[1px_1px_0px_0px_#000]">
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
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-2 transition-all {{ request()->routeIs('orders.create-design') ? 'bg-sky-400 border-black shadow-[3px_3px_0px_0px_#000]' : 'border-transparent text-slate-700 hover:border-black hover:bg-slate-100' }}">
                    <div class="w-7 h-7 rounded-lg border-2 border-black flex items-center justify-center text-black shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('orders.create-design') ? 'bg-white shadow-[1px_1px_0px_0px_#000]' : 'bg-sky-200' }}">
                        <x-icon name="palette" class="w-4 h-4" />
                    </div>
                    <span>Order Desain Baru</span>
                </a>

                <!-- Kasir POS Ritel -->
                <a href="{{ route('pos.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-2 transition-all {{ request()->routeIs('pos.*') ? 'bg-emerald-300 border-black shadow-[3px_3px_0px_0px_#000]' : 'border-transparent text-slate-700 hover:border-black hover:bg-slate-100' }}">
                    <div class="w-7 h-7 rounded-lg border-2 border-black flex items-center justify-center text-black shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('pos.*') ? 'bg-white shadow-[1px_1px_0px_0px_#000]' : 'bg-emerald-200' }}">
                        <x-icon name="shopping-cart" class="w-4 h-4" />
                    </div>
                    <span>Kasir POS Ritel (FC/ATK)</span>
                </a>

                <!-- Workdesk Desainer -->
                <a href="{{ route('workdesk.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-2 transition-all {{ request()->routeIs('workdesk.*') ? 'bg-blue-400 border-black shadow-[3px_3px_0px_0px_#000]' : 'border-transparent text-slate-700 hover:border-black hover:bg-slate-100' }}">
                    <div class="w-7 h-7 rounded-lg border-2 border-black flex items-center justify-center text-black shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('workdesk.*') ? 'bg-white shadow-[1px_1px_0px_0px_#000]' : 'bg-blue-200' }}">
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
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-2 transition-all {{ request()->routeIs('cash.*') ? 'bg-purple-300 border-black shadow-[3px_3px_0px_0px_#000]' : 'border-transparent text-slate-700 hover:border-black hover:bg-slate-100' }}">
                    <div class="w-7 h-7 rounded-lg border-2 border-black flex items-center justify-center text-black shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('cash.*') ? 'bg-white shadow-[1px_1px_0px_0px_#000]' : 'bg-purple-200' }}">
                        <x-icon name="wallet" class="w-4 h-4" />
                    </div>
                    <span>Buku Kas & Mutasi</span>
                </a>

                <!-- Arsip Cloud (S3/R2) -->
                <a href="{{ route('archive.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-2 transition-all {{ request()->routeIs('archive.*') ? 'bg-yellow-200 border-black shadow-[3px_3px_0px_0px_#000]' : 'border-transparent text-slate-700 hover:border-black hover:bg-slate-100' }}">
                    <div class="w-7 h-7 rounded-lg border-2 border-black flex items-center justify-center text-black shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('archive.*') ? 'bg-white shadow-[1px_1px_0px_0px_#000]' : 'bg-amber-200' }}">
                        <x-icon name="archive" class="w-4 h-4" />
                    </div>
                    <span>Arsip Cloud (S3/R2)</span>
                </a>

                @if(Auth::user()->isOwner())
                <!-- Kelola Cabang -->
                <a href="{{ route('branches.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-black border-2 transition-all {{ request()->routeIs('branches.*') ? 'bg-rose-300 border-black shadow-[3px_3px_0px_0px_#000]' : 'border-transparent text-slate-700 hover:border-black hover:bg-slate-100' }}">
                    <div class="w-7 h-7 rounded-lg border-2 border-black flex items-center justify-center text-black shrink-0 transition-transform group-hover:scale-105 {{ request()->routeIs('branches.*') ? 'bg-white shadow-[1px_1px_0px_0px_#000]' : 'bg-rose-200' }}">
                        <x-icon name="building-2" class="w-4 h-4" />
                    </div>
                    <span>Kelola Cabang</span>
                </a>
                @endif
            </div>

        </div>

        <!-- Bottom: Realtime Branch Drawer Widget & User Account Card -->
        <div class="p-4 border-t-2 border-black bg-slate-50 space-y-3">
            
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
            <div class="p-3 bg-white border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_#000] text-xs">
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
                <div class="text-sm font-black text-black">
                    Rp {{ number_format($activeBalance, 0, ',', '.') }}
                </div>
            </div>

            <!-- User Info Pill & Quick Switch / Logout -->
            <div class="p-2.5 bg-white border-2 border-black rounded-xl shadow-[2px_2px_0px_0px_#000] flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-8 h-8 rounded-lg border-2 border-black bg-amber-200 flex items-center justify-center font-black text-xs shrink-0 text-black">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-black text-black truncate leading-tight">{{ $user->name }}</div>
                        <div class="text-[10px] font-bold uppercase text-slate-500">{{ $user->role }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <!-- Switch button -->
                    <button type="button" 
                            onclick="document.getElementById('quick-switch-modal').showModal()"
                            title="Ganti Akun Role"
                            class="p-1.5 bg-amber-300 hover:bg-amber-400 border border-black rounded-lg text-black shadow-[1px_1px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] cursor-pointer">
                        <x-icon name="refresh-cw" class="w-3.5 h-3.5" />
                    </button>

                    <!-- Logout button -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                title="Keluar"
                                class="p-1.5 bg-rose-200 hover:bg-rose-300 border border-black rounded-lg text-black shadow-[1px_1px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] cursor-pointer">
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
        <header class="h-20 bg-white border-b-2 border-black sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between">
            
            <!-- Left: Mobile Toggle & Page Title -->
            <div class="flex items-center gap-3">
                <button type="button" 
                        onclick="toggleSidebar()" 
                        class="lg:hidden p-2 rounded-xl border-2 border-black bg-amber-300 hover:bg-amber-400 active:translate-x-[1px] active:translate-y-[1px] shadow-[2px_2px_0px_0px_#000] cursor-pointer">
                    <x-icon name="menu" class="w-5 h-5 text-black" stroke-width="2.5" />
                </button>

                <div>
                    <h2 class="font-black text-sm sm:text-base text-black tracking-tight flex items-center gap-2">
                        <span>@yield('title', 'Gemini Art')</span>
                    </h2>
                </div>
            </div>

            <!-- Right: Indonesian Date Only -->
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 bg-slate-100 border-2 border-black px-3 py-1.5 rounded-xl shadow-[2px_2px_0px_0px_#000]">
                    <x-icon name="calendar" class="w-3.5 h-3.5 text-slate-500" />
                    <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </span>
            </div>

        </header>

        <!-- Main Workspace Area (Full-Width Fluid Responsive) -->
        <main class="flex-1 w-full px-4 sm:px-8 py-6">

            <!-- Flash Notifications -->
            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-100 border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-400 border-2 border-black flex items-center justify-center text-black">
                        <x-icon name="check-circle-2" class="w-5 h-5 text-black" stroke-width="2.5" />
                    </div>
                    <p class="font-bold text-sm text-black">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="font-black text-lg px-2 hover:opacity-75 cursor-pointer">✕</button>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-6 p-4 bg-sky-100 border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-sky-300 border-2 border-black flex items-center justify-center text-black">
                        <x-icon name="info" class="w-5 h-5 text-black" stroke-width="2.5" />
                    </div>
                    <p class="font-bold text-sm text-black">{{ session('info') }}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="font-black text-lg px-2 hover:opacity-75 cursor-pointer">✕</button>
            </div>
            @endif

            @if(session('error') || $errors->any())
            <div class="mb-6 p-4 bg-rose-100 border-2 border-black rounded-xl shadow-[4px_4px_0px_0px_#000]">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-400 border-2 border-black flex items-center justify-center text-white">
                        <x-icon name="alert-triangle" class="w-5 h-5 text-white" stroke-width="2.5" />
                    </div>
                    <p class="font-bold text-sm text-black">{{ session('error') ?? 'Terjadi kesalahan pada input data:' }}</p>
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
        <footer class="bg-white border-t-2 border-black py-4 mt-auto">
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
            <div class="flex items-center justify-between pb-3 border-b-2 border-black mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-amber-300 border-2 border-black rounded-lg flex items-center justify-center text-black">
                        <x-icon name="refresh-cw" class="w-4 h-4" />
                    </div>
                    <h3 class="font-black text-lg text-black">Beralih Akun (Role Switcher)</h3>
                </div>
                <form method="dialog">
                    <button class="w-8 h-8 rounded-lg border-2 border-black bg-rose-200 hover:bg-rose-300 font-black text-base flex items-center justify-center cursor-pointer">
                        ✕
                    </button>
                </form>
            </div>
            <p class="text-xs font-medium text-slate-600 mb-4">Pilih peran akun untuk menguji alur kasir cabang, workdesk desainer, atau command center owner:</p>
            
            <div class="space-y-2.5">
                @php
                    $allUsers = \App\Models\User::with('branch')->get();
                @endphp
                @foreach($allUsers as $u)
                <form method="POST" action="{{ route('quick-login') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $u->id }}">
                    <button type="submit" 
                            class="w-full text-left p-3 rounded-xl border-2 border-black {{ $u->id === Auth::id() ? 'bg-amber-100 shadow-[3px_3px_0px_0px_#000]' : 'bg-slate-50 hover:bg-amber-50 shadow-[2px_2px_0px_0px_#000]' }} active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all flex items-center justify-between cursor-pointer">
                        <div>
                            <div class="font-extrabold text-sm text-black flex items-center gap-2">
                                {{ $u->name }}
                                @if($u->id === Auth::id())
                                    <span class="text-[10px] bg-emerald-400 border border-black px-1.5 py-0.2 rounded font-black uppercase">Aktif</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-600 font-medium">
                                {{ $u->branch ? $u->branch->name : 'Multi-Cabang (Pusat)' }}
                            </div>
                        </div>
                        <span class="text-xs font-black uppercase px-2 py-1 rounded-md border border-black {{ $u->role === 'owner' ? 'bg-purple-300' : ($u->role === 'cashier' ? 'bg-emerald-300' : 'bg-blue-300') }}">
                            {{ $u->role }}
                        </span>
                    </button>
                </form>
                @endforeach
            </div>

            <div class="mt-5 pt-3 border-t-2 border-black text-right">
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
