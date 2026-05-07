<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - POS Restoran</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <a href="{{ route('public.home') }}" class="text-xl font-bold text-blue-600 tracking-tight">POS Restoran</a>
            <div class="flex gap-6 text-sm font-medium">
                <a href="{{ route('public.terms') }}" class="hover:text-blue-600 transition {{ request()->routeIs('public.terms') ? 'text-blue-600' : 'text-gray-600' }}">Syarat & Ketentuan</a>
                <a href="{{ route('public.privacy') }}" class="hover:text-blue-600 transition {{ request()->routeIs('public.privacy') ? 'text-blue-600' : 'text-gray-600' }}">Kebijakan Privasi</a>
            </div>
        </div>
    </nav>
    <main class="flex-1 max-w-4xl mx-auto w-full px-4 py-10">
        @yield('content')
    </main>
    <footer class="bg-gray-900 text-white py-8 text-center text-sm">
        <p>&copy; {{ date('Y') }} POS Restoran. All rights reserved.</p>
    </footer>
</body>
</html>
