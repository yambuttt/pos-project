@extends('layouts.admin')
@section('title', 'Edit Bahan Baku')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Edit Bahan Baku</h1>
      <p class="text-sm text-white/40 font-medium">Perbarui informasi stok, harga, atau kategori untuk <span class="text-gold-primary font-bold">{{ $material->name }}</span>.</p>
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

  <form method="POST" action="{{ route('admin.raw_materials.update', $material) }}" class="max-w-4xl">
    @csrf
    @method('PUT')

    <div class="glass-panel p-8 rounded-[2.5rem] space-y-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- NAMA BAHAN -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Nama Bahan Baku</label>
          <input name="name" value="{{ old('name', $material->name) }}" placeholder="Contoh: Biji Kopi Arabica" required
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>

        <!-- SATUAN UNIT -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Satuan Unit</label>
          <input name="unit" value="{{ old('unit', $material->unit) }}" placeholder="Contoh: gram, ml, pcs, kg" required
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>

        <!-- STOK SAAT INI -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Stok Saat Ini</label>
          <input name="stock_on_hand" type="number" step="0.001" value="{{ old('stock_on_hand', $material->stock_on_hand) }}"
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>

        <!-- MINIMUM STOK -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Minimum Stok (Alert)</label>
          <input name="min_stock" type="number" step="0.001" value="{{ old('min_stock', $material->min_stock) }}"
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>

        <!-- HARGA DEFAULT -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Harga Beli per Unit (Rp)</label>
          <div class="relative">
            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-sm font-bold text-gold-primary">Rp</span>
            <input name="default_cost" type="number" min="0" value="{{ old('default_cost', $material->default_cost) }}"
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] pl-14 pr-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>
        </div>

        <!-- KATEGORI -->
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Kategori Bahan</label>
          <input name="category" value="{{ old('category', $material->category) }}" placeholder="Contoh: Coffee, Dairy, Syrup"
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>
      </div>

      <div class="pt-4">
        <button
          class="w-full rounded-[2rem] bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
          Perbarui Informasi Bahan
        </button>
      </div>
    </div>
  </form>
@endsection