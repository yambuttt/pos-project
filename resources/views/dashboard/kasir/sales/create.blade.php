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

    <form id="saleForm" method="POST" action="{{ route('kasir.sales.store') }}"
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
        <div class="mt-3">
    <label class="text-xs text-white/70">Jenis Pesanan</label>

    <div class="mt-2 grid grid-cols-2 gap-2">
        <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10">
            <input type="radio" name="order_type" value="takeaway" class="rounded border-white/20 bg-white/10"
                   {{ old('order_type', 'takeaway') === 'takeaway' ? 'checked' : '' }}>
            Take Away
        </label>

        <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10">
            <input type="radio" name="order_type" value="dine_in" class="rounded border-white/20 bg-white/10"
                   {{ old('order_type') === 'dine_in' ? 'checked' : '' }}>
            Dine In
        </label>
    </div>
</div>

<div id="tableWrap" class="mt-3 hidden">
    <label class="text-xs text-white/70">Meja</label>
    <select name="dining_table_id" id="diningTable"
        class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm outline-none focus:border-white/25">
        <option value="">— Pilih meja —</option>
        @foreach(($tables ?? []) as $t)
            <option value="{{ $t->id }}" {{ (string)old('dining_table_id') === (string)$t->id ? 'selected' : '' }}>
                {{ $t->name }}
            </option>
        @endforeach
    </select>
</div>
  <label class="text-xs text-white/70">Metode Pembayaran</label>
  <select name="payment_method" id="paymentMethod"
      class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm outline-none focus:border-white/25">
<option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
<option value="qris" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
<option value="bca_va" {{ old('payment_method') === 'bca_va' ? 'selected' : '' }}>VA BCA</option>
<option value="bni_va" {{ old('payment_method') === 'bni_va' ? 'selected' : '' }}>VA BNI</option>
<option value="bri_va" {{ old('payment_method') === 'bri_va' ? 'selected' : '' }}>VA BRI</option>
<option value="permata_va" {{ old('payment_method') === 'permata_va' ? 'selected' : '' }}>VA Permata</option>
  </select>
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
    <!-- <div id="paymentResult" class="mt-4 hidden rounded-2xl border border-white/10 bg-white/5 p-4">
    <div class="text-sm font-semibold">Instruksi Pembayaran</div>
    <div class="mt-2 text-xs text-white/60" id="paymentResultMeta"></div>

    <div id="paymentQrisWrap" class="mt-4 hidden">
        <div class="text-xs text-white/70">Scan QRIS ini:</div>
        <img id="paymentQrImage" src="" alt="QRIS"
            class="mt-3 w-full max-w-[280px] rounded-2xl bg-white p-3">
    </div>

    <div id="paymentVaWrap" class="mt-4 hidden">
        <div class="text-xs text-white/70">Nomor VA:</div>
        <div id="paymentVaNumber"
            class="mt-2 rounded-xl border border-white/10 bg-black/20 px-4 py-3 text-lg font-semibold tracking-wider"></div>
        <div id="paymentVaBank" class="mt-2 text-xs text-white/60"></div>
    </div> -->

    <div id="paymentStatusText" class="mt-4 text-sm text-emerald-300"></div>
</div>
<div class="mt-4 rounded-2xl border border-yellow-500/10 bg-white/5 p-4">
    <div class="text-sm font-semibold text-white">Bayar Pesanan QR Meja</div>
    <div class="mt-1 text-xs text-white/60">
        Input atau scan kode bayar customer untuk memproses pembayaran tunai di kasir.
    </div>

    <div class="mt-4 flex flex-col gap-3 sm:flex-row">
        <input id="pendingCashInvoiceNo" type="text"
            class="w-full rounded-xl border border-yellow-500/16 bg-white/5 px-4 py-3 text-sm outline-none placeholder:text-white/30 focus:border-yellow-500/30"
            placeholder="Contoh: INV-20260408-ABC">
        <button type="button" id="findPendingCashBtn"
            class="rounded-xl bg-yellow-500 px-4 py-3 text-sm font-semibold text-black hover:bg-yellow-400">
            Cari Pesanan
        </button>
    </div>

    <div id="pendingCashMessage" class="mt-3 text-sm text-white/60"></div>

    <div id="pendingCashResult" class="mt-4 hidden rounded-2xl border border-yellow-500/10 bg-black/20 p-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="text-xs text-white/50">Kode Bayar</div>
                <div id="pendingCashInvoiceLabel" class="mt-1 text-lg font-semibold text-yellow-400">-</div>
            </div>
            <div class="text-right text-xs text-white/60">
                <div>Order: <span id="pendingCashOrderType">-</span></div>
                <div>Meja: <span id="pendingCashTable">-</span></div>
            </div>
        </div>

        <div id="pendingCashItems" class="mt-4 space-y-2"></div>

        <div class="mt-4 flex items-center justify-between border-t border-white/10 pt-4">
            <span class="text-sm text-white/70">Total</span>
            <span id="pendingCashTotal" class="text-base font-semibold text-white">Rp 0</span>
        </div>

        <div class="mt-4">
            <label class="text-xs text-white/70">Uang Diterima</label>
            <input id="pendingCashPaidAmount" type="number" min="0"
                class="mt-2 w-full rounded-xl border border-yellow-500/16 bg-white/5 px-4 py-3 text-sm outline-none focus:border-yellow-500/30"
                placeholder="Masukkan uang bayar customer">
        </div>

        <div class="mt-2 text-xs text-white/60">
            Kembalian: <b id="pendingCashChange">Rp 0</b>
        </div>

        <button type="button" id="confirmPendingCashBtn"
            class="mt-4 w-full rounded-xl bg-yellow-500 px-5 py-3 text-sm font-semibold text-black hover:bg-yellow-400">
            Konfirmasi Pembayaran
        </button>
    </div>
</div>

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
            const saleForm = document.getElementById('saleForm');
const paymentResult = document.getElementById('paymentResult');
const paymentResultMeta = document.getElementById('paymentResultMeta');
const paymentQrisWrap = document.getElementById('paymentQrisWrap');
const paymentQrImage = document.getElementById('paymentQrImage');
const paymentVaWrap = document.getElementById('paymentVaWrap');
const paymentVaNumber = document.getElementById('paymentVaNumber');
const paymentVaBank = document.getElementById('paymentVaBank');
const paymentStatusText = document.getElementById('paymentStatusText');
let paymentPoller = null;

            const totalText = document.getElementById('totalText');
            const subtotalText = document.getElementById('subtotalText');
            const taxText = document.getElementById('taxText');
            const cartCount = document.getElementById('cartCount');
            const paidAmount = document.getElementById('paidAmount');
            const paymentMethod = document.getElementById('paymentMethod');
            const changeText = document.getElementById('changeText');
            const payBtn = document.getElementById('payBtn');
            const clearCart = document.getElementById('clearCart');
            const pendingCashInvoiceNo = document.getElementById('pendingCashInvoiceNo');
const findPendingCashBtn = document.getElementById('findPendingCashBtn');
const pendingCashMessage = document.getElementById('pendingCashMessage');
const pendingCashResult = document.getElementById('pendingCashResult');
const pendingCashInvoiceLabel = document.getElementById('pendingCashInvoiceLabel');
const pendingCashOrderType = document.getElementById('pendingCashOrderType');
const pendingCashTable = document.getElementById('pendingCashTable');
const pendingCashItems = document.getElementById('pendingCashItems');
const pendingCashTotal = document.getElementById('pendingCashTotal');
const pendingCashPaidAmount = document.getElementById('pendingCashPaidAmount');
const pendingCashChange = document.getElementById('pendingCashChange');
const confirmPendingCashBtn = document.getElementById('confirmPendingCashBtn');

let currentPendingCashSale = null;

            // cartMap: productId -> {id,name,price,qty,max}
            const cartMap = new Map();

            const fmtRp = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
            const escapeAttr = (str) => String(str ?? '')
  .replaceAll('&', '&amp;')
  .replaceAll('<', '&lt;')
  .replaceAll('>', '&gt;')
  .replaceAll('"', '&quot;')
  .replaceAll("'", '&#039;');
            // ===== DINE IN / TAKEAWAY toggle =====
const tableWrap = document.getElementById('tableWrap');
const diningTable = document.getElementById('diningTable');

function syncOrderTypeUI() {
  const typeEl = document.querySelector('input[name="order_type"]:checked');
  const type = typeEl ? typeEl.value : 'takeaway';

  if (!tableWrap) return;

  if (type === 'dine_in') {
    tableWrap.classList.remove('hidden');
    if (diningTable) diningTable.disabled = false;
  } else {
    tableWrap.classList.add('hidden');
    if (diningTable) {
      diningTable.value = '';
      diningTable.disabled = true;
    }
  }
}

document.querySelectorAll('input[name="order_type"]').forEach(r => {
  r.addEventListener('change', syncOrderTypeUI);
});
syncOrderTypeUI();

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
    // pastikan note ada biar gak undefined
    if (typeof item.note !== 'string') item.note = '';

    const subtotal = item.price * item.qty;
    subtotalAll += subtotal;
    countQty += item.qty;

    const row = document.createElement('div');
    row.className = 'rounded-2xl border border-white/10 bg-white/5 p-3';

    row.innerHTML = `
      <div class="flex items-start gap-3">
        <div class="flex-1">
          <div class="text-sm font-semibold">${item.name}</div>
          <div class="text-xs text-white/60">${fmtRp(item.price)} • Sub: <b>${fmtRp(subtotal)}</b></div>

          <div class="mt-2">
            <label class="block text-[11px] text-white/60">Catatan item (opsional)</label>
            <input
              type="text"
              class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm outline-none placeholder:text-white/30 focus:border-white/25"
              placeholder="Contoh: bakso agak asin / tanpa sambal"
              value="${escapeAttr(item.note)}"
              data-note-for="${item.id}"
            />
          </div>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <button type="button" class="h-9 w-9 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10">-</button>
          <div class="w-10 text-center text-sm font-semibold">${item.qty}</div>
          <button type="button" class="h-9 w-9 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10">+</button>
        </div>
      </div>
    `;

    const btns = row.querySelectorAll('button');
    btns[0].addEventListener('click', () => changeQty(item.id, -1));
    btns[1].addEventListener('click', () => changeQty(item.id, +1));

    // input catatan
    const noteInput = row.querySelector(`input[data-note-for="${item.id}"]`);
    noteInput.addEventListener('input', (e) => {
      const val = e.target.value ?? '';
      const it = cartMap.get(item.id);
      if (!it) return;
      it.note = val;
      cartMap.set(item.id, it);

      // update hidden note (kalau ada)
      const hidden = formData.querySelector(`input[data-note-hidden-for="${item.id}"]`);
      if (hidden) hidden.value = val;
    });

    cart.appendChild(row);

    // hidden submit
    formData.insertAdjacentHTML('beforeend', `
      <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
      <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
      <input type="hidden" name="items[${index}][note]" value="${escapeAttr(item.note)}" data-note-hidden-for="${item.id}">
    `);

    index++;
  });

  // Pajak & Total
  const tax = Math.round(subtotalAll * TAX_RATE);
  const grandTotal = subtotalAll + tax;

  if (subtotalText) subtotalText.textContent = fmtRp(subtotalAll);
  if (taxText) taxText.textContent = fmtRp(tax);

  totalText.textContent = fmtRp(grandTotal);
  cartCount.textContent = cartMap.size + ' produk • ' + countQty + ' qty';

  payBtn.disabled = cartMap.size === 0;

const method = (paymentMethod?.value || 'cash');

if (isMidtransMethod(method)) {
    paidAmount.value = String(grandTotal);
    paidAmount.setAttribute('readonly', 'readonly');
    changeText.textContent = fmtRp(0);
} else {
    paidAmount.removeAttribute('readonly');
    const paid = Number(paidAmount.value || 0);
    const change = Math.max(0, paid - grandTotal);
    changeText.textContent = fmtRp(change);
}
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
            paymentMethod?.addEventListener('change', renderCart);

            clearCart.addEventListener('click', () => {
                cartMap.clear();
                renderCart();
            });

            function isMidtransMethod(method) {
    return ['qris', 'bca_va', 'bni_va', 'bri_va', 'permata_va'].includes(method);
}



saleForm.addEventListener('submit', async (e) => {
    const method = paymentMethod?.value || 'cash';

    if (!isMidtransMethod(method)) return;

    e.preventDefault();

    const form = new FormData(saleForm);

    payBtn.disabled = true;
    payBtn.textContent = 'Memproses...';

    try {
        const res = await fetch(saleForm.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: form,
        });

        const data = await res.json();

        if (!res.ok || !data.ok) {
            throw new Error(data.message || 'Gagal membuat transaksi pembayaran.');
        }

        window.location.href = data.redirect_url;
    } catch (err) {
        alert(err.message || 'Terjadi kesalahan saat membuat pembayaran.');
        payBtn.disabled = false;
        payBtn.textContent = 'Bayar';
    }
});

function renderPendingCashSale(sale) {
    currentPendingCashSale = sale;
    pendingCashResult.classList.remove('hidden');

    pendingCashInvoiceLabel.textContent = sale.invoice_no || '-';
    pendingCashOrderType.textContent = sale.order_type || '-';
    pendingCashTable.textContent = sale.dining_table || '-';
    pendingCashTotal.textContent = fmtRp(sale.total_amount || 0);

    pendingCashItems.innerHTML = '';

    (sale.items || []).forEach(item => {
        const row = document.createElement('div');
        row.className = 'rounded-xl border border-white/10 bg-white/5 p-3';

        row.innerHTML = `
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-white">${item.product_name || '-'}</div>
                    <div class="mt-1 text-xs text-white/60">${fmtRp(item.price)} × ${item.qty}</div>
                    ${item.note ? `<div class="mt-1 text-xs text-white/50">Catatan: ${item.note}</div>` : ''}
                </div>
                <div class="text-sm font-semibold text-yellow-400">${fmtRp(item.subtotal)}</div>
            </div>
        `;

        pendingCashItems.appendChild(row);
    });

    pendingCashPaidAmount.value = sale.total_amount || 0;
    updatePendingCashChange();
}

function updatePendingCashChange() {
    if (!currentPendingCashSale) return;

    const paid = Number(pendingCashPaidAmount.value || 0);
    const total = Number(currentPendingCashSale.total_amount || 0);
    const change = Math.max(0, paid - total);

    pendingCashChange.textContent = fmtRp(change);
}

async function findPendingCashOrder() {
    const invoiceNo = (pendingCashInvoiceNo.value || '').trim();

    if (!invoiceNo) {
        pendingCashMessage.textContent = 'Masukkan kode bayar terlebih dahulu.';
        pendingCashResult.classList.add('hidden');
        return;
    }

    pendingCashMessage.textContent = 'Mencari pesanan...';
    pendingCashResult.classList.add('hidden');

    try {
        const res = await fetch("{{ route('kasir.sales.find-pending-cash') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                invoice_no: invoiceNo,
            }),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || !data.ok) {
            throw new Error(data.message || 'Pesanan tidak ditemukan.');
        }

        pendingCashMessage.textContent = 'Pesanan ditemukan.';
        renderPendingCashSale(data.sale);
    } catch (err) {
        pendingCashMessage.textContent = err.message || 'Pesanan tidak ditemukan.';
        pendingCashResult.classList.add('hidden');
        currentPendingCashSale = null;
    }
}

async function confirmPendingCashOrder() {
    if (!currentPendingCashSale) {
        pendingCashMessage.textContent = 'Cari pesanan dulu.';
        return;
    }

    try {
        const res = await fetch("{{ route('kasir.sales.confirm-pending-cash') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                invoice_no: currentPendingCashSale.invoice_no,
                paid_amount: Number(pendingCashPaidAmount.value || 0),
            }),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || !data.ok) {
            throw new Error(data.message || 'Gagal konfirmasi pembayaran.');
        }

        pendingCashMessage.textContent = data.message || 'Pembayaran berhasil.';
        pendingCashResult.classList.add('hidden');
        currentPendingCashSale = null;
        pendingCashInvoiceNo.value = '';
        pendingCashPaidAmount.value = '';
        pendingCashChange.textContent = fmtRp(0);

setTimeout(() => {
    window.location.href = data.redirect_url;
}, 800);
    } catch (err) {
        pendingCashMessage.textContent = err.message || 'Gagal konfirmasi pembayaran.';
    }
}

findPendingCashBtn?.addEventListener('click', findPendingCashOrder);
pendingCashInvoiceNo?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        findPendingCashOrder();
    }
});
pendingCashPaidAmount?.addEventListener('input', updatePendingCashChange);
confirmPendingCashBtn?.addEventListener('click', confirmPendingCashOrder);

            renderCart();
        })();
    </script>
@endsection
