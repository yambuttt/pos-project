@extends('toko.layouts.kasir')

@section('title', 'Riwayat Shift Kasir')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 animate-fade-up" x-data="{ 
    selectedShift: null,
    itemsSold: [],
    loading: false,
    async showDetail(id) {
        this.loading = true;
        this.selectedShift = null;
        try {
            const response = await fetch(`/toko/kasir/shift/${id}`);
            const data = await response.json();
            this.selectedShift = data.shift;
            this.itemsSold = data.items_sold;
        } catch (e) {
            alert('Gagal mengambil detail shift');
        } finally {
            this.loading = false;
        }
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Riwayat <span class="text-yellow-500">Shift</span></h1>
            <p class="text-white/40 text-sm">Daftar rekapan shift kasir dari waktu ke waktu.</p>
        </div>
        <a href="{{ route('toko.kasir.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white/70 hover:text-white hover:bg-white/10 transition-all text-sm font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Table Section -->
    <div class="bg-[#0a0a0a] border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.02] border-b border-white/5">
                        <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Waktu Shift</th>
                        <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Kasir</th>
                        <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Kas Awal</th>
                        <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Total Penjualan</th>
                        <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Saldo Akhir</th>
                        <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Status</th>
                        <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($shifts as $shift)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-6 py-4">
                            <div class="text-white font-bold text-sm">{{ $shift->start_time->format('d M Y') }}</div>
                            <div class="text-white/30 text-[10px]">{{ $shift->start_time->format('H:i') }} - {{ $shift->end_time ? $shift->end_time->format('H:i') : 'Aktif' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white font-medium text-sm">{{ $shift->user->name }}</div>
                            <div class="text-white/30 text-[10px]">{{ $shift->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-white font-mono text-sm">
                            Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-green-400 font-mono text-sm font-bold">
                            + Rp {{ number_format($shift->total_sales_cash, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-yellow-500 font-mono text-sm font-black">
                            Rp {{ number_format($shift->starting_cash + $shift->total_sales_cash, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($shift->status === 'open')
                                <span class="px-2.5 py-1 rounded-full bg-green-500/10 border border-green-500/20 text-green-500 text-[10px] font-black uppercase tracking-widest">Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-white/40 text-[10px] font-black uppercase tracking-widest">Selesai</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button @click="showDetail({{ $shift->id }})" class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:text-yellow-500 hover:border-yellow-500/30 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center opacity-20">
                            <p class="text-xl font-bold uppercase tracking-[0.2em]">Belum ada riwayat shift</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($shifts->hasPages())
        <div class="p-6 border-t border-white/5">
            {{ $shifts->links() }}
        </div>
        @endif
    </div>

    <!-- Detail Shift Modal -->
    <template x-teleport="body">
        <div x-show="selectedShift || loading" 
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-cloak>
            
            <div @click.away="selectedShift = null" 
                 class="bg-[#0f0f0f] border border-white/10 rounded-3xl w-full max-w-2xl shadow-[0_30px_100px_rgba(0,0,0,0.8)] overflow-hidden max-h-[85vh] flex flex-col relative">
                
                <!-- Close Button (Floating) -->
                <button @click="selectedShift = null" class="absolute top-5 right-6 z-20 p-2 bg-white/5 hover:bg-white/10 rounded-full transition-all text-white/40 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <template x-if="loading">
                    <div class="p-20 text-center">
                        <div class="animate-spin w-10 h-10 border-4 border-yellow-500 border-t-transparent rounded-full mx-auto mb-4"></div>
                        <p class="text-white/40 font-bold uppercase tracking-widest text-[10px]">Memuat Data...</p>
                    </div>
                </template>

                <template x-if="selectedShift">
                    <div class="flex flex-col flex-1 overflow-hidden">
                        <!-- Header -->
                        <div class="px-8 py-7 border-b border-white/5 bg-white/[0.01]">
                            <h2 class="text-xl font-black text-white uppercase tracking-tight leading-none mb-1">Detail Rekap <span class="text-yellow-500">Shift</span></h2>
                            <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest" x-text="`${selectedShift.user.name} • ${selectedShift.user.email}`"></p>
                        </div>

                        <!-- Scrollable Content -->
                        <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
                            <!-- Summary Stats -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white/[0.03] p-4 rounded-2xl border border-white/5">
                                    <div class="text-[9px] text-white/40 uppercase font-black tracking-widest mb-1.5">Kas Awal</div>
                                    <div class="text-white font-bold font-mono text-sm" x-text="'Rp ' + Number(selectedShift.starting_cash).toLocaleString('id-ID')"></div>
                                </div>
                                <div class="bg-white/[0.03] p-4 rounded-2xl border border-white/5">
                                    <div class="text-[9px] text-white/40 uppercase font-black tracking-widest mb-1.5">Tunai</div>
                                    <div class="text-green-400 font-bold font-mono text-sm" x-text="'+ Rp ' + Number(selectedShift.total_sales_cash).toLocaleString('id-ID')"></div>
                                </div>
                                <div class="bg-white/[0.03] p-4 rounded-2xl border border-white/5">
                                    <div class="text-[9px] text-white/40 uppercase font-black tracking-widest mb-1.5">Non-Tunai</div>
                                    <div class="text-blue-400 font-bold font-mono text-sm" x-text="'+ Rp ' + Number(selectedShift.total_sales_non_cash).toLocaleString('id-ID')"></div>
                                </div>
                                <div class="bg-yellow-500/10 p-4 rounded-2xl border border-yellow-500/20">
                                    <div class="text-[9px] text-yellow-500 uppercase font-black tracking-widest mb-1.5">Total Laci</div>
                                    <div class="text-yellow-500 font-black font-mono text-sm" x-text="'Rp ' + (Number(selectedShift.starting_cash) + Number(selectedShift.total_sales_cash)).toLocaleString('id-ID')"></div>
                                </div>
                            </div>

                            <!-- Time Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white/[0.01] p-5 rounded-2xl border border-white/5 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-yellow-500/10 flex items-center justify-center text-yellow-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-white/30 uppercase font-black tracking-widest mb-0.5">Waktu Mulai</div>
                                        <div class="text-white font-bold text-xs" x-text="new Date(selectedShift.start_time).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })"></div>
                                    </div>
                                </div>
                                <div class="bg-white/[0.01] p-5 rounded-2xl border border-white/5 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-white/30 uppercase font-black tracking-widest mb-0.5">Waktu Berakhir</div>
                                        <div class="text-white font-bold text-xs" x-text="selectedShift.end_time ? new Date(selectedShift.end_time).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : 'Masih Berjalan'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Item Table -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between px-1">
                                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Item Terjual</h3>
                                    <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/5 text-white/40 text-[9px] font-black uppercase tracking-widest" x-text="`${selectedShift.total_transactions} Transaksi`"></span>
                                </div>
                                <div class="bg-white/[0.02] rounded-2xl overflow-hidden border border-white/5">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-white/5 border-b border-white/5">
                                                <th class="px-6 py-4 text-[9px] uppercase font-black text-white/30 tracking-widest">Produk</th>
                                                <th class="px-6 py-4 text-[9px] uppercase font-black text-white/30 tracking-widest text-center">Qty</th>
                                                <th class="px-6 py-4 text-[9px] uppercase font-black text-white/30 tracking-widest text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5">
                                            <template x-for="item in itemsSold" :key="item.product_name + item.variant_name">
                                                <tr class="hover:bg-white/[0.02] transition-colors">
                                                    <td class="px-6 py-4">
                                                        <div class="text-white font-bold text-sm" x-text="item.product_name"></div>
                                                        <div class="text-white/30 text-[10px]" x-show="item.variant_name" x-text="item.variant_name"></div>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <span class="text-white font-mono font-bold text-sm" x-text="item.total_qty"></span>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <span class="text-yellow-500 font-mono font-black text-sm" x-text="'Rp ' + Number(item.total_amount).toLocaleString('id-ID')"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-6 border-t border-white/5 bg-white/[0.02]">
                            <button @click="selectedShift = null" class="w-full bg-white/5 hover:bg-white/10 text-white font-black py-4 rounded-2xl transition-all uppercase tracking-widest text-[10px]">
                                Tutup Detail Rekapan
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
@endsection