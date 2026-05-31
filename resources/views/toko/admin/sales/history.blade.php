@extends('toko.layouts.admin')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-20" 
     x-data="{ 
        showFilters: {{ $period == 'custom' ? 'true' : 'false' }}, 
        selectedPeriod: '{{ $period }}',
        selectedSale: null,
        openModal: false,
        showSale(sale) {
            this.selectedSale = sale;
            this.openModal = true;
        },
        toggleMode() {
            let currentUrl = new URL(window.location.href);
            let nextMode = '{{ ($viewMode ?? 'jual') === 'jual' ? 'semua' : 'jual' }}';
            currentUrl.searchParams.set('view', nextMode);
            window.location.href = currentUrl.toString();
        }
     }"
     @keydown.window.ctrl.shift.s.prevent="toggleMode()">
    <!-- Header & Quick Stats -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-3xl font-display font-bold text-white mb-2">Riwayat Transaksi</h1>
            <p class="text-white/50 text-sm">Pantau dan analisa performa penjualan toko Anda.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="showFilters = !showFilters" 
                    :class="showFilters ? 'bg-yellow-500 text-black' : 'bg-white/5 text-white/70 hover:bg-white/10'"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 border border-white/5 shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter Periode
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-2xl shadow-xl group hover:border-yellow-500/30 transition-all duration-300">
            <div class="text-white/40 text-[10px] uppercase tracking-widest font-bold mb-1">Total Pendapatan</div>
            <div class="text-xl font-bold text-white">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-2xl shadow-xl group hover:border-yellow-500/30 transition-all duration-300">
            <div class="text-white/40 text-[10px] uppercase tracking-widest font-bold mb-1">Jumlah Transaksi</div>
            <div class="text-xl font-bold text-white">{{ number_format($stats['total_transactions'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-2xl shadow-xl group hover:border-yellow-500/30 transition-all duration-300">
            <div class="text-white/40 text-[10px] uppercase tracking-widest font-bold mb-1">Rata-rata Transaksi</div>
            <div class="text-xl font-bold text-white">Rp {{ number_format($stats['avg_transaction'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Filter Panel -->
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-yellow-500/20 p-6 rounded-3xl shadow-2xl"
         style="display: none;">
        
        <form action="{{ route('toko.admin.sales.history') }}" method="GET" class="space-y-6">
            <input type="hidden" name="view" value="{{ $viewMode ?? 'jual' }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-white/40 uppercase tracking-widest">Pilih Periode</label>
                    <select name="period" x-model="selectedPeriod" 
                            x-on:change="if(selectedPeriod != 'custom') $el.form.submit()"
                            class="w-full bg-black/40 border border-white/10 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-yellow-500/50 transition-all">
                        <option value="today">Hari Ini</option>
                        <option value="weekly">Minggu Ini</option>
                        <option value="monthly">Bulan Ini</option>
                        <option value="yearly">Tahun Ini</option>
                        <option value="custom">Pilih Tanggal</option>
                    </select>
                </div>

                <div class="space-y-2" x-show="selectedPeriod == 'custom'" x-cloak x-transition>
                    <label class="text-xs font-bold text-white/40 uppercase tracking-widest">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-black/40 border border-white/10 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-yellow-500/50 transition-all">
                </div>
                <div class="space-y-2" x-show="selectedPeriod == 'custom'" x-cloak x-transition>
                    <label class="text-xs font-bold text-white/40 uppercase tracking-widest">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-black/40 border border-white/10 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-yellow-500/50 transition-all">
                </div>
                <div class="flex items-end" x-show="selectedPeriod == 'custom'" x-cloak x-transition>
                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-3 rounded-xl transition-all shadow-lg shadow-yellow-500/20 active:scale-95">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Sales Table / List -->
    <div class="bg-[#0a0a0a]/60 backdrop-blur-md rounded-3xl border border-white/5 overflow-hidden shadow-2xl">
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-white/40 uppercase tracking-[0.2em] text-[10px] border-b border-white/10">
                        <th class="px-8 py-5 font-bold">Invoice</th>
                        <th class="px-6 py-5 font-bold">Waktu</th>
                        <th class="px-6 py-5 font-bold">Kasir</th>
                        <th class="px-6 py-5 font-bold">Total Belanja</th>
                        <th class="px-6 py-5 font-bold">Metode</th>
                        <th class="px-8 py-5 font-bold text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($sales as $index => $sale)
                    <tr class="group hover:bg-white/5 transition-all duration-300"
                        style="animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: {{ ($index + 1) * 0.05 }}s; opacity: 0;">
                        <td class="px-8 py-5 font-mono text-yellow-500 font-bold tracking-wider">#{{ $sale->invoice_no }}</td>
                        <td class="px-6 py-5">
                            <div class="text-white">{{ $sale->paid_at ? $sale->paid_at->format('d M Y') : '-' }}</div>
                            <div class="text-white/40 text-[10px] mt-1">{{ $sale->paid_at ? $sale->paid_at->format('H:i') : '' }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-yellow-500/10 flex items-center justify-center text-yellow-500 font-bold text-[10px] border border-yellow-500/20">
                                    {{ strtoupper(substr($sale->cashier->name ?? 'K', 0, 1)) }}
                                </div>
                                <span class="text-white/80">{{ $sale->cashier->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 font-bold text-white text-base">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-5">
                            <span class="px-2 py-1 bg-white/5 border border-white/10 rounded-lg text-[10px] uppercase font-bold text-white/60 tracking-widest">
                                {{ $sale->payment_method ?? 'Cash' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <button @click="showSale({{ json_encode($sale) }})" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider bg-white/5 text-white/70 hover:bg-yellow-500 hover:text-black transition-all border border-white/10">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-white/20 italic text-lg">Tidak ada transaksi ditemukan pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile / Tablet List -->
        <div class="md:hidden divide-y divide-white/5">
            @forelse($sales as $index => $sale)
            <div class="p-6 space-y-4 hover:bg-white/5 transition-all active:bg-white/10"
                 style="animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: {{ ($index + 1) * 0.05 }}s; opacity: 0;">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <div class="font-mono text-yellow-500 font-bold text-sm tracking-widest">#{{ $sale->invoice_no }}</div>
                        <div class="text-white/40 text-[10px]">{{ $sale->paid_at ? $sale->paid_at->format('d M Y, H:i') : '-' }}</div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-green-500/10 text-green-400 border border-green-500/20">
                        Paid
                    </span>
                </div>
                
                <div class="flex justify-between items-end">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-yellow-500/10 flex items-center justify-center text-yellow-500 font-bold text-[10px] border border-yellow-500/20">
                            {{ strtoupper(substr($sale->cashier->name ?? 'K', 0, 1)) }}
                        </div>
                        <button @click="showSale({{ json_encode($sale) }})" class="text-yellow-500 text-xs font-bold underline decoration-yellow-500/30">Lihat Detail</button>
                    </div>
                    <div class="text-right">
                        <div class="text-white/40 text-[10px] uppercase font-bold tracking-widest mb-1">Total</div>
                        <div class="text-xl font-bold text-white font-display tracking-tight">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-20 text-center text-white/20 italic">Tidak ada transaksi ditemukan.</div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($sales->hasPages())
        <div class="px-8 py-6 border-t border-white/5 bg-black/20">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
    <!-- Transaction Detail Modal -->
    <div x-show="openModal" 
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6"
         x-cloak>
        <!-- Backdrop -->
        <div x-show="openModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="openModal = false"
             class="absolute inset-0 bg-black/90 backdrop-blur-sm"></div>

        <!-- Modal Content -->
        <div x-show="openModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative bg-[#0d0d0d] border border-white/10 w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)]">
            
            <!-- Modal Header -->
            <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-gradient-to-r from-yellow-500/10 to-transparent">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">Detail Transaksi</h3>
                    <p class="text-yellow-500 font-mono text-xs mt-1" x-text="'#' + (selectedSale?.invoice_no || '')"></p>
                </div>
                <button @click="openModal = false" class="p-2 hover:bg-white/10 rounded-full transition-colors text-white/40 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-8 space-y-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                        <div class="text-[10px] uppercase font-black text-white/30 tracking-widest mb-1">Kasir</div>
                        <div class="text-sm font-bold text-white/90" x-text="selectedSale?.cashier?.name || 'System'"></div>
                    </div>
                    <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                        <div class="text-[10px] uppercase font-black text-white/30 tracking-widest mb-1">Waktu</div>
                        <div class="text-sm font-bold text-white/90" x-text="selectedSale ? new Date(selectedSale.paid_at).toLocaleString('id-ID', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'}) : ''"></div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="space-y-3">
                    <div class="text-[10px] uppercase font-black text-white/30 tracking-widest px-1">Daftar Item</div>
                    <div class="space-y-2">
                        <template x-for="item in (selectedSale?.items || [])" :key="item.id">
                            <div class="flex items-center justify-between p-4 bg-white/[0.02] rounded-2xl border border-white/5">
                                <div class="flex-1">
                                    <div class="text-sm font-bold text-white/90" x-text="item.product_name"></div>
                                    <div class="text-[10px] text-white/40 mt-0.5" x-text="(item.variant_name ? item.variant_name + ' • ' : '') + item.qty + ' x Rp ' + new Intl.NumberFormat('id-ID').format(item.price)"></div>
                                </div>
                                <div class="text-sm font-bold text-white" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.subtotal)"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Summary -->
                <div class="pt-6 border-t border-white/5 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-white/40">Subtotal</span>
                        <span class="text-white/80" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedSale?.subtotal || 0)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-white/40">Pajak (10%)</span>
                        <span class="text-white/80" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedSale?.tax_amount || 0)"></span>
                    </div>
                    <div class="flex justify-between items-center pt-3">
                        <span class="text-base font-bold text-white">Total Akhir</span>
                        <span class="text-2xl font-black text-yellow-500" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedSale?.total_amount || 0)"></span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-8 bg-white/5 flex gap-3">
                <button @click="openModal = false" class="flex-1 py-4 rounded-2xl text-sm font-bold text-white/60 hover:text-white hover:bg-white/5 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(234, 179, 8, 0.2);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(234, 179, 8, 0.4);
    }
    /* Custom pagination styling for dark mode */
    .pagination {
        display: flex;
        gap: 0.5rem;
    }
    .page-item .page-link {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.6);
        border-radius: 0.75rem;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }
    .page-item.active .page-link {
        background: #eab308;
        border-color: #eab308;
        color: black;
        font-weight: bold;
    }
    .page-item:hover .page-link {
        background: rgba(234,179,8,0.2);
        color: #facc15;
    }
</style>
@endpush
@endsection
