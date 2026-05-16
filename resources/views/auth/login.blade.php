<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Operasional — Ayo Renne Premium</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');

        :root {
            --gold-primary: #eab308;
            --gold-light: #fef08a;
            --gold-dark: #a16207;
            --obsidian-950: #020617;
            --obsidian-900: #0f172a;
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--obsidian-950);
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(234, 179, 8, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 100% 100%, rgba(234, 179, 8, 0.05) 0%, transparent 40%);
            overflow-x: hidden;
        }

        .font-luxury { font-family: 'Playfair Display', serif; }
        
        .glass-container {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(234, 179, 8, 0.1);
        }

        .gold-gradient-text {
            background: linear-gradient(to right, var(--gold-light), var(--gold-primary), var(--gold-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gold-button {
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark));
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .gold-button::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
            transform: scale(0);
            transition: transform 0.6s;
        }

        .gold-button:hover::after {
            transform: scale(1);
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .input-glass:focus-within {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(234, 179, 8, 0.3);
            box-shadow: 0 0 20px rgba(234, 179, 8, 0.05);
        }

        .role-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s ease;
        }

        .role-card:hover {
            background: rgba(234, 179, 8, 0.03);
            border-color: rgba(234, 179, 8, 0.15);
            transform: translateY(-5px);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .animate-float { animation: float 6s ease-in-out infinite; }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <!-- BACKGROUND DECO -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-gold-primary/5 blur-[120px] rounded-full animate-pulse"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-gold-primary/5 blur-[120px] rounded-full animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <div class="relative w-full max-w-6xl glass-container rounded-[3rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.6)] overflow-hidden flex flex-col lg:flex-row min-h-[700px]">
        <!-- LEFT: LOGIN FORM -->
        <div class="w-full lg:w-[45%] p-10 sm:p-14 lg:p-16 flex flex-col justify-center relative z-10">
            <div class="mb-12">
                <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne" class="h-16 w-auto object-contain mb-8">
                <div class="space-y-2">
                    <span class="text-[10px] font-black uppercase tracking-[0.4em] text-gold-primary/60">Akses Internal</span>
                    <h1 class="text-4xl font-black text-white italic tracking-tighter leading-none">Selamat Datang <br><span class="gold-gradient-text not-italic font-luxury text-5xl">Kembali.</span></h1>
                    <p class="text-xs text-white/40 font-medium leading-relaxed max-w-[280px]">Otorisasi akses untuk memasuki ekosistem operasional <span class="text-white/60">Ayo Renne Premium.</span></p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-8 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
                    <div class="w-2 h-2 rounded-full bg-red-500 shadow-lg shadow-red-500/50"></div>
                    <p class="text-xs font-bold text-red-100 italic">{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-2 group">
                    <label class="text-[9px] font-black uppercase tracking-widest text-white/20 ml-2 group-focus-within:text-gold-primary transition-colors">Credential Email</label>
                    <div class="input-glass flex items-center gap-4 px-6 py-5 rounded-[1.5rem]">
                        <svg class="h-5 w-5 text-white/20 group-focus-within:text-gold-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="yourname@ayorenne.com"
                            class="bg-transparent border-none p-0 w-full text-sm font-bold text-white placeholder:text-white/10 focus:ring-0">
                    </div>
                </div>

                <div class="space-y-2 group">
                    <label class="text-[9px] font-black uppercase tracking-widest text-white/20 ml-2 group-focus-within:text-gold-primary transition-colors">Secure Password</label>
                    <div class="input-glass flex items-center gap-4 px-6 py-5 rounded-[1.5rem]">
                        <svg class="h-5 w-5 text-white/20 group-focus-within:text-gold-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input type="password" name="password" required placeholder="••••••••"
                            class="bg-transparent border-none p-0 w-full text-sm font-bold text-white placeholder:text-white/10 focus:ring-0 tracking-widest">
                    </div>
                </div>

                <button type="submit" class="gold-button w-full py-5 rounded-[1.5rem] shadow-xl shadow-gold-primary/10 text-xs font-black uppercase tracking-[0.3em] text-obsidian-950 hover:scale-[1.02] active:scale-95">
                    Authorize Access
                </button>
            </form>

            <div class="mt-12 flex items-center justify-between">
                <a href="{{ url('/') }}" class="text-[10px] font-black uppercase tracking-widest text-white/20 hover:text-gold-primary transition-colors">← Back to Landing</a>
                <div class="flex items-center gap-1 opacity-10">
                    <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                </div>
            </div>
        </div>

        <!-- RIGHT: BRANDING & ROLES -->
        <div class="hidden lg:flex flex-1 relative overflow-hidden bg-obsidian-900 border-l border-white/5">
            <!-- BACKGROUND IMAGE -->
            <div class="absolute inset-0">
                <img src="{{ asset('images/landing/hero-bg.jpg') }}" class="w-full h-full object-cover scale-110 blur-[2px] opacity-40" alt="Background">
                <div class="absolute inset-0 bg-gradient-to-t from-obsidian-950 via-obsidian-950/80 to-transparent"></div>
            </div>

            <div class="relative w-full h-full p-16 flex flex-col justify-end">
                <div class="mb-12 max-w-md">
                    <span class="px-3 py-1.5 rounded-xl bg-gold-primary/10 border border-gold-primary/20 text-[9px] font-black text-gold-primary uppercase tracking-[0.2em] mb-6 inline-block">Sistem Operasional v2.0</span>
                    <h2 class="text-6xl font-black text-white italic leading-[0.95] tracking-tighter mb-6">
                        Kendali Penuh <br><span class="gold-gradient-text not-italic font-luxury">Bisnis Anda.</span>
                    </h2>
                    <p class="text-sm text-white/40 leading-relaxed italic">Platform terintegrasi untuk manajemen reservasi, inventaris, dan pos dalam satu ekosistem eksklusif.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="role-card p-6 rounded-[2rem]">
                        <div class="w-8 h-8 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20 mb-4">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Administrator</h4>
                        <p class="text-[9px] text-white/30 italic">Laporan & Audit Global</p>
                    </div>

                    <div class="role-card p-6 rounded-[2rem]">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20 mb-4">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Cashier Unit</h4>
                        <p class="text-[9px] text-white/30 italic">Transaksi & Layanan Meja</p>
                    </div>

                    <div class="role-card p-6 rounded-[2rem]">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20 mb-4">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Kitchen Crew</h4>
                        <p class="text-[9px] text-white/30 italic">Manajemen Pesanan Masuk</p>
                    </div>

                    <div class="role-card p-6 rounded-[2rem]">
                        <div class="w-8 h-8 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-500/20 mb-4">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Employee</h4>
                        <p class="text-[9px] text-white/30 italic">Absensi & Riwayat Kerja</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>