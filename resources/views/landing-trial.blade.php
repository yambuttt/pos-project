<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayo Renne — Landing Trial</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            background:
                radial-gradient(circle at top, rgba(234, 179, 8, .08), transparent 24%),
                linear-gradient(180deg, #020202 0%, #060606 100%);
        }

        .font-display {
            font-family: Georgia, "Times New Roman", serif;
        }

        .glass-dark {
            background: rgba(18, 18, 18, 0.82);
            backdrop-filter: blur(10px);
        }

        .gold-border {
            border-color: rgba(234, 179, 8, 0.28);
        }

        .gold-shadow {
            box-shadow: 0 0 0 1px rgba(234, 179, 8, .14), 0 16px 40px rgba(0, 0, 0, .34);
        }

        .hero-overlay {
            background:
                linear-gradient(90deg, rgba(0, 0, 0, .88) 0%, rgba(0, 0, 0, .72) 34%, rgba(0, 0, 0, .48) 60%, rgba(0, 0, 0, .36) 100%),
                linear-gradient(180deg, rgba(0, 0, 0, .28) 0%, rgba(0, 0, 0, .55) 100%);
        }

.section-wrap {
    width: 100%;
    max-width: 1120px;
    margin-inline: auto;
    padding-inline: 16px;
}

@media (min-width: 640px) {
    .section-wrap {
        padding-inline: 24px;
    }
}

@media (min-width: 1024px) {
    .section-wrap {
        padding-inline: 32px;
    }
}

        .menu-card-image::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, .70), rgba(0, 0, 0, .06));
        }
        *,
*::before,
*::after {
    box-sizing: border-box;
}

img,
iframe,
video,
canvas,
svg {
    max-width: 100%;
}

body {
    overflow-x: hidden;
}
    </style>
</head>

<body class="overflow-x-hidden bg-[#050505] text-white antialiased">
    <header class="sticky top-0 z-50 border-b border-yellow-500/10 bg-black/90 backdrop-blur-xl">
        <div class="section-wrap flex items-center justify-between gap-3 py-3 sm:gap-6 sm:py-4">
            <a href="#hero" class="flex items-center gap-3">
                <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne Logo"
                    class="h-10 w-auto object-contain sm:h-14">
            </a>

            <nav class="hidden items-center gap-8 text-sm font-medium text-white/90 lg:flex">
                <a href="#hero" class="transition hover:text-yellow-400">Beranda</a>
                <a href="#tentang" class="transition hover:text-yellow-400">Tentang</a>
                <a href="#menu" class="transition hover:text-yellow-400">Menu</a>
                <a href="#galeri" class="transition hover:text-yellow-400">Galeri</a>
                <a href="#kontak" class="transition hover:text-yellow-400">Kontak</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="#kontak"
                    class="inline-flex items-center gap-2 rounded-xl bg-yellow-500 px-3 py-2.5 text-sm font-semibold text-black transition hover:bg-yellow-400 sm:px-5 sm:py-3">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.87 19.87 0 0 1-8.63-3.07A19.5 19.5 0 0 1 5.15 12.8 19.87 19.87 0 0 1 2.08 4.09 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.63 2.62a2 2 0 0 1-.45 2.11L8 9.91a16 16 0 0 0 6.09 6.09l1.46-1.24a2 2 0 0 1 2.11-.45c.84.3 1.72.51 2.62.63A2 2 0 0 1 22 16.92Z"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Reservasi
                </a>

                <button type="button" id="mobileMenuBtn"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-yellow-500/20 bg-white/5 text-white lg:hidden"
                    aria-label="Toggle menu">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobileMenu" class="hidden border-t border-yellow-500/10 bg-black/95 lg:hidden">
            <div class="section-wrap flex flex-col gap-3 py-4 text-sm">
                <a href="#hero" class="text-white/85">Beranda</a>
                <a href="#tentang" class="text-white/85">Tentang</a>
                <a href="#menu" class="text-white/85">Menu</a>
                <a href="#galeri" class="text-white/85">Galeri</a>
                <a href="#kontak" class="text-white/85">Kontak</a>
            </div>
        </div>
    </header>

    <main>
        <section id="hero" class="relative min-h-[92vh] overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ asset('images/landing/hero-bg.jpg') }}" alt="Ayo Renne Hero"
                    class="h-full w-full object-cover">
                <div class="hero-overlay absolute inset-0"></div>
                <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-[#050505] to-transparent"></div>
            </div>

            <div
                class="relative mx-auto flex min-h-[92vh] w-full max-w-[1400px] items-center px-6 py-20 sm:px-8 lg:px-12 xl:px-16">
                <div class="max-w-3xl lg:ml-0 lg:max-w-[760px] xl:max-w-[820px]">
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-yellow-500/20 bg-yellow-500/8 px-4 py-2 text-sm font-medium text-yellow-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z" stroke="currentColor"
                                stroke-width="1.8" />
                            <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8" />
                        </svg>
                        Probolinggo
                    </div>

                    <h1 class="font-display text-[46px] font-bold leading-[1.04] text-white sm:text-6xl lg:text-7xl">
                        Ayo Renne
                        <span class="mt-2 block text-yellow-500">Cafe &amp; Resto</span>
                    </h1>

                    <p class="mt-5 max-w-xl text-[18px] leading-[1.85] text-white/80 sm:text-[20px] sm:leading-[1.9]">
                        Ayo Renne adalah destinasi kuliner premium di Probolinggo yang menghadirkan pengalaman bersantap
                        istimewa. Dengan perpaduan sempurna antara cita rasa autentik Indonesia dan sentuhan modern,
                        kami menciptakan momen tak terlupakan untuk setiap tamu.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <div class="glass-dark gold-border gold-shadow rounded-2xl border px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-500/10 text-yellow-400">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
                                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs text-white/45">Jam Buka</div>
                                    <div class="text-xl font-semibold text-white">10:00 - 22:00 WIB</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('public.menu') }}"
                            class="inline-flex items-center gap-3 rounded-xl bg-yellow-500 px-7 py-4 text-base font-semibold text-black transition hover:bg-yellow-400">
                            Lihat Menu
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>

                        <a href="#kontak"
                            class="inline-flex items-center rounded-xl border border-white/55 px-7 py-4 text-base font-semibold text-white transition hover:bg-white/10">
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="tentang" class="bg-[#050505] py-24">
    <div class="mx-auto w-full max-w-[1280px] px-6 sm:px-8 lg:px-10 xl:px-12">
        <div class="grid items-start gap-14 lg:grid-cols-[1.08fr_.92fr] xl:gap-16">
            <!-- LEFT -->
            <div class="max-w-[720px]">
                <div class="mb-8">
                    <div class="text-sm font-semibold uppercase tracking-[0.18em] text-yellow-500">Tentang Kami</div>
                    <div class="mt-3 h-[3px] w-16 rounded-full bg-yellow-500"></div>
                </div>

                <h2 class="font-display text-[42px] font-bold leading-[1.08] text-white sm:text-[48px] xl:text-[60px]">
                    Cita Rasa Autentik dengan
                    <br>
                    Sentuhan Modern
                </h2>

                <p class="mt-8 max-w-[680px] text-[17px] leading-[1.9] text-white/78 sm:text-[19px]">
                    Ayo Renne adalah destinasi kuliner premium di Probolinggo yang menghadirkan pengalaman bersantap istimewa. Dengan perpaduan sempurna antara cita rasa otentik Indonesia dan sentuhan modern, kami menciptakan momen tak terlupakan untuk setiap tamu.
                </p>

                <p class="mt-6 max-w-[680px] text-[16px] leading-[1.9] text-white/62 sm:text-[18px]">
                    Berlokasi strategis di Probolinggo, kami menghadirkan suasana yang nyaman dan elegan untuk berbagai acara Anda. Dari pertemuan keluarga hingga acara bisnis, Ayo Renne adalah pilihan sempurna.
                </p>

                <!-- STATS -->
                <div class="mt-10 grid grid-cols-2 gap-y-6 sm:grid-cols-4 sm:gap-x-8">
                    <div>
                        <div class="font-display text-5xl font-bold text-yellow-500">10+</div>
                        <div class="mt-2 text-base text-white/68">Tahun Pengalaman</div>
                    </div>
                    <div>
                        <div class="font-display text-5xl font-bold text-yellow-500">50+</div>
                        <div class="mt-2 text-base text-white/68">Menu Pilihan</div>
                    </div>
                    <div>
                        <div class="font-display text-5xl font-bold text-yellow-500">1000+</div>
                        <div class="mt-2 text-base text-white/68">Pelanggan Setia</div>
                    </div>
                    <div>
                        <div class="font-display text-5xl font-bold text-yellow-500">4.9</div>
                        <div class="mt-2 text-base text-white/68">Rating Bintang</div>
                    </div>
                </div>

                <!-- FEATURES -->
                <div class="mt-12 grid gap-x-8 gap-y-7 sm:grid-cols-2">
                    @php
                        $features = [
                            ['title' => 'Kualitas Premium', 'desc' => 'Bahan-bahan pilihan terbaik untuk setiap hidangan'],
                            ['title' => 'Chef Berpengalaman', 'desc' => 'Tim koki profesional dengan keahlian internasional'],
                            ['title' => 'Pelayanan Ramah', 'desc' => 'Memberikan pengalaman terbaik untuk setiap tamu'],
                            ['title' => 'Award Winning', 'desc' => 'Diakui sebagai restoran terbaik di Probolinggo'],
                        ];
                    @endphp

                    @foreach($features as $feature)
                        <div class="flex items-start gap-4">
                            <div class="inline-flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-yellow-500/20 bg-yellow-500/6 text-yellow-500">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    <path d="M9.5 12.5l1.5 1.5 3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[18px] font-semibold leading-[1.35] text-white">
                                    {{ $feature['title'] }}
                                </div>
                                <div class="mt-2 text-[16px] leading-[1.75] text-white/60">
                                    {{ $feature['desc'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- RIGHT -->
            <div class="mx-auto w-full max-w-[520px] lg:mx-0 lg:max-w-none">
                <div class="grid grid-cols-2 gap-3 sm:gap-4 md:gap-5">
                    <div class="overflow-hidden rounded-2xl">
                        <img src="{{ asset('images/landing/about-1.jpg') }}" alt="" class="h-[300px] w-full object-cover">
                    </div>

                    <div class="overflow-hidden rounded-2xl pt-4">
                        <img src="{{ asset('images/landing/about-2.jpg') }}" alt="" class="h-[220px] w-full object-cover">
                    </div>

                    <div class="overflow-hidden rounded-2xl">
                        <img src="{{ asset('images/landing/about-3.jpg') }}" alt="" class="h-[230px] w-full object-cover">
                    </div>

                    <div class="overflow-hidden rounded-2xl pt-2">
                        <img src="{{ asset('images/landing/about-4.jpg') }}" alt="" class="h-[260px] w-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

        <section id="menu" class="bg-[#151515] py-24">
            <div class="section-wrap">
                <div class="mx-auto max-w-3xl text-center">
                    <div class="text-sm font-semibold uppercase tracking-[0.18em] text-yellow-500">Menu Spesial</div>
                    <div class="mx-auto mt-3 h-[3px] w-16 rounded-full bg-yellow-500"></div>

                    <h2 class="font-display mt-6 text-4xl font-bold text-white sm:text-5xl">
                        Menu Unggulan Kami
                    </h2>

                    <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-white/62">
                        Nikmati berbagai hidangan pilihan dengan cita rasa istimewa yang diolah oleh chef berpengalaman.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        @php
                            $filterPills = ['All', 'Main Course', 'Signature Dish', 'Traditional', 'Healthy Choice'];
                        @endphp
                        @foreach($filterPills as $idx => $pill)
                            <span
                                class="{{ $idx === 0 ? 'bg-yellow-500 text-black' : 'border border-white/10 bg-white/5 text-white/70' }} rounded-full px-5 py-2 text-sm font-medium">
                                {{ $pill }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="mx-auto mt-14 grid max-w-5xl gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($featuredProducts as $index => $product)
                        @php
                            $tags = ['Main Course', 'Signature Dish', 'Traditional', 'Healthy Choice'];
                            $tag = $tags[$index % count($tags)];
                            $img = $product->imageUrl();
                        @endphp

                        <article
                            class="overflow-hidden rounded-2xl border border-yellow-500/15 bg-black shadow-[0_0_0_1px_rgba(234,179,8,.08)]">
                            <div class="menu-card-image relative h-64 overflow-hidden">
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $product->name }}"
                                        class="h-full w-full object-cover transition duration-500 hover:scale-105">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-yellow-500/20 via-zinc-900 to-black"></div>
                                @endif

                                <div
                                    class="absolute right-3 top-3 z-10 rounded-full bg-yellow-500 px-3 py-1 text-xs font-semibold text-black">
                                    {{ $tag }}
                                </div>
                            </div>

                            <div class="p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <h3 class="text-2xl font-semibold text-white">{{ $product->name }}</h3>
                                    <div class="shrink-0 text-lg font-bold text-yellow-500">
                                        Rp {{ number_format((int) $product->price, 0, ',', '.') }}
                                    </div>
                                </div>

                                <p class="mt-3 min-h-[72px] text-base leading-7 text-white/60">
                                    {{ $product->description ?: 'Hidangan spesial dengan penyajian premium khas Ayo Renne.' }}
                                </p>

                                <a href="{{ route('public.menu') }}"
                                    class="mt-5 inline-flex w-full items-center justify-center rounded-lg border border-yellow-500/35 px-4 py-3 text-sm font-semibold text-yellow-500 transition hover:bg-yellow-500 hover:text-black">
                                    Pesan Sekarang
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10 text-center">
                    <a href="{{ route('public.menu') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-yellow-500/40 px-6 py-3 text-sm font-semibold text-white transition hover:bg-yellow-500 hover:text-black">
                        Lihat Menu Lengkap
                    </a>
                </div>
            </div>
        </section>

        <section class="bg-[#050505] py-24">
            <div class="section-wrap">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="text-sm font-semibold uppercase tracking-[0.18em] text-yellow-500">Testimoni</div>
                    <div class="mx-auto mt-3 h-[3px] w-16 rounded-full bg-yellow-500"></div>

                    <h2 class="font-display mt-6 text-4xl font-bold text-white sm:text-5xl">Apa Kata Mereka?</h2>

                    <p class="mx-auto mt-5 max-w-3xl text-lg leading-8 text-white/62">
                        Kepuasan pelanggan adalah prioritas kami. Simak pengalaman mereka yang telah merasakan cita rasa
                        Ayo Renne.
                    </p>
                </div>

                <div class="mt-16 grid gap-6 lg:grid-cols-4">
                    @foreach($testimonials as $item)
                        <article class="rounded-2xl border border-yellow-500/16 bg-[#171717] p-6">
                            <div class="mb-5 flex items-center gap-1 text-yellow-500">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                                        <path
                                            d="M12 17.3l-6.18 3.25 1.18-6.88L2 8.95l6.91-1 3.09-6.26 3.09 6.26 6.91 1-5 4.72 1.18 6.88z" />
                                    </svg>
                                @endfor
                            </div>

                            <p class="min-h-[180px] text-lg leading-8 text-white/78">
                                "{{ $item['text'] }}"
                            </p>

                            <div class="mt-6 border-t border-white/10 pt-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-yellow-500 font-bold text-black">
                                        {{ $item['initial'] }}
                                    </div>
                                    <div>
                                        <div class="text-xl font-semibold text-white">{{ $item['name'] }}</div>
                                        <div class="text-sm text-white/45">{{ $item['role'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-16 grid grid-cols-2 gap-8 text-center md:grid-cols-4">
                    <div>
                        <div class="font-display text-5xl font-bold text-yellow-500">500+</div>
                        <div class="mt-3 text-lg text-white/60">Review Positif</div>
                    </div>
                    <div>
                        <div class="font-display text-5xl font-bold text-yellow-500">4.9/5</div>
                        <div class="mt-3 text-lg text-white/60">Rating Google</div>
                    </div>
                    <div>
                        <div class="font-display text-5xl font-bold text-yellow-500">98%</div>
                        <div class="mt-3 text-lg text-white/60">Pelanggan Puas</div>
                    </div>
                    <div>
                        <div class="font-display text-5xl font-bold text-yellow-500">#1</div>
                        <div class="mt-3 text-lg text-white/60">Resto Probolinggo</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="galeri" class="bg-[#151515] py-24">
            <div class="section-wrap">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="text-sm font-semibold uppercase tracking-[0.18em] text-yellow-500">Galeri</div>
                    <div class="mx-auto mt-3 h-[3px] w-16 rounded-full bg-yellow-500"></div>

                    <h2 class="font-display mt-6 text-4xl font-bold text-white sm:text-5xl">Momen di Ayo Renne</h2>

                    <p class="mx-auto mt-5 max-w-3xl text-lg leading-8 text-white/62">
                        Jelajahi suasana nyaman dan hidangan lezat yang menanti Anda.
                    </p>
                </div>

                <div class="mt-14 grid auto-rows-[220px] gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($galleryItems as $item)
                        @php
                            $spanClass = $item['size'] === 'large'
                                ? 'md:row-span-2'
                                : '';
                        @endphp

                        <div class="group relative overflow-hidden rounded-2xl {{ $spanClass }}">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-black/10 to-transparent"></div>
                            <div
                                class="absolute bottom-4 left-4 rounded-full bg-yellow-500 px-4 py-2 text-sm font-semibold text-black">
                                {{ $item['title'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="kontak" class="bg-[#050505] py-24">
            <div class="section-wrap">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="text-sm font-semibold uppercase tracking-[0.18em] text-yellow-500">Hubungi Kami</div>
                    <div class="mx-auto mt-3 h-[3px] w-16 rounded-full bg-yellow-500"></div>

                    <h2 class="font-display mt-6 text-4xl font-bold text-white sm:text-5xl">Kunjungi Ayo Renne</h2>

                    <p class="mx-auto mt-5 max-w-3xl text-lg leading-8 text-white/62">
                        Kami siap melayani Anda. Hubungi kami untuk reservasi atau pertanyaan lainnya.
                    </p>
                </div>

                <div class="mt-16 grid gap-10 xl:grid-cols-[1fr_1.02fr]">
                    <div class="space-y-4">
                        @php
                            $contacts = [
                                ['title' => 'Alamat', 'value' => 'Jl. Raya Probolinggo No. 123, Probolinggo, Jawa Timur 67219'],
                                ['title' => 'Telepon', 'value' => '+62 335 421 888'],
                                ['title' => 'Email', 'value' => 'info@ayorenne.com'],
                                ['title' => 'Jam Operasional', 'value' => "Senin - Jumat: 10:00 - 22:00 WIB\nSabtu - Minggu: 09:00 - 23:00 WIB"],
                            ];
                        @endphp

                        @foreach($contacts as $contact)
                            <div class="rounded-2xl border border-yellow-500/16 bg-[#171717] p-6">
                                <div class="flex gap-4">
                                    <div
                                        class="inline-flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-yellow-500/20 bg-yellow-500/6 text-yellow-500">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z"
                                                stroke="currentColor" stroke-width="1.8" />
                                            <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-semibold text-white">{{ $contact['title'] }}</div>
                                        <div class="mt-2 whitespace-pre-line text-lg leading-8 text-white/62">
                                            {{ $contact['value'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="overflow-hidden rounded-2xl border border-yellow-500/16">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.166898086913!2d113.4064848!3d-7.772120200000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd701aead3ea01d%3A0xdc54aa0c5c148fa8!2sAyo%20Renne%20Cafe%20%26%20Resto%20Probolinggo!5e0!3m2!1sid!2sid!4v1774899746526!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>

                        <div class="flex items-center justify-center gap-4 pt-2 xl:justify-start">
    <a href="#"
       class="inline-flex h-12 w-12 items-center justify-center rounded-xl border border-yellow-500/16 bg-[#171717] text-white/70 transition hover:border-yellow-500/35 hover:text-yellow-500"
       aria-label="Instagram">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="17.2" cy="6.8" r="1.1" fill="currentColor"/>
        </svg>
    </a>

    <a href="#"
       class="inline-flex h-12 w-12 items-center justify-center rounded-xl border border-yellow-500/16 bg-[#171717] text-white/70 transition hover:border-yellow-500/35 hover:text-yellow-500"
       aria-label="Facebook">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M14 8h2V4h-2.5C10.46 4 9 5.79 9 8.6V11H7v4h2v5h4v-5h2.5l.5-4H13V9c0-.6.28-1 1-1Z"
                fill="currentColor"/>
        </svg>
    </a>

    <a href="#"
       class="inline-flex h-12 w-12 items-center justify-center rounded-xl border border-yellow-500/16 bg-[#171717] text-white/70 transition hover:border-yellow-500/35 hover:text-yellow-500"
       aria-label="Telegram">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M21 4L3 11.53l5.7 2.07L18 7l-7.02 7.12v5.38l3.24-3.12 4.35 3.2c.8.44 1.38.21 1.58-.74L23 5.53C23.27 4.35 22.53 3.82 21 4Z"
                stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
        </svg>
    </a>
</div>
                    </div>

                    <div class="rounded-2xl border border-yellow-500/16 bg-[#171717] p-7 sm:p-8">
                        <h3 class="font-display text-3xl font-bold text-white">Kirim Pesan</h3>
                        <p class="mt-3 text-lg leading-8 text-white/62">
                            Isi form di bawah ini dan kami akan segera menghubungi Anda.
                        </p>

                        <form class="mt-8 space-y-6"
                            onsubmit="event.preventDefault(); alert('Form dummy: belum tersambung backend.');">
                            <div>
                                <label class="mb-3 block text-sm font-medium text-white/85">Nama Lengkap</label>
                                <input type="text" placeholder="Masukkan nama Anda"
                                    class="w-full rounded-xl border border-yellow-500/14 bg-black px-4 py-4 text-white outline-none transition placeholder:text-white/25 focus:border-yellow-500/40">
                            </div>

                            <div>
                                <label class="mb-3 block text-sm font-medium text-white/85">Email</label>
                                <input type="email" placeholder="nama@email.com"
                                    class="w-full rounded-xl border border-yellow-500/14 bg-black px-4 py-4 text-white outline-none transition placeholder:text-white/25 focus:border-yellow-500/40">
                            </div>

                            <div>
                                <label class="mb-3 block text-sm font-medium text-white/85">Nomor Telepon</label>
                                <input type="text" placeholder="+62 xxx xxxx xxxx"
                                    class="w-full rounded-xl border border-yellow-500/14 bg-black px-4 py-4 text-white outline-none transition placeholder:text-white/25 focus:border-yellow-500/40">
                            </div>

                            <div>
                                <label class="mb-3 block text-sm font-medium text-white/85">Pesan</label>
                                <textarea rows="5" placeholder="Tulis pesan Anda di sini..."
                                    class="w-full rounded-xl border border-yellow-500/14 bg-black px-4 py-4 text-white outline-none transition placeholder:text-white/25 focus:border-yellow-500/40"></textarea>
                            </div>

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-yellow-500 px-6 py-4 text-base font-semibold text-black transition hover:bg-yellow-400">
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-yellow-500/12 bg-[#050505] py-16">
        <div class="section-wrap grid gap-12 lg:grid-cols-[1.15fr_.8fr_.95fr_.8fr]">
            <div>
                <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne Logo"
                    class="h-16 w-auto object-contain">
                <p class="mt-6 max-w-sm text-lg leading-8 text-white/62">
                    Destinasi kuliner premium di Probolinggo yang menghadirkan pengalaman bersantap istimewa.
                </p>

                <div class="mt-6 flex gap-3">
    <a href="#"
       class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-yellow-500/16 bg-[#171717] text-white/70 transition hover:border-yellow-500/35 hover:text-yellow-500"
       aria-label="Instagram">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="17.2" cy="6.8" r="1.1" fill="currentColor"/>
        </svg>
    </a>

    <a href="#"
       class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-yellow-500/16 bg-[#171717] text-white/70 transition hover:border-yellow-500/35 hover:text-yellow-500"
       aria-label="Facebook">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M14 8h2V4h-2.5C10.46 4 9 5.79 9 8.6V11H7v4h2v5h4v-5h2.5l.5-4H13V9c0-.6.28-1 1-1Z"
                fill="currentColor"/>
        </svg>
    </a>

    <a href="#"
       class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-yellow-500/16 bg-[#171717] text-white/70 transition hover:border-yellow-500/35 hover:text-yellow-500"
       aria-label="Telegram">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M21 4L3 11.53l5.7 2.07L18 7l-7.02 7.12v5.38l3.24-3.12 4.35 3.2c.8.44 1.38.21 1.58-.74L23 5.53C23.27 4.35 22.53 3.82 21 4Z"
                stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
        </svg>
    </a>
</div>
            </div>

            <div>
                <h4 class="text-2xl font-semibold text-white">Navigasi</h4>
                <div class="mt-6 space-y-4 text-lg text-white/62">
                    <a href="#hero" class="block hover:text-yellow-500">Beranda</a>
                    <a href="#tentang" class="block hover:text-yellow-500">Tentang</a>
                    <a href="#menu" class="block hover:text-yellow-500">Menu</a>
                    <a href="#galeri" class="block hover:text-yellow-500">Galeri</a>
                    <a href="#kontak" class="block hover:text-yellow-500">Kontak</a>
                </div>
            </div>

            <div>
                <h4 class="text-2xl font-semibold text-white">Kontak</h4>
                <div class="mt-6 space-y-4 break-words text-lg leading-8 text-white/62">
                    <div>Jl. Raya Probolinggo No. 123,<br>Probolinggo, Jawa Timur 67219</div>
                    <div>+62 335 421 888</div>
                    <div>info@ayorenne.com</div>
                </div>
            </div>

            <div>
                <h4 class="text-2xl font-semibold text-white">Jam Buka</h4>
                <div class="mt-6 space-y-4 text-lg leading-8 text-white/62">
                    <div>
                        <div class="font-semibold text-white">Senin - Jumat</div>
                        <div>10:00 - 22:00 WIB</div>
                    </div>
                    <div>
                        <div class="font-semibold text-white">Sabtu - Minggu</div>
                        <div>09:00 - 23:00 WIB</div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="section-wrap mt-12 flex flex-col items-center justify-between gap-4 border-t border-yellow-500/12 pt-8 text-sm text-white/48 md:flex-row">
            <div>© 2026 Ayo Renne. All rights reserved.</div>
            <div>Made with 💛 in Probolinggo</div>
        </div>
    </footer>

    <script>
        const btn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (btn && mobileMenu) {
            btn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
</body>

</html>