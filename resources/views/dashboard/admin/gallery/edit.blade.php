@extends('layouts.admin')
@section('title', 'Edit Foto Galeri')

@section('body')
    <!-- HEADER -->
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gold-gradient">Edit Foto</h1>
            <p class="text-sm text-white/40 font-medium">Perbarui informasi atau ganti foto galeri.</p>
        </div>

        <a href="{{ route('admin.gallery.index') }}"
            class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-sm font-bold text-white border border-white/5 hover:bg-white/10 transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            KEMBALI
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-sm font-bold text-red-100">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.gallery.update', $gallery->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- MAIN DETAILS -->
            <div class="lg:col-span-2 glass-panel p-8 rounded-[2.5rem] space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Judul / Keterangan (Opsional)</label>
                    <input name="title" value="{{ old('title', $gallery->title) }}" placeholder="Contoh: Interior Ambiance, Signature Dish"
                        class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Urutan Tampil</label>
                        <input name="sort_order" type="number" value="{{ old('sort_order', $gallery->sort_order) }}"
                            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Status</label>
                        <label class="flex items-center justify-between p-4 glass-card rounded-2xl cursor-pointer hover:bg-white/5 transition-all">
                            <span class="text-sm font-bold text-white/80">Aktif</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input name="is_active" type="checkbox" value="1" {{ $gallery->is_active ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold-primary"></div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- IMAGE UPLOAD -->
            <div class="space-y-8">
                <div class="glass-panel p-8 rounded-[2.5rem]">
                    <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1 mb-4 block">Ganti Foto (Opsional)</label>
                    
                    <div id="imagePreviewContainer" class="mb-6 animate-fade-in">
                        <img id="imagePreview" src="{{ $gallery->imageUrl() }}" alt="Preview" class="w-full aspect-square rounded-3xl object-cover border border-white/10 shadow-2xl">
                    </div>

                    <div class="relative group cursor-pointer border-2 border-dashed border-white/5 rounded-3xl p-8 text-center hover:border-gold-primary/30 hover:bg-white/[0.02] transition-all">
                        <input name="image" type="file" id="galleryImageInput" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                        <div class="flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white/10 group-hover:text-gold-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p id="uploadLabel" class="text-[10px] text-white/30 font-bold uppercase tracking-widest text-center">Ganti Gambar</p>
                        </div>
                    </div>
                    <p class="mt-4 text-[10px] text-center text-white/20 leading-relaxed italic">Biarkan kosong jika tidak ingin mengganti foto.</p>
                </div>

                <button type="submit"
                    class="w-full rounded-[2rem] bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
                    PERBARUI FOTO
                </button>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('galleryImageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
