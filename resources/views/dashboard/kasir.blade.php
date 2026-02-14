<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Kasir</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-900 text-white flex items-center justify-center">
  <div class="text-center space-y-4">
    <h1 class="text-3xl font-bold">Ini Dashboard Kasir</h1>
    <p class="text-white/70">Role: {{ auth()->user()->role }} | {{ auth()->user()->email }}</p>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="rounded-xl bg-white/10 border border-white/20 px-4 py-2 hover:bg-white/15">
        Logout
      </button>
    </form>
  </div>
</body>
</html>
