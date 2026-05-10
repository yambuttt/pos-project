@extends('layouts.kasir')
@section('title','Riwayat Transaksi')

@section('body')
  <div class="max-w-7xl mx-auto space-y-8 animate-fade-up">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-bold tracking-tight mb-1 text-white">Riwayat Transaksi</h1>
        <p class="text-white/40 text-sm">Daftar transaksi yang dilakukan melalui terminal ini.</p>
      </div>
      <a href="{{ route('kasir.sales.create') }}" class="btn-premium-primary text-sm px-8">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        Transaksi Baru
      </a>
    </div>

    @if(session('success'))
      <div class="rounded-2xl border border-accent-gold/20 bg-accent-gold/5 px-6 py-4 text-sm text-accent-gold flex items-center gap-3 animate-fade-up">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ session('success') }}
      </div>
    @endif

    {{-- Transactions Table --}}
    <div class="premium-card overflow-hidden border-white/5">
      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-white/5 bg-white/[0.02]">
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Waktu</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Invoice</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Metode</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Total</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Detail Item</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            @forelse($sales as $s)
              <tr class="hover:bg-white/[0.02] transition-colors group">
                <td class="px-6 py-6">
                  <div class="text-xs font-bold text-white/80">{{ $s->created_at->format('d M Y') }}</div>
                  <div class="text-[10px] text-white/20 mt-0.5 font-bold">{{ $s->created_at->format('H:i') }}</div>
                </td>
                <td class="px-6 py-6">
                  <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent-gold shadow-[0_0_8px_rgba(234,179,8,0.5)]"></span>
                    <span class="text-xs font-bold tracking-tight text-white">{{ $s->invoice_no }}</span>
                  </div>
                </td>
                <td class="px-6 py-6">
                  <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-white/40 group-hover:text-accent-gold transition-colors">
                    {{ str_replace('_', ' ', strtoupper($s->payment_method)) }}
                  </span>
                </td>
                <td class="px-6 py-6">
                  <div class="text-sm font-black text-accent-gold">Rp {{ number_format($s->total_amount,0,',','.') }}</div>
                </td>
                <td class="px-6 py-6">
                  <div class="space-y-1">
                    @foreach($s->items as $it)
                      <div class="flex items-center justify-between gap-4 max-w-xs">
                        <span class="text-[11px] text-white/40 truncate font-medium">{{ $it->product?->name }}</span>
                        <span class="text-[11px] font-black text-accent-gold/40 shrink-0">x{{ $it->qty }}</span>
                      </div>
                    @endforeach
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-20 text-center">
                  <div class="flex flex-col items-center opacity-10">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <p class="text-sm font-black uppercase tracking-[0.2em]">Belum ada transaksi</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-8">
      {{ $sales->links() }}
    </div>
  </div>
@endsection


