@extends('dashboard.admin._reservation_layout')

@section('title', 'Detail Reservasi')
@section('page_title', 'Detail Reservasi')
@section('page_subtitle', $reservation->code)

@section('content')
<div class="grid md:grid-cols-2 gap-4">
    <div class="border rounded p-3">
        <div class="font-semibold mb-2">Info</div>
        <div class="text-sm grid gap-1">
            <div><span class="text-gray-600">Customer:</span> {{ $reservation->customer_name }} ({{ $reservation->customer_phone }})</div>
            <div><span class="text-gray-600">Resource:</span> {{ $reservation->resource?->name }} [{{ $reservation->resource?->type }}]</div>
            <div><span class="text-gray-600">Waktu:</span> {{ $reservation->start_at->format('d M Y H:i') }} → {{ $reservation->end_at->format('d M Y H:i') }}</div>
            <div><span class="text-gray-600">Menu Type:</span> {{ $reservation->menu_type }}</div>
            <div><span class="text-gray-600">Status:</span> <span class="font-semibold">{{ $reservation->status }}</span></div>
        </div>
    </div>

    <div class="border rounded p-3">
        <div class="font-semibold mb-2">Tagihan</div>
        <div class="text-sm grid gap-1">
            <div><span class="text-gray-600">Menu:</span> Rp {{ number_format($reservation->menu_total) }}</div>
            <div><span class="text-gray-600">Rental:</span> Rp {{ number_format($reservation->rental_total) }}</div>
            <div><span class="text-gray-600">Grand Total:</span> <span class="font-semibold">Rp {{ number_format($reservation->grand_total) }}</span></div>
            <div><span class="text-gray-600">DP (50%):</span> Rp {{ number_format($reservation->dp_amount) }}</div>
            <div><span class="text-gray-600">Paid:</span> Rp {{ number_format($reservation->paid_amount) }}</div>
            <div><span class="text-gray-600">Remaining:</span> Rp {{ number_format(max(0, $reservation->grand_total - $reservation->paid_amount)) }}</div>
        </div>
    </div>
</div>

<div class="mt-4 grid lg:grid-cols-2 gap-4">
    <div class="border rounded p-3">
        <div class="font-semibold mb-2">Items</div>
        <ul class="text-sm list-disc ml-5">
            @forelse ($reservation->items as $it)
                <li>{{ $it->snapshot_name }} x{{ $it->qty }} (Rp {{ number_format($it->subtotal) }})</li>
            @empty
                <li class="text-gray-600">Tidak ada item.</li>
            @endforelse
        </ul>
    </div>

    <div class="border rounded p-3">
        <div class="font-semibold mb-2">Pembayaran</div>
        <ul class="text-sm list-disc ml-5">
            @forelse ($reservation->payments as $p)
                <li>{{ $p->type }} - Rp {{ number_format($p->amount) }} ({{ $p->method }}) - {{ $p->status }}</li>
            @empty
                <li class="text-gray-600">Belum ada pembayaran.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="mt-4 border rounded p-3">
    <div class="font-semibold mb-2">Material Locks (REGULAR)</div>
    @if ($reservation->menu_type !== 'REGULAR')
        <div class="text-sm text-gray-600">BUFFET: lock tidak otomatis (manual).</div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50 text-left">
                        <th class="p-2">Bahan</th>
                        <th class="p-2">Locked</th>
                        <th class="p-2">Released</th>
                        <th class="p-2">Consumed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservation->locks as $l)
                        <tr class="border-b">
                            <td class="p-2">{{ $l->rawMaterial?->name }}</td>
                            <td class="p-2">{{ $l->qty_locked }}</td>
                            <td class="p-2">{{ $l->qty_released }}</td>
                            <td class="p-2">{{ $l->qty_consumed }}</td>
                        </tr>
                    @empty
                        <tr><td class="p-3 text-gray-600" colspan="4">Belum ada lock (DP belum dibayar atau item kosong).</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="mt-4 flex flex-wrap gap-2">
    {{-- DP Paid --}}
    @if (in_array($reservation->status, ['pending_dp','draft'], true))
        <form method="POST" action="{{ route('admin.reservations.dp_paid', $reservation) }}" class="flex flex-wrap gap-2 items-end">
            @csrf
            <div>
                <label class="text-xs text-gray-600">Metode</label>
                <select name="method" class="px-3 py-2 border rounded">
                    <option value="CASH">CASH</option>
                    <option value="QRIS">QRIS</option>
                    <option value="MIDTRANS">MIDTRANS</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-600">Amount</label>
                <input type="number" name="amount" min="1" value="{{ $reservation->dp_amount }}" class="px-3 py-2 border rounded w-40">
            </div>
            <div>
                <label class="text-xs text-gray-600">Ref (opsional)</label>
                <input name="reference" class="px-3 py-2 border rounded w-56">
            </div>
            <button class="px-4 py-2 rounded bg-green-600 text-white">Mark DP Paid</button>
        </form>
    @endif

    {{-- Check-in --}}
    @if ($reservation->status === 'confirmed')
        <form method="POST" action="{{ route('admin.reservations.check_in', $reservation) }}">
            @csrf
            <button class="px-4 py-2 rounded bg-blue-600 text-white">Check-in</button>
        </form>
    @endif

    {{-- Checkout --}}
    @if ($reservation->status === 'checked_in')
        <form method="POST" action="{{ route('admin.reservations.checkout', $reservation) }}" class="flex flex-wrap gap-2 items-end">
            @csrf
            <div>
                <label class="text-xs text-gray-600">Metode</label>
                <select name="method" class="px-3 py-2 border rounded">
                    <option value="CASH">CASH</option>
                    <option value="QRIS">QRIS</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-600">Amount</label>
                <input type="number" name="amount" min="1"
                       value="{{ max(0, $reservation->grand_total - $reservation->paid_amount) }}"
                       class="px-3 py-2 border rounded w-40">
            </div>
            <div>
                <label class="text-xs text-gray-600">Ref (opsional)</label>
                <input name="reference" class="px-3 py-2 border rounded w-56">
            </div>
            <button class="px-4 py-2 rounded bg-gray-900 text-white">Checkout</button>
        </form>
    @endif

    {{-- Cancel --}}
    @if (!in_array($reservation->status, ['completed','cancelled'], true))
        <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}"
              onsubmit="return confirm('Batalkan reservasi ini?')">
            @csrf
            <button class="px-4 py-2 rounded border">Cancel</button>
        </form>
    @endif
</div>
@endsection