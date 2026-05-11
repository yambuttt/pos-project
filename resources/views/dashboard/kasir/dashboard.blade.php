@extends('layouts.kasir')
@section('title','Dashboard Kasir')

@section('body')
  <div class="max-w-7xl mx-auto space-y-8 animate-fade-up">
    
    {{-- Welcome Header --}}
    <div class="glass-panel rounded-[2.5rem] p-8 lg:p-10 relative overflow-hidden group">
      <div class="absolute -right-20 -top-20 w-80 h-80 bg-accent-gold/10 rounded-full blur-[80px] group-hover:bg-accent-gold/20 transition-all duration-700"></div>
      
      <div class="relative z-10">
        <div class="flex flex-wrap items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-white/50 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
                  <span class="w-2 h-2 rounded-full {{ $activeShift ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]' }} animate-pulse"></span>
                  {{ $activeShift ? 'Shift Aktif' : 'Shift Belum Dimulai' }}
                </div>
                <h1 class="text-4xl lg:text-5xl font-bold tracking-tight mb-2">Halo, <span class="text-accent-gold">{{ explode(' ', auth()->user()->name)[0] }}</span>!</h1>
                <p class="text-white/50 max-w-lg leading-relaxed">
                    @if($activeShift)
                        Shift dimulai sejak {{ $activeShift->start_time->format('H:i') }}. Modal awal: Rp {{ number_format($activeShift->starting_cash, 0, ',', '.') }}
                    @else
                        Silakan mulai shift Anda untuk dapat melakukan transaksi penjualan.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-4">
                @if(!$activeShift)
                    <button onclick="document.getElementById('startShiftModal').classList.remove('hidden')" class="btn-premium-primary text-sm px-8">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Mulai Shift
                    </button>
                @else
                    <button onclick="document.getElementById('endShiftModal').classList.remove('hidden')" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 px-8 py-3 rounded-2xl font-bold text-sm transition-all duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Akhiri Shift
                    </button>
                    <a href="{{ route('kasir.sales.create') }}" class="btn-premium-primary text-sm px-8">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Mulai Transaksi
                    </a>
                @endif
                <a href="{{ route('kasir.shift.history') }}" class="glass-panel px-8 py-3 rounded-2xl text-white/70 hover:text-white border border-white/10 text-sm font-bold transition-all duration-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Shift
                </a>
            </div>
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

    </div>

    {{-- Start Shift Modal --}}
    <div id="startShiftModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden p-4">
      <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
      <div class="glass-panel w-full max-w-md p-8 relative z-10 border-white/10 rounded-[2rem] animate-scale-up">
        <h3 class="text-2xl font-bold mb-6">Mulai Shift Baru</h3>
        <form action="{{ route('kasir.shift.start') }}" method="POST">
          @csrf
          <div class="space-y-6">
            <div>
              <label class="block text-white/40 text-[10px] font-black uppercase tracking-[0.2em] mb-3">Modal Awal (Cash)</label>
              <div class="relative">
                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-accent-gold font-bold">Rp</span>
                <input type="number" name="starting_cash" required min="0" placeholder="0" 
                  class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-6 text-white font-bold focus:border-accent-gold/50 focus:ring-0 transition-all">
              </div>
            </div>
            <button type="submit" class="btn-premium-primary w-full py-4">Buka Kasir & Mulai Shift</button>
          </div>
        </form>
      </div>
    </div>

    {{-- End Shift Modal --}}
    <div id="endShiftModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden p-4">
      <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
      <div class="glass-panel w-full max-w-lg p-8 relative z-10 border-white/10 rounded-[2rem] animate-scale-up">
        <h3 class="text-2xl font-bold mb-2">Akhiri Shift</h3>
        <p class="text-white/40 text-sm mb-8">Pastikan semua transaksi cash sudah sesuai dengan uang di laci.</p>
        
        <div class="grid grid-cols-2 gap-4 mb-8">
          <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Total Sales Cash</div>
            <div class="text-lg font-bold text-white">Rp {{ number_format($summary['omzet_cash'], 0, ',', '.') }}</div>
          </div>
          <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Total Sales QRIS</div>
            <div class="text-lg font-bold text-white">Rp {{ number_format($summary['omzet_qris'], 0, ',', '.') }}</div>
          </div>
          <div class="col-span-2 bg-accent-gold/10 rounded-2xl p-6 border border-accent-gold/20">
            <div class="text-[10px] font-black uppercase tracking-widest text-accent-gold mb-1">Estimasi Uang di Laci (Modal + Sales Cash)</div>
            <div class="text-2xl font-black text-accent-gold">
              @if($activeShift)
                Rp {{ number_format($activeShift->starting_cash + $summary['omzet_cash'], 0, ',', '.') }}
              @else
                Rp 0
              @endif
            </div>
          </div>
        </div>

        <form action="{{ route('kasir.shift.end') }}" method="POST">
          @csrf
          <button type="submit" class="bg-red-500 hover:bg-red-600 text-white w-full py-4 rounded-2xl font-bold transition-all shadow-lg shadow-red-500/20">Konfirmasi Tutup Kasir & Selesai Shift</button>
        </form>
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