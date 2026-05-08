@extends('toko.layouts.kasir')

@section('title', 'Terminal Kasir Toko')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-up">

    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-3xl p-8 sm:p-10 bg-gradient-to-br from-[#1a1a1a] via-[#0f0f0f] to-[#050505] border border-yellow-500/20 shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
        <!-- Abstract Glows -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-yellow-500/10 rounded-full mix-blend-screen filter blur-[80px] animate-pulse"></div>
        <div class="absolute top-1/2 -left-10 w-40 h-40 bg-yellow-600/10 rounded-full mix-blend-screen filter blur-[60px] animate-pulse" style="animation-delay: 1.5s;"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start justify-between gap-6 text-center md:text-left">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-white/70 text-xs font-bold tracking-widest uppercase mb-4">
                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Shift Berjalan: {{ date('d M Y') }}
                </div>
                <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-2 tracking-tight">
                    Halo, <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-600">{{ explode(' ', auth()->user()->name ?? 'Kasir')[0] }}</span>!
                </h1>
                <p class="text-white/50 text-sm md:text-base font-light max-w-lg">
                    Terminal kasir retail telah aktif dan siap digunakan. Pastikan laci kas sudah sesuai sebelum memulai transaksi.
                </p>
            </div>

            <!-- Big Action Button -->
            <div class="shrink-0 pt-4 md:pt-0">
                <a href="{{ route('toko.kasir.pos') }}" class="group relative inline-flex items-center justify-center">
                    <div class="absolute inset-0 bg-yellow-500 rounded-2xl blur-lg opacity-40 group-hover:opacity-60 transition-opacity duration-300"></div>
                    <div class="relative bg-gradient-to-br from-yellow-400 to-yellow-600 px-8 py-5 rounded-2xl text-black font-bold uppercase tracking-widest shadow-xl transform group-hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        Buka POS Sekarang
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats for Cashier -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-up delay-100">
        <!-- Stat 1 -->
        <div class="bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-colors shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/5 rounded-full blur-2xl group-hover:bg-yellow-500/10 transition-colors"></div>
            <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Transaksi Shift Ini</div>
            <div class="text-3xl font-display font-bold text-white mb-2">24</div>
            <div class="text-xs text-white/50">Diperbarui baru saja</div>
        </div>

        <!-- Stat 2 -->
        <div class="bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-colors shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/5 rounded-full blur-2xl group-hover:bg-yellow-500/10 transition-colors"></div>
            <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Total Penerimaan</div>
            <div class="text-3xl font-display font-bold text-yellow-400 mb-2">Rp 2.450.000</div>
            <div class="text-xs text-white/50">Cash & Cashless</div>
        </div>

        <!-- Stat 3 -->
        <div class="bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-colors shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/5 rounded-full blur-2xl group-hover:bg-yellow-500/10 transition-colors"></div>
            <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Status Laci</div>
            <div class="text-3xl font-display font-bold text-green-400 mb-2">Aman</div>
            <div class="text-xs text-white/50 flex items-center gap-1">
                <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Sinkronisasi sistem OK
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 animate-fade-up delay-200">
        <a href="#" class="bg-[#0f0f0f] hover:bg-[#151515] border border-white/5 hover:border-yellow-500/30 rounded-2xl p-6 flex flex-col items-center justify-center text-center gap-3 transition-all group">
            <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white/70 group-hover:text-yellow-500 group-hover:scale-110 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <span class="text-sm font-semibold text-white/80 group-hover:text-white">Transaksi Baru</span>
        </a>

        <a href="#" class="bg-[#0f0f0f] hover:bg-[#151515] border border-white/5 hover:border-yellow-500/30 rounded-2xl p-6 flex flex-col items-center justify-center text-center gap-3 transition-all group">
            <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white/70 group-hover:text-yellow-500 group-hover:scale-110 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <span class="text-sm font-semibold text-white/80 group-hover:text-white">Riwayat Struk</span>
        </a>

        <a href="#" class="bg-[#0f0f0f] hover:bg-[#151515] border border-white/5 hover:border-yellow-500/30 rounded-2xl p-6 flex flex-col items-center justify-center text-center gap-3 transition-all group">
            <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white/70 group-hover:text-yellow-500 group-hover:scale-110 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>
            <span class="text-sm font-semibold text-white/80 group-hover:text-white">Retur Produk</span>
        </a>

        <a href="#" class="bg-[#0f0f0f] hover:bg-[#151515] border border-white/5 hover:border-yellow-500/30 rounded-2xl p-6 flex flex-col items-center justify-center text-center gap-3 transition-all group">
            <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 group-hover:bg-red-500 group-hover:text-white group-hover:scale-110 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </div>
            <span class="text-sm font-semibold text-red-400 group-hover:text-red-300">Tutup Shift</span>
        </a>
    </div>

</div>
@endsection
