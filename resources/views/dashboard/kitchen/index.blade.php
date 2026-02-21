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
    {{-- Filters --}}
    <div class="mt-4 rounded-2xl border border-slate-200/70 bg-white/60 p-3 shadow-sm backdrop-blur-2xl">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
          <div>
            <label class="text-xs text-slate-600">Filter Tipe</label>
            <select id="filterType"
              class="mt-1 w-full rounded-xl border border-slate-200/70 bg-white/70 px-3 py-2 text-sm outline-none">
              <option value="all">Semua</option>
              <option value="dine_in">Dine In</option>
              <option value="takeaway">Take Away</option>
            </select>
          </div>

          <div>
            <label class="text-xs text-slate-600">Filter Meja</label>
            <select id="filterTable"
              class="mt-1 w-full rounded-xl border border-slate-200/70 bg-white/70 px-3 py-2 text-sm outline-none">
              <option value="all">Semua Meja</option>
            </select>
          </div>

          <div>
            <label class="text-xs text-slate-600">Sort</label>
            <select id="sortMode"
              class="mt-1 w-full rounded-xl border border-slate-200/70 bg-white/70 px-3 py-2 text-sm outline-none">
              <option value="time">Waktu</option>
              <option value="table_time">Meja → Waktu</option>
            </select>
          </div>
        </div>

        <div class="text-xs text-slate-500">
          Tips: pilih “Meja” hanya berlaku untuk Dine In.
        </div>
      </div>
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
        <p class="mt-1 text-xs text-slate-500">READY + DELIVERED</p>
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
    const SLA_GREEN_MIN = 5;
    const SLA_YELLOW_MIN = 10;

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

    const filterType = document.getElementById('filterType');
    const filterTable = document.getElementById('filterTable');
    const sortMode = document.getElementById('sortMode');

    // ==========================
    // SOUND (needs user interaction)
    // ==========================
    let audioCtx = null;
    let soundEnabled = (localStorage.getItem('kds_sound_enabled') === '1');

    async function ensureAudioReady() {
      try {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') await audioCtx.resume();
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
      } catch (e) { }
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
      return [...arr].sort((a, b) => (getDate(a)?.getTime?.() ?? 0) - (getDate(b)?.getTime?.() ?? 0));
    }
    function sortByDateDesc(arr, getDate) {
      return [...arr].sort((a, b) => (getDate(b)?.getTime?.() ?? 0) - (getDate(a)?.getTime?.() ?? 0));
    }

    function buildQueueMapByCreatedAt(sales) {
      const sorted = sortByDateAsc(sales, (s) => parseISOToDate(s.created_at));
      const map = new Map();
      sorted.forEach((s, idx) => map.set(s.id, idx + 1));
      return map;
    }

    function getOrderType(sale) {
      return (sale.order_type || 'takeaway');
    }
    function getTableName(sale) {
      return sale?.dining_table?.name || '';
    }

    // ==========================
    // FILTER UI helpers
    // ==========================
    function getCurrentFilters() {
      return {
        type: filterType?.value || 'all',
        table: filterTable?.value || 'all',
        sort: sortMode?.value || 'time',
      };
    }

    function applyFilters(sales) {
      const f = getCurrentFilters();
      return (sales || []).filter(s => {
        const type = getOrderType(s);
        const table = String(s.dining_table_id ?? '');

        if (f.type !== 'all' && type !== f.type) return false;
        if (f.table !== 'all') {
          if (f.table === '__TA__') {
            if (type !== 'takeaway') return false;
          } else {
            if (type !== 'dine_in') return false;
            if (table !== f.table) return false;
          }
        }
        return true;
      });
    }

    function rebuildTableFilterOptions(sales) {
      if (!filterTable) return;

      const current = filterTable.value || 'all';
      const map = new Map();

      for (const s of (sales || [])) {
        if (getOrderType(s) === 'dine_in' && s.dining_table_id) {
          const id = String(s.dining_table_id);
          const name = getTableName(s) || ('Meja ' + id);
          map.set(id, name);
        }
      }

      const options = [];
      options.push({ value: 'all', label: 'Semua Meja' });
      options.push({ value: '__TA__', label: 'Take Away Saja' });

      const sorted = [...map.entries()].sort((a, b) => a[1].localeCompare(b[1], 'id'));
      for (const [id, name] of sorted) options.push({ value: id, label: name });

      filterTable.innerHTML = options.map(o => `<option value="${escapeHtml(o.value)}">${escapeHtml(o.label)}</option>`).join('');

      const stillExists = options.some(o => o.value === current);
      filterTable.value = stillExists ? current : 'all';
    }

    // ==========================
    // RENDER
    // ==========================
    function renderOrderTypeBadge(sale) {
      const type = getOrderType(sale);

      if (type === 'dine_in') {
        const tname = getTableName(sale) || ('Meja ' + (sale.dining_table_id ?? '-'));
        return `
          <span class="inline-flex items-center gap-2 rounded-xl border border-sky-200/70 bg-sky-200/40 px-3 py-1 text-xs font-semibold text-sky-900">
            🍽️ DINE IN • ${escapeHtml(tname)}
          </span>
        `;
      }

      return `
        <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200/70 bg-slate-900/5 px-3 py-1 text-xs font-semibold text-slate-700">
          🥡 TAKE AWAY
        </span>
      `;
    }

    function renderKitchenStatusBadge(sale) {
      const st = sale.kitchen_status || 'new';
      if (st === 'delivered') {
        return `
          <span class="inline-flex items-center gap-2 rounded-xl border border-emerald-200/70 bg-emerald-200/40 px-3 py-1 text-xs font-semibold text-emerald-900">
            🚚 DELIVERED
          </span>
        `;
      }
      if (st === 'done') {
        return `
          <span class="inline-flex items-center gap-2 rounded-xl border border-amber-200/70 bg-amber-200/40 px-3 py-1 text-xs font-semibold text-amber-900">
            ✅ READY
          </span>
        `;
      }
      return '';
    }

    function renderCard(sale, queueNo) {
      const now = new Date();
      const createdAt = parseISOToDate(sale.created_at);
      const startedAt = parseISOToDate(sale.kitchen_started_at);
      const doneAt = parseISOToDate(sale.kitchen_done_at);
      const deliveredAt = parseISOToDate(sale.delivered_at);

      const ageSec = diffSeconds(createdAt, now);
      const ageMin = Math.floor(ageSec / 60);

      let cookSec = 0;
      if (sale.kitchen_status === 'processing' && startedAt) {
        cookSec = diffSeconds(startedAt, now);
      } else if ((sale.kitchen_status === 'done' || sale.kitchen_status === 'delivered') && startedAt && doneAt) {
        cookSec = diffSeconds(startedAt, doneAt);
      }

      const invoice = sale.invoice_no ?? ('#' + sale.id);
      const cashier = sale.cashier?.name ?? '-';
      const timeText = (sale.created_at ?? '').replace('T', ' ').slice(0, 16);
      const queueLabel = `Q-${pad3(queueNo || 0)}`;

const itemsHtml = (sale.items || []).map((it) => {
  const name = it.product?.name ?? ('Product#' + it.product_id);
  const note = (it.note || '').trim();

  return `<li class="flex justify-between gap-3">
    <div class="min-w-0">
      <div class="text-slate-900">${escapeHtml(name)}</div>
      ${note ? `<div class="mt-0.5 text-xs text-slate-500">📝 ${escapeHtml(note)}</div>` : ``}
    </div>
    <div class="shrink-0 text-slate-500">x${it.qty}</div>
  </li>`;
}).join('') || `<li class="text-slate-500">Tidak ada item</li>`;

      // Border:
      // delivered => emerald, done => amber, others => SLA
      let borderClass = '';
      if (sale.kitchen_status === 'delivered') borderClass = 'border-l-4 border-emerald-400';
      else if (sale.kitchen_status === 'done') borderClass = 'border-l-4 border-amber-400';
      else borderClass = slaBorderClassByMinutes(ageMin);

      let timerBadge = '';
      if (sale.kitchen_status === 'new' || sale.kitchen_status === 'processing') {
        timerBadge = `<span class="inline-flex items-center gap-2 rounded-xl border px-3 py-1 text-xs font-semibold ${slaPillClassByMinutes(ageMin)}">
          ⏱ ${formatDuration(ageSec)}
        </span>`;
      } else if ((sale.kitchen_status === 'done' || sale.kitchen_status === 'delivered') && cookSec > 0) {
        timerBadge = `<span class="inline-flex items-center gap-2 rounded-xl border border-slate-200/70 bg-slate-900/5 px-3 py-1 text-xs font-semibold text-slate-700">
          🍳 Cook ${formatDuration(cookSec)}
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
      // done/delivered: tidak ada tombol di kitchen

      const orderTypeBadge = renderOrderTypeBadge(sale);
      const kitchenStatusBadge = renderKitchenStatusBadge(sale);

      return `
        <div class="rounded-2xl border border-slate-200/70 bg-white/60 p-4 shadow-sm backdrop-blur-2xl ${borderClass}">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="flex flex-wrap items-center gap-2">
                <div class="text-xl font-extrabold tracking-tight text-slate-900">${escapeHtml(queueLabel)}</div>
                ${timerBadge}
                ${orderTypeBadge}
                ${kitchenStatusBadge}
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

    function sortGroup(group, which) {
      const f = getCurrentFilters();

      if (f.sort === 'table_time') {
        const getT = s => (getOrderType(s) === 'dine_in' ? (getTableName(s) || '') : '~~~TA');
        const getTime = which === 'done'
          ? (s => parseISOToDate(s.delivered_at) || parseISOToDate(s.kitchen_done_at) || parseISOToDate(s.created_at))
          : which === 'proc'
            ? (s => parseISOToDate(s.kitchen_started_at) || parseISOToDate(s.created_at))
            : (s => parseISOToDate(s.created_at));

        return [...group].sort((a, b) => {
          const ta = getT(a).toLowerCase();
          const tb = getT(b).toLowerCase();
          if (ta < tb) return -1;
          if (ta > tb) return 1;

          const da = getTime(a)?.getTime?.() ?? 0;
          const db = getTime(b)?.getTime?.() ?? 0;

          // NEW/PROC: oldest first; DONE: newest first
          if (which === 'done') return db - da;
          return da - db;
        });
      }

      if (which === 'new') return sortByDateAsc(group, s => parseISOToDate(s.created_at));
      if (which === 'proc') return sortByDateAsc(group, s => parseISOToDate(s.kitchen_started_at) || parseISOToDate(s.created_at));
      return sortByDateDesc(group, s => parseISOToDate(s.delivered_at) || parseISOToDate(s.kitchen_done_at) || parseISOToDate(s.created_at));
    }

    function renderFromCache() {
      rebuildTableFilterOptions(cachedSales);

      const filtered = applyFilters(cachedSales);
      const queueMap = buildQueueMapByCreatedAt(filtered);

      const groupNewRaw = filtered.filter(s => (s.kitchen_status || 'new') === 'new');
      const groupProcRaw = filtered.filter(s => s.kitchen_status === 'processing');

      // ✅ Selesai = done + delivered
      const groupDoneRaw = filtered.filter(s => s.kitchen_status === 'done' || s.kitchen_status === 'delivered');

      const groupNew = sortGroup(groupNewRaw, 'new');
      const groupProc = sortGroup(groupProcRaw, 'proc');
      const groupDone = sortGroup(groupDoneRaw, 'done');

      countNew.textContent = groupNew.length;
      countProc.textContent = groupProc.length;
      countDone.textContent = groupDone.length;

      colNew.innerHTML = groupNew.map(s => renderCard(s, queueMap.get(s.id) || 0)).join('');
      colProc.innerHTML = groupProc.map(s => renderCard(s, queueMap.get(s.id) || 0)).join('');
      colDone.innerHTML = groupDone.map(s => renderCard(s, queueMap.get(s.id) || 0)).join('');
    }

    async function loadOrders() {
      const res = await fetch("{{ route('kitchen.orders') }}", { headers: { 'Accept': 'application/json' } });
      const data = await res.json();

      lastUpdate.textContent = (data.now || '-');
      cachedSales = data.sales || [];

      // ping jika ada NEW order baru (berdasarkan data mentah)
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
    // FILTER events
    // ==========================
    [filterType, filterTable, sortMode].forEach(el => {
      if (!el) return;
      el.addEventListener('change', () => renderFromCache());
    });

    // ==========================
    // ACTION BUTTONS (kitchen)
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
    setInterval(loadOrders, 4000);
    setInterval(renderFromCache, 1000);
  </script>
@endsection