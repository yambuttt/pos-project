<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ayo Renne — Invoice {{ $sale->invoice_no }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(234, 179, 8, .08), transparent 24%),
                radial-gradient(circle at bottom right, rgba(234, 179, 8, .06), transparent 22%),
                linear-gradient(180deg, #030303 0%, #0a0a0a 100%);
        }

        .panel-dark {
            background: rgba(20, 20, 20, 0.82);
            backdrop-filter: blur(14px);
        }

        .gold-border {
            border-color: rgba(234, 179, 8, 0.16);
        }

        .gold-soft {
            background: linear-gradient(135deg, rgba(234, 179, 8, .10), rgba(255, 255, 255, .02));
        }

        .status-pending {
            color: rgb(250 204 21);
        }

        .status-paid {
            color: rgb(134 239 172);
        }

        .status-failed {
            color: rgb(252 165 165);
        }
    </style>
</head>

<body class="min-h-screen text-white">
    <div class="relative min-h-screen overflow-hidden">
        <div
            class="pointer-events-none absolute -left-24 top-0 h-[360px] w-[360px] rounded-full bg-yellow-500/10 blur-[120px]">
        </div>
        <div
            class="pointer-events-none absolute bottom-0 right-0 h-[420px] w-[420px] rounded-full bg-yellow-400/8 blur-[140px]">
        </div>

        <div class="mx-auto max-w-6xl px-4 pb-16 pt-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-[0.24em] text-yellow-500">Ayo Renne</div>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">Invoice Pembayaran</h1>
                    <p class="mt-2 text-sm leading-7 text-white/65 sm:text-base">
                        Selesaikan pembayaran QRIS agar pesanan diproses ke kitchen.
                    </p>
                </div>

                <a href="/"
                    class="inline-flex items-center rounded-xl border border-yellow-500/16 bg-white/[0.03] px-4 py-2.5 text-sm font-semibold text-white/90 backdrop-blur-xl hover:bg-white/[0.06]">
                    Kembali
                </a>
            </div>

            <div class="grid gap-5 xl:grid-cols-[1.1fr_.9fr]">
                <!-- LEFT -->
                <div class="space-y-5">
                    <div class="panel-dark gold-border rounded-[28px] border p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <div class="text-xs uppercase tracking-[0.22em] text-white/55">Invoice</div>
                                <div class="mt-2 text-2xl font-semibold text-white">{{ $sale->invoice_no }}</div>
                                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                                    <span class="text-white/55">Status Pembayaran:</span>
                                    <span id="paymentStatus" class="font-semibold
                                            @if(($sale->payment_status ?? '') === 'paid') status-paid
                                            @elseif(in_array(($sale->payment_status ?? ''), ['expired', 'failed', 'cancelled'])) status-failed
                                            @else status-pending @endif">
                                        {{ strtoupper($sale->payment_status ?? 'pending') }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="rounded-2xl border border-yellow-500/12 bg-white/[0.03] px-4 py-3 text-sm text-white/80">
                                <div>Pesanan: {{ strtoupper($sale->order_type) }}</div>
                                <div class="mt-1">Metode: {{ strtoupper($sale->payment_method) }}</div>
                                @if($sale->diningTable)
                                    <div class="mt-1">Meja: {{ $sale->diningTable->name }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-yellow-500/10 bg-white/[0.03] px-4 py-4">
                                <div class="text-xs uppercase tracking-[0.18em] text-white/45">Total</div>
                                <div class="mt-2 text-xl font-semibold text-white">Rp
                                    {{ number_format($sale->total_amount, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-yellow-500/10 bg-white/[0.03] px-4 py-4">
                                <div class="text-xs uppercase tracking-[0.18em] text-white/45">Berlaku Sampai</div>
                                <div id="expiresAt" class="mt-2 text-base font-semibold text-white">
                                    {{ $payment['expires_at'] ?? optional($sale->payment_expires_at)?->format('Y-m-d H:i:s') ?? '-' }}
                                </div>
                            </div>

                            <div id="countdownCard"
                                class="{{ ($sale->payment_status ?? 'pending') === 'paid' ? 'hidden ' : '' }}rounded-2xl border border-yellow-500/10 bg-white/[0.03] px-4 py-4">
                                <div class="text-xs uppercase tracking-[0.18em] text-white/45">Sisa Waktu</div>
                                <div id="countdown" class="mt-2 text-xl font-semibold text-yellow-400">--:--</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-dark gold-border rounded-[28px] border p-5 sm:p-6">
                        <div class="text-sm font-semibold text-white">Item Pesanan</div>

                        <div class="mt-4 space-y-3">
                            @foreach($sale->items as $item)
                                <div class="rounded-2xl border border-yellow-500/10 bg-white/[0.03] px-4 py-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-white">{{ $item->product->name ?? '-' }}
                                            </div>
                                            <div class="mt-1 text-xs text-white/55">
                                                Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->qty }}
                                            </div>
                                            @if($item->note)
                                                <div class="mt-2 text-xs text-white/60">Catatan: {{ $item->note }}</div>
                                            @endif
                                        </div>

                                        <div class="shrink-0 text-sm font-semibold text-yellow-500">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="space-y-5">
                    <div class="panel-dark gold-border rounded-[28px] border p-5 sm:p-6">
                        <div class="text-sm font-semibold text-white">Instruksi Pembayaran</div>
                        <div class="mt-2 text-sm leading-7 text-white/65">
                            Scan QRIS di bawah ini untuk menyelesaikan pembayaran.
                        </div>

                        <div id="qrWrapper" class="mt-5 rounded-[24px] border border-yellow-500/10 gold-soft p-5">
                            @if(!empty($payment['qr_url']))
                                <div class="flex justify-center">
                                    <img id="qrImage" src="{{ $payment['qr_url'] }}" alt="QRIS"
                                        class="h-auto w-full max-w-[320px] rounded-[24px] bg-white p-3 shadow-xl">
                                </div>
                            @else
                                <div
                                    class="rounded-2xl border border-white/10 bg-black/20 px-6 py-10 text-center text-sm text-white/55">
                                    QR belum tersedia.
                                </div>
                            @endif

                            <div id="pendingHelp" class="mt-4 text-center text-sm text-white/60">
                                Menunggu pembayaran dari aplikasi QRIS.
                            </div>
                        </div>

                        <div id="paidNotice"
                            class="mt-4 hidden rounded-2xl border border-green-400/20 bg-green-500/10 px-4 py-4 text-center text-sm font-semibold text-green-200">
                            Pembayaran berhasil. Pesanan sedang diproses oleh kitchen.
                        </div>

                        <div id="expiredNotice"
                            class="mt-4 hidden rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-4 text-center text-sm font-semibold text-red-200">
                            Pembayaran dibatalkan / kadaluarsa. QRIS sudah tidak berlaku.
                        </div>
                    </div>

                    <div class="panel-dark gold-border rounded-[28px] border p-5 sm:p-6">
                        <div class="text-sm font-semibold text-white">Ringkasan</div>

                        <div class="mt-4 space-y-3 text-sm">
                            @php
                                $subtotal = $sale->items->sum('subtotal');
                                $tax = max(0, (int) $sale->total_amount - (int) $subtotal - (int) ($sale->delivery_fee ?? 0));
                            @endphp

                            <div class="flex items-center justify-between">
                                <span class="text-white/60">Subtotal</span>
                                <span class="font-semibold text-white">Rp
                                    {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-white/60">Pajak</span>
                                <span class="font-semibold text-white">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                            </div>

                            @if(($sale->delivery_fee ?? 0) > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-white/60">Ongkir</span>
                                    <span class="font-semibold text-white">Rp
                                        {{ number_format($sale->delivery_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="border-t border-yellow-500/10 pt-3 flex items-center justify-between">
                                <span class="text-white/70">Total</span>
                                <span class="text-lg font-semibold text-yellow-500">Rp
                                    {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const statusUrl = @json(route('public.order.invoice.status', ['invoice' => $sale->invoice_no]));
        const initialExpiresAt = @json($payment['expires_at'] ?? optional($sale->payment_expires_at)?->toDateTimeString());

        function parseDateSafe(value) {
            if (!value) return null;
            const normalized = String(value).replace(' ', 'T');
            const d = new Date(normalized);
            return isNaN(d.getTime()) ? null : d;
        }

        function setStatusAppearance(status) {
            const paymentStatusEl = document.getElementById('paymentStatus');
            const qrWrapper = document.getElementById('qrWrapper');
            const qrImage = document.getElementById('qrImage');
            const pendingHelp = document.getElementById('pendingHelp');
            const paidNotice = document.getElementById('paidNotice');
            const expiredNotice = document.getElementById('expiredNotice');
            const countdownCard = document.getElementById('countdownCard');

            if (!paymentStatusEl) return;

            paymentStatusEl.classList.remove('status-pending', 'status-paid', 'status-failed');

            if (status === 'paid') {
                paymentStatusEl.classList.add('status-paid');
                qrImage?.classList.add('hidden');
                pendingHelp?.classList.add('hidden');
                paidNotice?.classList.remove('hidden');
                expiredNotice?.classList.add('hidden');
                countdownCard?.classList.add('hidden');
            } else if (['expired', 'failed', 'cancelled'].includes(status)) {
                paymentStatusEl.classList.add('status-failed');
                qrImage?.classList.add('hidden');
                pendingHelp?.classList.add('hidden');
                paidNotice?.classList.add('hidden');
                expiredNotice?.classList.remove('hidden');
                countdownCard?.classList.add('hidden');
            } else {
                paymentStatusEl.classList.add('status-pending');
                qrImage?.classList.remove('hidden');
                pendingHelp?.classList.remove('hidden');
                paidNotice?.classList.add('hidden');
                expiredNotice?.classList.add('hidden');
                countdownCard?.classList.remove('hidden');
            }
        }

        function startCountdown(expiresAtValue) {
            const countdownEl = document.getElementById('countdown');
            if (!countdownEl) return;

            const expiresAt = parseDateSafe(expiresAtValue);
            if (!expiresAt) {
                countdownEl.textContent = '--:--';
                return;
            }

            function tick() {
                const now = new Date();
                const diff = expiresAt.getTime() - now.getTime();

                if (diff <= 0) {
                    countdownEl.textContent = '00:00';
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;

                countdownEl.textContent =
                    String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }

            tick();
            window.__invoiceCountdown && clearInterval(window.__invoiceCountdown);
            window.__invoiceCountdown = setInterval(tick, 1000);
        }

        async function pollStatus() {
            try {
                const res = await fetch(statusUrl, {
                    headers: { 'Accept': 'application/json' }
                });

                const json = await res.json();
                if (!json.ok) return;

                const paymentStatusEl = document.getElementById('paymentStatus');
                const expiresAtEl = document.getElementById('expiresAt');

                if (paymentStatusEl) {
                    paymentStatusEl.textContent = String(json.payment_status || 'pending').toUpperCase();
                }

                if (expiresAtEl && json.expires_at) {
                    expiresAtEl.textContent = json.expires_at;
                }

                setStatusAppearance(json.payment_status);

                if (json.expires_at) {
                    startCountdown(json.expires_at);
                }

                if (json.payment_status === 'paid') {
                    clearInterval(window.__invoicePoller);
                    clearInterval(window.__invoiceCountdown);
                    return;
                }

                if (['expired', 'failed', 'cancelled'].includes(json.payment_status)) {
                    clearInterval(window.__invoicePoller);
                    clearInterval(window.__invoiceCountdown);
                    return;
                }
            } catch (e) {
                console.error(e);
            }
        }

        setStatusAppearance(@json($sale->payment_status ?? 'pending'));
        startCountdown(initialExpiresAt);

        window.__invoicePoller = setInterval(pollStatus, 5000);
        pollStatus();
    </script>
</body>

</html>