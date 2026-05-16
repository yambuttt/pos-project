@extends('layouts.admin')
@section('title', 'Tambah Paket Buffet')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Tambah Paket</h1>
        <p class="text-sm text-white/40 font-medium italic">Buat skema <span class="text-gold-primary font-bold not-italic">paket buffet baru.</span></p>
      </div>
    </div>

    <a href="{{ route('admin.buffet_packages.index') }}"
      class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-6 py-3.5 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali
    </a>
  </div>

  @if($errors->any())
    <div class="mb-8 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
         <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
       </svg>
       <p class="text-sm font-bold text-red-100 whitespace-pre-line">{{ $errors->first() }}</p>
    </div>
  @endif

  <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-64 h-64 bg-gold-primary/5 blur-[100px] rounded-full"></div>
    
    <form method="POST" action="{{ route('admin.buffet_packages.store') }}" class="relative z-10 space-y-10">
      @csrf

      <!-- SECTION 1: INFORMASI PAKET -->
      <div class="space-y-6">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
             </div>
             <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Informasi Paket</h3>
          </div>

          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
              <div class="space-y-2 lg:col-span-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Nama Paket</label>
                  <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Paket Meeting VIP, Wedding Platinum..."
                      class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all"
                      required>
              </div>

              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Tipe Harga</label>
                  <select name="pricing_type" class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none" required>
                      <option value="per_pax">PER PAX (Per Orang)</option>
                      <option value="per_event">PER EVENT (Per Acara)</option>
                  </select>
              </div>
          </div>

          <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Harga Paket (Rp)</label>
                  <input type="number" name="price" value="{{ old('price') }}" placeholder="0"
                      class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-gold-primary font-bold outline-none focus:border-gold-primary/30 transition-all"
                      required>
              </div>
              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Minimal Pax (opsional)</label>
                  <input type="number" name="min_pax" value="{{ old('min_pax') }}" placeholder="Contoh: 30"
                      class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
              </div>
          </div>
      </div>

      <!-- SECTION 2: STATUS -->
      <div class="space-y-6">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m12 4a2 2 0 100-4m0 4a2 2 0 110-4m-6 0a2 2 0 100-4m0 4a2 2 0 110-4m-4 6h8m-12 0a2 2 0 100-4m0 4a2 2 0 110-4" />
                </svg>
             </div>
             <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Konfigurasi Status</h3>
          </div>

          <div class="space-y-2">
              <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Status Aktif</label>
              <select name="is_active" class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none">
                  <option value="1">YA (Aktif)</option>
                  <option value="0">TIDAK (Non-aktif)</option>
              </select>
          </div>
      </div>

      <button class="w-full rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark py-5 text-sm font-black text-obsidian-950 uppercase tracking-[0.3em] shadow-2xl shadow-gold-primary/20 hover:shadow-[0_0_30px_rgba(212,175,55,0.4)] hover:scale-[1.01] transition-all active:scale-[0.98] border border-gold-light/20">
          Simpan Paket Baru
      </button>
    </form>
  </div>
@endsection