@extends('layouts.admin')
@section('title', 'Bahan Baku')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Inventory Bahan Baku</h1>
      <p class="text-sm text-white/40 font-medium">Kelola stok, minimum stok, dan harga default bahan baku restaurant.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.raw_materials.create') }}"
        class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Bahan Baku
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

  <!-- QUICK STATS -->
  <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-10">
    <div class="premium-card p-6 border-white/5 relative overflow-hidden group">
      <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
      </div>
      <p class="text-[10px] uppercase tracking-widest text-white/40 font-bold mb-1">Total Bahan</p>
      <h3 class="text-3xl font-black text-white leading-tight">{{ $totalMaterials }}</h3>
      <p class="text-xs text-white/20 mt-1 font-medium">Item bahan baku terdaftar</p>
    </div>

    <div class="premium-card p-6 border-red-500/10 bg-red-500/5 relative overflow-hidden group">
      <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <p class="text-[10px] uppercase tracking-widest text-red-400 font-bold mb-1">Low Stock</p>
      <h3 class="text-3xl font-black text-red-100 leading-tight">{{ $lowStockCount }}</h3>
      <p class="text-xs text-red-100/30 mt-1 font-medium">Item perlu segera restok</p>
    </div>

    <div class="premium-card p-6 border-gold-primary/10 relative overflow-hidden group">
      <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform text-gold-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <p class="text-[10px] uppercase tracking-widest text-gold-primary/60 font-bold mb-1">Estimasi Nilai Stok</p>
      <h3 class="text-3xl font-black text-white leading-tight">Rp {{ number_format((float) $totalStockValue, 0, ',', '.') }}</h3>
      <p class="text-xs text-white/20 mt-1 font-medium">Berdasarkan harga unit</p>
    </div>
  </div>

  <!-- SEARCH SECTION -->
  <section class="glass-panel p-6 rounded-[2rem] mb-8">
    <form method="GET" class="flex flex-col gap-4 lg:flex-row lg:items-center">
      <div class="flex-1 relative">
        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-6 top-1/2 -translate-y-1/2 h-5 w-5 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input name="q" value="{{ $q }}" placeholder="Cari nama bahan atau unit..."
          class="w-full rounded-2xl border border-white/5 bg-white/[0.02] pl-14 pr-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
      </div>

      <div class="flex items-center gap-3">
        <button class="flex-1 lg:flex-none rounded-2xl bg-white/5 px-8 py-4 text-xs font-black text-white hover:bg-white/10 hover:border-gold-primary/30 transition-all active:scale-95 border border-white/10 uppercase tracking-widest">
          Cari Bahan
        </button>

        @if($q)
          <a href="{{ route('admin.raw_materials.index') }}"
            class="flex-1 lg:flex-none rounded-2xl bg-red-500/5 px-8 py-4 text-xs font-black text-red-500 hover:bg-red-500/10 transition-all active:scale-95 border border-red-500/20 uppercase tracking-widest text-center">
            Reset
          </a>
        @endif
      </div>
    </form>
  </section>

  <!-- MATERIAL GRID -->
  <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
    @forelse($materials as $m)
      @php
        $isLow = (float) $m->stock_on_hand <= (float) ($m->min_stock ?? 0);
        $stockPercent = ((float) ($m->min_stock ?? 0) > 0)
          ? min(100, max(8, ((float) $m->stock_on_hand / (float) $m->min_stock) * 100))
          : 100;
      @endphp

      <div class="premium-card p-6 border-white/5 group hover:border-gold-primary/20">
        <div class="flex items-start justify-between gap-4 mb-6">
          <div class="space-y-1">
            <div class="flex items-center gap-3">
              <h2 class="text-xl font-bold text-white group-hover:text-gold-primary transition-colors">{{ $m->name }}</h2>
              @if($isLow)
                <span class="px-2 py-0.5 rounded-md bg-red-500/20 border border-red-500/30 text-[9px] font-black text-red-400 uppercase tracking-widest">Low Stock</span>
              @else
                <span class="px-2 py-0.5 rounded-md bg-green-500/20 border border-green-500/30 text-[9px] font-black text-green-400 uppercase tracking-widest">Stok Aman</span>
              @endif
            </div>
            <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold">Satuan Unit: <span class="text-gold-primary">{{ $m->unit }}</span></p>
          </div>

          <div class="flex items-center gap-2">
            <a href="{{ route('admin.raw_materials.edit', $m) }}"
              class="p-2.5 rounded-xl bg-white/5 text-white/40 hover:text-gold-primary hover:bg-gold-primary/10 transition-all border border-white/5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </a>
            <form method="POST" action="{{ route('admin.raw_materials.destroy', $m) }}"
              onsubmit="return confirm('Hapus bahan baku {{ $m->name }}?')" class="inline">
              @csrf @method('DELETE')
              <button class="p-2.5 rounded-xl bg-white/5 text-white/40 hover:text-red-500 hover:bg-red-500/10 transition-all border border-white/5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </form>
          </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
          <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5">
            <p class="text-[9px] uppercase tracking-widest text-white/30 font-bold mb-1">Stok Saat Ini</p>
            <p class="text-lg font-black text-white">{{ $m->stock_on_hand }} <span class="text-[10px] text-white/30">{{ $m->unit }}</span></p>
          </div>
          <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5">
            <p class="text-[9px] uppercase tracking-widest text-white/30 font-bold mb-1">Minimum Stok</p>
            <p class="text-lg font-black text-white">{{ $m->min_stock ?? 0 }} <span class="text-[10px] text-white/30">{{ $m->unit }}</span></p>
          </div>
          <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5">
            <p class="text-[9px] uppercase tracking-widest text-white/30 font-bold mb-1">Harga Satuan</p>
            <p class="text-lg font-black text-white">Rp {{ number_format((float) ($m->default_cost ?? 0), 0, ',', '.') }}</p>
          </div>
          <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5">
            <p class="text-[9px] uppercase tracking-widest text-white/30 font-bold mb-1">Nilai Inventori</p>
            <p class="text-lg font-black text-white">Rp {{ number_format((float) $m->stock_on_hand * (float) ($m->default_cost ?? 0), 0, ',', '.') }}</p>
          </div>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <p class="text-[10px] uppercase tracking-widest text-white/30 font-black">Visualisasi Stok</p>
            <p class="text-[10px] font-black uppercase {{ $isLow ? 'text-red-500' : 'text-green-500' }}">
              {{ $isLow ? 'Kritis' : 'Optimasi Aman' }}
            </p>
          </div>
          <div class="h-2 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
            <div class="h-full rounded-full transition-all duration-1000 {{ $isLow ? 'bg-gradient-to-r from-red-600 to-orange-500' : 'bg-gradient-to-r from-gold-dark to-gold-primary' }}"
              style="width: {{ $stockPercent }}%">
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-span-full premium-card py-20 border-white/5 text-center">
        <div class="flex flex-col items-center gap-4">
          <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-white/10 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white">Gudang Bahan Kosong</h3>
          <p class="text-sm text-white/30 max-w-xs mx-auto">Anda belum mendaftarkan bahan baku apa pun. Mulai kelola inventory Anda sekarang.</p>
          <a href="{{ route('admin.raw_materials.create') }}" class="mt-4 text-gold-primary font-black uppercase tracking-[0.2em] text-[10px] hover:text-white transition-colors">
            Tambah Bahan Sekarang →
          </a>
        </div>
      </div>
    @endforelse
  </div>

  <div class="mt-10">
    {{ $materials->links() }}
  </div>
@endsection