@extends('layouts.admin')
@section('title', 'Tambah Meja Baru')

@section('body')
  <div class="mx-auto max-w-2xl">
    <!-- HEADER -->
    <div class="flex items-center gap-4 mb-10">
      <button id="openMobileSidebar" type="button"
        class="inline-flex lg:hidden items-center justify-center w-10 h-10 rounded-xl border border-white/10 bg-white/5 text-white/70 hover:bg-white/10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
      <div>
        <h1 class="text-3xl font-bold text-gold-gradient">Tambah Meja</h1>
        <p class="text-sm text-white/40 font-medium italic">Registrasi <span class="text-gold-primary font-bold not-italic">alokasi fisik</span> baru di area operasional.</p>
      </div>
    </div>

    @if ($errors->any())
        <div class="mb-8 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
          <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
          <p class="text-sm font-bold text-red-100">{{ $errors->first() }}</p>
        </div>
    @endif

    <div class="glass-panel p-8 sm:p-12 rounded-[3rem] relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
        
        <form method="POST" action="{{ route('admin.tables.store') }}" class="relative z-10 space-y-10">
            @csrf

            <div class="space-y-8">
                <!-- INPUT NAME -->
                <div class="space-y-3 group">
                   <label class="text-[10px] font-black text-white/20 uppercase tracking-[0.2em] ml-1 group-focus-within:text-gold-primary transition-colors">Nama / Nomor Meja</label>
                   <div class="relative">
                      <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/20 group-focus-within:text-gold-primary transition-colors">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                         </svg>
                      </div>
                      <input name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Contoh: 01, VIP-1, Terrace A"
                        class="w-full pl-14 pr-6 py-5 rounded-3xl bg-white/5 border border-white/10 text-sm font-bold text-white placeholder:text-white/20 outline-none focus:border-gold-primary/30 transition-all shadow-inner">
                   </div>
                </div>

                <!-- TOGGLE ACTIVE -->
                <div class="flex items-center justify-between p-6 rounded-3xl bg-black/40 border border-white/5 group hover:border-emerald-500/20 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                           </svg>
                        </div>
                        <div>
                           <h4 class="text-xs font-black text-white uppercase tracking-widest">Status Operasional</h4>
                           <p class="text-[10px] text-white/30 italic mt-0.5">Aktifkan untuk menerima pemesanan.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                        <div class="w-14 h-8 bg-white/5 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500/40 peer-checked:after:bg-emerald-400 peer-checked:after:border-emerald-300"></div>
                    </label>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('admin.tables.index') }}"
                   class="flex items-center justify-center py-5 rounded-3xl bg-white/5 border border-white/10 text-xs font-black text-white/40 uppercase tracking-[0.2em] hover:bg-white/10 hover:text-white transition-all active:scale-95">
                   Batal
                </a>
                <button type="submit"
                   class="py-5 rounded-3xl bg-gradient-to-r from-gold-primary to-gold-dark text-xs font-black text-obsidian-950 uppercase tracking-[0.2em] shadow-2xl shadow-gold-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                   Registrasi Meja
                </button>
            </div>
        </form>
    </div>

    <!-- HINT -->
    <div class="mt-8 p-8 rounded-[2.5rem] bg-white/[0.02] border border-dashed border-white/10 text-center">
       <p class="text-[10px] text-white/30 font-medium italic leading-relaxed">
          Sistem akan secara otomatis melakukan <span class="text-gold-primary/60 font-bold not-italic">provisioning QR Token</span> unik setelah meja didaftarkan. Anda dapat mencetak QR tersebut di halaman daftar meja.
       </p>
    </div>
  </div>
@endsection