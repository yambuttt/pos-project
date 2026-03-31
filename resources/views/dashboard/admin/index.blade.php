@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('body')
    <div class="flex items-center justify-between gap-3">
        <button id="openMobileSidebar" type="button"
            class="inline-flex items-center justify-center rounded-xl border border-yellow-500/20 bg-white/[0.04] px-3 py-2 text-sm text-white backdrop-blur-xl hover:bg-white/[0.08] lg:hidden"
            title="Menu">☰</button>

        <div class="rounded-2xl border border-yellow-500/16 bg-white/[0.03] px-4 py-3 backdrop-blur-xl">
            <div class="text-xs uppercase tracking-[0.18em] text-yellow-500">Admin Panel</div>
            <div class="text-sm font-semibold text-white">POS Dashboard</div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <div class="hidden rounded-2xl border border-yellow-500/16 bg-white/[0.03] px-4 py-2 text-sm text-white/85 backdrop-blur-xl sm:block">
                {{ auth()->user()->name ?? 'Admin' }} • <span class="text-white/50">{{ auth()->user()->email }}</span>
            </div>
            <button
                class="rounded-xl border border-yellow-500/16 bg-white/[0.03] px-3 py-2 text-sm text-yellow-500 backdrop-blur-xl hover:bg-white/[0.08]">🔔</button>
        </div>
    </div>

    @php
        $isUp = $pctSales >= 0;
        $pctText = number_format(abs($pctSales), 1);
    @endphp

    <section class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
            <div class="text-xs uppercase tracking-[0.16em] text-white/50">Penjualan Hari Ini</div>
            <div class="mt-2 text-2xl font-semibold text-white">Rp {{ number_format($omzetToday, 0, ',', '.') }}</div>
            <div class="mt-3 text-xs {{ $isUp ? 'text-emerald-300' : 'text-rose-300' }}">
                {{ $isUp ? '▲' : '▼' }} {{ $pctText }}% vs kemarin
            </div>
        </div>

        <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
            <div class="text-xs uppercase tracking-[0.16em] text-white/50">Cash di Laci</div>
            <div class="mt-2 text-2xl font-semibold text-white">Rp {{ number_format($cashNet, 0, ',', '.') }}</div>
            <div class="mt-3 text-xs text-white/55">Cash masuk − kembalian</div>
        </div>

        <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
            <div class="text-xs uppercase tracking-[0.16em] text-white/50">QRIS Hari Ini</div>
            <div class="mt-2 text-2xl font-semibold text-white">Rp {{ number_format($omzetQris, 0, ',', '.') }}</div>
            <div class="mt-3 text-xs text-white/55">Non tunai</div>
        </div>

        <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
            <div class="text-xs uppercase tracking-[0.16em] text-white/50">Transaksi Hari Ini</div>
            <div class="mt-2 text-2xl font-semibold text-white">{{ number_format($trxToday) }}</div>
            <div class="mt-3 text-xs text-white/55">
                Kemarin: {{ number_format($trxYesterday) }} • Produk: {{ number_format($totalProducts) }}
            </div>
        </div>
    </section>

    <section class="mt-5 grid grid-cols-1 gap-5 2xl:grid-cols-[1.65fr_1fr]">
        <div class="rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-6 backdrop-blur-xl sm:p-7">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-white">Grafik Omzet</div>
                    <div class="text-xs text-white/50">14 hari terakhir • omzet & jumlah transaksi</div>
                </div>
                <div class="text-xs uppercase tracking-[0.14em] text-yellow-500">Paid Only</div>
            </div>

            <div class="mt-4 rounded-2xl border border-yellow-500/10 bg-white/[0.02] p-4">
                <div class="h-[280px] sm:h-[320px]">
                    <canvas id="adminSalesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-6 backdrop-blur-xl sm:p-7">
                <div class="text-sm font-semibold text-white">Perputaran Uang</div>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-white/60">Omzet Cash</span>
                        <span class="font-semibold text-white">Rp {{ number_format($omzetCash, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/60">Omzet QRIS</span>
                        <span class="font-semibold text-white">Rp {{ number_format($omzetQris, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/60">Pajak Terkumpul</span>
                        <span class="font-semibold text-white">Rp {{ number_format($taxToday, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-white/60">Belum dibayar di sistem</span>
                        <span class="text-right font-semibold text-white">{{ number_format($unpaidCount) }} • Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/60">Total Cash</span>
                        <span class="font-semibold text-white">Rp {{ number_format($totalCashAll, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/60">Total QRIS</span>
                        <span class="font-semibold text-white">Rp {{ number_format($totalQrisAll, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-yellow-500/10 bg-white/[0.02] p-4 text-xs leading-6 text-white/50">
                    Cash net adalah uang fisik yang seharusnya ada di laci setelah cash masuk dikurangi kembalian.
                </div>
            </div>

            <div class="rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-6 backdrop-blur-xl sm:p-7">
                <div class="text-sm font-semibold text-white">Produk Populer Hari Ini</div>

                @if($popularProduct)
                    <div class="mt-4 rounded-2xl border border-yellow-500/12 bg-white/[0.03] p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-white">{{ $popularProduct->product->name ?? '—' }}</div>
                                <div class="mt-1 text-xs text-white/55">Qty: {{ number_format($popularProduct->total_qty) }}</div>
                            </div>
                            <div class="rounded-xl border border-yellow-500/20 bg-yellow-500/10 px-3 py-2 text-xs font-semibold text-yellow-500">
                                #1
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-4 text-sm text-white/55">Belum ada transaksi paid hari ini.</div>
                @endif
            </div>
        </div>
    </section>

    <section class="mt-5 grid grid-cols-1 gap-5 2xl:grid-cols-[1.35fr_1fr]">
        <div class="rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-6 backdrop-blur-xl sm:p-7">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-white">Rank Produk Paling Sering Dipesan</div>
                    <div class="text-xs text-white/50">Berdasarkan total qty hari ini</div>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto rounded-2xl border border-yellow-500/10">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-white/[0.04] text-xs uppercase tracking-[0.14em] text-white/45">
                        <tr>
                            <th class="px-4 py-3">Rank</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Omzet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-yellow-500/8">
                        @forelse($topProductsToday as $i => $row)
                            <tr class="hover:bg-white/[0.03]">
                                <td class="px-4 py-3 font-semibold text-yellow-500">#{{ $i + 1 }}</td>
                                <td class="px-4 py-3 text-white">{{ $row->product->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-white/80">{{ number_format($row->total_qty) }}</td>
                                <td class="px-4 py-3 text-white">Rp {{ number_format((int) $row->total_subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-sm text-white/55">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-6 backdrop-blur-xl sm:p-7">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-white">Penjualan Terbaru</div>
                    <div class="text-xs text-white/50">8 transaksi terakhir</div>
                </div>
                <a href="{{ route('admin.sales.index') }}"
                    class="rounded-xl border border-yellow-500/16 bg-white/[0.03] px-3 py-2 text-xs text-white/80 hover:bg-white/[0.06]">
                    View all
                </a>
            </div>

            <div class="mt-5 space-y-3">
                @foreach($latestSales as $s)
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-yellow-500/10 bg-white/[0.03] px-4 py-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-white">{{ $s->invoice_no ?? ('#' . $s->id) }}</div>
                            <div class="mt-1 text-xs text-white/50">
                                {{ $s->cashier->name ?? '-' }} • {{ $s->created_at?->diffForHumans() }} • <span class="uppercase">{{ $s->payment_method }}</span>
                            </div>
                        </div>
                        <div class="shrink-0 text-sm font-semibold text-yellow-500">
                            Rp {{ number_format($s->total_amount ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

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
                        {
                            type: 'bar',
                            label: 'Omzet',
                            data: totals,
                            backgroundColor: 'rgba(234,179,8,0.45)',
                            borderColor: 'rgba(234,179,8,0.95)',
                            borderWidth: 1,
                            borderRadius: 8,
                        },
                        {
                            type: 'line',
                            label: 'Transaksi',
                            data: counts,
                            yAxisID: 'y2',
                            borderColor: 'rgba(255,255,255,0.85)',
                            backgroundColor: 'rgba(255,255,255,0.20)',
                            tension: 0.35,
                            pointRadius: 3,
                            pointHoverRadius: 4,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: 'rgba(255,255,255,0.75)'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: 'rgba(255,255,255,0.55)' },
                            grid: { color: 'rgba(255,255,255,0.06)' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: 'rgba(255,255,255,0.55)' },
                            grid: { color: 'rgba(255,255,255,0.06)' }
                        },
                        y2: {
                            beginAtZero: true,
                            position: 'right',
                            ticks: { color: 'rgba(255,255,255,0.55)' },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        })();
    </script>
@endsection