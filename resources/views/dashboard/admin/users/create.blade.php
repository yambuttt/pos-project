@extends('layouts.admin')
@section('title', 'Buat User Baru')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Buat User</h1>
        <p class="text-sm text-white/40 font-medium italic">Menambahkan personil operasional <span class="text-gold-primary font-bold not-italic">baru ke sistem.</span></p>
      </div>
    </div>

    <a href="{{ route('admin.cashiers.index') }}"
      class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-6 py-3.5 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali
    </a>
  </div>

  <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1.3fr_0.7fr]">
    <!-- LEFT: FORM -->
    <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] relative overflow-hidden h-fit">
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-gold-primary/5 blur-[100px] rounded-full"></div>
        
        <div class="relative z-10 space-y-10">
            <!-- SECTION 1: KREDENSIAL -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                   </div>
                   <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Data Akun & Kredensial</h3>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Nama Lengkap</label>
                        <input type="text" id="visible_name" value="{{ old('name') }}" placeholder="Masukkan nama..."
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all">
                        @error('name') <p class="text-[10px] text-red-400 italic font-medium ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Email (Unik)</label>
                        <input type="email" id="visible_email" value="{{ old('email') }}" placeholder="user@example.com"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all">
                        @error('email') <p class="text-[10px] text-red-400 italic font-medium ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 2: ROLE -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                      </svg>
                   </div>
                   <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Pilih Hak Akses (Role)</h3>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="role_radio" value="kasir" class="peer sr-only" {{ old('role', 'kasir') === 'kasir' ? 'checked' : '' }}>
                        <div class="p-4 rounded-2xl border border-white/5 bg-white/[0.02] text-center transition-all peer-checked:border-gold-primary peer-checked:bg-gold-primary/[0.05] group-hover:bg-white/[0.05]">
                           <div class="text-2xl mb-2 grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">🧾</div>
                           <div class="text-[10px] font-black text-white/40 uppercase tracking-widest peer-checked:text-gold-primary">Kasir</div>
                           <div class="text-[8px] text-white/20 italic mt-1">Transaksi & Bayar</div>
                        </div>
                    </label>

                    <label class="relative group cursor-pointer">
                        <input type="radio" name="role_radio" value="kitchen" class="peer sr-only" {{ old('role') === 'kitchen' ? 'checked' : '' }}>
                        <div class="p-4 rounded-2xl border border-white/5 bg-white/[0.02] text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-500/[0.05] group-hover:bg-white/[0.05]">
                           <div class="text-2xl mb-2 grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">🍳</div>
                           <div class="text-[10px] font-black text-white/40 uppercase tracking-widest peer-checked:text-emerald-500">Kitchen</div>
                           <div class="text-[8px] text-white/20 italic mt-1">Proses Menu</div>
                        </div>
                    </label>

                    <label class="relative group cursor-pointer">
                        <input type="radio" name="role_radio" value="pegawai" class="peer sr-only" {{ old('role') === 'pegawai' ? 'checked' : '' }}>
                        <div class="p-4 rounded-2xl border border-white/5 bg-white/[0.02] text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-500/[0.05] group-hover:bg-white/[0.05]">
                           <div class="text-2xl mb-2 grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">🧑‍💼</div>
                           <div class="text-[10px] font-black text-white/40 uppercase tracking-widest peer-checked:text-blue-500">Pegawai</div>
                           <div class="text-[8px] text-white/20 italic mt-1">Absensi Saja</div>
                        </div>
                    </label>
                </div>
                @error('role') <p class="text-[10px] text-red-400 italic font-medium ml-1 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- ACTUAL FORM -->
            <form method="POST" action="{{ route('admin.cashiers.store') }}" class="space-y-10">
                @csrf
                <input type="hidden" name="name" id="hidden_name" value="{{ old('name') }}">
                <input type="hidden" name="email" id="hidden_email" value="{{ old('email') }}">
                <input type="hidden" name="role" id="hidden_role" value="{{ old('role', 'kasir') }}">

                <!-- PASSWORD SECTION -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                       <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-400 border border-red-500/20">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                          </svg>
                       </div>
                       <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Keamanan (Password)</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Password Baru</label>
                            <input type="password" name="password" placeholder="Min. 6 karakter"
                                class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
                            @error('password') <p class="text-[10px] text-red-400 italic font-medium ml-1 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Ulangi Password</label>
                            <input type="password" name="password_confirmation" placeholder="Ketik ulang..."
                                class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
                        </div>
                    </div>
                </div>

                <button class="w-full rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark py-5 text-sm font-black text-obsidian-950 uppercase tracking-[0.3em] shadow-2xl shadow-gold-primary/20 hover:shadow-[0_0_30px_rgba(212,175,55,0.4)] hover:scale-[1.01] transition-all active:scale-[0.98] border border-gold-light/20">
                    Daftarkan Akun User
                </button>
            </form>
        </div>
    </div>

    <!-- RIGHT: INFO & CREATOR -->
    <div class="space-y-6">
        <div class="glass-panel p-8 rounded-[2.5rem] relative overflow-hidden">
            <div class="text-[10px] font-black text-white/20 uppercase tracking-widest mb-6">Penanggung Jawab</div>
            <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center gap-4">
               <div class="w-12 h-12 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.99 7.99 0 0120 13a7.99 7.99 0 01-2.343 5.657z" />
                  </svg>
               </div>
               <div>
                  <div class="text-sm font-bold text-white">{{ auth()->user()->name }}</div>
                  <div class="text-[10px] text-white/30 italic uppercase tracking-tighter">Current Admin</div>
               </div>
            </div>
            <p class="mt-4 text-[10px] text-white/30 italic text-center">Setiap pembuatan user akan diaudit & dicatat sebagai buatan Anda.</p>
        </div>

        <div class="premium-card p-8 border-white/5 bg-white/[0.02] space-y-6">
            <h4 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Panduan Cepat</h4>
            <ul class="space-y-4">
               <li class="flex items-start gap-3">
                  <span class="w-5 h-5 rounded-md bg-white/5 border border-white/10 flex items-center justify-center text-[10px] text-gold-primary font-black shrink-0">1</span>
                  <p class="text-[11px] text-white/40 leading-relaxed italic"><span class="text-white font-medium not-italic">Email</span> harus unik dan belum pernah terdaftar di sistem.</p>
               </li>
               <li class="flex items-start gap-3">
                  <span class="w-5 h-5 rounded-md bg-white/5 border border-white/10 flex items-center justify-center text-[10px] text-gold-primary font-black shrink-0">2</span>
                  <p class="text-[11px] text-white/40 leading-relaxed italic"><span class="text-white font-medium not-italic">Role</span> menentukan fitur apa saja yang bisa diakses user.</p>
               </li>
               <li class="flex items-start gap-3">
                  <span class="w-5 h-5 rounded-md bg-white/5 border border-white/10 flex items-center justify-center text-[10px] text-gold-primary font-black shrink-0">3</span>
                  <p class="text-[11px] text-white/40 leading-relaxed italic"><span class="text-white font-medium not-italic">Keamanan</span>: Gunakan minimal 6 karakter kombinasi yang kuat.</p>
               </li>
            </ul>
        </div>
    </div>
  </div>

  <script>
      (function () {
          const visibleName = document.getElementById('visible_name');
          const visibleEmail = document.getElementById('visible_email');
          const roleRadios = document.querySelectorAll('input[name="role_radio"]');

          const hiddenName = document.getElementById('hidden_name');
          const hiddenEmail = document.getElementById('hidden_email');
          const hiddenRole = document.getElementById('hidden_role');

          function sync() {
              if(visibleName && hiddenName) hiddenName.value = visibleName.value;
              if(visibleEmail && hiddenEmail) hiddenEmail.value = visibleEmail.value;
          }

          visibleName.addEventListener('input', sync);
          visibleEmail.addEventListener('input', sync);
          
          roleRadios.forEach(r => {
              r.addEventListener('change', () => {
                  if(r.checked) hiddenRole.value = r.value;
              });
              if(r.checked) hiddenRole.value = r.value;
          });

          // Initial sync
          sync();
      })();
  </script>
@endsection