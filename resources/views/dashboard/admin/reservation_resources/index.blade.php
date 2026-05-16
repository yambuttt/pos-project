@extends('layouts.admin')
@section('title', 'Resource Reservasi')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div class="flex items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gold-gradient">Resource Reservasi</h1>
        <p class="text-sm text-white/40 font-medium italic">Kelola meja, ruangan, dan hall beserta <span class="text-gold-primary font-bold not-italic">kapasitas & harga.</span></p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.reservation_resources.create') }}"
        class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Resource
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-6 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-green-100">{{ session('success') }}</p>
    </div>
  @endif

  <!-- FILTER PANEL -->
  <form method="GET" class="glass-panel p-8 rounded-[2.5rem] mb-10 relative overflow-hidden group">
    <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
    
    <div class="grid grid-cols-1 gap-6 md:grid-cols-12 items-end relative z-10">
      <div class="md:col-span-6 space-y-2">
        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Cari Nama Resource</label>
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Contoh: Meja 01, VIP Room..."
          class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all">
      </div>

      <div class="md:col-span-4 space-y-2">
        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Tipe Resource</label>
        <select name="type" class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23D4AF37%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.1rem_1.1rem] bg-[right:1rem_center] bg-no-repeat">
          <option value="">Semua Tipe</option>
          <option value="TABLE" @selected(($type ?? '')==='TABLE')>TABLE (Meja)</option>
          <option value="ROOM" @selected(($type ?? '')==='ROOM')>ROOM (Ruangan)</option>
          <option value="HALL" @selected(($type ?? '')==='HALL')>HALL (Aula)</option>
        </select>
      </div>

      <div class="md:col-span-2 flex gap-2">
        <button class="flex-1 rounded-xl bg-gold-primary py-3 text-[10px] font-black text-obsidian-950 uppercase tracking-widest shadow-lg shadow-gold-primary/20 hover:scale-[1.02] transition-all active:scale-95">Filter</button>
        <a href="{{ route('admin.reservation_resources.index') }}" class="flex-1 rounded-xl bg-white/5 border border-white/10 py-3 text-[10px] font-black text-white uppercase tracking-widest text-center hover:bg-white/10 transition-all active:scale-95">Reset</a>
      </div>
    </div>
  </form>

  <!-- TABLE SECTION -->
  <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/5">
    <!-- DESKTOP TABLE -->
    <div class="hidden lg:block overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-white/40 font-black border-b border-white/5">
            <th class="px-8 py-6">Nama & Tipe</th>
            <th class="px-6 py-6 text-center">Kapasitas</th>
            <th class="px-6 py-6">Pricing / Harga</th>
            <th class="px-6 py-6 text-center">Durasi & Buffer</th>
            <th class="px-6 py-6 text-center">Status</th>
            <th class="px-8 py-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @forelse($rows as $r)
            <tr class="group hover:bg-white/[0.02] transition-colors">
              <td class="px-8 py-6">
                 <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-gold-primary group-hover:border-gold-primary/30 transition-all">
                       @if($r->type === 'ROOM')
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                          </svg>
                       @elseif($r->type === 'HALL')
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                          </svg>
                       @else
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                          </svg>
                       @endif
                    </div>
                    <div>
                       <div class="text-sm font-bold text-white group-hover:text-gold-primary transition-colors">{{ $r->name }}</div>
                       <div class="text-[9px] text-white/20 uppercase tracking-[0.2em] mt-1">{{ $r->type }}</div>
                    </div>
                 </div>
              </td>
              <td class="px-6 py-6 text-center">
                 <div class="inline-flex flex-col items-center p-2 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-xs font-black text-white italic">{{ $r->capacity }}</span>
                    <span class="text-[8px] text-white/30 uppercase font-black">Pax</span>
                 </div>
              </td>
              <td class="px-6 py-6">
                 <div class="space-y-1">
                    @if($r->hourly_rate)
                       <div class="text-xs text-white/60 font-medium italic"><span class="text-gold-primary">Rp {{ number_format($r->hourly_rate, 0, ',', '.') }}</span> / Jam</div>
                    @endif
                    @if($r->flat_rate)
                       <div class="text-xs text-white/60 font-medium italic"><span class="text-blue-400">Rp {{ number_format($r->flat_rate, 0, ',', '.') }}</span> (Flat)</div>
                    @endif
                    @if(!$r->hourly_rate && !$r->flat_rate)
                       <div class="text-xs text-white/20 italic">Gratis / Belum diatur</div>
                    @endif
                 </div>
              </td>
              <td class="px-6 py-6 text-center">
                 <div class="text-xs font-bold text-white/60">{{ $r->min_duration_minutes }}m <span class="text-white/20 font-medium italic">min</span></div>
                 <div class="text-[10px] text-white/30 italic mt-0.5">+{{ $r->buffer_minutes }}m buffer</div>
              </td>
              <td class="px-6 py-6 text-center">
                 @if($r->is_active)
                    <span class="px-3 py-1 rounded-lg bg-green-500/10 border border-green-500/20 text-[9px] font-black text-green-500 uppercase tracking-widest">Active</span>
                 @else
                    <span class="px-3 py-1 rounded-lg bg-red-500/10 border border-red-500/20 text-[9px] font-black text-red-500 uppercase tracking-widest">Inactive</span>
                 @endif
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex items-center justify-end gap-2">
                   <a href="{{ route('admin.reservation_resources.edit', $r) }}"
                      class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:bg-gold-primary hover:text-obsidian-950 hover:border-gold-primary transition-all">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z" />
                      </svg>
                   </a>
                   <form method="POST" action="{{ route('admin.reservation_resources.destroy', $r) }}" class="inline"
                     onsubmit="return confirm('Hapus resource ini?')">
                     @csrf
                     @method('DELETE')
                     <button class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                     </button>
                   </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-8 py-24 text-center">
                 <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center text-white/10">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                       </svg>
                    </div>
                    <p class="text-sm text-white/30 font-medium italic">Belum ada resource yang ditambahkan.</p>
                 </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- MOBILE LIST -->
    <div class="lg:hidden divide-y divide-white/5">
       @forelse($rows as $r)
         <div class="p-6 premium-card border-none rounded-none bg-transparent space-y-4">
            <div class="flex items-start justify-between gap-4">
               <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-gold-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                  </div>
                  <div>
                    <h4 class="text-sm font-bold text-white">{{ $r->name }}</h4>
                    <p class="text-[10px] text-white/20 uppercase tracking-widest mt-1">{{ $r->type }} • {{ $r->capacity }} Pax</p>
                  </div>
               </div>
               <div>
                  @if($r->is_active)
                    <span class="px-2 py-0.5 rounded-md bg-green-500/10 text-green-500 text-[8px] font-black uppercase tracking-widest border border-green-500/20">Active</span>
                  @else
                    <span class="px-2 py-0.5 rounded-md bg-red-500/10 text-red-500 text-[8px] font-black uppercase tracking-widest border border-red-500/20">Off</span>
                  @endif
               </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 p-4 rounded-2xl bg-white/[0.02] border border-white/5">
               <div>
                  <p class="text-[8px] uppercase tracking-widest text-white/20 font-black mb-1">Pricing</p>
                  <p class="text-[10px] font-bold text-white italic">Rp {{ number_format($r->hourly_rate ?? $r->flat_rate ?? 0, 0, ',', '.') }}</p>
               </div>
               <div class="text-right">
                  <p class="text-[8px] uppercase tracking-widest text-white/20 font-black mb-1">Min Durasi</p>
                  <p class="text-[10px] font-bold text-white/60 italic">{{ $r->min_duration_minutes }} Menit</p>
               </div>
            </div>

            <div class="flex gap-2">
               <a href="{{ route('admin.reservation_resources.edit', $r) }}"
                  class="flex-1 py-3 rounded-xl bg-white/5 border border-white/10 text-center text-[10px] font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all">
                  Edit
               </a>
               <form method="POST" action="{{ route('admin.reservation_resources.destroy', $r) }}" class="flex-1" onsubmit="return confirm('Hapus?')">
                  @csrf @method('DELETE')
                  <button class="w-full py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-center text-[10px] font-black text-red-500 uppercase tracking-widest">
                     Hapus
                  </button>
               </form>
            </div>
         </div>
       @empty
         <div class="p-12 text-center text-white/20 italic text-xs font-medium">Data kosong.</div>
       @endforelse
    </div>
  </div>

  <div class="mt-8">
    {{ $rows->links() }}
  </div>
@endsection