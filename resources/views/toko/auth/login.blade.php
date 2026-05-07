<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Toko - Login Akses</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Base Dark Luxury Theme */
        body {
            background-color: #020202;
            overflow-x: hidden;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .font-display {
            font-family: Georgia, "Times New Roman", serif;
        }

        /* Ambient Animated Blobs */
        .blob-1 {
            position: absolute;
            top: -10%; left: -10%;
            width: 50vw; height: 50vw;
            background: radial-gradient(circle, rgba(234, 179, 8, 0.15) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            animation: moveBlob 20s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }
        .blob-2 {
            position: absolute;
            bottom: -20%; right: -10%;
            width: 60vw; height: 60vw;
            background: radial-gradient(circle, rgba(202, 138, 4, 0.1) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            animation: moveBlob 25s infinite alternate-reverse cubic-bezier(0.4, 0, 0.2, 1);
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }

        @keyframes moveBlob {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(10%, 15%) scale(1.1); }
            100% { transform: translate(-10%, -5%) scale(0.9); }
        }

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(15, 15, 15, 0.65);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(234, 179, 8, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8), 0 0 20px rgba(234, 179, 8, 0.05);
            position: relative;
            z-index: 10;
        }

        /* Entry Animations */
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up {
            animation: slideUpFade 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .delay-100 { animation-delay: 100ms; opacity: 0; }
        .delay-200 { animation-delay: 200ms; opacity: 0; }
        .delay-300 { animation-delay: 300ms; opacity: 0; }

        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .animate-float {
            animation: float 5s ease-in-out infinite;
        }

        /* Inputs */
        .input-dark {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .input-dark:focus {
            border-color: rgba(234, 179, 8, 0.6);
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.15);
            outline: none;
            background: rgba(0, 0, 0, 0.8);
        }

        /* Button */
        .btn-gold {
            background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
            box-shadow: 0 4px 15px rgba(202, 138, 4, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-gold::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.3), transparent);
            transform: skewX(-20deg);
            transition: 0.7s;
        }
        .btn-gold:hover {
            box-shadow: 0 8px 25px rgba(202, 138, 4, 0.5);
            transform: translateY(-2px);
        }
        .btn-gold:hover::after {
            left: 150%;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative">
    
    <!-- Background Animations -->
    <div class="blob-1"></div>
    <div class="blob-2"></div>

    <div class="w-full max-w-md px-6 relative z-10">
        <!-- Logo -->
        <div class="text-center mb-8 animate-slide-up">
            <div class="inline-block animate-float">
                <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne Logo" class="h-16 w-auto mx-auto object-contain drop-shadow-2xl">
            </div>
            <h1 class="font-display text-3xl text-white font-bold mt-4 tracking-wide">Ayo Renne Store</h1>
            <p class="text-white/50 text-sm mt-2 font-light tracking-widest uppercase">Portal Akses Pegawai</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-3xl p-8 sm:p-10 animate-slide-up delay-100 relative overflow-hidden">
            <!-- Subtle top border glow inside the card -->
            <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-yellow-500/50 to-transparent"></div>

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm mb-6 flex items-start gap-3 animate-slide-up">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <!-- Email Input -->
                <div class="animate-slide-up delay-200">
                    <label for="email" class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-white/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="input-dark w-full pl-11 pr-4 py-3.5 rounded-xl text-sm placeholder-white/20"
                            placeholder="admin@ayorenne.com">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="animate-slide-up delay-300">
                    <label for="password" class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-white/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password" id="password" required
                            class="input-dark w-full pl-11 pr-4 py-3.5 rounded-xl text-sm placeholder-white/20"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 animate-slide-up delay-300">
                    <button type="submit" class="btn-gold w-full text-black font-bold py-3.5 rounded-xl uppercase tracking-widest text-sm flex justify-center items-center gap-2 group">
                        Masuk Ke Sistem
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center animate-slide-up delay-300">
                <a href="{{ route('public.home') }}" class="text-xs text-white/40 hover:text-yellow-400 transition-colors uppercase tracking-widest flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="text-center mt-8 text-white/20 text-[10px] uppercase tracking-widest animate-slide-up delay-300">
            &copy; {{ date('Y') }} Ayo Renne Retail System. Secure Login.
        </div>
    </div>
</body>
</html>
