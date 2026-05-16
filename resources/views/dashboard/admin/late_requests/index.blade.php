@extends('layouts.admin')
@section('title', 'Otorisasi Pengajuan Telat')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Pengajuan Telat</h1>
        <p class="text-sm text-white/40 font-medium italic">Otorisasi keterlambatan <span class="text-gold-primary font-bold not-italic">check-in personil.</span></p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @php
            $filters = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'Semua'];
        @endphp

        @foreach($filters as $k => $label)
            <a href="{{ route('admin.late_requests.index', ['status' => $k]) }}" 
               class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-95 border
               {{ $status === $k 
                  ? 'bg-gold-primary text-obsidian-950 border-gold-primary shadow-lg shadow-gold-primary/20' 
                  : 'bg-white/5 text-white/40 border-white/5 hover:bg-white/10 hover:text-white/70' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
  </div>

  @if (session('ok'))
    <div class="mb-6 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-green-100">{{ session('ok') }}</p>
    </div>
  @endif

  <div class="space-y-6">
    @forelse($items as $it)
        <div class="glass-panel p-8 rounded-[2.5rem] relative overflow-hidden group transition-all hover:border-white/10">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
            
            <div class="relative z-10 flex flex-col xl:flex-row gap-10">
                <!-- LEFT: INFO -->
                <div class="flex-1 space-y-8">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-xl font-black text-white italic">
                           {{ strtoupper(substr($it->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                           <div class="flex items-center gap-2">
                              <h3 class="text-lg font-bold text-white group-hover:text-gold-primary transition-colors">{{ $it->user?->name ?? 'Unknown' }}</h3>
                              <span class="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-[8px] font-black text-white/40 uppercase tracking-widest">{{ $it->date?->format('d M Y') }}</span>
                           </div>
                           <div class="text-[10px] text-white/30 font-medium italic mt-0.5">Bukti foto bersifat wajib untuk pengajuan ini.</div>
                        </div>
                        <div class="ml-auto">
                           <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border
                              {{ $it->status === 'pending' ? 'bg-gold-primary/10 border-gold-primary/20 text-gold-primary' : ($it->status === 'approved' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'bg-red-500/10 border-red-500/20 text-red-400') }}">
                              {{ $it->status }}
                           </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 rounded-3xl bg-black/40 border border-white/5 space-y-4">
                           <div class="flex items-center gap-3">
                              <div class="w-8 h-8 rounded-lg bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                 </svg>
                              </div>
                              <div>
                                 <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-0.5">Time Variance Request</div>
                                 <div class="text-[14px] font-black text-white italic">{{ $it->requested_until_time ? substr($it->requested_until_time,0,5) : '-' }} <span class="text-[9px] font-medium opacity-30 not-italic uppercase tracking-tighter">Deadline</span></div>
                              </div>
                           </div>
                           @if($it->status === 'approved')
                              <div class="pt-3 border-t border-white/5 flex items-center justify-between">
                                 <div class="text-[8px] font-black text-emerald-400/50 uppercase tracking-widest">Allowed Until</div>
                                 <div class="text-[11px] font-bold text-emerald-400 italic">{{ substr($it->allowed_until_time, 0, 5) }}</div>
                              </div>
                           @endif
                        </div>

                        <div class="p-6 rounded-3xl bg-black/40 border border-white/5 space-y-4">
                           <div class="flex items-center gap-3">
                              <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                 </svg>
                              </div>
                              <div>
                                 <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-0.5">Explanatory Note</div>
                                 <div class="text-[10px] text-white/60 font-medium leading-relaxed italic">"{{ $it->reason ?: 'Tidak ada alasan spesifik.' }}"</div>
                              </div>
                           </div>
                        </div>
                    </div>

                    @if($it->evidence_path)
                       <div class="relative group/evidence overflow-hidden rounded-3xl border-4 border-white/5 bg-obsidian-900 w-full max-w-sm">
                          <img src="{{ route('admin.late_requests.evidence', $it) }}" class="w-full h-auto object-cover transition-transform duration-500 group-hover/evidence:scale-105" alt="Evidence Photo">
                          <div class="absolute inset-0 bg-gradient-to-t from-obsidian-950/80 to-transparent flex items-end p-4 opacity-0 group-hover/evidence:opacity-100 transition-opacity">
                             <span class="text-[9px] font-black text-white uppercase tracking-widest">Supporting Evidence</span>
                          </div>
                       </div>
                    @else
                       <div class="p-4 rounded-2xl bg-red-500/5 border border-red-500/10 flex items-center gap-3">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                          </svg>
                          <span class="text-[10px] font-black text-red-400/70 uppercase tracking-widest">No Visual Evidence Attached</span>
                       </div>
                    @endif
                </div>

                <!-- RIGHT: ACTIONS -->
                <div class="w-full xl:w-[350px] shrink-0">
                    @if($it->status === 'pending')
                       <div class="p-8 rounded-[2.5rem] bg-white/[0.02] border border-white/5 space-y-6">
                          <div class="text-center">
                             <div class="text-[10px] font-black text-gold-primary uppercase tracking-[0.3em] mb-2">Audit Decision</div>
                             <p class="text-[10px] text-white/30 italic">Approval memberikan toleransi waktu tambahan untuk personil ini.</p>
                          </div>

                          <div class="space-y-4">
                             <form method="POST" action="{{ route('admin.late_requests.approve', $it) }}">
                                @csrf
                                <button class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-[10px] font-black text-obsidian-950 uppercase tracking-[0.2em] shadow-lg shadow-emerald-500/10 hover:scale-[1.02] active:scale-95 transition-all">Authorize Request</button>
                             </form>
                             <form method="POST" action="{{ route('admin.late_requests.reject', $it) }}" class="space-y-3">
                                @csrf
                                <input name="review_note" placeholder="Alasan penolakan..." class="w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-3 text-xs text-white placeholder:text-white/20 outline-none focus:border-red-500/30">
                                <button class="w-full py-4 rounded-2xl bg-white/5 border border-white/10 text-[10px] font-black text-red-400 uppercase tracking-[0.2em] hover:bg-red-500/10 hover:border-red-500/20 active:scale-95 transition-all">Decline Request</button>
                             </form>
                          </div>
                       </div>
                    @else
                       <div class="p-8 rounded-[2.5rem] bg-white/[0.02] border border-white/5 space-y-4">
                          <div class="flex items-center justify-between text-[9px] font-black text-white/20 uppercase tracking-widest">
                             <span>Final Reviewer</span>
                             <span class="text-white/60 italic">{{ $it->reviewer?->name ?? 'System' }}</span>
                          </div>
                          <div class="flex items-center justify-between text-[9px] font-black text-white/20 uppercase tracking-widest">
                             <span>Decision Time</span>
                             <span class="text-white/60 italic">{{ $it->reviewed_at?->format('d M, H:i') }}</span>
                          </div>
                          @if($it->review_note)
                             <div class="pt-4 border-t border-white/5">
                                <div class="text-[9px] font-black text-white/20 uppercase tracking-widest mb-1">Internal Note</div>
                                <div class="text-[10px] text-white/40 italic">"{{ $it->review_note }}"</div>
                             </div>
                          @endif
                       </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="glass-panel p-20 rounded-[3rem] text-center border-dashed border-white/10">
           <div class="w-20 h-20 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-6 text-white/10">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
           </div>
           <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Punctual State</h3>
           <p class="text-xs text-white/30 italic mt-2">Tidak ada pengajuan keterlambatan yang tertunda.</p>
        </div>
    @endforelse

    <div class="mt-10">
        {{ $items->links() }}
    </div>
  </div>
@endsection