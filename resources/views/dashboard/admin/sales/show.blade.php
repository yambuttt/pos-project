@extends('layouts.admin')
@section('title', 'Detail Transaksi')

@section('body')
<div class="flex items-center justify-between gap-3">
  <div class="flex items-center gap-3">
    <button id="openMobileSidebar" type="button"
      class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
    <div>
      <h1 class="text-xl font-semibold">Detail Transaksi</h1>
      <p class="text-sm text-white/70">
        Invoice: <b>{{ $sale->invoice_no ?? ('#'.$sale->id) }}</b>
      </p>
    </div>
  </div>

  <a href="{{ route('admin.sales.index') }}"
    class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
    ← Kembali
  </a>
</div>

<div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.2fr_0.8fr]">
  {{-- LEFT: Items --}}
  <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm font-semibold">Items</div>
        <div class="text-xs text-white/60">Detail item yang dibeli</div>
      </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Produk</th>
              <th class="px-4 py-3">Qty</th>
              <th class="px-4 py-3">Harga</th>
              <th class="px-4 py-3">Subtotal</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($sale->items ?? [] as $it)
              <tr>
                <td class="px-4 py-3">
                  <div class="font-medium">{{ $it->product->name ?? '-' }}</div>
                  <div class="text-xs text-white/60">{{ $it->product->category ?? '' }}</div>
                </td>
                <td class="px-4 py-3 font-semibold">{{ $it->qty ?? 0 }}</td>
                <td class="px-4 py-3">Rp {{ number_format($it->price ?? 0, 0, ',', '.') }}</td>
                <td class="px-4 py-3 font-semibold">Rp {{ number_format($it->subtotal ?? 0, 0, ',', '.') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-4 py-6 text-white/70">Item belum ada.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- RIGHT: Info --}}
  <div class="space-y-5">
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="text-sm font-semibold">Kasir</div>
      <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 p-4">
        <div class="font-semibold">{{ $sale->cashier->name ?? '-' }}</div>
        <div class="text-xs text-white/60">{{ $sale->cashier->email ?? '' }}</div>
        <div class="mt-2 text-xs text-white/60">Waktu: <b class="text-white/80">{{ $sale->created_at?->format('d M Y H:i') }}</b></div>
      </div>
    </div>

    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="text-sm font-semibold">Pembayaran</div>

      <div class="mt-3 grid grid-cols-1 gap-2">
        <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
          <div class="text-sm text-white/70">Total</div>
          <div class="text-sm font-semibold">Rp {{ number_format($sale->total_amount ?? 0, 0, ',', '.') }}</div>
        </div>

        <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
          <div class="text-sm text-white/70">Bayar</div>
          <div class="text-sm font-semibold">Rp {{ number_format($sale->paid_amount ?? 0, 0, ',', '.') }}</div>
        </div>

        <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
          <div class="text-sm text-white/70">Kembalian</div>
          <div class="text-sm font-semibold">Rp {{ number_format($sale->change_amount ?? 0, 0, ',', '.') }}</div>
        </div>
      </div>
    </div>

    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="text-sm font-semibold">Catatan</div>
      <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm text-white/80">
        {{ $sale->note ?? '-' }}
      </div>
    </div>
  </div>
</div>
@endsection
