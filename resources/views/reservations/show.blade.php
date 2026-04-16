<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Reservasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#050505] text-white">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(250,204,21,.12),transparent_28%),radial-gradient(circle_at_top_right,rgba(255,255,255,.06),transparent_20%),linear-gradient(to_bottom,#050505,#09090b)]"></div>
    <div class="pointer-events-none fixed left-[-120px] top-[60px] -z-10 h-[240px] w-[240px] rounded-full bg-yellow-400/10 blur-3xl"></div>
    <div class="pointer-events-none fixed right-[-120px] top-[100px] -z-10 h-[220px] w-[220px] rounded-full bg-white/5 blur-3xl"></div>

    <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <div class="text-[11px] uppercase tracking-[0.28em] text-yellow-300/70">Status Reservasi</div>
                <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Pembayaran Reservasi</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-white/60">
                    Lihat detail reservasi, nominal tagihan, dan lakukan pembayaran DP melalui QRIS.
                </p>
            </div>

            <a href="/"
                class="inline-flex items-center rounded-2xl border border-white/12 bg-white/[0.05] px-4 py-2 text-sm font-semibold hover:bg-white/[0.09]">
                Beranda
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.05fr_.95fr]">
            {{-- LEFT --}}
            <section class="space-y-6">
                <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="text-sm text-white/55">Kode Reservasi</div>
                            <div class="mt-1 text-2xl font-bold sm:text-3xl">{{ $reservation->code }}</div>

                            @php
                                $statusColor = match($reservation->status) {
                                    'pending_dp' => 'text-yellow-300 border-yellow-400/20 bg-yellow-400/10',
                                    'confirmed' => 'text-emerald-300 border-emerald-400/20 bg-emerald-400/10',
                                    'checked_in' => 'text-blue-300 border-blue-400/20 bg-blue-400/10',
                                    'completed' => 'text-white border-white/15 bg-white/10',
                                    'cancelled' => 'text-red-300 border-red-400/20 bg-red-400/10',
                                    default => 'text-white border-white/15 bg-white/10',
                                };
                            @endphp

                            <div class="mt-3 inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                                {{ strtoupper(str_replace('_', ' ', $reservation->status)) }}
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-sm">
                            <div class="text-white/50">DP</div>
                            <div class="mt-1 text-xl font-bold text-yellow-300">
                                Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <div class="text-[11px] uppercase tracking-[0.18em] text-white/45">Customer</div>
                            <div class="mt-2 font-semibold">{{ $reservation->customer_name ?: '-' }}</div>
                            <div class="mt-1 text-sm text-white/60">{{ $reservation->customer_phone ?: '-' }}</div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <div class="text-[11px] uppercase tracking-[0.18em] text-white/45">Resource</div>
                            <div class="mt-2 font-semibold">{{ $reservation->resource?->name ?? '-' }}</div>
                            <div class="mt-1 text-sm text-white/60">[{{ $reservation->resource?->type ?? '-' }}]</div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4 sm:col-span-2">
                            <div class="text-[11px] uppercase tracking-[0.18em] text-white/45">Waktu Reservasi</div>
                            <div class="mt-2 font-semibold">
                                {{ $reservation->start_at->format('d M Y H:i') }} → {{ $reservation->end_at->format('d M Y H:i') }}
                            </div>
                            <div class="mt-1 text-sm text-white/60">Menu type: {{ $reservation->menu_type }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Tagihan</div>
                            <h2 class="mt-1 text-lg font-semibold">Rincian Biaya</h2>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-sm">
                            <span class="text-white/60">Menu</span>
                            <span class="font-semibold">Rp {{ number_format($reservation->menu_total, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-sm">
                            <span class="text-white/60">Sewa</span>
                            <span class="font-semibold">Rp {{ number_format($reservation->rental_total, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl border border-yellow-400/15 bg-yellow-400/10 px-4 py-3 text-sm">
                            <span class="text-white/80 font-medium">Total</span>
                            <span class="text-lg font-bold text-yellow-300">Rp {{ number_format($reservation->grand_total, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-sm">
                            <span class="text-white/60">DP (50%)</span>
                            <span class="font-semibold">Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-sm">
                            <span class="text-white/60">Sudah dibayar</span>
                            <span class="font-semibold">Rp {{ number_format($reservation->paid_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
                    <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Rincian Menu</div>
                    <h2 class="mt-1 text-lg font-semibold">Item Reservasi</h2>

                    <div class="mt-4 space-y-3">
                        @forelse($reservation->items as $it)
                            <div class="rounded-2xl border border-white/10 bg-black/20 px-4 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="font-semibold">{{ $it->snapshot_name }}</div>
                                        <div class="mt-1 text-sm text-white/60">
                                            Qty {{ $it->qty }} • {{ $it->item_type }}
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-sm font-semibold text-white/85">
                                        Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 bg-black/20 px-4 py-5 text-sm text-white/50">
                                Belum ada item reservasi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- RIGHT --}}
            <aside class="space-y-6 lg:sticky lg:top-6 lg:h-fit">
                <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
                    <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Pembayaran</div>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight">Bayar DP via QRIS</h2>
                    <p class="mt-2 text-sm leading-6 text-white/60">
                        Pembayaran DP hanya menggunakan QRIS. Setelah pembayaran berhasil, status reservasi akan ter-update otomatis.
                    </p>

                    @if ($reservation->status === 'pending_dp')
                        <div class="mt-5 rounded-[24px] border border-yellow-400/15 bg-yellow-400/10 p-4">
                            <div class="text-sm text-white/70">Nominal DP</div>
                            <div class="mt-1 text-3xl font-bold text-yellow-300">
                                Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-3">
                            <button id="payBtn"
                                class="w-full rounded-2xl bg-yellow-400 px-5 py-3 text-sm font-semibold text-black hover:bg-yellow-300">
                                Buat QRIS
                            </button>
                        </div>

                        <div id="payInfo" class="mt-4"></div>

                        <div id="payHint" class="mt-4 rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-xs leading-6 text-white/60">
                            Klik <span class="font-semibold text-white/85">Buat QRIS</span>, lalu scan QR dari aplikasi pembayaran kamu.
                        </div>
                    @elseif($reservation->status === 'confirmed')
                        <div class="mt-5 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-4 text-sm text-emerald-200">
                            ✅ DP sudah dibayar. Reservasi kamu sudah dikonfirmasi.
                        </div>
                    @elseif($reservation->status === 'checked_in')
                        <div class="mt-5 rounded-2xl border border-blue-400/20 bg-blue-400/10 px-4 py-4 text-sm text-blue-200">
                            ✅ Reservasi sudah check-in.
                        </div>
                    @elseif($reservation->status === 'completed')
                        <div class="mt-5 rounded-2xl border border-white/15 bg-white/10 px-4 py-4 text-sm text-white/85">
                            ✅ Reservasi sudah selesai.
                        </div>
                    @elseif($reservation->status === 'cancelled')
                        <div class="mt-5 rounded-2xl border border-red-400/20 bg-red-400/10 px-4 py-4 text-sm text-red-200">
                            Reservasi ini dibatalkan.
                        </div>
                    @endif
                </div>

                <div class="rounded-[28px] border border-white/12 bg-white/[0.06] p-5 shadow-2xl backdrop-blur-2xl sm:p-6">
                    <div class="text-[11px] uppercase tracking-[0.22em] text-white/45">Riwayat</div>
                    <h2 class="mt-1 text-lg font-semibold">Pembayaran Tercatat</h2>

                    <div class="mt-4 space-y-3">
                        @forelse($reservation->payments as $p)
                            <div class="rounded-2xl border border-white/10 bg-black/20 px-4 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-semibold">{{ $p->type }}</div>
                                        <div class="mt-1 text-xs text-white/60">
                                            {{ $p->method }} • {{ $p->status }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold">
                                        Rp {{ number_format($p->amount, 0, ',', '.') }}
                                    </div>
                                </div>

                                @if($p->reference)
                                    <div class="mt-2 text-xs text-white/50">
                                        Ref: {{ $p->reference }}
                                    </div>
                                @endif

                                <div class="mt-1 text-xs text-white/50">
                                    {{ optional($p->paid_at)->format('d M Y H:i') ?: '-' }}
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 bg-black/20 px-4 py-5 text-sm text-white/50">
                                Belum ada pembayaran tercatat.
                            </div>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        const payBtn = document.getElementById('payBtn');
        const payInfo = document.getElementById('payInfo');

        if (payBtn) {
            payBtn.addEventListener('click', async () => {
                payBtn.disabled = true;
                payBtn.textContent = 'Membuat QRIS...';

                try {
                    const res = await fetch("{{ route('public.reservations.pay_dp', $reservation->code) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ payment_method: "qris" })
                    });

                    const json = await res.json();

                    if (!json.ok) {
                        payInfo.innerHTML = `
                            <div class="rounded-2xl border border-red-400/20 bg-red-400/10 px-4 py-4 text-sm text-red-200">
                                ${json.message || 'Gagal membuat pembayaran.'}
                            </div>
                        `;
                        return;
                    }

                    const p = json.payment || {};
                    if (p.qr_url) {
                        payInfo.innerHTML = `
                            <div class="rounded-[24px] border border-white/10 bg-black/20 p-4">
                                <div class="text-sm font-semibold">QRIS Berhasil Dibuat</div>
                                <div class="mt-2 text-sm text-white/65">
                                    Scan QR berikut untuk membayar DP reservasi.
                                </div>

                                <a href="${p.qr_url}" target="_blank"
                                    class="mt-4 inline-flex rounded-2xl bg-yellow-400 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-300">
                                    Buka QRIS
                                </a>

                                <div class="mt-3 text-xs text-white/55">
                                    Expired: ${p.expires_at || '-'}
                                </div>
                            </div>
                        `;
                    } else {
                        payInfo.innerHTML = `
                            <div class="rounded-2xl border border-red-400/20 bg-red-400/10 px-4 py-4 text-sm text-red-200">
                                QRIS tidak tersedia dari respons pembayaran.
                            </div>
                        `;
                    }
                } catch (e) {
                    payInfo.innerHTML = `
                        <div class="rounded-2xl border border-red-400/20 bg-red-400/10 px-4 py-4 text-sm text-red-200">
                            Terjadi kesalahan saat membuat QRIS.
                        </div>
                    `;
                } finally {
                    payBtn.disabled = false;
                    payBtn.textContent = 'Buat QRIS';
                }
            });
        }

        (function poll() {
            const currentStatus = "{{ $reservation->status }}";
            if (currentStatus !== 'pending_dp') return;

            async function tick() {
                try {
                    const res = await fetch("{{ route('public.reservations.status', $reservation->code) }}", {
                        headers: { "Accept": "application/json" }
                    });
                    const j = await res.json();

                    if (j.ok && j.status && j.status !== 'pending_dp') {
                        location.reload();
                        return;
                    }

                    if (j.ok && j.qr_url && payInfo && payInfo.innerHTML.trim() === '') {
                        payInfo.innerHTML = `
                            <div class="rounded-[24px] border border-white/10 bg-black/20 p-4">
                                <div class="text-sm font-semibold">QRIS Aktif</div>
                                <div class="mt-2 text-sm text-white/65">
                                    QRIS sudah tersedia. Kamu bisa lanjut scan pembayaran.
                                </div>

                                <a href="${j.qr_url}" target="_blank"
                                    class="mt-4 inline-flex rounded-2xl bg-yellow-400 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-300">
                                    Buka QRIS
                                </a>

                                <div class="mt-3 text-xs text-white/55">
                                    Expired: ${j.expires_at || '-'}
                                </div>
                            </div>
                        `;
                    }
                } catch (e) {}

                setTimeout(tick, 4000);
            }

            tick();
        })();
    </script>
</body>
</html>