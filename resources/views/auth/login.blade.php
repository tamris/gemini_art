<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - Gemini Art Percetakan & Studio Desain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] text-slate-900 font-sans min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 relative selection:bg-amber-300 selection:text-slate-950">

    <!-- Subtle Ambient Accents -->
    <div class="absolute -top-24 -left-24 w-80 h-80 bg-amber-200/25 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-sky-200/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Centered Internal App Login Box -->
    <div class="max-w-md w-full neo-box p-7 sm:p-9 bg-white border-[1.5px] border-slate-800 shadow-[4px_4px_0px_0px_#0f172a] relative z-10">
        
        <!-- Brand Header (Clean Internal Identity) -->
        <div class="text-center pb-6 border-b border-slate-200 mb-6">
            <div class="w-14 h-14 bg-amber-300 border-[1.5px] border-slate-800 shadow-[2px_2px_0px_0px_#0f172a] rounded-2xl flex items-center justify-center text-slate-950 mx-auto mb-3">
                <x-icon name="printer" class="w-7 h-7" stroke-width="2.5" />
            </div>
            <h1 class="text-2xl font-black text-slate-950 tracking-tight leading-none">
                GEMINI ART
            </h1>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1.5">
                Sistem Operasional Percetakan
            </p>
        </div>

        <!-- Form Title -->
        <div class="mb-5">
            <h2 class="text-lg font-black text-slate-900">Masuk Akun</h2>
            <p class="text-xs font-medium text-slate-500 mt-0.5">
                Masukkan email dan kata sandi untuk mengakses sistem.
            </p>
        </div>

        <!-- Session Notification (e.g. Logout / Info) -->
        @if(session('info'))
            <div class="mb-4 p-3 bg-sky-50 border-[1.5px] border-sky-600/80 rounded-xl text-xs font-bold text-sky-950 flex items-center gap-2.5 shadow-[1.5px_1.5px_0px_0px_#0284c7]">
                <x-icon name="info" class="w-4 h-4 text-sky-700 shrink-0" />
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Error Notifications -->
        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border-[1.5px] border-rose-500/80 rounded-xl text-xs font-bold text-rose-950 flex items-center gap-2.5 shadow-[1.5px_1.5px_0px_0px_#e11d48]">
                <x-icon name="alert-triangle" class="w-4 h-4 text-rose-600 shrink-0" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                    Alamat Email
                </label>
                <div class="flex items-center rounded-xl border-[1.5px] border-slate-300 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] focus-within:border-slate-800 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] bg-white transition-all overflow-hidden">
                    <span class="pl-3.5 pr-2.5 text-slate-400 flex items-center justify-center shrink-0">
                        <x-icon name="mail" class="w-4 h-4" />
                    </span>
                    <input type="email" 
                           id="email"
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           autocomplete="email"
                           placeholder="nama@cabang.com"
                           class="w-full py-2.5 pr-3.5 text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none bg-transparent">
                </div>
            </div>

            <!-- Password Input with Show/Hide Toggle -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="text-xs font-black uppercase tracking-wider text-slate-800">
                        Kata Sandi
                    </label>
                </div>
                <div class="flex items-center rounded-xl border-[1.5px] border-slate-300 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] focus-within:border-slate-800 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] bg-white transition-all overflow-hidden">
                    <span class="pl-3.5 pr-2.5 text-slate-400 flex items-center justify-center shrink-0">
                        <x-icon name="lock" class="w-4 h-4" />
                    </span>
                    <input type="password" 
                           id="password"
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Masukkan kata sandi"
                           class="w-full py-2.5 px-1 text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none bg-transparent">
                    <button type="button" 
                            onclick="togglePasswordVisibility()" 
                            id="btn-toggle-password"
                            aria-label="Tampilkan atau sembunyikan kata sandi"
                            class="px-3.5 py-2.5 text-slate-400 hover:text-slate-700 cursor-pointer transition-colors shrink-0 flex items-center justify-center">
                        <span id="eye-icon">
                            <x-icon name="eye" class="w-4 h-4" />
                        </span>
                    </button>
                </div>
            </div>

            <!-- Remember Me & Help Hint -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700 select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[1.5px] border-slate-700 text-amber-500 focus:ring-amber-400">
                    <span>Ingat sesi saya</span>
                </label>
                <span class="text-[11px] font-semibold text-slate-500">
                    Lupa akses? Hubungi Owner
                </span>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full neo-btn bg-amber-300 hover:bg-amber-400 text-slate-950 font-black text-sm py-3 mt-2 flex items-center justify-center gap-2 cursor-pointer shadow-[2px_2px_0px_0px_#0f172a] hover:shadow-[3px_3px_0px_0px_#0f172a]">
                <span>Masuk ke Sistem</span>
                <x-icon name="arrow-right" class="w-4 h-4" stroke-width="2.5" />
            </button>
        </form>

        <!-- Internal Access Note -->
        <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-400 font-medium">
            Khusus staf kasir, desainer, dan manajemen Gemini Art.
        </div>
    </div>

    <!-- Minimal Copyright Footer -->
    <div class="mt-6 text-center text-xs font-semibold text-slate-400">
        &copy; {{ date('Y') }} Gemini Art &bull; Solo, Jogja & Pusat
    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const isPassword = passwordInput.type === 'password';
        
        passwordInput.type = isPassword ? 'text' : 'password';
        
        // Switch between eye and eye-off icon
        if (isPassword) {
            eyeIcon.innerHTML = `<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49" /><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242" /><path d="M17.479 17.499A10.75 10.75 0 0 1 2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 2.898-4.084" /><line x1="2" x2="22" y1="2" y2="22" /></svg>`;
        } else {
            eyeIcon.innerHTML = `<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" /><circle cx="12" cy="12" r="3" /></svg>`;
        }
    }
    </script>

</body>
</html>
