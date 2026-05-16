@extends('layouts.admin')
@section('title', 'Tambah Bahan Baku')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Tambah Bahan Baku</h1>
      <p class="text-sm text-white/40 font-medium">Daftarkan bahan baku baru untuk manajemen stok dan resep produk.</p>
    </div>

    <a href="{{ route('admin.raw_materials.index') }}"
      class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-xs font-black text-white border border-white/10 hover:bg-white/10 transition-all active:scale-95 uppercase tracking-widest">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali
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

  <form method="POST" action="{{ route('admin.raw_materials.store') }}" class="max-w-4xl">
    @csrf

    <div class="glass-panel p-8 rounded-[2.5rem] space-y-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- NAMA BAHAN -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Nama Bahan Baku</label>
          <input name="name" value="{{ old('name') }}" placeholder="Contoh: Biji Kopi Arabica" required
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>

        <!-- SATUAN UNIT -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Satuan Unit</label>
          <input name="unit" value="{{ old('unit') }}" placeholder="Contoh: gram, ml, pcs, kg" required
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>

        <!-- STOK AWAL -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Stok Saat Ini</label>
          <input name="stock_on_hand" type="number" step="0.001" value="{{ old('stock_on_hand', 0) }}"
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>

        <!-- MINIMUM STOK -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Minimum Stok (Alert)</label>
          <input name="min_stock" type="number" step="0.001" value="{{ old('min_stock', 0) }}"
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>

        <!-- HARGA DEFAULT -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Harga Beli per Unit (Rp)</label>
          <div class="relative">
            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-sm font-bold text-gold-primary">Rp</span>
            <input name="default_cost" type="number" min="0" value="{{ old('default_cost', 0) }}"
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] pl-14 pr-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>
        </div>

        <!-- KATEGORI -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Kategori Bahan</label>
          <input name="category" value="{{ old('category') }}" placeholder="Contoh: Coffee, Dairy, Syrup"
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>
      </div>

      <div class="pt-4">
        <button
          class="w-full rounded-[2rem] bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
          Daftarkan Bahan Baku
        </button>
      </div>
    </div>
  </form>

  <div class="mt-12 p-6 rounded-2xl border border-gold-primary/10 bg-gold-primary/5 flex items-start gap-4 max-w-4xl">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gold-primary shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div>
       <p class="text-xs font-black text-white/80 mb-1 uppercase tracking-widest">Pentingnya Manajemen Bahan</p>
       <p class="text-xs text-white/40 leading-relaxed font-medium">Informasi stok dan harga ini akan digunakan untuk menghitung COGS (Harga Pokok Penjualan) secara akurat. Pastikan satuan unit konsisten dengan yang digunakan pada resep produk.</p>
    </div>
  </div>
@endsection