@extends('layouts.kasir')
@section('title','Detail Reservasi ' . $reservation->code)

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div>
      <div class="text-xl font-semibold">Reservasi {{ $reservation->code }}</div>
      <div class="text-sm text-white/60">Operasional kasir: check-in & pelunasan</div>
    </div>
    <a href="{{ route('kasir.reservations.index') }}"
      class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm hover:bg-white/10">
      ← Kembali
    </a>
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

  <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1fr_.9fr]">
    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
      <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
        <div>
          <div class="text-white/50">Customer</div>
          <div class="mt-1 font-semibold">{{ $reservation->customer_name }}</div>
          <div class="text-xs text-white/60">{{ $reservation->customer_phone }}</div>
        </div>
        <div>
          <div class="text-white/50">Resource</div>
          <div class="mt-1 font-semibold">{{ $reservation->resource?->name ?? '-' }}</div>
        </div>
        <div>
          <div class="text-white/50">Waktu</div>
          <div class="mt-1 font-semibold">{{ $reservation->start_at->format('d M Y H:i') }}</div>
          <div class="text-xs text-white/60">{{ $reservation->end_at->format('d M Y H:i') }}</div>
        </div>
        <div>
          <div class="text-white/50">Status</div>
          <div class="mt-1 font-semibold">{{ strtoupper($reservation->status) }}</div>
        </div>
        <div>
          <div class="text-white/50">Total</div>
          <div class="mt-1 font-semibold">Rp {{ number_format($reservation->grand_total,0,',','.') }}</div>
        </div>
        <div>
          <div class="text-white/50">Sisa</div>
          <div class="mt-1 font-semibold text-yellow-300">Rp {{ number_format($remaining,0,',','.') }}</div>
        </div>
      </div>

      <div class="mt-6 space-y-2">
        @foreach($reservation->items as $it)
          <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm">
            <div class="flex justify-between gap-3">
              <div class="font-semibold">{{ $it->snapshot_name }}</div>
              <div class="text-white/70">x{{ $it->qty }}</div>
            </div>
            <div class="mt-2 text-white/70">Rp {{ number_format($it->subtotal,0,',','.') }}</div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
      <div class="text-sm font-semibold">Aksi</div>

      @if($reservation->status === 'confirmed')
        <form method="POST" action="{{ route('kasir.reservations.check_in', $reservation) }}" class="mt-4">
          @csrf
          <button class="w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
            Check-in
          </button>
        </form>
      @endif

      @if($reservation->status === 'checked_in')
        <form method="POST" action="{{ route('kasir.reservations.checkout', $reservation) }}" class="mt-4 space-y-3">
          @csrf

          <div>
            <div class="text-xs text-white/60">Metode</div>
            <select name="method"
              class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm outline-none">
              <option value="CASH">CASH</option>
              <option value="QRIS">QRIS</option>
            </select>
          </div>

          <div>
            <div class="text-xs text-white/60">Jumlah (harus pas)</div>
            <input type="number" name="amount" min="1" value="{{ $remaining }}"
              class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm outline-none">
          </div>

          <div>
            <div class="text-xs text-white/60">Reference (opsional)</div>
            <input name="reference"
              class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm outline-none placeholder:text-white/30"
              placeholder="contoh: nomor referensi QRIS">
          </div>

          <button class="w-full rounded-xl bg-emerald-500/90 px-5 py-3 text-sm font-semibold text-black hover:bg-emerald-400/90">
            Checkout & Selesaikan
          </button>
        </form>
      @endif

      @if($reservation->menu_type === 'REGULAR')
        <div class="mt-6 text-xs text-white/60">
          Catatan: Saat checkout, sistem akan consume bahan yang sudah di-lock untuk reservasi REGULAR.
        </div>
      @endif
    </div>
  </div>
@endsection