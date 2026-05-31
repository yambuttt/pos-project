<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Kasir') | POS Premium</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-[#050505] text-white font-sans selection:bg-accent-gold/30 overflow-x-hidden" x-data="{ mobileMenuOpen: false }">

  {{-- Premium Animated Background --}}
  <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-black via-zinc-950 to-[#0a0a0a]"></div>
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-accent-gold/5 blur-[120px] animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-accent-amber/5 blur-[150px] animate-pulse" style="animation-delay: 2s"></div>
  </div>

  {{-- Desktop Sidebar --}}
  <aside class="hidden lg:flex flex-col w-72 p-6 h-screen fixed left-0 top-0 z-[100]">
    <div class="glass-panel flex-1 rounded-[2.5rem] flex flex-col p-6 border-white/5 relative overflow-hidden group">
      {{-- Glow Effect --}}
      <div class="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
      
      {{-- Logo --}}
      <div class="flex items-center gap-4 mb-10 px-2 relative z-10">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-accent-gold to-accent-amber flex items-center justify-center shadow-lg shadow-accent-gold/20">
          <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <div>
          <div class="text-xs font-black uppercase tracking-[0.2em] text-white/40">Premium</div>
          <div class="text-lg font-bold tracking-tight text-white">KASIR POS</div>
        </div>
      </div>

      @php
        $kDash = request()->routeIs('kasir.dashboard');
        $kCreate = request()->routeIs('kasir.sales.create');
        $kIndex = request()->routeIs('kasir.sales.index');
        $kReady = request()->routeIs('kasir.ready.*');
        $kResv = request()->routeIs('kasir.reservations.*');
        
        $navItems = [
          ['route' => 'kasir.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'active' => $kDash],
          ['route' => 'kasir.reservations.index', 'label' => 'Reservasi Meja', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'active' => $kResv],
          ['route' => 'kasir.sales.create', 'label' => 'Transaksi Baru', 'icon' => 'M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z', 'active' => $kCreate],
          ['route' => 'kasir.ready.index', 'label' => 'Pesanan Siap', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'active' => $kReady],
          ['route' => 'kasir.sales.index', 'label' => 'Riwayat Transaksi', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'active' => $kIndex],
        ];
      @endphp

      <nav class="flex-1 space-y-2 relative z-10">
        @foreach($navItems as $item)
          <a href="{{ route($item['route']) }}" 
             id="nav-desktop-{{ Str::slug($item['label']) }}"
             class="group flex items-center justify-between px-4 py-3.5 rounded-2xl transition-all duration-300 {{ $item['active'] ? 'bg-accent-gold text-black shadow-lg shadow-accent-gold/20' : 'text-white/40 hover:bg-white/5 hover:text-white' }}">
            <div class="flex items-center gap-4 min-w-0">
              <div class="shrink-0 transition-transform duration-300 group-hover:scale-110">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
              </div>
              <span class="font-bold text-sm tracking-tight truncate">{{ $item['label'] }}</span>
            </div>
            @if($item['label'] === 'Pesanan Siap')
              <span id="dot-desktop-pesanan-siap" class="hidden w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_#ef4444] animate-pulse shrink-0"></span>
            @endif
          </a>
        @endforeach
      </nav>

      {{-- User Profile --}}
      <div class="mt-auto pt-6 border-t border-white/5 relative z-10">
        <div class="flex items-center gap-4 mb-6 px-2">
          <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 p-0.5">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=eab308&color=000" class="w-full h-full rounded-[10px] object-cover" />
          </div>
          <div class="min-w-0">
            <div class="text-sm font-bold truncate">{{ auth()->user()->name }}</div>
            <div class="text-[10px] text-white/40 font-black uppercase tracking-wider">{{ auth()->user()->role ?? 'Kasir' }}</div>
          </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="w-full btn-premium bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 text-xs py-2.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Logout
          </button>
        </form>
      </div>
    </div>
  </aside>

  {{-- Main Content Container --}}
  <div class="lg:ml-72 relative z-10 flex flex-col min-h-screen">
    {{-- Mobile Header --}}
    <header class="lg:hidden flex items-center justify-between p-4 bg-black/50 backdrop-blur-xl border-b border-white/5 sticky top-0 z-[60]">
      <button @click="mobileMenuOpen = true" class="relative w-10 h-10 rounded-xl glass-panel flex items-center justify-center">
        <svg class="w-6 h-6 text-accent-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        <span id="dot-hamburger" class="hidden absolute top-1 right-1 w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_8px_#ef4444] animate-pulse"></span>
      </button>
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-accent-gold flex items-center justify-center">
          <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <span class="font-bold tracking-tight">KASIR</span>
      </div>
      <div class="w-10"></div>
    </header>

    {{-- Main Body --}}
    <main class="flex-1 overflow-x-hidden">
      <div class="p-4 lg:p-8">
        @yield('body')
      </div>
    </main>

    {{-- Footer --}}
    <footer class="p-6 border-t border-white/5 text-center">
      <p class="text-[10px] font-black uppercase tracking-[0.3em] text-white/20">
        &copy; {{ date('Y') }} TOKO POS PREMIUM &bull; ELEGANT TERMINAL
      </p>
    </footer>
  </div>

  {{-- Mobile Sidebar Drawer --}}
  <div x-show="mobileMenuOpen" 
       x-cloak
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 z-[110] lg:hidden">
    <div class="absolute inset-0 bg-black/90 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
    <aside x-show="mobileMenuOpen"
           x-transition:enter="transition ease-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="absolute left-0 top-0 bottom-0 w-80 bg-black border-r border-white/10 p-6 flex flex-col shadow-2xl">
      
      <div class="flex items-center justify-between mb-10 px-2">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-xl bg-accent-gold flex items-center justify-center shadow-lg shadow-accent-gold/20">
            <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          </div>
          <span class="font-bold tracking-tight text-white">POS Premium</span>
        </div>
        <button @click="mobileMenuOpen = false" class="text-white/40 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <nav class="flex-1 space-y-2">
        @foreach($navItems as $item)
          <a href="{{ route($item['route']) }}" 
             id="nav-mobile-{{ Str::slug($item['label']) }}"
             class="flex items-center justify-between px-4 py-4 rounded-2xl transition-all duration-300 {{ $item['active'] ? 'bg-accent-gold text-black shadow-lg shadow-accent-gold/20' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
            <div class="flex items-center gap-4 min-w-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
              <span class="font-bold text-sm tracking-tight truncate">{{ $item['label'] }}</span>
            </div>
            @if($item['label'] === 'Pesanan Siap')
              <span id="dot-mobile-pesanan-siap" class="hidden w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_8px_#ef4444] animate-pulse shrink-0"></span>
            @endif
          </a>
        @endforeach
      </nav>

      <div class="mt-auto pt-6 border-t border-white/5">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="w-full bg-red-500/10 text-red-400 py-4 rounded-2xl font-bold flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Logout
          </button>
        </form>
      </div>
    </aside>
  </div>

  <script>
    (function () {
      let lastReadyIds = new Set();
      let initialLoaded = false;

      async function pollReady() {
        try {
          const res = await fetch("{{ route('kasir.ready.orders') }}", { headers: { 'Accept': 'application/json' } });
          const data = await res.json();
          const readySales = data.ready_sales || [];
          const newIds = new Set(readySales.map(s => s.id));

          if (initialLoaded) {
            let hasNew = false;
            for (const id of newIds) {
              if (!lastReadyIds.has(id)) { hasNew = true; break; }
            }
            if (hasNew) {
              showToast(`🔔 Pesanan READY: ${readySales.length} siap diambil`);
            }
          }

          lastReadyIds = newIds;
          initialLoaded = true;

          // Notification dot logic
          const isReadyPage = {{ request()->routeIs('kasir.ready.*') ? 'true' : 'false' }};
          
          if (isReadyPage) {
            updateNotificationDots(false); // Hide dots when actively viewing the ready page
          } else {
            // Show dots if there are any active ready orders in the queue
            updateNotificationDots(readySales.length > 0);
          }

        } catch (e) { }
      }

      function updateNotificationDots(show) {
        const dotDesktop = document.getElementById('dot-desktop-pesanan-siap');
        const dotMobile = document.getElementById('dot-mobile-pesanan-siap');
        const dotHamburger = document.getElementById('dot-hamburger');

        if (show) {
          if (dotDesktop) dotDesktop.classList.remove('hidden');
          if (dotMobile) dotMobile.classList.remove('hidden');
          if (dotHamburger) dotHamburger.classList.remove('hidden');
        } else {
          if (dotDesktop) dotDesktop.classList.add('hidden');
          if (dotMobile) dotMobile.classList.add('hidden');
          if (dotHamburger) dotHamburger.classList.add('hidden');
        }
      }

      function showToast(text) {
        const el = document.createElement('div');
        el.className = "fixed right-4 top-4 z-[9999] glass-panel rounded-2xl px-6 py-4 text-sm text-white animate-fade-up border-accent-gold/30 shadow-[0_0_30px_rgba(234,179,8,0.2)]";
        el.innerHTML = `<div class="flex items-center gap-3"><span class="w-2 h-2 rounded-full bg-accent-gold animate-ping"></span>${text}</div>`;
        document.body.appendChild(el);
        setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .5s'; }, 3500);
        setTimeout(() => el.remove(), 4000);
      }

      pollReady();
      setInterval(pollReady, 5000);
    })();
  </script>
  @stack('scripts')
</body>

</html>