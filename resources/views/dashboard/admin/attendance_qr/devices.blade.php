@extends('layouts.admin')
@section('title', 'Manajemen Device Absensi')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Device Absensi</h1>
        <p class="text-sm text-white/40 font-medium italic">Otorisasi & manajemen <span class="text-gold-primary font-bold not-italic">perangkat keras personil.</span></p>
      </div>
    </div>

    <a href="{{ route('admin.attendance.qr') }}"
      class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-6 py-3.5 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Control Center
    </a>
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

  <div class="grid grid-cols-1 gap-10 lg:grid-cols-3 items-start">
    {{-- PENDING APPROVAL --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-3">
               <div class="w-2 h-2 rounded-full bg-gold-primary animate-pulse"></div>
               <h3 class="text-xs font-black text-white uppercase tracking-[0.3em]">Pending Approval</h3>
            </div>
            <span class="px-2 py-1 rounded-lg bg-gold-primary/10 border border-gold-primary/20 text-[10px] font-black text-gold-primary">{{ $pending->count() }}</span>
        </div>

        <div class="space-y-4">
           @forelse ($pending as $d)
              <div class="glass-panel p-6 rounded-[2rem] border-gold-primary/10 relative overflow-hidden group">
                 <div class="absolute -top-10 -right-10 w-24 h-24 bg-gold-primary/5 blur-2xl rounded-full"></div>
                 
                 <div class="relative z-10 space-y-5">
                    <div class="flex items-start justify-between">
                       <div>
                          <div class="text-sm font-bold text-white">{{ $d->user?->name ?? 'Unknown User' }}</div>
                          <div class="text-[9px] text-white/30 italic uppercase tracking-tighter">{{ $d->user?->email ?? '-' }}</div>
                       </div>
                       <div class="px-2 py-1 rounded-lg bg-white/5 border border-white/10 text-[9px] font-black text-white/40 uppercase">DEV #{{ $deviceNo[$d->id] ?? '-' }}</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-black/40 border border-white/5 space-y-3">
                       <div>
                          <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Hardware Signature</div>
                          <div class="text-[10px] font-mono text-gold-primary/70 break-all leading-tight">{{ $d->device_hash }}</div>
                       </div>
                       <div class="grid grid-cols-2 gap-4">
                          <div>
                             <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Last Sync</div>
                             <div class="text-[9px] text-white/60 font-medium italic">{{ $d->last_seen_at?->format('H:i') ?? 'Never' }}</div>
                          </div>
                          <div>
                             <div class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Requested</div>
                             <div class="text-[9px] text-white/60 font-medium italic">{{ $d->created_at?->format('d M') }}</div>
                          </div>
                       </div>
                    </div>

                    <form method="POST" action="{{ route('admin.attendance.devices.rename', $d->id) }}" class="space-y-2">
                       @csrf
                       <label class="text-[8px] font-black text-white/20 uppercase tracking-widest ml-1">Alias Device</label>
                       <div class="flex gap-2">
                          <input name="device_name" value="{{ $d->device_name ?? '' }}" placeholder="HP Pribadi / Tablet..."
                            class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-xs text-white placeholder:text-white/20 outline-none focus:border-gold-primary/30 transition-all">
                          <button class="px-4 rounded-xl bg-white/5 border border-white/10 text-[10px] font-bold text-white hover:bg-white/10 active:scale-95 transition-all">Save</button>
                       </div>
                    </form>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                       <form method="POST" action="{{ route('admin.attendance.devices.approve', $d->id) }}">
                          @csrf
                          <button class="w-full py-3 rounded-xl bg-gradient-to-r from-gold-primary to-gold-dark text-[10px] font-black text-obsidian-950 uppercase tracking-widest shadow-lg shadow-gold-primary/10 hover:scale-[1.02] active:scale-95 transition-all">Approve</button>
                       </form>
                       <form method="POST" action="{{ route('admin.attendance.devices.revoke', $d->id) }}">
                          @csrf
                          <button class="w-full py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-[10px] font-black text-red-400 uppercase tracking-widest hover:bg-red-500/20 active:scale-95 transition-all">Reject</button>
                       </form>
                    </div>
                 </div>
              </div>
           @empty
              <div class="glass-panel p-10 rounded-[2rem] border-dashed border-white/10 text-center">
                 <div class="text-[10px] font-black text-white/20 uppercase tracking-[0.2em] italic">Antrian Kosong</div>
              </div>
           @endforelse
        </div>
    </div>

    {{-- APPROVED DEVICES --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-3">
               <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
               <h3 class="text-xs font-black text-white uppercase tracking-[0.3em]">Authorized Devices</h3>
            </div>
            <span class="px-2 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-black text-emerald-400">{{ $approved->count() }}</span>
        </div>

        <div class="space-y-4">
           @forelse ($approved as $d)
              <div class="glass-panel p-6 rounded-[2rem] border-emerald-500/10 relative overflow-hidden group">
                 <div class="absolute -top-10 -right-10 w-24 h-24 bg-emerald-500/5 blur-2xl rounded-full"></div>
                 
                 <div class="relative z-10 space-y-5">
                    <div class="flex items-start justify-between">
                       <div>
                          <div class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors">{{ $d->user?->name ?? 'Unknown User' }}</div>
                          <div class="text-[9px] text-white/30 italic uppercase tracking-tighter">{{ $d->user?->email ?? '-' }}</div>
                       </div>
                       <div class="flex flex-col items-end gap-1">
                          <span class="px-2 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-[8px] font-black text-emerald-400 uppercase">ACTIVE</span>
                          <span class="text-[9px] text-white/20 font-black italic">DEV #{{ $deviceNo[$d->id] ?? '-' }}</span>
                       </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-500/[0.02] border border-emerald-500/10 flex items-center gap-4">
                       <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                          </svg>
                       </div>
                       <div>
                          <div class="text-[11px] font-bold text-white">{{ $d->device_name ?: 'Unnamed Device' }}</div>
                          <div class="text-[8px] text-white/30 font-mono break-all line-clamp-1 max-w-[150px]">{{ $d->device_hash }}</div>
                       </div>
                    </div>

                    <div class="flex gap-2">
                       <form method="POST" action="{{ route('admin.attendance.devices.revoke', $d->id) }}" class="flex-1">
                          @csrf
                          <button class="w-full py-3 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black text-white/40 uppercase tracking-widest hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/20 active:scale-95 transition-all">Revoke Access</button>
                       </form>
                    </div>
                 </div>
              </div>
           @empty
              <div class="glass-panel p-10 rounded-[2rem] border-dashed border-white/10 text-center">
                 <div class="text-[10px] font-black text-white/20 uppercase tracking-[0.2em] italic">Belum Ada Approval</div>
              </div>
           @endforelse
        </div>
    </div>

    {{-- REVOKED HISTORY --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-3">
               <div class="w-2 h-2 rounded-full bg-red-500"></div>
               <h3 class="text-xs font-black text-white uppercase tracking-[0.3em]">Revoked History</h3>
            </div>
            <span class="px-2 py-1 rounded-lg bg-red-500/10 border border-red-500/20 text-[10px] font-black text-red-400">{{ $revoked->count() }}</span>
        </div>

        <div class="space-y-3">
           @forelse ($revoked as $d)
              <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-between group transition-all hover:bg-white/[0.04]">
                 <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-500/5 flex items-center justify-center text-red-400/30 group-hover:text-red-400 transition-colors">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                       </svg>
                    </div>
                    <div>
                       <div class="text-xs font-bold text-white/60 group-hover:text-white transition-colors">{{ $d->user?->name ?? 'Unknown' }}</div>
                       <div class="text-[8px] text-white/20 italic uppercase tracking-tighter">REVOKED ON {{ $d->revoked_at?->format('d M Y') }}</div>
                    </div>
                 </div>
                 <div class="text-[9px] text-white/10 font-black italic">#{{ $deviceNo[$d->id] ?? '-' }}</div>
              </div>
           @empty
              <div class="text-center py-6">
                 <div class="text-[9px] font-black text-white/10 uppercase tracking-widest italic">No History</div>
              </div>
           @endforelse
        </div>

        <!-- EMERGENCY ACTION -->
        <div class="pt-10">
           <div class="p-6 rounded-[2rem] bg-red-500/5 border border-red-500/10 text-center space-y-4">
              <div class="text-[10px] font-black text-red-400 uppercase tracking-widest">Emergency System Control</div>
              <p class="text-[10px] text-white/30 italic">Reset semua otorisasi device untuk seluruh pegawai dalam keadaan darurat.</p>
              <form method="POST" action="{{ route('admin.attendance.users.reset_devices', ['user' => auth()->id()]) }}" onsubmit="return confirm('WARNING: Reset ALL employee devices? This cannot be undone.')">
                 @csrf
                 <button class="w-full py-3 rounded-xl bg-red-500/20 border border-red-500/40 text-[9px] font-black text-red-100 uppercase tracking-[0.2em] hover:bg-red-500 hover:text-white transition-all active:scale-95 shadow-xl shadow-red-500/10">Purge All Device Access</button>
              </form>
           </div>
        </div>
    </div>
  </div>
@endsection