@extends('layouts.kasir')
@section('title', 'Transaksi Baru')

@section('body')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">Transaksi Baru</h1>
            <p class="text-sm text-white/60">Tap produk untuk menambah qty. Touch friendly.</p>
        </div>
        <a href="{{ route('kasir.sales.index') }}"
            class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm hover:bg-white/10">
            Riwayat
        </a>
    </div>

    @if ($errors->any())
        <div class="mt-4 whitespace-pre-line rounded-2xl border border-red-200/20 bg-red-500/10 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('kasir.sales.store') }}"
        class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.2fr_.8fr]">
        @csrf

        {{-- Hidden submit data --}}
        <div id="saleFormData" class="hidden"></div>

        {{-- LEFT --}}
        <div class="rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-5 sm:p-6">
            <div class="flex items-center justify-between gap-3">
                <input id="search" type="text" placeholder="Cari produk..."
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm outline-none placeholder:text-white/30 focus:border-white/25">
                <button type="button" id="clearCart"
                    class="shrink-0 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm hover:bg-white/10">
                    Clear
                </button>
            </div>

            {{-- GRID PRODUK (PENTING: harus ada id="grid") --}}
            <div id="grid" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($products as $p)
                    @php
                        $maxPortions = (int) ($p->max_portions ?? 0);
                        $isSoldOut = $maxPortions <= 0;
                    @endphp

                    <button type="button"
                        class="product-card group relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-5 text-left shadow-[0_20px_80px_rgba(0,0,0,0.35)] backdrop-blur-2xl transition hover:bg-white/10 active:scale-[0.99]
                               {{ $isSoldOut ? 'opacity-50 grayscale cursor-not-allowed hover:bg-white/5' : '' }}"
                        data-product-id="{{ $p->id }}"
                        data-product-name="{{ $p->name }}"
                        data-product-price="{{ (int) $p->price }}"
                        data-product-max="{{ $maxPortions }}"
                        {{ $isSoldOut ? 'disabled' : '' }}
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-white">{{ $p->name }}</div>
                                <div class="mt-1 text-xs text-white/60">Menu</div>
                            </div>

                            @if ($isSoldOut)
                                <span class="rounded-full border border-red-300/20 bg-red-500/15 px-2.5 py-1 text-[11px] font-semibold text-red-100">
                                    SOLD OUT
                                </span>
                            @else
                                <span class="rounded-full border border-white/10 bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-white/80">
                                    Max {{ $maxPortions }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-4 text-lg font-semibold text-white">
                            Rp {{ number_format((int) $p->price, 0, ',', '.') }}
                        </div>

                        <div class="mt-2 text-xs text-white/60">
                            {{ $isSoldOut ? 'Stok bahan tidak cukup' : 'Tap untuk tambah' }}
                        </div>

                        @if ($isSoldOut)
                            {{-- biar aman, tapi tetap tidak mengganggu klik card lain --}}
                            <div class="pointer-events-none absolute inset-0 bg-black/10"></div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- RIGHT: cart --}}
        <div class="rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Keranjang</div>
                    <div class="text-xs text-white/60">Klik + / - untuk qty</div>
                </div>
                <div class="text-xs text-white/60" id="cartCount">0 item</div>
            </div>

            <div id="cart" class="mt-4 space-y-2"></div>

            <div class="mt-4 rounded-2xl border border-white/10 bg-white/5 p-4">
    {{-- Subtotal --}}
    <div class="flex items-center justify-between text-sm">
        <div class="text-white/70">Subtotal</div>
        <div class="font-semibold" id="subtotalText">Rp 0</div>
    </div>

    {{-- Pajak --}}
    <div class="mt-2 flex items-center justify-between text-sm">
        <div class="text-white/70">Pajak 11%</div>
        <div class="font-semibold" id="taxText">Rp 0</div>
    </div>

    {{-- Total --}}
    <div class="mt-2 flex items-center justify-between text-sm">
        <div class="text-white/70">Total</div>
        <div class="font-semibold" id="totalText">Rp 0</div>
    </div>

    <div class="mt-3">
        <label class="text-xs text-white/70">Bayar (Rp)</label>
        <input name="paid_amount" id="paidAmount" type="number" min="0" value="{{ old('paid_amount', 0) }}"
            class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm outline-none focus:border-white/25">
        <div class="mt-2 text-xs text-white/60">
            Kembalian: <b id="changeText">Rp 0</b>
        </div>
    </div>

    <button id="payBtn" type="submit"
        class="mt-4 w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85 disabled:opacity-40 disabled:cursor-not-allowed">
        Bayar
    </button>

    <div class="mt-2 text-[11px] text-white/50">
        STRICT mode aktif: kalau bahan kurang → transaksi ditolak.
    </div>
</div>
        </div>
    </form>

    <script>
        (function () {
            const grid = document.getElementById('grid');
            const cart = document.getElementById('cart');
            const formData = document.getElementById('saleFormData');
            const search = document.getElementById('search');

            const totalText = document.getElementById('totalText');
            const subtotalText = document.getElementById('subtotalText');
const taxText = document.getElementById('taxText');
            const cartCount = document.getElementById('cartCount');
            const paidAmount = document.getElementById('paidAmount');
            const changeText = document.getElementById('changeText');
            const payBtn = document.getElementById('payBtn');
            const clearCart = document.getElementById('clearCart');

            // cartMap: productId -> {id,name,price,qty,max}
            const cartMap = new Map();

            const fmtRp = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

            function addToCart(p) {
                // safety: jangan bisa tambah kalau max <= 0
                if (Number(p.max || 0) <= 0) return;

                const item = cartMap.get(p.id) || { ...p, qty: 0 };
                item.qty += 1;
                cartMap.set(p.id, item);
                renderCart();
            }

            function changeQty(id, delta) {
                const item = cartMap.get(id);
                if (!item) return;

                item.qty += delta;
                if (item.qty <= 0) cartMap.delete(id);
                else cartMap.set(id, item);

                renderCart();
            }

            function renderCart() {
  const TAX_RATE = 0.11;

  cart.innerHTML = '';
  formData.innerHTML = '';

  let subtotalAll = 0;
  let countQty = 0;
  let index = 0;

  cartMap.forEach((item) => {
    const subtotal = item.price * item.qty;
    subtotalAll += subtotal;
    countQty += item.qty;

    const row = document.createElement('div');
    row.className = 'rounded-2xl border border-white/10 bg-white/5 p-3 flex items-center gap-3';
    row.innerHTML = `
      <div class="flex-1">
        <div class="text-sm font-semibold">${item.name}</div>
        <div class="text-xs text-white/60">${fmtRp(item.price)} • Sub: <b>${fmtRp(subtotal)}</b></div>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" class="h-9 w-9 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10">-</button>
        <div class="w-10 text-center text-sm font-semibold">${item.qty}</div>
        <button type="button" class="h-9 w-9 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10">+</button>
      </div>
    `;

    const btns = row.querySelectorAll('button');
    btns[0].addEventListener('click', () => changeQty(item.id, -1));
    btns[1].addEventListener('click', () => changeQty(item.id, +1));

    cart.appendChild(row);

    // hidden submit
    formData.insertAdjacentHTML('beforeend', `
      <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
      <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
    `);
    index++;
  });

  // Pajak & Total
  const tax = Math.round(subtotalAll * TAX_RATE);
  const grandTotal = subtotalAll + tax;

  // Update UI teks
  if (subtotalText) subtotalText.textContent = fmtRp(subtotalAll);
  if (taxText) taxText.textContent = fmtRp(tax);

  totalText.textContent = fmtRp(grandTotal);
  cartCount.textContent = cartMap.size + ' produk • ' + countQty + ' qty';

  payBtn.disabled = cartMap.size === 0;

  // Kembalian pakai grand total
  const paid = Number(paidAmount.value || 0);
  const change = Math.max(0, paid - grandTotal);
  changeText.textContent = fmtRp(change);
}

            // klik card produk (yang sudah dirender Blade)
            if (grid) {
                grid.querySelectorAll('.product-card').forEach((card) => {
                    card.addEventListener('click', (e) => {
                        // kalau disabled, browser biasanya sudah block, tapi ini extra safety
                        const max = Number(card.dataset.productMax || 0);
                        if (max <= 0 || card.disabled) {
                            e.preventDefault();
                            return;
                        }

                        addToCart({
                            id: Number(card.dataset.productId),
                            name: card.dataset.productName,
                            price: Number(card.dataset.productPrice || 0),
                            max: max,
                        });
                    });
                });
            }

            // Search: hide/show card
            if (search && grid) {
                search.addEventListener('input', () => {
                    const q = (search.value || '').toLowerCase().trim();
                    grid.querySelectorAll('.product-card').forEach((card) => {
                        const name = (card.dataset.productName || '').toLowerCase();
                        card.classList.toggle('hidden', q && !name.includes(q));
                    });
                });
            }

            paidAmount.addEventListener('input', renderCart);

            clearCart.addEventListener('click', () => {
                cartMap.clear();
                renderCart();
            });

            renderCart();
        })();
    </script>
@endsection
