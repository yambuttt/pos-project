@extends('layouts.admin')
@section('title','Edit Produk')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Edit Produk</h1>
        <p class="text-sm text-white/70">{{ $product->name }}</p>
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

  <form method="POST" action="{{ route('admin.products.update', $product->id) }}"
    class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      <div>
        <label class="text-sm text-white/80">Nama Produk</label>
        <input name="name" value="{{ old('name', $product->name) }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
      </div>

      <div>
        <label class="text-sm text-white/80">Kategori</label>
        <input name="category" value="{{ old('category', $product->category) }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40">
      </div>

      <div>
        <label class="text-sm text-white/80">SKU</label>
        <input name="sku" value="{{ old('sku', $product->sku) }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
      </div>

      <div>
        <label class="text-sm text-white/80">Harga (Rp)</label>
        <input name="price" type="number" min="0" value="{{ old('price', $product->price) }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
      </div>
    </div>

    <div class="mt-4 flex items-center gap-2">
      <input id="is_active" name="is_active" type="checkbox" value="1" {{ $product->is_active ? 'checked' : '' }}
        class="h-4 w-4 rounded border-white/30 bg-white/10">
      <label for="is_active" class="text-sm text-white/80">Aktif</label>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
      <button
        class="w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
        Simpan
      </button>

      <a href="{{ route('admin.products.recipes', $product->id) }}"
        class="w-full rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-center backdrop-blur-xl hover:bg-white/15">
        Atur Resep
      </a>
    </div>
  </form>
@endsection
