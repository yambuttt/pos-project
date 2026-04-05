<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Pegawai')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    html { scroll-behavior: smooth; }
    * { box-sizing: border-box; }
    body {
      overflow-x: hidden;
      background:
        radial-gradient(circle at top left, rgba(234, 179, 8, .08), transparent 26%),
        radial-gradient(circle at bottom right, rgba(234, 179, 8, .06), transparent 24%),
        linear-gradient(180deg, #030303 0%, #090909 100%);
    }
    .shell {
      background: linear-gradient(180deg, rgba(255, 255, 255, .01), rgba(255, 255, 255, .00));
    }
    .panel-dark {
      background: rgba(16, 16, 16, 0.86);
      backdrop-filter: blur(12px);
    }
    .panel-soft {
      background: rgba(255, 255, 255, .03);
      backdrop-filter: blur(10px);
    }
    .gold-border { border-color: rgba(234, 179, 8, 0.16); }
    .gold-border-strong { border-color: rgba(234, 179, 8, 0.30); }
    .gold-text { color: rgb(234 179 8); }
  </style>
</head>

<body class="min-h-screen text-white">
  <div class="relative min-h-screen overflow-hidden">
    <!-- ambient -->
    <div class="pointer-events-none absolute -left-24 top-0 h-[380px] w-[380px] rounded-full bg-yellow-500/10 blur-[120px]"></div>
    <div class="pointer-events-none absolute bottom-0 right-0 h-[440px] w-[440px] rounded-full bg-yellow-400/8 blur-[140px]"></div>

    <div class="relative min-h-screen shell">
      <div class="flex min-h-screen w-full">

        @php
          $isDash = request()->routeIs('pegawai.dashboard');
          $isAbsensi = request()->routeIs('pegawai.attendance');
          $isHistory = request()->routeIs('pegawai.attendance.history*');
        @endphp

        <!-- DESKTOP SIDEBAR -->
        <aside class="panel-dark gold-border relative m-4 hidden h-[calc(100vh-2rem)] w-64 shrink-0 overflow-y-auto rounded-[28px] border shadow-[0_20px_60px_rgba(0,0,0,.35)] lg:block">
          <div class="flex h-full flex-col p-4">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-xs uppercase tracking-[0.22em] text-yellow-500">Pegawai Panel</div>
                <div class="text-sm font-semibold text-white">Ayo Renne</div>
              </div>
              <span class="rounded-xl border gold-border bg-white/[0.03] px-3 py-1 text-xs text-white/80">
                {{ strtoupper(auth()->user()->role ?? 'PEGAWAI') }}
              </span>
            </div>

            <nav class="mt-6 space-y-2 text-sm">
              <a href="{{ route('pegawai.dashboard') }}"
                 class="relative flex items-center gap-3 rounded-xl px-3 py-3 transition
                 {{ $isDash ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                @if($isDash)
                  <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                @endif
                <span class="font-medium text-white/90">Dashboard</span>
              </a>

              <a href="{{ route('pegawai.attendance') }}"
                 class="relative flex items-center gap-3 rounded-xl px-3 py-3 transition
                 {{ $isAbsensi ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                @if($isAbsensi)
                  <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                @endif
                <span class="font-medium text-white/90">Absensi</span>
              </a>

              <a href="{{ route('pegawai.attendance.history') }}"
                 class="relative flex items-center gap-3 rounded-xl px-3 py-3 transition
                 {{ $isHistory ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                @if($isHistory)
                  <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                @endif
                <span class="font-medium text-white/90">Riwayat Absensi</span>
              </a>
            </nav>

            <div class="mt-6 rounded-2xl border gold-border bg-white/[0.03] p-4">
              <div class="text-xs text-white/55">Login sebagai</div>
              <div class="mt-1 text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
              <div class="text-xs text-white/55">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-auto pt-5">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full rounded-xl bg-yellow-500 px-4 py-3 text-sm font-semibold text-black hover:bg-yellow-400">
                  Logout
                </button>
              </form>
            </div>
          </div>
        </aside>

        <!-- MAIN -->
        <main class="flex-1 p-4 lg:p-6">
          <div class="flex items-center justify-between gap-3">
            <button id="openPegawaiSidebar" type="button"
              class="inline-flex lg:hidden items-center justify-center rounded-xl border border-yellow-500/20 bg-white/[0.04] px-3 py-2 text-sm text-white backdrop-blur-xl hover:bg-white/[0.08]">
              ☰
            </button>

            <div class="rounded-2xl border border-yellow-500/16 bg-white/[0.03] px-4 py-3 backdrop-blur-xl">
              <div class="text-xs uppercase tracking-[0.18em] text-yellow-500">@yield('page_label','Pegawai')</div>
              <div class="text-sm font-semibold text-white">@yield('page_title','Dashboard')</div>
            </div>

            <div class="hidden sm:flex items-center gap-2 rounded-2xl border border-yellow-500/16 bg-white/[0.03] px-4 py-2 text-sm text-white/85 backdrop-blur-xl">
              {{ auth()->user()->name ?? 'Pegawai' }} • <span class="text-white/50">{{ now()->format('d M Y') }}</span>
            </div>
          </div>

          <div class="mt-5">
            @yield('content')
          </div>
        </main>
      </div>
    </div>
  </div>

  <!-- MOBILE DRAWER -->
  <div id="pegawaiSidebarOverlay" class="fixed inset-0 z-[60] hidden bg-black/60 lg:hidden"></div>
  <aside id="pegawaiSidebarMobile" class="panel-dark gold-border fixed left-0 top-0 z-[61] hidden h-full w-[82%] max-w-[320px] border-r p-4 lg:hidden">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-[0.22em] text-yellow-500">Pegawai Panel</div>
        <div class="text-sm font-semibold text-white">Ayo Renne</div>
      </div>
      <button id="closePegawaiSidebar" type="button" class="rounded-xl border gold-border bg-white/[0.04] px-3 py-2 hover:bg-white/[0.08]">✕</button>
    </div>

    <nav class="mt-5 space-y-2 text-sm">
      <a href="{{ route('pegawai.dashboard') }}" class="block rounded-xl px-3 py-3 {{ $isDash ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02]' }}">Dashboard</a>
      <a href="{{ route('pegawai.attendance') }}" class="block rounded-xl px-3 py-3 {{ $isAbsensi ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02]' }}">Absensi</a>
      <a href="{{ route('pegawai.attendance.history') }}" class="block rounded-xl px-3 py-3 {{ $isHistory ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02]' }}">Riwayat Absensi</a>
    </nav>

    <div class="mt-6 rounded-2xl border gold-border bg-white/[0.03] p-4">
      <div class="text-xs text-white/55">Login sebagai</div>
      <div class="mt-1 text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
      <div class="text-xs text-white/55">{{ auth()->user()->email }}</div>
    </div>

    <div class="mt-auto pt-5">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="w-full rounded-xl bg-yellow-500 px-4 py-3 text-sm font-semibold text-black hover:bg-yellow-400">Logout</button>
      </form>
    </div>
  </aside>

  <script>
    (function () {
      const openBtn = document.getElementById('openPegawaiSidebar');
      const closeBtn = document.getElementById('closePegawaiSidebar');
      const overlay = document.getElementById('pegawaiSidebarOverlay');
      const sidebar = document.getElementById('pegawaiSidebarMobile');

      function openMobile() {
        overlay.classList.remove('hidden');
        sidebar.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
      function closeMobile() {
        overlay.classList.add('hidden');
        sidebar.classList.add('hidden');
        document.body.style.overflow = '';
      }
      if (sidebar) sidebar.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMobile));
      if (openBtn) openBtn.addEventListener('click', openMobile);
      if (closeBtn) closeBtn.addEventListener('click', closeMobile);
      if (overlay) overlay.addEventListener('click', closeMobile);
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMobile(); });
    })();
  </script>
</body>
</html>