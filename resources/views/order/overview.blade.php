<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ayo Renne — Overview Pesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen text-white">
    <div class="fixed inset-0 -z-10 bg-gradient-to-b from-[#070708] to-[#0b0b0d]"></div>

    <div class="mx-auto max-w-3xl px-4 pb-20 pt-8">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <div class="text-xs tracking-[0.22em] uppercase text-white/80">Ayo Renne</div>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight">Overview Pesanan</h1>
                <p class="mt-1 text-sm text-white/65">Cek item kamu dulu, lalu isi keterangan sebelum checkout.</p>
            </div>

            <a href="/"
                class="rounded-xl border border-white/15 bg-black/20 px-4 py-2 text-sm font-semibold text-white/90 backdrop-blur-2xl hover:bg-white/10">
                ← Kembali
            </a>
        </div>

        <div class="rounded-[24px] border border-white/15 bg-white/10 p-4 shadow-2xl backdrop-blur-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-xs tracking-[0.22em] uppercase text-white/80">Item Pesanan</h2>
                <button type="button"
                    class="rounded-xl border border-white/15 bg-black/20 px-3 py-2 text-xs font-semibold text-white/85 backdrop-blur-2xl hover:bg-white/10"
                    onclick="clearAll()">Clear</button>
            </div>

            <div id="items" class="mt-4 flex flex-col gap-2"></div>

            <div class="mt-4 space-y-2 border-t border-white/10 pt-4 text-sm">
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
        </div>

        <div class="mt-4 rounded-[24px] border border-white/15 bg-white/10 p-4 shadow-2xl backdrop-blur-2xl">
            <h2 class="text-xs tracking-[0.22em] uppercase text-white/80">Keterangan</h2>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-xs text-white/60">Nama</label>
                    <input id="custName" type="text" placeholder="Nama kamu"
                        class="mt-2 w-full rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm text-white/90 placeholder:text-white/40 outline-none focus:border-white/30" />
                </div>

                <div class="sm:col-span-2">
                    <label class="text-xs text-white/60">Meja</label>
                    <select id="diningTableId" data-locked="{{ $lockedTable ? '1' : '0' }}"
                        data-locked-id="{{ $lockedTable?->id ?? '' }}"
                        class="mt-2 w-full rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm text-white/90 outline-none focus:border-white/30">
                        <option value="">— Pilih meja —</option>
                        @foreach(($tables ?? []) as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>

                    @if($lockedTable)
                        <p class="mt-2 text-xs text-green-200/90">
                            Meja terkunci dari QR: <b>{{ $lockedTable->name }}</b>
                        </p>
                    @else
                        <p class="mt-2 text-xs text-white/45">* Order Type otomatis: <b>Dine In</b></p>
                    @endif
                    <p class="mt-2 text-xs text-white/45">* Order Type otomatis: <b>Dine In</b></p>
                </div>
            </div>

            <button
                class="mt-4 w-full rounded-xl bg-yellow-400/95 px-4 py-3 text-sm font-semibold text-black shadow-lg shadow-yellow-400/10 hover:bg-yellow-300"
                type="button" onclick="checkoutDb()">
                Checkout
            </button>

            <p class="mt-2 text-xs text-white/50">
                * Checkout akan menyimpan order ke database & masuk antrean kitchen.
            </p>
        </div>
    </div>

    <script>
        // Keys harus sama dengan welcome.blade.php terbaru
        const CART_QTY_KEY = 'ayo_renne_cart_v1';                // qty map
        const CART_OVERVIEW_KEY = 'ayo_renne_order_overview_v1'; // detail map

        function loadOverview() {
            try { return JSON.parse(localStorage.getItem(CART_OVERVIEW_KEY) || '{}') || {}; }
            catch { return {}; }
        }

        function saveOverview(cart) {
            localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify(cart));
        }

        function syncQtyFromOverview(overview) {
            const qty = {};
            for (const [id, it] of Object.entries(overview)) {
                qty[id] = Number(it.qty) || 0;
            }
            localStorage.setItem(CART_QTY_KEY, JSON.stringify(qty));
        }

        function clearAll() {
            localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify({}));
            localStorage.setItem(CART_QTY_KEY, JSON.stringify({}));
            render();
        }

        function formatRp(n) {
            n = Math.round(Number(n) || 0);
            return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function computeTotals(overview) {
            let subtotal = 0;
            for (const it of Object.values(overview)) {
                subtotal += (Number(it.price) || 0) * (Number(it.qty) || 0);
            }
            const tax = Math.round(subtotal * 0.11);
            return { subtotal, tax, total: subtotal + tax };
        }

        function setQty(id, qty) {
            const cart = loadOverview();
            if (!cart[id]) return;

            qty = Number(qty) || 0;
            if (qty <= 0) delete cart[id];
            else cart[id].qty = qty;

            saveOverview(cart);
            syncQtyFromOverview(cart);
            render();
        }

        function setNote(id, note) {
            const cart = loadOverview();
            if (!cart[id]) return;

            cart[id].note = String(note ?? '');
            saveOverview(cart);
            // qty tidak berubah, tapi kita tetap sync biar konsisten
            syncQtyFromOverview(cart);
        }

        (function lockTableFromQr(){
  const sel = document.getElementById('diningTableId');
  if (!sel) return;

  const locked = sel.dataset.locked === '1';
  const lockedId = (sel.dataset.lockedId || '').trim();

  if (locked && lockedId) {
    sel.value = lockedId;
    sel.disabled = true;
  }
})();

        function render() {
            const cart = loadOverview();
            const entries = Object.entries(cart);
            const wrap = document.getElementById('items');
            wrap.innerHTML = '';

            if (entries.length === 0) {
                wrap.innerHTML = `
          <div class="rounded-2xl border border-white/10 bg-black/20 px-4 py-4 text-center text-sm text-white/65 backdrop-blur-2xl">
            Keranjang kosong. Silakan pilih menu dulu 🙂
          </div>`;
                document.getElementById('subtotal').textContent = 'Rp 0';
                document.getElementById('tax').textContent = 'Rp 0';
                document.getElementById('total').textContent = 'Rp 0';
                return;
            }

            for (const [id, it] of entries) {
                const name = it.name || '-';
                const price = Number(it.price) || 0;
                const qty = Number(it.qty) || 0;
                const note = (typeof it.note === 'string') ? it.note : '';

                const row = document.createElement('div');
                row.className = "flex items-start justify-between gap-3 rounded-2xl border border-white/10 bg-black/20 px-4 py-3 backdrop-blur-2xl";
                row.innerHTML = `
          <div class="min-w-0 flex-1">
            <div class="truncate text-sm font-semibold text-white/90">${escapeHtml(name)}</div>
            <div class="mt-1 text-xs text-white/55">${formatRp(price)} × ${qty}</div>

            <div class="mt-2">
              <label class="block text-[11px] text-white/55">Catatan item (opsional)</label>
              <input
                type="text"
                value="${escapeHtml(note)}"
                oninput="setNote('${id}', this.value)"
                placeholder="Contoh: tanpa sambal / pedas sedang"
                class="mt-1 w-full rounded-xl border border-white/12 bg-black/25 px-3 py-2 text-sm text-white/90 placeholder:text-white/35 outline-none focus:border-white/25"
              />
            </div>
          </div>

          <div class="flex items-center gap-2 pt-1">
            <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/10"
                    onclick="setQty('${id}', ${qty - 1})" type="button">−</button>
            <div class="min-w-[20px] text-center text-sm font-bold text-white/95">${qty}</div>
            <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/10"
                    onclick="setQty('${id}', ${qty + 1})" type="button">+</button>
          </div>
        `;
                wrap.appendChild(row);
            }

            const t = computeTotals(cart);
            document.getElementById('subtotal').textContent = formatRp(t.subtotal);
            document.getElementById('tax').textContent = formatRp(t.tax);
            document.getElementById('total').textContent = formatRp(t.total);
        }

        async function checkoutDb() {
            const cart = loadOverview();
            const entries = Object.entries(cart);
            if (entries.length === 0) { alert('Keranjang masih kosong.'); return; }

            const name = (document.getElementById('custName').value || '').trim();
            if (!name) { alert('Nama wajib diisi.'); return; }

            const diningTableId = (document.getElementById('diningTableId').value || '').trim();
            if (!diningTableId) { alert('Meja wajib dipilih.'); return; }

            const items = entries.map(([id, it]) => ({
                product_id: Number(id),
                qty: Number(it.qty) || 0,
                note: (it.note || '').trim() || null,
            })).filter(x => x.qty > 0);

            if (items.length === 0) { alert('Keranjang masih kosong.'); return; }

            const res = await fetch("{{ route('public.order.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    customer_name: name,
                    dining_table_id: Number(diningTableId),
                    items,
                })
            });

            const json = await res.json().catch(() => ({}));

            if (!res.ok || !json.ok) {
                alert(json.message || 'Checkout gagal.');
                return;
            }

            clearAll();
            alert('Order tersimpan! Invoice: ' + (json.invoice_no || '-'));
            window.location.href = '/';
        }

        // init: pastikan key qty ikut sinkron dari overview (kalau user reload di halaman ini)
        (function init() {
            const ov = loadOverview();
            syncQtyFromOverview(ov);
            render();
        })();
    </script>
</body>

</html>