<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Kitchen')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])

  <style>
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }
  </style>
</head>

<body class="min-h-screen text-slate-900">
  <!-- Background -->
  <div class="fixed inset-0 -z-10 bg-gradient-to-br from-white via-slate-100 to-sky-100"></div>
  <div class="fixed inset-0 -z-10 opacity-60 backdrop-blur-3xl"></div>

  <!-- MOBILE DRAWER OVERLAY -->
  <div id="kitchenOverlay" class="fixed inset-0 z-40 hidden bg-black/40 backdrop-blur-sm lg:hidden"></div>

  <!-- MOBILE DRAWER -->
  <aside id="kitchenDrawer"
    class="fixed left-0 top-0 z-50 hidden h-full w-[86%] max-w-[320px] -translate-x-full border-r border-slate-200/70 bg-white/80 p-4 shadow-xl backdrop-blur-2xl transition-transform lg:hidden">
    @php
      $isDashboard = request()->routeIs('kitchen.dashboard');
      $isHistory = request()->routeIs('kitchen.history');
    @endphp

    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200/70 bg-white/70">
          🍳
        </div>
        <div>
          <div class="text-sm font-semibold">Kitchen</div>
          <div class="text-xs text-slate-500">Display & History</div>
        </div>
      </div>

      <button id="kitchenCloseNav" type="button"
        class="rounded-xl border border-slate-200/70 bg-white/70 px-3 py-2 text-sm hover:bg-white">
        ✕
      </button>
    </div>

    <nav class="mt-5 space-y-2 text-sm">
      <a href="{{ route('kitchen.dashboard') }}"
        class="flex items-center gap-2 rounded-xl px-3 py-2 border
          {{ $isDashboard ? 'border-slate-900/10 bg-slate-900/5' : 'border-transparent hover:border-slate-900/10 hover:bg-slate-900/5' }}">
        <span class="h-2 w-2 rounded-full {{ $isDashboard ? 'bg-sky-500' : 'bg-slate-300' }}"></span>
        <span class="font-medium">Kitchen Display</span>
      </a>

      <a href="{{ route('kitchen.history') }}"
        class="flex items-center gap-2 rounded-xl px-3 py-2 border
          {{ $isHistory ? 'border-slate-900/10 bg-slate-900/5' : 'border-transparent hover:border-slate-900/10 hover:bg-slate-900/5' }}">
        <span class="h-2 w-2 rounded-full {{ $isHistory ? 'bg-sky-500' : 'bg-slate-300' }}"></span>
        <span class="font-medium">Riwayat Masak</span>
      </a>

      <form method="POST" action="{{ route('logout') }}" class="pt-2">
        @csrf
        <button type="submit"
          class="w-full rounded-xl border border-slate-200/70 bg-white/70 px-3 py-2 text-left hover:bg-white">
          Logout
        </button>
      </form>
    </nav>

    <div class="mt-5 rounded-2xl border border-slate-200/70 bg-white/70 px-3 py-3 text-xs text-slate-600">
      Login: <span class="font-medium text-slate-900">{{ auth()->user()->name ?? '-' }}</span>
    </div>
  </aside>

  <div class="mx-auto max-w-[1500px] p-4 sm:p-6">
    <div class="flex gap-5">
      <!-- DESKTOP SIDEBAR -->
      <aside class="hidden w-[300px] shrink-0 lg:block">
        <div class="sticky top-6 rounded-[26px] border border-slate-200/70 bg-white/60 p-4 shadow-sm backdrop-blur-2xl">
          @php
            $isDashboard = request()->routeIs('kitchen.dashboard');
            $isHistory = request()->routeIs('kitchen.history');
          @endphp

          <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200/70 bg-white/70">
              🍳
            </div>
            <div>
              <div class="text-sm font-semibold">Kitchen</div>
              <div class="text-xs text-slate-500">Display & History</div>
            </div>
          </div>

          <nav class="mt-5 space-y-2 text-sm">
            <a href="{{ route('kitchen.dashboard') }}"
              class="flex items-center gap-2 rounded-xl px-3 py-2 border
                {{ $isDashboard ? 'border-slate-900/10 bg-slate-900/5' : 'border-transparent hover:border-slate-900/10 hover:bg-slate-900/5' }}">
              <span class="h-2 w-2 rounded-full {{ $isDashboard ? 'bg-sky-500' : 'bg-slate-300' }}"></span>
              <span class="font-medium">Kitchen Display</span>
            </a>

            <a href="{{ route('kitchen.history') }}"
              class="flex items-center gap-2 rounded-xl px-3 py-2 border
                {{ $isHistory ? 'border-slate-900/10 bg-slate-900/5' : 'border-transparent hover:border-slate-900/10 hover:bg-slate-900/5' }}">
              <span class="h-2 w-2 rounded-full {{ $isHistory ? 'bg-sky-500' : 'bg-slate-300' }}"></span>
              <span class="font-medium">Riwayat Masak</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="pt-2">
              @csrf
              <button type="submit"
                class="w-full rounded-xl border border-slate-200/70 bg-white/70 px-3 py-2 text-left hover:bg-white">
                Logout
              </button>
            </form>
          </nav>

          <div class="mt-5 rounded-2xl border border-slate-200/70 bg-white/70 px-3 py-3 text-xs text-slate-600">
            Login: <span class="font-medium text-slate-900">{{ auth()->user()->name ?? '-' }}</span>
          </div>

          <div class="mt-4 rounded-2xl border border-slate-200/70 bg-white/70 px-3 py-3 text-xs text-slate-600">
            Tips: tekan <span class="font-semibold text-slate-900">Enable Sound</span> sekali agar notifikasi aktif.
          </div>
        </div>
      </aside>

      <!-- MAIN -->
      <main class="min-w-0 flex-1">
        <!-- MOBILE TOP BAR -->
        <div class="mb-4 flex items-center justify-between lg:hidden">
          <button id="kitchenOpenNav" type="button"
            class="inline-flex items-center justify-center rounded-xl border border-slate-200/70 bg-white/70 px-3 py-2 text-sm shadow-sm backdrop-blur-2xl hover:bg-white">
            ☰ Menu
          </button>

          <div class="text-right">
            <div class="text-sm font-semibold">@yield('title', 'Kitchen')</div>
            <div class="text-xs text-slate-500">Kitchen Display</div>
          </div>
        </div>

        @yield('body')
      </main>
    </div>
  </div>

  <script>
    (function () {
      const openBtn = document.getElementById('kitchenOpenNav');
      const closeBtn = document.getElementById('kitchenCloseNav');
      const drawer = document.getElementById('kitchenDrawer');
      const overlay = document.getElementById('kitchenOverlay');

      function openDrawer() {
        if (!drawer || !overlay) return;
        drawer.classList.remove('hidden');
        overlay.classList.remove('hidden');
        requestAnimationFrame(() => {
          drawer.classList.remove('-translate-x-full');
        });
      }

      function closeDrawer() {
        if (!drawer || !overlay) return;
        drawer.classList.add('-translate-x-full');
        setTimeout(() => {
          drawer.classList.add('hidden');
          overlay.classList.add('hidden');
        }, 180);
      }

      if (openBtn) openBtn.addEventListener('click', openDrawer);
      if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
      if (overlay) overlay.addEventListener('click', closeDrawer);

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDrawer();
      });
    })();
  </script>
</body>
</html>