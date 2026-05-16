@extends('layouts.admin')
@section('title', 'Pengajuan Lembur Pegawai')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Pengajuan Lembur</h1>
        <p class="text-sm text-white/40 font-medium italic">Otorisasi durasi <span class="text-gold-primary font-bold not-italic">kerja tambahan personil.</span></p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @php
            $filters = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'Semua'];
        @endphp

        @foreach($filters as $k => $label)
            <a href="{{ route('admin.overtime_requests.index', ['status' => $k]) }}" 
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

  @if (session('error'))
    <div class="mb-6 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-red-100">{{ session('error') }}</p>
    </div>
  @endif

  <div class="space-y-6">
    @forelse($items as $it)
        <div class="glass-panel p-8 rounded-[2.5rem] relative overflow-hidden group transition-all hover:border-white/10">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
            
            <div class="relative z-10 flex flex-col xl:flex-row gap-10">
                <!-- LEFT: EMPLOYEE INFO -->
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
                           <div class="text-[10px] text-white/30 font-medium italic mt-0.5">Pengajuan lembur diaudit oleh sistem.</div>
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
                                 <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-0.5">Requested Duration</div>
                                 <div class="text-[14px] font-black text-white italic">{{ $it->requested_minutes }} <span class="text-[9px] font-medium opacity-30 not-italic uppercase tracking-tighter">Minutes</span></div>
                              </div>
                           </div>
                           @if($it->approved_minutes)
                              <div class="pt-3 border-t border-white/5 flex items-center justify-between">
                                 <div class="text-[8px] font-black text-emerald-400/50 uppercase tracking-widest">Final Approval</div>
                                 <div class="text-[11px] font-bold text-emerald-400 italic">{{ $it->approved_minutes }} Min</div>
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
                                 <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-0.5">Business Reason</div>
                                 <div class="text-[10px] text-white/60 font-medium leading-relaxed italic">"{{ $it->reason ?: 'Tidak ada alasan spesifik.' }}"</div>
                              </div>
                           </div>
                        </div>
                    </div>

                    @if($it->status !== 'pending')
                       <div class="pt-4" x-data="{ editing: false }">
                          <div class="flex items-center justify-between px-2 mb-2">
                             <div class="text-[9px] font-black text-white/20 uppercase tracking-widest">Internal Admin Note</div>
                             <button @click="editing = !editing" class="text-[9px] font-black text-gold-primary uppercase tracking-widest hover:underline">Edit Note</button>
                          </div>
                          
                          <div x-show="!editing" class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 text-[10px] text-white/40 italic">
                             "{{ $it->review_note ?: 'No internal notes provided.' }}"
                          </div>

                          <form x-show="editing" method="POST" action="{{ route('admin.overtime_requests.update_note', $it) }}" class="flex gap-2">
                             @csrf
                             <input name="review_note" value="{{ $it->review_note }}" class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-[11px] text-white placeholder:text-white/20 outline-none focus:border-gold-primary/30">
                             <button type="submit" class="px-4 rounded-xl bg-gold-primary text-[10px] font-black text-obsidian-950 uppercase">Save</button>
                          </form>
                       </div>
                    @endif
                </div>

                <!-- RIGHT: ACTIONS -->
                <div class="w-full xl:w-[350px] shrink-0">
                    @if($it->status === 'pending')
                       <div class="p-8 rounded-[2.5rem] bg-white/[0.02] border border-white/5 space-y-6">
                          <div class="text-center">
                             <div class="text-[10px] font-black text-gold-primary uppercase tracking-[0.3em] mb-2">Decision Center</div>
                             <p class="text-[10px] text-white/30 italic">Pastikan lembur sesuai dengan beban kerja operasional hari ini.</p>
                          </div>

                          <form method="POST" class="space-y-4">
                             @csrf
                             <div class="space-y-2">
                                <label class="text-[8px] font-black text-white/20 uppercase tracking-widest ml-1">Internal Note (Optional)</label>
                                <textarea name="review_note" placeholder="Tulis alasan approve/reject..." rows="2"
                                   class="w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-3 text-xs text-white placeholder:text-white/20 outline-none focus:border-gold-primary/30 transition-all"></textarea>
                             </div>

                             <div class="grid grid-cols-2 gap-3">
                                <button type="submit" formaction="{{ route('admin.overtime_requests.approve', $it) }}"
                                   class="py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-[10px] font-black text-obsidian-950 uppercase tracking-[0.2em] shadow-lg shadow-emerald-500/10 hover:scale-[1.02] active:scale-95 transition-all">Approve</button>
                                <button type="submit" formaction="{{ route('admin.overtime_requests.reject', $it) }}"
                                   class="py-4 rounded-2xl bg-white/5 border border-white/10 text-[10px] font-black text-red-400 uppercase tracking-[0.2em] hover:bg-red-500/10 hover:border-red-500/20 active:scale-95 transition-all">Reject</button>
                             </div>
                          </form>
                       </div>
                    @else
                       <div class="p-8 rounded-[2.5rem] bg-white/[0.02] border border-white/5 space-y-4">
                          <div class="flex items-center justify-between text-[9px] font-black text-white/20 uppercase tracking-widest">
                             <span>Final Reviewer</span>
                             <span class="text-white/60 italic">{{ $it->reviewer?->name ?? 'System' }}</span>
                          </div>
                          <div class="flex items-center justify-between text-[9px] font-black text-white/20 uppercase tracking-widest">
                             <span>Review Date</span>
                             <span class="text-white/60 italic">{{ $it->reviewed_at?->format('d M, H:i') }}</span>
                          </div>
                          <div class="pt-4 border-t border-white/5">
                             <div class="flex items-center gap-2 text-gold-primary/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span class="text-[8px] font-black uppercase tracking-widest">Audit Verified</span>
                             </div>
                          </div>
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
           <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Zero Queue</h3>
           <p class="text-xs text-white/30 italic mt-2">Seluruh pengajuan lembur telah diproses.</p>
        </div>
    @endforelse

    <div class="mt-10">
        {{ $items->links() }}
    </div>
  </div>
@endsection