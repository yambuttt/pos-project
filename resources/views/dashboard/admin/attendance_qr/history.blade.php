@extends('layouts.admin')
@section('title', 'History Absensi')

@section('body')
  <!-- HEADER & FILTERS -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div class="flex items-center gap-4">
      <button id="openMobileSidebar" type="button"
        class="inline-flex lg:hidden items-center justify-center w-10 h-10 rounded-xl border border-white/10 bg-white/5 text-white/70 hover:bg-white/10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
      <div>
        <h1 class="text-3xl font-bold text-gold-gradient">History Absensi</h1>
        <p class="text-sm text-white/40 font-medium italic">Rekapitulasi kehadiran personil tanggal <span class="text-gold-primary font-bold not-italic">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span></p>
      </div>
    </div>

    <form method="GET" action="{{ route('admin.attendance.history') }}" class="flex flex-wrap items-center gap-3">
       <div class="relative group">
          <input type="date" name="date" value="{{ $date }}"
            class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all">
       </div>

       <div class="relative group">
          <select name="user_id" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-xs font-bold text-white outline-none focus:border-gold-primary/30 transition-all appearance-none pr-10 min-w-[180px]">
             <option value="" class="bg-obsidian-900">Semua Pegawai</option>
             @foreach($employees as $e)
                 <option value="{{ $e->id }}" @selected((string) $selectedUserId === (string) $e->id) class="bg-obsidian-900">
                     {{ $e->name }}
                 </option>
             @endforeach
          </select>
          <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-white/20">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
             </svg>
          </div>
       </div>

       <button class="flex items-center gap-2 rounded-2xl bg-gold-primary px-6 py-3 text-[10px] font-black text-obsidian-950 uppercase tracking-widest hover:bg-gold-light transition-all active:scale-95 shadow-lg shadow-gold-primary/10">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Tampilkan
       </button>
    </form>
  </div>

  <!-- ANALYTICS CARDS -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
     <div class="glass-panel p-6 rounded-[2rem] border-emerald-500/10 flex items-center gap-5 relative overflow-hidden">
        <div class="absolute -top-4 -right-4 w-20 h-20 bg-emerald-500/5 blur-2xl rounded-full"></div>
        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
           </svg>
        </div>
        <div>
           <div class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-1">Check-In Total</div>
           <div class="text-3xl font-black text-white italic">{{ count($checkins) }} <span class="text-xs font-medium text-white/20 not-italic uppercase tracking-tighter">Personil</span></div>
        </div>
     </div>

     <div class="glass-panel p-6 rounded-[2rem] border-blue-500/10 flex items-center gap-5 relative overflow-hidden">
        <div class="absolute -top-4 -right-4 w-20 h-20 bg-blue-500/5 blur-2xl rounded-full"></div>
        <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
           </svg>
        </div>
        <div>
           <div class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-1">Check-Out Total</div>
           <div class="text-3xl font-black text-white italic">{{ count($checkouts) }} <span class="text-xs font-medium text-white/20 not-italic uppercase tracking-tighter">Personil</span></div>
        </div>
     </div>

     <div class="glass-panel p-6 rounded-[2rem] border-gold-primary/10 flex items-center gap-5 relative overflow-hidden">
        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gold-primary/5 blur-2xl rounded-full"></div>
        <div class="w-14 h-14 rounded-2xl bg-gold-primary/10 border border-gold-primary/20 flex items-center justify-center text-gold-primary">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
           </svg>
        </div>
        <div>
           <div class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-1">Avg. Work Duration</div>
           <div class="text-3xl font-black text-white italic">08:00 <span class="text-xs font-medium text-white/20 not-italic uppercase tracking-tighter">Hours</span></div>
        </div>
     </div>
  </div>

  <div class="grid grid-cols-1 gap-10 xl:grid-cols-2">
    <!-- TABLE CHECK-IN -->
    <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/5 blur-3xl rounded-full"></div>
        <div class="relative z-10 space-y-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                      </svg>
                   </div>
                   <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Log Check-In</h3>
                </div>
                <div class="px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-black text-emerald-400 uppercase tracking-widest">
                   {{ count($checkins) }} Entry
                </div>
            </div>

            <div class="overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-white/5">
                            <th class="pb-4 text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">Pegawai</th>
                            <th class="pb-4 text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">Waktu</th>
                            <th class="pb-4 text-[10px] font-black text-white/20 uppercase tracking-[0.2em] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($checkins as $a)
                            <tr class="group">
                                <td class="py-5">
                                    <div class="flex items-center gap-3">
                                       <div class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-xs font-black text-white italic">
                                          {{ strtoupper(substr($a->user->name ?? '?', 0, 1)) }}
                                       </div>
                                       <div>
                                          <div class="text-sm font-bold text-white group-hover:text-gold-primary transition-colors">{{ $a->user->name ?? 'Unknown' }}</div>
                                          <div class="text-[9px] text-white/30 italic uppercase tracking-tighter">{{ $a->device ?: 'Mobile Device' }}</div>
                                       </div>
                                    </div>
                                </td>
                                <td class="py-5">
                                    <div class="text-sm font-black text-white italic">{{ optional($a->check_in_at)->format('H:i:s') }}</div>
                                    <div class="text-[9px] text-white/20 uppercase font-medium">Recorded Time</div>
                                </td>
                                <td class="py-5">
                                    <div class="flex items-center justify-center gap-2">
                                       @if($a->check_in_lat && $a->check_in_lng)
                                          <button onclick="openMap({{ $a->check_in_lat }}, {{ $a->check_in_lng }})" class="p-2 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:text-gold-primary hover:border-gold-primary/30 transition-all active:scale-90">
                                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                             </svg>
                                          </button>
                                       @endif
                                       @if($a->check_in_photo_path)
                                          <button onclick="openPhoto('{{ route('admin.attendance.photo', ['attendance' => $a->id, 'type' => 'in']) }}')" class="p-2 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:text-emerald-400 hover:border-emerald-400/30 transition-all active:scale-90">
                                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                             </svg>
                                          </button>
                                       @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-10 text-center">
                                    <div class="text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">Belum ada data check-in</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TABLE CHECK-OUT -->
    <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/5 blur-3xl rounded-full"></div>
        <div class="relative z-10 space-y-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                      </svg>
                   </div>
                   <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Log Check-Out</h3>
                </div>
                <div class="px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-[10px] font-black text-blue-400 uppercase tracking-widest">
                   {{ count($checkouts) }} Entry
                </div>
            </div>

            <div class="overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-white/5">
                            <th class="pb-4 text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">Pegawai</th>
                            <th class="pb-4 text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">Waktu & Durasi</th>
                            <th class="pb-4 text-[10px] font-black text-white/20 uppercase tracking-[0.2em] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($checkouts as $a)
                            <tr class="group">
                                <td class="py-5">
                                    <div class="flex items-center gap-3">
                                       <div class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-xs font-black text-white italic">
                                          {{ strtoupper(substr($a->user->name ?? '?', 0, 1)) }}
                                       </div>
                                       <div>
                                          <div class="text-sm font-bold text-white group-hover:text-gold-primary transition-colors">{{ $a->user->name ?? 'Unknown' }}</div>
                                          <div class="text-[9px] text-white/30 italic uppercase tracking-tighter">{{ $a->device ?: 'Mobile Device' }}</div>
                                       </div>
                                    </div>
                                </td>
                                <td class="py-5">
                                    <div class="text-sm font-black text-white italic">{{ optional($a->check_out_at)->format('H:i:s') }}</div>
                                    <div class="flex items-center gap-2 mt-1">
                                       <span class="px-2 py-0.5 rounded bg-blue-500/10 border border-blue-500/20 text-[8px] font-black text-blue-400 uppercase tracking-tighter">
                                          {{ $a->work_duration ?? '--' }} Work
                                       </span>
                                    </div>
                                </td>
                                <td class="py-5">
                                    <div class="flex items-center justify-center gap-2">
                                       @if($a->check_out_lat && $a->check_out_lng)
                                          <button onclick="openMap({{ $a->check_out_lat }}, {{ $a->check_out_lng }})" class="p-2 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:text-gold-primary hover:border-gold-primary/30 transition-all active:scale-90">
                                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                             </svg>
                                          </button>
                                       @endif
                                       @if($a->check_out_photo_path)
                                          <button onclick="openPhoto('{{ route('admin.attendance.photo', ['attendance' => $a->id, 'type' => 'out']) }}')" class="p-2 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:text-blue-400 hover:border-blue-400/30 transition-all active:scale-90">
                                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                             </svg>
                                          </button>
                                       @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-10 text-center">
                                    <div class="text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">Belum ada data check-out</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>

  <!-- MODALS -->
  <div id="photoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-obsidian-950/90 backdrop-blur-md p-4 animate-fade-in">
    <div class="w-full max-w-2xl glass-panel border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="flex items-center justify-between border-b border-white/5 p-6 bg-white/[0.02]">
            <h3 class="text-xs font-black text-white uppercase tracking-[0.3em]">Verification Image</h3>
            <button onclick="closePhoto()" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all active:scale-90">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
               </svg>
            </button>
        </div>
        <div class="p-8">
            <div class="rounded-3xl border-4 border-white/5 overflow-hidden shadow-inner bg-obsidian-900">
               <img id="photoModalImg" src="" alt="Attendance Capture" class="w-full h-auto object-contain max-h-[60vh]">
            </div>
        </div>
    </div>
  </div>

  <div id="mapModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-obsidian-950/90 backdrop-blur-md p-4 animate-fade-in">
    <div class="w-full max-w-4xl glass-panel border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="flex items-center justify-between border-b border-white/5 p-6 bg-white/[0.02]">
            <h3 class="text-xs font-black text-white uppercase tracking-[0.3em]">Geospatial Location</h3>
            <button onclick="closeMap()" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all active:scale-90">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
               </svg>
            </button>
        </div>
        <div class="p-8">
            <div class="rounded-3xl border-4 border-white/5 overflow-hidden shadow-inner bg-obsidian-900 h-[50vh]">
               <iframe id="mapFrame" src="" class="w-full h-full border-0 grayscale invert opacity-80" loading="lazy"></iframe>
            </div>
            <div id="mapCoords" class="mt-4 text-[10px] font-mono text-gold-primary text-center tracking-widest uppercase opacity-40"></div>
        </div>
    </div>
  </div>

  <script>
    const photoModal = document.getElementById('photoModal');
    const photoImg = document.getElementById('photoModalImg');
    const mapModal = document.getElementById('mapModal');
    const mapFrame = document.getElementById('mapFrame');
    const mapCoords = document.getElementById('mapCoords');

    function openPhoto(url) {
        photoImg.src = url;
        photoModal.classList.remove('hidden');
        photoModal.classList.add('flex');
    }
    function closePhoto() {
        photoModal.classList.add('hidden');
        photoModal.classList.remove('flex');
        photoImg.src = '';
    }

    function openMap(lat, lng) {
        const url = `https://www.google.com/maps?q=${lat},${lng}&z=18&output=embed`;
        mapFrame.src = url;
        mapCoords.textContent = `LAT: ${lat} • LNG: ${lng}`;
        mapModal.classList.remove('hidden');
        mapModal.classList.add('flex');
    }
    function closeMap() {
        mapModal.classList.add('hidden');
        mapModal.classList.remove('flex');
        mapFrame.src = '';
    }

    // Modal click-out
    photoModal.addEventListener('click', (e) => { if(e.target === photoModal) closePhoto(); });
    mapModal.addEventListener('click', (e) => { if(e.target === mapModal) closeMap(); });
    
    window.openPhoto = openPhoto;
    window.openMap = openMap;
    window.closePhoto = closePhoto;
    window.closeMap = closeMap;
  </script>
@endsection