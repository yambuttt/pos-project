@extends('layouts.admin')
@section('title', 'Manajemen Shift Pegawai')

@section('body')
  <!-- HEADER & SEARCH -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div class="flex items-center gap-4">
      <button id="openMobileSidebar" type="button"
        class="inline-flex lg:hidden items-center justify-center w-10 h-10 rounded-xl border border-white/10 bg-white/5 text-white/70 hover:bg-white/10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
      <div>
        <h1 class="text-3xl font-bold text-gold-gradient">Shift Pegawai</h1>
        <p class="text-sm text-white/40 font-medium italic">Konfigurasi skema <span class="text-gold-primary font-bold not-italic">penjadwalan & alokasi waktu.</span></p>
      </div>
    </div>

    <form method="GET" action="{{ route('admin.shifts.index') }}" class="relative group">
       <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-white/20 group-focus-within:text-gold-primary transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
       </div>
       <input name="q" value="{{ $q }}" placeholder="Cari personil..."
         class="w-full lg:w-[300px] pl-11 pr-4 py-3.5 rounded-2xl bg-white/5 border border-white/10 text-xs font-bold text-white placeholder:text-white/20 outline-none focus:border-gold-primary/30 transition-all">
    </form>
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

  @if (session('error'))
    <div class="mb-8 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-red-100">{{ session('error') }}</p>
    </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
    @forelse($employees as $u)
        <a href="{{ route('admin.shifts.edit', $u) }}" class="glass-panel p-8 rounded-[2.5rem] relative overflow-hidden group transition-all hover:-translate-y-1 hover:border-gold-primary/30">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full transition-all group-hover:scale-150"></div>
            
            <div class="relative z-10 space-y-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-xl font-black text-white italic group-hover:bg-gold-primary group-hover:text-obsidian-950 transition-all duration-500">
                           {{ strtoupper(substr($u->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                           <h3 class="text-lg font-bold text-white group-hover:text-gold-primary transition-colors truncate max-w-[150px]">{{ $u->name }}</h3>
                           <div class="text-[9px] text-white/30 italic uppercase tracking-tighter truncate max-w-[150px]">{{ $u->email }}</div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-xl text-[8px] font-black uppercase tracking-widest border border-white/10 bg-white/5 text-white/40 group-hover:bg-gold-primary/10 group-hover:border-gold-primary/20 group-hover:text-gold-primary transition-all">
                       {{ $u->shift_scheme ?: 'fixed' }}
                    </span>
                </div>

                <div class="p-6 rounded-3xl bg-black/40 border border-white/5 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                           </svg>
                        </div>
                        <div>
                           <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-0.5">Primary Allocation</div>
                           <div class="text-[11px] font-bold text-white italic">
                              @php $ds = $shifts->firstWhere('id', $u->default_shift_id); @endphp
                              {{ $ds?->name ?: 'Standard Shift' }}
                           </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                   <div class="flex -space-x-2">
                      <div class="w-6 h-6 rounded-full border-2 border-obsidian-900 bg-white/5 flex items-center justify-center text-[8px] font-black text-white/20 uppercase">M</div>
                      <div class="w-6 h-6 rounded-full border-2 border-obsidian-900 bg-white/5 flex items-center justify-center text-[8px] font-black text-white/20 uppercase">T</div>
                      <div class="w-6 h-6 rounded-full border-2 border-obsidian-900 bg-white/5 flex items-center justify-center text-[8px] font-black text-white/20 uppercase">W</div>
                      <div class="w-6 h-6 rounded-full border-2 border-obsidian-900 bg-white/5 flex items-center justify-center text-[8px] font-black text-white/20 uppercase">T</div>
                      <div class="w-6 h-6 rounded-full border-2 border-obsidian-900 bg-white/5 flex items-center justify-center text-[8px] font-black text-white/20 uppercase text-gold-primary border-gold-primary/30 bg-gold-primary/10">F</div>
                   </div>
                   <div class="flex items-center gap-2 text-[9px] font-black text-white/20 uppercase tracking-widest group-hover:text-gold-primary transition-colors">
                      Configure
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                   </div>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full glass-panel p-20 rounded-[3rem] text-center border-dashed border-white/10">
           <div class="w-20 h-20 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-6 text-white/10">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
           </div>
           <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">No Staff Found</h3>
           <p class="text-xs text-white/30 italic mt-2">Gunakan kriteria pencarian lain atau tambahkan pegawai baru.</p>
        </div>
    @endforelse
  </div>

  <div class="mt-10">
      {{ $employees->links() }}
  </div>
@endsection