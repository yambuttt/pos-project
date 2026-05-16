@extends('layouts.admin')
@section('title','Produk')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Master Produk</h1>
      <p class="text-sm text-white/40 font-medium">Kelola menu restaurant, harga, dan resep (BOM) dalam satu panel eksklusif.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.products.create') }}"
        class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Produk Baru
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

  <!-- STATS -->
  <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4 mb-8">
    <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
      <div class="absolute -right-4 -top-4 w-24 h-24 bg-gold-primary/5 rounded-full blur-2xl group-hover:bg-gold-primary/10 transition-colors"></div>
      <p class="text-white/40 text-[10px] uppercase tracking-widest mb-1 font-bold">Total Produk</p>
      <h3 class="text-2xl font-bold text-white">{{ $totalProducts }}</h3>
      <p class="text-white/20 text-[10px]">Semua menu terdaftar</p>
    </div>

    <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
      <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-500/5 rounded-full blur-2xl group-hover:bg-green-500/10 transition-colors"></div>
      <p class="text-white/40 text-[10px] uppercase tracking-widest mb-1 font-bold">Menu Aktif</p>
      <h3 class="text-2xl font-bold text-green-400">{{ $activeProducts }}</h3>
      <p class="text-white/20 text-[10px]">Siap tampil di sistem</p>
    </div>

    <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
      <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/5 rounded-full blur-2xl group-hover:bg-red-500/10 transition-colors"></div>
      <p class="text-white/40 text-[10px] uppercase tracking-widest mb-1 font-bold">Non-Aktif</p>
      <h3 class="text-2xl font-bold text-red-400">{{ $inactiveProducts }}</h3>
      <p class="text-white/20 text-[10px]">Menunggu review</p>
    </div>

    <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
      <div class="absolute -right-4 -top-4 w-24 h-24 bg-gold-primary/5 rounded-full blur-2xl group-hover:bg-gold-primary/10 transition-colors"></div>
      <p class="text-white/40 text-[10px] uppercase tracking-widest mb-1 font-bold">Rata-rata Harga</p>
      <h3 class="text-2xl font-bold text-gold-gradient">Rp {{ number_format($avgPrice, 0, ',', '.') }}</h3>
      <p class="text-white/20 text-[10px]">Estimasi market value</p>
    </div>
  </section>

  <!-- SEARCH & FILTER -->
  <section class="glass-panel p-6 rounded-[2rem] mb-8">
    <form method="GET" class="flex flex-col gap-4 lg:flex-row lg:items-center">
      <div class="flex-1 flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative group">
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-white/20 group-focus-within:text-gold-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            type="text"
            name="q"
            value="{{ $q }}"
            placeholder="Cari nama produk, SKU, atau kategori..."
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] pl-12 pr-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all"
          >
        </div>

        <div class="w-full md:w-[240px] relative">
          <select
            name="status"
            class="w-full appearance-none rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
            <option value="">Semua Status</option>
            <option value="active" @selected($status === 'active')>Active</option>
            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
          </select>
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-4 top-1/2 -translate-y-1/2 h-4 w-4 text-white/20 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button class="flex-1 lg:flex-none rounded-2xl bg-white/5 px-8 py-3.5 text-xs font-black text-white hover:bg-white/10 hover:border-gold-primary/30 transition-all active:scale-95 border border-white/10 uppercase tracking-widest">
          Cari Menu
        </button>

        @if($q || $status)
          <a href="{{ route('admin.products.index') }}"
            class="flex-1 lg:flex-none rounded-2xl bg-red-500/5 px-8 py-3.5 text-xs font-black text-red-500 hover:bg-red-500/10 transition-all active:scale-95 border border-red-500/20 uppercase tracking-widest text-center">
            Reset
          </a>
        @endif
      </div>
    </form>
  </section>

  <!-- PRODUCT LIST -->
  <section class="glass-panel rounded-[2.5rem] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="text-[10px] text-white/30 uppercase tracking-widest border-b border-white/5">
            <th class="px-8 py-6 font-bold">Produk & SKU</th>
            <th class="px-8 py-6 font-bold">Kategori</th>
            <th class="px-8 py-6 font-bold">Harga Jual</th>
            <th class="px-8 py-6 font-bold">Status</th>
            <th class="px-8 py-6 font-bold">Potensi Stok</th>
            <th class="px-8 py-6 font-bold text-right">Manajemen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @forelse($products as $p)
            <tr class="group hover:bg-white/[0.02] transition-colors">
              <td class="px-8 py-6">
                <div class="font-bold text-white group-hover:text-gold-primary transition-colors">{{ $p->name }}</div>
                <div class="text-[10px] text-white/30 font-medium uppercase tracking-tight">{{ $p->sku ?: 'No SKU' }}</div>
              </td>

              <td class="px-8 py-6 text-sm text-white/60">{{ $p->category ?: 'General' }}</td>

              <td class="px-8 py-6">
                <span class="text-sm font-bold text-white">Rp {{ number_format($p->price,0,',','.') }}</span>
              </td>

              <td class="px-8 py-6">
                <span class="inline-flex px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest border {{ $p->is_active ? 'border-green-500/20 bg-green-500/10 text-green-400' : 'border-red-500/20 bg-red-500/10 text-red-400' }}">
                  {{ $p->is_active ? 'ACTIVE' : 'INACTIVE' }}
                </span>
              </td>

              <td class="px-8 py-6">
                <div class="text-sm font-bold text-white/80">{{ $p->maxServingsFromStock() }} <span class="text-[10px] text-white/40">Porsi</span></div>
                <div class="text-[9px] text-white/30 italic">Berdasarkan resep & bahan</div>
              </td>

              <td class="px-8 py-6">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('admin.products.recipes', $p->id) }}"
                    class="p-2.5 rounded-xl bg-white/5 text-white/40 hover:text-gold-primary hover:bg-gold-primary/10 transition-all"
                    title="Resep (BOM)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                  </a>

                  <a href="{{ route('admin.products.edit', $p->id) }}"
                    class="p-2.5 rounded-xl bg-white/5 text-white/40 hover:text-blue-400 hover:bg-blue-400/10 transition-all"
                    title="Edit Produk">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </a>

                  <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}"
                    onsubmit="return confirm('Hapus produk ini secara permanen?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                      class="p-2.5 rounded-xl bg-white/5 text-white/40 hover:text-red-500 hover:bg-red-500/10 transition-all"
                      title="Hapus Produk">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-8 py-20 text-center">
                <div class="flex flex-col items-center gap-4">
                  <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center text-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                  </div>
                  <p class="text-white/40 font-bold italic">Belum ada produk yang terdaftar</p>
                  <a href="{{ route('admin.products.create') }}" class="text-gold-primary font-bold hover:underline">Tambah produk sekarang</a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <div class="mt-8 px-4">
    {{ $products->links() }}
  </div>
@endsection
