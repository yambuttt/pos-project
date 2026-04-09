@extends('layouts.admin')
@section('title', 'Edit Shift Pegawai')

@section('body')
  <div class="mx-auto w-full max-w-6xl">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="text-lg font-semibold text-white">Edit Shift Pegawai</div>
        <div class="mt-1 text-sm text-white/60">{{ $user->name }} • {{ $user->email }}</div>
      </div>

      <a href="{{ route('admin.shifts.index') }}"
         class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-4 py-2 text-sm text-yellow-400 hover:bg-white/[0.08]">
        ← Kembali
      </a>
    </div>

    @if (session('ok'))
      <div class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
        {{ session('ok') }}
      </div>
    @endif
    @if (session('error'))
      <div class="mt-4 rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-100">
        {{ session('error') }}
      </div>
    @endif

    {{-- 2 COLUMN LAYOUT --}}
    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-[1.2fr_.8fr]">
      {{-- LEFT: SHIFT SETTINGS --}}
      <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
        <form method="POST" action="{{ route('admin.shifts.update', $user) }}" class="space-y-5">
          @csrf
          @method('PUT')

          <div>
            <label class="text-sm text-white/80">Skema Shift</label>
            <select id="shift_scheme" name="shift_scheme"
              class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
              <option value="fixed" {{ old('shift_scheme', $user->shift_scheme ?? 'fixed') === 'fixed' ? 'selected' : '' }}>
                Fixed (tetap)
              </option>
              <option value="rotation" {{ old('shift_scheme', $user->shift_scheme ?? 'fixed') === 'rotation' ? 'selected' : '' }}>
                Rotation (ABAB / Mingguan)
              </option>
            </select>
          </div>

          <div>
            <label class="text-sm text-white/80">Default Shift (fallback)</label>
            <select name="default_shift_id"
              class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
              <option value="">-</option>
              @foreach($shifts as $s)
                <option value="{{ $s->id }}" {{ (string) old('default_shift_id', $user->default_shift_id) === (string) $s->id ? 'selected' : '' }}>
                  {{ $s->name }}
                </option>
              @endforeach
            </select>
            <div class="mt-2 text-xs text-white/60">
              Dipakai jika tidak ada rotation/override untuk hari itu.
            </div>
          </div>

          {{-- Rotation box --}}
          @php
            $scheme = old('shift_scheme', $user->shift_scheme ?? 'fixed');
            $rotType = old('rotation_type', $rotation?->rotation_type);
            $rotStart = old('rotation_start_date', $rotation?->start_date?->toDateString());
            $rotFirst = old('rotation_first_shift_id', $rotation?->first_shift_id);
            $weekStart = old('rotation_week_starts_on', $rotation?->week_starts_on ?? 'monday');
          @endphp

          <div id="rotationBox"
            class="rounded-2xl border border-white/15 bg-white/5 p-5 {{ $scheme === 'rotation' ? '' : 'hidden' }}">
            <div class="text-sm font-semibold text-white">Rotation Settings</div>
            <div class="mt-1 text-xs text-white/70">
              Pilih pola rotasi. Override tanggal tertentu tetap bisa dibuat di kanan.
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
              <div>
                <label class="text-sm text-white/80">Tipe Rotation</label>
                <select name="rotation_type"
                  class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
                  <option value="">- pilih -</option>
                  <option value="daily_alternate" {{ $rotType === 'daily_alternate' ? 'selected' : '' }}>
                    Selang-seling harian (A B A B)
                  </option>
                  <option value="weekly_alternate" {{ $rotType === 'weekly_alternate' ? 'selected' : '' }}>
                    Seminggu A, seminggu B
                  </option>
                </select>
              </div>

              <div>
                <label class="text-sm text-white/80">Tanggal Mulai</label>
                <input type="date" name="rotation_start_date" value="{{ $rotStart }}"
                  class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40" />
              </div>

              <div>
                <label class="text-sm text-white/80">Shift Awal</label>
                <select name="rotation_first_shift_id"
                  class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
                  <option value="">- pilih -</option>
                  @foreach($shifts as $s)
                    <option value="{{ $s->id }}" {{ (string) $rotFirst === (string) $s->id ? 'selected' : '' }}>
                      {{ $s->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div>
                <label class="text-sm text-white/80">Patokan minggu mulai</label>
                <select name="rotation_week_starts_on"
                  class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
                  <option value="monday" {{ $weekStart === 'monday' ? 'selected' : '' }}>Senin</option>
                  <option value="sunday" {{ $weekStart === 'sunday' ? 'selected' : '' }}>Minggu</option>
                </select>
              </div>
            </div>
          </div>

          <button class="w-full rounded-xl bg-yellow-500 px-5 py-3 text-sm font-semibold text-black hover:bg-yellow-400">
            Simpan Setting Shift
          </button>
        </form>
      </div>

      {{-- RIGHT: OVERRIDES --}}
      <div class="space-y-5">
        <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
          <div class="text-sm font-semibold">Override Shift (Tukeran / Khusus)</div>
          <div class="mt-1 text-xs text-white/70">
            Override menang atas fixed/rotation untuk tanggal tertentu.
          </div>

          <form method="POST" action="{{ route('admin.shifts.override.store', $user) }}" class="mt-4 space-y-3">
            @csrf

            <div>
              <label class="text-sm text-white/80">Tanggal</label>
              <input type="date" name="date" value="{{ old('date') }}"
                class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40" />
            </div>

            <div>
              <label class="text-sm text-white/80">Shift</label>
              <select name="shift_id"
                class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
                @foreach($shifts as $s)
                  <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="text-sm text-white/80">Alasan (opsional)</label>
              <input name="reason" value="{{ old('reason') }}"
                class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
                placeholder="Contoh: Tukeran shift / kebutuhan operasional" />
            </div>

            <button class="w-full rounded-xl border border-white/15 bg-white/[0.04] px-4 py-3 text-sm font-semibold text-white/85 hover:bg-white/[0.08]">
              Simpan Override
            </button>
          </form>
        </div>

        <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
          <div class="text-sm font-semibold">Riwayat Override (Terbaru)</div>
          <div class="mt-4 space-y-3">
            @forelse($overrides as $ov)
              <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="text-sm font-semibold text-white">{{ $ov->date?->format('d M Y') }}</div>
                    <div class="mt-1 text-xs text-white/70">
                      Shift: <span class="text-white/85">{{ $ov->shift?->name ?? '-' }}</span>
                    </div>
                    <div class="mt-1 text-xs text-white/60">{{ $ov->reason ?? '-' }}</div>
                  </div>

                  <form method="POST" action="{{ route('admin.shifts.override.delete', $ov) }}">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/85 hover:bg-white/[0.08]">
                      Hapus
                    </button>
                  </form>
                </div>
              </div>
            @empty
              <div class="text-sm text-white/60">Belum ada override.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
    {{-- END GRID --}}

    {{-- FULL WIDTH CALENDAR --}}
    <div class="mt-6 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <div class="text-sm font-semibold">Kalender Shift</div>
          <div class="mt-1 text-xs text-white/70">Shift harian + status (Hadir/Alpha/Cuti/Sakit). OVR = Override.</div>
        </div>

        <div class="flex flex-wrap gap-2 text-[11px] sm:text-xs">
          <span class="rounded-full px-3 py-1 font-semibold text-black" style="background:#22c55e">Shift A (10–19)</span>
          <span class="rounded-full px-3 py-1 font-semibold text-black" style="background:#f59e0b">Shift B (13–22)</span>
          <span class="rounded-full px-3 py-1 font-semibold text-white" style="background:#3b82f6">HADIR</span>
          <span class="rounded-full px-3 py-1 font-semibold text-white" style="background:#6b7280">ALPHA</span>
          <span class="rounded-full px-3 py-1 font-semibold text-white" style="background:#a855f7">CUTI</span>
          <span class="rounded-full px-3 py-1 font-semibold text-white" style="background:#ef4444">SAKIT</span>
          <span class="rounded-full px-3 py-1 font-semibold text-black" style="background:#f472b6">OVR</span>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-3">
        <div id="shiftCalendar"></div>
      </div>

      <div id="shiftCalHint" class="mt-3 rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3 text-sm text-white/85">
        Klik event untuk melihat detail.
      </div>
    </div>
  </div>

  {{-- Script: toggle rotation box --}}
  <script>
    (function () {
      const scheme = document.getElementById('shift_scheme');
      const box = document.getElementById('rotationBox');
      if (!scheme || !box) return;

      function sync() {
        const v = scheme.value;
        if (v === 'rotation') box.classList.remove('hidden');
        else box.classList.add('hidden');
      }

      scheme.addEventListener('change', sync);
      sync();
    })();
  </script>

  {{-- FullCalendar (gunakan unpkg agar tidak kena MIME block) --}}
  <link rel="stylesheet" href="https://unpkg.com/fullcalendar@6.1.15/index.global.min.css">
  <script src="https://unpkg.com/fullcalendar@6.1.15/index.global.min.js"></script>

  <style>
    .fc {
      --fc-border-color: rgba(255,255,255,.10);
      --fc-page-bg-color: transparent;
      --fc-neutral-bg-color: rgba(255,255,255,.02);
      --fc-today-bg-color: rgba(234,179,8,.10);
      color: rgba(255,255,255,.90);
    }
    .fc .fc-toolbar-title { font-weight: 800; color:#fff; font-size:18px; }
    .fc .fc-button {
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(234,179,8,.18);
      color: rgba(255,255,255,.9);
      border-radius: 12px;
      padding: 6px 10px;
    }
    .fc .fc-button:hover { background: rgba(255,255,255,.10); }
    .fc .fc-button-primary:not(:disabled).fc-button-active {
      background: rgba(234,179,8,.22);
      border-color: rgba(234,179,8,.35);
    }
    .fc .fc-col-header-cell-cushion { color: rgba(255,255,255,.85); font-weight:700; font-size:12px; }
    .fc .fc-daygrid-day-number { color: rgba(255,255,255,.75); font-weight:700; padding:6px; }

    .fc .fc-daygrid-event {
      border-radius: 999px;
      padding: 3px 8px;
      font-size: 12px;
      font-weight: 800;
      line-height: 1.2;
      margin: 3px 4px;
    }
    .fc .fc-daygrid-day-frame { min-height: 88px; }

    .fc-pill{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    @media (max-width:640px){
      .fc .fc-daygrid-event{ padding:2px 6px; font-size:11px; margin:2px 2px; }
      .fc .fc-daygrid-day-frame{ min-height:72px; }
    }
  </style>

  <script>
    (function () {
      const el = document.getElementById('shiftCalendar');
      const hint = document.getElementById('shiftCalHint');
      if (!el || typeof FullCalendar === 'undefined') return;

      const calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        firstDay: 1,
        height: 'auto',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
        eventDisplay: 'block',
        dayMaxEvents: true,

        events: function(fetchInfo, successCallback, failureCallback) {
          const url = new URL("{{ route('admin.shifts.calendar', $user) }}", window.location.origin);
          url.searchParams.set('start', fetchInfo.startStr);
          url.searchParams.set('end', fetchInfo.endStr);

          fetch(url.toString(), { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(successCallback)
            .catch(failureCallback);
        },

        eventClick: function(info) {
          const p = info.event.extendedProps || {};
          const date = info.event.start ? info.event.start.toLocaleDateString('id-ID') : '';
          const shiftName = p.shift_name || info.event.title;
          const status = p.status || '-';
          const emoji = { HADIR:'✅', ALPHA:'❌', CUTI:'🟣', SAKIT:'🩺', SCHEDULE:'🗓️' }[status] || '🗓️';

          let msg = `${emoji} ${date}\n${shiftName}\nStatus: ${status}`;
          if (status === 'CUTI' || status === 'SAKIT') msg += `\nKeterangan: ${p.status_reason || '-'}`;
          if (p.is_override) msg += `\nOverride: ya\nAlasan override: ${p.override_reason || '-'}`;

          if (hint) hint.textContent = msg;
        },

        eventContent: function(arg) {
          const p = arg.event.extendedProps || {};
          const status = p.status || '';
          const shiftCode = p.shift_code || '';

          const shortMap = { HADIR:'H', ALPHA:'A', CUTI:'C', SAKIT:'S', SCHEDULE:'' };
          const shortStatus = shortMap[status] ?? '';

          const isMobile = window.matchMedia('(max-width: 640px)').matches;
          let text = arg.event.title;

          if (isMobile) {
            text = shortStatus ? `${shiftCode} ${shortStatus}` : `${shiftCode}`;
            if (p.is_override) text = `OVR ${text}`;
          }

          return { html: `<div class="fc-pill">${text}</div>` };
        },
      });

      calendar.render();
    })();
  </script>
@endsection