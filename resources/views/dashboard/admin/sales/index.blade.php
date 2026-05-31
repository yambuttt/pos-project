@extends('layouts.admin')
@section('title', 'Riwayat Transaksi')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div class="flex items-center gap-4">
      <button id="openMobileSidebar" type="button"
        class="inline-flex lg:hidden items-center justify-center w-10 h-10 rounded-xl border border-white/10 bg-white/5 text-white/70 hover:bg-white/10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gold-gradient">Riwayat Transaksi</h1>
        <p class="text-[11px] sm:text-sm text-white/40 font-medium italic">Pantau performa penjualan dan <span class="text-gold-primary font-bold not-italic">aktivitas kasir real-time.</span></p>
      </div>
    </div>

    <div class="flex items-center">
       <div class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between sm:justify-start gap-6 backdrop-blur-xl">
          <div class="flex flex-col">
             <span class="text-[8px] font-black text-white/20 uppercase tracking-widest">Total Trx</span>
             <span class="text-xs sm:text-sm font-bold text-white">{{ number_format($summary['total_trx']) }}</span>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div class="flex flex-col">
             <span class="text-[8px] font-black text-gold-primary/40 uppercase tracking-widest">Total Omzet</span>
             <span class="text-xs sm:text-sm font-black text-gold-primary italic">Rp {{ number_format($summary['sum_total'], 0, ',', '.') }}</span>
          </div>
       </div>
    </div>
  </div>

  {{-- QUICK SUMMARY CARDS --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
     <!-- DPP -->
     <div class="premium-card p-4 sm:p-6 border-white/5 bg-white/[0.02]">
        <p class="text-[8px] sm:text-[10px] uppercase tracking-widest text-white/30 font-black mb-1">Subtotal (DPP)</p>
        <h3 class="text-sm sm:text-xl font-bold text-white italic truncate">Rp {{ number_format($summary['sum_subtotal'], 0, ',', '.') }}</h3>
     </div>
     <!-- PAJAK -->
     <div class="premium-card p-4 sm:p-6 border-gold-primary/10 bg-gold-primary/5">
        <p class="text-[8px] sm:text-[10px] uppercase tracking-widest text-gold-primary/40 font-black mb-1">Pajak (10%)</p>
        <h3 class="text-sm sm:text-xl font-bold text-gold-primary italic truncate">Rp {{ number_format($summary['sum_tax'], 0, ',', '.') }}</h3>
     </div>
     <!-- MODE SELECTOR -->
     <div class="col-span-2 premium-card p-2 sm:p-4 border-white/5 bg-white/[0.01] flex items-center gap-2">
        <a href="{{ request()->fullUrlWithQuery(['view' => 'alt', 'page' => null]) }}"
           class="flex-1 text-center py-2.5 sm:py-3 rounded-xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest transition-all {{ request('view', 'alt') === 'alt' ? 'bg-gold-primary text-obsidian-950 shadow-lg shadow-gold-primary/20' : 'text-white/40 hover:text-white/70 bg-white/5' }}">
           <span class="sm:hidden">Alt View</span>
           <span class="hidden sm:inline">Tampilan 1: Selang-seling</span>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['view' => 'all', 'page' => null]) }}"
           class="flex-1 text-center py-2.5 sm:py-3 rounded-xl text-[8px] sm:text-[10px] font-black uppercase tracking-widest transition-all {{ request('view', 'alt') === 'all' ? 'bg-gold-primary text-obsidian-950 shadow-lg shadow-gold-primary/20' : 'text-white/40 hover:text-white/70 bg-white/5' }}">
           <span class="sm:hidden">Normal</span>
           <span class="hidden sm:inline">Tampilan 2: Normal</span>
        </a>
     </div>
  </div>

  {{-- FILTER PANEL --}}
  <form method="GET" class="glass-panel p-6 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] mb-8 relative overflow-hidden group">
    <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
    
    <div class="flex items-center gap-3 mb-6 relative z-10">
       <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
       </div>
       <h3 class="text-xs sm:text-sm font-black text-white uppercase tracking-[0.2em]">Filter Transaksi</h3>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-12 items-end relative z-10">
      <div class="md:col-span-3 space-y-1.5">
        <label class="text-[8px] sm:text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Nama Kasir</label>
        <select name="cashier_id" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs sm:text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none">
          <option value="">Semua Kasir</option>
          @foreach($cashiers as $c)
            <option value="{{ $c->id }}" @selected(request('cashier_id') == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-2 gap-4 md:col-span-4">
         <div class="space-y-1.5">
           <label class="text-[8px] sm:text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Dari</label>
           <input type="date" name="from" value="{{ request('from') }}"
             class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs sm:text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
         </div>
         <div class="space-y-1.5">
           <label class="text-[8px] sm:text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Sampai</label>
           <input type="date" name="to" value="{{ request('to') }}"
             class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs sm:text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
         </div>
      </div>

      <div class="md:col-span-3 space-y-1.5">
        <label class="text-[8px] sm:text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Invoice / ID</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="INV-2024..."
          class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs sm:text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all">
      </div>

      <div class="md:col-span-2 flex gap-2">
        <button class="flex-1 rounded-xl bg-gold-primary py-3.5 text-[9px] sm:text-[10px] font-black text-obsidian-950 uppercase tracking-widest shadow-lg shadow-gold-primary/20 hover:scale-[1.02] transition-all active:scale-95">Filter</button>
        <a href="{{ route('admin.sales.index') }}" class="flex-1 rounded-xl bg-white/5 border border-white/10 py-3.5 text-[9px] sm:text-[10px] font-black text-white uppercase tracking-widest text-center hover:bg-white/10 transition-all active:scale-95">Reset</a>
      </div>
    </div>
    <input type="hidden" name="view" value="{{ request('view', 'alt') }}">
  </form>

  {{-- CHART SECTION --}}
  <div class="glass-panel p-6 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-3">
         <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
            </svg>
         </div>
         <div>
            <h3 class="text-xs sm:text-sm font-black text-white uppercase tracking-widest">Tren Penjualan</h3>
            <p class="text-[9px] sm:text-[10px] text-white/30 font-medium italic">Data omzet 14 hari terakhir.</p>
         </div>
      </div>
      <div class="flex items-center gap-4 text-[8px] sm:text-[9px] font-black uppercase tracking-widest px-2">
         <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-gold-primary"></div> <span class="text-white/40">Omzet</span></div>
         <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-white/40"></div> <span class="text-white/40">Trx</span></div>
      </div>
    </div>

    <div class="h-[200px] sm:h-[250px] w-full">
      <canvas id="salesChart"></canvas>
    </div>
  </div>

  {{-- LIST SECTION --}}
  <div class="space-y-4 sm:space-y-6">
     <div class="flex items-center justify-between px-2 sm:px-4">
        <h3 class="text-[9px] sm:text-xs font-black text-white/40 uppercase tracking-[0.3em]">Daftar Transaksi ({{ $sales->total() }})</h3>
     </div>

     {{-- Desktop Table --}}
     <div class="hidden lg:block overflow-hidden rounded-[2rem] glass-panel border-white/5">
       <table class="w-full text-left">
         <thead class="bg-white/[0.03] text-[9px] uppercase tracking-widest text-white/30 font-black border-b border-white/5">
           <tr>
             <th class="px-8 py-6">Waktu & Invoice</th>
             <th class="px-6 py-6">Petugas Kasir</th>
             <th class="px-6 py-6 text-right">Dasar Pajak</th>
             <th class="px-6 py-6 text-right">Pajak (10%)</th>
             <th class="px-6 py-6 text-right">Total Akhir</th>
             <th class="px-6 py-6 text-center">Status</th>
             <th class="px-8 py-6 text-right">Opsi</th>
           </tr>
         </thead>
         <tbody class="divide-y divide-white/5">
           @forelse($sales as $s)
             @php
               $dpp = (float) ($s->items_subtotal ?? 0);
               $deliveryFee = (float) ($s->delivery_fee ?? 0);
               $tax = max(0, (float) ($s->total_amount ?? 0) - $dpp - $deliveryFee);
             @endphp
             <tr class="group hover:bg-white/[0.02] transition-colors">
               <td class="px-8 py-6">
                 <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-black text-white group-hover:text-gold-primary transition-colors">{{ $s->invoice_no ?? ('#'.$s->id) }}</span>
                    @if(($s->order_type ?? '') === 'dine_in')
                       <span class="text-[8px] font-bold text-gold-primary bg-gold-primary/10 px-1.5 py-0.5 rounded border border-gold-primary/20 uppercase tracking-wider">Dine In</span>
                    @elseif(($s->order_type ?? '') === 'delivery')
                       <span class="text-[8px] font-bold text-amber-500 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20 uppercase tracking-wider">Delivery</span>
                    @else
                       <span class="text-[8px] font-bold text-white/40 bg-white/5 px-1.5 py-0.5 rounded border border-white/10 uppercase tracking-wider">Take Away</span>
                    @endif
                 </div>
                 <div class="text-[10px] text-white/30 font-medium italic mt-0.5">{{ $s->created_at?->format('d M Y • H:i') }}</div>
               </td>
               <td class="px-6 py-6">
                 <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-[10px] font-black text-white/40 border border-white/10 group-hover:border-gold-primary/30 transition-all">
                       {{ substr($s->cashier->name ?? 'A', 0, 1) }}
                    </div>
                    <div>
                       <div class="text-xs font-bold text-white/80">{{ $s->cashier->name ?? 'Unknown' }}</div>
                       <div class="text-[9px] text-white/20 truncate max-w-[120px]">{{ $s->cashier->email ?? '' }}</div>
                    </div>
                 </div>
               </td>
               <td class="px-6 py-6 text-right text-[11px] font-medium text-white/60">Rp {{ number_format($dpp, 0, ',', '.') }}</td>
               <td class="px-6 py-6 text-right text-[11px] font-medium text-gold-primary/60">Rp {{ number_format($tax, 0, ',', '.') }}</td>
               <td class="px-6 py-6 text-right text-sm font-black text-white">Rp {{ number_format($s->total_amount ?? 0, 0, ',', '.') }}</td>
               <td class="px-6 py-6 text-center">
                  <span class="px-2.5 py-1 rounded-lg bg-green-500/10 border border-green-500/20 text-[9px] font-black text-green-500 uppercase tracking-tighter">
                     Success
                  </span>
               </td>
               <td class="px-8 py-6 text-right">
                 <a href="{{ route('admin.sales.show', $s->id) }}"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:bg-gold-primary hover:text-obsidian-950 hover:border-gold-primary transition-all">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                   </svg>
                 </a>
               </td>
             </tr>
           @empty
             <tr>
               <td colspan="7" class="px-8 py-20 text-center">
                  <p class="text-sm text-white/20 font-medium italic">Belum ada transaksi dalam periode ini.</p>
               </td>
             </tr>
           @endforelse
         </tbody>
       </table>
     </div>

     {{-- Mobile & Tablet Cards --}}
     <div class="lg:hidden grid grid-cols-1 md:grid-cols-2 gap-4">
       @forelse($sales as $s)
         <div class="premium-card p-5 sm:p-6 border-white/5 bg-white/[0.02]">
            <div class="flex items-start justify-between gap-4 mb-4">
               <div>
                  <div class="flex items-center gap-2">
                     <h4 class="text-sm font-black text-white group-hover:text-gold-primary transition-colors">{{ $s->invoice_no ?? ('#'.$s->id) }}</h4>
                     @if(($s->order_type ?? '') === 'dine_in')
                        <span class="text-[8px] font-bold text-gold-primary bg-gold-primary/10 px-1.5 py-0.5 rounded border border-gold-primary/20 uppercase tracking-wider">Dine In</span>
                     @elseif(($s->order_type ?? '') === 'delivery')
                        <span class="text-[8px] font-bold text-amber-500 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20 uppercase tracking-wider">Delivery</span>
                     @else
                        <span class="text-[8px] font-bold text-white/40 bg-white/5 px-1.5 py-0.5 rounded border border-white/10 uppercase tracking-wider">Take Away</span>
                     @endif
                  </div>
                  <p class="text-[10px] text-white/30 font-medium mt-0.5">{{ $s->created_at?->format('d M Y • H:i') }}</p>
               </div>
               <div class="text-right">
                  <p class="text-sm font-black text-gold-primary italic">Rp {{ number_format($s->total_amount ?? 0, 0, ',', '.') }}</p>
                  <span class="text-[8px] px-1.5 py-0.5 rounded-md bg-green-500/10 text-green-500 font-black uppercase tracking-tighter">Paid</span>
               </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5 mb-4">
               <div>
                  <p class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Kasir</p>
                  <p class="text-[10px] font-bold text-white/60 truncate">{{ $s->cashier->name ?? '-' }}</p>
               </div>
               <div class="text-right">
                  <p class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Kembalian</p>
                  <p class="text-[10px] font-bold text-white/60">Rp {{ number_format($s->change_amount ?? 0, 0, ',', '.') }}</p>
               </div>
            </div>

            <a href="{{ route('admin.sales.show', $s->id) }}"
               class="block w-full py-3 rounded-xl bg-white/5 border border-white/10 text-center text-[9px] font-black text-white uppercase tracking-[0.2em] hover:bg-white/10 transition-all active:scale-[0.98]">
               Lihat Rincian
            </a>
         </div>
       @empty
         <div class="col-span-full premium-card p-10 border-white/5 text-center text-xs text-white/30 italic">Kosong.</div>
       @endforelse
     </div>

     <div class="mt-8">
       {{ $sales->links() }}
     </div>
  </div>

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
              backgroundColor: 'rgba(212, 175, 55, 0.8)',
              borderRadius: 6,
              barThickness: 'flex',
              maxBarThickness: 12,
            },
            {
              type: 'line',
              label: 'Trx',
              data: counts,
              borderColor: 'rgba(255, 255, 255, 0.4)',
              borderWidth: 2,
              tension: 0.4,
              pointRadius: 0,
              yAxisID: 'y1',
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#0A0A0A',
              titleFont: { size: 10, weight: 'bold' },
              bodyFont: { size: 12 },
              padding: 12,
              displayColors: false,
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
            x: { 
               ticks: { color: 'rgba(255,255,255,0.2)', font: { size: 8 } }, 
               grid: { display: false } 
            },
            y: { 
               ticks: { color: 'rgba(255,255,255,0.2)', font: { size: 8 } }, 
               grid: { color: 'rgba(255,255,255,0.05)' } 
            },
            y1: {
               position: 'right',
               display: false,
               grid: { display: false }
            }
          }
        }
      });
    })();
  </script>
@endsection
