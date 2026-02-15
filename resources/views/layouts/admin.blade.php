<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen text-white">
    <!-- Background -->
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-500 via-indigo-500 to-fuchsia-500"></div>
        <div class="absolute -top-28 -left-28 h-[480px] w-[480px] rounded-full bg-cyan-300/35 blur-[140px]"></div>
        <div class="absolute -bottom-40 -right-32 h-[580px] w-[580px] rounded-full bg-pink-300/35 blur-[160px]"></div>
        <div class="absolute top-24 right-24 h-[360px] w-[360px] rounded-full bg-violet-300/30 blur-[130px]"></div>
        <div class="absolute inset-0 bg-black/15"></div>

        <div class="relative min-h-screen">
            <div class="flex min-h-screen w-full">

                {{-- =========================
                DESKTOP SIDEBAR (lg+)
                ========================= --}}
                <aside id="adminSidebarDesktop" class="group relative m-4 hidden h-[calc(100vh-2rem)] shrink-0 rounded-[26px] border border-white/20 bg-white/10 backdrop-blur-2xl lg:block
                 transition-all duration-200 ease-out w-16">
                    <div class="flex h-full flex-col p-3">
                        <div class="flex items-center justify-center">
                            <button id="sidebarPinBtn" type="button"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-sm hover:bg-white/15"
                                title="Pin sidebar">
                                ⚙️
                            </button>
                        </div>


                        <div class="mt-4 space-y-2">
                            @php
                                $isDashboard = request()->routeIs('admin.dashboard');
                            @endphp

                            <a href="{{ route('admin.dashboard') }}" class="relative flex items-center gap-3 rounded-xl px-3 py-2 transition
   {{ $isDashboard
    ? 'bg-white/20 text-white shadow-lg'
    : 'border border-white/10 bg-white/5 hover:bg-white/10 text-white/90'
   }}">

                                @if($isDashboard)
                                    <span
                                        class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-white"></span>
                                @endif

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/90" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 10.5L12 3l9 7.5v9a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 19.5v-9z" />
                                </svg>
                                <span class="sidebar-label hidden text-sm font-medium">Dashboard</span>
                            </a>

                            <a href="#"
                                class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2 hover:bg-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/90" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5v9L12 21l9-4.5v-9" />
                                </svg>

                                <span class="sidebar-label hidden text-sm font-medium text-white/90">Produk</span>
                            </a>
                            <a href="#"
                                class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2 hover:bg-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/90" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 3h9a1.5 1.5 0 011.5 1.5v15a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 016 19.5v-15A1.5 1.5 0 017.5 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7.5h6M9 11h6M9 14.5h3" />
                                </svg>

                                <span class="sidebar-label hidden text-sm font-medium text-white/90">Transaksi</span>
                            </a>

                            @php
                                $isUserActive = request()->routeIs('admin.cashiers.*');
                            @endphp

                            <a href="{{ route('admin.cashiers.index') }}" class="relative flex items-center gap-3 rounded-xl px-3 py-2 transition-all duration-200
   {{ $isUserActive
    ? 'bg-white/20 text-white shadow-lg'
    : 'border border-white/10 bg-white/5 hover:bg-white/10 text-white/90'
   }}">

                                {{-- Active left indicator --}}
                                @if($isUserActive)
                                    <span
                                        class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-white"></span>
                                @endif

                                <!-- SVG ICON DI SINI -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/90" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.5 20.25a8.25 8.25 0 0115 0" />
                                </svg>

                                <span class="sidebar-label hidden text-sm font-medium">User</span>
                            </a>



                            <a href="#"
                                class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2 hover:bg-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/90" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 14.25l3-3 3 3 4.5-4.5" />
                                </svg>

                                <span class="sidebar-label hidden text-sm font-medium text-white/90">Laporan</span>
                            </a>
                        </div>

                        <div class="mt-auto space-y-3">
                            <div class="rounded-2xl border border-white/15 bg-white/10 p-3">
                                <div class="text-[11px] text-white/70 sidebar-label hidden">Role</div>
                                <div class="mt-1 text-sm font-semibold sidebar-label hidden">{{ auth()->user()->role }}
                                </div>
                                <div class="mt-2 text-[11px] text-white/70 sidebar-label hidden">
                                    Dummy menu dulu.
                                </div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    class="flex w-full items-center justify-center gap-3 rounded-xl bg-blue-600/85 px-3 py-2 text-sm font-semibold hover:bg-blue-500/85">
                                    <span>⎋</span>
                                    <span class="sidebar-label hidden">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>

                {{-- =========================
                MOBILE/TABLET SIDEBAR (drawer)
                tampil di <lg=========================--}} <div id="sidebarOverlay"
                    class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden">
            </div>

            <aside id="adminSidebarMobile" @php
                $isDashboard = request()->routeIs('admin.dashboard');
                $isUsers = request()->routeIs('admin.cashiers.*');
                // nanti kalau ada produk/transaksi/laporan, tinggal tambah variabelnya
            @endphp
                class="fixed left-0 top-0 z-50 hidden h-full w-[280px] border-r border-white/20 bg-white/10 p-4 backdrop-blur-2xl lg:hidden">
                <div class="flex items-center justify-between">
                    <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2">
                        <div class="text-xs text-white/70">Admin Panel</div>
                        <div class="text-sm font-semibold">POS Dashboard</div>
                    </div>

                    <button id="closeMobileSidebar"
                        class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 hover:bg-white/15">
                        ✕
                    </button>
                </div>

                <div class="mt-5 space-y-2 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="relative flex items-center gap-3 rounded-xl px-3 py-2 transition-all duration-200
   {{ $isDashboard ? 'bg-white/20 shadow-lg' : 'border border-white/10 bg-white/5 hover:bg-white/10' }}">
                        @if($isDashboard)
                            <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-white"></span>
                        @endif

                        <span class="text-sm font-medium">Dashboard</span>
                    </a>

                    <a href="#"
                        class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2 hover:bg-white/10">📦
                        Produk</a>
                    <a href="#"
                        class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2 hover:bg-white/10">🧾
                        Transaksi</a>
                    <a href="{{ route('admin.cashiers.index') }}" class="relative flex items-center gap-3 rounded-xl px-3 py-2 transition-all duration-200
   {{ $isUsers ? 'bg-white/20 shadow-lg' : 'border border-white/10 bg-white/5 hover:bg-white/10' }}">
                        @if($isUsers)
                            <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-white"></span>
                        @endif

                        <span class="text-sm font-medium">User</span>
                    </a>


                    <a href="#"
                        class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2 hover:bg-white/10">📈
                        Laporan</a>
                </div>

                <div class="mt-6 rounded-2xl border border-white/15 bg-white/10 p-4">
                    <div class="text-xs text-white/70">Login as</div>
                    <div class="mt-1 text-sm font-semibold">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="text-xs text-white/70">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-auto pt-5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="w-full rounded-xl bg-blue-600/85 px-4 py-3 text-sm font-semibold hover:bg-blue-500/85">
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <!-- MAIN -->
            <main id="adminMain" class="flex-1 p-4 lg:p-6">
                @yield('body')
            </main>

        </div>
    </div>
    </div>

    <script>
        (function () {
            // ===== DESKTOP COLLAPSE / HOVER =====
            const sidebarD = document.getElementById('adminSidebarDesktop');
            const main = document.getElementById('adminMain');
            const pinBtn = document.getElementById('sidebarPinBtn');

            if (sidebarD && main && pinBtn) {
                let pinned = false;

                function openSidebar() {
                    sidebarD.classList.remove('w-16');
                    sidebarD.classList.add('w-64');
                    sidebarD.querySelectorAll('.sidebar-label').forEach(el => el.classList.remove('hidden'));
                }

                function closeSidebar() {
                    sidebarD.classList.add('w-16');
                    sidebarD.classList.remove('w-64');
                    sidebarD.querySelectorAll('.sidebar-label').forEach(el => el.classList.add('hidden'));
                }

                closeSidebar();

                sidebarD.addEventListener('mouseenter', () => { if (!pinned) openSidebar(); });
                sidebarD.addEventListener('mouseleave', () => { if (!pinned) closeSidebar(); });
                main.addEventListener('mouseenter', () => { if (!pinned) closeSidebar(); });

                pinBtn.addEventListener('click', () => {
                    pinned = !pinned;
                    if (pinned) {
                        openSidebar();
                        pinBtn.textContent = '📌';
                        pinBtn.title = 'Unpin sidebar';
                    } else {
                        closeSidebar();
                        pinBtn.textContent = '⚙️';
                        pinBtn.title = 'Pin sidebar';
                    }
                });
            }

            // ===== MOBILE DRAWER =====
            const openBtn = document.getElementById('openMobileSidebar');
            const closeBtn = document.getElementById('closeMobileSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const sidebarM = document.getElementById('adminSidebarMobile');

            function openMobile() {
                if (!overlay || !sidebarM) return;
                overlay.classList.remove('hidden');
                sidebarM.classList.remove('hidden');
            }

            function closeMobile() {
                if (!overlay || !sidebarM) return;
                overlay.classList.add('hidden');
                sidebarM.classList.add('hidden');
            }

            if (sidebarM) {
                sidebarM.querySelectorAll('a').forEach(a => {
                    a.addEventListener('click', closeMobile);
                });
            }

            // tombol open ada di dashboard view (topbar)
            if (openBtn) openBtn.addEventListener('click', openMobile);
            if (closeBtn) closeBtn.addEventListener('click', closeMobile);
            if (overlay) overlay.addEventListener('click', closeMobile);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeMobile();
            });
        })();
    </script>
</body>

</html>