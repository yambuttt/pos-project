<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Pegawai')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen text-white">
    <div class="relative min-h-screen overflow-hidden">
        {{-- Background: beda warna dari admin --}}
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 via-teal-500 to-amber-400"></div>
        <div class="absolute -top-28 -left-28 h-[520px] w-[520px] rounded-full bg-emerald-200/30 blur-[150px]"></div>
        <div class="absolute -bottom-40 -right-32 h-[620px] w-[620px] rounded-full bg-teal-200/25 blur-[170px]"></div>
        <div class="absolute top-20 right-24 h-[380px] w-[380px] rounded-full bg-amber-200/25 blur-[140px]"></div>
        <div class="absolute inset-0 bg-black/15"></div>

        <div class="relative min-h-screen">
            <div class="flex min-h-screen w-full">

                {{-- Sidebar (desktop) --}}
                <aside
                    class="hidden lg:block m-4 w-64 rounded-[26px] border border-white/20 bg-white/10 backdrop-blur-2xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-white/70">POS</div>
                            <div class="text-lg font-semibold">Pegawai</div>
                        </div>
                        <span class="rounded-xl border border-white/20 bg-white/10 px-3 py-1 text-xs text-white/80">
                            {{ strtoupper(auth()->user()->role ?? 'PEGAWAI') }}
                        </span>
                    </div>

                    @php $isDash = request()->routeIs('pegawai.dashboard'); @endphp
                    <nav class="mt-5 space-y-2 text-sm">
                        <a href="{{ route('pegawai.dashboard') }}"
                            class="relative block rounded-xl px-4 py-3 transition
              {{ $isDash ? 'bg-white/20 border border-white/15' : 'bg-white/5 border border-white/10 hover:bg-white/10' }}">
                            Dashboard
                            @if($isDash)<span
                            class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-white"></span>@endif
                        </a>

                        {{-- placeholder menu absensi --}}
                        <a href="{{ route('pegawai.attendance') }}"
                            class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm hover:bg-white/10">
                            Absensi
                        </a>

                      
                    </nav>

                    <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="text-xs text-white/70">Login sebagai</div>
                        <div class="mt-1 text-sm font-semibold">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-white/70">{{ auth()->user()->email }}</div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button
                            class="w-full rounded-xl bg-emerald-600/80 px-4 py-3 text-sm font-semibold shadow-lg shadow-emerald-900/20 hover:bg-emerald-500/80">
                            Logout
                        </button>
                    </form>
                </aside>

                {{-- Main --}}
                <main class="flex-1 p-4 lg:p-6">
                    {{-- top bar --}}
                    <div class="flex items-center justify-between gap-3">
                        <button id="openPegawaiSidebar" type="button"
                            class="inline-flex lg:hidden items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15">☰</button>

                        <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-2xl">
                            <div class="text-xs text-white/70">@yield('page_label', 'Dashboard Pegawai')</div>
                            <div class="text-sm font-semibold">@yield('page_title', 'Absensi & Aktivitas')</div>
                        </div>

                        <div
                            class="hidden sm:flex items-center gap-2 rounded-2xl border border-white/20 bg-white/10 px-4 py-2 text-sm backdrop-blur-2xl">
                            <span class="text-white/85">{{ auth()->user()->name ?? 'Pegawai' }}</span>
                            <span class="text-white/60">•</span>
                            <span class="text-white/70">{{ now()->format('d M Y') }}</span>
                        </div>
                    </div>

                    {{-- content --}}
                    <div class="mt-5">
                        @yield('content')
                    </div>
                </main>

            </div>
        </div>
    </div>
    {{-- ===== MOBILE DRAWER (pegawai) ===== --}}
    <div id="pegawaiSidebarOverlay" class="fixed inset-0 z-[60] hidden bg-black/40 backdrop-blur-[2px] lg:hidden"></div>

    <aside id="pegawaiSidebarMobile"
        class="fixed left-0 top-0 z-[61] hidden h-full w-[82%] max-w-[320px] border-r border-white/20 bg-white/10 p-4 backdrop-blur-2xl lg:hidden">

        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-white/70">POS</div>
                <div class="text-lg font-semibold">Pegawai</div>
            </div>

            <button id="closePegawaiSidebar" type="button"
                class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm hover:bg-white/15">
                ✕
            </button>
        </div>

        @php $isDash = request()->routeIs('pegawai.dashboard'); @endphp
        <nav class="mt-5 space-y-2 text-sm">
            <a href="{{ route('pegawai.dashboard') }}" class="block rounded-xl px-4 py-3 transition
      {{ $isDash ? 'bg-white/20 border border-white/15' : 'bg-white/5 border border-white/10 hover:bg-white/10' }}">
                Dashboard
            </a>

            {{-- kalau sudah kamu buat absensi, ganti jadi link --}}
            <a href="{{ route('pegawai.attendance') }}"
                class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 hover:bg-white/10">
                Absensi
            </a>


        </nav>

        <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-xs text-white/70">Login sebagai</div>
            <div class="mt-1 text-sm font-semibold">{{ auth()->user()->name }}</div>
            <div class="text-xs text-white/70">{{ auth()->user()->email }}</div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button
                class="w-full rounded-xl bg-emerald-600/80 px-4 py-3 text-sm font-semibold shadow-lg shadow-emerald-900/20 hover:bg-emerald-500/80">
                Logout
            </button>
        </form>
    </aside>

    <script>
        (function () {
            const openBtn = document.getElementById('openPegawaiSidebar');
            const closeBtn = document.getElementById('closePegawaiSidebar');
            const overlay = document.getElementById('pegawaiSidebarOverlay');
            const sidebar = document.getElementById('pegawaiSidebarMobile');

            function openMobile() {
                if (!overlay || !sidebar) return;
                overlay.classList.remove('hidden');
                sidebar.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeMobile() {
                if (!overlay || !sidebar) return;
                overlay.classList.add('hidden');
                sidebar.classList.add('hidden');
                document.body.style.overflow = '';
            }

            if (sidebar) {
                sidebar.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMobile));
            }

            if (openBtn) openBtn.addEventListener('click', openMobile);
            if (closeBtn) closeBtn.addEventListener('click', closeMobile);
            if (overlay) overlay.addEventListener('click', closeMobile);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeMobile();
            });
        })();
    </script>

    {{-- optional: nanti untuk drawer mobile bisa kamu copy dari admin/kasir --}}
</body>

</html>