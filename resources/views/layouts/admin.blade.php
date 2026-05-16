<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F9E2AF;
            --gold-dark: #996515;
            --dark-bg: #050505;
            --panel-bg: rgba(18, 18, 18, 0.7);
            --sidebar-width-full: 280px;
            --sidebar-width-mini: 80px;
            --transition-speed: 0.4s;
            --font-outfit: 'Outfit', sans-serif;
            color-scheme: dark;
        }

        select option {
            background-color: #121212;
            color: white;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-outfit);
            background-color: var(--dark-bg);
            color: #ffffff;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(212, 175, 55, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(212, 175, 55, 0.03) 0%, transparent 40%);
        }

        /* Glassmorphism Classes */
        .glass-panel {
            background: var(--panel-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            border-color: rgba(212, 175, 55, 0.3);
            background: rgba(212, 175, 55, 0.02);
        }

        /* Gold Gradient Text */
        .text-gold-gradient {
            background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold-primary) 50%, var(--gold-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        /* Sidebar Styling */
        #adminSidebar {
            transition: width var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 50;
        }

        .sidebar-item {
            position: relative;
            transition: all 0.3s ease;
        }

        .sidebar-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%) scaleX(0);
            width: 4px;
            height: 60%;
            background: var(--gold-primary);
            border-radius: 0 4px 4px 0;
            transition: transform 0.3s ease;
            transform-origin: left;
        }

        .sidebar-item.active::before {
            transform: translateY(-50%) scaleX(1);
        }

        .sidebar-item.active {
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold-primary);
        }

        .sidebar-item:hover:not(.active) {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-label {
            transition: opacity 0.3s ease, transform 0.3s ease;
            white-space: nowrap;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--gold-dark);
            border-radius: 10px;
        }

        /* Mobile Adjustments */
        @media (max-width: 1024px) {
            #adminSidebar {
                position: fixed !important;
                left: -100% !important;
                top: 0 !important;
                height: 100vh !important;
                width: var(--sidebar-width-full) !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }
            #adminSidebar.mobile-open {
                left: 0 !important;
            }
            #mainContent {
                margin-left: 0 !important;
            }
        }

        /* Hide Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="min-h-screen">
    @php
        $isDashboard = request()->routeIs('admin.dashboard');
        $isProducts = request()->routeIs('admin.products.*');
        $isSales = request()->routeIs('admin.sales.*');
        $isUsers = request()->routeIs('admin.cashiers.*');
        $isRawMaterials = request()->routeIs('admin.raw_materials.*');
        $isPurchases = request()->routeIs('admin.purchases.*');
        $isAttendanceQr = request()->routeIs('admin.attendance.qr*');
        $isAttendanceDevices = request()->routeIs('admin.attendance.devices*');
        $isAttendanceHistory = request()->routeIs('admin.attendance.history*');
        $isAttendanceExceptions = request()->routeIs('admin.attendance.exception_requests*');
        $isShiftSettings = request()->routeIs('admin.shifts.*');
        $isLeaveRequests = request()->routeIs('admin.leave_requests.*');
        $isLateRequests = request()->routeIs('admin.late_requests.*');
        $isCheckoutCorrections = request()->routeIs('admin.checkout_corrections.*');
        $isOvertimeRequests = request()->routeIs('admin.overtime_requests.*');
        $isUserGroup = $isUsers || $isAttendanceQr || $isAttendanceDevices || $isAttendanceHistory || $isAttendanceExceptions || $isShiftSettings || $isLeaveRequests || $isLateRequests || $isCheckoutCorrections || $isOvertimeRequests;

        $isWastes = request()->routeIs('admin.wastes.*');
        $isOpnames = request()->routeIs('admin.opnames.*');
        $isInvMovements = request()->routeIs('admin.inventory-movements.*');
        $isTables = request()->routeIs('admin.tables.*');
        $isInventory = $isRawMaterials || $isPurchases || $isWastes || $isOpnames || $isInvMovements || $isTables || $isAttendanceQr || $isAttendanceDevices;

        $isReservations = request()->routeIs('admin.reservations.*');
        $isReservationResources = request()->routeIs('admin.reservation_resources.*');
        $isBuffetPackages = request()->routeIs('admin.buffet_packages.*');
        $isReservationsGroup = $isReservations || $isReservationResources || $isBuffetPackages;
    @endphp

    <div class="flex">
        <!-- SIDEBAR -->
        <aside id="adminSidebar" class="glass-panel fixed h-[calc(100vh-2rem)] m-4 rounded-[2rem] overflow-hidden flex flex-col w-[280px]">
            <!-- Header/Logo -->
            <div class="p-6 flex items-center gap-4">
                <div class="h-12 w-12 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Logo" class="h-10 w-auto">
                </div>
                <div class="sidebar-label overflow-hidden">
                    <h1 class="text-gold-gradient font-bold text-lg leading-tight truncate">AYO RENNE</h1>
                    <p class="text-white/40 text-[10px] uppercase tracking-widest">Premium Admin</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-2 space-y-2 overflow-y-auto no-scrollbar">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item group flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ $isDashboard ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 {{ $isDashboard ? 'text-gold-primary' : 'text-white/60 group-hover:text-gold-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="sidebar-label font-medium">Dashboard</span>
                </a>

                <!-- Produk -->
                <a href="{{ route('admin.products.index') }}" class="sidebar-item group flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ $isProducts ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 {{ $isProducts ? 'text-gold-primary' : 'text-white/60 group-hover:text-gold-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="sidebar-label font-medium">Produk</span>
                </a>

                <!-- Inventory Group -->
                <div class="space-y-1">
                    <button type="button" class="sidebar-item group flex w-full items-center gap-4 px-4 py-3.5 rounded-2xl {{ $isInventory ? 'active' : '' }}" onclick="toggleDropdown('inventoryMenu', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 {{ $isInventory ? 'text-gold-primary' : 'text-white/60 group-hover:text-gold-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="sidebar-label flex-1 text-left font-medium">Inventory</span>
                        <svg id="chevron-inventoryMenu" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform {{ $isInventory ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="inventoryMenu" class="{{ $isInventory ? 'block' : 'hidden' }} pl-14 space-y-1 overflow-hidden">
                        <a href="{{ route('admin.raw_materials.index') }}" class="block py-2 text-sm {{ $isRawMaterials ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Bahan Baku</a>
                        <a href="{{ route('admin.purchases.index') }}" class="block py-2 text-sm {{ $isPurchases ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Purchases</a>
                        <a href="{{ route('admin.wastes.index') }}" class="block py-2 text-sm {{ $isWastes ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Waste</a>
                        <a href="{{ route('admin.opnames.index') }}" class="block py-2 text-sm {{ $isOpnames ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Stock Opname</a>
                        <a href="{{ route('admin.inventory-movements.index') }}" class="block py-2 text-sm {{ $isInvMovements ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Movements</a>
                    </div>
                </div>

                <!-- Sales -->
                <a href="{{ route('admin.sales.index') }}" class="sidebar-item group flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ $isSales ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 {{ $isSales ? 'text-gold-primary' : 'text-white/60 group-hover:text-gold-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="sidebar-label font-medium">Transaksi</span>
                </a>

                <!-- Reservasi Group -->
                <div class="space-y-1">
                    <button type="button" class="sidebar-item group flex w-full items-center gap-4 px-4 py-3.5 rounded-2xl {{ $isReservationsGroup ? 'active' : '' }}" onclick="toggleDropdown('resMenu', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 {{ $isReservationsGroup ? 'text-gold-primary' : 'text-white/60 group-hover:text-gold-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V6a2 2 0 012-2h4a2 2 0 012 2v1M5 9h14M6 9v10a2 2 0 002 2h8a2 2 0 002-2V9" />
                        </svg>
                        <span class="sidebar-label flex-1 text-left font-medium">Reservasi</span>
                        <svg id="chevron-resMenu" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform {{ $isReservationsGroup ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="resMenu" class="{{ $isReservationsGroup ? 'block' : 'hidden' }} pl-14 space-y-1 overflow-hidden">
                        <a href="{{ route('admin.reservations.index') }}" class="block py-2 text-sm {{ $isReservations ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Daftar Reservasi</a>
                        <a href="{{ route('admin.reservation_resources.index') }}" class="block py-2 text-sm {{ $isReservationResources ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Resource</a>
                        <a href="{{ route('admin.buffet_packages.index') }}" class="block py-2 text-sm {{ $isBuffetPackages ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Paket Buffet</a>
                    </div>
                </div>

                <!-- User & HRM Group -->
                <div class="space-y-1">
                    <button type="button" class="sidebar-item group flex w-full items-center gap-4 px-4 py-3.5 rounded-2xl {{ $isUserGroup ? 'active' : '' }}" onclick="toggleDropdown('userMenu', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 {{ $isUserGroup ? 'text-gold-primary' : 'text-white/60 group-hover:text-gold-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="sidebar-label flex-1 text-left font-medium">User & HRM</span>
                        <svg id="chevron-userMenu" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform {{ $isUserGroup ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="userMenu" class="{{ $isUserGroup ? 'block' : 'hidden' }} pl-14 space-y-1 overflow-hidden">
                        <a href="{{ route('admin.cashiers.index') }}" class="block py-2 text-sm {{ $isUsers ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Kelola User</a>
                        <a href="{{ route('admin.overtime_requests.index') }}" class="block py-2 text-sm {{ $isOvertimeRequests ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Pengajuan Lembur</a>
                        <a href="{{ route('admin.attendance.exception_requests') }}" class="block py-2 text-sm {{ $isAttendanceExceptions ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Pengajuan Absensi</a>
                        <a href="{{ route('admin.late_requests.index') }}" class="block py-2 text-sm {{ $isLateRequests ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Pengajuan Telat</a>
                        <a href="{{ route('admin.checkout_corrections.index') }}" class="block py-2 text-sm {{ $isCheckoutCorrections ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Koreksi Checkout</a>
                        <a href="{{ route('admin.shifts.index') }}" class="block py-2 text-sm {{ $isShiftSettings ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Shift Pegawai</a>
                        <a href="{{ route('admin.attendance.qr') }}" class="block py-2 text-sm {{ $isAttendanceQr ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Absensi QR</a>
                        <a href="{{ route('admin.attendance.devices') }}" class="block py-2 text-sm {{ $isAttendanceDevices ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Device Absensi</a>
                        <a href="{{ route('admin.attendance.history') }}" class="block py-2 text-sm {{ $isAttendanceHistory ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">History Absensi</a>
                        <a href="{{ route('admin.leave_requests.index') }}" class="block py-2 text-sm {{ $isLeaveRequests ? 'text-gold-primary' : 'text-white/50 hover:text-white' }}">Permintaan Cuti</a>
                    </div>
                </div>

                <!-- Tables -->
                <a href="{{ route('admin.tables.index') }}" class="sidebar-item group flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ $isTables ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 {{ $isTables ? 'text-gold-primary' : 'text-white/60 group-hover:text-gold-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16M4 14h16M4 18h16M4 6h16" />
                    </svg>
                    <span class="sidebar-label font-medium">Meja</span>
                </a>

                <!-- Laporan -->
                <a href="#" class="sidebar-item group flex items-center gap-4 px-4 py-3.5 rounded-2xl text-white/88 hover:bg-white/[0.05]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 text-white/60 group-hover:text-gold-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="sidebar-label font-medium">Laporan</span>
                </a>
            </nav>

            <!-- Footer -->
            <div class="p-6 space-y-4">
                <div class="glass-card rounded-2xl p-4 sidebar-label">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-gold-primary/20 flex items-center justify-center border border-gold-primary/30">
                            <span class="text-gold-primary font-bold">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-[10px] text-white/40 uppercase tracking-tight">{{ auth()->user()->role ?? 'Admin' }}</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-3 px-4 py-4 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark text-obsidian-950 font-black text-xs uppercase tracking-widest transition-all hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] active:scale-95 shadow-xl shadow-gold-primary/20 border border-gold-light/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="sidebar-label">Logout System</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main id="mainContent" class="flex-1 min-h-screen lg:ml-[312px] p-4 lg:p-8 transition-all duration-300">
            <!-- TOP NAVBAR -->
            <header class="glass-panel sticky top-4 mb-8 rounded-[2rem] px-8 py-4 flex items-center justify-between z-40">
                <div class="flex items-center gap-6">
                    <button id="toggleSidebar" class="p-2 rounded-xl hover:bg-white/5 transition-colors" onclick="toggleSidebar()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gold-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight">@yield('title', 'Admin Dashboard')</h2>
                        <p class="text-xs text-white/40">Selamat datang kembali, <span class="text-gold-primary">{{ auth()->user()->name }}</span></p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-2 glass-card px-4 py-2 rounded-xl text-xs text-white/60">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        System Active
                    </div>
                    <div id="currentTime" class="text-sm font-medium text-white/80 tabular-nums"></div>
                </div>
            </header>

            <!-- PAGE BODY -->
            <div class="animate-fade-in">
                @yield('body')
            </div>
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden" onclick="toggleSidebar()"></div>

    <script>
        // Real-time Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            const clockEl = document.getElementById('currentTime');
            if(clockEl) clockEl.innerText = `${dateStr} • ${timeStr}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Sidebar Control
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.getElementById('mainContent');
        const overlay = document.getElementById('mobileOverlay');
        let isCollapsed = false;

        function toggleSidebar() {
            const isMobile = window.innerWidth < 1024;
            
            if (isMobile) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('hidden');
            } else {
                isCollapsed = !isCollapsed;
                if (isCollapsed) {
                    sidebar.style.width = '100px';
                    mainContent.style.marginLeft = '132px';
                    document.querySelectorAll('.sidebar-label').forEach(el => el.style.opacity = '0');
                    setTimeout(() => {
                        document.querySelectorAll('.sidebar-label').forEach(el => el.classList.add('hidden'));
                    }, 200);
                } else {
                    sidebar.style.width = '280px';
                    mainContent.style.marginLeft = '312px';
                    document.querySelectorAll('.sidebar-label').forEach(el => el.classList.remove('hidden'));
                    setTimeout(() => {
                        document.querySelectorAll('.sidebar-label').forEach(el => el.style.opacity = '1');
                    }, 50);
                }
            }
        }

        // Dropdown Control
        function toggleDropdown(menuId, btn) {
            const menu = document.getElementById(menuId);
            const chevron = document.getElementById('chevron-' + menuId);
            const isHidden = menu.classList.contains('hidden');

            // Close other dropdowns
            document.querySelectorAll('[id$="Menu"]').forEach(otherMenu => {
                if (otherMenu.id !== menuId && !otherMenu.classList.contains('hidden')) {
                    otherMenu.classList.add('hidden');
                    const otherChevron = document.getElementById('chevron-' + otherMenu.id);
                    if (otherChevron) otherChevron.classList.remove('rotate-180');
                }
            });

            if (isHidden) {
                menu.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                menu.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        // Auto-close mobile sidebar on resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.add('hidden');
                sidebar.style.left = '0';
            } else {
                sidebar.style.left = '';
            }
        });
    </script>
</body>

</html>