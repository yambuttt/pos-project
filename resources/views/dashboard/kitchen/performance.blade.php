@extends('layouts.kitchen')
@section('title', 'Evaluasi Performa Koki')

@section('body')
  <div class="max-w-7xl mx-auto space-y-8 animate-fade-up">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-bold tracking-tight mb-1 text-white">Evaluasi Performa Koki</h1>
        <p class="text-white/40 text-sm">Analisis mendalam kecepatan masak dan efisiensi dapur.</p>
      </div>
      
      <div class="flex bg-white/5 p-1 rounded-xl border border-white/10 shrink-0">
        <a href="{{ route('kitchen.performance', ['period' => 'today']) }}" class="px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ $period === 'today' ? 'bg-accent-gold text-black shadow-lg shadow-accent-gold/20' : 'text-white/40 hover:text-white' }}">Hari</a>
        <a href="{{ route('kitchen.performance', ['period' => 'week']) }}" class="px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ $period === 'week' ? 'bg-accent-gold text-black shadow-lg shadow-accent-gold/20' : 'text-white/40 hover:text-white' }}">Minggu</a>
        <a href="{{ route('kitchen.performance', ['period' => 'month']) }}" class="px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ $period === 'month' ? 'bg-accent-gold text-black shadow-lg shadow-accent-gold/20' : 'text-white/40 hover:text-white' }}">Bulan</a>
      </div>
    </div>

    {{-- Summary Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Volume --}}
        <div class="premium-card p-6 border-white/5 flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-accent-blue/10 flex items-center justify-center text-accent-blue">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white/30">Total Masakan</span>
            </div>
            <div class="text-3xl font-bold text-white">{{ $stats->total_done }}</div>
            <div class="mt-2 text-[10px] text-white/20 uppercase font-black">Pesanan Selesai</div>
        </div>

        {{-- Avg Time --}}
        <div class="premium-card p-6 border-white/5 flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-accent-gold/10 flex items-center justify-center text-accent-gold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white/30">Rerata Durasi</span>
            </div>
            <div class="text-3xl font-bold text-white">
                {{ floor($stats->avg_seconds / 60) }}m {{ str_pad($stats->avg_seconds % 60, 2, '0', STR_PAD_LEFT) }}s
            </div>
            <div class="mt-2 text-[10px] text-white/20 uppercase font-black">Dari semua item</div>
        </div>

        {{-- Fast Orders --}}
        <div class="premium-card p-6 border-white/5 flex flex-col justify-between border-b-emerald-500/20">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white/30">Kilat (< 10m)</span>
            </div>
            <div class="text-3xl font-bold text-emerald-400">{{ $stats->fast_count }}</div>
            <div class="mt-2 text-[10px] text-white/20 uppercase font-black">Sangat Efisien</div>
        </div>

        {{-- Slow Orders --}}
        <div class="premium-card p-6 border-white/5 flex flex-col justify-between border-b-red-500/20">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white/30">Lambat (> 20m)</span>
            </div>
            <div class="text-3xl font-bold text-red-400">{{ $stats->slow_count }}</div>
            <div class="mt-2 text-[10px] text-white/20 uppercase font-black">Butuh Perhatian</div>
        </div>
    </div>

    {{-- Detailed Product Stats --}}
    <div class="premium-card border-white/5 overflow-hidden">
        <div class="p-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-widest text-white/60">Performa Per Menu</h3>
            <span class="text-[10px] text-white/20 uppercase font-bold tracking-tighter italic">Berdasarkan waktu masak per item</span>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.01]">
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Nama Menu</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20 text-center">Volume</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Rerata Durasi</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Tercepat</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Terlama</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20 text-right">Efisiensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($productStats as $p)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-5">
                            <div class="font-bold text-white group-hover:text-accent-gold transition-colors">{{ $p->product_name }}</div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-xs font-bold text-white/60">
                                {{ $p->total_cooked }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $p->avg_cook_seconds <= 600 ? 'bg-emerald-500' : ($p->avg_cook_seconds <= 1200 ? 'bg-accent-gold' : 'bg-red-500') }}"></div>
                                <span class="text-sm font-mono font-bold text-white">
                                    {{ floor($p->avg_cook_seconds / 60) }}m {{ str_pad($p->avg_cook_seconds % 60, 2, '0', STR_PAD_LEFT) }}s
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-xs text-white/40 font-mono">
                            {{ floor($p->min_cook_seconds / 60) }}m {{ str_pad($p->min_cook_seconds % 60, 2, '0', STR_PAD_LEFT) }}s
                        </td>
                        <td class="px-6 py-5 text-xs text-white/40 font-mono">
                            {{ floor($p->max_cook_seconds / 60) }}m {{ str_pad($p->max_cook_seconds % 60, 2, '0', STR_PAD_LEFT) }}s
                        </td>
                        <td class="px-6 py-5 text-right">
                            @php
                                $score = max(0, 100 - ($p->avg_cook_seconds / 18)); // arbitrary score logic: 30 mins = 0, 0 mins = 100
                            @endphp
                            <div class="flex flex-col items-end gap-1">
                                <div class="w-16 h-1 rounded-full bg-white/5 overflow-hidden">
                                    <div class="h-full {{ $score >= 80 ? 'bg-emerald-500' : ($score >= 50 ? 'bg-accent-gold' : 'bg-red-500') }}" style="width: {{ $score }}%"></div>
                                </div>
                                <span class="text-[10px] font-black text-white/20">{{ round($score) }} pts</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-white/20 italic text-sm">Belum ada data performa untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Performance Insight --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="premium-card p-8 border-white/5 bg-gradient-to-br from-white/[0.03] to-transparent">
            <h4 class="text-sm font-black uppercase tracking-widest text-accent-gold mb-6">Menu Paling Cepat</h4>
            <div class="space-y-4">
                @foreach($productStats->take(3) as $p)
                <div class="flex items-center justify-between p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10">
                    <div class="font-bold text-white">{{ $p->product_name }}</div>
                    <div class="text-emerald-400 font-mono font-bold">{{ floor($p->avg_cook_seconds / 60) }}m {{ str_pad($p->avg_cook_seconds % 60, 2, '0', STR_PAD_LEFT) }}s</div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="premium-card p-8 border-white/5 bg-gradient-to-br from-white/[0.03] to-transparent">
            <h4 class="text-sm font-black uppercase tracking-widest text-red-400 mb-6">Menu Paling Lama (Butuh Optimasi)</h4>
            <div class="space-y-4">
                @foreach($productStats->reverse()->take(3) as $p)
                <div class="flex items-center justify-between p-4 rounded-2xl bg-red-500/5 border border-red-500/10">
                    <div class="font-bold text-white">{{ $p->product_name }}</div>
                    <div class="text-red-400 font-mono font-bold">{{ floor($p->avg_cook_seconds / 60) }}m {{ str_pad($p->avg_cook_seconds % 60, 2, '0', STR_PAD_LEFT) }}s</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

  </div>
@endsection
