<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayo Renne Store - Premium Retail & Souvenir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Base Dark Luxury Theme */
        body {
            background:
                radial-gradient(circle at top right, rgba(234, 179, 8, 0.12), transparent 30%),
                radial-gradient(circle at bottom left, rgba(234, 179, 8, 0.08), transparent 40%),
                linear-gradient(180deg, #020202 0%, #0a0a0a 100%);
            background-attachment: fixed;
            overflow-x: hidden;
        }
        .font-display {
            font-family: Georgia, "Times New Roman", serif;
        }
        
        /* Wow Animations */
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(234, 179, 8, 0.2); }
            50% { box-shadow: 0 0 40px rgba(234, 179, 8, 0.6); }
        }
        @keyframes border-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .animate-fade-up {
            animation: fade-up 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }

        .animate-float {
            animation: float 7s ease-in-out infinite;
        }

        /* The "Wow" Glowing Border Card */
        .glow-card-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
            padding: 2px;
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .glow-card-wrapper:hover {
            transform: translateY(-10px);
        }
        .glow-card-wrapper::before {
            content: "";
            position: absolute;
            top: -100%; left: -100%;
            width: 300%; height: 300%;
            background: conic-gradient(from 0deg, transparent 0%, transparent 35%, rgba(234, 179, 8, 0.9) 50%, transparent 65%, transparent 100%);
            animation: border-spin 4s linear infinite;
            z-index: 0;
        }
        .glow-card-content {
            position: relative;
            z-index: 1;
            background: linear-gradient(145deg, #121212 0%, #080808 100%);
            border-radius: 1.4rem;
            height: 100%;
            overflow: hidden;
        }

        /* Glassmorphism Elements */
        .glass-panel {
            background: rgba(10, 10, 10, 0.55);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(234, 179, 8, 0.15);
        }
        
        /* Image Hover Reveal */
        .reveal-img-wrapper {
            overflow: hidden;
        }
        .reveal-img {
            transition: transform 1s cubic-bezier(0.16, 1, 0.3, 1), filter 1s;
            filter: brightness(0.8);
        }
        .group:hover .reveal-img {
            transform: scale(1.1);
            filter: brightness(1.1);
        }
        
        /* Magnetic Button Effect */
        .btn-gold {
            background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
            box-shadow: 0 4px 15px rgba(202, 138, 4, 0.3);
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            box-shadow: 0 8px 25px rgba(202, 138, 4, 0.5);
            transform: translateY(-2px);
            background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
        }
    </style>
</head>
<body class="text-white antialiased min-h-screen flex flex-col font-sans">
    
    <!-- Navbar -->
    <header class="sticky top-0 z-50 glass-panel border-b border-yellow-500/10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[76px] flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 group">
                <div class="relative animate-float" style="animation-duration: 4s;">
                    <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne Logo" class="h-12 w-auto object-contain">
                </div>
                <div class="hidden sm:block">
                    <span class="font-display font-bold text-xl tracking-wide text-white group-hover:text-yellow-400 transition-colors">Ayo Renne</span>
                    <span class="block text-[10px] uppercase tracking-[0.2em] text-yellow-500/80">Store & Retail</span>
                </div>
            </a>
            
            <nav class="hidden md:flex gap-10 font-medium text-sm tracking-wider uppercase">
                <a href="#" class="text-yellow-500 relative after:content-[''] after:absolute after:-bottom-2 after:left-0 after:w-full after:h-0.5 after:bg-yellow-500">Beranda</a>
                <a href="{{ route('public.toko.katalog') }}" class="text-white/70 hover:text-yellow-400 transition-colors">Katalog Koleksi</a>
                <a href="#cerita" class="text-white/70 hover:text-yellow-400 transition-colors">Cerita Kami</a>
            </nav>
            
            <div class="flex items-center gap-5">
                <a href="{{ route('login') }}" class="text-sm font-medium text-white/70 hover:text-yellow-400 transition hidden sm:block uppercase tracking-wider">Akses Kasir</a>
                <a href="{{ route('public.toko.katalog') }}" class="btn-gold text-black px-6 py-2.5 rounded-full font-bold text-sm uppercase tracking-wide">
                    Mulai Belanja
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1">
        <section class="relative pt-24 pb-32 lg:pt-36 lg:pb-40 overflow-hidden flex items-center min-h-[90vh]">
            <!-- Decorative Light Beams -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-4xl opacity-20 pointer-events-none z-0">
                <div class="absolute top-[-10%] left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-yellow-500 rounded-full mix-blend-screen filter blur-[120px] animate-pulse"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                    
                    <!-- Text Content -->
                    <div class="text-center lg:text-left pt-10">
                        <div class="animate-fade-up inline-flex items-center gap-3 px-4 py-2 rounded-full glass-panel text-yellow-400 text-xs font-bold uppercase tracking-[0.2em] mb-8">
                            <span class="flex h-2 w-2 rounded-full bg-yellow-400" style="box-shadow: 0 0 10px #facc15;"></span>
                            Pengalaman Retail Premium
                        </div>
                        
                        <h1 class="animate-fade-up delay-100 font-display text-5xl md:text-7xl lg:text-[84px] font-bold tracking-tight text-white mb-6 leading-[1.05]">
                            Eksklusifitas <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-yellow-500 to-yellow-600 italic font-normal">Dalam Genggaman</span>
                        </h1>
                        
                        <p class="animate-fade-up delay-200 text-lg md:text-xl text-white/60 max-w-xl mx-auto lg:mx-0 mb-10 leading-relaxed font-light">
                            Temukan koleksi pilihan dan souvenir premium dari Ayo Renne. Kualitas terbaik, dirancang khusus untuk memenuhi standar gaya hidup Anda.
                        </p>
                        
                        <div class="animate-fade-up delay-300 flex flex-col sm:flex-row justify-center lg:justify-start gap-5">
                            <a href="{{ route('public.toko.katalog') }}" class="btn-gold text-black px-8 py-4 rounded-full font-bold text-base uppercase tracking-widest flex items-center justify-center gap-3">
                                Jelajahi Koleksi
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                            <a href="{{ route('public.terms') }}" class="glass-panel text-white hover:text-yellow-400 border border-white/10 px-8 py-4 rounded-full font-bold text-base uppercase tracking-widest transition-all hover:border-yellow-500/50 flex items-center justify-center">
                                Syarat Pembelian
                            </a>
                        </div>
                    </div>

                    <!-- Floating 3D/Visual Element -->
                    <div class="animate-fade-up delay-400 relative hidden lg:block h-[600px] perspective-1000">
                        <div class="absolute inset-0 flex items-center justify-center animate-float">
                            <!-- Rotating Showcase Circle -->
                            <div class="relative w-[450px] h-[450px] rounded-full border border-yellow-500/20 flex items-center justify-center" style="animation: border-spin 30s linear infinite;">
                                <div class="absolute w-[400px] h-[400px] rounded-full border border-dashed border-yellow-500/30" style="animation: border-spin 20s linear infinite reverse;"></div>
                            </div>
                            
                            <!-- Main Showcase Card -->
                            <div class="absolute w-80 h-[420px] rounded-2xl glass-panel p-4 shadow-[0_20px_50px_rgba(0,0,0,0.5)] transform -rotate-6 hover:rotate-0 transition-transform duration-700">
                                <div class="w-full h-full rounded-xl overflow-hidden relative">
                                    <img src="{{ asset('images/landing/gallery-5.jpg') }}" alt="Premium Showcase" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                                    <div class="absolute bottom-6 left-6">
                                        <div class="text-xs text-yellow-500 font-bold tracking-widest uppercase mb-1">New Arrival</div>
                                        <div class="text-2xl font-display text-white">Signature Blend</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Secondary Floating Card -->
                            <div class="absolute -bottom-10 -right-10 w-48 h-56 rounded-2xl glass-panel p-3 shadow-[0_20px_50px_rgba(0,0,0,0.5)] transform rotate-12 hover:rotate-0 transition-transform duration-700 delay-100" style="animation: float 5s ease-in-out infinite reverse;">
                                <div class="w-full h-full rounded-xl overflow-hidden relative">
                                    <img src="{{ asset('images/landing/gallery-2.jpg') }}" alt="Accessory Showcase" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic Product Carousel Section (Katalog) -->
        <section id="katalog" class="py-24 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20 animate-fade-up">
                    <div class="text-xs font-bold uppercase tracking-[0.3em] text-yellow-500 mb-4 flex items-center justify-center gap-4">
                        <span class="w-12 h-[1px] bg-yellow-500/50"></span>
                        Koleksi Eksklusif
                        <span class="w-12 h-[1px] bg-yellow-500/50"></span>
                    </div>
                    <h2 class="font-display text-4xl md:text-5xl font-bold text-white">Pilihan Terbaik Kami</h2>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- WOW Glowing Card 1 -->
                    <div class="glow-card-wrapper group animate-fade-up delay-100">
                        <div class="glow-card-content p-6 flex flex-col">
                            <div class="reveal-img-wrapper h-64 rounded-xl mb-6 relative">
                                <img src="{{ asset('images/landing/gallery-6.jpg') }}" alt="Product 1" class="reveal-img w-full h-full object-cover">
                                <div class="absolute top-4 right-4 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-yellow-400 border border-yellow-500/30">
                                    Limited
                                </div>
                            </div>
                            <h3 class="font-display text-2xl text-white mb-2">Premium Gift Box</h3>
                            <p class="text-white/50 font-light text-sm mb-6 flex-1">Koleksi hadiah eksklusif yang dikemas secara mewah, sempurna untuk momen spesial orang terkasih.</p>
                            <div class="flex justify-between items-center border-t border-white/10 pt-4 mt-auto">
                                <span class="text-yellow-500 font-bold text-lg">Rp 450.000</span>
                                <button class="text-xs uppercase tracking-widest font-bold text-white hover:text-yellow-400 transition-colors flex items-center gap-2">
                                    Beli <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- WOW Glowing Card 2 -->
                    <div class="glow-card-wrapper group animate-fade-up delay-200">
                        <div class="glow-card-content p-6 flex flex-col">
                            <div class="reveal-img-wrapper h-64 rounded-xl mb-6 relative">
                                <img src="{{ asset('images/landing/gallery-4.jpg') }}" alt="Product 2" class="reveal-img w-full h-full object-cover">
                            </div>
                            <h3 class="font-display text-2xl text-white mb-2">Ayo Renne Tumbler</h3>
                            <p class="text-white/50 font-light text-sm mb-6 flex-1">Tumbler isolasi termal premium dengan ukiran logo emas Ayo Renne. Menjaga suhu minuman hingga 12 jam.</p>
                            <div class="flex justify-between items-center border-t border-white/10 pt-4 mt-auto">
                                <span class="text-yellow-500 font-bold text-lg">Rp 280.000</span>
                                <button class="text-xs uppercase tracking-widest font-bold text-white hover:text-yellow-400 transition-colors flex items-center gap-2">
                                    Beli <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- WOW Glowing Card 3 -->
                    <div class="glow-card-wrapper group animate-fade-up delay-300">
                        <div class="glow-card-content p-6 flex flex-col">
                            <div class="reveal-img-wrapper h-64 rounded-xl mb-6 relative">
                                <img src="{{ asset('images/landing/about-3.jpg') }}" alt="Product 3" class="reveal-img w-full h-full object-cover">
                            </div>
                            <h3 class="font-display text-2xl text-white mb-2">Artisan Coffee Beans</h3>
                            <p class="text-white/50 font-light text-sm mb-6 flex-1">Biji kopi pilihan yang dipanggang dengan tingkat presisi tinggi untuk menghasilkan aroma yang memikat.</p>
                            <div class="flex justify-between items-center border-t border-white/10 pt-4 mt-auto">
                                <span class="text-yellow-500 font-bold text-lg">Rp 125.000</span>
                                <button class="text-xs uppercase tracking-widest font-bold text-white hover:text-yellow-400 transition-colors flex items-center gap-2">
                                    Beli <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats / Parallax Section -->
        <section id="cerita" class="py-24 border-t border-b border-yellow-500/10 relative overflow-hidden bg-black/40">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#eab308 1px, transparent 1px); background-size: 30px 30px;"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid md:grid-cols-3 gap-10 text-center divide-y md:divide-y-0 md:divide-x divide-yellow-500/20">
                    <div class="py-6 animate-fade-up">
                        <div class="font-display text-5xl md:text-7xl text-yellow-500 mb-2 font-bold drop-shadow-[0_0_15px_rgba(234,179,8,0.3)]">100%</div>
                        <div class="text-white/70 uppercase tracking-widest text-sm font-bold">Kualitas Premium</div>
                    </div>
                    <div class="py-6 animate-fade-up delay-100">
                        <div class="font-display text-5xl md:text-7xl text-yellow-500 mb-2 font-bold drop-shadow-[0_0_15px_rgba(234,179,8,0.3)]">5K+</div>
                        <div class="text-white/70 uppercase tracking-widest text-sm font-bold">Pelanggan Puas</div>
                    </div>
                    <div class="py-6 animate-fade-up delay-200">
                        <div class="font-display text-5xl md:text-7xl text-yellow-500 mb-2 font-bold drop-shadow-[0_0_15px_rgba(234,179,8,0.3)]">24/7</div>
                        <div class="text-white/70 uppercase tracking-widest text-sm font-bold">Dukungan Transaksi</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Premium Footer -->
    <footer class="bg-[#050505] pt-24 pb-10 border-t border-yellow-500/10 relative overflow-hidden">
        <!-- Glow at bottom -->
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-yellow-600 rounded-full mix-blend-screen filter blur-[150px] opacity-10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-4 gap-12 mb-16">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne Logo" class="h-12 w-auto object-contain">
                        <div>
                            <span class="font-display font-bold text-2xl text-white block">Ayo Renne</span>
                            <span class="text-yellow-500 text-xs uppercase tracking-widest">Store Edition</span>
                        </div>
                    </div>
                    <p class="text-white/50 leading-relaxed max-w-md font-light text-sm">
                        Membawa kemewahan dan kelezatan Ayo Renne langsung ke rumah Anda. Belanja koleksi eksklusif, suvenir, dan produk premium kami dengan sistem POS pintar.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bold text-white mb-6 uppercase tracking-widest text-sm">Pintasan</h4>
                    <ul class="space-y-4 text-sm">
                        <li><a href="#" class="text-white/50 hover:text-yellow-400 transition flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Beranda</a></li>
                        <li><a href="{{ route('public.toko.katalog') }}" class="text-white/50 hover:text-yellow-400 transition flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Katalog</a></li>
                        <li><a href="{{ route('login') }}" class="text-white/50 hover:text-yellow-400 transition flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Login Kasir</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-white mb-6 uppercase tracking-widest text-sm">Hubungi Kami</h4>
                    <ul class="space-y-4 text-sm text-white/50 font-light">
                        <li class="flex items-start gap-3">
                            <span class="text-yellow-500 mt-1">📍</span>
                            <span>Jl. Raya Probolinggo No. 123,<br>Jawa Timur 67219</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-yellow-500">📞</span>
                            <span>+62 335 421 888</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-yellow-500">✉️</span>
                            <span>store@ayorenne.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-light text-white/40 uppercase tracking-wider">
                <div>&copy; {{ date('Y') }} Ayo Renne Retail. All rights reserved.</div>
                <div class="flex gap-6">
                    <a href="{{ route('public.terms') }}" class="hover:text-yellow-400 transition">Syarat & Ketentuan</a>
                    <a href="{{ route('public.privacy') }}" class="hover:text-yellow-400 transition">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Intersection Observer for Scroll Animations -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.animate-fade-up').forEach(el => {
                el.style.animationPlayState = 'paused';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
