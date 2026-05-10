@extends('layouts.kitchen')
@section('title', 'Riwayat Masak')

@section('body')
  <div class="premium-card p-6 lg:p-8 animate-fade-up">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between relative z-10">
      <div>
        <div class="inline-flex items-center gap-2 rounded-full border border-white/5 bg-white/5 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-white/40 mb-4">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Archive • Statistics
        </div>
        <h1 class="text-3xl font-bold tracking-tight text-white">Riwayat Masak</h1>
        <p class="mt-2 text-sm text-white/40">Ringkasan pesanan yang sudah berhasil diselesaikan di dapur.</p>
      </div>
    </div>

    <div class="mt-8 pt-8 border-t border-white/5 relative z-10">
      <form class="flex flex-col gap-6 sm:flex-row sm:items-end">
        <div class="space-y-2">
          <label class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 px-1">Dari Tanggal</label>
          <input type="date" name="from" value="{{ $from }}"
                 class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-accent-gold/50 transition-colors" />
        </div>
        <div class="space-y-2">
          <label class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 px-1">Sampai Tanggal</label>
          <input type="date" name="to" value="{{ $to }}"
                 class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-accent-gold/50 transition-colors" />
        </div>
        <button class="btn-premium-primary px-8 py-3.5 text-xs font-black uppercase tracking-widest">
          Filter Data
        </button>
      </form>
    </div>

    <div class="mt-8 flex flex-wrap gap-4 relative z-10">
      <div class="glass-panel rounded-2xl px-6 py-5 border-white/5 bg-white/[0.02]">
        <div class="text-[10px] font-black uppercase tracking-widest text-white/20 mb-1">Total Order Selesai</div>
        <div class="text-3xl font-bold text-accent-gold tracking-tight">{{ $totalOrdersDone }}</div>
      </div>
    </div>
  </div>

  <div class="premium-card p-6 lg:p-8 mt-8 animate-fade-up stagger-1">
    <div class="flex items-center gap-3 mb-8 relative z-10">
      <div class="w-1.5 h-6 bg-accent-gold rounded-full"></div>
      <h2 class="text-lg font-black uppercase tracking-widest text-white/60">Performa Menu</h2>
    </div>

    <div class="overflow-x-auto relative z-10">
      <table class="min-w-full">
        <thead>
          <tr class="text-left text-[10px] font-black uppercase tracking-[0.3em] text-white/20 border-b border-white/5">
            <th class="pb-4 px-4">Nama Produk / Menu</th>
            <th class="pb-4 px-4 text-right">Total Porsi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @forelse($byProduct as $row)
            <tr class="group hover:bg-white/[0.01] transition-colors">
              <td class="py-5 px-4 text-sm font-bold text-white/80 group-hover:text-accent-gold transition-colors">
                {{ $row->product->name ?? ('Product#'.$row->product_id) }}
              </td>
              <td class="py-5 px-4 text-right">
                <span class="bg-white/5 border border-white/10 text-white/40 px-3 py-1 rounded-lg text-xs font-black tracking-tight">
                  {{ (int) $row->total_qty }} <span class="ml-1 text-[10px] opacity-40">ITEM</span>
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td class="py-12 text-center text-white/10 italic" colspan="2">Belum ada data tersedia untuk rentang waktu ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection