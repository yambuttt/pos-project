@extends('layouts.admin')
@section('title', 'Edit Profil User')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Edit User</h1>
        <p class="text-sm text-white/40 font-medium italic">Memperbarui informasi akun <span class="text-gold-primary font-bold not-italic">#{{ $user->name }}</span></p>
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
        
        <form method="POST" action="{{ route('admin.cashiers.update', $user) }}" class="relative z-10 space-y-10">
            @csrf @method('PUT')

            <!-- SECTION 1: KREDENSIAL -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                   </div>
                   <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Informasi Profil</h3>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama..."
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
                        @error('name') <p class="text-[10px] text-red-400 italic font-medium ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Email (Unik)</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="user@example.com"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
                        @error('email') <p class="text-[10px] text-red-400 italic font-medium ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Role / Hak Akses</label>
                    <select name="role" class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23D4AF37%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.1rem_1.1rem] bg-[right:1rem_center] bg-no-repeat">
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>ADMIN (Full Access)</option>
                        <option value="kasir" @selected(old('role', $user->role) === 'kasir')>KASIR (Transaksi)</option>
                        <option value="kitchen" @selected(old('role', $user->role) === 'kitchen')>KITCHEN (Proses Menu)</option>
                        <option value="pegawai" @selected(old('role', $user->role) === 'pegawai')>PEGAWAI (Absensi)</option>
                    </select>
                    @error('role') <p class="text-[10px] text-red-400 italic font-medium ml-1 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- SECTION 2: PASSWORD (OPTIONAL) -->
            <div class="premium-card p-8 border-white/5 bg-white/[0.02] space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-400 border border-red-500/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                      </svg>
                   </div>
                   <div>
                      <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Ganti Password (Opsional)</h3>
                      <p class="text-[9px] text-white/30 italic">Kosongkan jika tidak ingin mengubah password.</p>
                   </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Password Baru</label>
                        <input type="password" name="password" placeholder="Min. 6 karakter"
                            class="w-full rounded-xl border border-white/5 bg-black/40 px-4 py-3.5 text-sm text-white outline-none focus:border-red-500/30 transition-all">
                        @error('password') <p class="text-[10px] text-red-400 italic font-medium ml-1 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Ulangi Password</label>
                        <input type="password" name="password_confirmation" placeholder="Ketik ulang..."
                            class="w-full rounded-xl border border-white/5 bg-black/40 px-4 py-3.5 text-sm text-white outline-none focus:border-red-500/30 transition-all">
                    </div>
                </div>
            </div>

            <button class="w-full rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark py-5 text-sm font-black text-obsidian-950 uppercase tracking-[0.3em] shadow-2xl shadow-gold-primary/20 hover:shadow-[0_0_30px_rgba(212,175,55,0.4)] hover:scale-[1.01] transition-all active:scale-[0.98] border border-gold-light/20">
                Simpan Perubahan Profil
            </button>
        </form>
    </div>

    <!-- RIGHT: USER SUMMARY -->
    <div class="space-y-6">
        <div class="glass-panel p-8 rounded-[2.5rem] relative overflow-hidden">
            <div class="text-[10px] font-black text-white/20 uppercase tracking-widest mb-6">Ringkasan Akun</div>
            
            <div class="flex flex-col items-center gap-4 mb-8">
               <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-gold-primary to-gold-dark p-[2px]">
                  <div class="w-full h-full rounded-[1.9rem] bg-obsidian-900 flex items-center justify-center text-2xl font-black text-gold-primary italic">
                     {{ strtoupper(substr($user->name, 0, 1)) }}
                  </div>
               </div>
               <div class="text-center">
                  <div class="text-lg font-bold text-white">{{ $user->name }}</div>
                  <div class="text-[10px] text-white/40 tracking-[0.2em] uppercase mt-1">{{ $user->role }}</div>
               </div>
            </div>

            <div class="space-y-3">
               <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                  <span class="text-[10px] text-white/30 uppercase font-black">Email</span>
                  <span class="text-[11px] text-white/70 font-medium italic">{{ $user->email }}</span>
               </div>
               <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                  <span class="text-[10px] text-white/30 uppercase font-black">Dibuat Oleh</span>
                  <span class="text-[11px] text-white/70 font-medium italic">{{ $user->creator?->name ?? 'System' }}</span>
               </div>
               <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                  <span class="text-[10px] text-white/30 uppercase font-black">Bergabung</span>
                  <span class="text-[11px] text-white/70 font-medium italic">{{ $user->created_at?->format('d M Y') }}</span>
               </div>
            </div>
        </div>

        <div class="premium-card p-8 border-white/5 bg-white/[0.02]">
            <div class="flex items-center gap-3 mb-4">
               <div class="w-2 h-2 rounded-full bg-gold-primary animate-pulse"></div>
               <h4 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Status Keamanan</h4>
            </div>
            <p class="text-[11px] text-white/40 leading-relaxed italic">Anda dapat mereset password user ini jika mereka lupa. Gunakan fitur ini secara bertanggung jawab untuk menjaga integritas data operasional.</p>
        </div>
    </div>
  </div>
@endsection