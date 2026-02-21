@extends('layouts.kitchen')
@section('title', 'Kitchen Display')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl font-semibold">Kitchen Display</h1>
      <p class="text-sm text-white/80">Antrian pesanan masuk dari kasir (real-time).</p>
    </div>

    <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2 text-sm backdrop-blur-2xl">
      <span class="text-white/70">Update:</span> <span id="lastUpdate" class="font-semibold">-</span>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-3">
    {{-- NEW --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-4 backdrop-blur-2xl">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Baru Masuk</h2>
        <span id="countNew" class="rounded-xl bg-white/15 px-3 py-1 text-sm">0</span>
      </div>
      <div id="colNew" class="mt-3 space-y-3"></div>
    </div>

    {{-- PROCESSING --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-4 backdrop-blur-2xl">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Sedang Diproses</h2>
        <span id="countProc" class="rounded-xl bg-white/15 px-3 py-1 text-sm">0</span>
      </div>
      <div id="colProc" class="mt-3 space-y-3"></div>
    </div>

    {{-- DONE --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-4 backdrop-blur-2xl">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Selesai</h2>
        <span id="countDone" class="rounded-xl bg-white/15 px-3 py-1 text-sm">0</span>
      </div>
      <div id="colDone" class="mt-3 space-y-3"></div>
    </div>
  </div>

  <form id="actionForm" method="POST" class="hidden">
    @csrf
  </form>

  <script>
    const colNew = document.getElementById('colNew');
    const colProc = document.getElementById('colProc');
    const colDone = document.getElementById('colDone');
    const lastUpdate = document.getElementById('lastUpdate');

    const countNew = document.getElementById('countNew');
    const countProc = document.getElementById('countProc');
    const countDone = document.getElementById('countDone');

    function rupiah(n){
      n = Number(n || 0);
      return 'Rp ' + n.toLocaleString('id-ID');
    }

    function escapeHtml(str){
      return String(str ?? '')
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'","&#039;");
    }

    function renderCard(sale){
      const items = (sale.items || []).map(it => {
        const name = it.product?.name ?? ('Product#' + it.product_id);
        return `<li class="flex justify-between gap-2">
          <span class="text-white/90">${escapeHtml(name)}</span>
          <span class="text-white/70">x${it.qty}</span>
        </li>`;
      }).join('');

      const invoice = sale.invoice_no ?? ('#' + sale.id);
      const cashier = sale.cashier?.name ?? '-';
      const time = (sale.created_at ?? '').replace('T',' ').slice(0,16);

      let buttons = '';
      if (sale.kitchen_status === 'new') {
        buttons = `
          <button type="button" data-action="process" data-id="${sale.id}"
            class="rounded-xl bg-amber-400/20 border border-amber-200/30 px-3 py-2 text-xs font-semibold hover:bg-amber-400/25">
            Proses
          </button>`;
      } else if (sale.kitchen_status === 'processing') {
        buttons = `
          <button type="button" data-action="done" data-id="${sale.id}"
            class="rounded-xl bg-emerald-400/20 border border-emerald-200/30 px-3 py-2 text-xs font-semibold hover:bg-emerald-400/25">
            Selesai
          </button>`;
      }

      return `
        <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-2xl">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-semibold">${escapeHtml(invoice)}</div>
              <div class="text-xs text-white/70">${escapeHtml(time)} • Kasir: ${escapeHtml(cashier)}</div>
            </div>
            <div class="text-sm font-semibold">${rupiah(sale.total_amount)}</div>
          </div>

          <ul class="mt-3 space-y-1 text-sm">
            ${items || '<li class="text-white/70">Tidak ada item</li>'}
          </ul>

          <div class="mt-3 flex items-center justify-end gap-2">
            ${buttons}
          </div>
        </div>
      `;
    }

    async function loadOrders(){
      const res = await fetch("{{ route('kitchen.orders') }}", { headers: { 'Accept': 'application/json' }});
      const data = await res.json();

      lastUpdate.textContent = (data.now || '-');

      const sales = data.sales || [];
      const groupNew = sales.filter(s => (s.kitchen_status || 'new') === 'new');
      const groupProc = sales.filter(s => s.kitchen_status === 'processing');
      const groupDone = sales.filter(s => s.kitchen_status === 'done');

      countNew.textContent = groupNew.length;
      countProc.textContent = groupProc.length;
      countDone.textContent = groupDone.length;

      colNew.innerHTML = groupNew.map(renderCard).join('');
      colProc.innerHTML = groupProc.map(renderCard).join('');
      colDone.innerHTML = groupDone.map(renderCard).join('');
    }

    // action buttons (submit hidden form)
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-action]');
      if(!btn) return;

      const action = btn.getAttribute('data-action');
      const id = btn.getAttribute('data-id');

      const form = document.getElementById('actionForm');
      if(action === 'process'){
        form.action = "{{ url('/kitchen/orders') }}/" + id + "/process";
      } else {
        form.action = "{{ url('/kitchen/orders') }}/" + id + "/done";
      }
      form.submit();
    });

    loadOrders();
    setInterval(loadOrders, 4000); // polling tiap 4 detik
  </script>
@endsection