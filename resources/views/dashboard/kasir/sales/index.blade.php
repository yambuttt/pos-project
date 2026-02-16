@extends('layouts.kasir')
@section('title','Riwayat Transaksi')

@section('body')
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-xl font-semibold">Riwayat Transaksi</h1>
      <p class="text-sm text-white/60">Transaksi milik akun kasir ini</p>
    </div>
    <a href="{{ route('kasir.sales.create') }}" class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
      + Transaksi Baru
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-emerald-200/20 bg-emerald-500/10 px-4 py-3 text-sm">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-5 sm:p-6">
    <div class="overflow-hidden rounded-2xl border border-white/10">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
          <thead class="bg-white/5 text-xs text-white/60">
            <tr>
              <th class="px-4 py-3">Waktu</th>
              <th class="px-4 py-3">Invoice</th>
              <th class="px-4 py-3">Total</th>
              <th class="px-4 py-3">Bayar</th>
              <th class="px-4 py-3">Kembali</th>
              <th class="px-4 py-3">Items</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($sales as $s)
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3 text-white/80">{{ $s->created_at->format('d M Y H:i') }}</td>
                <td class="px-4 py-3 font-semibold">{{ $s->invoice_no }}</td>
                <td class="px-4 py-3 font-semibold">Rp {{ number_format($s->total_amount,0,',','.') }}</td>
                <td class="px-4 py-3">Rp {{ number_format($s->paid_amount,0,',','.') }}</td>
                <td class="px-4 py-3">Rp {{ number_format($s->change_amount,0,',','.') }}</td>
                <td class="px-4 py-3 text-white/70">
                  @foreach($s->items as $it)
                    <div class="text-xs">{{ $it->product?->name }} x{{ $it->qty }}</div>
                  @endforeach
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="px-4 py-8 text-center text-white/60">Belum ada transaksi.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">{{ $sales->links() }}</div>
  </div>
@endsection
