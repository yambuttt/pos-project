@extends('layouts.admin')
@section('title','Produk')

@section('body')
  <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-yellow-500/20 bg-white/[0.04] px-3 py-2 text-sm text-white backdrop-blur-xl hover:bg-white/[0.08] lg:hidden">
        ☰
      </button>

      <div>
        <h1 class="text-xl font-semibold text-white">Produk</h1>
        <p class="text-sm text-white/65">Master menu resto/coffee + resep (BOM)</p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <a href="{{ route('admin.products.create') }}"
        class="rounded-xl bg-blue-600/90 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 hover:bg-blue-500/90">
        + Tambah Produk
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100 backdrop-blur-xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  <section class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
      <div class="text-xs uppercase tracking-[0.16em] text-white/45">Total Produk</div>
      <div class="mt-2 text-3xl font-semibold text-white">{{ $totalProducts }}</div>
      <div class="mt-2 text-sm text-white/55">Semua menu terdaftar</div>
    </div>

    <div class="rounded-[24px] border border-emerald-400/16 bg-emerald-500/10 p-5 backdrop-blur-xl">
      <div class="text-xs uppercase tracking-[0.16em] text-emerald-100/70">Aktif</div>
      <div class="mt-2 text-3xl font-semibold text-emerald-50">{{ $activeProducts }}</div>
      <div class="mt-2 text-sm text-emerald-100/70">Siap tampil di sistem</div>
    </div>

    <div class="rounded-[24px] border border-rose-400/16 bg-rose-500/10 p-5 backdrop-blur-xl">
      <div class="text-xs uppercase tracking-[0.16em] text-rose-100/70">Nonaktif</div>
      <div class="mt-2 text-3xl font-semibold text-rose-50">{{ $inactiveProducts }}</div>
      <div class="mt-2 text-sm text-rose-100/70">Perlu review / disembunyikan</div>
    </div>

    <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
      <div class="text-xs uppercase tracking-[0.16em] text-white/45">Rata-rata Harga</div>
      <div class="mt-2 text-3xl font-semibold text-white">Rp {{ number_format($avgPrice, 0, ',', '.') }}</div>
      <div class="mt-2 text-sm text-white/55">Estimasi harga menu</div>
    </div>
  </section>

  <section class="mt-5 rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-4 backdrop-blur-xl sm:p-5">
    <form method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
      <div class="grid w-full gap-3 md:grid-cols-[1fr_220px] lg:max-w-3xl">
        <input
          type="text"
          name="q"
          value="{{ $q }}"
          placeholder="Cari nama produk, SKU, atau kategori..."
          class="w-full rounded-xl border border-yellow-500/14 bg-white/[0.03] px-4 py-3 text-sm text-white outline-none placeholder:text-white/30 focus:border-yellow-500/35"
        >

        <select
          name="status"
          class="w-full rounded-xl border border-yellow-500/14 bg-white/[0.03] px-4 py-3 text-sm text-white outline-none focus:border-yellow-500/35">
          <option value="">Semua Status</option>
          <option value="active" @selected($status === 'active')>Active</option>
          <option value="inactive" @selected($status === 'inactive')>Inactive</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <button
          class="rounded-xl bg-white/[0.05] px-4 py-3 text-sm font-semibold text-white hover:bg-white/[0.09]">
          Cari
        </button>

        @if($q || $status)
          <a href="{{ route('admin.products.index') }}"
            class="rounded-xl border border-yellow-500/14 bg-white/[0.03] px-4 py-3 text-sm font-semibold text-white/80 hover:bg-white/[0.06]">
            Reset
          </a>
        @endif
      </div>
    </form>
  </section>

  {{-- Mobile / Tablet cards --}}
  <section class="mt-5 grid grid-cols-1 gap-4 xl:hidden">
    @forelse($products as $p)
      @php
        $maxServings = $p->maxServingsFromStock();
      @endphp

      <div class="rounded-[26px] border border-yellow-500/14 bg-[#121212]/90 p-5 backdrop-blur-xl">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="truncate text-lg font-semibold text-white">{{ $p->name }}</div>
            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-white/55">
              <span>{{ $p->sku ?: 'Tanpa SKU' }}</span>
              <span>•</span>
              <span>{{ $p->category ?: 'Tanpa kategori' }}</span>
            </div>
          </div>

          <span class="shrink-0 rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em]
            {{ $p->is_active
              ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-200'
              : 'border-rose-400/20 bg-rose-500/10 text-rose-200' }}">
            {{ $p->is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-3">
          <div class="rounded-2xl border border-yellow-500/10 bg-white/[0.03] p-4">
            <div class="text-[11px] uppercase tracking-[0.14em] text-white/40">Harga</div>
            <div class="mt-2 text-base font-semibold text-white">Rp {{ number_format($p->price,0,',','.') }}</div>
          </div>

          <div class="rounded-2xl border border-yellow-500/10 bg-white/[0.03] p-4">
            <div class="text-[11px] uppercase tracking-[0.14em] text-white/40">Estimasi Max Porsi</div>
            <div class="mt-2 text-base font-semibold text-white">{{ $maxServings }} porsi</div>
            <div class="text-xs text-white/50">berdasarkan stok bahan</div>
          </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
          <a href="{{ route('admin.products.recipes', $p->id) }}"
            class="rounded-xl border border-yellow-500/16 bg-white/[0.03] px-3 py-2 text-xs font-semibold text-white/85 hover:bg-white/[0.06]">
            Resep
          </a>

          <a href="{{ route('admin.products.edit', $p->id) }}"
            class="rounded-xl border border-yellow-500/16 bg-white/[0.03] px-3 py-2 text-xs font-semibold text-white/85 hover:bg-white/[0.06]">
            Edit
          </a>

          <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}"
            onsubmit="return confirm('Hapus produk ini?')">
            @csrf
            @method('DELETE')
            <button
              class="rounded-xl border border-rose-300/20 bg-rose-500/10 px-3 py-2 text-xs font-semibold text-rose-100 hover:bg-rose-500/15">
              Hapus
            </button>
          </form>
        </div>
      </div>
    @empty
      <div class="rounded-[26px] border border-yellow-500/16 bg-[#121212]/90 px-6 py-10 text-center backdrop-blur-xl">
        <div class="text-lg font-semibold text-white">Belum ada produk</div>
        <p class="mt-2 text-sm text-white/55">Tambahkan produk pertama untuk mulai mengelola menu.</p>
      </div>
    @endforelse
  </section>

  {{-- Desktop table --}}
  <section class="mt-5 hidden xl:block rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
    <div class="overflow-hidden rounded-2xl border border-yellow-500/10">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1100px] text-left text-sm">
          <thead class="bg-white/[0.04] text-xs uppercase tracking-[0.14em] text-white/45">
            <tr>
              <th class="px-4 py-4">Produk</th>
              <th class="px-4 py-4">Kategori</th>
              <th class="px-4 py-4">Harga</th>
              <th class="px-4 py-4">Status</th>
              <th class="px-4 py-4">Estimasi Max Porsi</th>
              <th class="px-4 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-yellow-500/8">
            @forelse($products as $p)
              <tr class="hover:bg-white/[0.03]">
                <td class="px-4 py-4">
                  <div class="font-semibold text-white">{{ $p->name }}</div>
                  <div class="mt-1 text-xs text-white/45">{{ $p->sku ?: 'Tanpa SKU' }}</div>
                </td>

                <td class="px-4 py-4 text-white/80">{{ $p->category ?: '-' }}</td>

                <td class="px-4 py-4 font-semibold text-white">
                  Rp {{ number_format($p->price,0,',','.') }}
                </td>

                <td class="px-4 py-4">
                  <span class="rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em]
                    {{ $p->is_active
                      ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-200'
                      : 'border-rose-400/20 bg-rose-500/10 text-rose-200' }}">
                    {{ $p->is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>

                <td class="px-4 py-4 text-white/80">
                  <div class="font-medium text-white">{{ $p->maxServingsFromStock() }} porsi</div>
                  <div class="mt-1 text-xs text-white/45">berdasarkan stok bahan & resep</div>
                </td>

                <td class="px-4 py-4">
                  <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.products.recipes', $p->id) }}"
                      class="rounded-xl border border-yellow-500/16 bg-white/[0.03] px-3 py-2 text-xs font-semibold text-white/85 hover:bg-white/[0.06]">
                      Resep
                    </a>

                    <a href="{{ route('admin.products.edit', $p->id) }}"
                      class="rounded-xl border border-yellow-500/16 bg-white/[0.03] px-3 py-2 text-xs font-semibold text-white/85 hover:bg-white/[0.06]">
                      Edit
                    </a>

                    <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}"
                      onsubmit="return confirm('Hapus produk ini?')">
                      @csrf
                      @method('DELETE')
                      <button
                        class="rounded-xl border border-rose-300/20 bg-rose-500/10 px-3 py-2 text-xs font-semibold text-rose-100 hover:bg-rose-500/15">
                        Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-10 text-center text-white/50">
                  Belum ada produk.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <div class="mt-6">
    {{ $products->links() }}
  </div>
@endsection