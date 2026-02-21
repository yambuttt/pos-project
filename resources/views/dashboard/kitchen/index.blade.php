@extends('layouts.kitchen')
@section('title', 'Kitchen Display')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl font-semibold">Kitchen Display</h1>
      <p class="text-sm text-slate-600">Antrian pesanan masuk dari kasir (real-time).</p>
    </div>
    <button id="btnEnableSound" type="button"
      class="rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-2 text-sm shadow-sm backdrop-blur-2xl hover:bg-white/80">
      🔊 Enable Sound
    </button>

    <div class="rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-2 text-sm shadow-sm backdrop-blur-2xl">
      <span class="text-slate-500">Update:</span> <span id="lastUpdate" class="font-semibold">-</span>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-slate-200/70 bg-white/60 px-4 py-3 text-sm shadow-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-3">
    {{-- NEW --}}
    <div class="rounded-[26px] border border-slate-200/70 bg-white/55 p-4 shadow-sm backdrop-blur-2xl">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Baru Masuk</h2>
        <span id="countNew" class="rounded-xl bg-slate-900/5 px-3 py-1 text-sm text-slate-700">0</span>
      </div>
      <div id="colNew" class="mt-3 space-y-3"></div>
    </div>

    {{-- PROCESSING --}}
    <div class="rounded-[26px] border border-slate-200/70 bg-white/55 p-4 shadow-sm backdrop-blur-2xl">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Sedang Diproses</h2>
        <span id="countProc" class="rounded-xl bg-slate-900/5 px-3 py-1 text-sm text-slate-700">0</span>
      </div>
      <div id="colProc" class="mt-3 space-y-3"></div>
    </div>

    {{-- DONE --}}
    <div class="rounded-[26px] border border-slate-200/70 bg-white/55 p-4 shadow-sm backdrop-blur-2xl">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Selesai</h2>
        <span id="countDone" class="rounded-xl bg-slate-900/5 px-3 py-1 text-sm text-slate-700">0</span>
      </div>
      <div id="colDone" class="mt-3 space-y-3"></div>
    </div>
  </div>

  <form id="actionForm" method="POST" class="hidden">
    @csrf
  </form>

<script>
  // ==========================
  // CONFIG SLA (menit)
  // ==========================
  const SLA_GREEN_MIN = 5;   // < 5 menit
  const SLA_YELLOW_MIN = 10; // 5-10 menit
  // >= 10 menit = merah

  // ==========================
  // DOM
  // ==========================
  const colNew = document.getElementById('colNew');
  const colProc = document.getElementById('colProc');
  const colDone = document.getElementById('colDone');
  const lastUpdate = document.getElementById('lastUpdate');

  const countNew = document.getElementById('countNew');
  const countProc = document.getElementById('countProc');
  const countDone = document.getElementById('countDone');

  const btnEnableSound = document.getElementById('btnEnableSound');

  // Guard kalau ada element yang belum ada
  if (!colNew || !colProc || !colDone || !lastUpdate || !countNew || !countProc || !countDone) {
    console.error('KDS: DOM element tidak lengkap. Pastikan id colNew/colProc/colDone/count*/lastUpdate ada.');
  }

  // ==========================
  // SOUND (needs user interaction)
  // ==========================
  let audioCtx = null;
  let soundEnabled = (localStorage.getItem('kds_sound_enabled') === '1');

  async function ensureAudioReady() {
    try {
      if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      }
      if (audioCtx.state === 'suspended') {
        await audioCtx.resume();
      }
      return true;
    } catch (e) {
      return false;
    }
  }

  async function ping() {
    if (!soundEnabled) return;

    const ok = await ensureAudioReady();
    if (!ok) return;

    try {
      const o = audioCtx.createOscillator();
      const g = audioCtx.createGain();

      o.type = 'sine';
      o.frequency.value = 880;
      g.gain.value = 0.0001;

      o.connect(g);
      g.connect(audioCtx.destination);

      const now = audioCtx.currentTime;
      g.gain.exponentialRampToValueAtTime(0.12, now + 0.01);
      g.gain.exponentialRampToValueAtTime(0.0001, now + 0.20);

      o.start(now);
      o.stop(now + 0.22);
    } catch (e) {
      // ignore
    }
  }

  function updateSoundButton() {
    if (!btnEnableSound) return;
    btnEnableSound.textContent = soundEnabled ? '✅ Sound Enabled' : '🔊 Enable Sound';
  }

  updateSoundButton();

  if (btnEnableSound) {
    btnEnableSound.addEventListener('click', async () => {
      soundEnabled = true;
      localStorage.setItem('kds_sound_enabled', '1');
      updateSoundButton();
      await ensureAudioReady();
      ping(); // test
    });
  }

  // ==========================
  // UTIL
  // ==========================
  function rupiah(n) {
    n = Number(n || 0);
    return 'Rp ' + n.toLocaleString('id-ID');
  }

  function escapeHtml(str) {
    return String(str ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function parseISOToDate(iso) {
    if (!iso) return null;
    const d = new Date(iso);
    return isNaN(d.getTime()) ? null : d;
  }

  function diffSeconds(fromDate, toDate) {
    if (!fromDate || !toDate) return 0;
    return Math.max(0, Math.floor((toDate.getTime() - fromDate.getTime()) / 1000));
  }

  function formatDuration(sec) {
    sec = Math.max(0, Number(sec || 0));
    const m = Math.floor(sec / 60);
    const s = sec % 60;
    if (m < 60) return `${m}m ${String(s).padStart(2, '0')}s`;
    const h = Math.floor(m / 60);
    const mm = m % 60;
    return `${h}h ${String(mm).padStart(2, '0')}m`;
  }

  function pad3(n) {
    return String(n).padStart(3, '0');
  }

  function slaBorderClassByMinutes(min) {
    if (min < SLA_GREEN_MIN) return 'border-l-4 border-emerald-400';
    if (min < SLA_YELLOW_MIN) return 'border-l-4 border-amber-400';
    return 'border-l-4 border-rose-400';
  }

  function slaPillClassByMinutes(min) {
    if (min < SLA_GREEN_MIN) return 'bg-emerald-200/50 text-emerald-900 border-emerald-200/70';
    if (min < SLA_YELLOW_MIN) return 'bg-amber-200/50 text-amber-900 border-amber-200/70';
    return 'bg-rose-200/50 text-rose-900 border-rose-200/70';
  }

  function sortByDateAsc(arr, getDate) {
    return [...arr].sort((a, b) => {
      const da = getDate(a)?.getTime?.() ?? 0;
      const db = getDate(b)?.getTime?.() ?? 0;
      return da - db;
    });
  }

  function sortByDateDesc(arr, getDate) {
    return [...arr].sort((a, b) => {
      const da = getDate(a)?.getTime?.() ?? 0;
      const db = getDate(b)?.getTime?.() ?? 0;
      return db - da;
    });
  }

  function buildQueueMapByCreatedAt(sales) {
    const sorted = sortByDateAsc(sales, (s) => parseISOToDate(s.created_at));
    const map = new Map();
    sorted.forEach((s, idx) => map.set(s.id, idx + 1));
    return map;
  }

  // ==========================
  // RENDER
  // ==========================
  function renderCard(sale, queueNo) {
    const now = new Date();
    const createdAt = parseISOToDate(sale.created_at);
    const startedAt = parseISOToDate(sale.kitchen_started_at);
    const doneAt = parseISOToDate(sale.kitchen_done_at);

    // umur order = sejak dibuat
    const ageSec = diffSeconds(createdAt, now);
    const ageMin = Math.floor(ageSec / 60);

    // durasi masak (untuk done)
    let cookSec = 0;
    if (sale.kitchen_status === 'processing' && startedAt) {
      cookSec = diffSeconds(startedAt, now);
    } else if (sale.kitchen_status === 'done' && startedAt && doneAt) {
      cookSec = diffSeconds(startedAt, doneAt);
    }

    const invoice = sale.invoice_no ?? ('#' + sale.id);
    const cashier = sale.cashier?.name ?? '-';
    const timeText = (sale.created_at ?? '').replace('T', ' ').slice(0, 16);
    const queueLabel = `Q-${pad3(queueNo || 0)}`;

    const itemsHtml = (sale.items || []).map((it) => {
      const name = it.product?.name ?? ('Product#' + it.product_id);
      return `<li class="flex justify-between gap-2">
        <span class="text-slate-900">${escapeHtml(name)}</span>
        <span class="text-slate-500">x${it.qty}</span>
      </li>`;
    }).join('') || `<li class="text-slate-500">Tidak ada item</li>`;

    const borderClass = (sale.kitchen_status === 'done')
      ? 'border-l-4 border-slate-200'
      : slaBorderClassByMinutes(ageMin);

    let badge = '';
    if (sale.kitchen_status === 'new' || sale.kitchen_status === 'processing') {
      badge = `<span class="inline-flex items-center gap-2 rounded-xl border px-3 py-1 text-xs font-semibold ${slaPillClassByMinutes(ageMin)}">
        ⏱ ${formatDuration(ageSec)}
      </span>`;
    } else if (sale.kitchen_status === 'done' && cookSec > 0) {
      badge = `<span class="inline-flex items-center gap-2 rounded-xl border border-slate-200/70 bg-slate-900/5 px-3 py-1 text-xs font-semibold text-slate-700">
        ✅ Cook ${formatDuration(cookSec)}
      </span>`;
    }

    let buttons = '';
    if (sale.kitchen_status === 'new') {
      buttons = `
        <button type="button" data-action="process" data-id="${sale.id}"
          class="rounded-xl border border-amber-200/70 bg-amber-200/40 px-3 py-2 text-xs font-semibold text-amber-900 hover:bg-amber-200/55 backdrop-blur-2xl">
          Proses
        </button>`;
    } else if (sale.kitchen_status === 'processing') {
      buttons = `
        <button type="button" data-action="done" data-id="${sale.id}"
          class="rounded-xl border border-emerald-200/70 bg-emerald-200/40 px-3 py-2 text-xs font-semibold text-emerald-900 hover:bg-emerald-200/55 backdrop-blur-2xl">
          Selesai
        </button>`;
    }

    return `
      <div class="rounded-2xl border border-slate-200/70 bg-white/60 p-4 shadow-sm backdrop-blur-2xl ${borderClass}">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <div class="text-xl font-extrabold tracking-tight text-slate-900">${escapeHtml(queueLabel)}</div>
              ${badge}
            </div>
            <div class="mt-1 text-sm font-semibold text-slate-900">${escapeHtml(invoice)}</div>
            <div class="text-xs text-slate-500">${escapeHtml(timeText)} • Kasir: ${escapeHtml(cashier)}</div>
          </div>
          <div class="shrink-0 text-sm font-semibold text-slate-900">${rupiah(sale.total_amount)}</div>
        </div>

        <ul class="mt-3 space-y-1 text-sm">
          ${itemsHtml}
        </ul>

        <div class="mt-3 flex items-center justify-end gap-2">
          ${buttons}
        </div>
      </div>
    `;
  }

  // ==========================
  // STATE + RENDER LOOP
  // ==========================
  let initialLoaded = false;
  let lastNewIds = new Set();

  let cachedSales = [];
  let cachedQueueMap = new Map();

  function renderFromCache() {
    const sales = cachedSales || [];
    cachedQueueMap = buildQueueMapByCreatedAt(sales);

    const groupNewRaw  = sales.filter(s => (s.kitchen_status || 'new') === 'new');
    const groupProcRaw = sales.filter(s => s.kitchen_status === 'processing');
    const groupDoneRaw = sales.filter(s => s.kitchen_status === 'done');

    // sorting rules
    const groupNew = sortByDateAsc(groupNewRaw, s => parseISOToDate(s.created_at));
    const groupProc = sortByDateAsc(groupProcRaw, s => parseISOToDate(s.kitchen_started_at) || parseISOToDate(s.created_at));
    const groupDone = sortByDateDesc(groupDoneRaw, s => parseISOToDate(s.kitchen_done_at) || parseISOToDate(s.created_at));

    countNew.textContent = groupNew.length;
    countProc.textContent = groupProc.length;
    countDone.textContent = groupDone.length;

    colNew.innerHTML  = groupNew.map(s => renderCard(s, cachedQueueMap.get(s.id) || 0)).join('');
    colProc.innerHTML = groupProc.map(s => renderCard(s, cachedQueueMap.get(s.id) || 0)).join('');
    colDone.innerHTML = groupDone.map(s => renderCard(s, cachedQueueMap.get(s.id) || 0)).join('');
  }

  async function loadOrders() {
    const res = await fetch("{{ route('kitchen.orders') }}", { headers: { 'Accept': 'application/json' }});
    const data = await res.json();

    lastUpdate.textContent = (data.now || '-');

    cachedSales = data.sales || [];

    // ping kalau ada NEW id baru setelah initial load
    const groupNew = cachedSales.filter(s => (s.kitchen_status || 'new') === 'new');
    const newIdsNow = new Set(groupNew.map(s => s.id));

    if (initialLoaded) {
      let hasNew = false;
      for (const id of newIdsNow) {
        if (!lastNewIds.has(id)) { hasNew = true; break; }
      }
      if (hasNew) ping();
    }

    lastNewIds = newIdsNow;
    initialLoaded = true;

    renderFromCache();
  }

  // ==========================
  // ACTION BUTTONS
  // ==========================
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;

    const action = btn.getAttribute('data-action');
    const id = btn.getAttribute('data-id');

    const form = document.getElementById('actionForm');
    if (!form) return;

    if (action === 'process') {
      form.action = "{{ url('/kitchen/orders') }}/" + id + "/process";
    } else {
      form.action = "{{ url('/kitchen/orders') }}/" + id + "/done";
    }
    form.submit();
  });

  // ==========================
  // START
  // ==========================
  loadOrders();
  setInterval(loadOrders, 4000);   // fetch data tiap 4 detik
  setInterval(renderFromCache, 1000); // update timer tiap 1 detik tanpa fetch
</script>
@endsection