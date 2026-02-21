@extends('layouts.admin')
@section('title','Tambah Produk')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Tambah Produk</h1>
        <p class="text-sm text-white/70">Buat produk dulu, lanjut isi resepnya</p>
      </div>
    </div>
    <a href="{{ route('admin.products.index') }}"
      class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">← Kembali</a>
  </div>

  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-red-200/30 bg-red-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data"
    class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
    @csrf

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      <div>
        <label class="text-sm text-white/80">Nama Produk</label>
        <input name="name" value="{{ old('name') }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
      </div>

      <div>
        <label class="text-sm text-white/80">Kategori</label>
        <input name="category" value="{{ old('category') }}" placeholder="Coffee / Non Coffee / Food"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40">
      </div>

      <div>
        <label class="text-sm text-white/80">SKU (opsional)</label>
        <input name="sku" value="{{ old('sku') }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
      </div>

      <div>
        <label class="text-sm text-white/80">Harga (Rp)</label>
        <input name="price" type="number" min="0" value="{{ old('price', 0) }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
      </div>

      <div class="sm:col-span-2">
        <label class="text-sm text-white/80">Deskripsi (opsional)</label>
        <textarea name="description" rows="3" placeholder="Contoh: Bakso sapi + kuah gurih..."
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40">{{ old('description') }}</textarea>
      </div>

      <div class="sm:col-span-2">
        <label class="text-sm text-white/80">Gambar Produk (opsional)</label>
        <input name="image" type="file" accept="image/*"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
        <p class="mt-2 text-xs text-white/50">Disarankan: JPG/PNG/WEBP, max 2MB.</p>
      </div>
    </div>

    <div class="mt-4 flex items-center gap-2">
      <input id="is_active" name="is_active" type="checkbox" value="1" checked
        class="h-4 w-4 rounded border-white/30 bg-white/10">
      <label for="is_active" class="text-sm text-white/80">Aktif</label>
    </div>

    <button
      class="mt-5 w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
      Simpan & Isi Resep
    </button>
  </form>
@endsection