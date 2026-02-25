@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('body')
    {{-- TOP BAR (biarkan punyamu, ini tetap cocok) --}}
    <div class="flex items-center justify-between gap-3">
        <button id="openMobileSidebar" type="button"
            class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden"
            title="Menu">☰</button>

        <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-2xl">
            <div class="text-xs text-white/70">Admin Panel</div>
            <div class="text-sm font-semibold">POS Dashboard</div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <div
                class="hidden sm:block rounded-2xl border border-white/20 bg-white/10 px-4 py-2 text-sm text-white/85 backdrop-blur-2xl">
                {{ auth()->user()->name ?? 'Admin' }} • <span class="text-white/70">{{ auth()->user()->email }}</span>
            </div>
            <button
                class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15">🔔</button>
        </div>
    </div>

    @php
        $isUp = $pctSales >= 0;
        $pctText = number_format(abs($pctSales), 1);
      @endphp

    {{-- STAT CARDS --}}
    <section class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-[22px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
            <div class="text-xs text-white/70">Penjualan Hari Ini</div>
            <div class="mt-1 text-2xl font-semibold">Rp {{ number_format($omzetToday, 0, ',', '.') }}</div>
            <div class="mt-2 text-xs {{ $isUp ? 'text-emerald-200' : 'text-rose-200' }}">
                {{ $isUp ? '▲' : '▼' }} {{ $pctText }}% vs kemarin
            </div>
        </div>

        <div class="rounded-[22px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
            <div class="text-xs text-white/70">Cash di Laci (net)</div>
            <div class="mt-1 text-2xl font-semibold">Rp {{ number_format($cashNet, 0, ',', '.') }}</div>
            <div class="mt-2 text-xs text-white/65">Cash masuk − kembalian</div>
        </div>

        <div class="rounded-[22px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
            <div class="text-xs text-white/70">QRIS (hari ini)</div>
            <div class="mt-1 text-2xl font-semibold">Rp {{ number_format($omzetQris, 0, ',', '.') }}</div>
            <div class="mt-2 text-xs text-white/65">Non-tunai</div>
        </div>

        <div class="rounded-[22px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
            <div class="text-xs text-white/70">Transaksi Hari Ini</div>
            <div class="mt-1 text-2xl font-semibold">{{ number_format($trxToday) }}</div>
            <div class="mt-2 text-xs text-white/65">
                Kemarin: {{ number_format($trxYesterday) }} • Total produk: {{ number_format($totalProducts) }}
            </div>
        </div>
    </section>

    {{-- CHART + MONEY FLOW --}}
    <section class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.6fr_1fr]">
        <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Grafik Omzet (14 hari terakhir)</div>
                    <div class="text-xs text-white/70">Bar = omzet, Line = jumlah transaksi</div>
                </div>
                <div class="text-xs text-white/60">Paid only</div>
            </div>

            <div class="mt-4 rounded-2xl border border-white/15 bg-white/5 p-4">
                <canvas id="adminSalesChart" height="110"></canvas>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                <div class="text-sm font-semibold">Perputaran Uang (ringkas)</div>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-white/70">Omzet Cash</span>
                        <span class="font-semibold">Rp {{ number_format($omzetCash, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/70">Omzet QRIS</span>
                        <span class="font-semibold">Rp {{ number_format($omzetQris, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/70">Pajak terkumpul (estimasi)</span>
                        <span class="font-semibold">Rp {{ number_format($taxToday, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/70">Order belum dibayar di sistem</span>
                        <span class="font-semibold">{{ number_format($unpaidCount) }} • Rp
                            {{ number_format($unpaidTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/70">Total Cash (keseluruhan)</span>
                        <span class="font-semibold">Rp {{ number_format($totalCashAll, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-white/70">Total QRIS (keseluruhan)</span>
                        <span class="font-semibold">Rp {{ number_format($totalQrisAll, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl border border-white/15 bg-white/5 p-4 text-xs text-white/70">
                    Catatan: “Cash net” adalah uang fisik yang seharusnya ada di laci (cash masuk dikurangi kembalian).
                </div>
            </div>

            {{-- Popular product --}}
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                <div class="text-sm font-semibold">Popular Produk (hari ini)</div>

                @if($popularProduct)
                    <div class="mt-4 rounded-2xl border border-white/15 bg-white/10 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold">{{ $popularProduct->product->name ?? '—' }}</div>
                                <div class="text-xs text-white/70">Qty: {{ number_format($popularProduct->total_qty) }}</div>
                            </div>
                            <div class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs">🔥 #1</div>
                        </div>
                    </div>
                @else
                    <div class="mt-4 text-sm text-white/70">Belum ada transaksi paid hari ini.</div>
                @endif
            </div>
        </div>
    </section>

    {{-- LOWER GRID: RANKING + LATEST SALES --}}
    <section class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.4fr_1fr]">
        {{-- Ranking produk --}}
        <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Rank Produk Paling Sering Dipesan (hari ini)</div>
                    <div class="text-xs text-white/70">Berdasarkan total qty</div>
                </div>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-white/15">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white/10 text-xs text-white/70">
                        <tr>
                            <th class="px-4 py-3">Rank</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Omzet (subtotal)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($topProductsToday as $i => $row)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3 font-semibold">#{{ $i + 1 }}</td>
                                <td class="px-4 py-3">{{ $row->product->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ number_format($row->total_qty) }}</td>
                                <td class="px-4 py-3">Rp {{ number_format((int) $row->total_subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-sm text-white/70">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Latest Sales --}}
        <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Penjualan Terbaru</div>
                    <div class="text-xs text-white/70">8 transaksi terakhir</div>
                </div>
                <a href="{{ route('admin.sales.index') }}"
                    class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs backdrop-blur-xl hover:bg-white/15">
                    View all
                </a>
            </div>

            <div class="mt-5 space-y-3">
                @foreach($latestSales as $s)
                    <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                        <div>
                            <div class="text-sm font-semibold">{{ $s->invoice_no ?? ('#' . $s->id) }}</div>
                            <div class="text-xs text-white/70">
                                {{ $s->cashier->name ?? '-' }} • {{ $s->created_at?->diffForHumans() }}
                                • <span class="uppercase">{{ $s->payment_method }}</span>
                            </div>
                        </div>
                        <div class="text-sm font-semibold">Rp {{ number_format($s->total_amount ?? 0, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const labels = @json($labels);
            const totals = @json($totals);
            const counts = @json($counts);

            const ctx = document.getElementById('adminSalesChart');
            if (!ctx) return;

            new Chart(ctx, {
                data: {
                    labels,
                    datasets: [
                        { type: 'bar', label: 'Omzet', data: totals },
                        { type: 'line', label: 'Transaksi', data: counts, yAxisID: 'y2' },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true },
                        y2: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
                    }
                }
            });
        })();
    </script>
@endsection