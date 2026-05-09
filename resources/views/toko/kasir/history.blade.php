@extends('toko.layouts.kasir')

@section('title', 'Riwayat Penjualan')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-fade-up" x-data="historyPage()">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">RIWAYAT <span class="text-yellow-500">PENJUALAN</span></h1>
            <p class="text-white/40 text-sm mt-1">Pantau semua transaksi yang telah berhasil dilakukan di terminal ini.</p>
        </div>

        <!-- Filter Days -->
        <div class="flex items-center gap-2 bg-white/5 p-1 rounded-2xl border border-white/10 backdrop-blur-md">
            <template x-for="d in [1, 7, 30, 90]" :key="d">
                <a :href="'?days=' + d" 
                   class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all"
                   :class="currentDays == d ? 'bg-yellow-500 text-black shadow-[0_4px_15px_rgba(234,179,8,0.3)]' : 'text-white/50 hover:text-white hover:bg-white/5'"
                   x-text="d == 1 ? 'Hari Ini' : d + ' Hari'">
                </a>
            </template>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white/5 border border-white/10 p-6 rounded-2xl backdrop-blur-sm group hover:border-yellow-500/30 transition-all duration-500">
            <div class="text-white/40 text-[10px] uppercase tracking-[0.2em] mb-1 font-bold">Total Transaksi</div>
            <div class="text-2xl font-black text-white group-hover:text-yellow-500 transition-colors">{{ $totalTransactions }}</div>
        </div>
        <div class="bg-white/5 border border-white/10 p-6 rounded-2xl backdrop-blur-sm group hover:border-yellow-500/30 transition-all duration-500">
            <div class="text-white/40 text-[10px] uppercase tracking-[0.2em] mb-1 font-bold">Total Pendapatan</div>
            <div class="text-2xl font-black text-white group-hover:text-yellow-500 transition-colors">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white/5 border border-white/10 p-6 rounded-2xl backdrop-blur-sm group hover:border-yellow-500/30 transition-all duration-500">
            <div class="text-white/40 text-[10px] uppercase tracking-[0.2em] mb-1 font-bold">Rata-rata Transaksi</div>
            <div class="text-2xl font-black text-white group-hover:text-yellow-500 transition-colors">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white/5 border border-white/10 p-6 rounded-2xl backdrop-blur-sm group hover:border-yellow-500/30 transition-all duration-500">
            <div class="text-white/40 text-[10px] uppercase tracking-[0.2em] mb-1 font-bold">Filter Aktif</div>
            <div class="text-2xl font-black text-yellow-500 uppercase">{{ $days }} Hari Terakhir</div>
        </div>
    </div>

    <!-- Table Section (Desktop) & Card List (Mobile) -->
    <div class="space-y-4">
        <!-- Desktop Table -->
        <div class="hidden md:block bg-white/5 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-md shadow-2xl relative">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.02] border-b border-white/5">
                            <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Invoice</th>
                            <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Waktu</th>
                            <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Pembeli</th>
                            <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black">Kasir</th>
                            <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black text-right">Total</th>
                            <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] text-white/40 font-black text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-white/[0.03] transition-colors group">
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-bold text-yellow-500">{{ $sale->invoice_no }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium text-sm">{{ $sale->created_at->format('d M Y') }}</div>
                                <div class="text-white/30 text-[10px]">{{ $sale->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-bold text-sm">{{ $sale->customer_name ?: 'Umum' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center text-[10px] font-bold text-yellow-500">
                                        {{ strtoupper(substr($sale->cashier->name ?? 'K', 0, 1)) }}
                                    </div>
                                    <span class="text-white/60 text-xs">{{ $sale->cashier->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-white font-black font-mono text-sm">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="showDetails({{ \Illuminate\Support\Js::from($sale) }})" 
                                        class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-white/40 hover:text-yellow-500 hover:border-yellow-500/30 hover:bg-yellow-500/5 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center opacity-20">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 02 2h2a2 2 0 02 2-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="text-xl font-bold uppercase tracking-widest">Belum ada transaksi</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card List -->
        <div class="md:hidden space-y-4">
            @forelse($sales as $sale)
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-md space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-yellow-500 font-mono text-[10px] font-bold mb-1">{{ $sale->invoice_no }}</div>
                        <div class="text-white font-bold text-sm">{{ $sale->customer_name ?: 'Umum' }}</div>
                    </div>
                    <button @click="showDetails({{ \Illuminate\Support\Js::from($sale) }})" 
                            class="p-2.5 rounded-xl bg-yellow-500/10 border border-yellow-500/20 text-yellow-500 hover:bg-yellow-500 hover:text-black transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                    <div>
                        <div class="text-white/30 text-[9px] uppercase tracking-widest font-bold mb-1">Waktu</div>
                        <div class="text-white text-xs">{{ $sale->created_at->format('d M, H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-white/30 text-[9px] uppercase tracking-widest font-bold mb-1">Total</div>
                        <div class="text-white font-black font-mono text-xs text-yellow-500">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <div class="w-5 h-5 rounded-full bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center text-[9px] font-bold text-yellow-500">
                        {{ strtoupper(substr($sale->cashier->name ?? 'K', 0, 1)) }}
                    </div>
                    <span class="text-white/40 text-[10px]">Kasir: {{ $sale->cashier->name ?? '-' }}</span>
                </div>
            </div>
            @empty
            <div class="bg-white/5 border border-white/10 rounded-2xl p-10 text-center opacity-20">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-sm font-bold uppercase tracking-widest">Belum ada transaksi</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($sales->hasPages())
        <div class="px-6 py-6 bg-white/[0.01] rounded-2xl border border-white/5 md:border-none">
            {{ $sales->links('vendor.pagination.simple-tailwind-toko') }}
        </div>
        @endif
    </div>

    <!-- Detail Modal -->
    <div x-show="selectedSale" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-[#0a0a0a] border border-white/10 w-full max-w-lg rounded-2xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] relative mx-auto"
             @click.away="selectedSale = null">
            
            <!-- Modal Header -->
            <div class="p-6 md:p-8 border-b border-white/5 flex items-center justify-between">
                <div>
                    <div class="text-yellow-500 font-mono text-[10px] md:text-xs font-bold mb-1" x-text="selectedSale?.invoice_no"></div>
                    <h3 class="text-lg md:text-xl font-black text-white uppercase">Detail Pesanan</h3>
                </div>
                <button @click="selectedSale = null" class="p-2 hover:bg-white/5 rounded-full text-white/20 hover:text-white transition-all">
                    <svg class="w-5 h-5 md:w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 md:p-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <div class="space-y-6">
                    <!-- Customer Info -->
                    <div class="flex justify-between items-center text-xs md:text-sm bg-white/[0.02] p-3 rounded-xl border border-white/5">
                        <span class="text-white/30 uppercase tracking-widest text-[9px] md:text-[10px] font-bold">Pelanggan</span>
                        <span class="text-white font-bold" x-text="selectedSale?.customer_name || 'Umum'"></span>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-3">
                        <div class="text-white/30 uppercase tracking-widest text-[9px] md:text-[10px] font-bold ml-1">Rincian Item</div>
                        <template x-for="item in selectedSale?.items" :key="item.id">
                            <div class="bg-white/5 border border-white/5 p-4 rounded-2xl flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-white truncate" x-text="item.product_name"></div>
                                    <div class="text-[10px] text-white/40 italic" x-text="item.variant_name ? item.variant_name : ''"></div>
                                    <div class="text-[10px] md:text-xs text-yellow-500/70 font-mono mt-1" x-text="item.qty + ' x Rp ' + formatNumber(item.price)"></div>
                                </div>
                                <div class="text-sm font-black text-white font-mono text-right shrink-0 border-t sm:border-t-0 border-white/5 pt-2 sm:pt-0" x-text="'Rp ' + formatNumber(item.subtotal)"></div>
                            </div>
                        </template>
                    </div>

                    <!-- Totals -->
                    <div class="pt-6 border-t border-white/5 space-y-3">
                        <div class="flex justify-between text-xs text-white/40">
                            <span>Subtotal</span>
                            <span class="font-mono" x-text="'Rp ' + formatNumber(selectedSale?.subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-xs text-white/40">
                            <span x-text="'Pajak (' + selectedSale?.tax_rate + '%)'"></span>
                            <span class="font-mono" x-text="'Rp ' + formatNumber(selectedSale?.tax_amount)"></span>
                        </div>
                        <div class="flex justify-between items-end pt-3">
                            <span class="text-white font-black uppercase tracking-widest text-[10px] md:text-xs">Total Tagihan</span>
                            <span class="text-xl md:text-2xl text-yellow-500 font-black font-mono leading-none" x-text="'Rp ' + formatNumber(selectedSale?.total_amount)"></span>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="p-4 bg-yellow-500/5 border border-yellow-500/10 rounded-2xl space-y-3">
                        <div class="flex justify-between text-xs">
                            <span class="text-white/40">Uang Diterima</span>
                            <span class="text-white font-bold font-mono" x-text="'Rp ' + formatNumber(selectedSale?.paid_amount)"></span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-white/40">Kembalian</span>
                            <span class="text-green-400 font-bold font-mono" x-text="'Rp ' + formatNumber(selectedSale?.change_amount)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 md:p-8 border-t border-white/5 bg-white/[0.01]">
                <button @click="selectedSale = null" class="w-full bg-white/5 hover:bg-white/10 text-white font-bold py-4 rounded-2xl transition-all uppercase tracking-widest text-[10px] md:text-xs">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(234, 179, 8, 0.2); }
</style>

<script>
function historyPage() {
    return {
        selectedSale: null,
        currentDays: {{ $days }},

        showDetails(sale) {
            this.selectedSale = sale;
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        }
    }
}
</script>
@endsection
