@extends('layouts.admin')
@section('title', 'Riwayat Transaksi')

@section('body')
<div class="flex items-center justify-between gap-3">
  <div class="flex items-center gap-3">
    <button id="openMobileSidebar" type="button"
      class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
    <div>
      <h1 class="text-xl font-semibold">Riwayat Transaksi</h1>
      <p class="text-sm text-white/70">Pantau transaksi + siapa kasirnya. Klik untuk lihat detail.</p>
    </div>
  </div>

  <div class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm backdrop-blur-xl">
  <span class="text-white/70">Total trx:</span>
  <span class="font-semibold">{{ number_format($summary['total_trx']) }}</span>

  <span class="mx-2 text-white/30">•</span>
  <span class="text-white/70">DPP:</span>
  <span class="font-semibold">Rp {{ number_format($summary['sum_subtotal'], 0, ',', '.') }}</span>

  <span class="mx-2 text-white/30">•</span>
  <span class="text-white/70">Pajak 11%:</span>
  <span class="font-semibold">Rp {{ number_format($summary['sum_tax'], 0, ',', '.') }}</span>

  <span class="mx-2 text-white/30">•</span>
  <span class="text-white/70">Omzet:</span>
  <span class="font-semibold">Rp {{ number_format($summary['sum_total'], 0, ',', '.') }}</span>
</div>
</div>

{{-- FILTER --}}
<form method="GET" class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
  <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
    <div class="md:col-span-3">
      <label class="text-xs text-white/70">Kasir</label>
      <select name="cashier_id" class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none">
        <option value="">Semua</option>
        @foreach($cashiers as $c)
          <option value="{{ $c->id }}" @selected(request('cashier_id') == $c->id)>
            {{ $c->name }} ({{ $c->email }})
          </option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-2">
      <label class="text-xs text-white/70">Dari</label>
      <input type="date" name="from" value="{{ request('from') }}"
        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none">
    </div>

    <div class="md:col-span-2">
      <label class="text-xs text-white/70">Sampai</label>
      <input type="date" name="to" value="{{ request('to') }}"
        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none">
    </div>

    <div class="md:col-span-3">
      <label class="text-xs text-white/70">Cari (invoice / id)</label>
      <input type="text" name="q" value="{{ request('q') }}" placeholder="INV-0001 / 12"
        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none placeholder:text-white/40">
    </div>

    <div class="md:col-span-2 flex items-end gap-2">
      <button class="w-full rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">Filter</button>
      <a href="{{ route('admin.sales.index') }}" class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-center hover:bg-white/15">Reset</a>
    </div>
  </div>
</form>

{{-- CHART --}}
<div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
  <div class="flex items-center justify-between">
    <div>
      <div class="text-sm font-semibold">Grafik Omzet (Harian)</div>
      <div class="text-xs text-white/60">Default 14 hari terakhir (atau ikut filter tanggal).</div>
    </div>
    <div class="text-xs text-white/60">Bar = omzet, Line = jumlah transaksi</div>
  </div>

  <div class="mt-4 rounded-2xl border border-white/15 bg-white/5 p-4">
    <canvas id="salesChart" height="110"></canvas>
  </div>
</div>

{{-- LIST --}}
<div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
  <div class="flex items-center justify-between">
    <div>
      <div class="text-sm font-semibold">Daftar Transaksi</div>
      <div class="text-xs text-white/60">Menampilkan {{ $sales->count() }} dari {{ $sales->total() }}</div>
    </div>
  </div>

  {{-- Desktop Table --}}
  <div class="mt-4 hidden sm:block overflow-hidden rounded-2xl border border-white/15">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[980px] text-left text-sm">
        <thead class="bg-white/10 text-xs text-white/70">
          <tr>
            <th class="px-4 py-3">Waktu</th>
            <th class="px-4 py-3">Invoice</th>
            <th class="px-4 py-3">Kasir</th>
            <th class="px-4 py-3">Total</th>
            <th class="px-4 py-3">Bayar</th>
            <th class="px-4 py-3">Pajak</th>
            <th class="px-4 py-3">Kembalian</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/10">

          @forelse($sales as $s)
            <tr class="hover:bg-white/5">
                        @php
  $dpp = (float) ($s->items_subtotal ?? 0);
  $tax = max(0, (float) ($s->total_amount ?? 0) - $dpp);
@endphp
              <td class="px-4 py-3 text-white/80">
                {{ $s->created_at?->format('d M Y H:i') }}
              </td>
              <td class="px-4 py-3 font-semibold">
                {{ $s->invoice_no ?? ('#'.$s->id) }}
              </td>
              <td class="px-4 py-3">
                <div class="font-medium">{{ $s->cashier->name ?? '-' }}</div>
                <div class="text-xs text-white/60">{{ $s->cashier->email ?? '' }}</div>
              </td>
              <td class="px-4 py-3 font-semibold">Rp {{ number_format($s->total_amount ?? 0, 0, ',', '.') }}</td>
              <td class="px-4 py-3 text-white/80">Rp {{ number_format($s->paid_amount ?? 0, 0, ',', '.') }}</td>
              <td class="px-4 py-3 text-white/80">Rp {{ number_format($tax, 0, ',', '.') }}</td>
              <td class="px-4 py-3 text-white/80">Rp {{ number_format($s->change_amount ?? 0, 0, ',', '.') }}</td>
              <td class="px-4 py-3 text-right">
                <a href="{{ route('admin.sales.show', $s->id) }}"
                   class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold hover:bg-white/15">
                  Detail
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-4 py-6 text-white/70" colspan="7">Belum ada transaksi.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Mobile Cards --}}
  <div class="mt-4 space-y-3 sm:hidden">
    @forelse($sales as $s)
      <a href="{{ route('admin.sales.show', $s->id) }}"
         class="block rounded-2xl border border-white/15 bg-white/10 p-4 hover:bg-white/15">
        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="text-sm font-semibold">{{ $s->invoice_no ?? ('#'.$s->id) }}</div>
            <div class="text-xs text-white/60">{{ $s->created_at?->format('d M Y H:i') }}</div>
          </div>
          <div class="text-sm font-semibold">Rp {{ number_format($s->total_amount ?? 0, 0, ',', '.') }}</div>
        </div>

        <div class="mt-3 rounded-xl border border-white/15 bg-white/5 p-3">
          <div class="text-xs text-white/60">Kasir</div>
          <div class="text-sm font-medium">{{ $s->cashier->name ?? '-' }}</div>
          <div class="text-xs text-white/60">{{ $s->cashier->email ?? '' }}</div>

          <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-white/70">
            <div>Bayar: <b class="text-white/90">Rp {{ number_format($s->paid_amount ?? 0, 0, ',', '.') }}</b></div>
            <div>Kembali: <b class="text-white/90">Rp {{ number_format($s->change_amount ?? 0, 0, ',', '.') }}</b></div>
          </div>
        </div>
      </a>
    @empty
      <div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-sm text-white/70">
        Belum ada transaksi.
      </div>
    @endforelse
  </div>

  <div class="mt-5">
    {{ $sales->links() }}
  </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  (function(){
    const labels = @json($labels);
    const totals = @json($totals);
    const counts = @json($counts);

    const ctx = document.getElementById('salesChart');
    if(!ctx) return;

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
          },
          {
            type: 'line',
            label: 'Transaksi',
            data: counts,
            tension: 0.35,
            pointRadius: 2,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { labels: { color: 'rgba(255,255,255,.85)' } },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const v = ctx.raw ?? 0;
                if(ctx.dataset.label === 'Omzet') return 'Omzet: Rp ' + Number(v).toLocaleString('id-ID');
                return 'Transaksi: ' + Number(v).toLocaleString('id-ID');
              }
            }
          }
        },
        scales: {
          x: { ticks: { color: 'rgba(255,255,255,.7)' }, grid: { color: 'rgba(255,255,255,.08)' } },
          y: { ticks: { color: 'rgba(255,255,255,.7)' }, grid: { color: 'rgba(255,255,255,.08)' } },
        }
      }
    });
  })();
</script>
@endsection
