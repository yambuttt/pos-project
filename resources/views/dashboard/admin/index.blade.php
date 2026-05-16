@extends('layouts.admin')
@section('title', 'Admin Overview')

@section('body')
    @php
        $isUp = $pctSales >= 0;
        $pctText = number_format(abs($pctSales), 1);
    @endphp

    <!-- TOP STATISTICS -->
    <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Sales Today -->
        <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gold-primary/5 rounded-full blur-2xl group-hover:bg-gold-primary/10 transition-colors"></div>
            <p class="text-white/40 text-[10px] uppercase tracking-widest mb-1 font-bold">Penjualan Hari Ini</p>
            <h3 class="text-2xl font-bold text-gold-gradient mb-4">Rp {{ number_format($omzetToday, 0, ',', '.') }}</h3>
            <div class="flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 rounded-full {{ $isUp ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $isUp ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}" />
                    </svg>
                </span>
                <span class="text-xs {{ $isUp ? 'text-green-400' : 'text-red-400' }} font-bold">{{ $pctText }}%</span>
                <span class="text-white/20 text-[10px]">vs Kemarin</span>
            </div>
        </div>

        <!-- Cash in Drawer -->
        <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gold-primary/5 rounded-full blur-2xl group-hover:bg-gold-primary/10 transition-colors"></div>
            <p class="text-white/40 text-[10px] uppercase tracking-widest mb-1 font-bold">Cash di Laci</p>
            <h3 class="text-2xl font-bold text-white mb-4">Rp {{ number_format($cashNet, 0, ',', '.') }}</h3>
            <p class="text-white/20 text-[10px] italic">Fisik yang seharusnya ada</p>
        </div>

        <!-- QRIS Today -->
        <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gold-primary/5 rounded-full blur-2xl group-hover:bg-gold-primary/10 transition-colors"></div>
            <p class="text-white/40 text-[10px] uppercase tracking-widest mb-1 font-bold">QRIS Hari Ini</p>
            <h3 class="text-2xl font-bold text-white mb-4">Rp {{ number_format($omzetQris, 0, ',', '.') }}</h3>
            <p class="text-white/20 text-[10px]">Pembayaran non-tunai</p>
        </div>

        <!-- Total Transactions -->
        <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gold-primary/5 rounded-full blur-2xl group-hover:bg-gold-primary/10 transition-colors"></div>
            <p class="text-white/40 text-[10px] uppercase tracking-widest mb-1 font-bold">Transaksi Hari Ini</p>
            <h3 class="text-2xl font-bold text-white mb-4">{{ number_format($trxToday) }} Trx</h3>
            <p class="text-white/20 text-[10px]">Kemarin: {{ number_format($trxYesterday) }}</p>
        </div>
    </section>

    <!-- CHARTS AND POPULAR -->
    <section class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 glass-panel p-8 rounded-[2.5rem]">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h4 class="text-lg font-bold">Performa Penjualan</h4>
                    <p class="text-xs text-white/40">Visualisasi omzet & frekuensi transaksi 14 hari terakhir</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-gold-primary"></span>
                        <span class="text-[10px] text-white/60 font-bold uppercase tracking-widest">Omzet</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-white/40"></span>
                        <span class="text-[10px] text-white/60 font-bold uppercase tracking-widest">Trx</span>
                    </div>
                </div>
            </div>
            <div class="h-[320px]">
                <canvas id="adminSalesChart"></canvas>
            </div>
        </div>

        <!-- Popular Products -->
        <div class="glass-panel p-8 rounded-[2.5rem] flex flex-col">
            <h4 class="text-lg font-bold mb-6">Produk Populer</h4>
            
            <div class="flex-1 space-y-4">
                @if($popularProduct)
                    <div class="glass-card p-5 rounded-2xl border-l-4 border-l-gold-primary">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gold-primary font-bold text-xs uppercase tracking-tighter">Terlaris #1</span>
                            <span class="px-2 py-1 bg-gold-primary/10 text-gold-primary text-[10px] font-bold rounded-lg">Top Choice</span>
                        </div>
                        <h5 class="font-bold text-lg mb-1">{{ $popularProduct->product->name ?? '—' }}</h5>
                        <p class="text-white/40 text-xs">Terjual {{ number_format($popularProduct->total_qty) }} unit hari ini</p>
                    </div>
                @endif

                <div class="space-y-3">
                    <p class="text-[10px] text-white/30 uppercase tracking-widest font-bold px-2">Top Performer Lainnya</p>
                    @foreach($topProductsToday->slice(1, 3) as $row)
                        <div class="flex items-center gap-4 px-4 py-3 glass-card rounded-2xl hover:translate-x-1 transition-transform">
                            <div class="w-8 h-8 rounded-xl bg-white/5 flex items-center justify-center font-bold text-white/60 text-xs">
                                {{ $loop->iteration + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold truncate">{{ $row->product->name ?? '-' }}</p>
                                <p class="text-[10px] text-white/40">{{ number_format($row->total_qty) }} terjual</p>
                            </div>
                            <p class="text-xs font-bold text-gold-primary">Rp {{ number_format((int) $row->total_subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('admin.products.index') }}" class="mt-6 w-full py-3 rounded-2xl border border-white/5 text-center text-xs font-bold text-white/40 hover:text-gold-primary hover:border-gold-primary/30 transition-all">Lihat Semua Produk</a>
        </div>
    </section>

    <!-- TABLES AND RECENT SALES -->
    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Rank Table -->
        <div class="glass-panel p-8 rounded-[2.5rem]">
            <h4 class="text-lg font-bold mb-6">Rank Penjualan Produk</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] text-white/30 uppercase tracking-widest border-b border-white/5">
                            <th class="pb-4 font-bold">Rank</th>
                            <th class="pb-4 font-bold">Nama Produk</th>
                            <th class="pb-4 font-bold text-center">Qty</th>
                            <th class="pb-4 font-bold text-right">Total Omzet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($topProductsToday as $i => $row)
                            <tr class="group hover:bg-white/[0.02] transition-colors">
                                <td class="py-4">
                                    <span class="w-6 h-6 rounded-lg bg-gold-primary/10 text-gold-primary text-xs font-bold flex items-center justify-center">#{{ $i + 1 }}</span>
                                </td>
                                <td class="py-4 font-bold text-white/80 group-hover:text-white">{{ $row->product->name ?? '-' }}</td>
                                <td class="py-4 text-center text-white/60 font-medium">{{ number_format($row->total_qty) }}</td>
                                <td class="py-4 text-right text-gold-primary font-bold">Rp {{ number_format((int) $row->total_subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-10 text-center text-white/20 italic">Belum ada data penjualan hari ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="glass-panel p-8 rounded-[2.5rem]">
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-lg font-bold">Transaksi Terbaru</h4>
                <a href="{{ route('admin.sales.index') }}" class="text-[10px] font-bold text-gold-primary uppercase tracking-widest hover:underline">View Ledger</a>
            </div>
            
            <div class="space-y-4">
                @foreach($latestSales as $s)
                    <div class="flex items-center gap-4 p-4 glass-card rounded-2xl relative overflow-hidden group">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-white/10 to-transparent flex items-center justify-center text-white/60 group-hover:text-gold-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="text-sm font-bold truncate">{{ $s->invoice_no ?? ('#' . $s->id) }}</h5>
                            <p class="text-[10px] text-white/30 font-medium uppercase tracking-tight">
                                {{ $s->cashier->name ?? '-' }} • {{ $s->created_at?->diffForHumans() }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-white mb-1">Rp {{ number_format($s->total_amount ?? 0, 0, ',', '.') }}</p>
                            <span class="px-2 py-0.5 bg-white/5 text-[8px] font-bold text-white/40 uppercase rounded border border-white/5">{{ $s->payment_method }}</span>
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
                            backgroundColor: 'rgba(212, 175, 55, 0.4)',
                            borderColor: 'rgba(212, 175, 55, 1)',
                            borderWidth: 0,
                            borderRadius: 12,
                            barThickness: 15,
                        },
                        {
                            type: 'line',
                            label: 'Transaksi',
                            data: counts,
                            yAxisID: 'y2',
                            borderColor: 'rgba(255, 255, 255, 0.3)',
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#D4AF37',
                            borderWidth: 2,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            ticks: { 
                                color: 'rgba(255, 255, 255, 0.2)',
                                font: { size: 10, weight: 'bold' }
                            },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { 
                                color: 'rgba(255, 255, 255, 0.2)',
                                font: { size: 10 },
                                callback: function(value) { return 'Rp ' + value.toLocaleString(); }
                            },
                            grid: { color: 'rgba(255, 255, 255, 0.03)' }
                        },
                        y2: {
                            beginAtZero: true,
                            position: 'right',
                            ticks: { display: false },
                            grid: { display: false }
                        }
                    }
                }
            });
        })();
    </script>
@endsection