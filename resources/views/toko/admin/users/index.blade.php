@extends('toko.layouts.admin')

@section('title', 'Manajemen Akun Toko')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-up">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-wide">Manajemen Akun</h2>
            <p class="text-white/50 text-sm mt-1">Kelola akses akun admin dan kasir khusus retail toko.</p>
        </div>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-yellow-500 hover:bg-yellow-400 text-black px-4 py-2 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(234,179,8,0.3)] transition-all">
            + Tambah Akun
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-white/5 border-b border-white/5 text-white/50 uppercase tracking-widest text-[10px]">
                    <th class="px-6 py-4 font-semibold">Nama / Email</th>
                    <th class="px-6 py-4 font-semibold">Role (Akses)</th>
                    <th class="px-6 py-4 font-semibold">Tanggal Dibuat</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-white/80">
                @forelse($users as $user)
                <tr class="hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-600 to-yellow-400 p-0.5 shrink-0">
                                <div class="w-full h-full bg-[#121212] rounded-full flex items-center justify-center font-bold text-yellow-500 text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div>
                                <div class="font-bold text-white">{{ $user->name }}</div>
                                <div class="text-white/50 text-xs">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->role === 'admin')
                            <span class="px-2 py-1 bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 rounded-full text-[10px] font-bold uppercase">Admin Toko</span>
                        @else
                            <span class="px-2 py-1 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full text-[10px] font-bold uppercase">Kasir</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-white/50 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}')" class="text-yellow-500 hover:text-yellow-400 p-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        
                        @if($user->id !== auth()->id())
                        <form action="{{ route('toko.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus akun ini secara permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 p-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-white/30">Belum ada data akun tambahan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $users->links() }}
    </div>

</div>

<!-- Modal Tambah/Edit -->
<div id="createModal" class="hidden fixed inset-0 z-[60] bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-[#121212] border border-white/10 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden transform transition-all scale-95 origin-center" id="modalBox">
        <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center bg-gradient-to-r from-yellow-500/10 to-transparent">
            <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modalTitle">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Buat Akun Baru
            </h3>
            <button onclick="closeModal()" class="text-white/50 hover:text-white p-1 bg-white/5 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        
        <form id="userForm" action="{{ route('toko.users.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-white/50 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="userName" required placeholder="Budi Santoso" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 outline-none transition-colors">
            </div>
            
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-white/50 mb-1">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="userEmail" required placeholder="budi@ayorenne.com" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 outline-none transition-colors">
            </div>
            
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-white/50 mb-1">Peran (Role) <span class="text-red-500">*</span></label>
                <select name="role" id="userRole" required class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 outline-none appearance-none">
                    <option value="kasir">Pegawai Kasir</option>
                    <option value="admin">Administrator Toko</option>
                </select>
            </div>
            
            <div>
                <label class="block text-[10px] uppercase tracking-widest text-white/50 mb-1">Password <span class="text-red-500" id="pwdAsterisk">*</span></label>
                <input type="password" name="password" id="userPassword" required placeholder="Minimal 8 karakter" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 outline-none transition-colors">
                <p class="text-[10px] text-white/40 mt-1" id="pwdHint">Gunakan password yang kuat.</p>
            </div>
            
            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-white/70 hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                <button type="submit" id="submitBtn" class="flex-1 px-4 py-3 rounded-xl bg-yellow-500 text-black hover:bg-yellow-400 text-sm font-bold shadow-lg transition-colors">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.getElementById('userForm').action = "{{ route('toko.users.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('userName').value = "";
        document.getElementById('userEmail').value = "";
        document.getElementById('userRole').value = "kasir";
        document.getElementById('userPassword').value = "";
        document.getElementById('userPassword').required = true;
        document.getElementById('pwdAsterisk').style.display = "inline";
        document.getElementById('pwdHint').innerText = "Gunakan password yang kuat.";
        document.getElementById('modalTitle').innerHTML = '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> Buat Akun Baru';
        document.getElementById('submitBtn').innerText = "Simpan Akun";
    }

    function editUser(id, name, email, role) {
        document.getElementById('createModal').classList.remove('hidden');
        document.getElementById('userForm').action = "/toko/users/" + id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('userName').value = name;
        document.getElementById('userEmail').value = email;
        document.getElementById('userRole').value = role;
        document.getElementById('userPassword').value = "";
        document.getElementById('userPassword').required = false;
        document.getElementById('pwdAsterisk').style.display = "none";
        document.getElementById('pwdHint').innerText = "Kosongkan jika tidak ingin mengubah password.";
        document.getElementById('modalTitle').innerHTML = '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Edit Data Akun';
        document.getElementById('submitBtn').innerText = "Update Akun";
        
        // Add pop animation
        const box = document.getElementById('modalBox');
        box.classList.remove('scale-95');
        box.classList.add('scale-100');
    }
</script>
@endsection
