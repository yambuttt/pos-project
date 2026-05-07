<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko - Retail Point of Sale</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col font-sans">
    
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                    T
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-900">TokoKita</span>
            </div>
            
            <nav class="hidden md:flex gap-8 font-medium text-sm">
                <a href="#" class="text-blue-600">Beranda</a>
                <a href="#katalog" class="text-slate-600 hover:text-blue-600 transition">Katalog</a>
                <a href="#tentang" class="text-slate-600 hover:text-blue-600 transition">Tentang Kami</a>
            </nav>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition hidden sm:block">Login Kasir</a>
                <a href="#katalog" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition shadow-lg shadow-blue-600/20">
                    Mulai Belanja
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1">
        <section class="relative pt-20 pb-32 overflow-hidden">
            <div class="absolute inset-0 bg-blue-50/50 -z-10"></div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-10 right-10 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-10 left-10 w-72 h-72 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-100/50 text-blue-700 text-sm font-semibold mb-8 border border-blue-200">
                    <span class="flex h-2 w-2 rounded-full bg-blue-600"></span>
                    Sistem POS Retail Modern
                </div>
                
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 mb-8 leading-[1.1]">
                    Kelola Toko Anda dengan <br class="hidden md:block">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Lebih Cerdas & Mudah</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                    Sistem kasir pintar yang dirancang khusus untuk toko retail. Pantau stok, catat transaksi, dan analisis penjualan dalam satu platform terpadu.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#katalog" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transition shadow-xl shadow-blue-600/30">
                        Lihat Katalog Produk
                    </a>
                    <a href="{{ route('login') }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-8 py-4 rounded-xl font-semibold text-lg transition shadow-sm">
                        Masuk Dashboard
                    </a>
                </div>
            </div>
        </section>

        <!-- Feature Section -->
        <section id="katalog" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">Fitur Unggulan Toko</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">Kami menyediakan berbagai alat untuk memudahkan Anda mengelola operasional toko sehari-hari.</p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 hover:border-blue-100 hover:shadow-lg transition">
                        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Kasir (POS) Cepat</h3>
                        <p class="text-slate-600 leading-relaxed">Proses checkout yang sangat cepat dan responsif, mendukung barcode scanner dan berbagai metode pembayaran.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 hover:border-blue-100 hover:shadow-lg transition">
                        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Manajemen Stok</h3>
                        <p class="text-slate-600 leading-relaxed">Sistem inventaris real-time yang mencegah kehabisan barang dan memberikan notifikasi saat stok menipis.</p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 hover:border-blue-100 hover:shadow-lg transition">
                        <div class="w-14 h-14 bg-sky-100 rounded-2xl flex items-center justify-center text-sky-600 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Laporan Analitik</h3>
                        <p class="text-slate-600 leading-relaxed">Pantau performa penjualan harian, mingguan, hingga bulanan dengan grafik yang mudah dipahami.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                            T
                        </div>
                        <span class="font-bold text-xl tracking-tight text-white">TokoKita</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed">Sistem kasir cerdas untuk memaksimalkan potensi retail Anda.</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-4 text-white">Menu</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-slate-400 hover:text-white transition">Beranda</a></li>
                        <li><a href="#katalog" class="text-slate-400 hover:text-white transition">Katalog Produk</a></li>
                        <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition">Dashboard Kasir</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-4 text-white">Kontak</h4>
                    <ul class="space-y-3 text-slate-400">
                        <li>📍 Jl. Retail Modern No. 1, Jakarta</li>
                        <li>📞 +62 811 2233 4455</li>
                        <li>✉️ hello@tokokita.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-500">
                <div>&copy; {{ date('Y') }} TokoKita. All rights reserved.</div>
                <div class="flex gap-4">
                    <a href="{{ route('public.terms') }}" class="hover:text-white transition">Syarat & Ketentuan</a>
                    <a href="{{ route('public.privacy') }}" class="hover:text-white transition">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
