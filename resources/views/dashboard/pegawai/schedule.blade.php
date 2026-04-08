@extends('layouts.pegawai')
@section('title', 'Jadwal Kerja')

@section('page_label', 'Pegawai')
@section('page_title', 'Jadwal Kerja')

@section('content')
  <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="text-sm font-semibold">Kalender Jadwal Kerja</div>
        <div class="mt-1 text-xs text-white/70">
          Lihat shift harian kamu. Label merah = override (tukeran/khusus).
        </div>
      </div>

      <div class="flex flex-wrap gap-2 text-xs">
        <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1 text-white/80">A = 10:00–19:00</span>
        <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1 text-white/80">B = 13:00–22:00</span>
        <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1 text-white/80">Override</span>
      </div>
    </div>

    <div class="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-3">
      <div id="pegawaiCalendar"></div>
    </div>

    <div id="pegawaiCalHint" class="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3 text-sm text-white/85">
      Klik event untuk melihat detail.
    </div>
  </div>

  {{-- FullCalendar --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

  <style>
    .fc {
      --fc-border-color: rgba(255,255,255,.10);
      --fc-page-bg-color: transparent;
      --fc-neutral-bg-color: rgba(255,255,255,.02);
      --fc-today-bg-color: rgba(234,179,8,.10);
      color: rgba(255,255,255,.90);
    }

    .fc .fc-toolbar-title {
      font-weight: 800;
      color: #fff;
      font-size: 18px;
    }

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

    .fc .fc-col-header-cell-cushion {
      color: rgba(255,255,255,.85);
      font-weight: 700;
      font-size: 12px;
    }

    .fc .fc-daygrid-day-number {
      color: rgba(255,255,255,.75);
      font-weight: 700;
      padding: 6px;
    }

    .fc .fc-daygrid-event {
      border-radius: 999px;
      padding: 3px 8px;
      font-size: 12px;
      font-weight: 800;
      line-height: 1.2;
      margin: 3px 4px;
    }

    .fc .fc-daygrid-day-frame { min-height: 92px; }
  </style>

  <script>
    (function () {
      const el = document.getElementById('pegawaiCalendar');
      const hint = document.getElementById('pegawaiCalHint');
      if (!el || typeof FullCalendar === 'undefined') return;

      const calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        height: 'auto',
        firstDay: 1, // Senin
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek'
        },
        eventDisplay: 'block',
        dayMaxEvents: true,

        events: function(fetchInfo, successCallback, failureCallback) {
          const url = new URL("{{ route('pegawai.schedule.calendar') }}", window.location.origin);
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

          const msg = p.is_override
            ? `📌 ${date} • Override\n${shiftName}\nAlasan: ${p.override_reason || '-'}`
            : `🗓️ ${date}\n${shiftName}`;

          if (hint) hint.textContent = msg;
        }
      });

      calendar.render();
    })();
  </script>
@endsection