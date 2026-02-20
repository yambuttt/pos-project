@extends('layouts.kasir')
@section('title','Dashboard Kasir')

@section('body')
  <div class="grid grid-cols-1 gap-6">

    {{-- Header --}}
    <div class="rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-6">
      <div class="text-lg font-semibold">Halo, {{ auth()->user()->name }}</div>
      <div class="mt-1 text-sm text-white/60">Rekap penjualan hari ini (reset otomatis jam 00:00).</div>

      <a href="{{ route('kasir.sales.create') }}"
        class="mt-5 inline-flex items-center justify-center rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
        + Mulai Transaksi
      </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-2xl p-5">
        <div class="text-xs text-white/60">Transaksi Hari Ini</div>
        <div class="mt-1 text-xl font-semibold">{{ number_format($summary['trx_count']) }}</div>
      </div>

      <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-2xl p-5">
        <div class="text-xs text-white/60">Omzet Total (incl. pajak)</div>
        <div class="mt-1 text-xl font-semibold">Rp {{ number_format($summary['omzet_total'], 0, ',', '.') }}</div>
      </div>

      <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-2xl p-5">
        <div class="text-xs text-white/60">QRIS (total)</div>
        <div class="mt-1 text-xl font-semibold">Rp {{ number_format($summary['omzet_qris'], 0, ',', '.') }}</div>
        <div class="mt-1 text-[11px] text-white/50">Non-tunai (tidak masuk laci kasir)</div>
      </div>

      <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-2xl p-5">
        <div class="text-xs text-white/60">Tunai harus ada di laci</div>
        <div class="mt-1 text-xl font-semibold">Rp {{ number_format($summary['cash_net'], 0, ',', '.') }}</div>
        <div class="mt-1 text-[11px] text-white/50">Cash masuk - kembalian</div>
      </div>
    </div>

    {{-- Chart --}}
    <div class="rounded-[26px] border border-white/10 bg-white/5 backdrop-blur-2xl p-6">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-base font-semibold">Grafik Penjualan Hari Ini (per jam)</div>
          <div class="text-sm text-white/60">Bar = omzet, Line = jumlah transaksi</div>
        </div>
      </div>

      <div class="mt-4">
        <canvas id="kasirChart" height="90"></canvas>
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

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              type: 'bar',
              label: 'Omzet',
              data: totals,
              borderWidth: 0,
              yAxisID: 'y',
            },
            {
              type: 'line',
              label: 'Transaksi',
              data: counts,
              borderWidth: 2,
              tension: 0.3,
              yAxisID: 'y1',
            }
          ]
        },
        options: {
          responsive: true,
          interaction: { mode: 'index', intersect: false },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { callback: (v) => 'Rp ' + Number(v).toLocaleString('id-ID') }
            },
            y1: {
              beginAtZero: true,
              position: 'right',
              grid: { drawOnChartArea: false },
              ticks: { precision: 0 }
            }
          }
        }
      });
    })();
  </script>
@endsection