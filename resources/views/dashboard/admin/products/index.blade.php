@extends('layouts.admin')
@section('title','Produk')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Produk</h1>
        <p class="text-sm text-white/70">Master menu resto/coffee + resep (BOM)</p>
      </div>
    </div>

    <a href="{{ route('admin.products.create') }}"
      class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
      + Tambah Produk
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-emerald-200/30 bg-emerald-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <div class="overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[920px] text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Kategori</th>
              <th class="px-4 py-3">Harga</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Estimasi Max Porsi</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($products as $p)
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3 font-semibold">{{ $p->name }}</td>
                <td class="px-4 py-3 text-white/80">{{ $p->category ?? '-' }}</td>
                <td class="px-4 py-3 font-semibold">Rp {{ number_format($p->price,0,',','.') }}</td>
                <td class="px-4 py-3">
                  <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs">
                    {{ $p->is_active ? 'ACTIVE' : 'INACTIVE' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-white/80">
                  {{ $p->maxServingsFromStock() }} porsi
                  <div class="text-xs text-white/60">(butuh resep)</div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.products.recipes', $p->id) }}"
                      class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs hover:bg-white/15">Resep</a>
                    <a href="{{ route('admin.products.edit', $p->id) }}"
                      class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs hover:bg-white/15">Edit</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}"
                      onsubmit="return confirm('Hapus produk ini?')">
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
                <td colspan="6" class="px-4 py-8 text-center text-white/60">Belum ada produk.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($products instanceof \Illuminate\Pagination\Paginator)
      <div class="mt-4">{{ $products->links() }}</div>
    @endif
  </div>
@endsection
