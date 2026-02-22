<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ayo Renne — Pesan Menu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- hanya animasi liquid --}}
    <style>
        @keyframes floaty {
            0% { transform: translate(-10px, 10px) scale(1); }
            50% { transform: translate(30px, -25px) scale(1.05); }
            100% { transform: translate(-10px, 10px) scale(1); }
        }

        @keyframes sheen {
            0% { transform: translateX(-6%) rotate(10deg); }
            50% { transform: translateX(6%) rotate(10deg); }
            100% { transform: translateX(-6%) rotate(10deg); }
        }
    </style>
</head>

<body class="min-h-screen text-white">
    <!-- Base background -->
    <div class="fixed inset-0 -z-10 bg-gradient-to-b from-[#070708] to-[#0b0b0d]"></div>

    <!-- Liquid blobs -->
    <div class="pointer-events-none fixed -left-36 -top-40 -z-10 h-[520px] w-[520px] rounded-full bg-yellow-400/25 blur-[60px]"
        style="animation: floaty 12s ease-in-out infinite;"></div>
    <div class="pointer-events-none fixed -right-52 top-28 -z-10 h-[620px] w-[620px] rounded-full bg-white/15 blur-[70px]"
        style="animation: floaty 16s ease-in-out infinite;"></div>
    <div class="pointer-events-none fixed left-24 -bottom-56 -z-10 h-[480px] w-[480px] rounded-full bg-yellow-300/20 blur-[65px]"
        style="animation: floaty 14s ease-in-out infinite;"></div>

    <div class="mx-auto max-w-6xl px-4 pb-24 pt-7">
        <!-- Topbar -->
        <div class="mb-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-2xl border border-yellow-300/30 bg-yellow-400/20 shadow-lg shadow-yellow-400/10 backdrop-blur-2xl"></div>
                <div>
                    <div class="text-xs tracking-[0.22em] uppercase text-white/85">Ayo Renne</div>
                    <div class="text-xs text-white/60">Pesan menu langsung dari aplikasi</div>
                </div>
            </div>

            <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/20 px-3 py-2 text-xs text-white/85 backdrop-blur-2xl">
                <span class="h-2 w-2 rounded-full bg-yellow-400 shadow-[0_0_0_6px_rgba(250,204,21,0.12)]"></span>
                <span id="timeGreeting">Selamat datang</span>
            </div>
        </div>

        <!-- Hero -->
        <div class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-[1.35fr_.65fr]">
            <div class="relative overflow-hidden rounded-[26px] border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur-2xl">
                <!-- sheen overlay -->
                <div class="pointer-events-none absolute -inset-[40%] opacity-70" style="background:
                    radial-gradient(circle at 20% 30%, rgba(255,255,255,.20), transparent 45%),
                    radial-gradient(circle at 70% 55%, rgba(250,204,21,.22), transparent 50%),
                    linear-gradient(120deg, transparent, rgba(255,255,255,.08), transparent);
                    transform: rotate(10deg);
                    animation: sheen 8s ease-in-out infinite;">
                </div>

                <div class="relative">
                    <h1 class="mb-2 text-3xl font-semibold tracking-tight sm:text-4xl">
                        <span class="text-yellow-300 drop-shadow-[0_18px_50px_rgba(250,204,21,0.16)]">Halo!</span>
                        Mau pesan apa hari ini?
                    </h1>

                    <p class="max-w-[62ch] text-sm leading-relaxed text-white/75">
                        Pilih menu favorit kamu, masukin ke keranjang, lalu checkout. Tampilan modern & elegan dengan
                        efek <i>liquid glass</i>.
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <button
                            class="rounded-xl bg-yellow-400/95 px-4 py-2 text-sm font-semibold text-black shadow-lg shadow-yellow-400/10 hover:bg-yellow-300"
                            onclick="document.getElementById('menu').scrollIntoView({behavior:'smooth'})" type="button">
                            Lihat Menu
                        </button>

                        <button
                            class="rounded-xl border border-white/15 bg-black/20 px-4 py-2 text-sm font-semibold text-white/90 backdrop-blur-2xl hover:bg-white/10"
                            onclick="openCart()" type="button">
                            Buka Keranjang
                        </button>

                        <span class="ml-1 text-xs text-white/55">Warna: hitam × kuning • Style: modern elegant</span>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur-2xl">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs tracking-[0.18em] uppercase text-white/75">Info Cepat</div>
                        <div class="mt-2 text-sm text-white/70">
                            Order kamu akan dirangkum sebelum checkout.
                        </div>
                    </div>
                    <span class="text-yellow-300">●</span>
                </div>

                <p class="mt-4 text-xs leading-relaxed text-white/55">
                    Tip: kalau ada menu yang tombolnya nonaktif, berarti stok bahan sedang tidak mencukupi (stock-based).
                </p>
            </div>
        </div>

        <section id="menu" class="rounded-[24px] border border-white/15 bg-white/10 p-4 shadow-2xl backdrop-blur-2xl">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="text-xs tracking-[0.22em] uppercase text-white/80">Menu</h2>
                <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/20 px-3 py-2 text-xs text-white/80 backdrop-blur-2xl">
                    <span class="h-2 w-2 rounded-full bg-yellow-400 shadow-[0_0_0_6px_rgba(250,204,21,0.12)]"></span>
                    <span id="menuCount">0 item</span>
                </div>
            </div>

            <!-- filters -->
            <div class="mb-3 flex flex-wrap gap-2" id="filters">
                <button
                    class="pill active rounded-full border border-yellow-300/30 bg-yellow-400/10 px-3 py-2 text-xs text-white/90 shadow-[0_0_0_6px_rgba(250,204,21,0.08)]"
                    data-cat="__all" type="button">Semua</button>
                @foreach($categories as $c)
                    <button
                        class="pill rounded-full border border-white/15 bg-black/20 px-3 py-2 text-xs text-white/80 backdrop-blur-2xl hover:bg-white/10"
                        data-cat="{{ $c }}" type="button">{{ $c }}</button>
                @endforeach
            </div>

            <!-- Search -->
            <div class="mb-3">
                <div class="flex items-center gap-2 rounded-2xl border border-white/12 bg-black/20 px-4 py-3 backdrop-blur-2xl">
                    <svg class="h-5 w-5 text-white/50" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <input id="searchInput" type="text" placeholder="Cari menu... (contoh: bakso, cendol)"
                        class="w-full bg-transparent text-sm text-white/90 placeholder:text-white/40 outline-none"
                        autocomplete="off" />
                    <button id="clearSearch" type="button"
                        class="hidden rounded-xl border border-white/12 bg-black/20 px-3 py-2 text-xs font-semibold text-white/75 hover:bg-white/10">
                        Clear
                    </button>
                </div>

                <div id="searchMeta" class="mt-2 text-xs text-white/55"></div>
            </div>

            <!-- products grid -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3" id="menuGrid">
                @foreach($products as $p)
                    @php
                        $max = (int) $p->maxServingsFromStock();
                        $sellable = $max > 0;
                        $cat = trim((string)($p->category ?? 'Lainnya'));
                        $img = $p->imageUrl();
                    @endphp

                    <div
                        class="group relative overflow-hidden rounded-[18px] border border-white/12 bg-white/5 p-4 backdrop-blur-2xl hover:border-white/20 hover:bg-white/10 transition cursor-pointer"
                        data-cat="{{ $cat }}"
                        data-id="{{ $p->id }}"
                        data-name="{{ $p->name }}"
                        data-price="{{ (int)$p->price }}"
                        data-desc="{{ e($p->description ?? '') }}"
                        data-img="{{ $img ? e($img) : '' }}"
                        onclick="openProductModal({{ $p->id }})"
                    >
                        <div class="pointer-events-none absolute -inset-24 opacity-60"
                            style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,.14), transparent 55%);">
                        </div>

                        <div class="relative flex gap-3">
                            {{-- IMAGE --}}
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl border border-white/12 bg-black/30">
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $p->name }}" class="h-full w-full object-cover" loading="lazy">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-yellow-400/20 via-white/5 to-transparent"></div>
                                @endif
                            </div>

                            {{-- INFO --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-semibold text-white/95">{{ $p->name }}</div>
                                        <div class="mt-1 text-xs text-white/55">{{ $cat }}</div>
                                    </div>
                                    <div class="shrink-0 text-sm font-bold text-yellow-300">
                                        Rp {{ number_format((int)$p->price, 0, ',', '.') }}
                                    </div>
                                </div>

                                {{-- DESCRIPTION --}}
                                <div class="mt-2 line-clamp-2 text-xs leading-relaxed text-white/65">
                                    {{ $p->description ?: '—' }}
                                </div>

                                <div class="mt-3 flex items-center justify-end">
                                    <button
                                        class="rounded-xl border border-yellow-300/30 bg-yellow-400/10 px-3 py-2 text-xs font-semibold text-white/90 hover:bg-yellow-400/15 disabled:cursor-not-allowed disabled:opacity-40 disabled:border-white/10 disabled:bg-black/20"
                                        onclick="event.stopPropagation(); addToCart({{ $p->id }});"
                                        @if(!$sellable) disabled @endif
                                        type="button"
                                    >
                                        + Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <!-- FLOATING CART BUTTON -->
    <button id="fabCart"
        class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-3 rounded-full border border-white/15 bg-black/40 px-4 py-3 text-sm font-semibold text-white/90 shadow-2xl backdrop-blur-2xl hover:bg-white/10"
        type="button" onclick="openCart()">
        <span class="h-2.5 w-2.5 rounded-full bg-yellow-400 shadow-[0_0_0_6px_rgba(250,204,21,0.12)]"></span>
        <span>Keranjang</span>
        <span id="cartBadge"
            class="ml-1 inline-flex min-w-[28px] items-center justify-center rounded-full bg-yellow-400/90 px-2 py-1 text-xs font-extrabold text-black">0</span>
    </button>

    <!-- CART BACKDROP -->
    <div id="cartBackdrop" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-[2px]" onclick="closeCart()"></div>

    <!-- CART DRAWER -->
    <div id="cartDrawer"
        class="fixed right-0 top-0 z-50 h-full w-full max-w-[420px] translate-x-full border-l border-white/15 bg-black/50 p-4 shadow-2xl backdrop-blur-2xl transition-transform duration-200"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xs tracking-[0.22em] uppercase text-white/80">Keranjang</h2>
            <div class="flex items-center gap-2">
                <button
                    class="rounded-xl border border-white/15 bg-black/20 px-3 py-2 text-xs font-semibold text-white/85 backdrop-blur-2xl hover:bg-white/10"
                    onclick="clearCart()" type="button">Clear</button>
                <button
                    class="rounded-xl border border-white/15 bg-black/20 px-3 py-2 text-xs font-semibold text-white/85 backdrop-blur-2xl hover:bg-white/10"
                    onclick="closeCart()" type="button">Tutup</button>
            </div>
        </div>

        <div id="cartList" class="mt-3 flex max-h-[340px] flex-col gap-2 overflow-auto pr-1"></div>

        <div class="mt-3 space-y-2 border-t border-white/10 pt-3 text-sm">
            <div class="flex items-center justify-between text-white/75">
                <span>Subtotal</span>
                <span class="font-semibold text-white/90" id="subtotal">Rp 0</span>
            </div>
            <div class="flex items-center justify-between text-white/75">
                <span>Estimasi Pajak (11%)</span>
                <span class="font-semibold text-white/90" id="tax">Rp 0</span>
            </div>
            <div class="flex items-center justify-between text-white/75">
                <span>Total</span>
                <span class="font-semibold text-white/95" id="total">Rp 0</span>
            </div>
        </div>

        <button
            class="mt-4 w-full rounded-xl bg-yellow-400/95 px-4 py-3 text-sm font-semibold text-black shadow-lg shadow-yellow-400/10 hover:bg-yellow-300"
            onclick="goToOverview()" type="button">
            Checkout
        </button>

        <p class="mt-2 text-xs leading-relaxed text-white/50">
            Lanjutkan ke halaman overview untuk isi keterangan.
        </p>
    </div>

    <!-- PRODUCT MODAL BACKDROP -->
    <div id="productBackdrop" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-[2px]" onclick="closeProductModal()"></div>

    <!-- PRODUCT MODAL -->
    <div id="productModal"
        class="fixed left-1/2 top-1/2 z-50 hidden w-[92%] max-w-[520px] -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-[26px] border border-white/15 bg-black/55 shadow-2xl backdrop-blur-2xl"
        role="dialog" aria-modal="true">
        <div class="relative">
            <div id="pmImageWrap" class="h-[220px] w-full bg-black/30">
                <img id="pmImage" class="hidden h-full w-full object-cover" alt="">
                <div id="pmNoImage" class="h-full w-full bg-gradient-to-br from-yellow-400/20 via-white/5 to-transparent"></div>
            </div>

            <button type="button"
                class="absolute right-3 top-3 rounded-xl border border-white/15 bg-black/30 px-3 py-2 text-xs font-semibold text-white/90 backdrop-blur-2xl hover:bg-white/10"
                onclick="closeProductModal()">Tutup</button>
        </div>

        <div class="p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div id="pmName" class="truncate text-lg font-semibold text-white/95">Nama Produk</div>
                    <div id="pmCategory" class="mt-1 text-xs text-white/55">Kategori</div>
                </div>
                <div id="pmPrice" class="shrink-0 text-base font-extrabold text-yellow-300">Rp 0</div>
            </div>

            <p id="pmDesc" class="mt-3 text-sm leading-relaxed text-white/75"></p>

            <div class="mt-5 flex items-center justify-between gap-3">
                <div class="text-xs text-white/50">Kamu bisa add dari sini atau langsung dari card.</div>
                <button id="pmAddBtn"
                    class="rounded-xl bg-yellow-400/95 px-4 py-2 text-sm font-semibold text-black shadow-lg shadow-yellow-400/10 hover:bg-yellow-300 disabled:opacity-40 disabled:cursor-not-allowed"
                    type="button">
                    + Add ke Keranjang
                </button>
            </div>
        </div>
    </div>

    <script>
        // ===== Greeting by time =====
        (function () {
            const h = new Date().getHours();
            let t = "Selamat datang";
            if (h >= 4 && h < 11) t = "Selamat pagi 👋";
            else if (h >= 11 && h < 15) t = "Selamat siang 👋";
            else if (h >= 15 && h < 18) t = "Selamat sore 👋";
            else t = "Selamat malam 👋";
            document.getElementById('timeGreeting').textContent = t;
        })();

        // ===== Menu count =====
        (function () {
            const n = document.querySelectorAll('#menuGrid [data-id]').length;
            document.getElementById('menuCount').textContent = n + " item";
        })();

        // ===== Search + Category filter (sinkron) =====
        let activeCategory = '__all';

        function applyFilters() {
            const q = (document.getElementById('searchInput')?.value || '').trim().toLowerCase();
            const cards = Array.from(document.querySelectorAll('#menuGrid [data-id]'));

            let visible = 0;
            for (const card of cards) {
                const cat = (card.getAttribute('data-cat') || '').trim();
                const name = (card.getAttribute('data-name') || '').toLowerCase();

                const matchCat = (activeCategory === '__all') || (cat === activeCategory);
                const matchText = !q || name.includes(q);

                const show = matchCat && matchText;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            }

            const meta = document.getElementById('searchMeta');
            if (meta) {
                if (!q && activeCategory === '__all') meta.textContent = '';
                else meta.textContent = `Menampilkan ${visible} item`;
            }

            const clearBtn = document.getElementById('clearSearch');
            if (clearBtn) clearBtn.classList.toggle('hidden', q.length === 0);
        }

        document.getElementById('filters').addEventListener('click', (e) => {
            const pill = e.target.closest('.pill');
            if (!pill) return;

            document.querySelectorAll('#filters .pill').forEach(p => {
                p.classList.remove('active', 'border-yellow-300/30', 'bg-yellow-400/10', 'shadow-[0_0_0_6px_rgba(250,204,21,0.08)]');
                p.classList.add('border-white/15', 'bg-black/20');
            });

            pill.classList.add('active', 'border-yellow-300/30', 'bg-yellow-400/10', 'shadow-[0_0_0_6px_rgba(250,204,21,0.08)]');
            pill.classList.remove('border-white/15', 'bg-black/20');

            activeCategory = pill.getAttribute('data-cat') || '__all';
            applyFilters();
        });

        document.getElementById('searchInput')?.addEventListener('input', applyFilters);
        document.getElementById('clearSearch')?.addEventListener('click', () => {
            const input = document.getElementById('searchInput');
            if (input) input.value = '';
            applyFilters();
        });

        // ===== Table token (QR) =====
const TABLE_TOKEN_KEY = 'ayo_renne_table_token_v1';

(function captureTableTokenFromUrl(){
  const u = new URL(window.location.href);
  const token = (u.searchParams.get('table') || '').trim();

  if (token) {
    localStorage.setItem(TABLE_TOKEN_KEY, token);

    // (opsional) rapihin URL supaya param hilang (biar enak dilihat)
    u.searchParams.delete('table');
    window.history.replaceState({}, '', u.toString());
  }
})();

        // ===== Cart storage keys (fix [object Object]) =====
        const CART_QTY_KEY = 'ayo_renne_cart_v1';                 // qty map {id: number}
        const CART_OVERVIEW_KEY = 'ayo_renne_order_overview_v1';  // detail {id: {name, price, qty}}

        // Auto-migrate jika CART_QTY_KEY terlanjur berisi object detail
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

        // ===== Drawer controls =====
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
                    <div class="rounded-2xl border border-white/10 bg-black/20 px-4 py-4 text-center text-sm text-white/65 backdrop-blur-2xl">
                        Keranjang kosong. Tambahkan menu dulu ya 🙂
                    </div>`;
            } else {
                for (const [id, qty] of entries) {
                    const p = getProductData(id);
                    if (!p) continue;

                    const item = document.createElement('div');
                    item.className = "flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-black/20 px-4 py-3 backdrop-blur-2xl";
                    item.innerHTML = `
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-white/90">${escapeHtml(p.name)}</div>
                            <div class="mt-1 text-xs text-white/55">${formatRp(p.price)} × ${qty}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/10"
                                    onclick="setQty(${p.id}, ${Number(qty) - 1})" type="button">−</button>
                            <div class="min-w-[20px] text-center text-sm font-bold text-white/95">${qty}</div>
                            <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/10"
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

        // ===== Checkout -> Overview =====
        function goToOverview() {
            const qtyCart = loadQtyCart();
            const entries = Object.entries(qtyCart);
            if (entries.length === 0) { alert('Keranjang masih kosong.'); return; }

            const overview = {};
            for (const [id, qty] of entries) {
                const p = getProductData(id);
                if (!p) continue;
                overview[id] = { name: p.name, price: p.price, qty: Number(qty) || 0 };
            }
            localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify(overview));
           const token = (localStorage.getItem(TABLE_TOKEN_KEY) || '').trim();
const url = token ? ('/order/overview?table=' + encodeURIComponent(token)) : '/order/overview';
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
            document.getElementById('pmDesc').textContent = desc.trim() ? desc : 'Belum ada deskripsi.';

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

            // disable add kalau card add disabled
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