<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Kasir')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-900 text-white">
  <div class="p-6">
    <div class="mb-6 text-sm text-white/60">Layout Kasir</div>
    @yield('content')
  </div>
</body>
</html>
