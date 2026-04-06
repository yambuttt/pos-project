<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ayo Renne — Pesan Menu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
        }

        @keyframes floaty {
            0% {
                transform: translate(-10px, 10px) scale(1);
            }

            50% {
                transform: translate(30px, -25px) scale(1.05);
            }

            100% {
                transform: translate(-10px, 10px) scale(1);
            }
        }

        @keyframes pulseSoft {

            0%,
            100% {
                transform: scale(1);
                opacity: .85;
            }

            50% {
                transform: scale(1.05);
                opacity: 1;
            }
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="min-h-screen bg-[#070708] text-white">
    {{-- Background --}}
    <div
        class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(250,204,21,.18),transparent_28%),radial-gradient(circle_at_top_right,rgba(255,255,255,.08),transparent_24%),linear-gradient(to_bottom,#070708,#0a0a0d)]">
    </div>
    <div class="pointer-events-none fixed -left-40 -top-40 -z-10 h-[420px] w-[420px] rounded-full bg-yellow-400/15 blur-[70px]"
        style="animation: floaty 14s ease-in-out infinite;"></div>
    <div class="pointer-events-none fixed right-[-120px] top-[80px] -z-10 h-[360px] w-[360px] rounded-full bg-white/10 blur-[80px]"
        style="animation: floaty 16s ease-in-out infinite;"></div>
    <div class="pointer-events-none fixed bottom-[-160px] left-[20%] -z-10 h-[320px] w-[320px] rounded-full bg-yellow-300/10 blur-[70px]"
        style="animation: floaty 13s ease-in-out infinite;"></div>

    <div class="mx-auto max-w-7xl px-4 pb-28 pt-5 sm:px-5 lg:px-6">
        {{-- Top Bar --}}
        <div class="mb-4 flex items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-yellow-300/25 bg-yellow-400/10 shadow-lg shadow-yellow-400/10 backdrop-blur-2xl">
                    <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne"
                        class="h-8 w-8 object-contain">
                </div>
                <div class="min-w-0">
                    <div class="truncate text-[11px] font-semibold uppercase tracking-[0.28em] text-yellow-200/95">Ayo
                        Renne</div>
                    <div class="truncate text-xs text-white/60">Pesan menu cepat, rapi, dan nyaman</div>
                </div>
            </div>

            <div
                class="hidden items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-3 py-2 text-xs text-white/75 backdrop-blur-2xl sm:inline-flex">
                <span class="h-2.5 w-2.5 rounded-full bg-yellow-400 shadow-[0_0_0_6px_rgba(250,204,21,0.12)]"
                    style="animation:pulseSoft 2.2s ease-in-out infinite;"></span>
                <span id="timeGreeting">Selamat datang</span>
            </div>
        </div>

        {{-- Hero --}}
        <section class="mb-5 grid grid-cols-1 gap-4 xl:grid-cols-[1.2fr_.8fr]">
            <div
                class="relative overflow-hidden rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-7">
                <div
                    class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(250,204,21,.16),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(255,255,255,.08),transparent_26%)]">
                </div>

                <div class="relative">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-yellow-300/20 bg-yellow-400/10 px-3 py-1 text-[11px] font-medium text-yellow-100/95">
                        <span class="h-2 w-2 rounded-full bg-yellow-300"></span>
                        Menu favorit tersedia hari ini
                    </div>

                    <h1
                        class="mt-4 max-w-3xl text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">
                        Pilih menu dengan
                        <span class="text-yellow-300">lebih cepat</span>,
                        lebih jelas, dan lebih nyaman.
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-white/68 sm:text-[15px]">
                        Cari menu favoritmu, pilih kategori, masukkan ke keranjang, lalu lanjut checkout.
                        Semua dibuat agar pelanggan lebih mudah memahami pilihan menu, baik di mobile maupun desktop.
                    </p>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <button type="button"
                            onclick="document.getElementById('menu-section').scrollIntoView({ behavior: 'smooth' })"
                            class="rounded-2xl bg-yellow-400 px-5 py-3 text-sm font-semibold text-black shadow-lg shadow-yellow-400/10 transition hover:bg-yellow-300">
                            Jelajahi Menu
                        </button>

                        <button type="button" onclick="openCart()"
                            class="rounded-2xl border border-white/12 bg-white/[0.05] px-5 py-3 text-sm font-semibold text-white/90 backdrop-blur-2xl transition hover:bg-white/[0.09]">
                            Buka Keranjang
                        </button>

                        <div class="text-xs text-white/48">
                            Tampilan baru: modern • elegan • responsif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-1">
                <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-[11px] uppercase tracking-[0.24em] text-white/55">Info Cepat</div>
                            <div class="mt-2 text-base font-semibold text-white/92">Pesan lebih mudah</div>
                        </div>
                        <span class="text-yellow-300">●</span>
                    </div>

                    <ul class="mt-4 space-y-3 text-sm leading-6 text-white/70">
                        <li>• Cari menu dengan kolom pencarian yang lebih jelas.</li>
                        <li>• Filter kategori lebih mudah disentuh di HP.</li>
                        <li>• Ringkasan keranjang selalu terlihat.</li>
                    </ul>
                </div>

                <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl">
                    <div class="text-[11px] uppercase tracking-[0.24em] text-white/55">Catatan</div>
                    <p class="mt-3 text-sm leading-6 text-white/68">
                        Jika tombol menu nonaktif, berarti stok bahan sedang tidak mencukupi.
                        Sistem akan menonaktifkan item yang tidak bisa dipesan.
                    </p>

                    <div
                        class="mt-4 inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/20 px-3 py-2 text-xs text-white/75">
                        <span class="h-2 w-2 rounded-full bg-yellow-400"></span>
                        <span id="menuCount">0 item</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Sticky Search / Filter --}}
        <section id="menu-section"
            class="rounded-[28px] border border-white/12 bg-white/[0.06] p-4 shadow-2xl backdrop-blur-2xl sm:p-5">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="text-[11px] uppercase tracking-[0.28em] text-white/55">Menu</div>
                        <div class="mt-1 text-lg font-semibold text-white/95">Pilih makanan & minuman favoritmu</div>
                    </div>

                    <div
                        class="inline-flex items-center gap-2 self-start rounded-full border border-white/10 bg-black/20 px-3 py-2 text-xs text-white/78">
                        <span
                            class="h-2.5 w-2.5 rounded-full bg-yellow-400 shadow-[0_0_0_6px_rgba(250,204,21,0.12)]"></span>
                        <span id="searchMeta">Semua menu ditampilkan</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_auto]">
                    <div
                        class="flex items-center gap-3 rounded-2xl border border-white/12 bg-black/20 px-4 py-3 backdrop-blur-2xl">
                        <svg class="h-5 w-5 shrink-0 text-white/45" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <input id="searchInput" type="text" placeholder="Cari menu... misalnya ayam, kopi, cireng"
                            class="w-full bg-transparent text-sm text-white placeholder:text-white/38 outline-none"
                            autocomplete="off">
                        <button id="clearSearch" type="button"
                            class="hidden rounded-xl border border-white/12 bg-white/[0.04] px-3 py-2 text-xs font-semibold text-white/75 hover:bg-white/[0.08]">
                            Clear
                        </button>
                    </div>

                    <button type="button" onclick="openCart()"
                        class="hidden rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/[0.09] lg:inline-flex lg:items-center lg:justify-center">
                        Lihat Keranjang
                    </button>
                </div>

                <div id="filters" class="hide-scrollbar flex gap-2 overflow-x-auto pb-1">
                    <button
                        class="pill active whitespace-nowrap rounded-full border border-yellow-300/30 bg-yellow-400/12 px-4 py-2 text-xs font-medium text-white shadow-[0_0_0_6px_rgba(250,204,21,0.08)]"
                        data-cat="__all" type="button">
                        Semua
                    </button>
                    @foreach($categories as $c)
                        <button
                            class="pill whitespace-nowrap rounded-full border border-white/12 bg-black/20 px-4 py-2 text-xs font-medium text-white/80 backdrop-blur-2xl transition hover:bg-white/[0.08]"
                            data-cat="{{ $c }}" type="button">
                            {{ $c }}
                        </button>
                    @endforeach
                </div>

                {{-- Menu Grid --}}
                <div id="menuGrid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($products as $p)
                        @php
                            $max = (int) $p->maxServingsFromStock();
                            $sellable = $max > 0;
                            $cat = trim((string) ($p->category ?? 'Lainnya'));
                            $img = $p->imageUrl();
                        @endphp

                        <article
                            class="group relative overflow-hidden rounded-[24px] border border-white/10 bg-[#121214]/85 p-4 shadow-xl transition duration-200 hover:-translate-y-[2px] hover:border-white/18 hover:bg-[#151518]"
                            data-cat="{{ $cat }}" data-id="{{ $p->id }}" data-name="{{ $p->name }}"
                            data-price="{{ (int) $p->price }}" data-desc="{{ e($p->description ?? '') }}"
                            data-img="{{ $img ? e($img) : '' }}" onclick="openProductModal({{ $p->id }})">

                            <div
                                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,.08),transparent_24%),radial-gradient(circle_at_bottom_right,rgba(250,204,21,.10),transparent_20%)] opacity-80">
                            </div>

                            <div class="relative flex h-full flex-col">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl border border-white/10 bg-black/30">
                                        @if($img)
                                            <img src="{{ $img }}" alt="{{ $p->name }}" class="h-full w-full object-cover"
                                                loading="lazy">
                                        @else
                                            <div
                                                class="h-full w-full bg-gradient-to-br from-yellow-400/15 via-white/5 to-transparent">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <h3 class="line-clamp-2 text-base font-semibold leading-6 text-white/95">
                                                    {{ $p->name }}
                                                </h3>
                                                <div
                                                    class="mt-1 inline-flex items-center rounded-full border border-white/10 bg-white/[0.04] px-2.5 py-1 text-[11px] text-white/62">
                                                    {{ $cat }}
                                                </div>
                                            </div>

                                            <div class="shrink-0 text-right">
                                                <div class="text-sm font-bold text-yellow-300">
                                                    Rp {{ number_format((int) $p->price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>

                                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-white/62">
                                            {{ $p->description ?: 'Belum ada deskripsi menu.' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <div class="text-xs text-white/45">
                                        @if($sellable)
                                            Siap dipesan
                                        @else
                                            Stok tidak tersedia
                                        @endif
                                    </div>

                                    <button type="button" onclick="event.stopPropagation(); addToCart({{ $p->id }});"
                                        @if(!$sellable) disabled @endif class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition
                                                    @if($sellable)
                                                        bg-yellow-400 text-black shadow-lg shadow-yellow-400/10 hover:bg-yellow-300
                                                    @else
                                                        cursor-not-allowed border border-white/10 bg-black/20 text-white/35
                                                    @endif">
                                        @if($sellable)
                                            + Tambah
                                        @else
                                            Habis
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    {{-- Floating cart --}}
    <button id="fabCart"
        class="fixed bottom-5 right-4 z-50 inline-flex items-center gap-3 rounded-full border border-white/12 bg-black/55 px-4 py-3 text-sm font-semibold text-white shadow-2xl backdrop-blur-2xl transition hover:bg-white/[0.08] sm:right-6"
        type="button" onclick="openCart()">
        <span class="hidden sm:inline">Keranjang</span>
        <span
            class="inline-flex min-w-[30px] items-center justify-center rounded-full bg-yellow-400 px-2.5 py-1 text-xs font-extrabold text-black"
            id="cartBadge">0</span>
    </button>

    {{-- Cart backdrop --}}
    <div id="cartBackdrop" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-[2px]" onclick="closeCart()">
    </div>

    {{-- Cart drawer --}}
    <aside id="cartDrawer"
        class="fixed right-0 top-0 z-50 flex h-full w-full max-w-[430px] translate-x-full flex-col border-l border-white/12 bg-[#0b0b0d]/95 p-4 shadow-2xl backdrop-blur-2xl transition-transform duration-200"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-[11px] uppercase tracking-[0.24em] text-white/55">Keranjang</div>
                <div class="mt-1 text-lg font-semibold text-white/95">Pesanan Kamu</div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button"
                    class="rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-semibold text-white/80 hover:bg-white/[0.08]"
                    onclick="clearCart()">
                    Clear
                </button>
                <button type="button"
                    class="rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-semibold text-white/80 hover:bg-white/[0.08]"
                    onclick="closeCart()">
                    Tutup
                </button>
            </div>
        </div>

        <div id="cartList" class="mt-4 flex-1 space-y-3 overflow-auto pr-1"></div>

        <div class="mt-4 rounded-2xl border border-white/10 bg-white/[0.04] p-4">
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between text-white/68">
                    <span>Subtotal</span>
                    <span id="subtotal" class="font-semibold text-white/95">Rp 0</span>
                </div>
                <div class="flex items-center justify-between text-white/68">
                    <span>Estimasi Pajak (11%)</span>
                    <span id="tax" class="font-semibold text-white/95">Rp 0</span>
                </div>
                <div class="flex items-center justify-between border-t border-white/10 pt-3 text-white/80">
                    <span class="font-medium">Total</span>
                    <span id="total" class="text-base font-bold text-yellow-300">Rp 0</span>
                </div>
            </div>

            <button type="button" onclick="goToOverview()"
                class="mt-4 w-full rounded-2xl bg-yellow-400 px-4 py-3 text-sm font-semibold text-black shadow-lg shadow-yellow-400/10 transition hover:bg-yellow-300">
                Lanjut Checkout
            </button>

            <p class="mt-2 text-xs leading-6 text-white/45">
                Kamu akan diarahkan ke halaman overview untuk mengisi detail pesanan.
            </p>
        </div>
    </aside>

    {{-- Product backdrop --}}
    <div id="productBackdrop" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-[2px]"
        onclick="closeProductModal()"></div>

    {{-- Product modal --}}
    <div id="productModal"
        class="fixed left-1/2 top-1/2 z-50 hidden w-[92%] max-w-[560px] -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-[28px] border border-white/12 bg-[#0b0b0d]/95 shadow-2xl backdrop-blur-2xl"
        role="dialog" aria-modal="true">
        <div class="relative">
            <div id="pmImageWrap" class="h-[240px] w-full bg-black/30">
                <img id="pmImage" class="hidden h-full w-full object-cover" alt="">
                <div id="pmNoImage"
                    class="h-full w-full bg-gradient-to-br from-yellow-400/20 via-white/5 to-transparent"></div>
            </div>

            <button type="button" onclick="closeProductModal()"
                class="absolute right-3 top-3 rounded-xl border border-white/12 bg-black/35 px-3 py-2 text-xs font-semibold text-white/90 backdrop-blur-2xl hover:bg-white/[0.08]">
                Tutup
            </button>
        </div>

        <div class="p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div id="pmName" class="text-xl font-semibold text-white/95">Nama Produk</div>
                    <div id="pmCategory"
                        class="mt-2 inline-flex rounded-full border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs text-white/60">
                        Kategori
                    </div>
                </div>

                <div id="pmPrice" class="shrink-0 text-lg font-bold text-yellow-300">Rp 0</div>
            </div>

            <p id="pmDesc" class="mt-4 text-sm leading-7 text-white/70"></p>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-xs text-white/45">
                    Tambahkan langsung ke keranjang atau tutup untuk kembali ke daftar menu.
                </div>
                <button id="pmAddBtn" type="button"
                    class="rounded-2xl bg-yellow-400 px-4 py-3 text-sm font-semibold text-black shadow-lg shadow-yellow-400/10 hover:bg-yellow-300 disabled:cursor-not-allowed disabled:opacity-40">
                    + Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile bottom helper --}}
    <div
        class="fixed bottom-0 left-0 right-0 z-40 border-t border-white/10 bg-black/65 px-4 py-3 backdrop-blur-2xl sm:hidden">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <div class="text-[11px] uppercase tracking-[0.18em] text-white/45">Keranjang</div>
                <div class="truncate text-sm font-semibold text-white/90">
                    <span id="mobileCartCount">0</span> item dipilih
                </div>
            </div>
            <button type="button" onclick="openCart()"
                class="rounded-2xl bg-yellow-400 px-4 py-2.5 text-sm font-semibold text-black">
                Lihat
            </button>
        </div>
    </div>

    <script>
        // ===== Greeting =====
        (function () {
            const h = new Date().getHours();
            let t = 'Selamat datang';
            if (h >= 4 && h < 11) t = 'Selamat pagi 👋';
            else if (h >= 11 && h < 15) t = 'Selamat siang 👋';
            else if (h >= 15 && h < 18) t = 'Selamat sore 👋';
            else t = 'Selamat malam 👋';

            const el = document.getElementById('timeGreeting');
            if (el) el.textContent = t;
        })();

        // ===== Menu count =====
        (function () {
            const n = document.querySelectorAll('#menuGrid [data-id]').length;
            const c = document.getElementById('menuCount');
            if (c) c.textContent = n + ' item tersedia';
        })();

        // ===== Search + Filter =====
        let activeCategory = '__all';

        function applyFilters() {
            const q = (document.getElementById('searchInput')?.value || '').trim().toLowerCase();
            const cards = Array.from(document.querySelectorAll('#menuGrid [data-id]'));

            let visible = 0;
            for (const card of cards) {
                const cat = (card.getAttribute('data-cat') || '').trim();
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const desc = (card.getAttribute('data-desc') || '').toLowerCase();

                const matchCat = activeCategory === '__all' || cat === activeCategory;
                const matchText = !q || name.includes(q) || desc.includes(q);

                const show = matchCat && matchText;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            }

            const meta = document.getElementById('searchMeta');
            if (meta) {
                if (!q && activeCategory === '__all') meta.textContent = 'Semua menu ditampilkan';
                else meta.textContent = `Menampilkan ${visible} item`;
            }

            const clearBtn = document.getElementById('clearSearch');
            if (clearBtn) clearBtn.classList.toggle('hidden', q.length === 0);
        }

        document.getElementById('filters')?.addEventListener('click', (e) => {
            const pill = e.target.closest('.pill');
            if (!pill) return;

            document.querySelectorAll('#filters .pill').forEach((p) => {
                p.classList.remove('active', 'border-yellow-300/30', 'bg-yellow-400/12', 'shadow-[0_0_0_6px_rgba(250,204,21,0.08)]');
                p.classList.add('border-white/12', 'bg-black/20');
            });

            pill.classList.add('active', 'border-yellow-300/30', 'bg-yellow-400/12', 'shadow-[0_0_0_6px_rgba(250,204,21,0.08)]');
            pill.classList.remove('border-white/12', 'bg-black/20');

            activeCategory = pill.getAttribute('data-cat') || '__all';
            applyFilters();
        });

        document.getElementById('searchInput')?.addEventListener('input', applyFilters);
        document.getElementById('clearSearch')?.addEventListener('click', () => {
            const input = document.getElementById('searchInput');
            if (input) input.value = '';
            applyFilters();
        });

        // ===== Storage Keys =====
        const CART_QTY_KEY = 'ayo_renne_cart_v1';
        const CART_OVERVIEW_KEY = 'ayo_renne_order_overview_v1';
        const TABLE_TOKEN_KEY = 'ayo_renne_table_token_v1';

        // ===== Capture table token =====
        (function captureTableTokenFromUrl() {
            const u = new URL(window.location.href);
            const token = (u.searchParams.get('table') || '').trim();

            if (token) {
                localStorage.setItem(TABLE_TOKEN_KEY, token);
                u.searchParams.delete('table');
                window.history.replaceState({}, '', u.toString());
            }
        })();

        function loadQtyCart() {
            let raw = {};
            try { raw = JSON.parse(localStorage.getItem(CART_QTY_KEY) || '{}') || {}; }
            catch { raw = {}; }

            const qtyCart = {};
            const overviewCart = {};
            let foundDetail = false;

            for (const [id, v] of Object.entries(raw)) {
                if (typeof v === 'number') {
                    qtyCart[id] = v;
                } else if (v && typeof v === 'object') {
                    foundDetail = true;
                    qtyCart[id] = Number(v.qty) || 0;
                    overviewCart[id] = {
                        name: String(v.name || ''),
                        price: Number(v.price) || 0,
                        qty: Number(v.qty) || 0,
                    };
                }
            }

            if (foundDetail) {
                localStorage.setItem(CART_QTY_KEY, JSON.stringify(qtyCart));
                localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify(overviewCart));
            }

            return qtyCart;
        }

        function saveQtyCart(cart) {
            localStorage.setItem(CART_QTY_KEY, JSON.stringify(cart));
        }

        function cartItemCount(cart) {
            return Object.values(cart).reduce((a, b) => a + (Number(b) || 0), 0);
        }

        function updateCartBadge() {
            const count = cartItemCount(loadQtyCart());

            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = String(count);

            const mobile = document.getElementById('mobileCartCount');
            if (mobile) mobile.textContent = String(count);
        }

        function formatRp(n) {
            n = Math.round(Number(n) || 0);
            return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function getProductData(id) {
            const el = document.querySelector(`#menuGrid [data-id="${id}"]`);
            if (!el) return null;
            return {
                id: Number(id),
                name: el.getAttribute('data-name'),
                price: Number(el.getAttribute('data-price') || 0),
            };
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function computeTotals(qtyCart) {
            let subtotal = 0;
            for (const [id, qty] of Object.entries(qtyCart)) {
                const p = getProductData(id);
                if (!p) continue;
                subtotal += p.price * (Number(qty) || 0);
            }
            const tax = Math.round(subtotal * 0.11);
            const total = subtotal + tax;
            return { subtotal, tax, total };
        }

        function addToCart(id) {
            const p = getProductData(id);
            if (!p) return;

            const cart = loadQtyCart();
            cart[id] = (cart[id] || 0) + 1;
            saveQtyCart(cart);
            renderCart();
        }

        function setQty(id, qty) {
            const cart = loadQtyCart();
            qty = Number(qty) || 0;

            if (qty <= 0) delete cart[id];
            else cart[id] = qty;

            saveQtyCart(cart);
            renderCart();
        }

        function clearCart() {
            saveQtyCart({});
            localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify({}));
            renderCart();
        }

        // ===== Drawer =====
        function openCart() {
            const backdrop = document.getElementById('cartBackdrop');
            const drawer = document.getElementById('cartDrawer');
            if (!backdrop || !drawer) return;

            backdrop.classList.remove('hidden');
            drawer.classList.remove('translate-x-full');
            renderCart();
        }

        function closeCart() {
            const backdrop = document.getElementById('cartBackdrop');
            const drawer = document.getElementById('cartDrawer');
            if (!backdrop || !drawer) return;

            drawer.classList.add('translate-x-full');
            setTimeout(() => backdrop.classList.add('hidden'), 180);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeCart();
                closeProductModal();
            }
        });

        function renderCart() {
            const cart = loadQtyCart();
            const list = document.getElementById('cartList');
            if (!list) return;

            list.innerHTML = '';
            const entries = Object.entries(cart);

            if (entries.length === 0) {
                list.innerHTML = `
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-5 text-center text-sm text-white/60">
                        Keranjang masih kosong. Yuk pilih menu dulu 🙂
                    </div>
                `;
            } else {
                for (const [id, qty] of entries) {
                    const p = getProductData(id);
                    if (!p) continue;

                    const item = document.createElement('div');
                    item.className = 'flex items-start justify-between gap-3 rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3';
                    item.innerHTML = `
    <div class="min-w-0 flex-1">
        <div class="truncate text-sm font-semibold text-white/92">${escapeHtml(p.name)}</div>

        <div class="mt-2 space-y-1 text-xs text-white/60">
            <div class="flex items-center justify-between gap-3">
                <span>Harga satuan</span>
                <span class="font-medium text-white/82">${formatRp(p.price)}</span>
            </div>

            <div class="flex items-center justify-between gap-3">
                <span>Qty</span>
                <span class="font-medium text-white/82">${qty}</span>
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-white/8 pt-2">
                <span class="text-white/72">Subtotal item</span>
                <span class="font-semibold text-yellow-300">${formatRp(p.price * qty)}</span>
            </div>
        </div>
    </div>

    <div class="ml-3 flex items-center gap-2 self-start">
        <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/[0.08]"
                onclick="setQty(${p.id}, ${Number(qty) - 1})" type="button">−</button>
        <div class="min-w-[22px] text-center text-sm font-bold text-white/95">${qty}</div>
        <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/[0.08]"
                onclick="setQty(${p.id}, ${Number(qty) + 1})" type="button">+</button>
    </div>
`;
                    list.appendChild(item);
                }
            }

            const t = computeTotals(cart);
            document.getElementById('subtotal').textContent = formatRp(t.subtotal);
            document.getElementById('tax').textContent = formatRp(t.tax);
            document.getElementById('total').textContent = formatRp(t.total);

            updateCartBadge();
        }

        function goToOverview() {
            const qtyCart = loadQtyCart();
            const entries = Object.entries(qtyCart);
            if (entries.length === 0) {
                alert('Keranjang masih kosong.');
                return;
            }

            const overview = {};
            for (const [id, qty] of entries) {
                const p = getProductData(id);
                if (!p) continue;
                overview[id] = {
                    name: p.name,
                    price: p.price,
                    qty: Number(qty) || 0
                };
            }

            localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify(overview));

            const token = (localStorage.getItem(TABLE_TOKEN_KEY) || '').trim();
            const url = token
                ? ('/order/overview?table=' + encodeURIComponent(token))
                : '/order/overview';

            window.location.href = url;
        }

        // ===== Product Modal =====
        function openProductModal(id) {
            const el = document.querySelector(`#menuGrid [data-id="${id}"]`);
            if (!el) return;

            const name = el.getAttribute('data-name') || '';
            const cat = el.getAttribute('data-cat') || '';
            const price = Number(el.getAttribute('data-price') || 0);
            const desc = el.getAttribute('data-desc') || '';
            const img = el.getAttribute('data-img') || '';

            document.getElementById('pmName').textContent = name;
            document.getElementById('pmCategory').textContent = cat;
            document.getElementById('pmPrice').textContent = formatRp(price);
            document.getElementById('pmDesc').textContent = desc.trim() ? desc : 'Belum ada deskripsi menu.';

            const imgEl = document.getElementById('pmImage');
            const noImg = document.getElementById('pmNoImage');

            if (img) {
                imgEl.src = img;
                imgEl.alt = name;
                imgEl.classList.remove('hidden');
                noImg.classList.add('hidden');
            } else {
                imgEl.src = '';
                imgEl.classList.add('hidden');
                noImg.classList.remove('hidden');
            }

            const cardAddBtn = el.querySelector('button[onclick*="addToCart"]');
            const sellable = cardAddBtn ? !cardAddBtn.disabled : true;

            const pmBtn = document.getElementById('pmAddBtn');
            pmBtn.disabled = !sellable;
            pmBtn.onclick = () => addToCart(id);

            document.getElementById('productBackdrop').classList.remove('hidden');
            document.getElementById('productModal').classList.remove('hidden');
        }

        function closeProductModal() {
            document.getElementById('productBackdrop')?.classList.add('hidden');
            document.getElementById('productModal')?.classList.add('hidden');
        }

        // init
        applyFilters();
        renderCart();
    </script>
</body>

</html>