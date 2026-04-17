@extends('layouts.admin')
@section('title', 'Detail Reservasi')

@section('body')
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button id="openMobileSidebar" type="button"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
            <div>
                <h1 class="text-xl font-semibold">Detail Reservasi</h1>
                <p class="text-sm text-white/70">{{ $reservation->code }} • status: <span
                        class="font-semibold">{{ $reservation->status }}</span></p>
            </div>
        </div>

        <a href="{{ route('admin.reservations.index') }}"
            class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold hover:bg-white/10">
            ← Kembali
        </a>
        @if (in_array($reservation->menu_type, ['BUFFET', 'MIXED']))
            <a href="{{ route('admin.reservations.buffet_inventory', $reservation) }}"
                class="block w-full text-center rounded-xl bg-white/15 px-5 py-3 text-sm font-semibold hover:bg-white/20">
                Kelola Buffet Inventory
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-500/10 px-4 py-3 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mt-4 rounded-2xl border border-red-300/20 bg-red-500/10 px-4 py-3 text-sm whitespace-pre-line">
            ❌ {{ $errors->first() }}
        </div>
    @endif

    @php
        $remaining = max(0, (int) $reservation->grand_total - (int) $reservation->paid_amount);

        $finalQrUrl = null;
        if (is_array($reservation->midtrans_response ?? null)) {
            $actions = collect($reservation->midtrans_response['actions'] ?? []);
            $finalQrUrl = optional($actions->firstWhere('name', 'generate-qr-code'))['url'] ?? null;
        }
        $isFinalOrder = is_string($reservation->midtrans_order_id ?? null) && str_starts_with($reservation->midtrans_order_id, 'RSV-FINAL-');
      @endphp

    <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1fr_.9fr]">
        <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/15 bg-white/5 p-4">
                    <div class="text-xs text-white/60">Customer</div>
                    <div class="mt-1 font-semibold">{{ $reservation->customer_name }}</div>
                    <div class="text-sm text-white/70">{{ $reservation->customer_phone }}</div>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/5 p-4">
                    <div class="text-xs text-white/60">Resource</div>
                    <div class="mt-1 font-semibold">{{ $reservation->resource?->name }}</div>
                    <div class="text-sm text-white/70">[{{ $reservation->resource?->type }}] kap
                        {{ $reservation->resource?->capacity }}</div>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/5 p-4">
                    <div class="text-xs text-white/60">Waktu</div>
                    <div class="mt-1 font-semibold">{{ $reservation->start_at->format('d M Y H:i') }}</div>
                    <div class="text-sm text-white/70">{{ $reservation->end_at->format('d M Y H:i') }}</div>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/5 p-4">
                    <div class="text-xs text-white/60">Menu Type</div>
                    <div class="mt-1 font-semibold">{{ $reservation->menu_type }}</div>
                    <div class="text-sm text-white/70">Pax: {{ $reservation->pax ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border border-white/15 bg-white/5 p-4">
                <div class="font-semibold">Items</div>
                <div class="mt-3 space-y-2">
                    @forelse($reservation->items as $it)
                        <div
                            class="flex items-center justify-between gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm">
                            <div class="font-semibold">{{ $it->snapshot_name }}</div>
                            <div class="text-white/70">x{{ $it->qty }} • Rp {{ number_format($it->subtotal, 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-white/60">Tidak ada item.</div>
                    @endforelse
                </div>
            </div>

            @if (in_array($reservation->menu_type, ['REGULAR', 'MIXED']))
                <div class="mt-5 rounded-2xl border border-white/15 bg-white/5 p-4">
                    <div class="font-semibold">Material Locks</div>
                    <div class="mt-3 overflow-hidden rounded-xl border border-white/10">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[650px] text-left text-sm">
                                <thead class="bg-white/10 text-xs text-white/70">
                                    <tr>
                                        <th class="px-4 py-3">Bahan</th>
                                        <th class="px-4 py-3">Locked</th>
                                        <th class="px-4 py-3">Released</th>
                                        <th class="px-4 py-3">Consumed</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/10">
                                    @forelse($reservation->locks as $l)
                                        <tr>
                                            <td class="px-4 py-3">{{ $l->rawMaterial?->name }}</td>
                                            <td class="px-4 py-3">{{ $l->qty_locked }}</td>
                                            <td class="px-4 py-3">{{ $l->qty_released }}</td>
                                            <td class="px-4 py-3">{{ $l->qty_consumed }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-white/60">Belum ada lock.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
            <div class="rounded-2xl border border-white/15 bg-white/5 p-4">
                <div class="font-semibold">Tagihan</div>
                <div class="mt-3 space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-white/70">Menu</span><span>Rp
                            {{ number_format($reservation->menu_total, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-white/70">Rental</span><span>Rp
                            {{ number_format($reservation->rental_total, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between font-semibold"><span>Total</span><span>Rp
                            {{ number_format($reservation->grand_total, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-white/70">DP</span><span>Rp
                            {{ number_format($reservation->dp_amount, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-white/70">Paid</span><span>Rp
                            {{ number_format($reservation->paid_amount, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-white/70">Remaining</span><span
                            class="text-yellow-300 font-semibold">Rp {{ number_format($remaining, 0, ',', '.') }}</span></div>
                </div>
            </div>

            {{-- QR FINAL kalau sudah dibuat --}}
            @if($isFinalOrder && $finalQrUrl && in_array($reservation->midtrans_transaction_status, ['pending', 'authorize', ''], true))
                <div class="mt-5 rounded-2xl border border-white/15 bg-white/5 p-4 text-sm">
                    <div class="font-semibold">QRIS Pelunasan (Midtrans)</div>
                    <div class="mt-2">
                        <a href="{{ $finalQrUrl }}" target="_blank" class="underline">Buka QR / Scan QR</a>
                    </div>
                    <div class="mt-1 text-xs text-white/60">
                        Status: {{ $reservation->midtrans_transaction_status ?? '-' }}
                        • Exp: {{ optional($reservation->payment_expires_at)?->format('d M Y H:i') ?? '-' }}
                    </div>
                </div>
            @endif

            <div class="mt-5 rounded-2xl border border-white/15 bg-white/5 p-4">
                <div class="font-semibold">Pembayaran</div>
                <div class="mt-3 space-y-2 text-sm">
                    @forelse($reservation->payments as $p)
                        <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                            <div class="flex justify-between">
                                <div class="font-semibold">{{ $p->type }}</div>
                                <div>Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
                            </div>
                            <div class="mt-1 text-xs text-white/60">{{ $p->method }} • {{ $p->status }} •
                                {{ optional($p->paid_at)->format('d M Y H:i') }}</div>
                        </div>
                    @empty
                        <div class="text-white/60">Belum ada pembayaran.</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @if(in_array($reservation->status, ['pending_dp', 'draft'], true))
                    <form method="POST" action="{{ route('admin.reservations.dp_paid', $reservation) }}"
                        class="rounded-2xl border border-white/15 bg-white/5 p-4">
                        @csrf
                        <div class="font-semibold">Mark DP Paid (manual)</div>
                        <div class="mt-3 grid grid-cols-1 gap-3">
                            <div>
                                <div class="text-xs text-white/60">Metode</div>
                                <select name="method"
                                    class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
                                    <option value="CASH">CASH</option>
                                    <option value="QRIS">QRIS</option>
                                    <option value="MIDTRANS">MIDTRANS</option>
                                </select>
                            </div>
                            <div>
                                <div class="text-xs text-white/60">Amount</div>
                                <input type="number" name="amount" min="1" value="{{ $reservation->dp_amount }}"
                                    class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
                            </div>
                            <div>
                                <div class="text-xs text-white/60">Reference (opsional)</div>
                                <input name="reference"
                                    class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
                            </div>
                            <button
                                class="w-full rounded-xl bg-emerald-500/90 px-5 py-3 text-sm font-semibold text-black hover:bg-emerald-400/90">
                                Konfirmasi DP
                            </button>
                        </div>
                    </form>
                @endif

                @if($reservation->status === 'confirmed')
                    <form method="POST" action="{{ route('admin.reservations.check_in', $reservation) }}">
                        @csrf
                        <button class="w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
                            Check-in
                        </button>
                    </form>
                @endif

                @if($reservation->status === 'checked_in')
                    <form method="POST" action="{{ route('admin.reservations.checkout', $reservation) }}"
                        class="rounded-2xl border border-white/15 bg-white/5 p-4">
                        @csrf
                        <div class="font-semibold">Checkout (Pelunasan)</div>
                        <div class="mt-3 grid grid-cols-1 gap-3">
                            <div>
                                <div class="text-xs text-white/60">Metode</div>
                                <select name="method"
                                    class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
                                    <option value="CASH">CASH</option>
                                    <option value="QRIS">QRIS (Midtrans)</option>
                                </select>
                            </div>
                            <div>
                                <div class="text-xs text-white/60">Amount</div>
                                <input type="number" name="amount" min="1" value="{{ $remaining }}"
                                    class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
                            </div>
                            <div>
                                <div class="text-xs text-white/60">Reference (opsional)</div>
                                <input name="reference"
                                    class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
                            </div>
                            <button class="w-full rounded-xl bg-white/15 px-5 py-3 text-sm font-semibold hover:bg-white/20">
                                Buat Pembayaran (CASH / QRIS)
                            </button>
                            <div class="text-xs text-white/60">
                                Jika pilih QRIS, reservasi selesai setelah webhook settlement Midtrans.
                            </div>
                        </div>
                    </form>
                @endif

                @if(!in_array($reservation->status, ['completed', 'cancelled'], true))
                    <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}"
                        onsubmit="return confirm('Batalkan reservasi ini?')">
                        @csrf
                        <button
                            class="w-full rounded-xl border border-red-300/20 bg-red-500/10 px-5 py-3 text-sm font-semibold text-red-100 hover:bg-red-500/15">
                            Cancel Reservasi
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection