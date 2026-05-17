<!doctype html>
<html lang="id" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Reservasi — Ayo Renne</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --gold-primary: #eab308;
            --gold-light: #fef08a;
            --gold-dark: #a16207;
            --obsidian-950: #020617;
            --obsidian-900: #0f172a;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--obsidian-950);
            color: white;
            overflow-x: hidden;
        }

        .font-luxury { font-family: 'Playfair Display', serif; }

        .glass-panel {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(234, 179, 8, 0.1);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            background: rgba(234, 179, 8, 0.02);
            border-color: rgba(234, 179, 8, 0.2);
            transform: translateY(-2px);
        }

        .gold-gradient-text {
            background: linear-gradient(to right, var(--gold-light), var(--gold-primary), var(--gold-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-luxury {
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark));
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-luxury:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -10px rgba(234, 179, 8, 0.3);
        }
    </style>
</head>

<body class="min-h-screen relative pb-12">
    {{-- Decorative Background Gradients --}}
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_50%_-20%,rgba(234,179,8,0.12),transparent_70%)]"></div>
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_0%_100%,rgba(234,179,8,0.06),transparent_50%)]"></div>
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_100%_100%,rgba(255,255,255,0.02),transparent_50%)]"></div>

    <div class="mx-auto max-w-6xl px-4 pt-8 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <header class="mb-12 flex items-center justify-between gap-6">
            <div>
                <div class="mb-2 text-[10px] font-black uppercase tracking-[0.3em] text-yellow-400">Reservation Status</div>
                <h1 class="font-luxury text-4xl font-bold text-white tracking-tight sm:text-5xl">Detail <span class="gold-gradient-text italic font-medium">Reservasi</span></h1>
                <p class="mt-2 text-xs font-semibold text-white/30 uppercase tracking-[0.2em] leading-relaxed">Review your booking detail & manual down payment instructions</p>
            </div>

            <a href="/" class="glass-card flex h-12 w-12 items-center justify-center rounded-2xl text-white/40 hover:text-white hover:border-white/20 hover:scale-105 transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </a>
        </header>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            {{-- LEFT COLUMN: Reservation Details & Billing --}}
            <div class="lg:col-span-7 space-y-6">
                <!-- Reservation Summary Card -->
                <div class="glass-panel rounded-[2.5rem] p-8 shadow-2xl backdrop-blur-2xl">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-8">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-[0.25em] text-white/30">Booking Code</span>
                            <div class="mt-1 text-2xl font-black text-white tracking-tight sm:text-3xl">{{ $reservation->code }}</div>

                            @php
                                $statusColor = match ($reservation->status) {
                                    'pending_dp' => 'text-yellow-400 border-yellow-400/20 bg-yellow-400/5 shadow-[0_0_15px_rgba(234,179,8,0.1)]',
                                    'confirmed' => 'text-emerald-400 border-emerald-400/20 bg-emerald-400/5 shadow-[0_0_15px_rgba(52,211,153,0.1)]',
                                    'checked_in' => 'text-blue-400 border-blue-400/20 bg-blue-400/5',
                                    'completed' => 'text-white border-white/10 bg-white/5',
                                    'cancelled' => 'text-red-400 border-red-400/20 bg-red-400/5',
                                    default => 'text-white border-white/10 bg-white/5',
                                };
                            @endphp

                            <div class="mt-3 inline-flex rounded-full border px-4 py-1.5 text-[9px] font-black uppercase tracking-widest {{ $statusColor }}">
                                {{ str_replace('_', ' ', $reservation->status) }}
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.01] px-5 py-4 text-right">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30">DP Required (50%)</span>
                            <div class="mt-1 text-2xl font-black text-yellow-400">
                                Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="glass-card rounded-2xl p-5">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30">Customer Name</span>
                            <div class="mt-2 font-bold text-white text-sm">{{ $reservation->customer_name ?: '-' }}</div>
                            <div class="mt-1 text-xs font-semibold text-white/55">{{ $reservation->customer_phone ?: '-' }}</div>
                        </div>

                        <div class="glass-card rounded-2xl p-5">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30">Reserved Space</span>
                            <div class="mt-2 font-bold text-white text-sm">{{ $reservation->resource?->name ?? '-' }}</div>
                            <div class="mt-1 text-xs font-semibold text-white/55">[{{ $reservation->resource?->type ?? '-' }}]</div>
                        </div>

                        <div class="glass-card rounded-2xl p-5 sm:col-span-2">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30">Reservation Schedule</span>
                            <div class="mt-2 font-bold text-white text-sm">
                                {{ $reservation->start_at->format('d M Y H:i') }} &mdash;
                                {{ $reservation->end_at->format('d M Y H:i') }}
                            </div>
                            <div class="mt-1 text-xs font-semibold text-white/55">Menu Selection: <span class="text-yellow-400 font-bold">{{ $reservation->menu_type }}</span></div>
                        </div>
                    </div>
                </div>

                <!-- Billing breakdown receipt -->
                <div class="glass-panel rounded-[2.5rem] p-8 shadow-2xl backdrop-blur-2xl">
                    <div class="mb-6">
                        <span class="text-[9px] font-black uppercase tracking-[0.25em] text-white/30">Financial Breakdown</span>
                        <h2 class="mt-1 text-xl font-bold tracking-tight">Billing Details</h2>
                    </div>

                    <div class="space-y-3">
                        <div class="glass-card flex items-center justify-between rounded-2xl px-5 py-4 text-xs font-semibold text-white/60">
                            <span>Food & Drink Menu items</span>
                            <span class="font-extrabold text-white">Rp {{ number_format($reservation->menu_total, 0, ',', '.') }}</span>
                        </div>

                        <div class="glass-card flex items-center justify-between rounded-2xl px-5 py-4 text-xs font-semibold text-white/60">
                            <span>Space Rental charge</span>
                            <span class="font-extrabold text-white">Rp {{ number_format($reservation->rental_total, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl border border-yellow-400/20 bg-yellow-400/5 px-5 py-4 text-sm font-semibold">
                            <span class="text-white/80 font-bold uppercase tracking-wider text-xs">Total Bill Estimation</span>
                            <span class="text-xl font-black text-yellow-400">Rp {{ number_format($reservation->grand_total, 0, ',', '.') }}</span>
                        </div>

                        <div class="glass-card flex items-center justify-between rounded-2xl px-5 py-4 text-xs font-semibold text-white/60">
                            <span>Required Down Payment (50%)</span>
                            <span class="font-extrabold text-yellow-400">Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="glass-card flex items-center justify-between rounded-2xl px-5 py-4 text-xs font-semibold text-white/60">
                            <span>Paid Down Payment</span>
                            <span class="font-extrabold text-emerald-400">Rp {{ number_format($reservation->paid_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Items ordered -->
                <div class="glass-panel rounded-[2.5rem] p-8 shadow-2xl backdrop-blur-2xl">
                    <span class="text-[9px] font-black uppercase tracking-[0.25em] text-white/30">Order Items</span>
                    <h2 class="mt-1 text-xl font-bold tracking-tight mb-6">Reservation Items</h2>

                    <div class="space-y-3">
                        @forelse($reservation->items as $it)
                            <div class="glass-card rounded-2xl px-5 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="text-xs font-extrabold uppercase tracking-tight text-white">{{ $it->snapshot_name }}</div>
                                        <div class="mt-1 text-[9px] font-black text-white/40 uppercase tracking-widest">
                                            Qty: {{ $it->qty }} &bull; {{ str_replace('_', ' ', $it->item_type) }}
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-xs font-black text-white">
                                        Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="glass-card rounded-2xl border-dashed py-8 text-center">
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/20 italic">No reservation items found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Manual Payment & Instructions --}}
            <div class="lg:col-span-5 space-y-6">
                <!-- Manual Payment Panel -->
                <div class="glass-panel rounded-[2.5rem] p-8 shadow-2xl backdrop-blur-2xl lg:sticky lg:top-8">
                    <span class="text-[9px] font-black uppercase tracking-[0.25em] text-yellow-400 mb-1 block">Manual Verification</span>
                    <h2 class="text-xl font-bold tracking-tight">Manual Payment</h2>
                    
                    <p class="mt-3 text-[10px] font-semibold leading-relaxed text-white/40 uppercase tracking-wider italic border-b border-white/5 pb-4">
                        Layanan pembayaran QRIS sedang dalam pemeliharaan. Silakan lakukan transfer DP secara manual ke salah satu rekening resmi di bawah ini.
                    </p>

                    @if ($reservation->status === 'pending_dp')
                        <!-- Required payment info -->
                        <div class="mt-6 rounded-[24px] border border-yellow-400/20 bg-yellow-400/5 p-5">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/40 block mb-0.5">Transfer Nominal DP</span>
                            <div class="text-3xl font-black text-yellow-400 tracking-tight">
                                Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Bank account details -->
                        <div class="mt-6 space-y-3">
                            <div class="glass-card rounded-2xl p-5 border-l-4 border-yellow-400">
                                <div class="flex items-center justify-between gap-4 mb-2">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-yellow-400">BANK MANDIRI</span>
                                    <span class="rounded-full bg-white/5 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-white/45">Official</span>
                                </div>
                                <div class="text-base font-black text-white tracking-tight" id="accNumber">143-00-1234567-8</div>
                                <div class="mt-1 text-[9px] font-black text-white/30 uppercase tracking-widest">A/N: AYO RENNE FINE DINING</div>
                            </div>

                            <div class="glass-card rounded-2xl p-5 border-l-4 border-yellow-400">
                                <div class="flex items-center justify-between gap-4 mb-2">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-yellow-400">BANK BCA</span>
                                    <span class="rounded-full bg-white/5 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-white/45">Official</span>
                                </div>
                                <div class="text-base font-black text-white tracking-tight" id="accNumberBca">890-123-4567</div>
                                <div class="mt-1 text-[9px] font-black text-white/30 uppercase tracking-widest">A/N: AYO RENNE FINE DINING</div>
                            </div>
                        </div>

                        <!-- WhatsApp Action CTA -->
                        @php
                            $waMessage = "Halo Admin Ayo Renne, saya ingin mengonfirmasi pembayaran DP reservasi saya dengan rincian berikut:\n\n"
                                       . "• Kode Reservasi: *" . $reservation->code . "*\n"
                                       . "• Nama Customer: *" . $reservation->customer_name . "*\n"
                                       . "• Nominal DP: *Rp " . number_format($reservation->dp_amount, 0, ',', '.') . "*\n\n"
                                       . "Berikut saya lampirkan bukti transfernya. Tolong segera dikonfirmasi ya. Terima kasih!";
                            $waLink = "https://wa.me/6281234567890?text=" . urlencode($waMessage);
                        @endphp

                        <div class="mt-8 space-y-4">
                            <a href="{{ $waLink }}" target="_blank"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-500 hover:bg-emerald-400 py-4 text-xs font-black uppercase tracking-[0.2em] text-white transition-all hover:scale-[1.02] active:scale-95 shadow-[0_15px_30px_rgba(16,185,129,0.15)]">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.625 1.451 5.403.002 9.792-4.382 9.795-9.79.002-2.618-1.01-5.08-2.858-6.932C16.36 2.03 13.9 1.01 11.531 1.01 6.126 1.01 1.734 5.4 1.732 10.806c-.001 1.547.41 3.055 1.192 4.412l-.125.688-1.026 3.743 3.84-1.006z"></path></svg>
                                KIRIM BUKTI TRANSFER
                            </a>

                            <div class="rounded-2xl border border-white/5 bg-white/[0.01] px-5 py-4 text-[10px] leading-relaxed text-white/40 italic">
                                Klik tombol di atas untuk mengirimkan bukti transfer secara instan ke WhatsApp Admin. Status reservasi Anda akan segera diaktifkan menjadi <span class="font-bold text-white">CONFIRMED</span> secara manual oleh admin setelah diverifikasi.
                            </div>
                        </div>
                    @elseif($reservation->status === 'confirmed')
                        <div class="mt-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 px-5 py-5 text-xs font-semibold leading-relaxed text-emerald-300 flex gap-3 items-start">
                            <span class="text-base">✓</span>
                            <div>
                                <h4 class="font-bold uppercase tracking-wider text-[10px] text-emerald-400 mb-1">Down Payment Confirmed</h4>
                                DP sebesar Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }} telah berhasil diverifikasi dan dikonfirmasi oleh Admin. Jadwal reservasi Anda sudah aman.
                            </div>
                        </div>
                    @elseif($reservation->status === 'checked_in')
                        <div class="mt-6 rounded-2xl border border-blue-500/20 bg-blue-500/5 px-5 py-5 text-xs font-semibold leading-relaxed text-blue-300 flex gap-3 items-start">
                            <span class="text-base">✓</span>
                            <div>
                                <h4 class="font-bold uppercase tracking-wider text-[10px] text-blue-400 mb-1">Checked In</h4>
                                Anda telah sukses melakukan check-in di meja/ruangan. Selamat menikmati hidangan spesial Ayo Renne!
                            </div>
                        </div>
                    @elseif($reservation->status === 'completed')
                        <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 px-5 py-5 text-xs font-semibold leading-relaxed text-white/70 flex gap-3 items-start">
                            <span class="text-base">✓</span>
                            <div>
                                <h4 class="font-bold uppercase tracking-wider text-[10px] text-white mb-1">Dining Completed</h4>
                                Sesi makan malam Anda telah selesai. Terima kasih atas kunjungan Anda di Ayo Renne Fine Dining!
                            </div>
                        </div>
                    @elseif($reservation->status === 'cancelled')
                        <div class="mt-6 rounded-2xl border border-red-500/20 bg-red-500/5 px-5 py-5 text-xs font-semibold leading-relaxed text-red-300 flex gap-3 items-start">
                            <span class="text-base">✕</span>
                            <div>
                                <h4 class="font-bold uppercase tracking-wider text-[10px] text-red-400 mb-1">Reservation Cancelled</h4>
                                Reservasi ini telah dibatalkan. Jika Anda merasa ini adalah kesalahan, silakan hubungi tim dukungan pelanggan kami.
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Payment History log list -->
                <div class="glass-panel rounded-[2.5rem] p-8 shadow-2xl backdrop-blur-2xl">
                    <span class="text-[9px] font-black uppercase tracking-[0.25em] text-white/30">Payment History</span>
                    <h2 class="mt-1 text-xl font-bold tracking-tight mb-6">Pembayaran Tercatat</h2>

                    <div class="space-y-3">
                        @forelse($reservation->payments as $p)
                            <div class="glass-card rounded-2xl p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-xs font-extrabold uppercase tracking-tight text-white">{{ $p->type }}</div>
                                        <div class="mt-1 text-[9px] font-black text-white/40 uppercase tracking-widest">
                                            Method: {{ $p->method }} &bull; {{ $p->status }}
                                        </div>
                                    </div>
                                    <div class="text-xs font-black text-white">
                                        Rp {{ number_format($p->amount, 0, ',', '.') }}
                                    </div>
                                </div>

                                @if($p->reference)
                                    <div class="mt-3 text-[9px] font-black text-yellow-400/60 uppercase tracking-widest">
                                        Ref: {{ $p->reference }}
                                    </div>
                                @endif

                                <div class="mt-2 text-[9px] font-semibold text-white/30 uppercase tracking-wider">
                                    {{ optional($p->paid_at)->format('d M Y H:i') ?: '-' }}
                                </div>
                            </div>
                        @empty
                            <div class="glass-card rounded-2xl border-dashed py-8 text-center">
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/20 italic">Belum ada pembayaran tercatat.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Continuous background check for status upgrades (e.g. when admin confirms manual payment) --}}
    <script>
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
                } catch (e) { }

                setTimeout(tick, 4000);
            }

            tick();
        })();
    </script>
</body>
</html>