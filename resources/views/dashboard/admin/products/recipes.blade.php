@extends('layouts.admin')
@section('title', 'Resep Produk')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient text-shadow-glow">Resep & BOM</h1>
      <p class="text-sm text-white/40 font-medium">Manajemen Bill of Materials (BOM) untuk <span class="text-gold-primary font-bold">{{ $product->name }}</span></p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.products.edit', $product->id) }}"
          class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-xs font-black text-gold-primary border border-gold-primary/30 hover:bg-gold-primary/10 transition-all active:scale-95 uppercase tracking-widest">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          Edit Produk
        </a>
        <a href="{{ route('admin.products.index') }}"
          class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-xs font-black text-white border border-white/10 hover:bg-white/10 transition-all active:scale-95 uppercase tracking-widest">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Kembali
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

  <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
    <!-- LEFT: COMPOSITION LIST -->
    <div class="lg:col-span-2 glass-panel p-8 rounded-[2.5rem]">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h4 class="text-lg font-bold">Komposisi Bahan</h4>
          <p class="text-xs text-white/40">Daftar bahan baku yang digunakan untuk 1 porsi produk.</p>
        </div>
        <div class="px-4 py-2 bg-gold-primary/10 border border-gold-primary/20 rounded-xl">
          <p class="text-[10px] text-gold-primary font-bold uppercase tracking-widest text-center">Estimasi Maksimal</p>
          <p class="text-lg font-bold text-white text-center">{{ $product->maxServingsFromStock() }} <span class="text-xs text-white/40 font-normal">Porsi</span></p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="text-[10px] text-white/30 uppercase tracking-widest border-b border-white/5">
              <th class="pb-6 font-bold">Bahan Baku</th>
              <th class="pb-6 font-bold">Takaran / Porsi</th>
              <th class="pb-6 font-bold text-center">Stok Gudang</th>
              <th class="pb-6 font-bold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            @forelse($product->recipes as $r)
              <tr class="group hover:bg-white/[0.02] transition-colors">
                <td class="py-6">
                  <div class="font-bold text-white group-hover:text-gold-primary transition-colors">{{ $r->rawMaterial?->name }}</div>
                  <div class="text-[10px] text-white/30 font-medium uppercase tracking-tight">Kategori: {{ $r->rawMaterial?->category ?: '-' }}</div>
                </td>
                <td class="py-6">
                  <span class="text-sm font-bold text-white">{{ number_format((float) $r->qty, 3, '.', '') }}</span>
                  <span class="text-[10px] text-white/40 font-bold uppercase ml-1">{{ $r->rawMaterial?->unit }}</span>
                </td>
                <td class="py-6 text-center">
                  <span class="text-sm font-medium text-white/60">{{ number_format((float) ($r->rawMaterial?->stock_on_hand ?? 0), 3, '.', '') }}</span>
                  <span class="text-[10px] text-white/30 ml-1">{{ $r->rawMaterial?->unit }}</span>
                </td>
                <td class="py-6 text-right">
                  <form method="POST" action="{{ route('admin.products.recipes.destroy', [$product->id, $r->id]) }}"
                    onsubmit="return confirm('Hapus item resep ini?')" class="inline">
                    @csrf @method('DELETE')
                    <button
                      class="p-2.5 rounded-xl bg-white/5 text-white/40 hover:text-red-500 hover:bg-red-500/10 transition-all">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="py-12 text-center text-white/20 italic">Belum ada bahan baku yang didaftarkan untuk resep ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-8 p-6 rounded-2xl border border-blue-500/10 bg-blue-500/5 flex items-start gap-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
           <p class="text-xs font-bold text-white/80 mb-1 uppercase tracking-widest">Informasi Inventori</p>
           <p class="text-xs text-white/50 leading-relaxed">Resep ini akan digunakan oleh sistem untuk menghitung pengurangan stok secara otomatis saat transaksi kasir dilakukan. Pastikan takaran akurat untuk menghindari selisih stok.</p>
        </div>
      </div>
    </div>

    <!-- RIGHT: ADD MATERIAL FORM -->
    <div class="glass-panel p-8 rounded-[2.5rem]">
      <h4 class="text-lg font-bold mb-2">Kelola Komposisi</h4>
      <p class="text-xs text-white/40 mb-8 font-medium">Tambah bahan baru atau update takaran yang sudah ada.</p>

      <form method="POST" action="{{ route('admin.products.recipes.store', $product->id) }}" class="space-y-6">
        @csrf
        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Pilih Bahan Baku</label>
          <div class="relative">
            <select name="raw_material_id"
              class="w-full appearance-none rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
              <option value="" class="bg-black">Pilih bahan...</option>
              @foreach($materials as $m)
                <option value="{{ $m->id }}" class="bg-black">
                  {{ $m->name }} ({{ $m->unit }})
                </option>
              @endforeach
            </select>
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-6 top-1/2 -translate-y-1/2 h-4 w-4 text-white/20 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Takaran / Porsi</label>
          <div class="relative group">
            <input name="qty" type="number" step="0.001" min="0.001" value="{{ old('qty') }}"
              placeholder="Contoh: 0.25 (kg) atau 200 (ml)"
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Catatan (Opsional)</label>
          <input name="note" value="{{ old('note') }}"
            placeholder="Contoh: Topping, Base Liquid, dll"
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
        </div>

        <button
          class="w-full rounded-2xl bg-gradient-to-r from-blue-600 via-blue-600 to-blue-700 px-6 py-5 text-xs font-black text-white uppercase tracking-widest shadow-xl shadow-blue-600/20 hover:shadow-[0_0_20px_rgba(37,99,235,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-blue-400/20">
          Simpan Komposisi
        </button>
      </form>
    </div>
  </div>
@endsection