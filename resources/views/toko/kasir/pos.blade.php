@extends('toko.layouts.kasir')

@section('title', 'Point of Sale')

@section('content')
<div class="h-[calc(100vh-theme(spacing.20))] overflow-hidden -m-6" 
     x-data="posSystem()" 
     x-init="init()">
    
    <div class="flex flex-col lg:flex-row h-full w-full gap-0 bg-[#050505]">
        
        <div class="flex-1 flex flex-col h-full border-r border-white/5">
            <div class="p-6 space-y-4 bg-[#0a0a0a]/50 backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white tracking-tight">Pilih Produk</h2>
                    <span class="text-[10px] bg-yellow-500/10 text-yellow-500 px-2 py-1 rounded-full font-bold border border-yellow-500/20">RETAIL MODE</span>
                </div>
                
                <div class="relative group">
                    <input type="text" 
                           x-model="search"
                           placeholder="Cari nama produk atau SKU..." 
                           class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 px-12 text-white placeholder-white/20 focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all outline-none">
                    <svg class="w-5 h-5 absolute left-4 top-3.5 text-white/20 group-focus-within:text-yellow-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar">
                    <button @click="selectedCategory = 'all'"
                            :class="selectedCategory === 'all' ? 'bg-yellow-500 text-black shadow-[0_0_15px_rgba(234,179,8,0.4)]' : 'bg-white/5 text-white/50 hover:bg-white/10'"
                            class="px-5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-300">
                        Semua
                    </button>
                    @foreach($categories as $cat)
                    <button @click="selectedCategory = '{{ $cat->id }}'"
                            :class="selectedCategory == '{{ $cat->id }}' ? 'bg-yellow-500 text-black shadow-[0_0_15px_rgba(234,179,8,0.4)]' : 'bg-white/5 text-white/50 hover:bg-white/10'"
                            class="px-5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-300">
                        {{ $cat->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="handleProductClick(product)"
                             :class="getProductStock(product) <= 0 ? 'opacity-40 grayscale cursor-not-allowed' : 'cursor-pointer hover:border-yellow-500/50 hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)] transform active:scale-95'"
                             class="group relative bg-[#0a0a0a] border border-white/5 rounded-3xl overflow-hidden transition-all duration-500">
                            
                            <div class="aspect-square bg-gradient-to-br from-white/5 to-transparent flex items-center justify-center relative overflow-hidden">
                                <img :src="product.image_url ? '/storage/' + product.image_url : '/images/placeholder.png'" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-60 group-hover:opacity-100">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-transparent to-transparent opacity-80"></div>
                                
                                <div class="absolute top-3 right-3">
                                    <span :class="getProductStock(product) <= 0 ? 'bg-gray-700 text-gray-300' : (getProductStock(product) <= 5 ? 'bg-red-500' : 'bg-green-500')" 
                                          class="text-[9px] text-white font-black px-2 py-1 rounded-lg shadow-lg" 
                                          x-text="getProductStock(product) <= 0 ? 'HABIS' : (product.has_variants ? 'Ada Varian' : getProductStock(product) + ' Stok')"></span>
                                </div>
                            </div>

                            <div class="p-4 space-y-1">
                                <p class="text-[10px] text-yellow-500 font-bold uppercase tracking-widest" x-text="product.category?.name || '-'"></p>
                                <h3 class="text-white font-medium text-sm truncate" x-text="product.name"></h3>
                                <p class="text-white/40 font-mono text-[11px]" x-text="product.has_variants ? getPriceRange(product) : ('Rp ' + formatNumber(product.price))"></p>
                            </div>

                            <template x-if="getProductStock(product) > 0">
                                <div class="absolute inset-0 flex items-center justify-center bg-yellow-500/10 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="bg-yellow-500 text-black p-3 rounded-2xl shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-[400px] bg-[#0a0a0a] flex flex-col h-full shadow-2xl relative z-10 border-l border-white/5">
            <div class="p-6 border-b border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-yellow-500/10 p-2 rounded-xl">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <h2 class="text-white font-bold">Keranjang Belanja</h2>
                </div>
                <button @click="cart = []" class="text-white/20 hover:text-red-400 text-xs transition-colors">Reset</button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-center space-y-4 opacity-20">
                        <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="text-sm">Keranjang masih kosong</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex items-center gap-4 bg-white/5 p-3 rounded-2xl border border-white/5 group hover:border-white/10 transition-all"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="w-12 h-12 rounded-xl bg-white/5 shrink-0 overflow-hidden">
                            <img :src="item.image_url ? '/storage/' + item.image_url : '/images/placeholder.png'" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-white text-xs font-bold truncate" x-text="item.name"></h4>
                            <p x-show="item.variant_name" class="text-yellow-500/70 text-[10px] uppercase tracking-wider" x-text="item.variant_name"></p>
                            <p class="text-yellow-500 font-mono text-[10px]" x-text="'Rp ' + formatNumber(item.price) + ' × ' + item.qty + ' = Rp ' + formatNumber(item.price * item.qty)"></p>
                        </div>
                        <div class="flex items-center bg-black/40 rounded-lg p-1 border border-white/5">
                            <button @click="updateQty(index, -1)" class="p-1 hover:text-red-400 transition-colors"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>
                            <span class="w-6 text-center text-[11px] font-bold text-white" x-text="item.qty"></span>
                            <button @click="updateQty(index, 1)" 
                                    :class="item.qty >= item.maxStock ? 'opacity-30 cursor-not-allowed' : 'hover:text-green-400'"
                                    :disabled="item.qty >= item.maxStock"
                                    class="p-1 transition-colors"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-6 bg-[#070707] border-t border-white/5 space-y-4 shadow-[0_-10px_40px_rgba(0,0,0,0.5)]">
                <div class="space-y-2">
                    <div class="flex justify-between text-white/40 text-xs">
                        <span>Subtotal</span>
                        <span x-text="'Rp ' + formatNumber(totalPrice)"></span>
                    </div>
                    <div class="flex justify-between text-white/40 text-xs">
                        <span>Pajak (10%)</span>
                        <span x-text="'Rp ' + formatNumber(taxAmount)"></span>
                    </div>
                    <div class="flex justify-between items-end pt-2 border-t border-white/5">
                        <span class="text-white font-bold text-sm uppercase tracking-widest">Total Tagihan</span>
                        <span class="text-2xl font-black text-yellow-500 font-mono" x-text="'Rp ' + formatNumber(grandTotal)"></span>
                    </div>
                </div>

                <button @click="checkout()" 
                        :disabled="cart.length === 0"
                        :class="cart.length === 0 ? 'opacity-50 grayscale cursor-not-allowed' : 'hover:scale-[1.02] active:scale-95 shadow-[0_10px_20px_rgba(234,179,8,0.2)]'"
                        class="w-full bg-yellow-500 text-black py-4 rounded-2xl font-black text-sm transition-all flex items-center justify-center gap-3">
                    LANJUT PEMBAYARAN
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Step 1: Pilih Metode Pembayaran -->
    <div x-show="showPaymentModal" style="display:none" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/90 backdrop-blur-md" @click="showPaymentModal=false"></div>
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="relative bg-[#121212] border border-white/10 rounded-[32px] w-full max-w-md p-8 shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white">Metode Pembayaran</h3>
                <p class="text-white/40 text-sm mt-1">Total: <span class="text-yellow-500 font-bold" x-text="'Rp '+formatNumber(grandTotal)"></span></p>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <button @click="selectPaymentMethod('cash')" class="bg-white/5 border border-white/5 p-5 rounded-2xl hover:border-yellow-500/50 hover:bg-yellow-500/5 transition-all group flex flex-col items-center gap-2">
                    <svg class="w-8 h-8 text-white/60 group-hover:text-yellow-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                    <span class="text-white group-hover:text-yellow-500 font-bold text-sm">TUNAI / CASH</span>
                </button>
                <div class="bg-white/5 border border-white/5 p-5 rounded-2xl opacity-40 cursor-not-allowed flex flex-col items-center gap-2">
                    <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    <span class="text-white/40 font-bold text-sm">QRIS<br><span class="text-[10px] font-normal">(Segera Hadir)</span></span>
                </div>
            </div>
            <button @click="showPaymentModal=false" class="w-full text-white/30 hover:text-white text-xs font-bold py-2">BATALKAN</button>
        </div>
    </div>

    <!-- Step 2: Rincian Cash -->
    <div x-show="showCashModal" style="display:none" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/90 backdrop-blur-md" @click="showCashModal=false"></div>
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="relative bg-[#121212] border border-yellow-500/20 rounded-[28px] w-full max-w-md p-7 shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-1">Rincian Pembayaran Cash</h3>
            <p class="text-white/40 text-xs mb-6">Isi detail transaksi di bawah ini</p>

            <!-- Order Summary -->
            <div class="bg-white/5 rounded-2xl p-4 mb-5 space-y-2 max-h-36 overflow-y-auto custom-scrollbar">
                <template x-for="item in cart" :key="item.cartKey">
                    <div class="flex justify-between text-xs">
                        <span class="text-white/70" x-text="item.name + (item.variant_name ? ' ('+item.variant_name+')' : '') + ' ×'+item.qty"></span>
                        <span class="text-yellow-500 font-mono" x-text="'Rp '+formatNumber(item.price*item.qty)"></span>
                    </div>
                </template>
                <div class="border-t border-white/10 pt-2 flex justify-between text-xs text-white/40">
                    <span>Subtotal</span><span x-text="'Rp '+formatNumber(totalPrice)"></span>
                </div>
                <div class="flex justify-between text-xs text-white/40">
                    <span>Pajak (10%)</span><span x-text="'Rp '+formatNumber(taxAmount)"></span>
                </div>
                <div class="flex justify-between text-sm font-bold">
                    <span class="text-white">TOTAL</span><span class="text-yellow-500" x-text="'Rp '+formatNumber(grandTotal)"></span>
                </div>
            </div>

            <!-- Form -->
            <div class="space-y-3 mb-5">
                <div>
                    <label class="text-xs text-white/50 uppercase tracking-wider block mb-1">Nama Pembeli (Opsional)</label>
                    <input x-model="customerName" type="text" placeholder="Contoh: Budi Santoso" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 focus:outline-none focus:border-yellow-500 transition">
                </div>
                <div>
                    <label class="text-xs text-white/50 uppercase tracking-wider block mb-1">Uang Diterima</label>
                    <input x-model.number="paidAmount" type="number" min="0" placeholder="0" @input="calcChange()" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 focus:outline-none focus:border-yellow-500 transition font-mono">
                </div>
                <!-- Quick Amount Buttons -->
                <div class="grid grid-cols-4 gap-2">
                    <template x-for="amt in quickAmounts" :key="amt">
                        <button @click="paidAmount=amt; calcChange()" class="bg-white/5 hover:bg-yellow-500/20 border border-white/10 hover:border-yellow-500/50 text-white/70 hover:text-yellow-400 text-xs py-2 rounded-xl transition font-mono" x-text="'Rp '+formatNumber(amt)"></button>
                    </template>
                </div>
                <div class="bg-white/5 rounded-xl px-4 py-3 flex justify-between items-center">
                    <span class="text-white/50 text-sm">Kembalian</span>
                    <span class="font-bold font-mono text-lg" :class="changeAmount >= 0 ? 'text-green-400' : 'text-red-400'" x-text="'Rp '+formatNumber(Math.max(0,changeAmount))"></span>
                </div>
            </div>

            <button @click="proceedCheckout()" :disabled="paidAmount < grandTotal || isProcessing"
                    :class="paidAmount < grandTotal || isProcessing ? 'opacity-40 cursor-not-allowed bg-white/10 text-white/40' : 'bg-yellow-500 text-black hover:bg-yellow-400'"
                    class="w-full py-4 rounded-2xl font-black text-sm transition-all flex items-center justify-center gap-2">
                <template x-if="isProcessing">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                </template>
                <span x-text="isProcessing ? 'Memproses...' : 'PROSES TRANSAKSI'"></span>
            </button>
            <button @click="showCashModal=false" class="w-full text-white/30 hover:text-white text-xs font-bold py-2 mt-2">KEMBALI</button>
        </div>
    </div>

    <!-- Step 3: Struk Cetak -->
    <div x-show="showReceiptModal" style="display:none" class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/95 backdrop-blur-md"></div>
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="relative bg-[#121212] border border-yellow-500/20 rounded-[28px] w-full max-w-md shadow-2xl overflow-hidden">
            <div class="p-6 text-center border-b border-white/10">
                <h3 class="text-white font-bold text-lg">Transaksi Berhasil! 🎉</h3>
                <p class="text-green-400 text-sm mt-1" x-text="'Invoice: ' + (lastSale?.invoice_no || '')"></p>
            </div>
            <!-- Receipt Preview -->
            <div class="p-6 max-h-96 overflow-y-auto custom-scrollbar">
                <div id="receipt-content" class="bg-white text-black p-4 rounded-xl font-mono text-xs" style="width:100%;max-width:320px;margin:auto">
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Logo" style="height:40px;margin:auto;display:block">
                        <div class="font-bold text-sm mt-1">AYO RENNE STORE</div>
                        <div class="text-gray-500 text-[10px] leading-tight">Jl. Contoh No. 123, Kota Anda<br>Telp: 0812-3456-7890</div>
                        <div class="border-t border-dashed border-gray-400 my-2"></div>
                    </div>
                    <div class="space-y-0.5 mb-2">
                        <div class="flex justify-between"><span>No. Invoice</span><span x-text="lastSale?.invoice_no"></span></div>
                        <div class="flex justify-between"><span>Tanggal</span><span x-text="lastSale?.date"></span></div>
                        <div class="flex justify-between"><span>Kasir</span><span x-text="lastSale?.cashier"></span></div>
                        <div class="flex justify-between"><span>Pembeli</span><span x-text="lastSale?.customer_name || 'Umum'"></span></div>
                    </div>
                    <div class="border-t border-dashed border-gray-400 my-2"></div>
                    <div class="space-y-0.5 mb-2">
                        <template x-for="item in lastSale?.items" :key="item.id">
                            <div>
                                <div x-text="item.product_name + (item.variant_name ? ' ('+item.variant_name+')' : '')"></div>
                                <div class="flex justify-between pl-2">
                                    <span x-text="item.qty + ' × Rp '+formatNumber(item.price)"></span>
                                    <span x-text="'Rp '+formatNumber(item.subtotal)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="border-t border-dashed border-gray-400 my-2"></div>
                    <div class="space-y-0.5">
                        <div class="flex justify-between"><span>Subtotal</span><span x-text="'Rp '+formatNumber(lastSale?.subtotal)"></span></div>
                        <div class="flex justify-between"><span>Pajak (10%)</span><span x-text="'Rp '+formatNumber(lastSale?.tax_amount)"></span></div>
                        <div class="flex justify-between font-bold"><span>TOTAL</span><span x-text="'Rp '+formatNumber(lastSale?.total_amount)"></span></div>
                        <div class="flex justify-between"><span>Tunai</span><span x-text="'Rp '+formatNumber(lastSale?.paid_amount)"></span></div>
                        <div class="flex justify-between font-bold"><span>Kembalian</span><span x-text="'Rp '+formatNumber(lastSale?.change_amount)"></span></div>
                    </div>
                    <div class="border-t border-dashed border-gray-400 my-2"></div>
                    <div class="text-center text-[10px] text-gray-500">Terima kasih atas kunjungan Anda!<br>Barang yang sudah dibeli tidak dapat dikembalikan.</div>
                </div>
            </div>
            <div class="p-5 flex gap-3">
                <button @click="printReceipt()" class="flex-1 bg-yellow-500 text-black py-3 rounded-2xl font-black text-sm hover:bg-yellow-400 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    CETAK STRUK
                </button>
                <button @click="doneTransaction()" class="flex-1 bg-white/10 text-white py-3 rounded-2xl font-bold text-sm hover:bg-white/20 transition">SELESAI</button>
            </div>
        </div>
    </div>

    <!-- Variant Selection Modal -->
    <div x-show="showVariantModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div x-show="showVariantModal" x-transition.opacity class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeVariantModal()"></div>
        <div x-show="showVariantModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="relative bg-[#121212] border border-yellow-500/20 rounded-[24px] w-full max-w-md overflow-hidden shadow-2xl p-6">
            
            <button @click="closeVariantModal()" class="absolute top-4 right-4 text-white/40 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <h3 class="text-white font-bold text-lg mb-1 pr-8" x-text="selectedProductForVariant?.name"></h3>
            <p class="text-white/40 text-xs mb-5">Pilih varian sebelum menambahkan ke keranjang</p>

            <div class="space-y-2 max-h-72 overflow-y-auto custom-scrollbar pr-1 mb-5">
                <template x-for="v in selectedProductForVariant?.variants" :key="v.id">
                    <button @click="v.stock > 0 && selectVariant(v)"
                            :class="v.stock <= 0 ? 'opacity-40 cursor-not-allowed' : (selectedVariant?.id === v.id ? 'border-yellow-500 bg-yellow-500/10' : 'border-white/10 bg-white/5 hover:border-yellow-500/50')"
                            class="w-full flex items-center justify-between p-4 rounded-2xl border transition-all text-left">
                        <div>
                            <div class="text-white font-medium text-sm" x-text="v.name"></div>
                            <div class="text-xs mt-0.5"
                                 :class="v.stock <= 0 ? 'text-red-400 font-bold' : 'text-white/40'"
                                 x-text="v.stock <= 0 ? 'Stok Habis' : 'Sisa ' + v.stock + ' pcs'"></div>
                        </div>
                        <div class="text-yellow-500 font-bold text-sm font-mono" x-text="'Rp ' + formatNumber(v.price)"></div>
                    </button>
                </template>
            </div>

            <button @click="addVariantToCart()"
                    :disabled="!selectedVariant"
                    :class="!selectedVariant ? 'opacity-40 cursor-not-allowed bg-white/10 text-white/40' : 'bg-yellow-500 text-black hover:bg-yellow-400'"
                    class="w-full py-3.5 rounded-2xl font-black text-sm transition-all flex items-center justify-center gap-2">
                Tambah ke Keranjang
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            </button>
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
function posSystem() {
    return {
        products: @json($products),
        search: '',
        selectedCategory: 'all',
        cart: [],
        // Modal states
        showPaymentModal: false,
        showCashModal: false,
        showReceiptModal: false,
        showVariantModal: false,
        // Variant selection
        selectedProductForVariant: null,
        selectedVariant: null,
        // Checkout data
        customerName: '',
        paidAmount: 0,
        changeAmount: 0,
        isProcessing: false,
        lastSale: null,
        // Quick amount presets
        quickAmounts: [10000, 20000, 50000, 100000],

        init() {},

        getProductStock(product) {
            if (product.has_variants && product.variants && product.variants.length > 0) {
                return product.variants.reduce((sum, v) => sum + (v.stock || 0), 0);
            }
            return product.stock || 0;
        },

        getPriceRange(product) {
            if (!product.variants || product.variants.length === 0) {
                return 'Rp ' + this.formatNumber(product.price);
            }
            const prices = product.variants.map(v => v.price);
            const min = Math.min(...prices);
            const max = Math.max(...prices);
            if (min === max) return 'Rp ' + this.formatNumber(min);
            return 'Rp ' + this.formatNumber(min) + ' – Rp ' + this.formatNumber(max);
        },

        get filteredProducts() {
            return this.products.filter(p => {
                const matchesSearch = p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                     (p.sku && p.sku.toLowerCase().includes(this.search.toLowerCase()));
                const categoryId = p.category_id || p.toko_category_id;
                const matchesCategory = this.selectedCategory === 'all' || categoryId == this.selectedCategory;
                return matchesSearch && matchesCategory;
            });
        },

        get totalPrice() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        get taxAmount() {
            return Math.round(this.totalPrice * 10 / 100);
        },

        get grandTotal() {
            return this.totalPrice + this.taxAmount;
        },

        handleProductClick(product) {
            if (this.getProductStock(product) <= 0) return;
            if (product.has_variants && product.variants && product.variants.length > 0) {
                this.selectedProductForVariant = product;
                this.selectedVariant = null;
                this.showVariantModal = true;
            } else {
                this.addToCart(product);
            }
        },

        selectVariant(variant) { this.selectedVariant = variant; },

        closeVariantModal() {
            this.showVariantModal = false;
            this.selectedProductForVariant = null;
            this.selectedVariant = null;
        },

        addVariantToCart() {
            if (!this.selectedVariant || !this.selectedProductForVariant) return;
            const v = this.selectedVariant;
            const product = this.selectedProductForVariant;
            const cartKey = product.id + '_v' + v.id;
            const index = this.cart.findIndex(i => i.cartKey === cartKey);
            if (index > -1) {
                if (this.cart[index].qty < v.stock) this.cart[index].qty++;
            } else {
                this.cart.push({ cartKey, id: product.id, variant_id: v.id, name: product.name,
                    variant_name: v.name, price: v.price, image_url: product.image_url, qty: 1, maxStock: v.stock });
            }
            this.closeVariantModal();
        },

        addToCart(product) {
            if (this.getProductStock(product) <= 0) return;
            const cartKey = product.id + '_base';
            const index = this.cart.findIndex(i => i.cartKey === cartKey);
            if (index > -1) {
                if (this.cart[index].qty < product.stock) this.cart[index].qty++;
            } else {
                this.cart.push({ cartKey, id: product.id, variant_id: null, name: product.name,
                    variant_name: null, price: product.price, image_url: product.image_url, qty: 1, maxStock: product.stock });
            }
        },

        updateQty(index, amount) {
            const item = this.cart[index];
            if (amount > 0 && item.qty >= item.maxStock) return;
            this.cart[index].qty += amount;
            if (this.cart[index].qty <= 0) this.cart.splice(index, 1);
        },

        checkout() {
            if (this.cart.length === 0) return;
            this.showPaymentModal = true;
        },

        selectPaymentMethod(method) {
            if (method === 'cash') {
                this.showPaymentModal = false;
                this.customerName = '';
                this.paidAmount = 0;
                this.changeAmount = 0;
                const gt = this.grandTotal;
                const round = (n) => Math.ceil(n / 1000) * 1000;
                this.quickAmounts = [
                    round(gt),
                    round(gt / 1000) * 1000 + 10000,
                    50000,
                    100000,
                ].filter((v, i, a) => a.indexOf(v) === i).sort((a,b) => a-b).slice(0,4);
                this.showCashModal = true;
            }
        },

        calcChange() {
            this.changeAmount = this.paidAmount - this.grandTotal;
        },

        async proceedCheckout() {
            if (this.paidAmount < this.grandTotal || this.isProcessing) return;
            this.isProcessing = true;

            const payload = {
                customer_name: this.customerName,
                paid_amount: this.paidAmount,
                items: this.cart.map(item => ({
                    product_id: item.id,
                    variant_id: item.variant_id || null,
                    qty: item.qty,
                    price: item.price,
                })),
                _token: document.querySelector('meta[name="csrf-token"]').content,
            };

            try {
                const res = await fetch('{{ route("toko.kasir.sales.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (data.success) {
                    const sale = data.sale;
                    this.lastSale = {
                        ...sale,
                        cashier: sale.cashier?.name || 'Kasir',
                        date: new Date().toLocaleString('id-ID'),
                    };
                    this.showCashModal = false;
                    this.showReceiptModal = true;
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (e) {
                alert('Koneksi gagal, coba lagi.');
            } finally {
                this.isProcessing = false;
            }
        },

        printReceipt() {
            const content = document.getElementById('receipt-content').innerHTML;
            const win = window.open('', '_blank', 'width=450,height=800');
            win.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Struk - ${this.lastSale?.invoice_no}</title>
                    <style>
                        @page { margin: 0; size: 80mm auto; }
                        body { 
                            font-family: 'Courier New', Courier, monospace; 
                            font-size: 12px; 
                            line-height: 1.4;
                            color: #000;
                            margin: 0;
                            padding: 20px;
                            width: 72mm; /* Typical printable width for 80mm paper */
                            background: #fff;
                        }
                        .text-center { text-align: center !important; }
                        .font-bold { font-weight: bold !important; }
                        .flex { 
                            display: flex !important; 
                            justify-content: space-between !important; 
                            align-items: flex-start !important;
                            gap: 10px;
                        }
                        .border-t { 
                            border-top: 1px dashed #000 !important; 
                            margin: 8px 0 !important;
                            height: 0;
                            width: 100%;
                        }
                        .my-2 { margin-top: 8px !important; margin-bottom: 8px !important; }
                        .mb-3 { margin-bottom: 12px !important; }
                        .space-y-0\\.5 > * + * { margin-top: 2px !important; }
                        img { display: block; margin: 0 auto 10px; max-height: 50px; filter: grayscale(100%); }
                        .text-sm { font-size: 14px !important; }
                        .text-xs { font-size: 12px !important; }
                        .text-\[10px\] { font-size: 10px !important; }
                        .justify-between { justify-content: space-between !important; }
                        .pl-2 { padding-left: 10px !important; }
                        .pr-2 { padding-right: 10px !important; }
                        .font-mono { font-family: 'Courier New', Courier, monospace !important; }
                        
                        .border-t.border-dashed { 
                            border-top: 1px dashed #000 !important; 
                            border-bottom: none !important;
                            border-left: none !important;
                            border-right: none !important;
                        }

                        #receipt-content { 
                            background: transparent !important; 
                            padding: 0 !important; 
                            margin: 0 !important; 
                            width: 100% !important; 
                            max-width: none !important; 
                            border-radius: 0 !important; 
                            color: #000 !important;
                        }

                        .text-gray-500 { color: #444 !important; }
                        
                        @media print {
                            body { width: 72mm; padding: 5px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div style="width: 100%;">
                        ${content}
                    </div>
                    <script>
                        window.onload = () => {
                            setTimeout(() => {
                                window.print();
                                window.close();
                            }, 1000);
                        };
                    <\/script>
                </body>
                </html>
            `);
            win.document.close();
        },

        doneTransaction() {
            this.cart = [];
            this.lastSale = null;
            this.showReceiptModal = false;
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        }
    }
}
</script>
@endsection