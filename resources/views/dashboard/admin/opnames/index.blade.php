@extends('layouts.admin')
@section('title', 'Stock Opname')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Stock Opname</h1>
      <p class="text-sm text-white/40 font-medium italic">Koreksi stok fisik vs stok sistem untuk menjaga <span class="text-gold-primary font-bold not-italic">akurasi inventori.</span></p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.opnames.create') }}"
        class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Mulai Opname Baru
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

  <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/5">
    <!-- DESKTOP TABLE -->
    <div class="hidden lg:block overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-white/40 font-black border-b border-white/5">
            <th class="px-8 py-6">Tanggal Opname</th>
            <th class="px-6 py-6">Status Dokumen</th>
            <th class="px-6 py-6">Dibuat Oleh</th>
            <th class="px-6 py-6">Disetujui (Posted)</th>
            <th class="px-8 py-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @forelse($opnames as $o)
            <tr class="group hover:bg-white/[0.02] transition-colors">
              <td class="px-8 py-6">
                <div class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($o->opname_date)->format('d M Y') }}</div>
                <div class="text-[10px] text-white/30 font-bold uppercase tracking-tight italic">Doc #{{ $o->id }}</div>
              </td>
              <td class="px-6 py-6">
                @if($o->status === 'posted')
                  <span class="px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20 text-[10px] font-black text-green-400 uppercase tracking-widest">POSTED</span>
                @else
                  <span class="px-3 py-1 rounded-full bg-gold-primary/10 border border-gold-primary/20 text-[10px] font-black text-gold-primary uppercase tracking-widest">DRAFT</span>
                @endif
              </td>
              <td class="px-6 py-6">
                 <div class="text-sm font-bold text-white/80">{{ $o->creator?->name ?? '-' }}</div>
                 <div class="text-[10px] text-white/30 truncate max-w-[150px]">{{ $o->creator?->email }}</div>
              </td>
              <td class="px-6 py-6">
                 @if($o->posted_at)
                    <div class="text-sm font-bold text-white/80">{{ $o->poster?->name ?? '-' }}</div>
                    <div class="text-[10px] text-white/30">{{ \Carbon\Carbon::parse($o->posted_at)->format('d M Y H:i') }}</div>
                 @else
                    <span class="text-[10px] text-white/20 italic font-medium">Menunggu Persetujuan</span>
                 @endif
              </td>
              <td class="px-8 py-6 text-right">
                <a href="{{ route('admin.opnames.show', $o->id) }}"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black text-white uppercase tracking-widest hover:bg-gold-primary hover:text-obsidian-950 transition-all">
                  Detail / Koreksi
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-8 py-20 text-center">
                 <div class="flex flex-col items-center gap-3">
                   <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white/20">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                     </svg>
                   </div>
                   <p class="text-sm text-white/30 font-medium italic">Belum ada riwayat stock opname.</p>
                 </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- MOBILE LIST -->
    <div class="lg:hidden divide-y divide-white/5">
       @forelse($opnames as $o)
         <a href="{{ route('admin.opnames.show', $o->id) }}" class="block p-6 space-y-4 hover:bg-white/[0.02] transition-all">
            <div class="flex items-start justify-between gap-4">
               <div>
                  <h4 class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($o->opname_date)->format('d M Y') }}</h4>
                  <p class="text-[10px] text-white/30 font-bold uppercase tracking-[0.2em] mt-0.5">DOC #{{ $o->id }}</p>
               </div>
               <div>
                  @if($o->status === 'posted')
                    <span class="px-2 py-0.5 rounded-md bg-green-500/10 border border-green-500/20 text-[8px] font-black text-green-400 uppercase tracking-widest">POSTED</span>
                  @else
                    <span class="px-2 py-0.5 rounded-md bg-gold-primary/10 border border-gold-primary/20 text-[8px] font-black text-gold-primary uppercase tracking-widest">DRAFT</span>
                  @endif
               </div>
            </div>
            
            <div class="flex items-center justify-between pt-2">
               <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full bg-white/5 flex items-center justify-center text-[10px] font-black text-white/40">
                     {{ substr($o->creator?->name ?? 'A', 0, 1) }}
                  </div>
                  <span class="text-[10px] text-white/40 font-medium italic">By: {{ $o->creator?->name ?? '-' }}</span>
               </div>
               <span class="text-[9px] text-gold-primary font-black uppercase tracking-widest">Detail →</span>
            </div>
         </a>
       @empty
         <div class="p-10 text-center text-white/30 italic text-xs">Belum ada data.</div>
       @endforelse
    </div>
  </div>

  <div class="mt-8">
    {{ $opnames->onEachSide(1)->links() }}
  </div>
@endsection
