<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Kitchen')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen text-slate-900">
  <!-- Light liquid background -->
  <div class="fixed inset-0 -z-10 bg-gradient-to-br from-white via-slate-100 to-sky-100"></div>
  <div class="fixed inset-0 -z-10 opacity-70 backdrop-blur-3xl"></div>

  <div class="mx-auto max-w-[1500px] p-4 sm:p-6">
    <div class="flex gap-4">
      <!-- Sidebar -->
      <aside class="hidden lg:block w-[280px] shrink-0">
        <div class="sticky top-6 rounded-[26px] border border-slate-200/70 bg-white/55 p-4 shadow-sm backdrop-blur-2xl">
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl border border-slate-200/70 bg-white/60 backdrop-blur-2xl"></div>
            <div>
              <div class="text-sm font-semibold">Kitchen</div>
              <div class="text-xs text-slate-500">Display & History</div>
            </div>
          </div>

          <nav class="mt-4 space-y-2 text-sm">
            @php
              $isDashboard = request()->routeIs('kitchen.dashboard');
              $isHistory = request()->routeIs('kitchen.history');
            @endphp

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
                      class="w-full rounded-xl border border-slate-200/70 bg-white/60 px-3 py-2 text-left hover:bg-white/80 backdrop-blur-2xl">
                Logout
              </button>
            </form>
          </nav>

          <div class="mt-4 rounded-2xl border border-slate-200/70 bg-white/60 px-3 py-3 text-xs text-slate-600 backdrop-blur-2xl">
            Login: <span class="font-medium text-slate-900">{{ auth()->user()->name ?? '-' }}</span>
          </div>
        </div>
      </aside>

      <!-- Main content -->
      <main class="min-w-0 flex-1">
        @yield('body')
      </main>
    </div>
  </div>
</body>
</html>