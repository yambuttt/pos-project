@extends('layouts.admin')
@section('title','Resep Produk')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Resep / BOM</h1>
        <p class="text-sm text-white/70">Produk: <b>{{ $product->name }}</b></p>
      </div>
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.products.index') }}"
        class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">← Kembali</a>
      <a href="{{ route('admin.products.edit', $product->id) }}"
        class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">Edit Produk</a>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-emerald-200/30 bg-emerald-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-red-200/30 bg-red-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.1fr_.9fr]">
    {{-- LEFT: list resep --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-sm font-semibold">Item Resep</div>
          <div class="text-xs text-white/60">Qty per 1 porsi</div>
        </div>
        <div class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs">
          Estimasi max: <b>{{ $product->maxServingsFromStock() }}</b> porsi
        </div>
      </div>

      <div class="mt-4 overflow-hidden rounded-2xl border border-white/15">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-white/10 text-xs text-white/70">
              <tr>
                <th class="px-4 py-3">Bahan</th>
                <th class="px-4 py-3">Unit</th>
                <th class="px-4 py-3">Qty / porsi</th>
                <th class="px-4 py-3">Stok saat ini</th>
                <th class="px-4 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              @forelse($product->recipes as $r)
                <tr class="hover:bg-white/5">
                  <td class="px-4 py-3 font-semibold">{{ $r->rawMaterial?->name }}</td>
                  <td class="px-4 py-3 text-white/70">{{ $r->rawMaterial?->unit }}</td>
                  <td class="px-4 py-3 font-semibold">{{ number_format((float)$r->qty, 3, '.', '') }}</td>
                  <td class="px-4 py-3 text-white/80">
                    {{ number_format((float)($r->rawMaterial?->stock_on_hand ?? 0), 3, '.', '') }}
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex justify-end">
                      <form method="POST" action="{{ route('admin.products.recipes.destroy', [$product->id, $r->id]) }}"
                        onsubmit="return confirm('Hapus item resep ini?')">
                        @csrf @method('DELETE')
                        <button class="rounded-xl border border-red-200/30 bg-red-500/10 px-3 py-2 text-xs hover:bg-red-500/15">
                          Hapus
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-4 py-8 text-center text-white/60">
                    Belum ada resep. Tambahkan di form sebelah kanan.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-white/15 bg-white/10 p-4 text-sm text-white/70">
        <b>Catatan:</b> Resep ini akan dipakai untuk STRICT stock check saat transaksi kasir.
      </div>
    </div>

    {{-- RIGHT: form tambah resep --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="text-sm font-semibold">Tambah / Update Resep</div>
      <p class="mt-1 text-sm text-white/70">Kalau bahan sudah ada, akan di-update qty-nya.</p>

      <form method="POST" action="{{ route('admin.products.recipes.store', $product->id) }}" class="mt-4 space-y-4">
        @csrf
        <div>
          <label class="text-sm text-white/80">Bahan</label>
          <select name="raw_material_id"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
            <option value="">Pilih bahan...</option>
            @foreach($materials as $m)
              <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unit }})</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-sm text-white/80">Qty per 1 porsi</label>
          <input name="qty" type="number" step="0.001" min="0.001" value="{{ old('qty') }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40"
            placeholder="contoh: 18 (gram), 200 (ml), 1 (pcs)">
        </div>

        <div>
          <label class="text-sm text-white/80">Note (opsional)</label>
          <input name="note" value="{{ old('note') }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
            placeholder="contoh: espresso shot, susu full cream, dll">
        </div>

        <button
          class="w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
          Simpan Resep
        </button>
      </form>
    </div>
  </div>
@endsection
