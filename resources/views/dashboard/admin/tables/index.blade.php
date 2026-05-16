@extends('layouts.admin')
@section('title', 'Manajemen Meja')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Kelola Meja</h1>
        <p class="text-sm text-white/40 font-medium italic">Konfigurasi alokasi <span class="text-gold-primary font-bold not-italic">meja & QR pemesanan mandiri.</span></p>
      </div>
    </div>

    <a href="{{ route('admin.tables.create') }}"
      class="flex items-center gap-2 rounded-2xl bg-gold-primary px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest hover:bg-gold-light transition-all active:scale-95 shadow-lg shadow-gold-primary/20">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
      </svg>
      Tambah Meja
    </a>
  </div>

  @if(session('success'))
    <div class="mb-8 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-green-100">{{ session('success') }}</p>
    </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
    @foreach($tables as $t)
      <div class="glass-panel p-8 rounded-[2.5rem] relative overflow-hidden group transition-all hover:border-gold-primary/30">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full transition-all group-hover:scale-150"></div>
        
        <div class="relative z-10 space-y-6">
            <!-- TOP: NAME & STATUS -->
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-2xl font-black text-white group-hover:text-gold-primary transition-colors italic uppercase tracking-tighter">{{ $t->name }}</h3>
                    <div class="mt-1 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $t->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest {{ $t->is_active ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $t->is_active ? 'Operasional' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.tables.edit', $t->id) }}"
                       class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:text-gold-primary hover:border-gold-primary/20 transition-all active:scale-90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('admin.tables.destroy', $t->id) }}" onsubmit="return confirm('Hapus konfigurasi meja ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-white/20 hover:text-red-400 hover:border-red-500/20 transition-all active:scale-90">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- QR SECTION -->
            @php
              $url = rtrim(config('app.url'), '/') . route('public.table.token', $t->qr_token, false);
              $qrImg = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($url);
            @endphp

            <div class="p-6 rounded-[2rem] bg-white/[0.03] border border-white/5 space-y-6">
                <div class="relative mx-auto w-32 h-32 p-3 bg-white rounded-3xl shadow-2xl shadow-gold-primary/10 overflow-hidden group/qr">
                    <img src="{{ $qrImg }}" alt="QR {{ $t->name }}" class="w-full h-full object-contain" />
                    <div class="absolute inset-0 bg-gold-primary/80 flex items-center justify-center opacity-0 group-hover/qr:opacity-100 transition-opacity">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-obsidian-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                       </svg>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-[9px] font-black text-white/20 uppercase tracking-[0.2em] mb-1">Self-Order URL</div>
                        <a href="{{ $url }}" target="_blank" class="text-[10px] text-gold-primary/60 hover:text-gold-primary transition-colors font-mono break-all leading-relaxed">{{ $url }}</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button type="button"
                          class="py-3 rounded-xl bg-white/5 border border-white/10 text-[9px] font-black text-white/40 uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all active:scale-95"
                          onclick="navigator.clipboard.writeText('{{ $url }}'); alert('Link disalin!')">
                          Copy Link
                        </button>

                        <form method="POST" action="{{ route('admin.tables.regenerateQr', $t->id) }}" onsubmit="return confirm('Sistem akan merusak token lama. QR yang tercetak tidak akan berlaku lagi. Lanjutkan?')">
                          @csrf
                          <button type="submit"
                            class="w-full py-3 rounded-xl bg-white/5 border border-white/10 text-[9px] font-black text-yellow-500/50 uppercase tracking-widest hover:bg-yellow-500/10 hover:text-yellow-400 hover:border-yellow-500/20 transition-all active:scale-95">
                            Rotate QR
                          </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="flex items-center justify-between pt-2">
                <div class="text-[9px] font-black text-white/10 uppercase tracking-widest italic">Provisioned: {{ $t->created_at?->format('d M Y') }}</div>
                <div class="flex items-center gap-1 opacity-20">
                   <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                   <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                   <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                </div>
            </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-12">
      {{ $tables->links() }}
  </div>
@endsection