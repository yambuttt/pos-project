<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Kitchen')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen text-white">
  <div class="fixed inset-0 -z-10 bg-gradient-to-br from-sky-600 via-indigo-600 to-fuchsia-600"></div>
  <div class="fixed inset-0 -z-10 opacity-30 backdrop-blur-3xl"></div>

  <div class="mx-auto max-w-[1400px] p-4 sm:p-6">
    @yield('body')
  </div>
</body>
</html>