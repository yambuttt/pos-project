@extends('layouts.admin')
@section('title', 'Konfigurasi Shift Pegawai')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div class="flex items-center gap-4">
      <button id="openMobileSidebar" type="button"
        class="inline-flex lg:hidden items-center justify-center w-10 h-10 rounded-xl border border-white/10 bg-white/5 text-white/70 hover:bg-white/10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
      <div>
        <h1 class="text-3xl font-bold text-gold-gradient">Konfigurasi Shift</h1>
        <p class="text-sm text-white/40 font-medium italic">Personil: <span class="text-gold-primary font-bold not-italic">{{ $user->name }}</span> <span class="mx-2 text-white/10">•</span> {{ $user->email }}</p>
      </div>
    </div>

    <a href="{{ route('admin.shifts.index') }}"
      class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-6 py-3.5 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Daftar
    </a>
  </div>

  @if (session('ok'))
    <div class="mb-8 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-green-100">{{ session('ok') }}</p>
    </div>
  @endif

  <div class="grid grid-cols-1 gap-10 lg:grid-cols-[1.2fr_.8fr] items-start">
    <!-- LEFT: SHIFT SCHEME SETTINGS -->
    <div class="glass-panel p-8 sm:p-10 rounded-[3rem] relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
        
        <form method="POST" action="{{ route('admin.shifts.update', $user) }}" class="relative z-10 space-y-10">
           @csrf
           @method('PUT')

           <div class="space-y-6">
              <div class="flex items-center gap-3">
                 <div class="w-2 h-2 rounded-full bg-gold-primary shadow-lg shadow-gold-primary/20"></div>
                 <h3 class="text-xs font-black text-white uppercase tracking-[0.3em]">Core Scheduling</h3>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                 <div class="space-y-3">
                    <label class="text-[10px] font-black text-white/20 uppercase tracking-widest ml-1">System Scheme</label>
                    <div class="relative">
                       <select id="shift_scheme" name="shift_scheme" class="w-full rounded-2xl bg-white/5 border border-white/10 px-5 py-4 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all appearance-none pr-10">
                          <option value="fixed" {{ old('shift_scheme', $user->shift_scheme ?? 'fixed') === 'fixed' ? 'selected' : '' }} class="bg-obsidian-900">Fixed Pattern (Statis)</option>
                          <option value="rotation" {{ old('shift_scheme', $user->shift_scheme ?? 'fixed') === 'rotation' ? 'selected' : '' }} class="bg-obsidian-900">Dynamic Rotation (ABAB)</option>
                       </select>
                       <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-white/20">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                       </div>
                    </div>
                 </div>

                 <div class="space-y-3">
                    <label class="text-[10px] font-black text-white/20 uppercase tracking-widest ml-1">Fallback Allocation</label>
                    <div class="relative">
                       <select name="default_shift_id" class="w-full rounded-2xl bg-white/5 border border-white/10 px-5 py-4 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all appearance-none pr-10">
                          <option value="" class="bg-obsidian-900">- Select Shift -</option>
                          @foreach($shifts as $s)
                             <option value="{{ $s->id }}" {{ (string) old('default_shift_id', $user->default_shift_id) === (string) $s->id ? 'selected' : '' }} class="bg-obsidian-900">{{ $s->name }}</option>
                          @endforeach
                       </select>
                       <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-white/20">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                       </div>
                    </div>
                 </div>
              </div>
           </div>

           <!-- ROTATION SETTINGS -->
           @php
             $scheme = old('shift_scheme', $user->shift_scheme ?? 'fixed');
             $rotType = old('rotation_type', $rotation?->rotation_type);
             $rotStart = old('rotation_start_date', $rotation?->start_date?->toDateString());
             $rotFirst = old('rotation_first_shift_id', $rotation?->first_shift_id);
             $weekStart = old('rotation_week_starts_on', $rotation?->week_starts_on ?? 'monday');
           @endphp

           <div id="rotationBox" class="p-8 rounded-[2.5rem] bg-black/40 border border-white/5 space-y-8 {{ $scheme === 'rotation' ? '' : 'hidden' }}">
              <div class="flex items-center gap-3">
                 <div class="w-8 h-8 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                 </div>
                 <h3 class="text-xs font-black text-white uppercase tracking-[0.2em]">Rotation Logic</h3>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                 <div class="space-y-3">
                    <label class="text-[10px] font-black text-white/20 uppercase tracking-widest ml-1">Rotation Type</label>
                    <select name="rotation_type" class="w-full rounded-2xl bg-white/5 border border-white/10 px-5 py-4 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all appearance-none pr-10">
                       <option value="" class="bg-obsidian-900">- Select Mode -</option>
                       <option value="daily_alternate" {{ $rotType === 'daily_alternate' ? 'selected' : '' }} class="bg-obsidian-900">Daily Alternate (A B A B)</option>
                       <option value="weekly_alternate" {{ $rotType === 'weekly_alternate' ? 'selected' : '' }} class="bg-obsidian-900">Weekly Flip (7A -> 7B)</option>
                    </select>
                 </div>
                 <div class="space-y-3">
                    <label class="text-[10px] font-black text-white/20 uppercase tracking-widest ml-1">Cycle Starts From</label>
                    <input type="date" name="rotation_start_date" value="{{ $rotStart }}" class="w-full rounded-2xl bg-white/5 border border-white/10 px-5 py-4 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all">
                 </div>
                 <div class="space-y-3">
                    <label class="text-[10px] font-black text-white/20 uppercase tracking-widest ml-1">Initial Primary Shift (A)</label>
                    <select name="rotation_first_shift_id" class="w-full rounded-2xl bg-white/5 border border-white/10 px-5 py-4 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all appearance-none pr-10">
                       <option value="" class="bg-obsidian-900">- Select First Shift -</option>
                       @foreach($shifts as $s)
                          <option value="{{ $s->id }}" {{ (string) $rotFirst === (string) $s->id ? 'selected' : '' }} class="bg-obsidian-900">{{ $s->name }}</option>
                       @endforeach
                    </select>
                 </div>
                 <div class="space-y-3">
                    <label class="text-[10px] font-black text-white/20 uppercase tracking-widest ml-1">Week Anchor</label>
                    <select name="rotation_week_starts_on" class="w-full rounded-2xl bg-white/5 border border-white/10 px-5 py-4 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all appearance-none pr-10">
                       <option value="monday" {{ $weekStart === 'monday' ? 'selected' : '' }} class="bg-obsidian-900">Monday (Senin)</option>
                       <option value="sunday" {{ $weekStart === 'sunday' ? 'selected' : '' }} class="bg-obsidian-900">Sunday (Minggu)</option>
                    </select>
                 </div>
              </div>
           </div>

           <button class="w-full py-5 rounded-3xl bg-gradient-to-r from-gold-primary to-gold-dark text-xs font-black text-obsidian-950 uppercase tracking-[0.3em] shadow-2xl shadow-gold-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
              Update Configuration
           </button>
        </form>
    </div>

    <!-- RIGHT: OVERRIDES -->
    <div class="space-y-10">
        <!-- ADD OVERRIDE -->
        <div class="glass-panel p-8 rounded-[3rem] border-white/5 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-gold-primary/5 blur-2xl rounded-full"></div>
            
            <div class="relative z-10 space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                   </div>
                   <h3 class="text-xs font-black text-white uppercase tracking-[0.2em]">Manual Override</h3>
                </div>

                <form method="POST" action="{{ route('admin.shifts.override.store', $user) }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                       <div class="space-y-2">
                          <label class="text-[8px] font-black text-white/20 uppercase tracking-widest ml-1">Target Date</label>
                          <input type="date" name="date" value="{{ old('date') }}" class="w-full rounded-2xl bg-white/5 border border-white/10 px-4 py-3 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all">
                       </div>
                       <div class="space-y-2">
                          <label class="text-[8px] font-black text-white/20 uppercase tracking-widest ml-1">New Shift</label>
                          <select name="shift_id" class="w-full rounded-2xl bg-white/5 border border-white/10 px-4 py-3 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all appearance-none pr-8">
                             @foreach($shifts as $s)
                                <option value="{{ $s->id }}" class="bg-obsidian-900">{{ $s->name }}</option>
                             @endforeach
                          </select>
                       </div>
                    </div>
                    <div class="space-y-2">
                       <label class="text-[8px] font-black text-white/20 uppercase tracking-widest ml-1">Official Justification</label>
                       <input name="reason" value="{{ old('reason') }}" placeholder="Ex: Tukeran Shift / Maintenance..." class="w-full rounded-2xl bg-white/5 border border-white/10 px-4 py-3 text-xs font-bold text-white placeholder:text-white/20 outline-none focus:border-gold-primary/30 transition-all">
                    </div>
                    <button class="w-full py-4 rounded-2xl bg-white/5 border border-white/10 text-[10px] font-black text-gold-primary uppercase tracking-[0.2em] hover:bg-gold-primary/10 active:scale-95 transition-all">
                       Apply Override
                    </button>
                </form>
            </div>
        </div>

        <!-- HISTORY OVERRIDES -->
        <div class="glass-panel p-8 rounded-[3rem] border-white/5 relative overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                   </div>
                   <h3 class="text-xs font-black text-white uppercase tracking-[0.2em]">Recent Incidents</h3>
                </div>
                <span class="text-[9px] font-black text-white/20 uppercase tracking-widest">{{ count($overrides) }} OVR</span>
            </div>

            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
               @forelse($overrides as $ov)
                  <div class="p-5 rounded-3xl bg-white/[0.02] border border-white/5 flex items-center justify-between group">
                     <div>
                        <div class="text-[11px] font-bold text-white">{{ $ov->date?->format('d M Y') }}</div>
                        <div class="text-[9px] text-white/30 italic uppercase mt-0.5">{{ $ov->shift?->name ?: 'System Shift' }}</div>
                        @if($ov->reason)
                           <div class="text-[8px] text-gold-primary/40 font-medium mt-1 truncate max-w-[120px]">"{{ $ov->reason }}"</div>
                        @endif
                     </div>
                     <form method="POST" action="{{ route('admin.shifts.override.delete', $ov) }}">
                        @csrf
                        @method('DELETE')
                        <button class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-white/20 hover:text-red-400 hover:border-red-500/20 active:scale-90 transition-all">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                           </svg>
                        </button>
                     </form>
                  </div>
               @empty
                  <div class="text-center py-10">
                     <div class="text-[9px] font-black text-white/10 uppercase tracking-widest italic">All Scheduled as Planned</div>
                  </div>
               @endforelse
            </div>
        </div>
    </div>
  </div>

  <!-- FULL WIDTH CALENDAR -->
  <div class="mt-10 glass-panel p-8 sm:p-10 rounded-[3rem] border-white/5 relative overflow-hidden">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between mb-8">
          <div>
             <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                   </svg>
                </div>
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em]">Deployment Calendar</h3>
             </div>
             <p class="text-[10px] text-white/30 italic">Visualisasi jadwal shift harian beserta riwayat kehadiran personil.</p>
          </div>

          <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-black text-emerald-400 uppercase tracking-widest">Hadir</span>
              <span class="px-3 py-1.5 rounded-xl bg-red-500/10 border border-red-500/20 text-[9px] font-black text-red-400 uppercase tracking-widest">Alpha</span>
              <span class="px-3 py-1.5 rounded-xl bg-purple-500/10 border border-purple-500/20 text-[9px] font-black text-purple-400 uppercase tracking-widest">Cuti</span>
              <span class="px-3 py-1.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-[9px] font-black text-blue-400 uppercase tracking-widest">Sakit</span>
              <span class="px-3 py-1.5 rounded-xl bg-gold-primary/10 border border-gold-primary/20 text-[9px] font-black text-gold-primary uppercase tracking-widest">Override</span>
          </div>
      </div>

      <div class="rounded-[2.5rem] bg-black/40 border border-white/5 p-6 sm:p-8">
          <div id="shiftCalendar"></div>
      </div>

      <div id="shiftCalHint" class="mt-8 p-6 rounded-3xl bg-white/[0.02] border border-white/5 text-[11px] text-white/40 italic text-center">
         <span class="not-italic opacity-100 font-bold text-gold-primary uppercase tracking-[0.1em] mr-2">Pro Tip:</span> Klik pada event kalender untuk melihat detail metadata absensi harian.
      </div>
  </div>

  <script>
    (function () {
      const scheme = document.getElementById('shift_scheme');
      const box = document.getElementById('rotationBox');
      if (!scheme || !box) return;

      function sync() {
        const v = scheme.value;
        if (v === 'rotation') {
            box.classList.remove('hidden');
            box.classList.add('animate-fade-in');
        } else {
            box.classList.add('hidden');
        }
      }

      scheme.addEventListener('change', sync);
      sync();
    })();
  </script>

  <link rel="stylesheet" href="https://unpkg.com/fullcalendar@6.1.15/index.global.min.css">
  <script src="https://unpkg.com/fullcalendar@6.1.15/index.global.min.js"></script>

  <style>
    .fc {
      --fc-border-color: rgba(255,255,255,.05);
      --fc-page-bg-color: transparent;
      --fc-neutral-bg-color: rgba(255,255,255,.02);
      --fc-today-bg-color: rgba(234,179,8,.05);
      color: rgba(255,255,255,.8);
      font-family: inherit;
    }
    .fc .fc-toolbar-title { font-weight: 900; color:#fff; font-size:18px; text-transform: uppercase; letter-spacing: 0.1em; }
    .fc .fc-button {
      background: rgba(255,255,255,.03) !important;
      border: 1px solid rgba(255,255,255,.05) !important;
      color: rgba(255,255,255,.6) !important;
      border-radius: 12px !important;
      font-weight: 800 !important;
      font-size: 10px !important;
      text-transform: uppercase !important;
      letter-spacing: 0.1em !important;
      padding: 8px 16px !important;
      box-shadow: none !important;
    }
    .fc .fc-button:hover { background: rgba(255,255,255,.08) !important; color:#fff !important; }
    .fc .fc-button-primary:not(:disabled).fc-button-active {
      background: rgba(234,179,8,.1) !important;
      border-color: rgba(234,179,8,.2) !important;
      color: #eab308 !important;
    }
    .fc .fc-col-header-cell-cushion { color: rgba(255,255,255,.2); font-weight:900; font-size:9px; text-transform: uppercase; letter-spacing: 0.2em; padding: 15px 0 !important; }
    .fc .fc-daygrid-day-number { color: rgba(255,255,255,.3); font-weight:900; font-size:10px; padding:12px; }
    .fc .fc-daygrid-event {
      border-radius: 12px !important;
      padding: 6px 12px !important;
      font-size: 10px !important;
      font-weight: 900 !important;
      letter-spacing: 0.05em !important;
      margin: 4px 6px !important;
      border: none !important;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .fc .fc-daygrid-day-frame { min-height: 110px; }
    .fc-pill{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .fc .fc-daygrid-more-link { font-size: 10px; font-weight: 800; color: #eab308; }
    
    @media (max-width:640px){
      .fc .fc-daygrid-event{ padding:4px 8px !important; font-size:9px !important; margin:2px 4px !important; }
      .fc .fc-daygrid-day-frame{ min-height:80px; }
      .fc .fc-toolbar-title { font-size:14px; }
    }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
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
          const date = info.event.start ? info.event.start.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' }) : '';
          const shiftName = p.shift_name || info.event.title;
          const status = p.status || '-';
          
          let meta = `<div class="flex flex-col gap-1">
             <div class="text-gold-primary font-black uppercase tracking-widest text-[10px] mb-2">${date}</div>
             <div class="text-white font-bold text-sm mb-1">${shiftName}</div>
             <div class="flex items-center gap-2">
                <span class="text-[9px] font-black text-white/20 uppercase">Status:</span>
                <span class="text-[9px] font-black text-white uppercase">${status}</span>
             </div>`;
          
          if (p.check_in_at) meta += `<div class="flex items-center gap-2"><span class="text-[9px] font-black text-white/20 uppercase">In:</span><span class="text-[9px] font-bold text-emerald-400">${p.check_in_at}</span></div>`;
          if (p.check_out_at) meta += `<div class="flex items-center gap-2"><span class="text-[9px] font-black text-white/20 uppercase">Out:</span><span class="text-[9px] font-bold text-blue-400">${p.check_out_at}</span></div>`;
          if (p.status_reason) meta += `<div class="mt-2 text-[9px] text-white/40 italic border-t border-white/5 pt-2">Note: ${p.status_reason}</div>`;
          if (p.is_override) meta += `<div class="mt-1 text-[9px] text-gold-primary/60 font-black italic">! Manual Override Applied</div>`;
          
          meta += `</div>`;
          
          if (hint) {
              hint.innerHTML = meta;
              hint.classList.remove('text-white/40', 'italic', 'text-center');
              hint.classList.add('text-left', 'bg-gold-primary/5', 'border-gold-primary/20');
          }
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
            if (p.is_override) text = `! ${text}`;
          }

          return { html: `<div class="fc-pill truncate">${text}</div>` };
        },
      });

      calendar.render();
    })();
  </script>
@endsection