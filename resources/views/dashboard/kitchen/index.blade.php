@extends('layouts.kitchen')
@section('title', 'Kitchen Display')

@section('body')
  {{-- HEADER --}}
  <div class="premium-card p-6 lg:p-8 animate-fade-up">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between relative z-10">
      <div class="min-w-0">
        <div class="inline-flex items-center gap-2 rounded-full border border-accent-gold/20 bg-accent-gold/5 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-accent-gold mb-4">
          <span class="h-1.5 w-1.5 rounded-full bg-accent-gold animate-pulse shadow-[0_0_8px_rgba(234,179,8,0.5)]"></span>
          Kitchen • Live Queue
        </div>

        <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
          Kitchen Display
        </h1>
        <p class="mt-2 text-sm text-white/40 max-w-xl">
          Antrean pesanan masuk dari kasir secara real-time. Kelola efisiensi dapur dengan filter tipe dan nomor meja.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <button id="btnEnableSound" type="button"
          class="btn-premium-glass text-xs px-6 py-3 font-black uppercase tracking-widest">
          🔊 Enable Sound
        </button>

        <div class="glass-panel rounded-2xl px-5 py-3 text-xs border-white/5">
          <span class="text-white/20 font-black uppercase tracking-widest mr-2">Update:</span>
          <span id="lastUpdate" class="font-bold text-accent-gold">-</span>
        </div>
      </div>
    </div>

    {{-- FILTERS --}}
    <div class="mt-8 pt-8 border-t border-white/5 relative z-10">
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="space-y-2">
          <label class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 px-1">Filter Tipe</label>
          <select id="filterType"
            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-accent-gold/50 transition-colors cursor-pointer">
            <option value="all" class="bg-obsidian-950">Semua Tipe</option>
            <option value="dine_in" class="bg-obsidian-950">🍽️ Dine In</option>
            <option value="takeaway" class="bg-obsidian-950">🥡 Take Away</option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 px-1">Filter Meja</label>
          <select id="filterTable"
            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-accent-gold/50 transition-colors cursor-pointer">
            <option value="all" class="bg-obsidian-950">Semua Meja</option>
          </select>
        </div>

        <div class="space-y-2">
          <label class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 px-1">Urutan</label>
          <select id="sortMode"
            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-accent-gold/50 transition-colors cursor-pointer">
            <option value="time" class="bg-obsidian-950">⏱️ Berdasarkan Waktu</option>
            <option value="table_time" class="bg-obsidian-950">🪑 Meja &rarr; Waktu</option>
          </select>
        </div>

        <div class="flex items-end">
          <div class="w-full rounded-2xl border border-white/5 bg-white/[0.02] p-4 text-[10px] leading-relaxed text-white/30 font-medium italic">
            Tips: Gunakan filter meja untuk memprioritaskan pesanan <span class="text-accent-gold font-bold">Dine In</span> selama jam sibuk.
          </div>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="mt-6 rounded-2xl border border-accent-gold/20 bg-accent-gold/5 px-6 py-4 text-sm font-bold text-accent-gold flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ session('success') }}
      </div>
    @endif
  </div>

  {{-- COLUMNS --}}
  <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3 animate-fade-up stagger-1">
    {{-- NEW --}}
    <section class="space-y-6">
      <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
          <div class="w-1.5 h-6 bg-accent-gold rounded-full"></div>
          <h2 class="text-lg font-black uppercase tracking-widest text-white/60">Baru Masuk</h2>
        </div>
        <span id="countNew"
          class="bg-accent-gold text-black px-3 py-1 rounded-lg text-xs font-black shadow-lg shadow-accent-gold/10">0</span>
      </div>
      <div class="space-y-4" id="colNew"></div>
    </section>

    {{-- PROCESSING --}}
    <section class="space-y-6">
      <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
          <div class="w-1.5 h-6 bg-accent-blue rounded-full"></div>
          <h2 class="text-lg font-black uppercase tracking-widest text-white/60">Diproses</h2>
        </div>
        <span id="countProc"
          class="bg-accent-blue text-white px-3 py-1 rounded-lg text-xs font-black shadow-lg shadow-accent-blue/10">0</span>
      </div>
      <div class="space-y-4" id="colProc"></div>
    </section>

    {{-- DONE --}}
    <section class="space-y-6">
      <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
          <div class="w-1.5 h-6 bg-accent-emerald/40 rounded-full"></div>
          <h2 class="text-lg font-black uppercase tracking-widest text-white/30">Selesai</h2>
        </div>
        <span id="countDone"
          class="bg-white/10 text-white/40 px-3 py-1 rounded-lg text-xs font-black">0</span>
      </div>
      <div class="space-y-4 opacity-50" id="colDone"></div>
    </section>
  </div>

  <form id="actionForm" method="POST" class="hidden">
    @csrf
  </form>

  <script>
    // SLA CONFIG
    const SLA_GREEN_MIN = 5;
    const SLA_YELLOW_MIN = 10;

    // DOM
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

    // SOUND
    let audioCtx = null;
    let soundEnabled = (localStorage.getItem('kds_sound_enabled') === '1');

    async function ensureAudioReady() {
      try {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') await audioCtx.resume();
        return true;
      } catch (e) { return false; }
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
      btnEnableSound.innerHTML = soundEnabled ? '✅ <span class="ml-1">Sound Active</span>' : '🔊 <span class="ml-1">Enable Sound</span>';
      if(soundEnabled) btnEnableSound.classList.add('border-accent-gold/40', 'text-accent-gold');
    }

    updateSoundButton();

    if (btnEnableSound) {
      btnEnableSound.addEventListener('click', async () => {
        soundEnabled = true;
        localStorage.setItem('kds_sound_enabled', '1');
        updateSoundButton();
        await ensureAudioReady();
        ping();
      });
    }

    // UTILS
    function rupiah(n) { return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }
    function escapeHtml(str) { return String(str ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#039;"); }
    function parseISOToDate(iso) { if(!iso) return null; const d = new Date(iso); return isNaN(d.getTime()) ? null : d; }
    function diffSeconds(fromDate, toDate) { if(!fromDate || !toDate) return 0; return Math.max(0, Math.floor((toDate.getTime() - fromDate.getTime()) / 1000)); }
    function formatDuration(sec) {
      sec = Math.max(0, Number(sec || 0));
      const m = Math.floor(sec / 60);
      const s = sec % 60;
      if (m < 60) return `${m}m ${String(s).padStart(2, '0')}s`;
      const h = Math.floor(m / 60);
      const mm = m % 60;
      return `${h}h ${String(mm).padStart(2, '0')}m`;
    }
    function pad3(n) { return String(n).padStart(3, '0'); }

    function slaBorderClassByMinutes(min) {
      if (min < SLA_GREEN_MIN) return 'border-l-4 border-emerald-500/50';
      if (min < SLA_YELLOW_MIN) return 'border-l-4 border-accent-gold/50';
      return 'border-l-4 border-red-500/50 shadow-[inset_4px_0_15px_rgba(239,68,68,0.05)]';
    }

    function slaPillClassByMinutes(min) {
      if (min < SLA_GREEN_MIN) return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
      if (min < SLA_YELLOW_MIN) return 'bg-accent-gold/10 text-accent-gold border-accent-gold/20';
      return 'bg-red-500/10 text-red-400 border-red-500/20 animate-pulse';
    }

    function sortByDateAsc(arr, getDate) { return [...arr].sort((a, b) => (getDate(a)?.getTime?.() ?? 0) - (getDate(b)?.getTime?.() ?? 0)); }
    function sortByDateDesc(arr, getDate) { return [...arr].sort((a, b) => (getDate(b)?.getTime?.() ?? 0) - (getDate(a)?.getTime?.() ?? 0)); }

    function buildQueueMapByCreatedAt(sales) {
      const sorted = sortByDateAsc(sales, (s) => parseISOToDate(s.created_at));
      const map = new Map();
      sorted.forEach((s, idx) => map.set(s.id, idx + 1));
      return map;
    }

    function getOrderType(sale) { return (sale.order_type || 'takeaway'); }
    function getTableName(sale) { return sale?.dining_table?.name || ''; }

    function getCurrentFilters() {
      return { type: filterType?.value || 'all', table: filterTable?.value || 'all', sort: sortMode?.value || 'time' };
    }

    function applyFilters(sales) {
      const f = getCurrentFilters();
      return (sales || []).filter(s => {
        const type = getOrderType(s);
        const table = String(s.dining_table_id ?? '');
        if (f.type !== 'all' && type !== f.type) return false;
        if (f.table !== 'all') {
          if (f.table === '__TA__') { if (type !== 'takeaway') return false; }
          else { if (type !== 'dine_in' || table !== f.table) return false; }
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
      const options = [{ value: 'all', label: 'Semua Meja' }, { value: '__TA__', label: 'Take Away Saja' }];
      const sorted = [...map.entries()].sort((a, b) => a[1].localeCompare(b[1], 'id'));
      for (const [id, name] of sorted) options.push({ value: id, label: name });
      filterTable.innerHTML = options.map(o => `<option value="${escapeHtml(o.value)}" class="bg-obsidian-950">${escapeHtml(o.label)}</option>`).join('');
      filterTable.value = options.some(o => o.value === current) ? current : 'all';
    }

    function renderOrderTypeBadge(sale) {
      const type = getOrderType(sale);
      if (type === 'dine_in') {
        const tname = getTableName(sale) || ('Meja ' + (sale.dining_table_id ?? '-'));
        return `<span class="inline-flex items-center gap-2 rounded-xl border border-accent-blue/20 bg-accent-blue/10 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-accent-blue">🍽️ ${escapeHtml(tname)}</span>`;
      }
      return `<span class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white/40">🥡 TAKE AWAY</span>`;
    }

    function renderKitchenStatusBadge(sale) {
      const st = sale.kitchen_status || 'new';
      if (st === 'delivered') return `<span class="inline-flex items-center gap-2 rounded-xl border border-white/5 bg-white/5 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white/20">Diterima</span>`;
      if (st === 'done') return `<span class="inline-flex items-center gap-2 rounded-xl border border-accent-gold/20 bg-accent-gold/10 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-accent-gold">Siap</span>`;
      return '';
    }

    function renderCard(sale, queueNo) {
      const now = new Date();
      const createdAt = parseISOToDate(sale.created_at);
      const startedAt = parseISOToDate(sale.kitchen_started_at);
      const doneAt = parseISOToDate(sale.kitchen_done_at);
      const ageSec = diffSeconds(createdAt, now);
      const ageMin = Math.floor(ageSec / 60);

      let cookSec = 0;
      if (sale.kitchen_status === 'processing' && startedAt) cookSec = diffSeconds(startedAt, now);
      else if ((sale.kitchen_status === 'done' || sale.kitchen_status === 'delivered') && startedAt && doneAt) cookSec = diffSeconds(startedAt, doneAt);

      const invoice = sale.invoice_no ?? ('#' + sale.id);
      const cashier = sale.cashier?.name ?? '-';
      const timeText = (sale.created_at ?? '').replace('T', ' ').slice(11, 16);
      const queueLabel = `Q-${pad3(queueNo || 0)}`;

      const itemsHtml = (sale.items || []).map((it) => {
        const name = it.product?.name ?? ('Product#' + it.product_id);
        const note = (it.note || '').trim();
        const cooked = Number(it.kitchen_cooked_qty || 0);
        const qty = Number(it.qty || 0);
        const startedAtItem = parseISOToDate(it.kitchen_started_at);
        const doneAtItem = parseISOToDate(it.kitchen_done_at);

        let itemSec = 0;
        if (startedAtItem && !doneAtItem) itemSec = diffSeconds(startedAtItem, new Date());
        if (startedAtItem && doneAtItem) itemSec = diffSeconds(startedAtItem, doneAtItem);

        const timeBadge = doneAtItem
          ? `<span class="ml-2 inline-flex items-center rounded-lg bg-white/5 border border-white/10 px-2 py-0.5 text-[10px] font-bold text-accent-gold/60">⏱ ${formatDuration(itemSec)}</span>`
          : '';

        if (qty <= 1) {
          const checked = cooked >= 1;
          return `
            <li class="flex items-start justify-between gap-3 p-2 rounded-xl transition-colors ${checked ? 'bg-white/[0.02]' : 'hover:bg-white/[0.03]'}">
              <button type="button" class="flex min-w-0 items-start gap-3 text-left group" data-item-action="${checked ? 'uncook' : 'cook'}" data-item-id="${it.id}">
                <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-lg border transition-all
                  ${checked ? 'bg-accent-gold border-accent-gold text-black shadow-lg shadow-accent-gold/20' : 'bg-white/5 border-white/10 group-hover:border-accent-gold/50'}">
                  ${checked ? '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>' : ''}
                </span>
                <div class="min-w-0">
                  <div class="text-sm font-bold ${checked ? 'text-white/20 line-through' : 'text-white/80'}">
                    ${escapeHtml(name)} ${timeBadge}
                  </div>
                  ${note ? `<div class="mt-1 text-[11px] font-medium text-accent-amber/50 leading-tight">📝 ${escapeHtml(note)}</div>` : ``}
                </div>
              </button>
            </li>
          `;
        }

        const boxes = Array.from({ length: qty }).map((_, idx) => {
          const done = (idx + 1) <= cooked;
          return `<button type="button" data-item-action="${done ? 'uncook' : 'cook'}" data-item-id="${it.id}"
            class="h-7 w-7 rounded-lg border text-[10px] font-black transition-all
            ${done ? 'bg-accent-gold border-accent-gold text-black shadow-lg shadow-accent-gold/20' : 'bg-white/5 border-white/10 hover:border-accent-gold/50'}">
            ${done ? '✓' : (idx + 1)}
          </button>`;
        }).join('');

        const allDone = cooked >= qty;
        return `
          <li class="p-2 space-y-3">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-bold ${allDone ? 'text-white/20 line-through' : 'text-white/80'}">
                  ${escapeHtml(name)} ${timeBadge}
                </div>
                ${note ? `<div class="mt-1 text-[11px] font-medium text-accent-amber/50 leading-tight">📝 ${escapeHtml(note)}</div>` : ``}
              </div>
              <div class="shrink-0 text-xs font-black text-white/20 uppercase tracking-widest">x${qty}</div>
            </div>
            <div class="flex flex-wrap gap-1.5">${boxes}</div>
          </li>
        `;
      }).join('') || `<li class="text-white/10 text-xs italic p-4 text-center">Tidak ada item</li>`;

      let borderClass = (sale.kitchen_status === 'delivered') ? 'border-l-4 border-white/10'
                        : (sale.kitchen_status === 'done') ? 'border-l-4 border-accent-gold/40'
                        : slaBorderClassByMinutes(ageMin);

      let timerBadge = '';
      if (sale.kitchen_status === 'new' || sale.kitchen_status === 'processing') {
        timerBadge = `<span class="inline-flex items-center gap-2 rounded-xl border px-3 py-1 text-[10px] font-black uppercase tracking-widest ${slaPillClassByMinutes(ageMin)}">⏱ ${formatDuration(ageSec)}</span>`;
      } else if ((sale.kitchen_status === 'done' || sale.kitchen_status === 'delivered') && cookSec > 0) {
        timerBadge = `<span class="inline-flex items-center gap-2 rounded-xl border border-white/5 bg-white/[0.03] px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white/30 italic">🍳 ${formatDuration(cookSec)}</span>`;
      }

      let buttons = '';
      if (sale.kitchen_status === 'new') {
        buttons = `<button type="button" data-action="process" data-id="${sale.id}"
          class="btn-premium-primary py-2.5 px-6 text-[10px] uppercase tracking-widest font-black">Mulai Masak</button>`;
      } else if (sale.kitchen_status === 'processing') {
        buttons = `<button type="button" data-action="done" data-id="${sale.id}"
          class="btn-premium-glass bg-accent-gold/10 text-accent-gold border-accent-gold/20 hover:bg-accent-gold hover:text-black py-2.5 px-6 text-[10px] uppercase tracking-widest font-black">Tandai Selesai</button>`;
      }

      return `
        <div class="premium-card p-5 border-white/5 hover:border-white/10 transition-colors ${borderClass}">
          <div class="flex items-start justify-between gap-4 mb-4">
            <div class="min-w-0">
              <div class="flex flex-wrap items-center gap-2 mb-3">
                <div class="text-2xl font-black tracking-tighter text-white">${escapeHtml(queueLabel)}</div>
                ${timerBadge}
              </div>
              <div class="flex flex-wrap gap-2 mb-2">
                ${renderOrderTypeBadge(sale)}
                ${renderKitchenStatusBadge(sale)}
              </div>
              <div class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20">
                ${escapeHtml(invoice)} &bull; ${timeText} &bull; ${escapeHtml(cashier)}
              </div>
            </div>
            <div class="shrink-0 text-xs font-black text-accent-gold/40 tracking-tight">${rupiah(sale.total_amount)}</div>
          </div>

          <div class="bg-white/[0.01] rounded-2xl border border-white/5 overflow-hidden">
            <ul class="divide-y divide-white/5">${itemsHtml}</ul>
          </div>

          <div class="mt-4 flex items-center justify-end gap-2">${buttons}</div>
        </div>
      `;
    }

    // STATE
    let initialLoaded = false;
    let lastNewIds = new Set();
    let cachedSales = [];

    function sortGroup(group, which) {
      const f = getCurrentFilters();
      if (f.sort === 'table_time') {
        const getT = s => (getOrderType(s) === 'dine_in' ? (getTableName(s) || '') : '~~~TA');
        const getTime = which === 'done' ? (s => parseISOToDate(s.delivered_at) || parseISOToDate(s.kitchen_done_at) || parseISOToDate(s.created_at))
                        : which === 'proc' ? (s => parseISOToDate(s.kitchen_started_at) || parseISOToDate(s.created_at))
                        : (s => parseISOToDate(s.created_at));
        return [...group].sort((a, b) => {
          const ta = getT(a).toLowerCase(); const tb = getT(b).toLowerCase();
          if (ta < tb) return -1; if (ta > tb) return 1;
          const da = getTime(a)?.getTime?.() ?? 0; const db = getTime(b)?.getTime?.() ?? 0;
          return (which === 'done') ? db - da : da - db;
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
      const groupDoneRaw = filtered.filter(s => s.kitchen_status === 'done' || s.kitchen_status === 'delivered');
      const groupNew = sortGroup(groupNewRaw, 'new');
      const groupProc = sortGroup(groupProcRaw, 'proc');
      const groupDone = sortGroup(groupDoneRaw, 'done');
      countNew.textContent = groupNew.length; countProc.textContent = groupProc.length; countDone.textContent = groupDone.length;
      colNew.innerHTML = groupNew.map(s => renderCard(s, queueMap.get(s.id) || 0)).join('');
      colProc.innerHTML = groupProc.map(s => renderCard(s, queueMap.get(s.id) || 0)).join('');
      colDone.innerHTML = groupDone.map(s => renderCard(s, queueMap.get(s.id) || 0)).join('');
    }

    async function loadOrders() {
      try {
        const res = await fetch("{{ route('kitchen.orders') }}", { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        lastUpdate.textContent = (data.now || '-');
        cachedSales = data.sales || [];
        const groupNew = cachedSales.filter(s => (s.kitchen_status || 'new') === 'new');
        const newIdsNow = new Set(groupNew.map(s => s.id));
        if (initialLoaded) {
          let hasNew = false; for (const id of newIdsNow) { if (!lastNewIds.has(id)) { hasNew = true; break; } }
          if (hasNew) ping();
        }
        lastNewIds = newIdsNow; initialLoaded = true;
        renderFromCache();
      } catch(e) {}
    }

    [filterType, filterTable, sortMode].forEach(el => { if (el) el.addEventListener('change', () => renderFromCache()); });

    async function postKitchen(url) {
      const token = document.querySelector('#actionForm input[name=_token]')?.value;
      const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } });
      return res.ok;
    }

    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('button[data-item-action][data-item-id]');
      if (!btn) return;
      const action = btn.getAttribute('data-item-action'); const id = btn.getAttribute('data-item-id');
      btn.disabled = true;
      const url = action === 'cook' ? `{{ url('/kitchen/items') }}/${id}/cook` : `{{ url('/kitchen/items') }}/${id}/uncook`;
      const ok = await postKitchen(url); btn.disabled = false;
      if (ok) await loadOrders();
    });

    document.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-action]'); if (!btn) return;
      const action = btn.getAttribute('data-action'); const id = btn.getAttribute('data-id');
      if (!id) return;
      const form = document.getElementById('actionForm'); if (!form) return;
      form.action = action === 'process' ? "{{ url('/kitchen/orders') }}/" + id + "/process" : "{{ url('/kitchen/orders') }}/" + id + "/done";
      form.submit();
    });

    loadOrders(); setInterval(loadOrders, 4000); setInterval(renderFromCache, 1000);
  </script>
@endsection