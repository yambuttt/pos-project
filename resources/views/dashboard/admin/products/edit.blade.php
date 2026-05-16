@extends('layouts.admin')
@section('title','Edit Produk')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Edit Produk</h1>
      <p class="text-sm text-white/40 font-medium">Perbarui informasi detail, harga, atau status ketersediaan produk Anda.</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.products.recipes', $product->id) }}"
          class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-sm font-bold text-gold-primary border border-gold-primary/20 hover:bg-gold-primary/5 transition-all active:scale-95">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          MANAJEMEN RESEP
        </a>
        <a href="{{ route('admin.products.index') }}"
          class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-sm font-bold text-white border border-white/5 hover:bg-white/10 transition-all active:scale-95">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          KEMBALI
        </a>
    </div>
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

  <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- MAIN DETAILS -->
      <div class="lg:col-span-2 glass-panel p-8 rounded-[2.5rem] space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Nama Produk</label>
            <input name="name" value="{{ old('name', $product->name) }}" placeholder="Contoh: Espresso Martini"
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>

          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Kategori</label>
            <div class="relative">
              <select name="category"
                class="w-full appearance-none rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
                <option value="" class="bg-black">Pilih kategori...</option>
                <option value="Makanan" {{ old('category', $product->category) === 'Makanan' ? 'selected' : '' }} class="bg-black">Makanan</option>
                <option value="Minuman" {{ old('category', $product->category) === 'Minuman' ? 'selected' : '' }} class="bg-black">Minuman</option>
                <option value="Snacks" {{ old('category', $product->category) === 'Snacks' ? 'selected' : '' }} class="bg-black">Snacks</option>
              </select>
              <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-6 top-1/2 -translate-y-1/2 h-4 w-4 text-white/20 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">SKU (Opsional)</label>
            <input name="sku" value="{{ old('sku', $product->sku) }}" placeholder="PRD-001"
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>

          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Harga Jual (Rp)</label>
            <div class="relative">
              <span class="absolute left-6 top-1/2 -translate-y-1/2 text-sm font-bold text-gold-primary">Rp</span>
              <input name="price" type="number" min="0" value="{{ old('price', $product->price) }}"
                class="w-full rounded-2xl border border-white/5 bg-white/[0.02] pl-14 pr-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
            </div>
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Deskripsi Produk</label>
          <textarea name="description" rows="5" placeholder="Gambarkan cita rasa atau keunikan produk ini..."
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">{{ old('description', $product->description) }}</textarea>
        </div>
      </div>

      <!-- SIDEBAR OPTIONS -->
      <div class="space-y-8">
        <!-- IMAGE PREVIEW & UPLOAD -->
        <div class="glass-panel p-8 rounded-[2.5rem]">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1 mb-4 block">Foto Produk</label>
          
          <div id="imagePreviewContainer" class="{{ $product->image_path ? '' : 'hidden' }} mb-6 relative group animate-fade-in">
            <img id="imagePreview" src="{{ $product->image_path ? asset('storage/'.$product->image_path) : '#' }}" alt="Product Image"
              class="w-full aspect-square rounded-3xl object-cover border border-white/10 shadow-2xl">
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-3xl flex items-center justify-center">
               <p id="previewStatusLabel" class="text-[10px] font-bold text-white uppercase tracking-widest">Gambar Saat Ini</p>
            </div>
          </div>

          <div class="relative group cursor-pointer border-2 border-dashed border-white/5 rounded-3xl p-8 text-center hover:border-gold-primary/30 hover:bg-white/[0.02] transition-all">
            <input name="image" type="file" id="productImageInput" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
            <div class="flex flex-col items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white/10 group-hover:text-gold-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p id="uploadLabel" class="text-[10px] text-white/30 font-bold uppercase tracking-widest text-center">Ganti Media</p>
            </div>
          </div>
          <p class="mt-4 text-[10px] text-center text-white/20 leading-relaxed italic">JPG, PNG, atau WEBP (Maksimal 2MB)</p>
        </div>

        <script>
          document.getElementById('productImageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');
            const statusLabel = document.getElementById('previewStatusLabel');
            const uploadLabel = document.getElementById('uploadLabel');
            
            if (file) {
              const reader = new FileReader();
              reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
                if (statusLabel) statusLabel.innerText = 'Preview Gambar Baru';
                if (uploadLabel) uploadLabel.innerText = 'Ganti Pilihan';
              }
              reader.readAsDataURL(file);
            }
          });
        </script>

        <!-- STATUS -->
        <div class="glass-panel p-8 rounded-[2.5rem]">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1 mb-4 block">Status Ketersediaan</label>
          <label class="flex items-center justify-between p-4 glass-card rounded-2xl cursor-pointer hover:bg-white/5 transition-all">
            <span class="text-sm font-bold text-white/80">Tampilkan di Menu</span>
            <div class="relative inline-flex items-center cursor-pointer">
              <input name="is_active" type="checkbox" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="sr-only peer">
              <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold-primary"></div>
            </div>
          </label>
        </div>

        <button
          class="w-full rounded-[2rem] bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
          Simpan Perubahan
        </button>
      </div>
    </div>
  </form>
@endsection