@extends('layouts.admin')
@section('title', 'Kelola User')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Kelola User</h1>
        <p class="text-sm text-white/40 font-medium italic">Manajemen akun <span class="text-gold-primary font-bold not-italic">Admin, Kasir, Kitchen, & Pegawai.</span></p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.cashiers.create') }}"
        class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah User
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-6 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-green-100">{{ session('success') }}</p>
    </div>
  @endif

  <!-- FILTER PANEL -->
  <div class="glass-panel p-8 rounded-[2.5rem] mb-10 relative overflow-hidden group">
    <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
    
    <div class="flex flex-col md:flex-row gap-6 items-end relative z-10">
      <div class="flex-1 space-y-2">
        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Cari Nama / Email / Role</label>
        <div class="relative">
           <input id="userSearch" type="text" placeholder="Masukkan kata kunci..."
             class="w-full rounded-xl border border-white/5 bg-white/[0.02] pl-10 pr-4 py-3 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-4 top-1/2 -translate-y-1/2 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
           </svg>
        </div>
      </div>

      <div class="w-full md:w-64 space-y-2">
        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Filter Role</label>
        <select id="roleFilter" class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23D4AF37%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.1rem_1.1rem] bg-[right:1rem_center] bg-no-repeat">
          <option value="">Semua Role</option>
          <option value="admin">Admin</option>
          <option value="kasir">Kasir</option>
          <option value="kitchen">Kitchen</option>
          <option value="pegawai">Pegawai</option>
        </select>
      </div>
    </div>
  </div>

  <!-- TABLE SECTION -->
  <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/5">
    <!-- DESKTOP TABLE -->
    <div class="hidden lg:block overflow-x-auto">
      <table class="w-full text-left" id="userTable">
        <thead>
          <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-white/40 font-black border-b border-white/5">
            <th class="px-8 py-6">User</th>
            <th class="px-6 py-6">Role</th>
            <th class="px-6 py-6">Dibuat Oleh</th>
            <th class="px-6 py-6">Tanggal Dibuat</th>
            <th class="px-8 py-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @forelse($users as $u)
            <tr class="user-row group hover:bg-white/[0.02] transition-colors"
                data-name="{{ strtolower($u->name) }}"
                data-email="{{ strtolower($u->email) }}"
                data-role="{{ strtolower($u->role) }}">
              <td class="px-8 py-6">
                 <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 border border-white/10 flex items-center justify-center text-gold-primary text-sm font-black italic shadow-inner">
                       {{ strtoupper(substr($u->name, 0, 1)) }}{{ strtoupper(substr(strrchr($u->name, " "), 1, 1)) ?: '' }}
                    </div>
                    <div>
                       <div class="text-sm font-bold text-white group-hover:text-gold-primary transition-colors">{{ $u->name }}</div>
                       <div class="text-[10px] text-white/30 italic mt-0.5">{{ $u->email }}</div>
                    </div>
                 </div>
              </td>
              <td class="px-6 py-6">
                 @php
                    $roleColors = [
                        'admin' => 'bg-gold-primary/10 border-gold-primary/20 text-gold-primary',
                        'kasir' => 'bg-blue-500/10 border-blue-500/20 text-blue-400',
                        'kitchen' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                        'pegawai' => 'bg-white/5 border-white/10 text-white/40'
                    ];
                    $c = $roleColors[$u->role] ?? $roleColors['pegawai'];
                 @endphp
                 <span class="px-3 py-1 rounded-lg border text-[9px] font-black uppercase tracking-widest {{ $c }}">
                    {{ $u->role }}
                 </span>
              </td>
              <td class="px-6 py-6">
                 <div class="text-xs font-bold text-white/60">{{ $u->creator?->name ?? 'System' }}</div>
                 <div class="text-[10px] text-white/20 italic mt-0.5">{{ $u->creator?->email ?? '-' }}</div>
              </td>
              <td class="px-6 py-6">
                 <div class="text-xs font-medium text-white/40 italic">{{ $u->created_at?->format('d M Y') }}</div>
                 <div class="text-[10px] text-white/20 italic mt-0.5">{{ $u->created_at?->format('H:i') }}</div>
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex items-center justify-end gap-2">
                   <a href="{{ route('admin.cashiers.edit', $u) }}"
                      class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:bg-gold-primary hover:text-obsidian-950 hover:border-gold-primary transition-all">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z" />
                      </svg>
                   </a>
                   @if(auth()->id() !== $u->id)
                     <form method="POST" action="{{ route('admin.cashiers.destroy', $u) }}" class="inline" onsubmit="return confirm('Hapus user ini?')">
                       @csrf @method('DELETE')
                       <button class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                       </button>
                     </form>
                   @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-8 py-24 text-center">
                 <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center text-white/10">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                       </svg>
                    </div>
                    <p class="text-sm text-white/30 font-medium italic">Belum ada user yang ditambahkan.</p>
                 </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- MOBILE LIST -->
    <div class="lg:hidden divide-y divide-white/5" id="userListMobile">
       @forelse($users as $u)
         <div class="user-row p-6 premium-card border-none rounded-none bg-transparent space-y-4"
              data-name="{{ strtolower($u->name) }}"
              data-email="{{ strtolower($u->email) }}"
              data-role="{{ strtolower($u->role) }}">
            <div class="flex items-start justify-between gap-4">
               <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-gold-primary text-sm font-black italic">
                    {{ strtoupper(substr($u->name, 0, 1)) }}
                  </div>
                  <div>
                    <h4 class="text-sm font-bold text-white">{{ $u->name }}</h4>
                    <p class="text-[10px] text-white/20 italic mt-0.5">{{ $u->email }}</p>
                  </div>
               </div>
               <div>
                  <span class="px-2 py-0.5 rounded-md bg-white/5 text-white/40 text-[8px] font-black uppercase tracking-widest border border-white/10">
                    {{ $u->role }}
                  </span>
               </div>
            </div>

            <div class="flex gap-2">
               <a href="{{ route('admin.cashiers.edit', $u) }}"
                  class="flex-1 py-3 rounded-xl bg-white/5 border border-white/10 text-center text-[10px] font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all">
                  Edit
               </a>
               @if(auth()->id() !== $u->id)
                 <form method="POST" action="{{ route('admin.cashiers.destroy', $u) }}" class="flex-1" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button class="w-full py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-center text-[10px] font-black text-red-500 uppercase tracking-widest">
                       Hapus
                    </button>
                 </form>
               @endif
            </div>
         </div>
       @empty
         <div class="p-12 text-center text-white/20 italic text-xs font-medium">Data kosong.</div>
       @endforelse
    </div>
  </div>

  <div class="mt-8">
    {{ $users->onEachSide(1)->links() }}
  </div>

  <script>
    (function () {
      const search = document.getElementById('userSearch');
      const roleFilter = document.getElementById('roleFilter');

      function normalize(v) {
        return (v || '').toString().toLowerCase().trim();
      }

      function applyFilter() {
        const q = normalize(search?.value);
        const role = normalize(roleFilter?.value);

        document.querySelectorAll('.user-row').forEach((row) => {
          const name = normalize(row.getAttribute('data-name'));
          const email = normalize(row.getAttribute('data-email'));
          const r = normalize(row.getAttribute('data-role'));

          const matchText = !q || name.includes(q) || email.includes(q) || r.includes(q);
          const matchRole = !role || r === role;

          row.style.display = (matchText && matchRole) ? '' : 'none';
        });
      }

      if (search) search.addEventListener('input', applyFilter);
      if (roleFilter) roleFilter.addEventListener('change', applyFilter);
    })();
  </script>
@endsection