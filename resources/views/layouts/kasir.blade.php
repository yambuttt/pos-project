<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title','Kasir')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen text-white">
  {{-- Dark background --}}
  <div class="relative min-h-screen overflow-hidden bg-gray-950">
    <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-slate-950 to-zinc-950"></div>
    <div class="absolute -top-24 -left-24 h-[420px] w-[420px] rounded-full bg-indigo-500/20 blur-[120px]"></div>
    <div class="absolute -bottom-32 -right-32 h-[520px] w-[520px] rounded-full bg-cyan-500/15 blur-[140px]"></div>
    <div class="absolute top-24 right-24 h-[360px] w-[360px] rounded-full bg-fuchsia-500/10 blur-[130px]"></div>
    <div class="absolute inset-0 bg-black/35"></div>

    <div class="relative min-h-screen">
      <div class="flex min-h-screen">

        {{-- Desktop sidebar --}}
        <aside class="hidden lg:block m-4 w-64 rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-4">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-xs text-white/60">POS</div>
              <div class="text-lg font-semibold">Kasir</div>
            </div>
            <span class="rounded-xl border border-white/10 bg-white/5 px-3 py-1 text-xs text-white/70">
              {{ strtoupper(auth()->user()->role ?? 'KASIR') }}
            </span>
          </div>

          @php
            $kDash = request()->routeIs('kasir.dashboard');
            $kCreate = request()->routeIs('kasir.sales.create');
            $kIndex = request()->routeIs('kasir.sales.index');
          @endphp

          <nav class="mt-5 space-y-2 text-sm">
            <a href="{{ route('kasir.dashboard') }}"
              class="relative block rounded-xl px-4 py-3 transition
              {{ $kDash ? 'bg-white/15 border border-white/15' : 'bg-white/5 border border-white/10 hover:bg-white/10' }}">
              Dashboard
              @if($kDash)<span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-white"></span>@endif
            </a>

            <a href="{{ route('kasir.sales.create') }}"
              class="relative block rounded-xl px-4 py-3 transition
              {{ $kCreate ? 'bg-white/15 border border-white/15' : 'bg-white/5 border border-white/10 hover:bg-white/10' }}">
              Transaksi Baru
              @if($kCreate)<span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-white"></span>@endif
            </a>

            <a href="{{ route('kasir.sales.index') }}"
              class="relative block rounded-xl px-4 py-3 transition
              {{ $kIndex ? 'bg-white/15 border border-white/15' : 'bg-white/5 border border-white/10 hover:bg-white/10' }}">
              Riwayat Transaksi
              @if($kIndex)<span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-white"></span>@endif
            </a>
          </nav>

          <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-xs text-white/60">Login sebagai</div>
            <div class="mt-1 text-sm font-semibold">{{ auth()->user()->name }}</div>
            <div class="text-xs text-white/60">{{ auth()->user()->email }}</div>
          </div>

          <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button class="w-full rounded-xl bg-blue-600/85 px-4 py-3 text-sm font-semibold hover:bg-blue-500/85">
              Logout
            </button>
          </form>
        </aside>

        {{-- Mobile drawer --}}
        <div id="kasirOverlay" class="fixed inset-0 z-40 hidden bg-black/60 lg:hidden"></div>
        <aside id="kasirDrawer"
          class="fixed left-0 top-0 z-50 hidden h-full w-[280px] border-r border-white/10 bg-white/5 backdrop-blur-2xl p-4 lg:hidden">
          <div class="flex items-center justify-between">
            <div class="text-lg font-semibold">POS Kasir</div>
            <button id="kasirClose" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 hover:bg-white/10">✕</button>
          </div>

          <div class="mt-4 space-y-2">
            <a href="{{ route('kasir.dashboard') }}" class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 hover:bg-white/10">Dashboard</a>
            <a href="{{ route('kasir.sales.create') }}" class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 hover:bg-white/10">Transaksi Baru</a>
            <a href="{{ route('kasir.sales.index') }}" class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 hover:bg-white/10">Riwayat</a>
          </div>

          <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button class="w-full rounded-xl bg-blue-600/85 px-4 py-3 text-sm font-semibold hover:bg-blue-500/85">Logout</button>
          </form>
        </aside>

        {{-- Main --}}
        <main class="flex-1 p-4 lg:p-6">
          <div class="mb-4 flex items-center justify-between lg:hidden">
            <button id="kasirOpen"
              class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10">
              ☰ Menu
            </button>
            <div class="text-sm text-white/70">{{ auth()->user()->name }}</div>
          </div>

          @yield('body')
        </main>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const openBtn = document.getElementById('kasirOpen');
      const closeBtn = document.getElementById('kasirClose');
      const overlay = document.getElementById('kasirOverlay');
      const drawer = document.getElementById('kasirDrawer');

      function open() {
        if (!overlay || !drawer) return;
        overlay.classList.remove('hidden');
        drawer.classList.remove('hidden');
      }
      function close() {
        if (!overlay || !drawer) return;
        overlay.classList.add('hidden');
        drawer.classList.add('hidden');
      }

      if (openBtn) openBtn.addEventListener('click', open);
      if (closeBtn) closeBtn.addEventListener('click', close);
      if (overlay) overlay.addEventListener('click', close);
      if (drawer) drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', close));
      document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
    })();
  </script>
</body>
</html>
