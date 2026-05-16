@extends('layouts.admin')
@section('title', 'Manajemen Reservasi')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Reservasi</h1>
        <p class="text-sm text-white/40 font-medium italic">Kelola jadwal kunjungan, <span class="text-gold-primary font-bold not-italic">DP, dan ketersediaan resource.</span></p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.reservations.create') }}"
        class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Buat Reservasi Baru
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

  @if($errors->any())
    <div class="mb-6 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-red-100">{{ $errors->first() }}</p>
    </div>
  @endif

  <!-- FILTER PANEL -->
  <form method="GET" class="glass-panel p-8 rounded-[2.5rem] mb-10 relative overflow-hidden group">
    <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
    
    <div class="flex items-center gap-3 mb-6 relative z-10">
       <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
       </div>
       <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Filter Reservasi</h3>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-12 items-end relative z-10">
      <div class="md:col-span-6 space-y-2">
        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Cari Data</label>
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari kode, nama customer, atau nomor HP..."
          class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all">
      </div>

      <div class="md:col-span-4 space-y-2">
        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Status Reservasi</label>
        <select name="status" class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23D4AF37%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.1rem_1.1rem] bg-[right:1rem_center] bg-no-repeat">
          <option value="">Semua Status</option>
          @foreach (['draft','pending_dp','confirmed','checked_in','completed','cancelled','no_show'] as $st)
            <option value="{{ $st }}" @selected(($status ?? '')===$st)>{{ strtoupper($st) }}</option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-2 flex gap-2">
        <button class="flex-1 rounded-xl bg-gold-primary py-3 text-[10px] font-black text-obsidian-950 uppercase tracking-widest shadow-lg shadow-gold-primary/20 hover:scale-[1.02] transition-all active:scale-95">Filter</button>
        <a href="{{ route('admin.reservations.index') }}" class="flex-1 rounded-xl bg-white/5 border border-white/10 py-3 text-[10px] font-black text-white uppercase tracking-widest text-center hover:bg-white/10 transition-all active:scale-95">Reset</a>
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
            <th class="px-8 py-6">Customer & Kode</th>
            <th class="px-6 py-6">Resource / Tempat</th>
            <th class="px-6 py-6">Jadwal Reservasi</th>
            <th class="px-6 py-6 text-right">Total Tagihan</th>
            <th class="px-6 py-6 text-center">Status</th>
            <th class="px-8 py-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @forelse($rows as $r)
            <tr class="group hover:bg-white/[0.02] transition-colors">
              <td class="px-8 py-6">
                <div class="text-xs font-black text-gold-primary uppercase tracking-tighter mb-1">{{ $r->code }}</div>
                <div class="text-sm font-bold text-white">{{ $r->customer_name }}</div>
                <div class="text-[10px] text-white/30 font-medium">{{ $r->customer_phone }}</div>
              </td>
              <td class="px-6 py-6">
                 <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gold-primary group-hover:border-gold-primary/30 transition-all">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                       </svg>
                    </div>
                    <div>
                       <div class="text-xs font-bold text-white/90">{{ $r->resource?->name ?? '-' }}</div>
                       <div class="text-[9px] text-white/20 uppercase tracking-widest">{{ $r->resource?->type ?? 'Resource' }}</div>
                    </div>
                 </div>
              </td>
              <td class="px-6 py-6">
                <div class="text-xs font-bold text-white/80 italic">{{ $r->start_at->format('d M Y') }}</div>
                <div class="text-[10px] text-gold-primary font-black uppercase tracking-tighter mt-0.5">{{ $r->start_at->format('H:i') }} - {{ $r->end_at->format('H:i') }}</div>
              </td>
              <td class="px-6 py-6 text-right">
                 <div class="text-sm font-black text-white italic">Rp {{ number_format($r->grand_total, 0, ',', '.') }}</div>
                 <div class="text-[9px] text-white/20 font-medium uppercase tracking-tighter mt-1">DP: Rp {{ number_format($r->dp_amount, 0, ',', '.') }}</div>
              </td>
              <td class="px-6 py-6 text-center">
                 @php
                    $statusColors = [
                       'draft' => 'text-white/40 bg-white/5 border-white/10',
                       'pending_dp' => 'text-yellow-500 bg-yellow-500/10 border-yellow-500/20',
                       'confirmed' => 'text-blue-500 bg-blue-500/10 border-blue-500/20',
                       'checked_in' => 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20',
                       'completed' => 'text-green-500 bg-green-500/10 border-green-500/20',
                       'cancelled' => 'text-red-500 bg-red-500/10 border-red-500/20',
                       'no_show' => 'text-orange-500 bg-orange-500/10 border-orange-500/20',
                    ];
                    $color = $statusColors[$r->status] ?? 'text-white bg-white/10 border-white/20';
                 @endphp
                 <span class="px-3 py-1 rounded-lg border text-[9px] font-black uppercase tracking-widest {{ $color }}">
                    {{ $r->status }}
                 </span>
              </td>
              <td class="px-8 py-6 text-right">
                <a href="{{ route('admin.reservations.show', $r) }}"
                   class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:bg-gold-primary hover:text-obsidian-950 hover:border-gold-primary transition-all">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-8 py-24 text-center">
                 <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center text-white/10">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                       </svg>
                    </div>
                    <p class="text-sm text-white/30 font-medium italic">Belum ada data reservasi ditemukan.</p>
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
               <div>
                  <div class="text-[9px] font-black text-gold-primary uppercase tracking-widest mb-1">{{ $r->code }}</div>
                  <h4 class="text-sm font-bold text-white">{{ $r->customer_name }}</h4>
                  <p class="text-[10px] text-white/30 font-medium italic">{{ $r->customer_phone }}</p>
               </div>
               <div class="text-right">
                  <p class="text-sm font-black text-white">Rp {{ number_format($r->grand_total, 0, ',', '.') }}</p>
                  <p class="text-[9px] text-gold-primary font-black uppercase tracking-tighter mt-1">{{ $r->status }}</p>
               </div>
            </div>
            
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 grid grid-cols-2 gap-4">
               <div>
                  <p class="text-[8px] uppercase tracking-widest text-white/20 font-black mb-1">Resource</p>
                  <p class="text-[10px] font-bold text-white/80">{{ $r->resource?->name ?? '-' }}</p>
               </div>
               <div class="text-right">
                  <p class="text-[8px] uppercase tracking-widest text-white/20 font-black mb-1">Waktu</p>
                  <p class="text-[10px] font-bold text-white/80">{{ $r->start_at->format('d M, H:i') }}</p>
               </div>
            </div>

            <a href="{{ route('admin.reservations.show', $r) }}"
               class="block w-full py-3 rounded-xl bg-white/5 border border-white/10 text-center text-[10px] font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
               Detail Reservasi
            </a>
         </div>
       @empty
         <div class="p-12 text-center text-white/20 italic text-xs font-medium">Data kosong.</div>
       @endforelse
    </div>
  </div>

  <div class="mt-8">
    {{ $rows->onEachSide(1)->links() }}
  </div>
@endsection