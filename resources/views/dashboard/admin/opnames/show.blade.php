@extends('layouts.admin')
@section('title', 'Detail Stock Opname')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <div class="flex items-center gap-3 mb-1">
        <h1 class="text-3xl font-bold text-gold-gradient">Review Stock Opname</h1>
        <span class="px-3 py-1 rounded-full {{ $opname->status === 'posted' ? 'bg-green-500/10 border-green-500/20 text-green-400' : 'bg-gold-primary/10 border-gold-primary/20 text-gold-primary' }} border text-[10px] font-black uppercase tracking-widest">
          {{ $opname->status }}
        </span>
      </div>
      <p class="text-sm text-white/40 font-medium italic">Dokumen: <span class="text-white/60 font-bold not-italic">#{{ $opname->id }}</span> • Tanggal: <span class="text-white/60 font-bold not-italic">{{ \Carbon\Carbon::parse($opname->opname_date)->format('d M Y') }}</span></p>
    </div>

    <div class="flex items-center gap-3">
       <a href="{{ route('admin.opnames.index') }}"
         class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-xs font-black text-white border border-white/10 hover:bg-white/10 transition-all active:scale-95 uppercase tracking-widest">
         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
         </svg>
         Kembali
       </a>

       @if($opname->status === 'draft')
         <form method="POST" action="{{ route('admin.opnames.post', $opname->id) }}">
           @csrf
           <button class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-8 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
             </svg>
             Finalisasi & Posting
           </button>
         </form>
       @endif
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

  <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_380px]">
    <!-- ITEMS LIST -->
    <div class="space-y-6">
      <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/5">
        <!-- DESKTOP TABLE -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-white/40 font-black border-b border-white/5">
                <th class="px-8 py-6">Nama Bahan</th>
                <th class="px-6 py-6">Unit</th>
                <th class="px-6 py-6">Sistem</th>
                <th class="px-6 py-6">Fisik</th>
                <th class="px-6 py-6">Selisih</th>
                <th class="px-8 py-6 text-right">Catatan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              @foreach($opname->items as $it)
                @php $diff = (float)$it->difference; @endphp
                <tr class="group hover:bg-white/[0.01] transition-colors">
                  <td class="px-8 py-6">
                    <div class="text-sm font-bold text-white">{{ $it->rawMaterial?->name }}</div>
                  </td>
                  <td class="px-6 py-6">
                    <span class="text-[10px] text-white/40 font-black uppercase tracking-widest">{{ $it->rawMaterial?->unit }}</span>
                  </td>
                  <td class="px-6 py-6 text-sm font-medium text-white/60 italic">
                    {{ $it->system_qty }}
                  </td>
                  <td class="px-6 py-6 text-sm font-black text-white">
                    {{ $it->physical_qty }}
                  </td>
                  <td class="px-6 py-6">
                    <div class="flex items-center gap-2">
                       @if($diff > 0)
                         <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                         <span class="text-sm font-black text-emerald-400">+{{ $it->difference }}</span>
                       @elseif($diff < 0)
                         <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                         <span class="text-sm font-black text-red-400">{{ $it->difference }}</span>
                       @else
                         <span class="text-sm font-black text-white/20">0</span>
                       @endif
                    </div>
                  </td>
                  <td class="px-8 py-6 text-right">
                    <p class="text-[11px] text-white/30 italic font-medium max-w-[150px] ml-auto">
                       {{ $it->note ?? '-' }}
                    </p>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- MOBILE LIST -->
        <div class="md:hidden divide-y divide-white/5">
           @foreach($opname->items as $it)
             @php $diff = (float)$it->difference; @endphp
             <div class="p-6 space-y-4">
                <div class="flex items-start justify-between gap-4">
                   <div>
                      <h4 class="text-sm font-bold text-white">{{ $it->rawMaterial?->name }}</h4>
                      <p class="text-[10px] text-white/30 font-black uppercase tracking-widest">{{ $it->rawMaterial?->unit }}</p>
                   </div>
                   <div class="text-right">
                      @if($diff > 0)
                        <p class="text-sm font-black text-emerald-400">+{{ $it->difference }}</p>
                      @elseif($diff < 0)
                        <p class="text-sm font-black text-red-400">{{ $it->difference }}</p>
                      @else
                        <p class="text-sm font-black text-white/20">0</p>
                      @endif
                      <p class="text-[9px] text-white/20 uppercase tracking-tighter mt-1">Selisih</p>
                   </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 py-3 border-y border-white/5">
                   <div class="flex flex-col gap-1">
                      <span class="text-[8px] uppercase tracking-widest text-white/30 font-black">Sistem</span>
                      <span class="text-xs font-bold text-white/60 italic">{{ $it->system_qty }}</span>
                   </div>
                   <div class="flex flex-col gap-1 text-right">
                      <span class="text-[8px] uppercase tracking-widest text-white/30 font-black">Fisik</span>
                      <span class="text-xs font-black text-white">{{ $it->physical_qty }}</span>
                   </div>
                </div>

                @if($it->note)
                   <p class="text-[10px] text-white/30 italic font-medium">"{{ $it->note }}"</p>
                @endif
             </div>
           @endforeach
        </div>
      </div>

      <!-- NOTES -->
      @if($opname->note)
        <div class="glass-panel p-8 rounded-[2.5rem] border-white/5">
           <h4 class="text-[10px] font-black text-gold-primary uppercase tracking-[0.2em] mb-4">Catatan Dokumen</h4>
           <p class="text-sm text-white/60 leading-relaxed italic">
              "{{ $opname->note }}"
           </p>
        </div>
      @endif
    </div>

    <!-- SUMMARY SIDEBAR -->
    <div class="space-y-8">
       <!-- CREATOR INFO -->
       <div class="premium-card p-8 border-white/5 space-y-6">
          <h4 class="text-xs font-black text-gold-primary uppercase tracking-[0.2em]">Data Personel</h4>
          
          <div class="space-y-6">
             <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-white/40">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                   </svg>
                </div>
                <div class="overflow-hidden">
                   <p class="text-[9px] uppercase tracking-widest text-white/30 font-black mb-1">Dibuat Oleh</p>
                   <p class="text-sm font-bold text-white truncate">{{ $opname->creator?->name ?? '-' }}</p>
                   <p class="text-[9px] text-white/20 italic">{{ $opname->created_at?->format('d M Y, H:i') }}</p>
                </div>
             </div>

             <div class="flex items-start gap-4 pt-6 border-t border-white/5">
                <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-white/40">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                   </svg>
                </div>
                <div class="overflow-hidden">
                   <p class="text-[9px] uppercase tracking-widest text-white/30 font-black mb-1">Diposting Oleh</p>
                   @if($opname->posted_at)
                      <p class="text-sm font-bold text-white truncate">{{ $opname->poster?->name ?? '-' }}</p>
                      <p class="text-[9px] text-white/20 italic">{{ \Carbon\Carbon::parse($opname->posted_at)->format('d M Y, H:i') }}</p>
                   @else
                      <p class="text-sm font-bold text-white/20 italic">Belum Diposting</p>
                   @endif
                </div>
             </div>
          </div>
       </div>

       <!-- SYSTEM INFO -->
       <div class="p-6 rounded-3xl border border-white/5 bg-white/5 space-y-4">
          <h4 class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">Informasi Dokumen</h4>
          <div class="space-y-3">
             <div class="flex items-start gap-3">
                <div class="mt-1 w-1.5 h-1.5 rounded-full bg-gold-primary shrink-0"></div>
                <p class="text-[10px] text-white/40 font-medium leading-relaxed italic">Stok sistem hanya akan berubah setelah dokumen ini mendapatkan status <span class="text-white font-bold not-italic">POSTED.</span></p>
             </div>
             <div class="flex items-start gap-3">
                <div class="mt-1 w-1.5 h-1.5 rounded-full bg-gold-primary shrink-0"></div>
                <p class="text-[10px] text-white/40 font-medium leading-relaxed italic">Riwayat pergerakan stok otomatis tercatat untuk audit mendatang.</p>
             </div>
          </div>
       </div>
    </div>
  </div>
@endsection
