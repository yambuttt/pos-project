@extends('layouts.admin')
@section('title', 'Purchases')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Riwayat Pembelian</h1>
      <p class="text-sm text-white/40 font-medium">Manajemen barang masuk, supplier, dan faktur pembelian stok.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.purchases.create') }}"
        class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Buat Purchase Baru
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-6 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-green-100">{{ session('success') }}</p>
    </div>
  @endif

  <!-- EXPORT PANEL -->
  <form method="POST" action="{{ route('admin.purchases.exportPdf') }}" class="mb-10">
    @csrf
    <input type="hidden" name="mode" id="exportMode" value="selected">
    
    <div class="glass-panel p-8 rounded-[2.5rem] border-white/5 relative overflow-hidden">
      <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">
        <!-- MODE SELECTED -->
        <div class="flex-1 space-y-4">
          <div>
            <h4 class="text-xs font-black text-gold-primary uppercase tracking-[0.2em] mb-1">Export Seleksi</h4>
            <p class="text-[10px] text-white/40 font-medium">Pilih beberapa transaksi di tabel lalu export sekaligus.</p>
          </div>
          <button type="submit" onclick="document.getElementById('exportMode').value='selected'"
            class="flex items-center gap-2 px-6 py-3 rounded-xl bg-white/5 border border-white/10 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gold-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
            </svg>
            Export Terpilih
          </button>
        </div>

        <div class="hidden lg:block w-px bg-white/5"></div>

        <!-- MODE PERIOD -->
        <div class="flex-1 space-y-4">
          <div>
            <h4 class="text-xs font-black text-gold-primary uppercase tracking-[0.2em] mb-1">Export Berdasarkan Periode</h4>
            <p class="text-[10px] text-white/40 font-medium">Tentukan periode waktu transaksi yang ingin di-export.</p>
          </div>
          <div class="flex flex-wrap items-end gap-3">
            <div class="space-y-1">
              <label class="text-[9px] uppercase tracking-widest text-white/30 font-bold ml-1">Periode</label>
              <select name="period" class="bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-xs text-white outline-none focus:border-gold-primary/30 transition-all">
                <option value="daily">Harian</option>
                <option value="weekly">Mingguan</option>
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
              </select>
            </div>
            <div class="space-y-1">
              <label class="text-[9px] uppercase tracking-widest text-white/30 font-bold ml-1">Tanggal Acuan</label>
              <input type="date" name="anchor_date" class="bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-xs text-white outline-none focus:border-gold-primary/30 transition-all">
            </div>
            <button type="submit" onclick="document.getElementById('exportMode').value='period'"
              class="px-6 py-2.5 rounded-xl bg-white/5 border border-white/10 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
              Export
            </button>
          </div>
        </div>

        <div class="hidden lg:block w-px bg-white/5"></div>

        <!-- MODE RANGE -->
        <div class="flex-1 space-y-4">
          <div>
            <h4 class="text-xs font-black text-gold-primary uppercase tracking-[0.2em] mb-1">Export Rentang Waktu</h4>
            <p class="text-[10px] text-white/40 font-medium">Filter transaksi berdasarkan rentang tanggal spesifik.</p>
          </div>
          <div class="flex flex-wrap items-end gap-3">
            <div class="space-y-1">
              <label class="text-[9px] uppercase tracking-widest text-white/30 font-bold ml-1">Dari</label>
              <input type="date" name="from" class="bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-xs text-white outline-none focus:border-gold-primary/30 transition-all">
            </div>
            <div class="space-y-1">
              <label class="text-[9px] uppercase tracking-widest text-white/30 font-bold ml-1">Sampai</label>
              <input type="date" name="to" class="bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-xs text-white outline-none focus:border-gold-primary/30 transition-all">
            </div>
            <button type="submit" onclick="document.getElementById('exportMode').value='range'"
              class="px-6 py-2.5 rounded-xl bg-white/5 border border-white/10 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
              Export
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MAIN LIST (DESKTOP TABLE) -->
    <div class="mt-10 hidden lg:block overflow-hidden rounded-[2.5rem] glass-panel border-white/5">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-white/40 font-black border-b border-white/5">
            <th class="px-8 py-6 w-[60px]">
               <input id="checkAll" type="checkbox" class="h-4 w-4 rounded border-white/10 bg-white/5 text-gold-primary focus:ring-gold-primary/20">
            </th>
            <th class="px-6 py-6">Tanggal</th>
            <th class="px-6 py-6">Sumber / Supplier</th>
            <th class="px-6 py-6">Invoice</th>
            <th class="px-6 py-6">Total Amount</th>
            <th class="px-6 py-6">Admin</th>
            <th class="px-8 py-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @forelse($purchases as $p)
            <tr class="group hover:bg-white/[0.02] transition-colors">
              <td class="px-8 py-6">
                 <input type="checkbox" name="purchase_ids[]" value="{{ $p->id }}" class="rowCheck h-4 w-4 rounded border-white/10 bg-white/5 text-gold-primary focus:ring-gold-primary/20">
              </td>
              <td class="px-6 py-6">
                <div class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}</div>
                <div class="text-[10px] text-white/30 font-medium uppercase tracking-tight">Purchase ID: #{{ $p->id }}</div>
              </td>
              <td class="px-6 py-6">
                @if($p->source_type === 'supplier')
                  <div class="text-sm font-bold text-gold-primary">{{ $p->supplier?->name }}</div>
                  <div class="text-[10px] text-white/30 font-bold uppercase">Official Supplier</div>
                @else
                  <div class="text-sm font-bold text-white/80">{{ $p->source_name }}</div>
                  <div class="text-[10px] text-white/30 font-bold uppercase">External Source</div>
                @endif
              </td>
              <td class="px-6 py-6">
                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/5 text-[11px] font-bold text-white/60">
                  {{ $p->invoice_no ?? 'No Invoice' }}
                </span>
              </td>
              <td class="px-6 py-6">
                <div class="text-sm font-black text-white">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</div>
              </td>
              <td class="px-6 py-6">
                <div class="text-sm font-bold text-white/80">{{ $p->creator?->name ?? '-' }}</div>
                <div class="text-[10px] text-white/30">{{ $p->creator?->email }}</div>
              </td>
              <td class="px-8 py-6 text-right">
                <a href="{{ route('admin.purchases.show', $p) }}"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black text-white uppercase tracking-widest hover:bg-gold-primary hover:text-obsidian-950 transition-all">
                  Detail
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-8 py-20 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <p class="text-sm text-white/30 font-medium italic">Belum ada data transaksi pembelian.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- MOBILE LIST (CARDS) -->
    <div class="mt-6 lg:hidden space-y-4">
      @forelse($purchases as $p)
        <div class="premium-card p-6 border-white/5 relative overflow-hidden group">
          <div class="flex items-start justify-between gap-4 mb-4">
            <div class="flex items-start gap-4">
               <input type="checkbox" name="purchase_ids[]" value="{{ $p->id }}" class="rowCheck h-5 w-5 mt-1 rounded border-white/10 bg-white/5 text-gold-primary focus:ring-gold-primary/20">
               <div>
                  <h4 class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}</h4>
                  <p class="text-[10px] text-white/30 font-bold uppercase tracking-widest mt-0.5">
                    {{ $p->source_type === 'supplier' ? ($p->supplier?->name ?? '-') : ($p->source_name ?? '-') }}
                  </p>
               </div>
            </div>
            <div class="text-right">
               <p class="text-sm font-black text-gold-primary leading-none">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</p>
               <p class="text-[9px] text-white/20 uppercase tracking-tighter mt-1">Invoice: {{ $p->invoice_no ?? '-' }}</p>
            </div>
          </div>
          
          <div class="flex items-center justify-between pt-4 border-t border-white/5">
             <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-gold-primary/10 flex items-center justify-center text-[10px] font-bold text-gold-primary">
                  {{ substr($p->creator?->name ?? 'A', 0, 1) }}
                </div>
                <span class="text-[10px] text-white/40 font-medium italic">By: {{ $p->creator?->name ?? 'Admin' }}</span>
             </div>
             <a href="{{ route('admin.purchases.show', $p) }}"
               class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-[9px] font-black text-white uppercase tracking-widest">
               Detail Transaksi
             </a>
          </div>
        </div>
      @empty
        <div class="premium-card p-10 border-white/5 text-center">
          <p class="text-xs text-white/30 italic">Belum ada data transaksi.</p>
        </div>
      @endforelse
    </div>

    <div class="mt-10">
      {{ $purchases->onEachSide(1)->links() }}
    </div>
  </form>

  <script>
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
      checkAll.addEventListener('change', function () {
        document.querySelectorAll('.rowCheck').forEach(cb => cb.checked = checkAll.checked);
      });
    }
  </script>
@endsection