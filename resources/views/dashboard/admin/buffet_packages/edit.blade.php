@extends('layouts.admin')
@section('title','Edit Paket Buffet')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Edit Paket Buffet</h1>
        <p class="text-sm text-white/70">{{ $pkg->name }}</p>
      </div>
    </div>
    <a href="{{ route('admin.buffet_packages.index') }}"
      class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold hover:bg-white/10">← Kembali</a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-500/10 px-4 py-3 text-sm">✅ {{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-red-300/20 bg-red-500/10 px-4 py-3 text-sm">❌ {{ $errors->first() }}</div>
  @endif

  <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1fr_.9fr]">
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <form method="POST" action="{{ route('admin.buffet_packages.update', $pkg) }}" class="space-y-4">
        @csrf @method('PUT')

        <div>
          <div class="text-sm text-white/70">Nama Paket</div>
          <input name="name" value="{{ $pkg->name }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none" required>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <div class="text-sm text-white/70">Pricing Type</div>
            <select name="pricing_type" class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
              <option value="per_pax" @selected($pkg->pricing_type==='per_pax')>per_pax</option>
              <option value="per_event" @selected($pkg->pricing_type==='per_event')>per_event</option>
            </select>
          </div>
          <div>
            <div class="text-sm text-white/70">Harga (Rp)</div>
            <input type="number" name="price" min="0" value="{{ $pkg->price }}"
              class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none" required>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <div class="text-sm text-white/70">Min Pax (opsional)</div>
            <input type="number" name="min_pax" min="1" value="{{ $pkg->min_pax }}"
              class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
          </div>
          <label class="flex items-center gap-2 text-sm mt-7">
            <input type="checkbox" name="is_active" value="1" @checked($pkg->is_active)>
            <span class="text-white/80">Aktif</span>
          </label>
        </div>

        <div>
          <div class="text-sm text-white/70">Notes</div>
          <textarea name="notes" rows="3"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">{{ $pkg->notes }}</textarea>
        </div>

        <button class="w-full rounded-2xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
          Update Paket
        </button>
      </form>
    </div>

    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="font-semibold">Isi Paket</div>
      <div class="mt-2 text-xs text-white/60">Tambahkan menu reguler apa saja yang “termasuk” dalam paket.</div>

      <form method="POST" action="{{ route('admin.buffet_packages.items.store', $pkg) }}" class="mt-4 space-y-3">
        @csrf
        <div>
          <div class="text-xs text-white/60">Produk</div>
          <select name="product_id" class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
            @foreach($products as $p)
              <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price,0,',','.') }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <div class="text-xs text-white/60">Qty</div>
          <input type="number" name="qty" min="1" value="1"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
        </div>
        <div>
          <div class="text-xs text-white/60">Note (opsional)</div>
          <input name="note"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
        </div>
        <button class="w-full rounded-xl bg-emerald-500/90 px-5 py-3 text-sm font-semibold text-black hover:bg-emerald-400/90">
          Tambah / Update Item
        </button>
      </form>

      <div class="mt-5 overflow-hidden rounded-2xl border border-white/15">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[520px] text-left text-sm">
            <thead class="bg-white/10 text-xs text-white/70">
              <tr>
                <th class="px-4 py-3">Produk</th>
                <th class="px-4 py-3">Qty</th>
                <th class="px-4 py-3">Note</th>
                <th class="px-4 py-3"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              @forelse($pkg->items as $it)
                <tr class="hover:bg-white/5">
                  <td class="px-4 py-3 font-semibold">{{ $it->product?->name }}</td>
                  <td class="px-4 py-3">{{ $it->qty }}</td>
                  <td class="px-4 py-3">{{ $it->note }}</td>
                  <td class="px-4 py-3 text-right">
                    <form method="POST" action="{{ route('admin.buffet_packages.items.destroy', [$pkg, $it]) }}"
                      onsubmit="return confirm('Hapus item ini?')">
                      @csrf @method('DELETE')
                      <button class="rounded-xl border border-red-300/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100 hover:bg-red-500/15">
                        Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-white/60">Belum ada item.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection