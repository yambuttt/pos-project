<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir Terminal') - Ayo Renne Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #020202;
            color: #ffffff;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }

        .glass-panel {
            background: rgba(10, 10, 10, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(234, 179, 8, 0.15);
        }

        .glass-topbar {
            background: rgba(5, 5, 5, 0.85);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(234, 179, 8, 0.2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }

        /* Ambient Glows for POS feel */
        .ambient-glow-1 {
            position: fixed;
            width: 60vw; height: 60vw;
            background: radial-gradient(circle, rgba(234, 179, 8, 0.03) 0%, rgba(0,0,0,0) 60%);
            border-radius: 50%;
            top: -30%; right: -10%;
            pointer-events: none;
            z-index: -1;
            animation: float-glow 20s infinite alternate ease-in-out;
        }
        .ambient-glow-2 {
            position: fixed;
            width: 40vw; height: 40vw;
            background: radial-gradient(circle, rgba(202, 138, 4, 0.04) 0%, rgba(0,0,0,0) 60%);
            border-radius: 50%;
            bottom: -20%; left: -10%;
            pointer-events: none;
            z-index: -1;
            animation: float-glow 15s infinite alternate-reverse ease-in-out;
        }

        @keyframes float-glow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-5%, 5%) scale(1.1); }
        }

        /* Kasir Nav Buttons */
        .nav-btn {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .nav-btn:hover {
            background: rgba(234, 179, 8, 0.15);
            color: #facc15;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(234, 179, 8, 0.1);
        }
        .nav-btn.active {
            background: linear-gradient(135deg, rgba(234, 179, 8, 0.2) 0%, rgba(202, 138, 4, 0.05) 100%);
            color: #facc15;
            border-color: rgba(234, 179, 8, 0.4);
        }

        /* Entry Animation */
        .animate-fade-up {
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(234, 179, 8, 0.2); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(234, 179, 8, 0.5); }
    </style>
    @stack('styles')
</head>
<body class="antialiased h-screen flex flex-col overflow-hidden selection:bg-yellow-500 selection:text-black">
    
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <!-- Topbar Kasir -->
    <header class="h-[72px] glass-topbar flex items-center justify-between px-4 sm:px-6 z-30 shrink-0">
        <!-- Brand & Terminal Info -->
        <div class="flex items-center gap-6">
            <a href="{{ route('toko.kasir.dashboard') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Logo" class="h-9 w-auto object-contain transform group-hover:scale-105 transition-transform duration-300">
                <div class="hidden sm:flex flex-col">
                    <span class="font-bold text-base tracking-wider text-white">AYO RENNE</span>
                    <span class="text-[9px] uppercase tracking-[0.2em] text-yellow-500">Retail Terminal</span>
                </div>
            </a>
            
            <div class="hidden md:flex h-8 w-px bg-white/10"></div>
            
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-green-500/10 border border-green-500/20 text-green-400 text-xs font-bold tracking-widest">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                TERMINAL AKTIF
            </div>
        </div>

        <!-- Navigation -->
        <nav class="hidden lg:flex items-center gap-2">
            <a href="{{ route('toko.kasir.dashboard') }}" 
               class="nav-btn {{ request()->routeIs('toko.kasir.dashboard') ? 'active border-yellow-500/40' : 'text-white/70 border-transparent' }} px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>
            
            <a href="{{ route('toko.kasir.pos') }}" 
               class="nav-btn {{ request()->routeIs('toko.kasir.pos') ? 'active border-yellow-500/40' : 'text-white/70 border-transparent' }} px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Buka POS
            </a>
            
            <a href="#" class="nav-btn px-4 py-2 rounded-xl text-sm font-medium text-white/70 border border-transparent flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Riwayat Transaksi
            </a>
        </nav>

        <!-- User Actions -->
        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <div class="text-sm font-bold text-white">{{ auth()->user()->name ?? 'Kasir' }}</div>
                <div id="live-clock" class="text-[10px] uppercase tracking-wider text-yellow-500 font-mono">00:00:00</div>
            </div>
            <div class="h-9 w-9 rounded-full bg-[#121212] border border-yellow-500/30 flex items-center justify-center text-yellow-500 font-bold shadow-[0_0_10px_rgba(234,179,8,0.2)]">
                {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
            </div>
            
            <div class="h-6 w-px bg-white/10 mx-1"></div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-2 text-white/50 hover:text-red-400 hover:bg-red-500/10 rounded-xl transition-colors" title="Tutup Shift & Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </header>

    <!-- Mobile Nav (Visible only on small screens) -->
  <div class="lg:hidden bg-[#0a0a0a] border-b border-white/5 flex overflow-x-auto">
        <a href="{{ route('toko.kasir.dashboard') }}" 
           class="flex-none px-4 py-3 text-xs uppercase tracking-wider {{ request()->routeIs('toko.kasir.dashboard') ? 'font-bold text-yellow-500 border-b-2 border-yellow-500' : 'font-medium text-white/50 hover:text-white' }}">
           Dashboard
        </a>
        
        <a href="{{ route('toko.kasir.pos') }}" 
           class="flex-none px-4 py-3 text-xs uppercase tracking-wider {{ request()->routeIs('toko.kasir.pos') ? 'font-bold text-yellow-500 border-b-2 border-yellow-500' : 'font-medium text-white/50 hover:text-white' }}">
           Buka POS
        </a>
        
        <a href="#" class="flex-none px-4 py-3 text-xs font-medium text-white/50 hover:text-white uppercase tracking-wider">
           Riwayat
        </a>
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 relative">
        @yield('content')
    </main>

    <script>
        // Simple Live Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('live-clock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    @stack('scripts')
</body>
</html>
