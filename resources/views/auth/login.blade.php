<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - Gemini Art Percetakan & Studio Desain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] text-slate-900 font-sans min-h-screen flex items-center justify-center p-4 antialiased selection:bg-amber-300 selection:text-black">

    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
        
        <!-- Left Banner: Information & 1-Click Fast Login -->
        <div class="md:col-span-7 flex flex-col justify-between p-6 md:p-8 bg-amber-300 border-2 border-black rounded-2xl shadow-[6px_6px_0px_0px_#000]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_#000] text-xs font-black uppercase mb-6 text-black">
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Multi-Cabang Cloud Operating System</span>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-black text-black tracking-tight leading-tight mb-3">
                    GEMINI ART
                    <span class="block text-xl md:text-2xl font-extrabold text-slate-800">Manajemen Percetakan & Studio Desain</span>
                </h1>

                <p class="text-sm font-semibold text-slate-800 mb-6 leading-relaxed">
                    Sistem terpusat pemisahan kasir ritel cepat (fotokopi/ATK), komitmen biaya order desain kustom, workdesk antrean pengerjaan, dan otomatisasi arsip file cetak cloud (S3/R2).
                </p>
            </div>

            <!-- Quick Demo Login Buttons (Instant Access) -->
            <div class="mt-4 pt-4 border-t-2 border-black">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Akses Cepat Demo (1-Click Login):
                    </span>
                    <span class="text-[11px] font-bold text-slate-700 bg-white px-2 py-0.5 rounded border border-black shadow-[1px_1px_0px_0px_#000]">No Password</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($demoUsers as $user)
                    <form method="POST" action="{{ route('quick-login') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <button type="submit" 
                                class="w-full text-left p-2.5 bg-white hover:bg-amber-100 border-2 border-black rounded-xl shadow-[3px_3px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all cursor-pointer">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-xs text-black">{{ $user->name }}</span>
                                <span class="text-[10px] font-black uppercase px-1.5 py-0.2 rounded border border-black {{ $user->role === 'owner' ? 'bg-purple-300' : ($user->role === 'cashier' ? 'bg-emerald-300' : 'bg-blue-300') }}">
                                    {{ $user->role }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-600 font-semibold truncate mt-0.5">
                                {{ $user->branch ? $user->branch->name : 'Global Multi-Cabang' }}
                            </div>
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Side: Standard Login Form -->
        <div class="md:col-span-5 flex flex-col justify-center p-6 md:p-8 bg-white border-2 border-black rounded-2xl shadow-[6px_6px_0px_0px_#000]">
            <h2 class="text-2xl font-black text-black mb-1">Masuk Akun</h2>
            <p class="text-xs font-medium text-slate-500 mb-6">Gunakan email & password terdaftar untuk masuk.</p>

            @if(session('info'))
                <div class="mb-4 p-3 bg-sky-100 border-2 border-black rounded-xl text-xs font-bold text-black flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 bg-rose-100 border-2 border-black rounded-xl text-xs font-bold text-rose-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-black mb-1.5">Email Karyawan / Owner</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', 'owner@geminiart.test') }}" 
                           required 
                           placeholder="nama@geminiart.test"
                           class="neo-input text-sm">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-black uppercase tracking-wider text-black">Password</label>
                        <span class="text-[11px] font-bold text-slate-400">Default: password</span>
                    </div>
                    <input type="password" 
                           name="password" 
                           value="password" 
                           required 
                           class="neo-input text-sm">
                </div>

                <div class="flex items-center justify-between text-xs font-bold">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 border-2 border-black rounded accent-black">
                        <span>Ingat Sesi Saya</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full neo-btn bg-emerald-400 hover:bg-emerald-300 text-black font-black text-sm py-3 mt-2 flex items-center justify-center gap-2 cursor-pointer">
                    <span>Masuk ke Sistem</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <div class="mt-6 pt-4 border-t-2 border-black text-center text-xs text-slate-500 font-semibold">
                Sistem internal operasional cabang. Hubungi Owner jika lupa akses.
            </div>
        </div>

    </div>

</body>
</html>
