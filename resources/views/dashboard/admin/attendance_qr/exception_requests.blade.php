@extends('layouts.admin')
@section('title', 'Pengajuan Absensi Darurat')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Absensi Darurat</h1>
        <p class="text-sm text-white/40 font-medium italic">Otorisasi absensi <span class="text-gold-primary font-bold not-italic">lintas perangkat (Exception).</span></p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @php
            $filters = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'Semua'];
        @endphp

        @foreach($filters as $k => $label)
            <a href="{{ route('admin.attendance.exception_requests', ['status' => $k]) }}" 
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
                              <span class="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-[8px] font-black text-white/40 uppercase tracking-widest">{{ $it->mode }}</span>
                           </div>
                           <div class="text-[10px] text-white/30 font-medium italic mt-0.5">Diajukan pada {{ $it->created_at?->format('d M Y • H:i') }}</div>
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
                              <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                 </svg>
                              </div>
                              <div>
                                 <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-0.5">Device Borrowed From</div>
                                 <div class="text-[11px] font-bold text-white">{{ $it->deviceOwnerUser?->name ?? 'System/Unknown' }}</div>
                              </div>
                           </div>
                           <div class="pt-2 border-t border-white/5">
                              <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Hardware ID</div>
                              <div class="text-[10px] font-mono text-white/40 break-all leading-tight">{{ $it->device_hash }}</div>
                           </div>
                        </div>

                        <div class="p-6 rounded-3xl bg-black/40 border border-white/5 space-y-4">
                           <div class="flex items-center gap-3">
                              <div class="w-8 h-8 rounded-lg bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                 </svg>
                              </div>
                              <div>
                                 <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-0.5">Location Snapshot</div>
                                 <a target="_blank" href="https://www.google.com/maps?q={{ $it->lat }},{{ $it->lng }}" class="text-[11px] font-bold text-gold-primary hover:underline italic">{{ number_format($it->lat, 4) }}, {{ number_format($it->lng, 4) }}</a>
                              </div>
                           </div>
                           <div class="pt-2 border-t border-white/5">
                              <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Employee's Statement</div>
                              <div class="text-[10px] text-white/60 font-medium leading-relaxed">"{{ $it->reason ?: 'Tidak ada alasan spesifik.' }}"</div>
                           </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: MEDIA & ACTIONS -->
                <div class="w-full xl:w-[350px] shrink-0 space-y-6">
                    <div class="relative group/photo overflow-hidden rounded-3xl border-4 border-white/5 shadow-2xl bg-obsidian-900 aspect-video lg:aspect-square flex items-center justify-center">
                        @if($it->photo_path)
                           <img src="{{ route('admin.attendance.exception_requests.photo', $it->id) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover/photo:scale-110" alt="Selfie Verification">
                           <div class="absolute inset-0 bg-gradient-to-t from-obsidian-950/80 to-transparent flex items-end p-4 opacity-0 group-hover/photo:opacity-100 transition-opacity">
                              <span class="text-[9px] font-black text-white uppercase tracking-widest">Verification Image</span>
                           </div>
                        @else
                           <div class="text-center space-y-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white/10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                              </svg>
                              <div class="text-[10px] font-black text-white/20 uppercase tracking-widest">No Image Captured</div>
                           </div>
                        @endif
                    </div>

                    @if($it->status === 'pending')
                       <div class="space-y-3">
                          <form method="POST" action="{{ route('admin.attendance.exception_requests.approve', $it->id) }}">
                             @csrf
                             <button class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-sm font-black text-obsidian-950 uppercase tracking-[0.2em] shadow-lg shadow-emerald-500/10 hover:scale-[1.02] active:scale-95 transition-all">Approve Request</button>
                          </form>
                          <form method="POST" action="{{ route('admin.attendance.exception_requests.reject', $it->id) }}" class="space-y-3">
                             @csrf
                             <input name="review_note" placeholder="Alasan penolakan..." class="w-full rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3 text-xs text-white placeholder:text-white/20 outline-none focus:border-red-500/30 transition-all">
                             <button class="w-full py-4 rounded-2xl bg-white/5 border border-white/10 text-xs font-black text-red-400 uppercase tracking-[0.2em] hover:bg-red-500/10 hover:border-red-500/20 active:scale-95 transition-all">Reject Exception</button>
                          </form>
                       </div>
                    @else
                       <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/5 space-y-3">
                          <div class="flex items-center justify-between text-[9px] font-black text-white/20 uppercase tracking-widest">
                             <span>Reviewed By</span>
                             <span class="text-white/60 italic">{{ $it->reviewer?->name ?? 'System' }}</span>
                          </div>
                          <div class="flex items-center justify-between text-[9px] font-black text-white/20 uppercase tracking-widest">
                             <span>Decision Time</span>
                             <span class="text-white/60 italic">{{ $it->reviewed_at?->format('d M, H:i') }}</span>
                          </div>
                          @if($it->review_note)
                             <div class="pt-3 border-t border-white/5">
                                <div class="text-[9px] font-black text-white/20 uppercase tracking-widest mb-1">Review Note</div>
                                <div class="text-[10px] text-white/60 font-medium italic">"{{ $it->review_note }}"</div>
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
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
           </div>
           <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Antrian Bersih</h3>
           <p class="text-xs text-white/30 italic mt-2">Tidak ada pengajuan absensi darurat yang perlu diproses.</p>
        </div>
    @endforelse

    <div class="mt-10">
        {{ $items->links() }}
    </div>
  </div>
@endsection