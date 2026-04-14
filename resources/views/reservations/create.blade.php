<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reservasi</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#070708] text-white">
  <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(250,204,21,.14),transparent_28%),radial-gradient(circle_at_top_right,rgba(255,255,255,.08),transparent_24%),linear-gradient(to_bottom,#070708,#0a0a0d)]"></div>

  <div class="mx-auto max-w-6xl px-4 pb-28 pt-6 sm:px-5 lg:px-6">
    <div class="mb-5 flex items-center justify-between gap-3">
      <div class="min-w-0">
        <div class="text-[11px] uppercase tracking-[0.28em] text-white/55">Reservasi</div>
        <h1 class="mt-1 text-2xl font-bold tracking-tight">Booking Ruangan/Meja + Menu</h1>
        <p class="mt-1 text-sm text-white/60">Klik “Pilih Jadwal” untuk pilih tanggal & jam. Lalu pilih buffet dan/atau menu regular.</p>
      </div>
      <a href="/" class="rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">← Kembali</a>
    </div>

    @if ($errors->any())
      <div class="mb-4 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-100 whitespace-pre-line">
        ❌ {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('public.reservations.store') }}" id="reservationForm"
      class="grid grid-cols-1 gap-5 xl:grid-cols-[1fr_.8fr]">
      @csrf

      {{-- LEFT --}}
      <section class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <div class="lg:col-span-1">
            <div class="text-sm text-white/70">Pilih Resource</div>
            <select id="resourceSelect" name="reservation_resource_id"
              class="mt-2 w-full rounded-2xl border border-white/12 bg-black/25 px-4 py-3 text-sm outline-none">
              @foreach($resources as $rs)
                <option value="{{ $rs->id }}"
                  data-min="{{ (int)$rs->min_duration_minutes }}"
                  data-buffer="{{ (int)($rs->buffer_minutes ?? 0) }}"
                  data-hourly="{{ (int)($rs->hourly_rate ?? 0) }}"
                  data-flat="{{ (int)($rs->flat_rate ?? 0) }}"
                >
                  [{{ $rs->type }}] {{ $rs->name }} (kap {{ $rs->capacity }})
                </option>
              @endforeach
            </select>
            <div class="mt-2 text-xs text-white/50">Slot jam yang sudah dibooking akan nonaktif.</div>
          </div>

          <div class="lg:col-span-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
              <div class="text-sm text-white/70">Nama</div>
              <input name="customer_name" value="{{ old('customer_name') }}"
                class="mt-2 w-full rounded-2xl border border-white/12 bg-black/25 px-4 py-3 text-sm outline-none" required>
            </div>
            <div>
              <div class="text-sm text-white/70">No HP (opsional)</div>
              <input name="customer_phone" value="{{ old('customer_phone') }}"
                class="mt-2 w-full rounded-2xl border border-white/12 bg-black/25 px-4 py-3 text-sm outline-none">
            </div>
          </div>
        </div>

        {{-- JADWAL --}}
        <div class="mt-6">
          <div class="flex items-center justify-between gap-3">
            <div class="font-semibold">Jadwal Reservasi</div>
            <button type="button" id="openScheduleModal"
              class="rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">
              Pilih Jadwal
            </button>
          </div>

          <div class="mt-3 rounded-2xl border border-white/10 bg-black/20 p-4">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 text-sm">
              <div>
                <div class="text-white/60">Start</div>
                <div id="summaryStart" class="font-semibold">-</div>
              </div>
              <div>
                <div class="text-white/60">End</div>
                <div id="summaryEnd" class="font-semibold">-</div>
              </div>
              <div>
                <div class="text-white/60">Estimasi Sewa</div>
                <div id="summaryRental" class="font-semibold">Rp 0</div>
              </div>
            </div>
            <div class="mt-3 text-xs text-white/60">
              DP = 50% dari total. (Total akhir dihitung server setelah submit).
            </div>
          </div>

          {{-- hidden submit --}}
          <input type="hidden" name="start_date" id="startDateInput">
          <input type="hidden" name="start_time" id="startTimeInput">
          <input type="hidden" name="end_date" id="endDateInput">
          <input type="hidden" name="end_time" id="endTimeInput">
        </div>

        {{-- BUFFET --}}
        <div class="mt-8">
          <div class="flex items-center justify-between gap-3">
            <div class="font-semibold">Buffet (opsional)</div>
            <div class="text-xs text-white/60">Tidak terikat stok (disiapkan dapur)</div>
          </div>

          <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
            @foreach($buffetPackages as $bp)
              <label class="cursor-pointer rounded-2xl border border-white/10 bg-white/[0.04] p-4 hover:bg-white/[0.06]">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="font-semibold">{{ $bp->name }}</div>
                    <div class="mt-1 text-xs text-white/60">
                      {{ $bp->pricing_type === 'per_pax' ? 'Per pax' : 'Per event' }}
                      • Rp {{ number_format((int)$bp->price,0,',','.') }}
                      {!! $bp->min_pax ? ' • Min pax '.$bp->min_pax : '' !!}
                    </div>
                  </div>
                  <input type="radio" name="buffet_package_id" value="{{ $bp->id }}" class="mt-1">
                </div>
                <div class="mt-2 text-sm text-white/70 line-clamp-3">{{ $bp->notes ?: 'Tidak ada deskripsi.' }}</div>
              </label>
            @endforeach
          </div>

          <div class="mt-3">
            <div class="text-sm text-white/70">Pax (wajib jika paket per pax)</div>
            <input type="number" name="pax" min="1" value="{{ old('pax') }}"
              class="mt-2 w-full rounded-2xl border border-white/12 bg-black/25 px-4 py-3 text-sm outline-none"
              placeholder="contoh: 30">
          </div>
        </div>

        {{-- REGULAR MENU (LAZY) --}}
        <div class="mt-10">
          <div class="flex items-center justify-between gap-3">
            <div>
              <div class="font-semibold">Menu Regular (opsional)</div>
              <div class="text-xs text-white/60">Tambah menu lewat modal (lazy load).</div>
            </div>
            <button type="button" id="openMenuModal"
              class="rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">
              + Tambah Menu
            </button>
          </div>

          <div id="selectedList" class="mt-4 space-y-2"></div>
          <div id="itemsHidden"></div>
        </div>

        <div class="mt-8">
          <div class="text-sm text-white/70">Catatan (opsional)</div>
          <textarea name="notes" rows="3"
            class="mt-2 w-full rounded-2xl border border-white/12 bg-black/25 px-4 py-3 text-sm outline-none">{{ old('notes') }}</textarea>
        </div>
      </section>

      {{-- RIGHT --}}
      <aside class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
        <div class="text-[11px] uppercase tracking-[0.24em] text-white/55">Checkout</div>
        <div class="mt-2 text-lg font-semibold">Ringkasan Reservasi</div>

        <div class="mt-4 rounded-2xl border border-white/10 bg-black/20 p-4 text-sm">
          <div class="flex items-center justify-between">
            <span class="text-white/60">Menu total (regular)</span>
            <span id="sumMenu" class="font-semibold">Rp 0</span>
          </div>
          <div class="mt-2 flex items-center justify-between">
            <span class="text-white/60">Sewa</span>
            <span id="sumRental" class="font-semibold">Rp 0</span>
          </div>
          <div class="mt-3 border-t border-white/10 pt-3 flex items-center justify-between">
            <span class="text-white/80 font-medium">Grand (estimasi)</span>
            <span id="sumGrand" class="text-yellow-300 font-bold">Rp 0</span>
          </div>
          <div class="mt-2 flex items-center justify-between">
            <span class="text-white/60">DP (50%)</span>
            <span id="sumDp" class="font-semibold">Rp 0</span>
          </div>
        </div>

        <button type="submit"
          class="mt-5 w-full rounded-2xl bg-yellow-400 px-5 py-3 text-sm font-semibold text-black hover:bg-yellow-300">
          Buat Reservasi
        </button>

        <div id="submitHint" class="mt-3 text-xs text-white/60">Pilih jadwal dulu (klik “Pilih Jadwal”).</div>
      </aside>
    </form>
  </div>

  {{-- MODAL KALENDER JADWAL --}}
  <div id="scheduleModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60"></div>

    <div class="relative mx-auto mt-10 w-[95%] max-w-4xl rounded-[28px] border border-white/12 bg-[#0d0d10] p-5 shadow-2xl">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-[11px] uppercase tracking-[0.22em] text-white/55">Jadwal</div>
          <div class="mt-1 text-xl font-bold">Pilih Tanggal & Jam</div>
          <div class="mt-1 text-sm text-white/60">Klik tanggal → pilih jam tersedia → pilih durasi → “Pakai Jadwal”.</div>
        </div>
        <button type="button" id="closeScheduleModal"
          class="rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">
          Tutup
        </button>
      </div>

      <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[1.05fr_.95fr]">
        {{-- Kalender --}}
        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <button type="button" id="calPrev"
                class="rounded-xl border border-white/12 bg-white/[0.05] px-3 py-2 text-sm font-semibold hover:bg-white/[0.09]">←</button>
              <button type="button" id="calNext"
                class="rounded-xl border border-white/12 bg-white/[0.05] px-3 py-2 text-sm font-semibold hover:bg-white/[0.09]">→</button>
              <button type="button" id="calToday"
                class="rounded-xl border border-white/12 bg-white/[0.05] px-3 py-2 text-sm font-semibold hover:bg-white/[0.09]">today</button>
            </div>
            <div id="calTitle" class="font-semibold"></div>
          </div>

          <div class="mt-3 grid grid-cols-7 gap-2 text-xs text-white/60">
            <div class="text-center">Mon</div><div class="text-center">Tue</div><div class="text-center">Wed</div>
            <div class="text-center">Thu</div><div class="text-center">Fri</div><div class="text-center">Sat</div><div class="text-center">Sun</div>
          </div>

          <div id="calGrid" class="mt-2 grid grid-cols-7 gap-2"></div>

          <div class="mt-3 text-xs text-white/60">
            <span class="inline-block h-2 w-2 rounded-full bg-red-400/80"></span> penuh (indikasi)
            <span class="ml-4 inline-block h-2 w-2 rounded-full bg-white/40"></span> tersedia
          </div>
        </div>

        {{-- Slot Jam --}}
        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
          <div class="font-semibold">Pilih Jam</div>
          <div id="slotMeta" class="mt-1 text-xs text-white/60">Pilih tanggal dulu.</div>

          <div id="slotGrid" class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-4"></div>

          <div class="mt-4">
            <div class="text-sm text-white/70">Durasi</div>
            <select id="durationSelect"
              class="mt-2 w-full rounded-2xl border border-white/12 bg-black/25 px-4 py-3 text-sm outline-none">
              <option value="">Pilih jam dulu</option>
            </select>
            <div class="mt-1 text-xs text-white/50">Durasi minimal mengikuti aturan resource.</div>
          </div>

          <button type="button" id="applySchedule"
            class="mt-4 w-full rounded-2xl bg-yellow-400 px-4 py-3 text-sm font-semibold text-black hover:bg-yellow-300">
            Pakai Jadwal Ini
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL MENU REGULAR (LAZY) --}}
  <div id="menuModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60"></div>

    <div class="relative mx-auto mt-10 w-[95%] max-w-5xl rounded-[28px] border border-white/12 bg-[#0d0d10] p-5 shadow-2xl">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-[11px] uppercase tracking-[0.22em] text-white/55">Menu Regular</div>
          <div class="mt-1 text-xl font-bold">Tambah Menu</div>
          <div class="mt-1 text-sm text-white/60">Cari menu, lihat max tersedia, lalu atur qty.</div>
        </div>
        <button type="button" id="closeMenuModal"
          class="rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">
          Tutup
        </button>
      </div>

      <div class="mt-4 flex gap-3">
        <input id="menuSearch" placeholder="Cari menu…"
          class="w-full rounded-2xl border border-white/12 bg-black/25 px-4 py-3 text-sm outline-none placeholder:text-white/35">
        <button type="button" id="menuSearchBtn"
          class="rounded-2xl bg-white/15 px-4 py-3 text-sm font-semibold hover:bg-white/20">
          Cari
        </button>
      </div>

      <div id="menuMeta" class="mt-3 text-xs text-white/60">Memuat…</div>

      <div id="menuGrid" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>

      <div class="mt-4 flex items-center justify-between gap-3">
        <button type="button" id="loadMore"
          class="rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">
          Muat lagi
        </button>

        <button type="button" id="applySelection"
          class="rounded-2xl bg-yellow-400 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-300">
          Selesai Pilih Menu
        </button>
      </div>
    </div>
  </div>

<script>
  // ===== Helpers
  const fmtRp = (n) => 'Rp ' + (Math.round(n||0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  const DP_RATIO = {{ (float)config('reservations.dp_ratio', 0.5) }};
  function pad2(n){ return String(n).padStart(2,'0'); }
  function toISODate(d){ return d.getFullYear()+'-'+pad2(d.getMonth()+1)+'-'+pad2(d.getDate()); }
  function parseHHMM(hhmm){ const [h,m]=hhmm.split(':').map(Number); return h*60+m; }
  function fromMin(min){ const h=Math.floor(min/60), m=min%60; return pad2(h)+':'+pad2(m); }
  function isOverlap(startMin,endMin,booked){
    return (booked||[]).some(b=>{
      const bs=parseHHMM(b.start), be=parseHHMM(b.end);
      return startMin < be && endMin > bs;
    });
  }

  // ===== Cart menu regular: id => {id,name,price,max,qty}
  const cart = new Map();

  function rebuildHiddenItems(){
    const wrap=document.getElementById('itemsHidden');
    wrap.innerHTML='';
    let idx=0;
    for(const it of cart.values()){
      if(it.qty>0){
        const a=document.createElement('input'); a.type='hidden'; a.name=`items[${idx}][product_id]`; a.value=String(it.id);
        const b=document.createElement('input'); b.type='hidden'; b.name=`items[${idx}][qty]`; b.value=String(it.qty);
        wrap.appendChild(a); wrap.appendChild(b);
        idx++;
      }
    }
  }

  function renderSelectedList(){
    const box=document.getElementById('selectedList');
    const items=[...cart.values()].filter(x=>x.qty>0);

    if(items.length===0){
      box.innerHTML=`<div class="text-sm text-white/50">Belum ada menu regular dipilih.</div>`;
      return;
    }

    box.innerHTML='';
    items.forEach(it=>{
      const row=document.createElement('div');
      row.className='flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-black/20 px-4 py-3';
      row.innerHTML=`
        <div class="min-w-0">
          <div class="font-semibold line-clamp-1">${it.name}</div>
          <div class="text-xs text-white/60">${fmtRp(it.price)} • Max ${it.max ?? '-'}</div>
        </div>
        <div class="flex items-center gap-2">
          <button type="button" class="sel-minus rounded-xl border border-white/12 bg-white/[0.05] px-3 py-2 text-sm font-bold" data-id="${it.id}">−</button>
          <div class="w-10 text-center font-semibold">${it.qty}</div>
          <button type="button" class="sel-plus rounded-xl border border-white/12 bg-white/[0.05] px-3 py-2 text-sm font-bold" data-id="${it.id}">+</button>
        </div>
      `;
      box.appendChild(row);
    });

    box.querySelectorAll('.sel-minus').forEach(btn=>{
      btn.onclick=()=>{
        const id=parseInt(btn.dataset.id,10);
        const it=cart.get(id); if(!it) return;
        it.qty=Math.max(0,it.qty-1);
        cart.set(id,it);
        rebuildHiddenItems();
        renderSelectedList();
        updateTotals();
      };
    });

    box.querySelectorAll('.sel-plus').forEach(btn=>{
      btn.onclick=()=>{
        const id=parseInt(btn.dataset.id,10);
        const it=cart.get(id); if(!it) return;
        const max=(it.max ?? 999999);
        it.qty=Math.min(max,it.qty+1);
        cart.set(id,it);
        rebuildHiddenItems();
        renderSelectedList();
        updateTotals();
      };
    });
  }

  // ===== Jadwal state (popup)
  const scheduleModal = document.getElementById('scheduleModal');
  let calMonth = new Date();
  let fullMap = {};          // date => is_full (indikasi)
  let selectedDate = null;   // YYYY-MM-DD
  let availability = null;   // from API
  let selectedStart = null;  // HH:MM
  let selectedDuration = null;

  function calcRental(){
    const opt=document.querySelector('#resourceSelect option:checked');
    const hourly=parseInt(opt?.dataset.hourly||'0',10);
    const flat=parseInt(opt?.dataset.flat||'0',10);
    if(!selectedDuration) return 0;
    if(flat>0) return flat;
    if(hourly>0) return hourly * Math.ceil(selectedDuration/60);
    return 0;
  }

  function updateScheduleSummary(){
    const rental=calcRental();
    document.getElementById('summaryRental').textContent=fmtRp(rental);
    document.getElementById('sumRental').textContent=fmtRp(rental);

    if(selectedDate && selectedStart && selectedDuration){
      const endHHMM = fromMin(parseHHMM(selectedStart) + selectedDuration);

      document.getElementById('summaryStart').textContent=`${selectedDate} ${selectedStart}`;
      document.getElementById('summaryEnd').textContent=`${selectedDate} ${endHHMM}`;

      document.getElementById('startDateInput').value=selectedDate;
      document.getElementById('startTimeInput').value=selectedStart;
      document.getElementById('endDateInput').value=selectedDate;
      document.getElementById('endTimeInput').value=endHHMM;

      document.getElementById('submitHint').textContent='Siap dibuat.';
    } else {
      document.getElementById('summaryStart').textContent='-';
      document.getElementById('summaryEnd').textContent='-';
      document.getElementById('startDateInput').value='';
      document.getElementById('startTimeInput').value='';
      document.getElementById('endDateInput').value='';
      document.getElementById('endTimeInput').value='';
      document.getElementById('submitHint').textContent='Pilih jadwal dulu (klik “Pilih Jadwal”).';
    }
  }

  function updateTotals(){
    let menuTotal=0;
    for(const it of cart.values()){
      menuTotal += (it.price||0) * (it.qty||0);
    }
    const rental=calcRental();
    const grand=menuTotal + rental;

    document.getElementById('sumMenu').textContent=fmtRp(menuTotal);
    document.getElementById('sumGrand').textContent=fmtRp(grand);
    document.getElementById('sumDp').textContent=fmtRp(grand * DP_RATIO);
    updateScheduleSummary();
  }

  async function loadFullDates(){
    const resourceId=document.getElementById('resourceSelect').value;
    const startDate=toISODate(new Date());

    const url=new URL("{{ route('public.reservations.availability_range') }}", window.location.origin);
    url.searchParams.set('reservation_resource_id', resourceId);
    url.searchParams.set('start_date', startDate);
    url.searchParams.set('days', '31');

    const res=await fetch(url.toString());
    const json=await res.json();

    fullMap={};
    (json.dates||[]).forEach(x=>{ fullMap[x.date]=!!x.is_full; });
  }

  function renderCalendar(){
    const title=document.getElementById('calTitle');
    const grid=document.getElementById('calGrid');
    grid.innerHTML='';

    const y=calMonth.getFullYear();
    const m=calMonth.getMonth();

    title.textContent = calMonth.toLocaleDateString('en-US', { month:'long', year:'numeric' });

    const first=new Date(y,m,1);
    const last=new Date(y,m+1,0);
    const firstDay=((first.getDay()+6)%7); // Mon=0
    const daysInMonth=last.getDate();

    for(let i=0;i<42;i++){
      const dayNum=i-firstDay+1;
      const cell=document.createElement('button');
      cell.type='button';

      if(dayNum<1 || dayNum>daysInMonth){
        cell.disabled=true;
        cell.className='h-12 rounded-xl border border-white/5 bg-black/10 text-white/20';
        cell.textContent='';
        grid.appendChild(cell);
        continue;
      }

      const dateObj=new Date(y,m,dayNum);
      const iso=toISODate(dateObj);
      const isFull=fullMap[iso]===true;

      cell.dataset.date=iso;
      cell.textContent=String(dayNum);
      cell.className='h-12 rounded-xl border text-sm font-semibold ' +
        (isFull ? 'border-red-500/20 bg-red-500/10 text-red-100'
                : 'border-white/12 bg-white/[0.04] hover:bg-white/[0.07]');

      if(selectedDate===iso){
        cell.className += ' ring-2 ring-yellow-400/60';
      }

      cell.onclick=async ()=>{
        selectedDate=iso;
        selectedStart=null;
        selectedDuration=null;
        availability=null;

        await loadAvailabilityForDate();
        renderCalendar();
        renderSlots();
        renderDurations();
        updateTotals();
      };

      grid.appendChild(cell);
    }
  }

  async function loadAvailabilityForDate(){
    const resourceId=document.getElementById('resourceSelect').value;

    const url=new URL("{{ route('public.reservations.availability') }}", window.location.origin);
    url.searchParams.set('reservation_resource_id', resourceId);
    url.searchParams.set('date', selectedDate);

    const res=await fetch(url.toString());
    availability=await res.json();
  }

  function renderSlots(){
    const meta=document.getElementById('slotMeta');
    const grid=document.getElementById('slotGrid');
    grid.innerHTML='';

    if(!availability){
      meta.textContent='Pilih tanggal dulu.';
      return;
    }

    meta.textContent=`Tanggal ${selectedDate} • pilih jam tersedia`;

    const open=parseHHMM(availability.open);
    const close=parseHHMM(availability.close);
    const minDur=availability.min_duration_minutes;
    const step=availability.slot_minutes;
    const booked=availability.booked||[];

    for(let t=open; t+minDur<=close; t+=step){
      const hhmm=fromMin(t);
      const disabled=isOverlap(t, t+minDur, booked);

      const btn=document.createElement('button');
      btn.type='button';
      btn.textContent=hhmm;
      btn.disabled=disabled;
      btn.className='rounded-xl border px-3 py-2 text-xs font-semibold ' +
        (disabled ? 'border-white/10 bg-black/20 text-white/35 cursor-not-allowed'
                  : 'border-white/12 bg-white/[0.04] hover:bg-white/[0.07]');

      btn.onclick=()=>{
        selectedStart=hhmm;
        selectedDuration=null;

        [...grid.querySelectorAll('button')].forEach(x=>{
          const active=x.textContent===hhmm;
          x.classList.toggle('ring-2', active);
          x.classList.toggle('ring-yellow-400/60', active);
        });

        renderDurations();
        updateTotals();
      };

      grid.appendChild(btn);
    }
  }

  function renderDurations(){
    const sel=document.getElementById('durationSelect');
    sel.innerHTML='';

    if(!availability || !selectedStart){
      sel.disabled=true;
      sel.innerHTML=`<option value="">Pilih jam dulu</option>`;
      return;
    }

    sel.disabled=false;

    const start=parseHHMM(selectedStart);
    const close=parseHHMM(availability.close);
    const minDur=availability.min_duration_minutes;
    const step=availability.slot_minutes;
    const booked=availability.booked||[];

    let first=true;
    for(let dur=minDur; start+dur<=close; dur+=step){
      if(isOverlap(start, start+dur, booked)) break;

      const opt=document.createElement('option');
      opt.value=String(dur);
      const h=Math.floor(dur/60), m=dur%60;
      opt.textContent=(h>0?`${h} jam `:'') + (m>0?`${m} menit`:'');
      if(first){ opt.selected=true; selectedDuration=dur; first=false; }
      sel.appendChild(opt);
    }

    sel.onchange=()=>{
      selectedDuration=parseInt(sel.value||'0',10)||null;
      updateTotals();
    };
  }

  document.getElementById('openScheduleModal').onclick=async ()=>{
    scheduleModal.classList.remove('hidden');
    await loadFullDates();
    renderCalendar();
    renderSlots();
    renderDurations();
  };
  document.getElementById('closeScheduleModal').onclick=()=>scheduleModal.classList.add('hidden');

  document.getElementById('calPrev').onclick=()=>{ calMonth=new Date(calMonth.getFullYear(), calMonth.getMonth()-1, 1); renderCalendar(); };
  document.getElementById('calNext').onclick=()=>{ calMonth=new Date(calMonth.getFullYear(), calMonth.getMonth()+1, 1); renderCalendar(); };
  document.getElementById('calToday').onclick=()=>{ const t=new Date(); calMonth=new Date(t.getFullYear(), t.getMonth(), 1); renderCalendar(); };

  document.getElementById('applySchedule').onclick=()=>{
    if(!selectedDate || !selectedStart || !selectedDuration){
      alert('Pilih tanggal, jam, dan durasi dulu.');
      return;
    }
    scheduleModal.classList.add('hidden');
    updateTotals();
  };

  // reset kalau ganti resource
  document.getElementById('resourceSelect').addEventListener('change', ()=>{
    selectedDate=null; selectedStart=null; selectedDuration=null; availability=null;
    updateTotals();
  });

  // ===== Modal menu regular (lazy)
  const menuModal=document.getElementById('menuModal');
  const menuGrid=document.getElementById('menuGrid');
  const menuMeta=document.getElementById('menuMeta');
  const loadMoreBtn=document.getElementById('loadMore');

  let page=1, lastPage=1, query='';

  function openMenuModal(){
    menuModal.classList.remove('hidden');
    page=1; lastPage=1; query='';
    document.getElementById('menuSearch').value='';
    menuGrid.innerHTML='';
    fetchMenuPage();
  }
  function closeMenuModal(){ menuModal.classList.add('hidden'); }

  document.getElementById('openMenuModal').onclick=openMenuModal;
  document.getElementById('closeMenuModal').onclick=closeMenuModal;
  document.getElementById('applySelection').onclick=()=>{
    closeMenuModal();
    rebuildHiddenItems();
    renderSelectedList();
    updateTotals();
  };

  document.getElementById('menuSearchBtn').onclick=()=>{
    query=document.getElementById('menuSearch').value.trim();
    page=1;
    menuGrid.innerHTML='';
    fetchMenuPage();
  };
  document.getElementById('menuSearch').addEventListener('keydown', (e)=>{
    if(e.key==='Enter'){ e.preventDefault(); document.getElementById('menuSearchBtn').click(); }
  });

  loadMoreBtn.onclick=()=>{
    if(page<lastPage){
      page++;
      fetchMenuPage();
    }
  };

  async function fetchMenuPage(){
    menuMeta.textContent='Memuat menu…';

    const url=new URL("{{ route('public.reservations.products') }}", window.location.origin);
    url.searchParams.set('page', String(page));
    url.searchParams.set('per_page', '12');
    if(query) url.searchParams.set('q', query);

    const res=await fetch(url.toString());
    const json=await res.json();

    lastPage=json.meta.last_page || 1;
    menuMeta.textContent=`Halaman ${json.meta.current_page} / ${json.meta.last_page} • Total ${json.meta.total}`;
    loadMoreBtn.disabled = page >= lastPage;

    (json.data||[]).forEach(p=>{
      const max = (p.max_available ?? 0);
      const disabled = max <= 0;
      const currentQty = cart.get(p.id)?.qty || 0;

      // sync meta
      if(!cart.has(p.id)) cart.set(p.id, {id:p.id, name:p.name, price:p.price, max:max, qty:0});
      else { const it=cart.get(p.id); it.max=max; it.name=p.name; it.price=p.price; cart.set(p.id,it); }

      const card=document.createElement('div');
      card.className='rounded-[22px] border border-white/10 bg-white/[0.04] p-4';
      card.innerHTML=`
        <div class="flex gap-3">
          <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl border border-white/10 bg-black/30">
            ${p.image_url ? `<img src="${p.image_url}" class="h-full w-full object-cover">`
                          : `<div class="h-full w-full bg-gradient-to-br from-yellow-400/15 via-white/5 to-transparent"></div>`}
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-2">
              <div class="min-w-0">
                <div class="font-semibold line-clamp-1">${p.name}</div>
                <div class="mt-1 text-xs text-white/60">${p.category ?? 'Menu'}</div>
              </div>
              <div class="text-sm font-bold text-yellow-300">${fmtRp(p.price)}</div>
            </div>

            <div class="mt-2 text-sm text-white/65 line-clamp-2">${p.description || 'Belum ada deskripsi.'}</div>

            <div class="mt-3 flex items-center justify-between gap-3">
              <div class="text-xs text-white/55">${disabled ? 'Tidak tersedia' : `Max: <span class="font-semibold text-white/85">${max}</span>`}</div>

              <div class="flex items-center gap-2">
                <button type="button" class="m-minus rounded-xl border border-white/12 bg-white/[0.05] px-3 py-2 text-sm font-bold" data-id="${p.id}" ${disabled?'disabled':''}>−</button>
                <div class="w-10 text-center font-semibold" id="q-${p.id}">${currentQty}</div>
                <button type="button" class="m-plus rounded-xl border border-white/12 bg-white/[0.05] px-3 py-2 text-sm font-bold" data-id="${p.id}" ${disabled?'disabled':''}>+</button>
              </div>
            </div>
          </div>
        </div>
      `;
      menuGrid.appendChild(card);
    });
  }

  // delegation for plus/minus in modal grid
  menuGrid.onclick=(e)=>{
    const plus=e.target.closest('.m-plus');
    const minus=e.target.closest('.m-minus');
    if(!plus && !minus) return;

    const id=parseInt((plus||minus).dataset.id,10);
    const it=cart.get(id); if(!it) return;

    const max=(it.max ?? 999999);
    if(plus) it.qty = Math.min(max, it.qty+1);
    if(minus) it.qty = Math.max(0, it.qty-1);

    cart.set(id,it);
    const qEl=document.getElementById('q-'+id);
    if(qEl) qEl.textContent=String(it.qty);
  };

  // init
  renderSelectedList();
  updateTotals();
</script>
</body>
</html>