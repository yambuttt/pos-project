<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coffe Shop</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-950 text-white antialiased">
    <div class="min-h-screen overflow-hidden">

        {{-- HERO / LANDING --}}
        <section id="landing" class="relative min-h-screen"
            style="background-image: url('/images/coffee-bg.jpg'); background-size: cover; background-position: center;">
            {{-- Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/55 to-black/20"></div>

            {{-- Content container: responsive --}}
            <div class="relative min-h-screen">
                <div class="mx-auto w-full max-w-6xl px-5 sm:px-8 lg:px-10">

                    {{-- top chips --}}
                    <div class="pt-6 flex items-center justify-between text-xs text-white/70">
                        <span class="rounded-full border border-white/20 px-3 py-1">Table QR Menu</span>
                        <span class="rounded-full border border-white/20 px-3 py-1">EN / ID</span>
                    </div>

                    {{-- Layout responsive:
                    - Mobile: bottom
                    - Desktop: center-left
                    --}}
                    <div class="min-h-[calc(100vh-120px)] flex items-end pb-10 md:items-center md:pb-0">
                        <div class="w-full max-w-md md:max-w-xl lg:max-w-2xl">

                            <p class="text-white/70 text-sm tracking-wide">Welcome to</p>

                            <h1 class="mt-1 text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight">
                                Coffe <span class="text-amber-400">Shop</span>
                            </h1>

                            <p class="mt-3 text-white/70 text-sm md:text-base leading-relaxed max-w-prose">
                                Scan, choose your drink, and checkout in seconds.
                                Modern ordering experience for your coffee break.
                            </p>

                            {{-- Buttons responsive --}}
                            <div class="mt-7 flex flex-col sm:flex-row gap-3 sm:items-center sm:max-w-lg">
                                <button id="startOrderBtn"
                                    class="w-full sm:w-auto sm:flex-1 rounded-xl bg-amber-400 px-6 py-3.5 text-sm font-semibold text-zinc-950 shadow-lg shadow-amber-400/20 active:scale-[0.99] hover:bg-amber-300 transition">
                                    Start Your Order
                                </button>

                                <a href="#"
                                    class="w-full sm:w-auto sm:flex-1 rounded-xl border border-white/15 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white/90 hover:bg-white/10 transition text-center">
                                    View Promotions
                                </a>
                            </div>

                            <p class="mt-3 text-left text-xs text-white/50">
                                By continuing, you agree to our terms & service.
                            </p>

                            {{-- Features (desktop/tablet) --}}
                            <div class="mt-8 hidden md:grid grid-cols-3 gap-3 max-w-xl">
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-xs text-white/60">Fast</p>
                                    <p class="mt-1 text-sm font-semibold">Order in 10s</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-xs text-white/60">Simple</p>
                                    <p class="mt-1 text-sm font-semibold">Scan & Choose</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-xs text-white/60">Cashless</p>
                                    <p class="mt-1 text-sm font-semibold">Easy Checkout</p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- MENU OVERLAY (CURTAIN EFFECT) --}}
        <section id="menu" data-state="closed" class="fixed inset-0 z-40 pointer-events-none">
            {{-- Curtain (tirai) --}}
            <div id="curtain"
                class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950 to-zinc-950/90 translate-y-full transition-transform duration-500 ease-[cubic-bezier(.2,.8,.2,1)]">
            </div>

            {{-- Menu content (muncul setelah curtain) --}}
            <div id="menuContent" class="relative h-full opacity-0 translate-y-4 transition-all duration-300 ease-out">
                <div class="absolute inset-0 bg-zinc-950"></div>

                {{-- ✅ SCROLL CONTAINER (fix HP scroll) --}}
                <div id="menuScroll" class="relative h-[100svh] overflow-y-auto overscroll-contain">
                    <div class="mx-auto w-full max-w-6xl px-5 sm:px-8 lg:px-10">

                        {{-- Header --}}
                        <div class="sticky top-0 z-30 bg-zinc-950/85 backdrop-blur border-b border-white/10">
                            <div class="flex items-center justify-between py-4">
                                <div>
                                    <p class="text-xs text-white/60">Coffe Shop</p>
                                    <h2 class="text-lg md:text-xl font-semibold">Menu</h2>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button id="openCartIconBtn"
                                        class="relative rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition"
                                        type="button">
                                        🛒
                                        <span id="cartBadge"
                                            class="absolute -top-2 -right-2 rounded-full bg-amber-400 px-2 py-0.5 text-[10px] font-bold text-zinc-950">
                                            0
                                        </span>
                                    </button>

                                    <button id="closeMenuBtn"
                                        class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition"
                                        aria-label="Tutup menu">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            {{-- Search + Category --}}
                            <div class="pb-4 grid gap-3 md:grid-cols-2 md:items-center">
                                <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                                    <span class="text-white/60">🔎</span>
                                    <input id="menuSearch"
                                        class="w-full bg-transparent text-sm outline-none placeholder:text-white/40"
                                        placeholder="Cari menu... (UI saja)" />
                                </div>

                                <div id="filterBar" class="flex gap-2 overflow-x-auto pb-1 md:justify-end">
                                    <button data-filter="popular"
                                        class="filter-btn shrink-0 rounded-full bg-amber-400 px-4 py-2 text-xs font-semibold text-zinc-950">
                                        Popular
                                    </button>
                                    <button data-filter="coffee"
                                        class="filter-btn shrink-0 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs text-white/80 hover:bg-white/10 transition">
                                        Coffee
                                    </button>
                                    <button data-filter="noncoffee"
                                        class="filter-btn shrink-0 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs text-white/80 hover:bg-white/10 transition">
                                        Non Coffee
                                    </button>
                                    <button data-filter="snack"
                                        class="filter-btn shrink-0 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs text-white/80 hover:bg-white/10 transition">
                                        Snack
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ Menu list: padding bawah besar + safe-area (biar item terakhir bisa ditekan) --}}
                        <div class="py-6 pb-[calc(190px+env(safe-area-inset-bottom))]">
                            <div id="menuGrid" class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">

                                {{-- CARD: klik kartu untuk modal (kecuali tombol +Tambah) --}}
                                <div class="menu-item group cursor-pointer rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition"
                                    data-category="coffee" data-popular="true" data-name="Americano" data-price="18000"
                                    data-desc="Bold espresso with hot water. Perfect for coffee lovers." data-icon="☕">
                                    <div class="flex items-start gap-4">
                                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-amber-400/30 to-white/5 flex items-center justify-center text-xl">
                                            ☕
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold">Americano</p>
                                                    <p class="mt-1 text-xs text-white/60">Bold espresso with hot water</p>
                                                </div>
                                                <p class="shrink-0 text-sm font-semibold text-amber-300">Rp 18.000</p>
                                            </div>

                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="text-xs text-white/50">Size: Regular</div>
                                                <button class="add-btn rounded-xl bg-amber-400 px-4 py-2 text-xs font-semibold text-zinc-950 hover:bg-amber-300 transition" type="button">
                                                    + Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="menu-item group cursor-pointer rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition"
                                    data-category="coffee" data-popular="true" data-name="Caffè Latte" data-price="25000"
                                    data-desc="Espresso with steamed milk. Smooth, creamy, and comforting." data-icon="🥛">
                                    <div class="flex items-start gap-4">
                                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-amber-400/30 to-white/5 flex items-center justify-center text-xl">🥛</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold">Caffè Latte</p>
                                                    <p class="mt-1 text-xs text-white/60">Espresso with steamed milk</p>
                                                </div>
                                                <p class="shrink-0 text-sm font-semibold text-amber-300">Rp 25.000</p>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="text-xs text-white/50">Size: Regular</div>
                                                <button class="add-btn rounded-xl bg-amber-400 px-4 py-2 text-xs font-semibold text-zinc-950 hover:bg-amber-300 transition" type="button">+ Tambah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="menu-item group cursor-pointer rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition"
                                    data-category="snack" data-popular="true" data-name="Butter Croissant" data-price="22000"
                                    data-desc="Flaky, buttery, fresh baked. Great with coffee." data-icon="🥐">
                                    <div class="flex items-start gap-4">
                                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-amber-400/30 to-white/5 flex items-center justify-center text-xl">🥐</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold">Butter Croissant</p>
                                                    <p class="mt-1 text-xs text-white/60">Flaky & buttery, fresh baked</p>
                                                </div>
                                                <p class="shrink-0 text-sm font-semibold text-amber-300">Rp 22.000</p>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="text-xs text-white/50">Snack</div>
                                                <button class="add-btn rounded-xl bg-amber-400 px-4 py-2 text-xs font-semibold text-zinc-950 hover:bg-amber-300 transition" type="button">+ Tambah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="menu-item group cursor-pointer rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition"
                                    data-category="noncoffee" data-popular="false" data-name="Iced Caramel" data-price="28000"
                                    data-desc="Sweet caramel with milk, served cold and refreshing." data-icon="🧋">
                                    <div class="flex items-start gap-4">
                                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-amber-400/30 to-white/5 flex items-center justify-center text-xl">🧋</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold">Iced Caramel</p>
                                                    <p class="mt-1 text-xs text-white/60">Sweet caramel with milk</p>
                                                </div>
                                                <p class="shrink-0 text-sm font-semibold text-amber-300">Rp 28.000</p>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="text-xs text-white/50">Size: Large</div>
                                                <button class="add-btn rounded-xl bg-amber-400 px-4 py-2 text-xs font-semibold text-zinc-950 hover:bg-amber-300 transition" type="button">+ Tambah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="menu-item group cursor-pointer rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition"
                                    data-category="noncoffee" data-popular="true" data-name="Matcha Latte" data-price="30000"
                                    data-desc="Creamy matcha & milk with a balanced earthy taste." data-icon="🍵">
                                    <div class="flex items-start gap-4">
                                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-amber-400/30 to-white/5 flex items-center justify-center text-xl">🍵</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold">Matcha Latte</p>
                                                    <p class="mt-1 text-xs text-white/60">Creamy matcha & milk</p>
                                                </div>
                                                <p class="shrink-0 text-sm font-semibold text-amber-300">Rp 30.000</p>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="text-xs text-white/50">Size: Regular</div>
                                                <button class="add-btn rounded-xl bg-amber-400 px-4 py-2 text-xs font-semibold text-zinc-950 hover:bg-amber-300 transition" type="button">+ Tambah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="menu-item group cursor-pointer rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition"
                                    data-category="snack" data-popular="false" data-name="Cheesecake" data-price="26000"
                                    data-desc="Soft & creamy dessert with a rich cheese flavor." data-icon="🍰">
                                    <div class="flex items-start gap-4">
                                        <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-amber-400/30 to-white/5 flex items-center justify-center text-xl">🍰</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold">Cheesecake</p>
                                                    <p class="mt-1 text-xs text-white/60">Soft & creamy dessert</p>
                                                </div>
                                                <p class="shrink-0 text-sm font-semibold text-amber-300">Rp 26.000</p>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="text-xs text-white/50">Dessert</div>
                                                <button class="add-btn rounded-xl bg-amber-400 px-4 py-2 text-xs font-semibold text-zinc-950 hover:bg-amber-300 transition" type="button">+ Tambah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="emptyState" class="hidden md:col-span-2 xl:col-span-3 rounded-2xl border border-white/10 bg-white/5 p-6 text-center">
                                    <p class="text-sm font-semibold">Menu tidak ditemukan</p>
                                    <p class="mt-1 text-xs text-white/60">Coba pilih kategori lain atau ubah pencarian.</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom bar --}}
                <div class="fixed bottom-0 left-0 right-0 z-40">
                    <div class="mx-auto w-full max-w-6xl px-5 sm:px-8 lg:px-10 pb-[max(16px,env(safe-area-inset-bottom))]">
                        <div class="rounded-2xl border border-white/10 bg-zinc-900/80 backdrop-blur p-4 shadow-xl">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="text-xs text-white/60">Cart (UI)</p>
                                    <p class="text-sm font-semibold">2 items • <span class="text-amber-300">Rp 43.000</span></p>
                                </div>
                                <button id="openCartBtn"
                                    class="w-full sm:w-auto rounded-xl bg-amber-400 px-5 py-3 text-xs font-semibold text-zinc-950 hover:bg-amber-300 transition"
                                    type="button">
                                    Lihat Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ✅ PRODUCT MODAL --}}
                <div id="productModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
                    <div id="modalBackdrop" class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>

                    <div class="relative h-full w-full flex items-end sm:items-center justify-center p-4">
                        <div id="modalPanel"
                            class="w-full sm:max-w-md rounded-3xl border border-white/10 bg-zinc-900 text-white shadow-2xl translate-y-6 sm:translate-y-0 sm:scale-95 opacity-0 transition-all duration-200">
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div id="modalIcon"
                                            class="h-12 w-12 rounded-2xl bg-gradient-to-br from-amber-400/30 to-white/5 flex items-center justify-center text-2xl">
                                            ☕
                                        </div>
                                        <div>
                                            <p id="modalName" class="text-lg font-semibold leading-tight">Americano</p>
                                            <p id="modalPrice" class="text-sm font-semibold text-amber-300">Rp 18.000</p>
                                        </div>
                                    </div>

                                    <button id="closeModalBtn"
                                        class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition"
                                        aria-label="Tutup detail" type="button">
                                        ✕
                                    </button>
                                </div>

                                <div class="mt-4">
                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                        <p class="text-xs text-white/60">Description</p>
                                        <p id="modalDesc" class="mt-1 text-sm leading-relaxed text-white/85">
                                            Bold espresso with hot water. Perfect for coffee lovers.
                                        </p>
                                    </div>

                                    <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center">
                                            <p class="text-white/60">Ice</p>
                                            <p class="mt-1 font-semibold">Normal</p>
                                        </div>
                                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center">
                                            <p class="text-white/60">Sugar</p>
                                            <p class="mt-1 font-semibold">Normal</p>
                                        </div>
                                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center">
                                            <p class="text-white/60">Size</p>
                                            <p class="mt-1 font-semibold">Reg</p>
                                        </div>
                                    </div>

                                    <button id="modalAddBtn"
                                        class="mt-4 w-full rounded-2xl bg-amber-400 px-5 py-3 text-sm font-semibold text-zinc-950 hover:bg-amber-300 transition"
                                        type="button">
                                        + Tambah ke Keranjang
                                    </button>

                                    <p class="mt-3 text-center text-xs text-white/50">
                                        UI only (belum ada backend)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ✅ CART DRAWER --}}
                <div id="cartDrawer" class="fixed inset-0 z-[60] hidden" aria-hidden="true">
                    <div id="cartBackdrop" class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>

                    <div class="absolute inset-0 flex items-end sm:items-stretch sm:justify-end p-3 sm:p-0">
                        <div id="cartPanel"
                            class="w-full sm:w-[420px] sm:h-full rounded-3xl sm:rounded-none sm:rounded-l-3xl border border-white/10 bg-zinc-950 text-white shadow-2xl
                            translate-y-6 sm:translate-y-0 sm:translate-x-8 opacity-0 transition-all duration-200">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-white/10 bg-zinc-950/80 backdrop-blur">
                                <div>
                                    <p class="text-xs text-white/60">Coffe Shop</p>
                                    <h3 class="text-lg font-semibold">Keranjang</h3>
                                </div>
                                <button id="closeCartBtn"
                                    class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition"
                                    type="button" aria-label="Tutup keranjang">
                                    ✕
                                </button>
                            </div>

                            <div class="px-5 py-4">
                                <div id="cartEmpty" class="hidden rounded-2xl border border-white/10 bg-white/5 p-5 text-center">
                                    <p class="text-sm font-semibold">Keranjang masih kosong</p>
                                    <p class="mt-1 text-xs text-white/60">Klik “+ Tambah” di menu untuk mulai pesan.</p>
                                </div>
                                <div id="cartItems" class="space-y-3"></div>
                            </div>

                            <div class="mt-auto px-5 pb-[max(16px,env(safe-area-inset-bottom))] pt-3 border-t border-white/10 bg-zinc-950/80 backdrop-blur">
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center justify-between text-white/80">
                                        <span>Subtotal</span>
                                        <span id="cartSubtotal">Rp 0</span>
                                    </div>
                                    <div class="flex items-center justify-between text-white/80">
                                        <span>Pajak (10%)</span>
                                        <span id="cartTax">Rp 0</span>
                                    </div>
                                    <div class="h-px bg-white/10 my-2"></div>
                                    <div class="flex items-center justify-between font-semibold">
                                        <span>Total</span>
                                        <span id="cartTotal" class="text-amber-300">Rp 0</span>
                                    </div>
                                </div>

                                <button id="checkoutBtn"
                                    class="mt-4 w-full rounded-2xl bg-amber-400 px-5 py-3 text-sm font-semibold text-zinc-950 hover:bg-amber-300 transition"
                                    type="button">
                                    Checkout
                                </button>

                                <p class="mt-3 text-center text-xs text-white/50">
                                    Prototype UI (pembayaran belum benar-benar diproses).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ✅ CHECKOUT OVERLAY --}}
                <div id="checkoutOverlay" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                    <div id="checkoutBackdrop" class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>

                    <div class="absolute inset-0 flex items-end sm:items-center justify-center p-3">
                        <div id="checkoutPanel"
                            class="w-full sm:max-w-lg rounded-3xl border border-white/10 bg-zinc-950 text-white shadow-2xl
                            translate-y-6 sm:scale-95 opacity-0 transition-all duration-200">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-white/10 bg-zinc-950/80 backdrop-blur rounded-t-3xl">
                                <div>
                                    <p class="text-xs text-white/60">Coffe Shop</p>
                                    <h3 class="text-lg font-semibold">Checkout</h3>
                                </div>
                                <button id="closeCheckoutBtn"
                                    class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition"
                                    type="button" aria-label="Tutup checkout">
                                    ✕
                                </button>
                            </div>

                            <div class="p-5 space-y-4">
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-xs text-white/60">Ringkasan</p>
                                    <div class="mt-2 space-y-2 text-sm">
                                        <div class="flex justify-between text-white/80">
                                            <span>Subtotal</span>
                                            <span id="coSubtotal">Rp 0</span>
                                        </div>
                                        <div class="flex justify-between text-white/80">
                                            <span>Pajak (10%)</span>
                                            <span id="coTax">Rp 0</span>
                                        </div>
                                        <div class="h-px bg-white/10 my-2"></div>
                                        <div class="flex justify-between font-semibold">
                                            <span>Total</span>
                                            <span id="coTotal" class="text-amber-300">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold">Metode Pembayaran</p>

                                    <div class="mt-3 grid gap-2">
                                        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition cursor-pointer">
                                            <input type="radio" name="payMethod" value="qris" class="accent-amber-400" checked>
                                            <div class="flex-1">
                                                <p class="font-semibold">QRIS</p>
                                                <p class="text-xs text-white/60">Scan QR untuk bayar (prototype)</p>
                                            </div>
                                            <span class="text-xl">📱</span>
                                        </label>

                                        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition cursor-pointer">
                                            <input type="radio" name="payMethod" value="va" class="accent-amber-400">
                                            <div class="flex-1">
                                                <p class="font-semibold">Virtual Account</p>
                                                <p class="text-xs text-white/60">BCA / BRI / Mandiri (prototype)</p>
                                            </div>
                                            <span class="text-xl">🏦</span>
                                        </label>

                                        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition cursor-pointer">
                                            <input type="radio" name="payMethod" value="cashier" class="accent-amber-400">
                                            <div class="flex-1">
                                                <p class="font-semibold">Bayar Tunai di Kasir</p>
                                                <p class="text-xs text-white/60">Bayar langsung saat ambil pesanan</p>
                                            </div>
                                            <span class="text-xl">💵</span>
                                        </label>
                                    </div>
                                </div>

                                <button id="payNowBtn"
                                    class="w-full rounded-2xl bg-amber-400 px-5 py-3 text-sm font-semibold text-zinc-950 hover:bg-amber-300 transition"
                                    type="button">
                                    Konfirmasi Metode Pembayaran
                                </button>

                                <p class="text-center text-xs text-white/50">
                                    Prototype UI: tombol ini belum memproses pembayaran.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ✅ INVOICE OVERLAY (RESPONSIVE FIX) --}}
                <div id="invoiceOverlay" class="fixed inset-0 z-[80] hidden" aria-hidden="true">
                    <div id="invoiceBackdrop" class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>

                    {{-- ✅ wrapper penting: biar panel ketengah & responsif --}}
                    <div class="absolute inset-0 flex items-end sm:items-center justify-center p-3">
                        <div id="invoicePanel"
                            class="w-full sm:max-w-3xl lg:max-w-5xl rounded-3xl border border-white/10 bg-zinc-950 text-white shadow-2xl
                            max-h-[100svh] overflow-hidden
                            translate-y-6 sm:scale-95 opacity-0 transition-all duration-200">

                            {{-- Header --}}
                            <div class="flex items-center justify-between px-5 py-4 border-b border-white/10 bg-zinc-950/80 backdrop-blur rounded-t-3xl">
                                <div>
                                    <p class="text-xs text-white/60">Coffe Shop</p>
                                    <h3 class="text-lg font-semibold">Invoice</h3>
                                    <p class="text-xs text-white/60 mt-1">Order ID:
                                        <span id="invOrderId" class="font-semibold text-white/80">CS-0001</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span id="invStatusBadge" class="rounded-full px-3 py-1 text-xs font-semibold border"></span>

                                    <button id="closeInvoiceBtn"
                                        class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition"
                                        type="button" aria-label="Tutup invoice">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            {{-- ✅ Scrollable body (header & footer tetap) --}}
                            <div class="p-5 overflow-y-auto max-h-[calc(100svh-160px)]">
                                <div class="grid gap-4 md:grid-cols-2">

                                    {{-- Left: Items --}}
                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                        <p class="text-sm font-semibold">Pesanan</p>
                                        <div id="invItems" class="mt-3 space-y-2"></div>

                                        <div class="mt-4 space-y-2 text-sm">
                                            <div class="flex justify-between text-white/80">
                                                <span>Subtotal</span>
                                                <span id="invSubtotal">Rp 0</span>
                                            </div>
                                            <div class="flex justify-between text-white/80">
                                                <span>Pajak (10%)</span>
                                                <span id="invTax">Rp 0</span>
                                            </div>
                                            <div class="h-px bg-white/10 my-2"></div>
                                            <div class="flex justify-between font-semibold">
                                                <span>Total</span>
                                                <span id="invTotal" class="text-amber-300">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Right: Payment instruction --}}
                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 md:self-start">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs text-white/60">Metode Pembayaran</p>
                                                <p id="invMethodTitle" class="text-sm font-semibold mt-1">QRIS</p>
                                            </div>
                                            <span id="invMethodIcon" class="text-2xl">📱</span>
                                        </div>

                                        <div id="invStatusTextWrap" class="mt-3 rounded-2xl border border-white/10 bg-zinc-950/40 p-4">
                                            <p id="invStatusTitle" class="text-sm font-semibold">Menunggu Pembayaran</p>
                                            <p id="invStatusDesc" class="mt-1 text-xs text-white/70">
                                                Silakan selesaikan pembayaran untuk melanjutkan pesanan.
                                            </p>
                                        </div>

                                        <div id="invMethodContent" class="mt-4 space-y-3"></div>

                                        <div class="mt-4 grid gap-2 grid-cols-1 sm:grid-cols-2">
                                            <button id="invCopyBtn"
                                                class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold hover:bg-white/10 transition"
                                                type="button">
                                                Salin Info
                                            </button>

                                            <button id="invMarkPaidBtn"
                                                class="rounded-2xl bg-amber-400 px-4 py-3 text-sm font-semibold text-zinc-950 hover:bg-amber-300 transition"
                                                type="button">
                                                Simulasi: Tandai Sudah Bayar
                                            </button>
                                        </div>

                                        <p class="mt-3 text-center text-xs text-white/50">
                                            Prototype UI: tombol “Simulasi” hanya untuk demo presentasi.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- ✅ Footer fixed inside panel (bukan ikut scroll) --}}
                            <div class="px-5 pb-[max(16px,env(safe-area-inset-bottom))] pt-4 border-t border-white/10 bg-zinc-950/80 backdrop-blur rounded-b-3xl">
                                <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between text-xs text-white/60">
                                    <span>Serahkan invoice ini ke kasir jika diperlukan.</span>
                                    <span id="invTime">Estimasi waktu: 10 menit</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>
</body>

</html>
