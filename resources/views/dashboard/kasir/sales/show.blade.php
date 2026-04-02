@extends('layouts.kasir')
@section('title', 'Invoice ' . $sale->invoice_no)

@section('body')
<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Invoice {{ $sale->invoice_no }}</h1>
            <p class="text-sm text-white/60">
                Detail pesanan dan status pembayaran
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('kasir.sales.create') }}"
               class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm hover:bg-white/10">
                Transaksi Baru
            </a>

            @if ($sale->payment_status === 'paid')
                <a href="{{ route('kasir.sales.print', $sale) }}"
                   target="_blank"
                   class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
                    Cetak Invoice
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-[1fr_.9fr]">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                <div>
                    <div class="text-white/50">Invoice</div>
                    <div class="mt-1 font-semibold">{{ $sale->invoice_no }}</div>
                </div>
                <div>
                    <div class="text-white/50">Jenis Pesanan</div>
                    <div class="mt-1 font-semibold">{{ strtoupper(str_replace('_', ' ', $sale->order_type)) }}</div>
                </div>
                <div>
                    <div class="text-white/50">Meja</div>
                    <div class="mt-1 font-semibold">{{ $sale->diningTable?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-white/50">Tanggal</div>
                    <div class="mt-1 font-semibold">{{ $sale->created_at?->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="text-white/50">Jam Pesan</div>
                    <div class="mt-1 font-semibold">{{ $sale->created_at?->format('H:i:s') }}</div>
                </div>
                <div>
                    <div class="text-white/50">Jam Bayar</div>
                    <div class="mt-1 font-semibold">{{ $sale->paid_at?->format('H:i:s') ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @foreach ($sale->items as $item)
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-semibold">{{ $item->product->name ?? '-' }}</div>
                                <div class="mt-1 text-sm text-white/60">
                                    Rp {{ number_format((int) $item->price, 0, ',', '.') }} × {{ (int) $item->qty }}
                                </div>
                                @if ($item->note)
                                    <div class="mt-1 text-xs text-white/50">Catatan: {{ $item->note }}</div>
                                @endif
                            </div>
                            <div class="font-semibold">
                                Rp {{ number_format((int) $item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="text-sm font-semibold">Status Pembayaran</div>

            <div class="mt-4 rounded-2xl border border-white/10 bg-black/20 p-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-white/60">Metode</span>
                    <span class="font-semibold uppercase">{{ $sale->payment_method }}</span>
                </div>

                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-white/60">Status</span>
                    <span id="paymentStatusBadge" class="font-semibold">
                        {{ strtoupper($sale->payment_status) }}
                    </span>
                </div>

                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-white/60">Total</span>
                    <span class="font-semibold">Rp {{ number_format((int) $sale->total_amount, 0, ',', '.') }}</span>
                </div>

                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-white/60">Dibayar</span>
                    <span class="font-semibold">Rp {{ number_format((int) $sale->paid_amount, 0, ',', '.') }}</span>
                </div>

                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-white/60">Kembalian</span>
                    <span class="font-semibold">Rp {{ number_format((int) $sale->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($sale->payment_status === 'pending')
                <div class="mt-4 rounded-2xl border border-yellow-500/20 bg-yellow-500/10 p-4">
                    <div class="text-sm font-semibold text-yellow-300">Menunggu Pembayaran</div>

                    @if (($payment['payment_type'] ?? null) === 'qris' && !empty($payment['qr_url']))
                        <div class="mt-3 text-xs text-white/70">Scan QRIS berikut untuk membayar:</div>
                        <img src="{{ $payment['qr_url'] }}" alt="QRIS"
                             class="mt-3 w-full max-w-[280px] rounded-2xl bg-white p-3">
                    @endif

                    @if (!empty($payment['va_number']))
                        <div class="mt-4 text-xs text-white/70">Nomor VA</div>
                        <div class="mt-2 rounded-xl border border-white/10 bg-black/20 px-4 py-3 text-lg font-semibold tracking-wider">
                            {{ $payment['va_number'] }}
                        </div>
                        <div class="mt-2 text-xs text-white/60">
                            Bank: {{ strtoupper($payment['bank'] ?? '-') }}
                        </div>
                    @endif

                    @if (!empty($payment['expires_at']))
                        <div class="mt-4 text-xs text-white/60">
                            Batas pembayaran: <span id="expiresAtText">{{ $payment['expires_at'] }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@if ($sale->payment_status === 'pending' && in_array($sale->payment_method, ['qris','bca_va','bni_va','bri_va','permata_va']))
<script>
(function () {
    const badge = document.getElementById('paymentStatusBadge');

    const poll = setInterval(async () => {
        try {
            const res = await fetch("{{ route('kasir.sales.payment-status', $sale) }}", {
                headers: { 'Accept': 'application/json' }
            });

            const data = await res.json();

            if (badge) {
                badge.textContent = String(data.payment_status || '').toUpperCase();
            }

            if (data.payment_status === 'paid') {
                clearInterval(poll);
                window.location.reload();
            }

            if (['expired', 'failed'].includes(data.payment_status)) {
                clearInterval(poll);
                window.location.reload();
            }
        } catch (e) {}
    }, 5000);
})();
</script>
@endif
@endsection