@extends('layouts.kasir')
@section('title','Reservasi (Kasir)')

@section('body')
  <div class="max-w-7xl mx-auto space-y-8 animate-fade-up">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-bold tracking-tight mb-1 text-white">Reservasi</h1>
        <p class="text-white/40 text-sm">Check-in & checkout reservasi harian.</p>
      </div>
    </div>

    @if(session('success'))
      <div class="rounded-2xl border border-accent-gold/20 bg-accent-gold/5 px-6 py-4 text-sm text-accent-gold flex items-center gap-3 animate-fade-up">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ session('success') }}
      </div>
    @endif
    @if($errors->any())
      <div class="rounded-2xl border border-red-500/20 bg-red-500/5 px-6 py-4 text-sm text-red-500 flex items-center gap-3 animate-fade-up">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ $errors->first() }}
      </div>
    @endif

    {{-- Filter Section --}}
    <div class="premium-card p-4 sm:p-6 border-white/5">
      <form class="flex flex-col sm:flex-row flex-wrap items-end gap-4" method="GET">
        <div class="w-full sm:w-auto">
          <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-2">Tanggal</label>
          <input type="date" name="date" value="{{ $date }}"
            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:border-accent-gold/50 focus:ring-0 transition-all outline-none">
        </div>
        <div class="w-full sm:w-auto flex-1 sm:max-w-xs">
          <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-2">Cari</label>
          <input name="q" value="{{ $q }}" placeholder="Kode/Nama/HP"
            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-sm text-white placeholder:text-white/20 focus:border-accent-gold/50 focus:ring-0 transition-all outline-none">
        </div>
        <button class="btn-premium-primary text-sm px-8 py-3 w-full sm:w-auto justify-center">
          <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
          Filter
        </button>
      </form>
    </div>

    {{-- Reservations Table --}}
    <div class="premium-card overflow-hidden border-white/5">
      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse min-w-[900px]">
          <thead>
            <tr class="border-b border-white/5 bg-white/[0.02]">
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Waktu</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Kode</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Customer</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Resource</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Status</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Total</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Sisa</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            @forelse($rows as $r)
              @php $remaining = max(0, (int)$r->grand_total - (int)$r->paid_amount); @endphp
              <tr class="hover:bg-white/[0.02] transition-colors group">
                <td class="px-6 py-6">
                  <div class="text-xs font-bold text-white/80">{{ $r->start_at->format('H:i') }} – {{ $r->end_at->format('H:i') }}</div>
                </td>
                <td class="px-6 py-6">
                  <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent-gold shadow-[0_0_8px_rgba(234,179,8,0.5)]"></span>
                    <a href="{{ route('kasir.reservations.show', $r) }}" class="text-xs font-bold tracking-tight text-white hover:text-accent-gold transition-colors">{{ $r->code }}</a>
                  </div>
                </td>
                <td class="px-6 py-6">
                  <div class="text-sm font-bold text-white/90">{{ $r->customer_name }}</div>
                  <div class="text-[10px] font-bold text-white/40 mt-1">{{ $r->customer_phone }}</div>
                </td>
                <td class="px-6 py-6 text-sm font-bold text-white/80">{{ $r->resource?->name ?? '-' }}</td>
                <td class="px-6 py-6">
                  <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-white/40 group-hover:text-accent-gold transition-colors">
                    {{ strtoupper($r->status) }}
                  </span>
                </td>
                <td class="px-6 py-6">
                  <div class="text-sm font-black text-white/90">Rp {{ number_format($r->grand_total,0,',','.') }}</div>
                </td>
                <td class="px-6 py-6">
                  <div class="text-sm font-black text-accent-gold">Rp {{ number_format($remaining,0,',','.') }}</div>
                </td>
                <td class="px-6 py-6">
                  <a href="{{ route('kasir.reservations.show', $r) }}" class="btn-premium-glass px-4 py-2 text-[10px] rounded-xl flex items-center gap-2 w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Detail
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-6 py-20 text-center">
                  <div class="flex flex-col items-center opacity-10">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-sm font-black uppercase tracking-[0.2em]">Tidak ada reservasi aktif</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-8">
      {{ $rows->links() }}
    </div>
  </div>
@endsection