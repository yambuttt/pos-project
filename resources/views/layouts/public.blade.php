<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Ayo Renne Cafe & Resto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background:
                radial-gradient(circle at top, rgba(234, 179, 8, .08), transparent 24%),
                linear-gradient(180deg, #020202 0%, #060606 100%);
        }
        .font-display {
            font-family: Georgia, "Times New Roman", serif;
        }
    </style>
</head>
<body class="bg-[#050505] text-white antialiased min-h-screen flex flex-col">
    <header class="sticky top-0 z-50 border-b border-yellow-500/10 bg-black/90 backdrop-blur-xl">
        <div class="max-w-6xl mx-auto px-4 py-3 sm:py-4 flex justify-between items-center gap-4">
            <a href="{{ route('public.home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne Logo" class="h-10 sm:h-12 w-auto object-contain">
            </a>
            <div class="flex gap-6 text-sm font-medium">
                <a href="{{ route('public.terms') }}" class="transition {{ request()->routeIs('public.terms') ? 'text-yellow-500' : 'text-white/80 hover:text-yellow-400' }}">Syarat & Ketentuan</a>
                <a href="{{ route('public.privacy') }}" class="transition {{ request()->routeIs('public.privacy') ? 'text-yellow-500' : 'text-white/80 hover:text-yellow-400' }}">Kebijakan Privasi</a>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-4xl mx-auto w-full px-4 py-16">
        @yield('content')
    </main>

    <footer class="border-t border-yellow-500/12 bg-[#050505] py-8 mt-auto">
        <div class="max-w-6xl mx-auto px-4 flex flex-col items-center justify-between gap-4 text-sm text-white/48 md:flex-row">
            <div>&copy; {{ date('Y') }} Ayo Renne. All rights reserved.</div>
            <div>Made with 💛 in Probolinggo</div>
        </div>
    </footer>
</body>
</html>
