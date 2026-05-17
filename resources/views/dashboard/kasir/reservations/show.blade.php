@extends('layouts.kasir')
@section('title', 'Detail Reservasi ' . $reservation->code)

@section('body')
    <div class="max-w-7xl mx-auto space-y-8 animate-fade-up">
        
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight mb-1 text-white">Reservasi {{ $reservation->code }}</h1>
                <p class="text-white/40 text-sm">Operasional kasir: check-in & pelunasan</p>
            </div>
            <a href="{{ route('kasir.reservations.index') }}"
                class="btn-premium-glass px-6 py-2.5 text-sm rounded-xl flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-2xl border border-accent-gold/20 bg-accent-gold/5 px-6 py-4 text-sm text-accent-gold flex items-center gap-3 animate-fade-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="rounded-2xl border border-red-500/20 bg-red-500/5 px-6 py-4 text-sm text-red-500 flex items-center gap-3 animate-fade-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $errors->first() }}
            </div>
        @endif

        @php
            $finalQrUrl = null;
            if (is_array($reservation->midtrans_response ?? null)) {
                $actions = collect($reservation->midtrans_response['actions'] ?? []);
                $finalQrUrl = optional($actions->firstWhere('name', 'generate-qr-code'))['url'] ?? null;
            }
            $isFinalOrder = is_string($reservation->midtrans_order_id ?? null) && str_starts_with($reservation->midtrans_order_id, 'RSV-FINAL-');
        @endphp

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_.6fr]">
            <div class="space-y-6">
                {{-- Customer & Reservation Info --}}
                <div class="premium-card p-6 border-white/5">
                    <h3 class="text-xs font-black uppercase tracking-widest text-white/40 mb-4">Informasi Reservasi</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Customer</div>
                            <div class="text-sm font-bold text-white">{{ $reservation->customer_name }}</div>
                            <div class="text-[10px] font-bold text-white/40 mt-0.5">{{ $reservation->customer_phone }}</div>
                        </div>
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Resource</div>
                            <div class="text-sm font-bold text-white">{{ $reservation->resource?->name ?? '-' }}</div>
                        </div>
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Status</div>
                            <div class="text-sm font-bold text-accent-gold">{{ strtoupper($reservation->status) }}</div>
                        </div>
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5 sm:col-span-2 lg:col-span-3">
                            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Waktu</div>
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="text-sm font-bold text-white">{{ $reservation->start_at->format('d M Y H:i') }}</div>
                                <span class="text-white/20 hidden sm:inline">→</span>
                                <span class="text-white/20 sm:hidden">sampai</span>
                                <div class="text-sm font-bold text-white/60">{{ $reservation->end_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Items List --}}
                <div class="premium-card p-6 border-white/5">
                    <h3 class="text-xs font-black uppercase tracking-widest text-white/40 mb-4">Daftar Item</h3>
                    <div class="space-y-3">
                        @foreach($reservation->items as $it)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                                <div class="flex-1">
                                    <div class="font-bold text-white text-sm">{{ $it->snapshot_name }}</div>
                                </div>
                                <div class="text-right flex items-center gap-4">
                                    <div class="text-xs text-white/40 font-bold">x<span>{{ $it->qty }}</span></div>
                                    <div class="text-sm font-bold text-accent-gold w-24">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Payment Summary --}}
                <div class="premium-card p-6 border-white/5 bg-accent-gold/5">
                    <h3 class="text-xs font-black uppercase tracking-widest text-accent-gold/60 mb-4">Ringkasan Pembayaran</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-white/5">
                            <span class="text-xs font-bold text-white/60">Total Keseluruhan</span>
                            <span class="text-sm font-bold text-white">Rp {{ number_format($reservation->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-white/5">
                            <span class="text-xs font-bold text-white/60">Telah Dibayar (DP)</span>
                            <span class="text-sm font-bold text-white">Rp {{ number_format($reservation->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-black text-accent-gold">Sisa Tagihan</span>
                            <span class="text-2xl font-black text-accent-gold">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action Section --}}
                <div class="premium-card p-6 border-white/5">
                    <h3 class="text-xs font-black uppercase tracking-widest text-white/40 mb-4">Aksi Kasir</h3>

                    @if($isFinalOrder && $finalQrUrl && in_array($reservation->midtrans_transaction_status, ['pending', 'authorize', ''], true))
                        <div class="mb-4 rounded-2xl border border-blue-500/20 bg-blue-500/5 p-4 text-sm">
                            <div class="font-bold text-blue-400 mb-2 text-xs uppercase tracking-widest">QRIS Pelunasan</div>
                            <div class="mb-3">
                                <a href="{{ $finalQrUrl }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-white hover:text-blue-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    Buka QR / Scan QR
                                </a>
                            </div>
                            <div class="text-[10px] font-bold text-white/40 flex flex-col gap-1">
                                <div>Status: <span class="text-blue-400">{{ $reservation->midtrans_transaction_status ?? 'PENDING' }}</span></div>
                                <div>Exp: {{ optional($reservation->payment_expires_at)?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    @endif

                    @if($reservation->status === 'confirmed')
                        <form method="POST" action="{{ route('kasir.reservations.check_in', $reservation) }}">
                            @csrf
                            <button class="btn-premium-primary w-full py-3.5 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Check-in Customer
                            </button>
                        </form>
                    @endif

                    @if($reservation->status === 'checked_in')
                        <form method="POST" action="{{ route('kasir.reservations.checkout', $reservation) }}" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-2">Metode Pembayaran</label>
                                <select name="method" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:border-accent-gold/50 focus:ring-0 transition-all outline-none">
                                    <option value="CASH">CASH</option>
                                    <option value="QRIS">QRIS (Midtrans)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-2">Jumlah Pembayaran</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/40 font-bold">Rp</span>
                                    <input type="number" name="amount" min="1" value="{{ $remaining }}" readonly
                                        class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-10 pr-4 text-sm font-bold text-accent-gold focus:border-accent-gold/50 focus:ring-0 transition-all outline-none cursor-not-allowed">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-2">Referensi (Opsional)</label>
                                <input name="reference" placeholder="Contoh: Tunai pas / Catatan Midtrans"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-sm text-white placeholder:text-white/20 focus:border-accent-gold/50 focus:ring-0 transition-all outline-none">
                            </div>

                            <button class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-400 px-5 py-3.5 text-sm font-bold text-black hover:from-emerald-400 hover:to-emerald-300 transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)] flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Proses Pembayaran
                            </button>
                        </form>

                        <div class="mt-4 p-4 rounded-xl border border-white/5 bg-white/[0.02] flex items-start gap-3">
                            <svg class="w-4 h-4 text-white/40 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-[10px] text-white/40 leading-relaxed font-bold">
                                Jika memilih QRIS, sistem akan membuat QR Midtrans. Reservasi baru selesai otomatis setelah webhook settlement diterima.
                            </p>
                        </div>
                    @endif

                    @if($reservation->menu_type === 'REGULAR')
                        <div class="mt-4 p-4 rounded-xl border border-white/5 bg-white/[0.02] flex items-start gap-3">
                            <svg class="w-4 h-4 text-white/40 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <p class="text-[10px] text-white/40 leading-relaxed font-bold">
                                Catatan: Saat settlement FINAL, sistem akan otomatis consume bahan yang sudah di-lock untuk reservasi REGULAR.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            // cuma polling kalau sedang ada transaksi Midtrans FINAL yang pending
            const isPending = "{{ $reservation->midtrans_transaction_status ?? '' }}" === "pending";
            const isCheckedIn = "{{ $reservation->status }}" === "checked_in";

            if (!isCheckedIn || !isPending) return;

            const url = "{{ route('kasir.reservations.status', $reservation) }}";
            let lastStatus = "{{ $reservation->status }}";
            let tries = 0;

            async function poll() {
                tries++;
                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();

                    if (data.status && data.status !== lastStatus) {
                        location.reload();
                        return;
                    }

                    // stop polling setelah 10 menit biar tidak terus2an (optional)
                    if (tries < 120) setTimeout(poll, 5000);
                } catch (e) {
                    if (tries < 120) setTimeout(poll, 5000);
                }
            }

            setTimeout(poll, 3000);
        })();
    </script>
@endsection