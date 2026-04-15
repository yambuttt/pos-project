<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reservasi</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#070708] text-white">
  <div
    class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(250,204,21,.14),transparent_28%),radial-gradient(circle_at_top_right,rgba(255,255,255,.08),transparent_24%),linear-gradient(to_bottom,#070708,#0a0a0d)]">
  </div>
  <div
    class="pointer-events-none fixed left-[-120px] top-[80px] -z-10 h-[260px] w-[260px] rounded-full bg-yellow-400/10 blur-3xl">
  </div>
  <div
    class="pointer-events-none fixed right-[-120px] top-[120px] -z-10 h-[240px] w-[240px] rounded-full bg-white/5 blur-3xl">
  </div>

  <div class="mx-auto max-w-7xl px-4 pb-24 pt-6 sm:px-5 lg:px-6">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
      <div class="min-w-0">
        <div class="text-[11px] uppercase tracking-[0.30em] text-yellow-300/70">Reservasi</div>
        <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Booking Ruangan / Meja + Menu</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-white/60">
          Pilih resource, tentukan jadwal, lalu tambahkan buffet dan/atau menu regular sesuai kebutuhan acara.
        </p>
      </div>

      <a href="/"
        class="inline-flex items-center rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">
        ← Kembali
      </a>
    </div>

    @if ($errors->any())
      <div
        class="mb-5 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-100 whitespace-pre-line">
        ❌ {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('public.reservations.store') }}" id="reservationForm"
      class="grid grid-cols-1 gap-6 2xl:grid-cols-[1fr_.78fr]">
      @csrf

      {{-- LEFT --}}
      <section class="space-y-6">
        {{-- RESOURCE + CUSTOMER --}}
        <div class="grid grid-cols-1 gap-4 2xl:grid-cols-[1.2fr_.8fr]">
          <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
              <div>
                <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Resource</div>
                <h2 class="mt-1 text-lg font-semibold">Pilih Hall / Room / Table</h2>
                <p class="mt-1 max-w-xl text-sm leading-6 text-white/55">
                  Harga sewa, kapasitas, dan aturan reservasi akan tampil otomatis sesuai resource yang dipilih.
                </p>
              </div>

              <div
                class="rounded-full border border-yellow-400/20 bg-yellow-400/10 px-3 py-1 text-xs font-semibold text-yellow-300">
                Sewa otomatis
              </div>
            </div>

            <div class="mt-4">
              <select id="resourceSelect" name="reservation_resource_id"
                class="w-full rounded-2xl border border-white/12 bg-[#111214] px-4 py-3 text-sm outline-none">
                @foreach($resources as $rs)
                  <option value="{{ $rs->id }}" data-type="{{ $rs->type }}" data-name="{{ $rs->name }}"
                    data-capacity="{{ (int) $rs->capacity }}" data-min="{{ (int) $rs->min_duration_minutes }}"
                    data-buffer="{{ (int) ($rs->buffer_minutes ?? 0) }}" data-hourly="{{ (int) ($rs->hourly_rate ?? 0) }}"
                    data-flat="{{ (int) ($rs->flat_rate ?? 0) }}">
                    [{{ $rs->type }}] {{ $rs->name }} (kap {{ $rs->capacity }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-2">
              <div class="rounded-[24px] border border-white/10 bg-black/20 p-4">
                <div class="text-[11px] uppercase tracking-[0.18em] text-white/45">Info Resource</div>

                <div class="mt-4 space-y-3">
                  <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-3">
                    <span class="text-sm text-white/60">Tipe</span>
                    <span id="resourceTypeText" class="text-right text-sm font-semibold text-white">-</span>
                  </div>

                  <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-3">
                    <span class="text-sm text-white/60">Kapasitas</span>
                    <span id="resourceCapacityText" class="text-right text-sm font-semibold text-white">-</span>
                  </div>

                  <div class="flex items-start justify-between gap-4">
                    <span class="text-sm text-white/60">Min. Durasi</span>
                    <span id="resourceMinText" class="text-right text-sm font-semibold text-white">-</span>
                  </div>
                </div>
              </div>

              <div class="rounded-[24px] border border-white/10 bg-black/20 p-4">
                <div class="text-[11px] uppercase tracking-[0.18em] text-white/45">Tarif</div>

                <div class="mt-4 space-y-3">
                  <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-3">
                    <span class="text-sm text-white/60">Harga / jam</span>
                    <span id="resourceHourlyText" class="text-right text-sm font-semibold text-yellow-300">-</span>
                  </div>

                  <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-3">
                    <span class="text-sm text-white/60">Harga flat</span>
                    <span id="resourceFlatText" class="text-right text-sm font-semibold text-white">-</span>
                  </div>

                  <div class="flex items-start justify-between gap-4">
                    <span class="text-sm text-white/60">Buffer</span>
                    <span id="resourceBufferText" class="text-right text-sm font-semibold text-white">-</span>
                  </div>
                </div>
              </div>
            </div>

            <div
              class="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3 text-sm leading-6 text-white/60">
              Tarif aktif:
              <span id="checkoutRateText" class="font-semibold text-white/85">-</span>.
              Jika resource punya harga flat, sistem akan memakai harga flat. Jika tidak ada, sistem memakai harga per
              jam.
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-1">
            <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
              <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Customer</div>
              <div class="mt-2 text-sm text-white/70">Nama</div>
              <input name="customer_name" value="{{ old('customer_name') }}"
                class="mt-2 w-full rounded-2xl border border-white/12 bg-[#111214] px-4 py-3 text-sm outline-none"
                required>
            </div>

            <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
              <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Kontak</div>
              <div class="mt-2 text-sm text-white/70">No HP</div>
              <input name="customer_phone" value="{{ old('customer_phone') }}"
                class="mt-2 w-full rounded-2xl border border-white/12 bg-[#111214] px-4 py-3 text-sm outline-none">
            </div>
          </div>
        </div>

        {{-- JADWAL --}}
        <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Jadwal</div>
              <h2 class="mt-1 text-lg font-semibold">Jadwal Reservasi</h2>
              <p class="mt-1 text-sm text-white/55">Pilih tanggal, jam, dan durasi dari popup jadwal.</p>
            </div>

            <button type="button" id="openScheduleModal"
              class="rounded-2xl border border-yellow-400/25 bg-yellow-400/10 px-4 py-2 text-sm font-semibold text-yellow-300 hover:bg-yellow-400/15">
              Pilih Jadwal
            </button>
          </div>

          <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
              <div class="text-[11px] uppercase tracking-[0.18em] text-white/45">Start</div>
              <div id="summaryStart" class="mt-2 text-sm font-semibold">-</div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
              <div class="text-[11px] uppercase tracking-[0.18em] text-white/45">End</div>
              <div id="summaryEnd" class="mt-2 text-sm font-semibold">-</div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
              <div class="text-[11px] uppercase tracking-[0.18em] text-white/45">Estimasi Sewa</div>
              <div id="summaryRental" class="mt-2 text-base font-bold text-yellow-300">Rp 0</div>
            </div>
          </div>

          <div class="mt-3 text-xs text-white/55">
            DP = 50% dari total. Total akhir dihitung server saat submit.
          </div>

          <input type="hidden" name="start_date" id="startDateInput">
          <input type="hidden" name="start_time" id="startTimeInput">
          <input type="hidden" name="end_date" id="endDateInput">
          <input type="hidden" name="end_time" id="endTimeInput">
        </div>

        {{-- BUFFET --}}
        <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Buffet</div>
              <h2 class="mt-1 text-lg font-semibold">Paket Buffet (opsional)</h2>
              <p class="mt-1 text-sm text-white/55">Pilih paket buffet jika dibutuhkan. Buffet tidak terikat stok
                regular.</p>
            </div>

            <div class="rounded-full border border-white/10 bg-black/20 px-3 py-1 text-xs text-white/60">
              Disiapkan dapur
            </div>
          </div>

          <div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-2">
            @foreach($buffetPackages as $bp)
              <label
                class="cursor-pointer rounded-[24px] border border-white/10 bg-black/20 p-4 transition hover:border-yellow-400/20 hover:bg-white/[0.04]">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="text-base font-semibold">{{ $bp->name }}</div>
                    <div class="mt-1 text-xs text-white/60">
                      {{ $bp->pricing_type === 'per_pax' ? 'Per pax' : 'Per event' }}
                      • Rp {{ number_format((int) $bp->price, 0, ',', '.') }}
                      {!! $bp->min_pax ? ' • Min pax ' . $bp->min_pax : '' !!}
                    </div>
                  </div>
                  <input type="radio" name="buffet_package_id" value="{{ $bp->id }}" class="mt-1">
                </div>

                <div class="mt-3 text-sm leading-6 text-white/68">
                  {{ $bp->notes ?: 'Tidak ada deskripsi.' }}
                </div>
              </label>
            @endforeach
          </div>

          <div class="mt-4 max-w-md">
            <div class="text-sm text-white/70">Pax (wajib jika paket per pax)</div>
            <input type="number" name="pax" min="1" value="{{ old('pax') }}"
              class="mt-2 w-full rounded-2xl border border-white/12 bg-[#111214] px-4 py-3 text-sm outline-none"
              placeholder="contoh: 30">
          </div>
        </div>

        {{-- MENU REGULAR --}}
        <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Menu Regular</div>
              <h2 class="mt-1 text-lg font-semibold">Menu Regular (opsional)</h2>
              <p class="mt-1 text-sm text-white/55">Tambahkan menu regular melalui modal. Qty mengikuti stok yang
                tersedia.</p>
            </div>

            <button type="button" id="openMenuModal"
              class="rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">
              + Tambah Menu
            </button>
          </div>

          <div id="selectedList" class="mt-4 space-y-2"></div>
          <div id="itemsHidden"></div>
        </div>

        {{-- NOTES --}}
        <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
          <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Catatan</div>
          <div class="mt-2 text-sm text-white/70">Catatan tambahan (opsional)</div>
          <textarea name="notes" rows="4"
            class="mt-3 w-full rounded-2xl border border-white/12 bg-[#111214] px-4 py-3 text-sm outline-none">{{ old('notes') }}</textarea>
        </div>
      </section>

      {{-- RIGHT --}}
      <aside
        class="h-fit rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6 xl:sticky xl:top-6">
        <div class="text-[11px] uppercase tracking-[0.24em] text-white/55">Checkout</div>
        <h2 class="mt-2 text-2xl font-bold tracking-tight">Ringkasan Reservasi</h2>
        <p class="mt-1 text-sm text-white/55">Estimasi biaya sebelum reservasi dibuat.</p>

        <div class="mt-5 rounded-[26px] border border-white/10 bg-black/20 p-4 text-sm">
          <div class="mb-3 flex items-center justify-between">
            <span class="text-white/60">Menu total</span>
            <span id="sumMenu" class="font-semibold">Rp 0</span>
          </div>

          <div class="mb-3 flex items-center justify-between">
            <span class="text-white/60">Sewa</span>
            <span id="sumRental" class="font-semibold">Rp 0</span>
          </div>

          <div class="mb-3 rounded-2xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs text-white/60">
            Tarif resource:
            <span id="checkoutRateText" class="ml-1 font-semibold text-white/85">-</span>
          </div>

          <div class="border-t border-white/10 pt-3">
            <div class="flex items-center justify-between">
              <span class="text-white/80 font-medium">Grand (estimasi)</span>
              <span id="sumGrand" class="text-lg font-bold text-yellow-300">Rp 0</span>
            </div>

            <div class="mt-2 flex items-center justify-between">
              <span class="text-white/60">DP (50%)</span>
              <span id="sumDp" class="font-semibold">Rp 0</span>
            </div>
          </div>
        </div>

        <button type="submit"
          class="mt-5 w-full rounded-2xl bg-yellow-400 px-5 py-3 text-sm font-semibold text-black hover:bg-yellow-300">
          Buat Reservasi
        </button>

        <div id="submitHint" class="mt-3 text-xs text-white/60">
          Pilih jadwal dulu, lalu tambahkan menu jika diperlukan.
        </div>
      </aside>
    </form>
  </div>

  {{-- MODAL JADWAL --}}
  <div id="scheduleModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60"></div>

    <div
      class="relative mx-auto mt-4 flex max-h-[92vh] w-[95%] max-w-4xl flex-col rounded-[28px] border border-white/12 bg-[#0d0d10] p-4 shadow-2xl sm:mt-8 sm:p-5">
      <div class="mb-4 flex shrink-0 items-start justify-between gap-3">
        <div>
          <div class="text-[11px] uppercase tracking-[0.22em] text-white/55">Jadwal</div>
          <div class="mt-1 text-xl font-bold">Pilih Tanggal & Jam</div>
          <div class="mt-1 text-sm text-white/60">Klik tanggal, lalu pilih jam yang tersedia.</div>
        </div>

        <button type="button" id="closeScheduleModal"
          class="rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">
          Tutup
        </button>
      </div>

      <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain pr-1">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.05fr_.95fr]">
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
              <div class="text-center">Mon</div>
              <div class="text-center">Tue</div>
              <div class="text-center">Wed</div>
              <div class="text-center">Thu</div>
              <div class="text-center">Fri</div>
              <div class="text-center">Sat</div>
              <div class="text-center">Sun</div>
            </div>

            <div id="calGrid" class="mt-2 grid grid-cols-7 gap-2"></div>
          </div>

          {{-- Slot Jam --}}
          <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
            <div class="font-semibold">Pilih Jam</div>
            <div id="slotMeta" class="mt-1 text-xs text-white/60">Pilih tanggal dulu.</div>

            <div class="mt-3 max-h-[42vh] overflow-y-auto overscroll-contain pr-1">
              <div id="slotGrid" class="grid grid-cols-3 gap-2 sm:grid-cols-4"></div>
            </div>

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
  </div>

  {{-- MODAL MENU --}}
  <div id="menuModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60"></div>

    <div
      class="relative mx-auto mt-4 flex max-h-[90vh] w-[95%] max-w-5xl flex-col rounded-[28px] border border-white/12 bg-[#0d0d10] p-5 shadow-2xl sm:mt-8">
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
        <input id="menuSearch" placeholder="Cari menu..."
          class="w-full rounded-2xl border border-white/12 bg-[#111214] px-4 py-3 text-sm outline-none placeholder:text-white/35">
        <button type="button" id="menuSearchBtn"
          class="rounded-2xl bg-white/15 px-4 py-3 text-sm font-semibold hover:bg-white/20">
          Cari
        </button>
      </div>

      <div id="menuMeta" class="mt-3 shrink-0 text-xs text-white/60">Memuat…</div>

      {{-- AREA SCROLL --}}
      <div class="mt-4 min-h-0 flex-1 overflow-y-auto pr-1">
        <div id="menuGrid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>
      </div>

      <div class="mt-4 flex shrink-0 items-center justify-between gap-3 border-t border-white/10 pt-4">
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
    const fmtRp = (n) => 'Rp ' + (Math.round(n || 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    const DP_RATIO = {{ (float) config('reservations.dp_ratio', 0.5) }};

    function pad2(n) { return String(n).padStart(2, '0'); }
    function toISODate(d) { return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()); }
    function parseHHMM(hhmm) {
      const [h, m] = hhmm.split(':').map(Number);
      return h * 60 + m;
    }
    function fromMin(min) {
      const h = Math.floor(min / 60);
      const m = min % 60;
      return pad2(h) + ':' + pad2(m);
    }
    function isOverlap(startMin, endMin, booked) {
      return (booked || []).some(b => {
        const bs = parseHHMM(b.start);
        const be = parseHHMM(b.end);
        return startMin < be && endMin > bs;
      });
    }

    const cart = new Map();

    function rebuildHiddenItems() {
      const wrap = document.getElementById('itemsHidden');
      wrap.innerHTML = '';
      let idx = 0;

      for (const it of cart.values()) {
        if (it.qty > 0) {
          const a = document.createElement('input');
          a.type = 'hidden';
          a.name = `items[${idx}][product_id]`;
          a.value = String(it.id);

          const b = document.createElement('input');
          b.type = 'hidden';
          b.name = `items[${idx}][qty]`;
          b.value = String(it.qty);

          wrap.appendChild(a);
          wrap.appendChild(b);
          idx++;
        }
      }
    }

    function renderSelectedList() {
      const box = document.getElementById('selectedList');
      const items = [...cart.values()].filter(x => x.qty > 0);

      if (items.length === 0) {
        box.innerHTML = `<div class="rounded-2xl border border-dashed border-white/10 bg-black/20 px-4 py-5 text-sm text-white/50">Belum ada menu regular dipilih.</div>`;
        return;
      }

      box.innerHTML = '';

      items.forEach(it => {
        const row = document.createElement('div');
        row.className = 'flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-black/20 px-4 py-3';
        row.innerHTML = `
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

      box.querySelectorAll('.sel-minus').forEach(btn => {
        btn.onclick = () => {
          const id = parseInt(btn.dataset.id, 10);
          const it = cart.get(id);
          if (!it) return;
          it.qty = Math.max(0, it.qty - 1);
          cart.set(id, it);
          rebuildHiddenItems();
          renderSelectedList();
          updateTotals();
        };
      });

      box.querySelectorAll('.sel-plus').forEach(btn => {
        btn.onclick = () => {
          const id = parseInt(btn.dataset.id, 10);
          const it = cart.get(id);
          if (!it) return;
          const max = (it.max ?? 999999);
          it.qty = Math.min(max, it.qty + 1);
          cart.set(id, it);
          rebuildHiddenItems();
          renderSelectedList();
          updateTotals();
        };
      });
    }

    const scheduleModal = document.getElementById('scheduleModal');
    let calMonth = new Date();
    let selectedDate = null;
    let availability = null;
    let selectedStart = null;
    let selectedDuration = null;

    function updateResourceMeta() {
      const opt = document.querySelector('#resourceSelect option:checked');
      if (!opt) return;

      const type = opt.dataset.type || '-';
      const capacity = parseInt(opt.dataset.capacity || '0', 10);
      const min = parseInt(opt.dataset.min || '0', 10);
      const buffer = parseInt(opt.dataset.buffer || '0', 10);
      const hourly = parseInt(opt.dataset.hourly || '0', 10);
      const flat = parseInt(opt.dataset.flat || '0', 10);

      document.getElementById('resourceTypeText').textContent = type;
      document.getElementById('resourceCapacityText').textContent = capacity > 0 ? `${capacity} orang` : '-';
      document.getElementById('resourceHourlyText').textContent = hourly > 0 ? fmtRp(hourly) : 'Tidak ada';
      document.getElementById('resourceFlatText').textContent = flat > 0 ? fmtRp(flat) : 'Tidak ada';
      document.getElementById('resourceMinText').textContent = min > 0 ? `${min} menit` : '-';
      document.getElementById('resourceBufferText').textContent = `${buffer} menit`;

      let rateText = '-';
      if (flat > 0 && hourly > 0) rateText = `${fmtRp(hourly)}/jam • flat ${fmtRp(flat)}`;
      else if (flat > 0) rateText = `flat ${fmtRp(flat)}`;
      else if (hourly > 0) rateText = `${fmtRp(hourly)}/jam`;

      document.getElementById('checkoutRateText').textContent = rateText;
    }

    function calcRental() {
      const opt = document.querySelector('#resourceSelect option:checked');
      const hourly = parseInt(opt?.dataset.hourly || '0', 10);
      const flat = parseInt(opt?.dataset.flat || '0', 10);

      if (!selectedDuration) return 0;
      if (flat > 0) return flat;
      if (hourly > 0) return hourly * Math.ceil(selectedDuration / 60);
      return 0;
    }

    function updateScheduleSummary() {
      const rental = calcRental();
      document.getElementById('summaryRental').textContent = fmtRp(rental);
      document.getElementById('sumRental').textContent = fmtRp(rental);

      if (selectedDate && selectedStart && selectedDuration) {
        const endHHMM = fromMin(parseHHMM(selectedStart) + selectedDuration);

        document.getElementById('summaryStart').textContent = `${selectedDate} ${selectedStart}`;
        document.getElementById('summaryEnd').textContent = `${selectedDate} ${endHHMM}`;

        document.getElementById('startDateInput').value = selectedDate;
        document.getElementById('startTimeInput').value = selectedStart;
        document.getElementById('endDateInput').value = selectedDate;
        document.getElementById('endTimeInput').value = endHHMM;

        document.getElementById('submitHint').textContent = 'Siap dibuat.';
      } else {
        document.getElementById('summaryStart').textContent = '-';
        document.getElementById('summaryEnd').textContent = '-';
        document.getElementById('startDateInput').value = '';
        document.getElementById('startTimeInput').value = '';
        document.getElementById('endDateInput').value = '';
        document.getElementById('endTimeInput').value = '';
        document.getElementById('submitHint').textContent = 'Pilih jadwal dulu, lalu tambahkan menu jika diperlukan.';
      }
    }

    function updateTotals() {
      let menuTotal = 0;
      for (const it of cart.values()) {
        menuTotal += (it.price || 0) * (it.qty || 0);
      }

      const rental = calcRental();
      const grand = menuTotal + rental;

      document.getElementById('sumMenu').textContent = fmtRp(menuTotal);
      document.getElementById('sumGrand').textContent = fmtRp(grand);
      document.getElementById('sumDp').textContent = fmtRp(grand * DP_RATIO);

      updateScheduleSummary();
    }

    function renderCalendar() {
      const title = document.getElementById('calTitle');
      const grid = document.getElementById('calGrid');
      grid.innerHTML = '';

      const y = calMonth.getFullYear();
      const m = calMonth.getMonth();

      title.textContent = calMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

      const first = new Date(y, m, 1);
      const last = new Date(y, m + 1, 0);
      const firstDay = ((first.getDay() + 6) % 7);
      const daysInMonth = last.getDate();

      for (let i = 0; i < 42; i++) {
        const dayNum = i - firstDay + 1;
        const cell = document.createElement('button');
        cell.type = 'button';

        if (dayNum < 1 || dayNum > daysInMonth) {
          cell.disabled = true;
          cell.className = 'h-12 rounded-xl border border-white/5 bg-black/10 text-white/20';
          cell.textContent = '';
          grid.appendChild(cell);
          continue;
        }

        const dateObj = new Date(y, m, dayNum);
        const iso = toISODate(dateObj);

        cell.dataset.date = iso;
        cell.textContent = String(dayNum);
        cell.className = 'h-12 rounded-xl border border-white/12 bg-white/[0.04] text-sm font-semibold hover:bg-white/[0.07]';

        if (selectedDate === iso) {
          cell.className += ' ring-2 ring-yellow-400/60';
        }

        cell.onclick = async () => {
          selectedDate = iso;
          selectedStart = null;
          selectedDuration = null;
          availability = null;

          await loadAvailabilityForDate();
          renderCalendar();
          renderSlots();
          renderDurations();
          updateTotals();
        };

        grid.appendChild(cell);
      }
    }

    async function loadAvailabilityForDate() {
      const resourceId = document.getElementById('resourceSelect').value;
      const url = new URL("{{ route('public.reservations.availability') }}", window.location.origin);
      url.searchParams.set('reservation_resource_id', resourceId);
      url.searchParams.set('date', selectedDate);

      const res = await fetch(url.toString());
      availability = await res.json();
    }

    function renderSlots() {
      const meta = document.getElementById('slotMeta');
      const grid = document.getElementById('slotGrid');
      grid.innerHTML = '';

      if (!availability) {
        meta.textContent = 'Pilih tanggal dulu.';
        return;
      }

      meta.textContent = `Tanggal ${selectedDate} • pilih jam tersedia`;

      const open = parseHHMM(availability.open);
      const close = parseHHMM(availability.close);
      const minDur = parseInt(availability.min_duration_minutes || 60, 10);
      const step = Math.max(5, parseInt(availability.slot_minutes || 30, 10));
      const booked = availability.booked || [];

      let hasAvailable = false;

      for (let t = open; t + minDur <= close; t += step) {
        const hhmm = fromMin(t);
        const disabled = isOverlap(t, t + minDur, booked);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = hhmm;
        btn.disabled = disabled;
        btn.className = 'rounded-xl border px-3 py-2 text-xs font-semibold ' +
          (disabled
            ? 'border-white/10 bg-black/20 text-white/35 cursor-not-allowed'
            : 'border-white/12 bg-white/[0.04] hover:bg-white/[0.07]');

        btn.onclick = () => {
          selectedStart = hhmm;
          selectedDuration = null;

          [...grid.querySelectorAll('button')].forEach(x => {
            const active = x.textContent === hhmm;
            x.classList.toggle('ring-2', active);
            x.classList.toggle('ring-yellow-400/60', active);
          });

          renderDurations();
          updateTotals();
        };

        if (!disabled) hasAvailable = true;
        grid.appendChild(btn);
      }

      if (!hasAvailable) {
        meta.textContent = `Tanggal ${selectedDate} penuh. Pilih tanggal lain.`;
      }
    }

    function renderDurations() {
      const sel = document.getElementById('durationSelect');
      sel.innerHTML = '';

      if (!availability || !selectedStart) {
        sel.disabled = true;
        sel.innerHTML = `<option value="">Pilih jam dulu</option>`;
        return;
      }

      sel.disabled = false;

      const start = parseHHMM(selectedStart);
      const close = parseHHMM(availability.close);
      const minDur = parseInt(availability.min_duration_minutes || 60, 10);
      const step = Math.max(5, parseInt(availability.slot_minutes || 30, 10));
      const booked = availability.booked || [];

      let first = true;

      for (let dur = minDur; start + dur <= close; dur += step) {
        if (isOverlap(start, start + dur, booked)) break;

        const opt = document.createElement('option');
        opt.value = String(dur);
        const h = Math.floor(dur / 60);
        const m = dur % 60;
        opt.textContent = (h > 0 ? `${h} jam ` : '') + (m > 0 ? `${m} menit` : '');

        if (first) {
          opt.selected = true;
          selectedDuration = dur;
          first = false;
        }

        sel.appendChild(opt);
      }

      if (first) {
        sel.innerHTML = `<option value="">Tidak ada durasi tersedia</option>`;
        selectedDuration = null;
        sel.disabled = true;
        updateTotals();
        return;
      }

      sel.onchange = () => {
        selectedDuration = parseInt(sel.value || '0', 10) || null;
        updateTotals();
      };
    }

    document.getElementById('openScheduleModal').onclick = () => {
      scheduleModal.classList.remove('hidden');
      renderCalendar();
      renderSlots();
      renderDurations();
    };

    document.getElementById('closeScheduleModal').onclick = () => {
      scheduleModal.classList.add('hidden');
    };

    document.getElementById('calPrev').onclick = () => {
      calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() - 1, 1);
      renderCalendar();
    };

    document.getElementById('calNext').onclick = () => {
      calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() + 1, 1);
      renderCalendar();
    };

    document.getElementById('calToday').onclick = () => {
      const t = new Date();
      calMonth = new Date(t.getFullYear(), t.getMonth(), 1);
      renderCalendar();
    };

    document.getElementById('applySchedule').onclick = () => {
      if (!selectedDate || !selectedStart || !selectedDuration) {
        alert('Pilih tanggal, jam, dan durasi dulu.');
        return;
      }

      scheduleModal.classList.add('hidden');
      updateTotals();
    };

    document.getElementById('resourceSelect').addEventListener('change', () => {
      selectedDate = null;
      selectedStart = null;
      selectedDuration = null;
      availability = null;
      updateResourceMeta();
      updateTotals();
    });

    const menuModal = document.getElementById('menuModal');
    const menuGrid = document.getElementById('menuGrid');
    const menuMeta = document.getElementById('menuMeta');
    const loadMoreBtn = document.getElementById('loadMore');

    let page = 1;
    let lastPage = 1;
    let query = '';

    function openMenuModal() {
      menuModal.classList.remove('hidden');
      page = 1;
      lastPage = 1;
      query = '';
      document.getElementById('menuSearch').value = '';
      menuGrid.innerHTML = '';
      fetchMenuPage();
    }

    function closeMenuModal() {
      menuModal.classList.add('hidden');
    }

    document.getElementById('openMenuModal').onclick = openMenuModal;
    document.getElementById('closeMenuModal').onclick = closeMenuModal;

    document.getElementById('applySelection').onclick = () => {
      closeMenuModal();
      rebuildHiddenItems();
      renderSelectedList();
      updateTotals();
    };

    document.getElementById('menuSearchBtn').onclick = () => {
      query = document.getElementById('menuSearch').value.trim();
      page = 1;
      menuGrid.innerHTML = '';
      fetchMenuPage();
    };

    document.getElementById('menuSearch').addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('menuSearchBtn').click();
      }
    });

    loadMoreBtn.onclick = () => {
      if (page < lastPage) {
        page++;
        fetchMenuPage();
      }
    };

    async function fetchMenuPage() {
      menuMeta.textContent = 'Memuat menu…';

      const url = new URL("{{ route('public.reservations.products') }}", window.location.origin);
      url.searchParams.set('page', String(page));
      url.searchParams.set('per_page', '12');
      if (query) url.searchParams.set('q', query);

      const res = await fetch(url.toString());
      const json = await res.json();

      lastPage = json.meta.last_page || 1;
      menuMeta.textContent = `Halaman ${json.meta.current_page} / ${json.meta.last_page} • Total ${json.meta.total}`;
      loadMoreBtn.disabled = page >= lastPage;

      (json.data || []).forEach(p => {
        const max = (p.max_available ?? 0);
        const disabled = max <= 0;
        const currentQty = cart.get(p.id)?.qty || 0;

        if (!cart.has(p.id)) {
          cart.set(p.id, { id: p.id, name: p.name, price: p.price, max: max, qty: 0 });
        } else {
          const it = cart.get(p.id);
          it.max = max;
          it.name = p.name;
          it.price = p.price;
          cart.set(p.id, it);
        }

        const card = document.createElement('div');
        card.className = 'rounded-[22px] border border-white/10 bg-white/[0.04] p-4';
        card.innerHTML = `
        <div class="flex gap-3">
          <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl border border-white/10 bg-black/30">
            ${p.image_url
            ? `<img src="${p.image_url}" class="h-full w-full object-cover">`
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
              <div class="text-xs text-white/55">
                ${disabled ? 'Tidak tersedia' : `Max: <span class="font-semibold text-white/85">${max}</span>`}
              </div>
              <div class="flex items-center gap-2">
                <button type="button" class="m-minus rounded-xl border border-white/12 bg-white/[0.05] px-3 py-2 text-sm font-bold" data-id="${p.id}" ${disabled ? 'disabled' : ''}>−</button>
                <div class="w-10 text-center font-semibold" id="q-${p.id}">${currentQty}</div>
                <button type="button" class="m-plus rounded-xl border border-white/12 bg-white/[0.05] px-3 py-2 text-sm font-bold" data-id="${p.id}" ${disabled ? 'disabled' : ''}>+</button>
              </div>
            </div>
          </div>
        </div>
      `;
        menuGrid.appendChild(card);
      });
    }

    menuGrid.onclick = (e) => {
      const plus = e.target.closest('.m-plus');
      const minus = e.target.closest('.m-minus');
      if (!plus && !minus) return;

      const id = parseInt((plus || minus).dataset.id, 10);
      const it = cart.get(id);
      if (!it) return;

      const max = (it.max ?? 999999);
      if (plus) it.qty = Math.min(max, it.qty + 1);
      if (minus) it.qty = Math.max(0, it.qty - 1);

      cart.set(id, it);
      const qEl = document.getElementById('q-' + id);
      if (qEl) qEl.textContent = String(it.qty);
    };

    renderSelectedList();
    updateResourceMeta();
    updateTotals();
  </script>
</body>

</html>