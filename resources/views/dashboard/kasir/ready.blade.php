@extends('layouts.kasir')
@section('title','Pesanan Siap')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div>
      <div class="text-xl font-semibold">Pesanan Siap Diambil</div>
      <div class="text-sm text-white/60">Pesanan dari kitchen yang sudah selesai dimasak.</div>
    </div>

    <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm">
      Update: <span id="readyUpdate" class="font-semibold">-</span>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-xl border border-green-300/20 bg-green-500/10 px-4 py-3 text-sm">
      ✅ {{ session('success') }}
    </div>
  @endif
  @if($errors->any())
    <div class="mt-4 rounded-xl border border-red-300/20 bg-red-500/10 px-4 py-3 text-sm">
      ❌ {{ $errors->first() }}
    </div>
  @endif

  <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-5">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold">READY (Belum delivered)</div>
        <span id="readyCount" class="rounded-xl border border-white/10 bg-white/5 px-3 py-1 text-xs text-white/70">0</span>
      </div>
      <div id="readyList" class="mt-4 space-y-3"></div>
    </div>

    <div class="rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-5">
      <div class="text-sm font-semibold">Riwayat (Done + Delivered hari ini)</div>
      <div id="allList" class="mt-4 space-y-3"></div>
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
        return `<span class="inline-flex items-center gap-2 rounded-xl border border-sky-300/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold text-sky-100">🍽️ DINE IN • ${escapeHtml(t)}</span>`;
      }
      return `<span class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-white/80">🥡 TAKE AWAY</span>`;
    }

    function renderCard(s, showAction){
      const invoice = s.invoice_no ?? ('#' + s.id);
      const items = (s.items||[]).map(it => {
        const name = it.product?.name ?? ('Product#'+it.product_id);
        return `<li class="flex justify-between gap-2 text-sm">
          <span>${escapeHtml(name)}</span><span class="text-white/60">x${it.qty}</span>
        </li>`;
      }).join('') || `<li class="text-white/60 text-sm">Tidak ada item</li>`;

      const statusBadge = (s.kitchen_status === 'delivered')
        ? `<span class="rounded-xl border border-emerald-300/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-100">🚚 DELIVERED</span>`
        : `<span class="rounded-xl border border-amber-300/20 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-100">✅ READY</span>`;

      const actionBtn = showAction
        ? `<button type="button" data-deliver="${s.id}"
            class="rounded-xl bg-blue-600/85 px-4 py-2 text-xs font-semibold hover:bg-blue-500/85">
            Sudah diberikan ke customer
          </button>`
        : '';

      return `
        <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-2xl p-4">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="text-sm font-semibold">${escapeHtml(invoice)}</div>
              <div class="mt-2 flex flex-wrap items-center gap-2">
                ${badgeType(s)}
                ${statusBadge}
              </div>
            </div>
            <div class="text-sm font-semibold">${rupiah(s.total_amount)}</div>
          </div>

          <ul class="mt-3 space-y-1">${items}</ul>

          ${showAction ? `<div class="mt-3 flex justify-end">${actionBtn}</div>` : ''}
        </div>
      `;
    }

    let lastReadyIds = new Set();
    let initialLoaded = false;

    async function loadReady(){
      const res = await fetch("{{ route('kasir.ready.orders') }}", { headers:{'Accept':'application/json'}});
      const data = await res.json();

      readyUpdate.textContent = data.now || '-';
      readyCount.textContent = data.ready_count ?? 0;

      const readySales = data.ready_sales || [];
      const allSales = data.all_sales || [];

      // notif jika ada ready baru
      const newIds = new Set(readySales.map(s => s.id));
      if(initialLoaded){
        let hasNew = false;
        for(const id of newIds){
          if(!lastReadyIds.has(id)){ hasNew = true; break; }
        }
        if(hasNew){
          showToast(`Pesanan baru READY dari Kitchen (${readySales.length} siap diambil)`);
        }
      }
      lastReadyIds = newIds;
      initialLoaded = true;

      readyList.innerHTML = readySales.map(s => renderCard(s, true)).join('') || `<div class="text-white/60 text-sm">Belum ada pesanan READY.</div>`;
      allList.innerHTML = allSales.map(s => renderCard(s, false)).join('') || `<div class="text-white/60 text-sm">Belum ada data.</div>`;
    }

    // deliver action
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-deliver]');
      if(!btn) return;
      const id = btn.getAttribute('data-deliver');

      if(!confirm('Tandai pesanan ini sudah diberikan ke customer?')) return;

      const form = document.getElementById('deliverForm');
      form.action = "{{ url('/kasir/ready-orders') }}/" + id + "/deliver";
      form.submit();
    });

    // simple toast
    function showToast(text){
      const el = document.createElement('div');
      el.className = "fixed right-4 top-4 z-50 rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white backdrop-blur-2xl shadow-lg";
      el.textContent = text;
      document.body.appendChild(el);
      setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .3s'; }, 2500);
      setTimeout(() => el.remove(), 3000);
    }

    loadReady();
    setInterval(loadReady, 4000);
  </script>
@endsection