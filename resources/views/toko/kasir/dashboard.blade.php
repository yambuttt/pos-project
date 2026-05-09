@extends('toko.layouts.kasir')

@section('title', 'Terminal Kasir Toko')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-up" x-data="{ 
    showStartShift: false, 
    showEndShift: false,
    startingCash: 0,
    activeShift: {{ $activeShift ? 'true' : 'false' }}
}">

    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-3xl p-8 sm:p-10 bg-gradient-to-br from-[#1a1a1a] via-[#0f0f0f] to-[#050505] border border-yellow-500/20 shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-yellow-500/10 rounded-full mix-blend-screen filter blur-[80px] animate-pulse"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start justify-between gap-6 text-center md:text-left">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-white/70 text-xs font-bold tracking-widest uppercase mb-4">
                    <span class="w-2 h-2 rounded-full {{ $activeShift ? 'bg-green-500 animate-ping' : 'bg-red-500' }}"></span>
                    {{ $activeShift ? 'Shift Aktif' : 'Shift Belum Dimulai' }}
                </div>
                <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-2 tracking-tight">
                    Halo, <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-600">{{ explode(' ', auth()->user()->name ?? 'Kasir')[0] }}</span>!
                </h1>
                <p class="text-white/50 text-sm md:text-base font-light max-w-lg">
                    Terminal kasir retail telah aktif. {{ $activeShift ? 'Silakan lanjutkan transaksi Anda.' : 'Anda harus memulai shift sebelum dapat melakukan transaksi.' }}
                </p>
                
                @if($activeShift)
                <div class="mt-6 flex flex-wrap gap-4 justify-center md:justify-start">
                    <div class="bg-white/5 border border-white/10 px-4 py-2 rounded-xl">
                        <div class="text-[10px] text-white/40 uppercase font-black tracking-widest">Kas Awal</div>
                        <div class="text-yellow-500 font-bold font-mono">Rp {{ number_format($activeShift->starting_cash, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white/5 border border-white/10 px-4 py-2 rounded-xl">
                        <div class="text-[10px] text-white/40 uppercase font-black tracking-widest">Mulai Sejak</div>
                        <div class="text-white font-bold">{{ $activeShift->start_time->format('H:i') }} WIB</div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Button -->
            <div class="shrink-0 pt-4 md:pt-0">
                @if($activeShift)
                    <a href="{{ route('toko.kasir.pos') }}" class="group relative inline-flex items-center justify-center">
                        <div class="absolute inset-0 bg-yellow-500 rounded-2xl blur-lg opacity-40 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <div class="relative bg-gradient-to-br from-yellow-400 to-yellow-600 px-8 py-5 rounded-2xl text-black font-bold uppercase tracking-widest shadow-xl transform group-hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            Buka POS
                        </div>
                    </a>
                @else
                    <button @click="showStartShift = true" class="group relative inline-flex items-center justify-center">
                        <div class="absolute inset-0 bg-green-500 rounded-2xl blur-lg opacity-40 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <div class="relative bg-gradient-to-br from-green-400 to-green-600 px-8 py-5 rounded-2xl text-black font-bold uppercase tracking-widest shadow-xl transform group-hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Mulai Shift
                        </div>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-up delay-100">
        <div class="bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-colors shadow-lg relative overflow-hidden group">
            <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Transaksi Shift Ini</div>
            <div class="text-3xl font-display font-bold text-white mb-2">{{ $totalTransactions }}</div>
            <div class="text-xs text-white/50 italic">Total penjualan dalam shift ini</div>
        </div>

        <div class="bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-colors shadow-lg relative overflow-hidden group">
            <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Hasil Penjualan (Shift)</div>
            <div class="text-3xl font-display font-bold text-yellow-400 mb-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-xs text-white/50 italic">Akumulasi seluruh pembayaran</div>
        </div>

        <div class="bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-colors shadow-lg relative overflow-hidden group">
            <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Total di Laci (Tunai)</div>
            <div class="text-3xl font-display font-bold text-green-400 mb-2">Rp {{ number_format($cashInDrawer + ($activeShift->starting_cash ?? 0), 0, ',', '.') }}</div>
            <div class="text-xs text-white/50 italic">Uang Awal + Hasil Tunai</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 animate-fade-up delay-200">
        <a href="{{ route('toko.kasir.pos') }}" class="bg-[#0f0f0f] hover:bg-[#151515] border border-white/5 hover:border-yellow-500/30 rounded-2xl p-6 flex flex-col items-center justify-center text-center gap-3 transition-all group">
            <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white/70 group-hover:text-yellow-500 group-hover:scale-110 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <span class="text-sm font-semibold text-white/80 group-hover:text-white">Transaksi Baru</span>
        </a>

        <a href="{{ route('toko.kasir.history') }}" class="bg-[#0f0f0f] hover:bg-[#151515] border border-white/5 hover:border-yellow-500/30 rounded-2xl p-6 flex flex-col items-center justify-center text-center gap-3 transition-all group">
            <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white/70 group-hover:text-yellow-500 group-hover:scale-110 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <span class="text-sm font-semibold text-white/80 group-hover:text-white">Riwayat Struk</span>
        </a>

        <a href="{{ route('toko.kasir.shift.history') }}" class="bg-[#0f0f0f] hover:bg-[#151515] border border-white/5 hover:border-yellow-500/30 rounded-2xl p-6 flex flex-col items-center justify-center text-center gap-3 transition-all group">
            <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white/70 group-hover:text-yellow-500 group-hover:scale-110 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-sm font-semibold text-white/80 group-hover:text-white">Riwayat Shift</span>
        </a>

        <button @click="showEndShift = true" :disabled="!activeShift" class="bg-[#0f0f0f] hover:bg-[#151515] border border-white/5 hover:border-red-500/30 rounded-2xl p-6 flex flex-col items-center justify-center text-center gap-3 transition-all group disabled:opacity-20 disabled:cursor-not-allowed">
            <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 group-hover:bg-red-500 group-hover:text-white group-hover:scale-110 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </div>
            <span class="text-sm font-semibold text-red-400 group-hover:text-red-300">Tutup Shift</span>
        </button>
    </div>

    <!-- Modals -->
    <!-- Start Shift Modal -->
    <div x-show="showStartShift" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div @click.away="showStartShift = false" class="bg-[#0f0f0f] border border-white/10 rounded-[2rem] w-full max-w-md p-8 shadow-2xl animate-fade-up">
            <div class="text-center space-y-4">
                <div class="w-20 h-20 bg-green-500/10 rounded-full flex items-center justify-center mx-auto text-green-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-white">Mulai Shift Baru</h2>
                <div class="bg-white/5 p-4 rounded-2xl text-left border border-white/5">
                    <div class="text-[10px] text-white/40 uppercase font-black tracking-[0.2em] mb-1">Informasi Kasir</div>
                    <div class="text-white font-bold">{{ auth()->user()->name }}</div>
                    <div class="text-white/40 text-xs">{{ auth()->user()->email }}</div>
                </div>
                
                <form action="{{ route('toko.kasir.shift.start') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-xs font-black text-white/40 uppercase tracking-widest block text-left">Input Kas Awal di Laci</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-yellow-500 font-bold">Rp</div>
                            <input type="number" name="starting_cash" required placeholder="0" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white font-black text-xl focus:outline-none focus:border-yellow-500 transition-all">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-br from-green-400 to-green-600 py-4 rounded-2xl text-black font-black uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-green-500/20">
                        Buka Terminal Kasir
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- End Shift Modal -->
    <div x-show="showEndShift" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div @click.away="showEndShift = false" class="bg-[#0f0f0f] border border-white/10 rounded-[2rem] w-full max-w-md p-8 shadow-2xl animate-fade-up">
            <div class="text-center space-y-6">
                <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto text-red-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white">Tutup Shift Kasir</h2>
                    <p class="text-white/40 text-sm mt-1">Pastikan semua transaksi telah tersimpan.</p>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between p-3 bg-white/5 border border-white/5 rounded-xl text-sm">
                        <span class="text-white/40">Kasir</span>
                        <span class="text-white font-bold">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-white/5 border border-white/5 rounded-xl text-sm">
                        <span class="text-white/40">Kas Awal</span>
                        <span class="text-white font-bold">Rp {{ number_format($activeShift->starting_cash ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-white/5 border border-white/5 rounded-xl text-sm">
                        <span class="text-white/40">Total Penjualan Tunai</span>
                        <span class="text-green-400 font-bold">Rp {{ number_format($cashInDrawer, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-xl">
                        <span class="text-yellow-500 font-black uppercase text-[10px] tracking-widest">Saldo Akhir Laci</span>
                        <span class="text-yellow-500 font-black">Rp {{ number_format($cashInDrawer + ($activeShift->starting_cash ?? 0), 0, ',', '.') }}</span>
                    </div>
                </div>

                <form action="{{ route('toko.kasir.shift.end') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 py-4 rounded-2xl text-white font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-500/20">
                        Konfirmasi Tutup Shift
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
