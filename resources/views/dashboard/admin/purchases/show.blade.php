@extends('layouts.admin')
@section('title', 'Detail Purchase')

@section('body')
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button id="openMobileSidebar" type="button"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
            <div>
                <h1 class="text-xl font-semibold">Detail Purchase</h1>
                <p class="text-sm text-white/70">
                    Invoice: <b>{{ $purchase->invoice_no ?? ('#' . $purchase->id) }}</b>
                </p>
            </div>
        </div>
        <a href="{{ route('admin.purchases.export.pdf', $purchase) }}"
            class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
            Export PDF
        </a>

        <a href="{{ route('admin.purchases.index') }}"
            class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
            ← Kembali
        </a>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.2fr_0.8fr]">
        {{-- LEFT: Items --}}
        <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Items</div>
                    <div class="text-xs text-white/60">Detail bahan yang dibeli</div>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto rounded-2xl border border-white/15">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <table class="w-full min-w-[820px] text-left text-sm">
                        <thead class="bg-white/10 text-xs text-white/70">
                            <tr>
                                <th class="px-4 py-3">Bahan</th>
                                <th class="px-4 py-3">Qty</th>
                                <th class="px-4 py-3">Unit Cost</th>
                                <th class="px-4 py-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($purchase->items ?? [] as $it)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $it->rawMaterial->name ?? '-' }}</div>
                                        <div class="text-xs text-white/60">{{ $it->rawMaterial->unit ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-semibold">
                                        {{ rtrim(rtrim(number_format($it->qty ?? 0, 3, '.', ''), '0'), '.') }}
                                    </td>
                                    <td class="px-4 py-3">Rp {{ number_format($it->unit_cost ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($it->subtotal ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-white/70">Item belum ada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>
        </div>

        {{-- RIGHT: Info --}}
        <div class="space-y-5">
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
                <div class="text-sm font-semibold">Info</div>

                <div class="mt-3 grid grid-cols-1 gap-2">
                    <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                        <div class="text-sm text-white/70">Tanggal</div>
                        <div class="text-sm font-semibold">
                            {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                        <div class="text-sm text-white/70">Sumber</div>
                        <div class="text-sm font-semibold">
                            @if($purchase->source_type === 'supplier')
                                {{ $purchase->supplier?->name ?? '-' }}
                            @else
                                {{ $purchase->source_name ?? '-' }}
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                        <div class="text-sm text-white/70">Tipe</div>
                        <div class="text-sm font-semibold uppercase">{{ $purchase->source_type ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
                <div class="text-sm font-semibold">Dibuat oleh</div>
                <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 p-4">
                    <div class="font-semibold">{{ $purchase->creator?->name ?? '-' }}</div>
                    <div class="text-xs text-white/60">{{ $purchase->creator?->email ?? '' }}</div>
                    <div class="mt-2 text-xs text-white/60">
                        Waktu: <b class="text-white/80">{{ $purchase->created_at?->format('d M Y H:i') }}</b>
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
                @php
                    $sumItems = (float) ($purchase->items?->sum('subtotal') ?? 0);
                    $total = (float) ($purchase->total_amount ?? 0);
                @endphp

                <div class="text-sm font-semibold">Total</div>

                <div class="mt-3 grid grid-cols-1 gap-2">
                    <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                        <div class="text-sm text-white/70">Subtotal Items</div>
                        <div class="text-sm font-semibold">Rp {{ number_format($sumItems, 0, ',', '.') }}</div>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                        <div class="text-sm text-white/70">Total Purchase</div>
                        <div class="text-sm font-semibold">Rp {{ number_format($total, 0, ',', '.') }}</div>
                    </div>
                </div>

                @if(abs($total - $sumItems) > 0.0001)
                    <div class="mt-3 text-xs text-white/60">
                        Catatan: total_amount berbeda dengan jumlah subtotal items.
                    </div>
                @endif
            </div>

            <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
                <div class="text-sm font-semibold">Catatan</div>
                <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm text-white/80">
                    {{ $purchase->note ?? '-' }}
                </div>
            </div>
        </div>
    </div>
@endsection