@extends('toko.layouts.admin')

@section('title', 'Kategori Produk Toko')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-wide">Kategori Produk</h2>
            <p class="text-white/50 text-sm mt-1">Kelola kategori untuk mengelompokkan produk retail Anda.</p>
        </div>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-yellow-500 hover:bg-yellow-400 text-black px-4 py-2 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(234,179,8,0.3)] transition-all">
            + Tambah Kategori
        </button>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-white/5 border-b border-white/5 text-white/50 uppercase tracking-widest text-[10px]">
                    <th class="px-6 py-4 font-semibold">Nama Kategori</th>
                    <th class="px-6 py-4 font-semibold">Slug</th>
                    <th class="px-6 py-4 font-semibold text-center">Jml Produk</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-white/80">
                @forelse($categories as $cat)
                <tr class="hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4 font-medium text-white">{{ $cat->name }}</td>
                    <td class="px-6 py-4 text-white/50">{{ $cat->slug }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-white/10 px-2 py-1 rounded text-xs">{{ $cat->products_count }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description) }}')" class="text-yellow-500 hover:text-yellow-400 p-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <form action="{{ route('toko.categories.destroy', $cat) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 p-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-white/30">Belum ada kategori data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- Modal Tambah/Edit -->
<div id="createModal" class="hidden fixed inset-0 z-[60] bg-black/80 flex items-center justify-center p-4">
    <div class="bg-[#121212] border border-white/10 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white" id="modalTitle">Kategori Baru</h3>
            <button onclick="closeModal()" class="text-white/50 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        <form id="catForm" action="{{ route('toko.categories.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div>
                <label class="block text-xs uppercase tracking-widest text-white/50 mb-2">Nama Kategori</label>
                <input type="text" name="name" id="catName" required class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-white/50 mb-2">Deskripsi Singkat</label>
                <textarea name="description" id="catDesc" rows="3" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none"></textarea>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-white/70 hover:bg-white/5 text-sm font-bold">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-yellow-500 text-black hover:bg-yellow-400 text-sm font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.getElementById('catForm').action = "{{ route('toko.categories.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('catName').value = "";
        document.getElementById('catDesc').value = "";
        document.getElementById('modalTitle').innerText = "Kategori Baru";
    }

    function editCategory(id, name, desc) {
        document.getElementById('createModal').classList.remove('hidden');
        document.getElementById('catForm').action = "/toko/categories/" + id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('catName').value = name;
        document.getElementById('catDesc').value = desc;
        document.getElementById('modalTitle').innerText = "Edit Kategori";
    }
</script>
@endsection
