@extends('layouts.admin')
@section('title', 'Waste / Bahan Rusak')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Waste Management</h1>
      <p class="text-sm text-white/40 font-medium italic">Pencatatan bahan baku keluar karena <span class="text-red-400 font-bold not-italic">basi, tumpah, atau expired.</span></p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.wastes.create') }}"
        class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Input Data Waste
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

  <!-- QUICK STATS -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    <div class="premium-card p-6 border-red-500/10 bg-red-500/5 relative overflow-hidden group">
       <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
       </div>
       <p class="text-[10px] uppercase tracking-widest text-red-400/60 font-black mb-1">Total Kejadian Waste</p>
       <h3 class="text-3xl font-black text-white leading-tight">{{ $wastes->total() }}</h3>
       <p class="text-[10px] text-white/20 mt-1 font-medium italic">Data akumulasi seluruh periode</p>
    </div>

    <div class="premium-card p-6 border-white/5 relative overflow-hidden group">
       <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
       </div>
       <p class="text-[10px] uppercase tracking-widest text-white/40 font-black mb-1">Estimasi Kerugian (Bahan Baku)</p>
       <h3 class="text-3xl font-black text-white leading-tight">Rp {{ number_format($wastes->sum('total_estimated_cost'), 0, ',', '.') }}</h3>
       <p class="text-[10px] text-white/20 mt-1 font-medium italic">Berdasarkan harga default per unit</p>
    </div>
  </div>

  <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/5">
    <!-- DESKTOP TABLE -->
    <div class="hidden lg:block overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-white/40 font-black border-b border-white/5">
            <th class="px-8 py-6">Tanggal</th>
            <th class="px-6 py-6">Alasan (Reason)</th>
            <th class="px-6 py-6">Bahan Terbuang</th>
            <th class="px-6 py-6">Estimasi Kerugian</th>
            <th class="px-6 py-6">Admin / Input Oleh</th>
            <th class="px-8 py-6 text-right">Catatan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @forelse($wastes as $w)
            <tr class="group hover:bg-white/[0.02] transition-colors">
              <td class="px-8 py-6">
                <div class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($w->waste_date)->format('d M Y') }}</div>
                <div class="text-[10px] text-white/30 font-bold uppercase tracking-tight italic">Batch #{{ $w->id }}</div>
              </td>
              <td class="px-6 py-6">
                <span class="px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-[10px] font-black text-red-400 uppercase tracking-widest">
                   {{ $w->reason ?? 'Tidak Ada Alasan' }}
                </span>
              </td>
              <td class="px-6 py-6">
                 <div class="flex flex-col gap-1.5">
                    @foreach($w->items as $item)
                       <div class="flex items-center gap-2 group/item">
                          <div class="w-1.5 h-1.5 rounded-full bg-gold-primary/40 group-hover/item:bg-gold-primary transition-colors"></div>
                          <span class="text-xs font-bold text-white/90">{{ $item->rawMaterial?->name ?? 'Unknown' }}</span>
                          <span class="text-[10px] font-black text-white/30 uppercase tracking-tighter">{{ (float)$item->qty }} {{ $item->rawMaterial?->unit }}</span>
                       </div>
                    @endforeach
                 </div>
              </td>
              <td class="px-6 py-6 text-sm font-black text-white italic">
                Rp {{ number_format($w->total_estimated_cost, 0, ',', '.') }}
              </td>
              <td class="px-6 py-6">
                 <div class="text-sm font-bold text-white/80">{{ $w->creator?->name ?? '-' }}</div>
                 <div class="text-[10px] text-white/30 truncate max-w-[150px]">{{ $w->creator?->email }}</div>
              </td>
              <td class="px-8 py-6 text-right">
                <p class="text-[11px] text-white/40 italic font-medium max-w-[200px] ml-auto leading-relaxed">
                   "{{ $w->note ?? '-' }}"
                </p>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-8 py-20 text-center">
                 <div class="flex flex-col items-center gap-3">
                   <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white/20">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                     </svg>
                   </div>
                   <p class="text-sm text-white/30 font-medium italic">Belum ada data barang rusak (waste).</p>
                 </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- MOBILE LIST -->
    <div class="lg:hidden divide-y divide-white/5">
       @forelse($wastes as $w)
         <div class="p-6 space-y-4 premium-card border-none rounded-none bg-transparent">
            <div class="flex items-start justify-between gap-4">
               <div>
                  <h4 class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($w->waste_date)->format('d M Y') }}</h4>
                  <p class="text-[10px] text-red-400 font-bold uppercase tracking-[0.2em] mt-0.5">{{ $w->reason ?? '-' }}</p>
               </div>
               <div class="text-right">
                  <p class="text-sm font-black text-white italic">Rp {{ number_format($w->total_estimated_cost, 0, ',', '.') }}</p>
                  <p class="text-[9px] text-white/20 uppercase tracking-tighter mt-1">Estimasi Rugi</p>
               </div>
            </div>
            
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 space-y-3">
               <div class="space-y-2">
                  <p class="text-[8px] uppercase tracking-widest text-white/20 font-black">Detail Bahan:</p>
                  <div class="flex flex-wrap gap-2">
                     @foreach($w->items as $item)
                        <div class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/5 flex items-center gap-2">
                           <span class="text-[10px] font-bold text-white/80">{{ $item->rawMaterial?->name }}</span>
                           <span class="text-[9px] font-black text-gold-primary">{{ (float)$item->qty }} {{ $item->rawMaterial?->unit }}</span>
                        </div>
                     @endforeach
                  </div>
               </div>
               <div class="pt-3 border-t border-white/5">
                  <p class="text-[10px] text-white/30 font-medium leading-relaxed italic">"{{ $w->note ?? 'Tidak ada catatan.' }}"</p>
               </div>
               <div class="pt-2 border-t border-white/5 flex items-center justify-between">
                  <span class="text-[9px] text-white/40 font-black uppercase tracking-widest">Input By:</span>
                  <span class="text-[9px] text-gold-primary font-bold">{{ $w->creator?->name ?? '-' }}</span>
               </div>
            </div>
         </div>
       @empty
         <div class="p-10 text-center text-white/30 italic text-xs">Belum ada data.</div>
       @endforelse
    </div>
  </div>

  <div class="mt-8">
    {{ $wastes->onEachSide(1)->links() }}
  </div>
@endsection
