@extends('layouts.kasir')
@section('title', 'Transaksi Baru')

@section('body')
<div class="h-[calc(100vh-6rem)] -m-4 lg:-m-8 flex flex-col lg:flex-row overflow-hidden" x-data="posSystem()">
    
    {{-- Left Side: Products --}}
    <div class="flex-1 flex flex-col min-w-0 bg-obsidian-950">
        {{-- POS Header --}}
        <div class="p-6 border-b border-white/5 space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div class="relative flex-1 max-w-md group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-white/20 group-focus-within:text-accent-gold transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Cari menu atau kategori..." 
                           class="w-full bg-white/5 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-sm focus:outline-none focus:border-accent-gold/50 focus:ring-4 focus:ring-accent-gold/10 transition-all placeholder-white/20">
                </div>
                <div class="flex items-center gap-2">
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-accent-gold text-black' : 'bg-white/5 text-white/40'" class="p-2.5 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-accent-gold text-black' : 'bg-white/5 text-white/40'" class="p-2.5 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            <div class="flex items-center gap-3 overflow-x-auto pb-2 custom-scrollbar">
                <button @click="currentCategory = 'All'" 
                        :class="currentCategory === 'All' ? 'bg-accent-gold text-black shadow-lg shadow-accent-gold/20' : 'bg-white/5 text-white/50 hover:bg-white/10'"
                        class="shrink-0 px-6 py-2.5 rounded-2xl text-xs font-bold transition-all uppercase tracking-wider">
                    Semua Menu
                </button>
                <template x-for="cat in categories" :key="cat">
                    <button @click="currentCategory = cat" 
                            :class="currentCategory === cat ? 'bg-accent-gold text-black shadow-lg shadow-accent-gold/20' : 'bg-white/5 text-white/50 hover:bg-white/10'"
                            class="shrink-0 px-6 py-2.5 rounded-2xl text-xs font-bold transition-all uppercase tracking-wider"
                            x-text="cat">
                    </button>
                </template>
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
            <div :class="viewMode === 'grid' ? 'grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6' : 'space-y-4'">
                <template x-for="p in filteredProducts" :key="p.id">
                    <div @click="addToCart(p)" 
                         :class="[
                            viewMode === 'grid' ? 'flex flex-col' : 'flex items-center gap-4',
                            p.max_portions <= 0 ? 'opacity-40 grayscale pointer-events-none' : 'cursor-pointer transform active:scale-95'
                         ]"
                         class="premium-card p-3 relative group border-white/5 hover:border-accent-gold/30">
                        
                        {{-- Badge --}}
                        <template x-if="getCartQty(p.id) > 0">
                            <div class="absolute -top-2 -right-2 w-7 h-7 bg-accent-gold text-black rounded-full flex items-center justify-center text-xs font-black shadow-lg shadow-accent-gold/30 z-10 border-2 border-black" x-text="getCartQty(p.id)"></div>
                        </template>

                        {{-- Image --}}
                        <div :class="viewMode === 'grid' ? 'w-full aspect-square mb-4' : 'w-20 h-20'" class="rounded-2xl overflow-hidden bg-white/5 flex items-center justify-center relative">
                            <template x-if="p.image_url">
                                <img :src="p.image_url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            </template>
                            <template x-if="!p.image_url">
                                <svg class="w-8 h-8 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </template>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] font-black uppercase tracking-widest text-accent-gold mb-1" x-text="p.category || 'Lainnya'"></div>
                            <h3 class="font-bold text-sm truncate mb-1 text-white" x-text="p.name"></h3>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white/80" x-text="fmtRp(p.price)"></span>
                                <span class="text-[10px] text-white/20 font-bold" x-text="p.max_portions + ' stok'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Right Side: Cart --}}
    <div class="w-full lg:w-[420px] glass-panel border-y-0 border-r-0 flex flex-col relative z-20 shadow-[-20px_0_50px_rgba(0,0,0,0.8)] bg-black">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h2 class="text-xl font-bold tracking-tight text-white">Pesanan Baru</h2>
            <button @click="clearCart()" class="text-[10px] font-black text-red-400/60 hover:text-red-400 transition-colors uppercase tracking-widest">Reset</button>
        </div>

        {{-- Order Settings --}}
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-2 p-1 bg-white/5 rounded-2xl border border-white/5">
                <button @click="orderType = 'takeaway'" :class="orderType === 'takeaway' ? 'bg-accent-gold text-black shadow-md shadow-accent-gold/20' : 'text-white/40 hover:text-white'" class="py-2.5 rounded-xl text-[10px] font-black transition-all uppercase tracking-widest">Take Away</button>
                <button @click="orderType = 'dine_in'" :class="orderType === 'dine_in' ? 'bg-accent-gold text-black shadow-md shadow-accent-gold/20' : 'text-white/40 hover:text-white'" class="py-2.5 rounded-xl text-[10px] font-black transition-all uppercase tracking-widest">Dine In</button>
            </div>

            {{-- Table Selector --}}
            <div x-show="orderType === 'dine_in'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <button @click="showTableSelector = true" 
                        class="w-full flex items-center justify-between px-4 py-3.5 bg-white/5 border border-white/10 rounded-2xl text-sm transition-all hover:bg-white/10">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-accent-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span x-text="selectedTable ? 'Meja: ' + selectedTable.name : 'Pilih Meja...'" :class="selectedTable ? 'text-white font-bold' : 'text-white/30'"></span>
                    </div>
                    <svg class="w-4 h-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-center opacity-10">
                    <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p class="text-sm font-black uppercase tracking-[0.2em]">Keranjang Kosong</p>
                </div>
            </template>

            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.04] transition-all animate-fade-up">
                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-white/5 shrink-0">
                        <img x-show="item.image_url" :src="item.image_url" class="w-full h-full object-cover">
                        <div x-show="!item.image_url" class="w-full h-full flex items-center justify-center text-white/10"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-sm truncate text-white" x-text="item.name"></h4>
                        <div class="text-accent-gold font-bold text-xs mt-1" x-text="fmtRp(item.price)"></div>
                        <input type="text" x-model="item.note" placeholder="Catatan..." class="mt-2 w-full bg-black/40 border border-white/5 rounded-lg px-2 py-1.5 text-[10px] text-white/60 focus:outline-none focus:border-accent-gold/30 placeholder-white/10">
                    </div>
                    <div class="flex flex-col items-center gap-1 bg-black rounded-xl p-1 border border-white/10">
                        <button @click="updateQty(index, 1)" class="w-7 h-7 flex items-center justify-center text-white/40 hover:text-accent-gold transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg></button>
                        <span class="text-xs font-black text-white" x-text="item.qty"></span>
                        <button @click="updateQty(index, -1)" class="w-7 h-7 flex items-center justify-center text-white/40 hover:text-red-400 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg></button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Summary --}}
        <div class="p-6 bg-black border-t border-white/10 space-y-4">
            <div class="space-y-2">
                <div class="flex justify-between text-xs text-white/30 font-bold">
                    <span>Subtotal</span>
                    <span x-text="fmtRp(totalPrice)"></span>
                </div>
                <div class="flex justify-between text-xs text-white/30 font-bold">
                    <span>Pajak (11%)</span>
                    <span x-text="fmtRp(taxAmount)"></span>
                </div>
                <div class="flex justify-between items-end pt-3 border-t border-white/5">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Total Tagihan</span>
                    <span class="text-2xl font-black text-accent-gold" x-text="fmtRp(grandTotal)"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="relative group">
                    <select x-model="paymentMethod" class="w-full bg-white/5 border border-white/10 rounded-2xl py-3.5 px-4 text-xs font-black uppercase tracking-widest appearance-none focus:outline-none focus:border-accent-gold/50 transition-all cursor-pointer text-white">
                        <option value="cash" class="bg-black">Tunai / Cash</option>
                        <option value="qris" class="bg-black">QRIS</option>
                        <option value="bca_va" class="bg-black">VA BCA</option>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-white/20"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                </div>
                <div class="relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-white/20">RP</div>
                    <input type="number" x-model.number="paidAmount" :readonly="paymentMethod !== 'cash'" :class="paymentMethod !== 'cash' ? 'opacity-20' : ''"
                           class="w-full bg-white/5 border border-white/10 rounded-2xl py-3.5 pl-10 pr-4 text-sm font-bold focus:outline-none focus:border-accent-gold/50 transition-all text-white">
                </div>
            </div>
            
            <div class="flex items-center justify-between px-2">
                <span class="text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">Kembalian</span>
                <span class="text-sm font-bold font-mono" :class="changeAmount >= 0 ? 'text-accent-gold' : 'text-red-400'" x-text="fmtRp(Math.max(0, changeAmount))"></span>
            </div>

            <button @click="processCheckout()" :disabled="cart.length === 0 || isProcessing"
                    class="w-full btn-premium-primary py-5 rounded-[1.5rem] uppercase tracking-[0.2em] font-black text-xs disabled:opacity-20 disabled:grayscale">
                <template x-if="isProcessing">
                    <svg class="animate-spin h-5 w-5 mr-3 text-black" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <span x-text="isProcessing ? 'Memproses...' : 'Selesaikan Transaksi'"></span>
            </button>
        </div>
    </div>

    {{-- Mobile Visual Table Selector Drawer --}}
    <div x-show="showTableSelector" style="display:none" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div x-show="showTableSelector" x-transition.opacity class="absolute inset-0 bg-black/95 backdrop-blur-md" @click="showTableSelector = false"></div>
        <div x-show="showTableSelector" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="translate-y-full sm:scale-95 sm:translate-y-0" 
             x-transition:enter-end="translate-y-0 sm:scale-100"
             class="relative bg-black border-t sm:border border-white/10 w-full max-w-4xl h-[85vh] sm:h-auto sm:max-h-[80vh] rounded-t-[2.5rem] sm:rounded-[3rem] overflow-hidden flex flex-col shadow-2xl">
            
            <div class="p-8 border-b border-white/5 shrink-0">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="text-2xl font-bold tracking-tight text-white">Manajemen Meja</h3>
                        <p class="text-accent-gold text-[10px] mt-1 uppercase tracking-widest font-black">Pilih lokasi dine-in</p>
                    </div>
                    <button @click="showTableSelector = false" class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-white/40 hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    <template x-for="t in tables" :key="t.id">
                        <div @click="selectedTable = t; showTableSelector = false"
                             :class="selectedTable?.id === t.id ? 'bg-accent-gold border-accent-gold shadow-lg shadow-accent-gold/20 scale-105' : 'bg-white/5 border-white/10 hover:border-accent-gold/30'"
                             class="aspect-square rounded-[2rem] border-2 flex flex-col items-center justify-center gap-4 transition-all cursor-pointer group">
                            
                            <div :class="selectedTable?.id === t.id ? 'bg-black text-accent-gold' : 'bg-white/5 text-white/20'" class="w-16 h-16 rounded-2xl flex items-center justify-center transition-all">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div class="text-center">
                                <div :class="selectedTable?.id === t.id ? 'text-black font-black' : 'text-white/70 font-bold'" class="text-sm uppercase tracking-widest" x-text="t.name"></div>
                                <div :class="selectedTable?.id === t.id ? 'text-black/40' : 'text-white/20'" class="text-[10px] mt-1 uppercase font-bold tracking-widest">Available</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="p-8 bg-black border-t border-white/5 flex gap-4">
                <button @click="showTableSelector = false" class="flex-1 btn-premium-glass text-xs py-4">Batal</button>
                <button @click="showTableSelector = false" class="flex-[2] btn-premium-primary text-xs py-4">Pilih Meja</button>
            </div>
        </div>
    </div>

</div>

<script>
function posSystem() {
    return {
        products: @json($productsJson),
        tables: @json($tables),
        categories: [],
        currentCategory: 'All',
        search: '',
        viewMode: 'grid',
        
        cart: [],
        orderType: 'takeaway',
        selectedTable: null,
        paymentMethod: 'cash',
        paidAmount: 0,
        isProcessing: false,
        showTableSelector: false,

        init() {
            const cats = new Set();
            this.products.forEach(p => { if(p.category) cats.add(p.category); });
            this.categories = Array.from(cats);
        },

        get filteredProducts() {
            return this.products.filter(p => {
                const matchCat = this.currentCategory === 'All' || p.category === this.currentCategory;
                const matchSearch = p.name.toLowerCase().includes(this.search.toLowerCase());
                return matchCat && matchSearch;
            });
        },

        get totalPrice() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        get taxAmount() {
            return Math.round(this.totalPrice * 0.11);
        },

        get grandTotal() {
            return this.totalPrice + this.taxAmount;
        },

        get changeAmount() {
            return this.paidAmount - this.grandTotal;
        },

        addToCart(p) {
            if (p.max_portions <= 0) return;
            const index = this.cart.findIndex(i => i.id === p.id);
            if (index > -1) {
                if (this.cart[index].qty < p.max_portions) this.cart[index].qty++;
            } else {
                this.cart.push({ ...p, qty: 1, note: '' });
            }
        },

        updateQty(index, delta) {
            const item = this.cart[index];
            if (delta > 0 && item.qty >= item.max_portions) return;
            item.qty += delta;
            if (item.qty <= 0) this.cart.splice(index, 1);
        },

        getCartQty(id) {
            const item = this.cart.find(i => i.id === id);
            return item ? item.qty : 0;
        },

        clearCart() {
            this.cart = [];
            this.selectedTable = null;
            this.paidAmount = 0;
        },

        fmtRp(v) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(v || 0);
        },

        async processCheckout() {
            if (this.cart.length === 0 || this.isProcessing) return;
            if (this.orderType === 'dine_in' && !this.selectedTable) {
                alert('Pilih meja terlebih dahulu untuk Dine In.');
                this.showTableSelector = true;
                return;
            }
            if (this.paymentMethod === 'cash' && this.paidAmount < this.grandTotal) {
                alert('Uang bayar kurang!');
                return;
            }

            this.isProcessing = true;
            const payload = {
                order_type: this.orderType,
                dining_table_id: this.orderType === 'dine_in' ? this.selectedTable.id : null,
                payment_method: this.paymentMethod,
                paid_amount: this.paidAmount,
                items: this.cart.map(i => ({ product_id: i.id, qty: i.qty, note: i.note })),
                _token: document.querySelector('meta[name="csrf-token"]').content
            };

            try {
                const res = await fetch("{{ route('kasir.sales.store') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.ok) {
                    window.location.href = data.redirect_url;
                } else {
                    alert(data.message || 'Terjadi kesalahan.');
                }
            } catch (e) {
                alert('Gagal memproses transaksi.');
            } finally {
                this.isProcessing = false;
            }
        }
    }
}
</script>
@endsection
