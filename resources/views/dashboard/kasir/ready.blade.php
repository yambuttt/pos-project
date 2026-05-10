@extends('layouts.kasir')
@section('title','Pesanan Siap')

@section('body')
  <div class="max-w-7xl mx-auto space-y-8 animate-fade-up">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-bold tracking-tight mb-1 text-white">Pesanan Siap Diambil</h1>
        <p class="text-white/40 text-sm">Pesanan dari kitchen yang sudah selesai dimasak dan siap disajikan.</p>
      </div>

      <div class="glass-panel rounded-2xl px-6 py-3 flex items-center gap-4 border-white/5">
        <div class="flex flex-col items-end">
          <span class="text-[10px] font-black uppercase tracking-widest text-white/20">Pembaruan Terakhir</span>
          <span id="readyUpdate" class="text-xs font-bold text-accent-gold">-</span>
        </div>
        <div class="w-px h-8 bg-white/10"></div>
        <div class="w-2 h-2 rounded-full bg-accent-gold animate-pulse shadow-[0_0_8px_rgba(234,179,8,0.5)]"></div>
      </div>
    </div>

    @if(session('success'))
      <div class="rounded-2xl border border-accent-gold/20 bg-accent-gold/5 px-6 py-4 text-sm text-accent-gold flex items-center gap-3 animate-fade-up">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ session('success') }}
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      {{-- Ready Column --}}
      <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
          <h2 class="text-lg font-bold tracking-tight flex items-center gap-3 text-white">
            <span class="w-2 h-6 bg-accent-gold rounded-full"></span>
            Siap Diambil
          </h2>
          <span id="readyCount" class="bg-accent-gold text-black px-3 py-1 rounded-lg text-xs font-black shadow-lg shadow-accent-gold/10">0</span>
        </div>
        <div id="readyList" class="space-y-4"></div>
      </div>

      {{-- History Column --}}
      <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
          <h2 class="text-lg font-bold tracking-tight text-white/40">Riwayat Hari Ini</h2>
          <span class="text-[10px] font-black uppercase tracking-widest text-white/20">Selesai & Diserahkan</span>
        </div>
        <div id="allList" class="space-y-4 opacity-50"></div>
      </div>
    </div>

  </div>

  <form id="deliverForm" method="POST" class="hidden">
    @csrf
  </form>

  <script>
    const readyUpdate = document.getElementById('readyUpdate');
    const readyCount = document.getElementById('readyCount');
    const readyList = document.getElementById('readyList');
    const allList = document.getElementById('allList');

    function escapeHtml(str){
      return String(str ?? '')
        .replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;')
        .replaceAll('"','&quot;').replaceAll("'","&#039;");
    }
    function rupiah(n){ return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }

    function badgeType(s){
      if(s.order_type === 'dine_in'){
        const t = s.dining_table?.name ?? ('Meja ' + (s.dining_table_id ?? '-'));
        return `<span class="inline-flex items-center gap-2 rounded-xl border border-accent-gold/20 bg-accent-gold/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-accent-gold">🍽️ DINE IN • ${escapeHtml(t)}</span>`;
      }
      if(s.order_type === 'delivery'){
        return `<span class="inline-flex items-center gap-2 rounded-xl border border-accent-amber/20 bg-accent-amber/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-accent-amber">🚚 DELIVERY</span>`;
      }
      return `<span class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-white/40">🥡 TAKE AWAY</span>`;
    }

    function renderCard(s, showAction){
      const invoice = s.invoice_no ?? ('#' + s.id);
      const items = (s.items||[]).map(it => {
        const name = it.product?.name ?? ('Product#'+it.product_id);
        return `<li class="flex justify-between gap-4 text-xs font-medium">
          <span class="text-white/70">${escapeHtml(name)}</span>
          <span class="font-black text-accent-gold">x${it.qty}</span>
        </li>`;
      }).join('') || `<li class="text-white/20 text-xs italic">Tidak ada item</li>`;

      const statusBadge = (s.kitchen_status === 'delivered')
        ? `<span class="inline-flex items-center gap-2 rounded-xl border border-white/5 bg-white/5 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-white/20">Selesai</span>`
        : `<span class="inline-flex items-center gap-2 rounded-xl border border-accent-gold/20 bg-accent-gold/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-accent-gold animate-pulse">Ready</span>`;

      const actionBtn = showAction
        ? `<button type="button" data-deliver="${s.id}"
            class="btn-premium-primary py-3 px-6 text-[10px] uppercase tracking-[0.2em] font-black">
            Serahkan Pesanan
          </button>`
        : '';

      const isDelivery = (s.order_type === 'delivery');
      const hasLatLng = (s.delivery_lat != null && s.delivery_lng != null);
      const mapsUrl = (isDelivery && hasLatLng)
        ? `https://www.google.com/maps?q=${encodeURIComponent(s.delivery_lat)},${encodeURIComponent(s.delivery_lng)}`
        : null;

      const customerNameRaw = (s.user?.name || '').trim();
      const customerName = customerNameRaw ? customerNameRaw.replace('(Tamu)', '').trim() : '';

      const deliveryBlock = isDelivery ? `
        <div class="mt-4 p-4 rounded-2xl bg-white/[0.02] border border-white/5 space-y-2">
          <div class="text-[10px] font-black uppercase tracking-[0.2em] text-accent-gold">Detail Pengantaran</div>
          <div class="grid grid-cols-2 gap-4 text-[11px]">
            ${customerName ? `<div><span class="text-white/30 block mb-0.5">Nama</span><b class="text-white">${escapeHtml(customerName)}</b></div>` : ''}
            ${s.delivery_phone ? `<div><span class="text-white/30 block mb-0.5">Telepon</span><b class="text-white">${escapeHtml(s.delivery_phone)}</b></div>` : ''}
            <div class="col-span-2"><span class="text-white/30 block mb-0.5">Alamat</span><span class="text-white/70">${escapeHtml(s.delivery_address)}</span></div>
          </div>
          ${hasLatLng ? `
            <div class="mt-2 pt-2 border-t border-white/5">
              <a class="text-[10px] font-bold text-accent-gold hover:underline flex items-center gap-1" href="${mapsUrl}" target="_blank">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Buka Peta Lokasi
              </a>
            </div>
          ` : ''}
        </div>
      ` : '';

      return `
        <div class="premium-card p-6 border-white/5 hover:border-accent-gold/20">
          <div class="flex items-start justify-between gap-4 mb-6">
            <div>
              <div class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 mb-1">Invoice</div>
              <div class="text-sm font-bold tracking-tight text-white">${escapeHtml(invoice)}</div>
            </div>
            <div class="text-right">
              <div class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 mb-1">Total</div>
              <div class="text-sm font-bold text-accent-gold">${rupiah(s.total_amount)}</div>
            </div>
          </div>

          <div class="flex flex-wrap gap-2 mb-6">
            ${badgeType(s)}
            ${statusBadge}
          </div>

          <div class="space-y-3 mb-6">
            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-white/10 border-b border-white/5 pb-1">Detail Item</div>
            <ul class="space-y-2">${items}</ul>
          </div>

          ${deliveryBlock}

          ${showAction ? `<div class="mt-6 flex justify-end">${actionBtn}</div>` : ''}
        </div>
      `;
    }

    let lastReadyIds = new Set();
    let initialLoaded = false;

    async function loadReady(){
      try {
        const res = await fetch("{{ route('kasir.ready.orders') }}", { headers:{'Accept':'application/json'}});
        const data = await res.json();

        readyUpdate.textContent = data.now || '-';
        readyCount.textContent = data.ready_count ?? 0;

        const readySales = data.ready_sales || [];
        const allSales = data.all_sales || [];

        const newIds = new Set(readySales.map(s => s.id));
        if(initialLoaded){
          let hasNew = false;
          for(const id of newIds){
            if(!lastReadyIds.has(id)){ hasNew = true; break; }
          }
          if(hasNew){
            showToast(`🛎️ Pesanan READY! (${readySales.length} total)`);
          }
        }
        lastReadyIds = newIds;
        initialLoaded = true;

        readyList.innerHTML = readySales.map(s => renderCard(s, true)).join('') || `<div class="glass-panel rounded-2xl p-12 text-center text-white/10 border-white/5"><svg class="w-12 h-12 mx-auto mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg><p class="text-xs font-black uppercase tracking-[0.2em]">Antrean Kosong</p></div>`;
        allList.innerHTML = allSales.map(s => renderCard(s, false)).join('') || `<div class="text-white/10 text-xs italic font-bold uppercase tracking-widest text-center py-8">Belum ada riwayat</div>`;
      } catch(e) {}
    }

    document.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-deliver]');
      if(!btn) return;
      const id = btn.getAttribute('data-deliver');
      if(!confirm('Konfirmasi penyerahan pesanan ke customer?')) return;
      const form = document.getElementById('deliverForm');
      form.action = "{{ url('/kasir/ready-orders') }}/" + id + "/deliver";
      form.submit();
    });

    function showToast(text){
      const el = document.createElement('div');
      el.className = "fixed right-4 top-4 z-[9999] glass-panel rounded-2xl px-6 py-4 text-sm text-white animate-fade-up border-accent-gold/30 shadow-[0_0_30px_rgba(234,179,8,0.2)]";
      el.innerHTML = `<div class="flex items-center gap-3"><span class="w-2 h-2 rounded-full bg-accent-gold animate-ping"></span>${text}</div>`;
      document.body.appendChild(el);
      setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .5s'; }, 3500);
      setTimeout(() => el.remove(), 4000);
    }

    loadReady();
    setInterval(loadReady, 5000);
  </script>
@endsection