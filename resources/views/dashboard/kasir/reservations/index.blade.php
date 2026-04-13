@extends('layouts.kasir')
@section('title','Reservasi (Kasir)')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div>
      <div class="text-xl font-semibold">Reservasi</div>
      <div class="text-sm text-white/60">Check-in & checkout reservasi harian</div>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-xl border border-green-300/20 bg-green-500/10 px-4 py-3 text-sm">
      ✅ {{ session('success') }}
    </div>
  @endif
  @if($errors->any())
    <div class="mt-4 rounded-xl border border-red-300/20 bg-red-500/10 px-4 py-3 text-sm whitespace-pre-line">
      ❌ {{ $errors->first() }}
    </div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-5 sm:p-6">
    <form class="flex flex-wrap items-end gap-2" method="GET">
      <div>
        <div class="text-xs text-white/60">Tanggal</div>
        <input type="date" name="date" value="{{ $date }}"
          class="mt-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm outline-none">
      </div>
      <div>
        <div class="text-xs text-white/60">Cari</div>
        <input name="q" value="{{ $q }}" placeholder="kode/nama/HP"
          class="mt-2 w-64 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm outline-none placeholder:text-white/30">
      </div>
      <button class="rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
        Filter
      </button>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
          <thead class="bg-white/5 text-xs text-white/60">
            <tr>
              <th class="px-4 py-3">Jam</th>
              <th class="px-4 py-3">Kode</th>
              <th class="px-4 py-3">Customer</th>
              <th class="px-4 py-3">Resource</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Total</th>
              <th class="px-4 py-3">Sisa</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($rows as $r)
              @php $remaining = max(0, (int)$r->grand_total - (int)$r->paid_amount); @endphp
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3 text-white/80">{{ $r->start_at->format('H:i') }}–{{ $r->end_at->format('H:i') }}</td>
                <td class="px-4 py-3 font-semibold">
                  <a class="underline decoration-white/20 hover:decoration-white/60"
                     href="{{ route('kasir.reservations.show', $r) }}">{{ $r->code }}</a>
                </td>
                <td class="px-4 py-3 text-white/80">
                  <div class="font-semibold">{{ $r->customer_name }}</div>
                  <div class="text-xs text-white/50">{{ $r->customer_phone }}</div>
                </td>
                <td class="px-4 py-3 text-white/80">{{ $r->resource?->name }}</td>
                <td class="px-4 py-3">{{ strtoupper($r->status) }}</td>
                <td class="px-4 py-3 font-semibold">Rp {{ number_format($r->grand_total,0,',','.') }}</td>
                <td class="px-4 py-3">Rp {{ number_format($remaining,0,',','.') }}</td>
              </tr>
            @empty
              <tr><td colspan="7" class="px-4 py-8 text-center text-white/60">Tidak ada reservasi aktif.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
  </div>
@endsection