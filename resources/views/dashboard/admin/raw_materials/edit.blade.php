@extends('layouts.admin')
@section('title','Edit Bahan Baku')

@section('body')
  <div class="flex items-start justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
        ☰
      </button>

      <div>
        <h1 class="text-xl font-semibold">Edit Bahan Baku</h1>
        <p class="text-sm text-white/70">Perbarui stok, minimum stok, unit, dan harga default.</p>
      </div>
    </div>

    <a href="{{ route('admin.raw_materials.index') }}"
      class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
      ← Kembali
    </a>
  </div>

  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-red-200/30 bg-red-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.raw_materials.update', $material) }}"
    class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.15fr_.85fr]">
    @csrf
    @method('PUT')

    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
      <div class="flex items-center justify-between gap-3">
        <div>
          <div class="text-sm font-semibold">Informasi Bahan</div>
          <div class="text-xs text-white/60">Perubahan stok akan dicatat sebagai adjustment.</div>
        </div>

        <span class="hidden sm:inline-flex rounded-xl border border-yellow-500/20 bg-yellow-500/10 px-3 py-2 text-xs text-yellow-200">
          EDIT
        </span>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
          <label class="text-sm text-white/80">Nama</label>
          <input
            name="name"
            value="{{ old('name', $material->name) }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-yellow-500/35"
            required
          />
        </div>

        <div>
          <label class="text-sm text-white/80">Unit</label>
          <input
            name="unit"
            value="{{ old('unit', $material->unit) }}"
            list="unit_list"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-yellow-500/35"
            required
          />
          <datalist id="unit_list">
            <option value="kg"></option>
            <option value="gram"></option>
            <option value="liter"></option>
            <option value="ml"></option>
            <option value="pcs"></option>
            <option value="pack"></option>
            <option value="box"></option>
            <option value="tabung"></option>
            <option value="galon"></option>
          </datalist>
        </div>

        <div>
          <label class="text-sm text-white/80">Harga Default / Unit</label>
          <input
            name="default_cost"
            type="number"
            min="0"
            step="0.01"
            value="{{ old('default_cost', $material->default_cost) }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-yellow-500/35"
          />
        </div>

        <div>
          <label class="text-sm text-white/80">Stok Saat Ini</label>
          <input
            name="stock_on_hand"
            type="number"
            min="0"
            step="0.01"
            value="{{ old('stock_on_hand', $material->stock_on_hand) }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-yellow-500/35"
            required
          />
        </div>

        <div>
          <label class="text-sm text-white/80">Minimum Stock</label>
          <input
            name="min_stock"
            type="number"
            min="0"
            step="0.01"
            value="{{ old('min_stock', $material->min_stock) }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-yellow-500/35"
          />
        </div>
      </div>

      <div class="mt-6 flex items-center justify-end gap-2">
        <a href="{{ route('admin.raw_materials.index') }}"
          class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
          Batal
        </a>

        <button
          class="rounded-xl bg-yellow-500 px-5 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
          Simpan Perubahan
        </button>
      </div>
    </div>

    <aside class="space-y-5">
      <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
        <div class="text-sm font-semibold">Status Saat Ini</div>

        <div class="mt-4 space-y-3 text-sm">
          <div class="flex items-center justify-between gap-3">
            <span class="text-white/65">Nama</span>
            <span class="font-semibold">{{ $material->name }}</span>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-white/65">Stok</span>
            <span class="font-semibold">{{ $material->stock_on_hand }} {{ $material->unit }}</span>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-white/65">Min Stock</span>
            <span class="font-semibold">{{ $material->min_stock ?? 0 }} {{ $material->unit }}</span>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-white/65">Default Cost</span>
            <span class="font-semibold">Rp {{ number_format((float) ($material->default_cost ?? 0), 0, ',', '.') }}</span>
          </div>
        </div>

        @if((float) $material->stock_on_hand <= (float) ($material->min_stock ?? 0))
          <div class="mt-4 rounded-2xl border border-red-400/25 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            ⚠ Bahan ini sedang berada di batas minimum stok atau di bawahnya.
          </div>
        @else
          <div class="mt-4 rounded-2xl border border-emerald-400/25 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            ✓ Stok bahan ini masih dalam kondisi aman.
          </div>
        @endif
      </div>
    </aside>
  </form>
@endsection