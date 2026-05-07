@extends('toko.layouts.admin')

@section('title', 'Dashboard Admin Toko')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <!-- Greeting Section -->
    <div class="relative overflow-hidden rounded-3xl p-8 sm:p-10 border border-yellow-500/20 shadow-[0_0_40px_rgba(234,179,8,0.05)]">
        <!-- Abstract Background inside greeting -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#111] via-[#0a0a0a] to-[#020202]"></div>
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-yellow-500/10 rounded-full mix-blend-screen filter blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-20 left-10 w-48 h-48 bg-yellow-600/10 rounded-full mix-blend-screen filter blur-2xl animate-pulse" style="animation-delay: 2s;"></div>

        <div class="relative z-10">
            <h1 class="text-3xl sm:text-4xl font-display font-bold text-white mb-2 tracking-tight">
                Selamat Datang, <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-600">{{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}</span>
            </h1>
            <p class="text-white/60 font-light text-sm sm:text-base max-w-xl">
                Ini adalah pusat kendali untuk mengelola operasi Ayo Renne Store. Pantau performa toko, inventaris, dan penjualan secara real-time.
            </p>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Stat Card 1 -->
        <div class="group relative bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-all duration-300 shadow-lg overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Total Pendapatan</div>
                    <div class="text-2xl font-bold text-white mb-2">Rp 12.500.000</div>
                    <div class="text-xs text-green-400 font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        12% dari kemarin
                    </div>
                </div>
                <div class="p-3 bg-yellow-500/10 rounded-xl text-yellow-500 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="group relative bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-all duration-300 shadow-lg overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Total Transaksi</div>
                    <div class="text-2xl font-bold text-white mb-2">145</div>
                    <div class="text-xs text-green-400 font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        8% dari kemarin
                    </div>
                </div>
                <div class="p-3 bg-yellow-500/10 rounded-xl text-yellow-500 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="group relative bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-all duration-300 shadow-lg overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Produk Terjual</div>
                    <div class="text-2xl font-bold text-white mb-2">320</div>
                    <div class="text-xs text-red-400 font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                        2% dari kemarin
                    </div>
                </div>
                <div class="p-3 bg-yellow-500/10 rounded-xl text-yellow-500 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="group relative bg-[#0a0a0a] rounded-2xl p-6 border border-white/5 hover:border-yellow-500/30 transition-all duration-300 shadow-lg overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <div class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">Stok Menipis</div>
                    <div class="text-2xl font-bold text-yellow-400 mb-2">12 Item</div>
                    <div class="text-xs text-white/50 font-medium flex items-center gap-1">
                        Perlu segera direstock
                    </div>
                </div>
                <div class="p-3 bg-yellow-500/10 rounded-xl text-yellow-500 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Activity Placeholder -->
    <div class="bg-[#0a0a0a]/80 backdrop-blur-md rounded-2xl border border-white/5 p-6 shadow-xl">
        <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
            Transaksi Terbaru
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-white/40 uppercase tracking-widest text-[10px] border-b border-white/10">
                        <th class="pb-3 font-semibold">ID Transaksi</th>
                        <th class="pb-3 font-semibold">Waktu</th>
                        <th class="pb-3 font-semibold">Kasir</th>
                        <th class="pb-3 font-semibold">Total</th>
                        <th class="pb-3 font-semibold text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-white/80">
                    <tr class="group hover:bg-white/5 transition-colors">
                        <td class="py-4 font-mono text-yellow-500">#TRX-9921</td>
                        <td class="py-4">Hari ini, 14:30</td>
                        <td class="py-4 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-bold">K1</div>
                            Kasir Utama
                        </td>
                        <td class="py-4 font-bold">Rp 350.000</td>
                        <td class="py-4 text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-green-500/10 text-green-400 border border-green-500/20">Selesai</span>
                        </td>
                    </tr>
                    <tr class="group hover:bg-white/5 transition-colors">
                        <td class="py-4 font-mono text-yellow-500">#TRX-9920</td>
                        <td class="py-4">Hari ini, 14:15</td>
                        <td class="py-4 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-bold">K2</div>
                            Kasir Dua
                        </td>
                        <td class="py-4 font-bold">Rp 1.250.000</td>
                        <td class="py-4 text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-green-500/10 text-green-400 border border-green-500/20">Selesai</span>
                        </td>
                    </tr>
                    <tr class="group hover:bg-white/5 transition-colors">
                        <td class="py-4 font-mono text-yellow-500">#TRX-9919</td>
                        <td class="py-4">Hari ini, 13:45</td>
                        <td class="py-4 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-bold">K1</div>
                            Kasir Utama
                        </td>
                        <td class="py-4 font-bold">Rp 80.000</td>
                        <td class="py-4 text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-green-500/10 text-green-400 border border-green-500/20">Selesai</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
