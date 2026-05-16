<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayo Renne — The Epitome of Fine Dining</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');

        :root {
            --gold-primary: #eab308;
            --gold-light: #fef08a;
            --gold-dark: #a16207;
            --obsidian-950: #020617;
            --obsidian-900: #0f172a;
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--obsidian-950);
            color: white;
            overflow-x: hidden;
        }

        .font-luxury { font-family: 'Playfair Display', serif; }

        .glass-panel {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(234, 179, 8, 0.1);
        }

        .gold-gradient-text {
            background: linear-gradient(to right, var(--gold-light), var(--gold-primary), var(--gold-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-overlay {
            background: linear-gradient(to bottom, rgba(2, 6, 23, 0.4), rgba(2, 6, 23, 0.9));
        }

        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .btn-luxury {
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark));
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-luxury:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -10px rgba(234, 179, 8, 0.3);
        }

        .menu-card {
            transition: all 0.4s ease;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .menu-card:hover {
            background: rgba(234, 179, 8, 0.03);
            border-color: rgba(234, 179, 8, 0.15);
            transform: translateY(-10px);
        }

        .scroll-indicator {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-10px);}
            60% {transform: translateY(-5px);}
        }

        .nav-blur {
            background: rgba(2, 6, 23, 0.8);
            backdrop-filter: blur(20px);
        }

        .gallery-image {
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gallery-item:hover .gallery-image {
            transform: scale(1.1);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--obsidian-950); }
        ::-webkit-scrollbar-thumb {
            background: var(--gold-dark);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--gold-primary); }
    </style>
</head>

<body class="antialiased">
    <!-- NAVIGATION -->
    <nav class="fixed top-0 left-0 right-0 z-[100] nav-blur border-b border-white/5 py-4 transition-all duration-500" id="mainNav">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 group">
                <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Logo" class="h-12 w-auto transition-transform duration-500 group-hover:scale-110">
                <div class="hidden sm:block">
                    <span class="text-xs font-black uppercase tracking-[0.4em] text-white/40 block">Ayo Renne</span>
                    <span class="text-[10px] font-bold text-gold-primary tracking-widest uppercase">Premium Dining</span>
                </div>
            </a>

            <div class="hidden lg:flex items-center gap-12">
                @foreach(['Beranda' => '#hero', 'Tentang' => '#tentang', 'Menu' => '#menu', 'Galeri' => '#galeri', 'Kontak' => '#kontak'] as $label => $link)
                    <a href="{{ $link }}" class="text-[11px] font-black uppercase tracking-[0.3em] text-white/50 hover:text-gold-primary transition-colors">{{ $label }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-6">
                <a href="{{ route('public.reservations.create') }}" class="hidden sm:flex items-center gap-2 btn-luxury px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest text-obsidian-950">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Reservasi Meja
                </a>
                <button class="lg:hidden p-2 text-white/60 hover:text-gold-primary" id="mobileMenuBtn">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- MOBILE MENU OVERLAY -->
    <div class="fixed inset-0 z-[110] bg-obsidian-950 flex flex-col items-center justify-center gap-8 transition-all duration-700 translate-x-full" id="mobileMenu">
        <button class="absolute top-8 right-8 text-white/40 hover:text-gold-primary" id="closeMenuBtn">
            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        @foreach(['Beranda' => '#hero', 'Tentang' => '#tentang', 'Menu' => '#menu', 'Galeri' => '#galeri', 'Kontak' => '#kontak'] as $label => $link)
            <a href="{{ $link }}" class="text-3xl font-black uppercase tracking-[0.4em] text-white/20 hover:text-gold-primary transition-all mobile-link">{{ $label }}</a>
        @endforeach
    </div>

    <!-- HERO SECTION -->
    <section id="hero" class="relative h-screen flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/landing/hero-bg.jpg') }}" class="w-full h-full object-cover scale-110" alt="Hero">
            <div class="absolute inset-0 hero-overlay"></div>
        </div>

        <div class="relative z-10 text-center px-6 max-w-5xl" data-aos="fade-up" data-aos-duration="1500">
            <span class="inline-block px-4 py-2 rounded-full bg-gold-primary/10 border border-gold-primary/20 text-[10px] font-black text-gold-primary uppercase tracking-[0.4em] mb-8">Est. 2014 — Probolinggo</span>
            <h1 class="text-6xl md:text-8xl lg:text-9xl font-black text-white italic leading-[0.9] tracking-tighter mb-8">
                The Art of <br><span class="gold-gradient-text not-italic font-luxury">Fine Dining.</span>
            </h1>
            <p class="text-sm md:text-lg text-white/40 max-w-2xl mx-auto leading-relaxed italic mb-12">
                Discover a culinary journey where authentic Indonesian flavors meet contemporary luxury. Every dish tells a story of passion and precision.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="{{ route('public.menu') }}" class="btn-luxury px-12 py-5 rounded-full text-xs font-black uppercase tracking-[0.3em] text-obsidian-950 w-full sm:w-auto">Lihat Menu</a>
                <a href="#tentang" class="px-12 py-5 rounded-full border border-white/20 text-xs font-black uppercase tracking-[0.3em] text-white hover:bg-white/5 transition-all w-full sm:w-auto">Tentang Kami</a>
            </div>
        </div>

        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 scroll-indicator">
            <div class="w-px h-16 bg-gradient-to-b from-transparent via-gold-primary to-transparent"></div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="tentang" class="py-32 relative overflow-hidden">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-gold-primary/5 blur-[120px] rounded-full"></div>
        
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="grid lg:grid-cols-2 gap-24 items-center">
                <div class="relative" data-aos="fade-right">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-4 pt-12">
                            <img src="{{ asset('images/landing/about-1.jpg') }}" class="w-full h-80 object-cover rounded-[3rem] shadow-2xl" alt="About 1">
                            <img src="{{ asset('images/landing/about-3.jpg') }}" class="w-full h-64 object-cover rounded-[3rem] shadow-2xl" alt="About 3">
                        </div>
                        <div class="space-y-4">
                            <img src="{{ asset('images/landing/about-2.jpg') }}" class="w-full h-64 object-cover rounded-[3rem] shadow-2xl" alt="About 2">
                            <img src="{{ asset('images/landing/about-4.jpg') }}" class="w-full h-80 object-cover rounded-[3rem] shadow-2xl" alt="About 4">
                        </div>
                    </div>
                    <!-- Badge Deco -->
                    <div class="absolute -bottom-10 -right-10 glass-panel p-8 rounded-[2.5rem] hidden md:block animate-float">
                        <div class="text-4xl font-black text-gold-primary mb-1 italic tracking-tighter">4.9/5</div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-white/40">Google Rating</div>
                    </div>
                </div>

                <div class="space-y-10" data-aos="fade-left">
                    <div class="space-y-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-gold-primary">Filosofi Kami</span>
                        <h2 class="text-5xl lg:text-7xl font-black text-white italic tracking-tighter leading-none">
                            Cita Rasa <br><span class="gold-gradient-text not-italic font-luxury text-6xl lg:text-8xl">Legendaris.</span>
                        </h2>
                    </div>
                    <p class="text-lg text-white/50 leading-relaxed italic">
                        Ayo Renne adalah manifestasi dari kecintaan kami terhadap kuliner Nusantara. Kami percaya bahwa bersantap bukan sekadar memuaskan lapar, melainkan sebuah ritual apresiasi terhadap rasa dan momen.
                    </p>
                    <div class="grid grid-cols-2 gap-8 pt-8 border-t border-white/5">
                        <div>
                            <h4 class="text-4xl font-black text-white italic tracking-tighter mb-2">10+</h4>
                            <p class="text-[10px] font-black uppercase tracking-widest text-white/30">Tahun Dedikasi</p>
                        </div>
                        <div>
                            <h4 class="text-4xl font-black text-white italic tracking-tighter mb-2">50+</h4>
                            <p class="text-[10px] font-black uppercase tracking-widest text-white/30">Menu Kurasi</p>
                        </div>
                    </div>
                    <a href="#kontak" class="inline-flex items-center gap-4 text-[10px] font-black uppercase tracking-[0.4em] text-gold-primary hover:text-white transition-colors group">
                        Pesan Meja Sekarang
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SIGNATURE MENU SECTION -->
    <section id="menu" class="py-32 bg-obsidian-900/50 relative">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 text-center mb-20">
            <div class="inline-block px-4 py-2 rounded-full bg-gold-primary/5 border border-gold-primary/10 text-[9px] font-black text-gold-primary uppercase tracking-[0.4em] mb-6" data-aos="fade-up">Curated Collection</div>
            <h2 class="text-5xl lg:text-7xl font-black text-white italic tracking-tighter mb-8" data-aos="fade-up" data-aos-delay="100">
                Signature <span class="gold-gradient-text not-italic font-luxury">Gastronomy.</span>
            </h2>
            <p class="text-sm text-white/30 max-w-2xl mx-auto italic" data-aos="fade-up" data-aos-delay="200">Menyajikan mahakarya kuliner yang dipersiapkan dengan teknik modern tanpa menghilangkan esensi tradisi.</p>
        </div>

        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredProducts as $index => $product)
                    <div class="menu-card rounded-[3rem] p-4 flex flex-col h-full" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="relative aspect-[4/5] overflow-hidden rounded-[2.5rem] mb-6">
                            @if($product->imageUrl())
                                <img src="{{ $product->imageUrl() }}" class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" alt="{{ $product->name }}">
                            @else
                                <div class="w-full h-full bg-obsidian-950 flex items-center justify-center">
                                    <svg class="h-12 w-12 text-white/5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                            @endif
                            <div class="absolute bottom-4 right-4 glass-panel px-4 py-2 rounded-2xl">
                                <span class="text-xs font-black text-gold-primary">Rp {{ number_format((int) $product->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="px-4 pb-4 flex flex-col flex-1">
                            <h3 class="text-2xl font-black text-white italic tracking-tighter mb-3 uppercase group-hover:text-gold-primary transition-colors">{{ $product->name }}</h3>
                            <p class="text-xs text-white/30 leading-relaxed italic mb-8 line-clamp-3">
                                {{ $product->description ?: 'Hidangan eksklusif yang dirancang khusus untuk memanjakan panca indera Anda dengan harmoni rasa yang sempurna.' }}
                            </p>
                            <a href="{{ route('public.menu') }}" class="mt-auto inline-flex items-center justify-center py-4 rounded-2xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-white/40 hover:bg-gold-primary hover:text-obsidian-950 hover:border-gold-primary transition-all">Pesan Sekarang</a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-20 text-center">
                <a href="{{ route('public.menu') }}" class="btn-luxury px-12 py-5 rounded-full text-xs font-black uppercase tracking-[0.3em] text-obsidian-950 shadow-2xl shadow-gold-primary/20">Eksplor Menu Lengkap</a>
            </div>
        </div>
    </section>

    <!-- GALLERY SECTION -->
    <section id="galeri" class="py-32 relative overflow-hidden">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 flex flex-col lg:flex-row items-end justify-between mb-20 gap-8">
            <div class="max-w-2xl" data-aos="fade-right">
                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-gold-primary block mb-6">Visual Story</span>
                <h2 class="text-5xl lg:text-7xl font-black text-white italic tracking-tighter leading-none">
                    Momen <br><span class="gold-gradient-text not-italic font-luxury text-6xl lg:text-8xl">Tak Terlupakan.</span>
                </h2>
            </div>
            <p class="text-sm text-white/30 max-w-sm italic lg:text-right" data-aos="fade-left">Jelajahi keindahan setiap sudut dan detail yang kami sajikan untuk Anda.</p>
        </div>

        <div class="max-w-[1600px] mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($galleryItems as $index => $item)
                    <div class="gallery-item relative aspect-square overflow-hidden rounded-[3rem] group" data-aos="zoom-in" data-aos-delay="{{ $index * 50 }}">
                        <img src="{{ $item['image'] }}" class="gallery-image w-full h-full object-cover" alt="{{ $item['title'] }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-obsidian-950 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-8">
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-gold-primary mb-1">{{ $item['title'] }}</span>
                            <h4 class="text-xl font-black text-white italic tracking-tighter">Ayo Renne Series</h4>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="py-32 bg-obsidian-900/30">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="grid lg:grid-cols-2 gap-24 items-center">
                <div data-aos="fade-right">
                    <span class="text-[10px] font-black uppercase tracking-[0.4em] text-gold-primary block mb-6">Voices of Guest</span>
                    <h2 class="text-5xl lg:text-7xl font-black text-white italic tracking-tighter leading-none mb-10">
                        Apa Kata <br><span class="gold-gradient-text not-italic font-luxury text-6xl lg:text-8xl">Mereka?</span>
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 text-gold-primary">
                            @for($i=0; $i<5; $i++)
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            @endfor
                        </div>
                        <p class="text-lg text-white/40 italic">Dipercaya oleh ribuan penikmat kuliner sejak 2014.</p>
                    </div>
                </div>

                <div class="space-y-6" data-aos="fade-left">
                    @foreach(array_slice($testimonials, 0, 3) as $item)
                        <div class="glass-panel p-10 rounded-[3rem] relative">
                            <svg class="absolute top-10 right-10 h-12 w-12 text-white/5" fill="currentColor" viewBox="0 0 32 32"><path d="M10 8v8h6v8H8v-8H4V8h6zm14 0v8h6v8h-8v-8h-4V8h6z" /></svg>
                            <p class="text-lg text-white/70 italic leading-relaxed mb-8">"{{ $item['text'] }}"</p>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gold-primary/10 border border-gold-primary/20 flex items-center justify-center text-gold-primary font-black text-xs">{{ $item['initial'] }}</div>
                                <div>
                                    <h4 class="text-sm font-black text-white uppercase tracking-widest">{{ $item['name'] }}</h4>
                                    <p class="text-[10px] text-white/30 italic">{{ $item['role'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section id="kontak" class="py-32 relative">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="glass-panel rounded-[4rem] overflow-hidden grid lg:grid-cols-2 shadow-[0_40px_100px_-20px_rgba(0,0,0,0.8)]">
                <!-- FORM -->
                <div class="p-10 sm:p-20 order-2 lg:order-1" data-aos="fade-right">
                    <span class="text-[10px] font-black uppercase tracking-[0.4em] text-gold-primary block mb-6">Inquiry</span>
                    <h2 class="text-5xl font-black text-white italic tracking-tighter mb-12">Kirim <br><span class="gold-gradient-text not-italic font-luxury text-6xl">Pesan.</span></h2>
                    
                    <form action="#" class="space-y-6">
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-white/20 ml-2">Nama Lengkap</label>
                                <input type="text" placeholder="John Doe" class="w-full px-6 py-5 rounded-[1.5rem] bg-white/5 border border-white/5 text-sm font-bold text-white placeholder:text-white/10 focus:border-gold-primary/30 transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-white/20 ml-2">Email</label>
                                <input type="email" placeholder="john@example.com" class="w-full px-6 py-5 rounded-[1.5rem] bg-white/5 border border-white/5 text-sm font-bold text-white placeholder:text-white/10 focus:border-gold-primary/30 transition-all outline-none">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-white/20 ml-2">Nomor Telepon</label>
                            <input type="text" placeholder="+62 xxx xxxx xxxx" class="w-full px-6 py-5 rounded-[1.5rem] bg-white/5 border border-white/5 text-sm font-bold text-white placeholder:text-white/10 focus:border-gold-primary/30 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-white/20 ml-2">Pesan</label>
                            <textarea rows="4" placeholder="Tulis pesan Anda di sini..." class="w-full px-6 py-5 rounded-[1.5rem] bg-white/5 border border-white/5 text-sm font-bold text-white placeholder:text-white/10 focus:border-gold-primary/30 transition-all outline-none resize-none"></textarea>
                        </div>
                        <button type="submit" class="btn-luxury w-full py-6 rounded-[1.5rem] text-xs font-black uppercase tracking-[0.3em] text-obsidian-950">Kirim Pesan</button>
                    </form>
                </div>

                <!-- INFO & MAP -->
                <div class="relative min-h-[500px] order-1 lg:order-2" data-aos="fade-left">
                    <div class="absolute inset-0 z-0">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.166898086913!2d113.4064848!3d-7.772120200000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd701aead3ea01d%3A0xdc54aa0c5c148fa8!2sAyo%20Renne%20Cafe%20%26%20Resto%20Probolinggo!5e0!3m2!1sid!2sid!4v1774899746526!5m2!1sid!2sid" class="w-full h-full border-none grayscale opacity-30 hover:grayscale-0 hover:opacity-100 transition-all duration-700" allowfullscreen="" loading="lazy"></iframe>
                        <div class="absolute inset-0 bg-gradient-to-r from-obsidian-950/90 via-obsidian-950/20 to-transparent pointer-events-none lg:block hidden"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-obsidian-950 via-transparent to-transparent pointer-events-none"></div>
                    </div>
                    <div class="relative z-10 p-10 sm:p-20 flex flex-col justify-end h-full">
                        <div class="space-y-8">
                            <div class="flex gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-gold-primary/10 border border-gold-primary/20 flex items-center justify-center text-gold-primary shrink-0">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Lokasi Kami</h4>
                                    <p class="text-[10px] text-white/40 italic">Jl. Raya Probolinggo No. 123, <br>Jawa Timur 67219</p>
                                </div>
                            </div>
                            <div class="flex gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Jam Operasional</h4>
                                    <p class="text-[10px] text-white/40 italic">Setiap Hari: 10:00 - 22:00 WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-24 bg-obsidian-950 border-t border-white/5">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="grid lg:grid-cols-4 gap-16 mb-24">
                <div class="space-y-8">
                    <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Logo" class="h-16 w-auto">
                    <p class="text-base text-white/30 italic leading-relaxed">Destinasi kuliner premium di Probolinggo yang menghadirkan pengalaman bersantap istimewa.</p>
                    <div class="flex items-center gap-3">
                        @foreach([
                            'Instagram' => 'M17.2 6.8r1.1', 
                            'Facebook' => 'M14 8h2V4h-2.5C10.46 4 9 5.79 9 8.6V11H7v4h2v5h4v-5h2.5l.5-4H13V9c0-.6.28-1 1-1Z',
                            'Telegram' => 'M21 4L3 11.53l5.7 2.07L18 7l-7.02 7.12v5.38l3.24-3.12 4.35 3.2c.8.44 1.38.21 1.58-.74L23 5.53C23.27 4.35 22.53 3.82 21 4Z'
                        ] as $name => $path)
                            <a href="#" class="w-11 h-11 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-white/20 hover:text-gold-primary hover:border-gold-primary/30 transition-all">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $path }}" /></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <h4 class="text-xs font-black text-white uppercase tracking-[0.3em] mb-10 italic">Navigasi</h4>
                    <div class="flex flex-col gap-5 text-[11px] font-bold text-white/30 uppercase tracking-widest">
                        <a href="#hero" class="hover:text-gold-primary transition-colors">Beranda</a>
                        <a href="#tentang" class="hover:text-gold-primary transition-colors">Tentang</a>
                        <a href="#menu" class="hover:text-gold-primary transition-colors">Menu</a>
                        <a href="#galeri" class="hover:text-gold-primary transition-colors">Galeri</a>
                        <a href="#kontak" class="hover:text-gold-primary transition-colors">Kontak</a>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-black text-white uppercase tracking-[0.3em] mb-10 italic">Kontak</h4>
                    <div class="flex flex-col gap-6 text-[11px] font-bold text-white/30 uppercase tracking-widest leading-relaxed">
                        <div class="flex flex-col gap-1">
                            <span class="text-white/10 text-[9px] uppercase tracking-widest">Alamat</span>
                            <span>Jl. Raya Probolinggo No. 123,<br>Probolinggo, Jawa Timur 67219</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-white/10 text-[9px] uppercase tracking-widest">Telepon</span>
                            <span>+62 335 421 888</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-white/10 text-[9px] uppercase tracking-widest">Email</span>
                            <span>info@ayorenne.com</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-black text-white uppercase tracking-[0.3em] mb-10 italic">Jam Buka</h4>
                    <div class="flex flex-col gap-6 text-[11px] font-bold text-white/30 uppercase tracking-widest leading-relaxed">
                        <div class="flex flex-col gap-1">
                            <span class="text-white/10 text-[9px] uppercase tracking-widest">Senin - Jumat</span>
                            <span>10:00 - 22:00 WIB</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-white/10 text-[9px] uppercase tracking-widest">Sabtu - Minggu</span>
                            <span>09:00 - 23:00 WIB</span>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('public.reservations.create') }}" class="inline-block gold-gradient-text font-black text-[9px] uppercase tracking-[0.3em]">Book a Table →</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-10 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-8 text-[10px] font-black uppercase tracking-[0.2em] text-white/20 italic">
                <div class="flex flex-col md:flex-row items-center gap-4 md:gap-8">
                    <span>© {{ date('Y') }} Ayo Renne. All rights reserved.</span>
                    <div class="flex gap-6 md:border-l md:border-white/10 md:pl-8">
                        <a href="{{ route('public.terms') }}" class="hover:text-gold-primary transition-colors">Syarat & Ketentuan</a>
                        <a href="{{ route('public.privacy') }}" class="hover:text-gold-primary transition-colors">Kebijakan Privasi</a>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span>Made with 💛 in Probolinggo</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            mirror: false
        });

        // Navbar Scroll Effect
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                nav.classList.add('shadow-2xl', 'py-3');
                nav.classList.remove('py-4');
            } else {
                nav.classList.remove('shadow-2xl', 'py-3');
                nav.classList.add('py-4');
            }
        });

        // Mobile Menu Logic
        const menuBtn = document.getElementById('mobileMenuBtn');
        const closeBtn = document.getElementById('closeMenuBtn');
        const menu = document.getElementById('mobileMenu');
        const links = document.querySelectorAll('.mobile-link');

        menuBtn.addEventListener('click', () => {
            menu.classList.remove('translate-x-full');
        });

        const closeMenu = () => {
            menu.classList.add('translate-x-full');
        };

        closeBtn.addEventListener('click', closeMenu);
        links.forEach(link => link.addEventListener('click', closeMenu));
    </script>
</body>

</html>