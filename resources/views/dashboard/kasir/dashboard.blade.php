@extends('layouts.kasir')
@section('title','Dashboard Kasir')

@section('body')
  <div class="max-w-7xl mx-auto space-y-8 animate-fade-up">
    
    {{-- Welcome Header --}}
    <div class="glass-panel rounded-[2.5rem] p-8 lg:p-10 relative overflow-hidden group">
      <div class="absolute -right-20 -top-20 w-80 h-80 bg-accent-gold/10 rounded-full blur-[80px] group-hover:bg-accent-gold/20 transition-all duration-700"></div>
      
      <div class="relative z-10">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-white/50 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
          <span class="w-2 h-2 rounded-full bg-accent-gold animate-pulse shadow-[0_0_8px_rgba(234,179,8,0.5)]"></span>
          Terminal Aktif
        </div>
        <h1 class="text-4xl lg:text-5xl font-bold tracking-tight mb-2">Halo, <span class="text-accent-gold">{{ explode(' ', auth()->user()->name)[0] }}</span>!</h1>
        <p class="text-white/50 max-w-lg leading-relaxed">Rekap penjualan hari ini diperbarui secara real-time. Pastikan semua transaksi tercatat dengan benar.</p>
        
        <div class="mt-8">
          <a href="{{ route('kasir.sales.create') }}" class="btn-premium-primary text-sm px-8">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Mulai Transaksi Baru
          </a>
        </div>
      </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @php
        $stats = [
          ['label' => 'Transaksi Hari Ini', 'value' => number_format($summary['trx_count']), 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'gold'],
          ['label' => 'Omzet Total', 'value' => 'Rp ' . number_format($summary['omzet_total'], 0, ',', '.'), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'gold'],
          ['label' => 'QRIS (Total)', 'value' => 'Rp ' . number_format($summary['omzet_qris'], 0, ',', '.'), 'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'color' => 'amber'],
          ['label' => 'Uang di Laci', 'value' => 'Rp ' . number_format($summary['cash_net'], 0, ',', '.'), 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'color' => 'gold'],
        ];
      @endphp

      @foreach($stats as $index => $stat)
        <div class="premium-card p-6 stagger-{{ $index + 1 }} border-white/5 hover:border-accent-gold/30 group">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-accent-{{ $stat['color'] }}/10 border border-accent-{{ $stat['color'] }}/20 flex items-center justify-center text-accent-{{ $stat['color'] }} group-hover:scale-110 transition-transform duration-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"></path></svg>
            </div>
            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40">{{ $stat['label'] }}</div>
          </div>
          <div class="text-2xl font-bold tracking-tight text-white">{{ $stat['value'] }}</div>
          <div class="mt-2 h-1 w-full bg-white/5 rounded-full overflow-hidden">
            <div class="h-full bg-accent-{{ $stat['color'] }}/50 rounded-full" style="width: 65%"></div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Charts Section --}}
    <div class="glass-panel rounded-[2.5rem] p-8 lg:p-10 stagger-3">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
          <h2 class="text-2xl font-bold tracking-tight mb-1">Performa Penjualan</h2>
          <p class="text-white/40 text-sm">Grafik transaksi per jam (Hari Ini)</p>
        </div>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-accent-gold shadow-[0_0_8px_rgba(234,179,8,0.6)]"></span>
            <span class="text-xs text-white/60 font-bold uppercase tracking-widest">Omzet</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-white/20"></span>
            <span class="text-xs text-white/40 font-bold uppercase tracking-widest">Transaksi</span>
          </div>
        </div>
      </div>

      <div class="relative h-[400px]">
        <canvas id="kasirChart"></canvas>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    (function () {
      const labels = @json($labels);
      const totals = @json($totals);
      const counts = @json($counts);

      const ctx = document.getElementById('kasirChart');
      if (!ctx) return;

      const gradientGold = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
      gradientGold.addColorStop(0, 'rgba(234, 179, 8, 0.4)');
      gradientGold.addColorStop(1, 'rgba(234, 179, 8, 0)');

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              type: 'bar',
              label: 'Omzet',
              data: totals,
              backgroundColor: gradientGold,
              borderColor: '#eab308',
              borderWidth: 2,
              borderRadius: 8,
              yAxisID: 'y',
            },
            {
              type: 'line',
              label: 'Transaksi',
              data: counts,
              borderColor: 'rgba(255, 255, 255, 0.3)',
              backgroundColor: 'transparent',
              borderWidth: 3,
              borderDash: [5, 5],
              pointBackgroundColor: '#eab308',
              pointBorderColor: '#000',
              pointBorderWidth: 2,
              pointRadius: 4,
              tension: 0.4,
              yAxisID: 'y1',
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { mode: 'index', intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(0,0,0,0.9)',
              titleFont: { size: 13, weight: 'bold' },
              padding: 12,
              cornerRadius: 12,
              borderColor: 'rgba(234, 179, 8, 0.3)',
              borderWidth: 1
            }
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: 'rgba(255, 255, 255, 0.4)', font: { size: 10, weight: 'bold' } }
            },
            y: {
              beginAtZero: true,
              grid: { color: 'rgba(255, 255, 255, 0.03)' },
              ticks: { 
                color: 'rgba(255, 255, 255, 0.3)',
                font: { size: 10 },
                callback: (v) => 'Rp ' + Number(v).toLocaleString('id-ID')
              }
            },
            y1: {
              beginAtZero: true,
              position: 'right',
              grid: { display: false },
              ticks: { 
                color: 'rgba(255, 255, 255, 0.2)',
                font: { size: 10 },
                precision: 0 
              }
            }
          }
        }
      });
    })();
  </script>
@endsection