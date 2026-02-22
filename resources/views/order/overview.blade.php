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
          onclick="clearAll()"
        >Clear</button>
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

        <div>
          <label class="text-xs text-white/60">Order Type</label>
          <select id="orderType"
            class="mt-2 w-full rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm text-white/90 outline-none focus:border-white/30">
            <option value="dine_in">Dine In</option>
            <option value="takeaway">Takeaway</option>
          </select>
        </div>

        <div id="tableField">
          <label class="text-xs text-white/60">Meja (opsional)</label>
          <input id="tableNo" type="text" placeholder="Contoh: Meja 3"
            class="mt-2 w-full rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm text-white/90 placeholder:text-white/40 outline-none focus:border-white/30" />
        </div>

        <div class="sm:col-span-2">
          <label class="text-xs text-white/60">Catatan (opsional)</label>
          <textarea id="note" placeholder="Contoh: tanpa pedas, es sedikit, dll"
            class="mt-2 min-h-[84px] w-full resize-y rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm text-white/90 placeholder:text-white/40 outline-none focus:border-white/30"></textarea>
        </div>
      </div>

      <button
        class="mt-4 w-full rounded-xl bg-yellow-400/95 px-4 py-3 text-sm font-semibold text-black shadow-lg shadow-yellow-400/10 hover:bg-yellow-300"
        type="button"
        onclick="checkoutWhatsApp()"
      >
        Checkout (WhatsApp)
      </button>

      <p class="mt-2 text-xs text-white/50">
        * Checkout masih via WhatsApp (belum simpan order ke database).
      </p>
    </div>
  </div>

  <script>
    // Keys harus sama dengan welcome.blade.php terbaru
    const CART_QTY_KEY = 'ayo_renne_cart_v1';                // qty map
    const CART_OVERVIEW_KEY = 'ayo_renne_order_overview_v1'; // detail map

    function loadOverview(){
      try { return JSON.parse(localStorage.getItem(CART_OVERVIEW_KEY) || '{}') || {}; }
      catch { return {}; }
    }

    function saveOverview(cart){
      localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify(cart));
    }

    function syncQtyFromOverview(overview){
      const qty = {};
      for (const [id, it] of Object.entries(overview)){
        qty[id] = Number(it.qty) || 0;
      }
      localStorage.setItem(CART_QTY_KEY, JSON.stringify(qty));
    }

    function clearAll(){
      localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify({}));
      localStorage.setItem(CART_QTY_KEY, JSON.stringify({}));
      render();
    }

    function formatRp(n){
      n = Math.round(Number(n) || 0);
      return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function escapeHtml(str){
      return String(str || '')
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function computeTotals(overview){
      let subtotal = 0;
      for (const it of Object.values(overview)){
        subtotal += (Number(it.price)||0) * (Number(it.qty)||0);
      }
      const tax = Math.round(subtotal * 0.11);
      return { subtotal, tax, total: subtotal + tax };
    }

    function setQty(id, qty){
      const cart = loadOverview();
      if (!cart[id]) return;

      qty = Number(qty) || 0;
      if (qty <= 0) delete cart[id];
      else cart[id].qty = qty;

      saveOverview(cart);
      syncQtyFromOverview(cart);
      render();
    }

    function render(){
      const cart = loadOverview();
      const entries = Object.entries(cart);
      const wrap = document.getElementById('items');
      wrap.innerHTML = '';

      if (entries.length === 0){
        wrap.innerHTML = `
          <div class="rounded-2xl border border-white/10 bg-black/20 px-4 py-4 text-center text-sm text-white/65 backdrop-blur-2xl">
            Keranjang kosong. Silakan pilih menu dulu 🙂
          </div>`;
        document.getElementById('subtotal').textContent = 'Rp 0';
        document.getElementById('tax').textContent = 'Rp 0';
        document.getElementById('total').textContent = 'Rp 0';
        return;
      }

      for (const [id, it] of entries){
        const name = it.name || '-';
        const price = Number(it.price) || 0;
        const qty = Number(it.qty) || 0;

        const row = document.createElement('div');
        row.className = "flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-black/20 px-4 py-3 backdrop-blur-2xl";
        row.innerHTML = `
          <div class="min-w-0">
            <div class="truncate text-sm font-semibold text-white/90">${escapeHtml(name)}</div>
            <div class="mt-1 text-xs text-white/55">${formatRp(price)} × ${qty}</div>
          </div>
          <div class="flex items-center gap-2">
            <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/10"
                    onclick="setQty('${id}', ${qty-1})" type="button">−</button>
            <div class="min-w-[20px] text-center text-sm font-bold text-white/95">${qty}</div>
            <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/10"
                    onclick="setQty('${id}', ${qty+1})" type="button">+</button>
          </div>
        `;
        wrap.appendChild(row);
      }

      const t = computeTotals(cart);
      document.getElementById('subtotal').textContent = formatRp(t.subtotal);
      document.getElementById('tax').textContent = formatRp(t.tax);
      document.getElementById('total').textContent = formatRp(t.total);

      const orderType = document.getElementById('orderType').value;
      document.getElementById('tableField').style.display = (orderType === 'dine_in') ? '' : 'none';
    }

    document.getElementById('orderType').addEventListener('change', render);

    function checkoutWhatsApp(){
      const cart = loadOverview();
      const entries = Object.entries(cart);
      if (entries.length === 0){ alert('Keranjang masih kosong.'); return; }

      const name = (document.getElementById('custName').value || '').trim();
      const orderType = document.getElementById('orderType').value;
      const tableNo = (document.getElementById('tableNo').value || '').trim();
      const note = (document.getElementById('note').value || '').trim();

      const t = computeTotals(cart);

      let lines = [];
      lines.push('🍽️ *Ayo Renne — Order Baru*');
      if (name) lines.push('👤 Nama: ' + name);
      lines.push('📦 Tipe: ' + (orderType === 'dine_in' ? 'Dine In' : 'Takeaway'));
      if (orderType === 'dine_in' && tableNo) lines.push('🪑 Meja: ' + tableNo);
      lines.push('');
      lines.push('*Item:*');

      for (const [, it] of entries){
        const q = Number(it.qty)||0;
        const p = Number(it.price)||0;
        lines.push(`- ${it.name} × ${q} = ${formatRp(p*q)}`);
      }

      lines.push('');
      lines.push(`Subtotal: ${formatRp(t.subtotal)}`);
      lines.push(`Pajak (11%): ${formatRp(t.tax)}`);
      lines.push(`*Total: ${formatRp(t.total)}*`);

      if (note) { lines.push(''); lines.push('📝 Catatan: ' + note); }

      const phone = '6280000000000'; // ganti nomor resto
      const msg = encodeURIComponent(lines.join('\n'));
      window.open(`https://wa.me/${phone}?text=${msg}`, '_blank');
    }

    // init: pastikan key qty ikut sinkron dari overview (kalau user reload di halaman ini)
    (function init(){
      const ov = loadOverview();
      syncQtyFromOverview(ov);
      render();
    })();
  </script>
</body>
</html>