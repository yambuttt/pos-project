<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Ayo Renne Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- AlpineJS for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #020202;
            color: #ffffff;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }

        .glass-sidebar {
            background: rgba(10, 10, 10, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(234, 179, 8, 0.1);
        }

        .glass-topbar {
            background: rgba(10, 10, 10, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(234, 179, 8, 0.1);
        }

        /* Subtle Background Glows */
        .ambient-glow {
            position: fixed;
            width: 50vw; height: 50vw;
            background: radial-gradient(circle, rgba(234, 179, 8, 0.05) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            top: -20%; left: 20%;
            pointer-events: none;
            z-index: -1;
            animation: pulse-glow 15s infinite alternate ease-in-out;
        }

        @keyframes pulse-glow {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.2); opacity: 1; }
        }

        /* Nav Link Hover Effects */
        .nav-link {
            transition: all 0.3s ease;
            position: relative;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(234, 179, 8, 0.1);
            color: #facc15; /* yellow-400 */
        }
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 10%;
            height: 80%; width: 3px;
            background: #eab308;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px #eab308;
        }

        /* Main Content Entry Animation */
        .main-content {
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Scrollbar customization */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0a0a0a;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(234, 179, 8, 0.3);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(234, 179, 8, 0.6);
        }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }" class="antialiased h-screen flex overflow-hidden selection:bg-yellow-500 selection:text-black">
    
    <div class="ambient-glow"></div>

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/80 lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 glass-sidebar transform transition-transform duration-500 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:flex flex-col h-full">
        <!-- Brand -->
        <div class="h-20 flex items-center justify-center border-b border-yellow-500/10 px-6">
            <a href="{{ route('toko.admin.dashboard') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Logo" class="h-10 w-auto object-contain transform group-hover:scale-110 transition-transform duration-500">
                <div class="flex flex-col">
                    <span class="font-bold text-lg tracking-wider text-white">AYO RENNE</span>
                    <span class="text-[10px] uppercase tracking-[0.2em] text-yellow-500">Store Admin</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <div class="text-xs font-semibold text-white/40 uppercase tracking-widest mb-4 px-3">Menu Utama</div>
            
            <a href="{{ route('toko.admin.dashboard') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium text-white/80">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>
            
            <a href="#" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium text-white/70">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Katalog Produk
            </a>

            <a href="#" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium text-white/70">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Laporan Penjualan
            </a>

            <a href="#" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium text-white/70">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Pegawai Kasir
            </a>
        </div>

        <!-- User Info / Logout -->
        <div class="p-4 border-t border-yellow-500/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Topbar -->
        <header class="h-20 glass-topbar flex items-center justify-between px-4 sm:px-6 lg:px-8 z-30">
            <!-- Mobile Menu Button -->
            <button @click="sidebarOpen = true" class="lg:hidden text-white/70 hover:text-yellow-400 p-2 rounded-lg transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <!-- Search / Breadcrumb Placeholder -->
            <div class="hidden lg:flex flex-1 items-center gap-4">
                <div class="relative w-96 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-white/30 group-focus-within:text-yellow-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" class="w-full bg-black/40 border border-white/10 text-white text-sm rounded-full pl-11 pr-4 py-2.5 focus:outline-none focus:border-yellow-500/50 focus:ring-1 focus:ring-yellow-500/50 transition-all placeholder-white/30" placeholder="Pencarian cepat...">
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4 ml-auto">
                <button class="relative p-2 text-white/70 hover:text-yellow-400 transition-colors rounded-full hover:bg-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border border-[#0a0a0a]"></span>
                </button>

                <div class="h-8 w-px bg-white/10 mx-2"></div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-white">{{ auth()->user()->name ?? 'Administrator' }}</div>
                        <div class="text-[10px] uppercase tracking-wider text-yellow-500">Toko Admin</div>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-yellow-600 to-yellow-400 p-0.5 shadow-[0_0_15px_rgba(234,179,8,0.3)]">
                        <div class="w-full h-full rounded-full bg-[#121212] flex items-center justify-center text-yellow-500 font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 main-content relative">
            @yield('content')
        </main>
        
    </div>

    @stack('scripts')
</body>
</html>
