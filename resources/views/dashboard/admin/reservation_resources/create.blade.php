@extends('layouts.admin')
@section('title', 'Tambah Resource Reservasi')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div class="flex items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gold-gradient">Tambah Resource</h1>
        <p class="text-sm text-white/40 font-medium italic">Konfigurasi <span class="text-gold-primary font-bold not-italic">meja, ruangan, atau aula baru.</span></p>
      </div>
    </div>

    <a href="{{ route('admin.reservation_resources.index') }}"
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
    
    <form method="POST" action="{{ route('admin.reservation_resources.store') }}" class="relative z-10 space-y-10">
      @csrf

      <!-- SECTION 1: INFORMASI UMUM -->
      <div class="space-y-6">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
             </div>
             <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Informasi Umum</h3>
          </div>

          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Nama Resource</label>
                  <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: VIP Room A, Meja 12..."
                      class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all"
                      required>
              </div>

              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Tipe</label>
                  <select name="type" class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none" required>
                      <option value="TABLE">TABLE (Meja)</option>
                      <option value="ROOM">ROOM (Ruangan)</option>
                      <option value="HALL">HALL (Aula)</option>
                  </select>
              </div>

              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Kapasitas (Pax)</label>
                  <input type="number" name="capacity" value="{{ old('capacity') }}" placeholder="Contoh: 10"
                      class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all"
                      required>
              </div>
          </div>
      </div>

      <!-- SECTION 2: PRICING -->
      <div class="space-y-6">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
             </div>
             <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Skema Harga (Opsional)</h3>
          </div>

          <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Tarif Per Jam (Rp)</label>
                  <input type="number" name="hourly_rate" value="{{ old('hourly_rate') }}" placeholder="0"
                      class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-gold-primary font-bold outline-none focus:border-gold-primary/30 transition-all">
              </div>
              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Tarif Flat (Rp)</label>
                  <input type="number" name="flat_rate" value="{{ old('flat_rate') }}" placeholder="0"
                      class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-blue-400 font-bold outline-none focus:border-blue-500/30 transition-all">
              </div>
          </div>
      </div>

      <!-- SECTION 3: CONFIGURATION -->
      <div class="space-y-6">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m12 4a2 2 0 100-4m0 4a2 2 0 110-4m-6 0a2 2 0 100-4m0 4a2 2 0 110-4m-4 6h8m-12 0a2 2 0 100-4m0 4a2 2 0 110-4" />
                </svg>
             </div>
             <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Konfigurasi Durasi & Status</h3>
          </div>

          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Min. Durasi (Menit)</label>
                  <input type="number" name="min_duration_minutes" value="{{ old('min_duration_minutes', 60) }}"
                      class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
              </div>
              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Buffer Waktu (Menit)</label>
                  <input type="number" name="buffer_minutes" value="{{ old('buffer_minutes', 15) }}"
                      class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
              </div>
              <div class="space-y-2">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Status Aktif</label>
                  <select name="is_active" class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none">
                      <option value="1">YA (Aktif)</option>
                      <option value="0">TIDAK (Non-aktif)</option>
                  </select>
              </div>
          </div>
      </div>

      <button class="w-full rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark py-5 text-sm font-black text-obsidian-950 uppercase tracking-[0.3em] shadow-2xl shadow-gold-primary/20 hover:shadow-[0_0_30px_rgba(212,175,55,0.4)] hover:scale-[1.01] transition-all active:scale-[0.98] border border-gold-light/20">
          Simpan Resource Baru
      </button>
    </form>
  </div>
@endsection