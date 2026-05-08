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
                        <div @click="if(product.stock > 0) addToCart(product)"
                             :class="product.stock <= 0 ? 'opacity-40 grayscale cursor-not-allowed' : 'cursor-pointer hover:border-yellow-500/50 hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)] transform active:scale-95'"
                             class="group relative bg-[#0a0a0a] border border-white/5 rounded-3xl overflow-hidden transition-all duration-500">
                            
                            <div class="aspect-square bg-gradient-to-br from-white/5 to-transparent flex items-center justify-center relative overflow-hidden">
                                <img :src="product.image_url ? '/storage/' + product.image_url : '/images/placeholder.png'" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-60 group-hover:opacity-100">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-transparent to-transparent opacity-80"></div>
                                
                                <div class="absolute top-3 right-3">
                                    <span :class="product.stock <= 0 ? 'bg-gray-700 text-gray-300' : (product.stock <= 5 ? 'bg-red-500' : 'bg-green-500')" 
                                          class="text-[9px] text-white font-black px-2 py-1 rounded-lg shadow-lg" 
                                          x-text="product.stock <= 0 ? 'HABIS' : product.stock + ' Stok'"></span>
                                </div>
                            </div>

                            <div class="p-4 space-y-1">
                                <p class="text-[10px] text-yellow-500 font-bold uppercase tracking-widest" x-text="product.category?.name || '-'"></p>
                                <h3 class="text-white font-medium text-sm truncate" x-text="product.name"></h3>
                                <p class="text-white/40 font-mono text-[11px]" x-text="'Rp ' + formatNumber(product.price)"></p>
                            </div>

                            <template x-if="product.stock > 0">
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
                            <p class="text-yellow-500 font-mono text-[10px]" x-text="'Rp ' + formatNumber(item.price * item.qty)"></p>
                        </div>
                        <div class="flex items-center bg-black/40 rounded-lg p-1 border border-white/5">
                            <button @click="updateQty(index, -1)" class="p-1 hover:text-red-400 transition-colors"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>
                            <span class="w-6 text-center text-[11px] font-bold text-white" x-text="item.qty"></span>
                            <button @click="updateQty(index, 1)" class="p-1 hover:text-green-400 transition-colors"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
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
                        <span>Pajak (0%)</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="flex justify-between items-end pt-2">
                        <span class="text-white font-bold text-sm uppercase tracking-widest">Total Tagihan</span>
                        <span class="text-2xl font-black text-yellow-500 font-mono" x-text="'Rp ' + formatNumber(totalPrice)"></span>
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

    <div x-show="showPaymentModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div x-show="showPaymentModal" x-transition.opacity class="absolute inset-0 bg-black/90 backdrop-blur-md" @click="showPaymentModal = false"></div>
        <div x-show="showPaymentModal" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 scale-90 translate-y-10"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="relative bg-[#121212] border border-white/10 rounded-[32px] w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-8 text-center space-y-6">
                <div class="w-20 h-20 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">Metode Pembayaran</h3>
                    <p class="text-white/40 text-sm mt-1 text-balance">Pilih metode pembayaran yang digunakan oleh pelanggan</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <button class="bg-white/5 border border-white/5 p-6 rounded-3xl hover:border-yellow-500/50 hover:bg-yellow-500/5 transition-all group">
                        <div class="text-white group-hover:text-yellow-500 font-bold">TUNAI / CASH</div>
                    </button>
                    <button class="bg-white/5 border border-white/5 p-6 rounded-3xl hover:border-yellow-500/50 hover:bg-yellow-500/5 transition-all group">
                        <div class="text-white group-hover:text-yellow-500 font-bold">QRIS / TRANSFER</div>
                    </button>
                </div>
                <button @click="showPaymentModal = false" class="text-white/30 hover:text-white text-xs font-bold">BATALKAN TRANSAKSI</button>
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
function posSystem() {
    return {
        products: @json($products),
        search: '',
        selectedCategory: 'all',
        cart: [],
        showPaymentModal: false,

        get filteredProducts() {
            return this.products.filter(p => {
                const matchesSearch = p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                     (p.sku && p.sku.toLowerCase().includes(this.search.toLowerCase()));
                
                // Cek category_id atau toko_category_id untuk fallback yang aman
                const categoryId = p.category_id || p.toko_category_id;
                const matchesCategory = this.selectedCategory === 'all' || categoryId == this.selectedCategory;
                
                return matchesSearch && matchesCategory;
            });
        },

        get totalPrice() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        addToCart(product) {
            // Proteksi 1: Tolak jika stok sudah 0 dari awal
            if (product.stock <= 0) return;

            const index = this.cart.findIndex(i => i.id === product.id);
            if (index > -1) {
                // Proteksi 2: Tolak klik berkali-kali jika qty di keranjang sudah sama dengan batas stok
                if (this.cart[index].qty < product.stock) {
                    this.cart[index].qty++;
                }
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image_url: product.image_url,
                    qty: 1
                });
            }
        },

        updateQty(index, amount) {
            const item = this.cart[index];
            const product = this.products.find(p => p.id === item.id);

            // Proteksi 3: Cegah penambahan (tombol + di keranjang) jika melampaui stok
            if (amount > 0 && product && item.qty >= product.stock) {
                return; // Batalkan aksi penambahan
            }

            this.cart[index].qty += amount;
            
            // Hapus dari keranjang jika qty di bawah 1
            if (this.cart[index].qty <= 0) {
                this.cart.splice(index, 1);
            }
        },

        checkout() {
            this.showPaymentModal = true;
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID').format(val);
        }
    }
}
</script>
@endsection