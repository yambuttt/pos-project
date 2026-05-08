<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Ayo Renne Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Base Dark Luxury Theme */
        body {
            background:
                radial-gradient(circle at top right, rgba(234, 179, 8, 0.12), transparent 30%),
                radial-gradient(circle at bottom left, rgba(234, 179, 8, 0.08), transparent 40%),
                linear-gradient(180deg, #020202 0%, #0a0a0a 100%);
            background-attachment: fixed;
            overflow-x: hidden;
        }
        .font-display {
            font-family: Georgia, "Times New Roman", serif;
        }
        
        /* Animations */
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
        }

        .animate-fade-up {
            animation: fade-up 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        /* Glassmorphism Elements */
        .glass-panel {
            background: rgba(10, 10, 10, 0.55);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(234, 179, 8, 0.15);
        }
        
        /* The Glow Card */
        .glow-card-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
            padding: 1px;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            background: linear-gradient(145deg, rgba(234, 179, 8, 0.4), rgba(255, 255, 255, 0.05));
        }
        .glow-card-wrapper:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(234, 179, 8, 0.15);
        }
        .glow-card-content {
            background: linear-gradient(145deg, #121212 0%, #080808 100%);
            border-radius: 1.45rem;
            height: 100%;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        /* Image Reveal */
        .reveal-img-wrapper {
            overflow: hidden;
            position: relative;
        }
        .reveal-img {
            transition: transform 1s cubic-bezier(0.16, 1, 0.3, 1), filter 1s;
        }
        .group:hover .reveal-img {
            transform: scale(1.08);
        }

        /* Out of Stock styles */
        .out-of-stock-card {
            filter: grayscale(100%);
            opacity: 0.6;
        }
        .out-of-stock-card .glow-card-wrapper:hover {
            transform: none;
            box-shadow: none;
        }
        
        .btn-gold {
            background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
            box-shadow: 0 4px 15px rgba(202, 138, 4, 0.3);
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            box-shadow: 0 8px 25px rgba(202, 138, 4, 0.5);
            transform: translateY(-2px);
            background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
        }
    </style>
</head>
<body class="text-white antialiased min-h-screen flex flex-col font-sans">
    
    <!-- Navbar -->
    <header class="sticky top-0 z-50 glass-panel border-b border-yellow-500/10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[76px] flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex items-center gap-3 group">
                <div class="relative animate-float" style="animation-duration: 4s;">
                    <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne Logo" class="h-12 w-auto object-contain">
                </div>
                <div class="hidden sm:block">
                    <span class="font-display font-bold text-xl tracking-wide text-white group-hover:text-yellow-400 transition-colors">Ayo Renne</span>
                    <span class="block text-[10px] uppercase tracking-[0.2em] text-yellow-500/80">Store & Retail</span>
                </div>
            </a>
            
            <nav class="hidden md:flex gap-10 font-medium text-sm tracking-wider uppercase">
                <a href="{{ route('public.home') }}" class="text-white/70 hover:text-yellow-400 transition-colors">Beranda</a>
                <a href="{{ route('public.toko.katalog') }}" class="text-yellow-500 relative after:content-[''] after:absolute after:-bottom-2 after:left-0 after:w-full after:h-0.5 after:bg-yellow-500">Katalog Koleksi</a>
            </nav>
            
            <div class="flex items-center gap-5">
                <a href="{{ route('public.home') }}" class="btn-gold text-black px-6 py-2.5 rounded-full font-bold text-sm uppercase tracking-wide">
                    Kembali
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-20 relative">
        <!-- Decorative Glow -->
        <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-yellow-600/20 rounded-full mix-blend-screen filter blur-[150px] pointer-events-none z-0"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="text-center mb-16 animate-fade-up">
                <div class="text-xs font-bold uppercase tracking-[0.3em] text-yellow-500 mb-4 flex items-center justify-center gap-4">
                    <span class="w-12 h-[1px] bg-yellow-500/50"></span>
                    Koleksi Lengkap
                    <span class="w-12 h-[1px] bg-yellow-500/50"></span>
                </div>
                <h1 class="font-display text-5xl md:text-6xl font-bold text-white mb-6">Jelajahi Produk Kami</h1>
                <p class="text-white/60 max-w-2xl mx-auto font-light leading-relaxed">
                    Temukan rangkaian produk premium eksklusif kami. Dari biji kopi pilihan hingga souvenir berkelas, setiap item dibuat dengan standar kualitas tertinggi.
                </p>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($products as $index => $product)
                    @php
                        // Hitung total stok (dari produk base atau dari variannya)
                        $totalStock = 0;
                        if ($product->has_variants && $product->variants->count() > 0) {
                            $totalStock = $product->variants->sum('stock');
                        } else {
                            $totalStock = $product->stock;
                        }
                        
                        $isOutOfStock = $totalStock <= 0;

                        // Perhitungan Harga Varian
                        $priceDisplay = 'Rp ' . number_format($product->price, 0, ',', '.');
                        if ($product->has_variants && $product->variants->count() > 0) {
                            $minPrice = $product->variants->min('price');
                            $maxPrice = $product->variants->max('price');
                            if ($minPrice != $maxPrice) {
                                $priceDisplay = 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
                            } else {
                                $priceDisplay = 'Rp ' . number_format($minPrice, 0, ',', '.');
                            }
                        }
                    @endphp
                    
                    <div class="{{ $isOutOfStock ? 'out-of-stock-card' : '' }} group animate-fade-up" style="animation-delay: {{ ($index % 4) * 100 }}ms;">
                        <div class="glow-card-wrapper h-full flex flex-col">
                            <div class="glow-card-content p-5 flex flex-col h-full">
                                
                                <!-- Image Container -->
                                <div class="reveal-img-wrapper h-56 rounded-xl mb-5 relative bg-white/5 flex items-center justify-center">
                                    @if($product->image_url)
                                        <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" class="reveal-img w-full h-full object-cover">
                                    @else
                                        <!-- Placeholder if no image -->
                                        <div class="reveal-img w-full h-full flex items-center justify-center text-white/20">
                                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    
                                    <!-- Category Badge -->
                                    @if($product->category)
                                        <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-full text-[10px] font-bold text-yellow-400 border border-yellow-500/30 tracking-wider uppercase">
                                            {{ $product->category->name }}
                                        </div>
                                    @endif

                                    <!-- Out of Stock Overlay -->
                                    @if($isOutOfStock)
                                        <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center rounded-xl">
                                            <div class="bg-red-500/90 text-white px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-red-400/50 transform -rotate-12 shadow-xl">
                                                Stok Habis
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-1 flex flex-col">
                                    <h3 class="font-display text-xl text-white mb-2 leading-tight group-hover:text-yellow-400 transition-colors">{{ $product->name }}</h3>
                                    
                                    <p class="text-white/40 font-light text-xs mb-4 line-clamp-2 leading-relaxed">
                                        {{ $product->description ?? 'Produk eksklusif Ayo Renne.' }}
                                    </p>
                                    
                                    <div class="mt-auto pt-4 border-t border-white/10 flex flex-col gap-3">
                                        <div>
                                            <span class="text-[10px] text-white/40 uppercase tracking-widest block mb-1">Harga</span>
                                            <span class="text-yellow-500 font-bold text-base sm:text-lg leading-tight block">{{ $priceDisplay }}</span>
                                        </div>
                                        
                                        @if($isOutOfStock)
                                            <button disabled class="w-full bg-white/10 text-white/30 cursor-not-allowed py-2.5 rounded-full text-xs uppercase tracking-wider font-bold transition-all text-center">
                                                Stok Kosong
                                            </button>
                                        @else
                                            <button onclick="buyProduct({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->image_url ? asset('storage/' . $product->image_url) : '' }}', {{ $product->has_variants ? 'true' : 'false' }}, {{ json_encode($product->variants) }}, {{ (int) $product->stock }})" class="w-full bg-yellow-500/10 hover:bg-yellow-500 text-yellow-400 hover:text-black border border-yellow-500/50 hover:border-yellow-500 py-2.5 rounded-full text-xs uppercase tracking-wider font-bold transition-all flex items-center justify-center gap-2">
                                                <span>Tambah ke Keranjang</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20 animate-fade-up">
                        <div class="inline-flex w-20 h-20 rounded-full glass-panel items-center justify-center text-yellow-500/50 mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <h3 class="font-display text-2xl text-white mb-2">Belum Ada Produk</h3>
                        <p class="text-white/50 font-light text-sm">Koleksi produk belum tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Floating Cart Button -->
    <button onclick="toggleCart()" class="fixed bottom-8 right-8 z-40 bg-yellow-500 text-black p-4 rounded-full shadow-[0_0_20px_rgba(234,179,8,0.4)] hover:scale-110 transition-transform flex items-center justify-center">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center border-2 border-black">0</span>
    </button>

    <!-- Cart Sidebar -->
    <div id="cart-sidebar" class="fixed inset-y-0 right-0 z-[60] w-full max-w-md bg-[#0a0a0a] border-l border-yellow-500/20 transform translate-x-full transition-transform duration-300 flex flex-col shadow-2xl">
        <div class="p-6 border-b border-yellow-500/20 flex items-center justify-between">
            <h2 class="font-display text-2xl text-white flex items-center gap-3">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Keranjang
            </h2>
            <button onclick="toggleCart()" class="text-white/50 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-4">
            <!-- Cart items injected here -->
        </div>
        <div class="p-6 border-t border-yellow-500/20 bg-[#111]">
            <div class="flex justify-between items-center mb-6">
                <span class="text-white/70">Total Belanja</span>
                <span id="cart-total" class="text-yellow-500 font-bold text-xl">Rp 0</span>
            </div>
            <button onclick="checkout()" class="w-full btn-gold text-black py-4 rounded-full font-bold uppercase tracking-widest flex justify-center items-center gap-2">
                Checkout (WhatsApp)
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766 0 1.011.263 1.996.763 2.864l-.816 2.982 3.053-.801a5.727 5.727 0 002.768.708h.001c3.18 0 5.767-2.586 5.768-5.766 0-3.181-2.587-5.767-5.769-5.767zm3.172 8.232c-.174.49-.974.95-1.353.996-.341.042-.782.083-2.316-.554-1.85-0.768-3.033-2.656-3.125-2.779-.092-.123-.746-.992-.746-1.892 0-.901.469-1.344.636-1.517.166-.174.364-.217.487-.217.123 0 .245 0 .351.005.109.005.253-.041.396.305.145.348.495 1.21.538 1.298.044.088.072.19.014.305-.058.115-.087.188-.174.288-.087.101-.183.218-.26.306-.088.1-.18.21-.078.385.102.176.452.748.972 1.213.67.599 1.229.784 1.405.872.176.088.278.073.381-.044.102-.116.442-.514.56-.69.117-.175.234-.145.394-.087.16.058 1.014.478 1.188.566.174.088.291.131.334.204.044.073.044.42-.13.91zM12 2C6.477 2 2 6.477 2 12c0 1.758.455 3.414 1.261 4.881L2 22l5.228-1.228A9.954 9.954 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
            </button>
        </div>
    </div>
    
    <!-- Cart Overlay -->
    <div id="cart-overlay" onclick="toggleCart()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[55] hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Variant Modal -->
    <div id="variant-modal" class="fixed inset-0 z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeVariantModal()"></div>
        <div class="relative bg-[#121212] border border-yellow-500/20 rounded-2xl w-full max-w-lg p-6 m-4 shadow-2xl transform scale-95 transition-transform duration-300" id="variant-modal-content">
            <button onclick="closeVariantModal()" class="absolute top-4 right-4 text-white/50 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h3 id="variant-product-name" class="font-display text-2xl text-white mb-2 pr-8">Pilih Varian</h3>
            <p class="text-white/50 text-sm mb-6">Silakan pilih varian yang tersedia sebelum menambah ke keranjang.</p>
            
            <div id="variant-options" class="space-y-3 mb-8 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                <!-- Variant radios injected here -->
            </div>
            
            <button id="add-variant-btn" onclick="addSelectedVariantToCart()" class="w-full btn-gold text-black py-3 rounded-full font-bold uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed">
                Tambah ke Keranjang
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#050505] pt-16 pb-8 border-t border-yellow-500/10 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Logo" class="h-8 w-auto">
                    <span class="font-display font-bold text-white tracking-widest uppercase text-sm">Ayo Renne Store</span>
                </div>
                <div class="text-xs font-light text-white/40 uppercase tracking-wider">
                    &copy; {{ date('Y') }} Ayo Renne Retail. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.animate-fade-up').forEach(el => {
                el.style.animationPlayState = 'paused';
                observer.observe(el);
            });
        });

        // Cart Logic
        let cart = JSON.parse(localStorage.getItem('ayorenne_cart')) || [];
        
        // Fix for old cart items that might not have maxStock saved
        cart = cart.map(item => {
            if (typeof item.maxStock === 'undefined') {
                item.maxStock = item.qty; // Lock at current quantity for backward compatibility
            }
            return item;
        });

        let currentProductVariants = null;
        let selectedProduct = null;

        function saveCart() {
            localStorage.setItem('ayorenne_cart', JSON.stringify(cart));
            updateCartUI();
        }

        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
        }

        function toggleCart() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('cart-overlay');
            
            if (sidebar.classList.contains('translate-x-full')) {
                sidebar.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                updateCartUI();
            } else {
                sidebar.classList.add('translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        function buyProduct(id, name, price, image, hasVariants, variants, stock) {
            if (hasVariants && variants && variants.length > 0) {
                selectedProduct = { id, name, price, image, hasVariants };
                currentProductVariants = variants;
                openVariantModal();
            } else {
                addToCart({
                    id: 'p_' + id,
                    productId: id,
                    name: name,
                    variantName: null,
                    price: price,
                    image: image,
                    qty: 1,
                    maxStock: stock
                });
                toggleCart(); // Show cart when added
            }
        }

        function openVariantModal() {
            const modal = document.getElementById('variant-modal');
            const content = document.getElementById('variant-modal-content');
            const nameEl = document.getElementById('variant-product-name');
            const optionsEl = document.getElementById('variant-options');
            const btn = document.getElementById('add-variant-btn');

            nameEl.textContent = selectedProduct.name;
            optionsEl.innerHTML = '';
            btn.disabled = true;
            btn.dataset.selectedVariant = '';

            currentProductVariants.forEach(v => {
                const isOutOfStock = v.stock <= 0;
                const priceStr = formatRupiah(v.price);
                
                const label = document.createElement('label');
                label.className = `flex items-center justify-between p-4 rounded-xl border ${isOutOfStock ? 'border-red-500/20 bg-red-500/5 cursor-not-allowed opacity-60' : 'border-yellow-500/20 bg-white/5 cursor-pointer hover:border-yellow-500/50'} transition-colors`;
                
                label.innerHTML = `
                    <div class="flex items-center gap-3">
                        <input type="radio" name="variant" value="${v.id}" ${isOutOfStock ? 'disabled' : ''} class="w-4 h-4 text-yellow-500 bg-black border-yellow-500/50 focus:ring-yellow-500 focus:ring-offset-black" onchange="selectVariant(${v.id})">
                        <div>
                            <div class="text-white font-medium">${v.name}</div>
                            <div class="text-xs ${isOutOfStock ? 'text-red-400 font-bold' : 'text-white/50'}">${isOutOfStock ? 'Habis' : 'Sisa ' + v.stock}</div>
                        </div>
                    </div>
                    <div class="text-yellow-500 font-bold">${priceStr}</div>
                `;
                optionsEl.appendChild(label);
            });

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
            }, 10);
        }

        function closeVariantModal() {
            const modal = document.getElementById('variant-modal');
            const content = document.getElementById('variant-modal-content');
            
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                currentProductVariants = null;
                selectedProduct = null;
            }, 300);
        }

        function selectVariant(variantId) {
            const btn = document.getElementById('add-variant-btn');
            btn.disabled = false;
            btn.dataset.selectedVariant = variantId;
        }

        function addSelectedVariantToCart() {
            const btn = document.getElementById('add-variant-btn');
            const variantId = btn.dataset.selectedVariant;
            if (!variantId) return;

            const variant = currentProductVariants.find(v => v.id == variantId);
            
            addToCart({
                id: 'v_' + variant.id,
                productId: selectedProduct.id,
                variantId: variant.id,
                name: selectedProduct.name,
                variantName: variant.name,
                price: variant.price,
                image: selectedProduct.image,
                qty: 1,
                maxStock: variant.stock
            });

            closeVariantModal();
            toggleCart();
        }

        function addToCart(item) {
            const existingItem = cart.find(i => i.id === item.id);
            if (existingItem) {
                if (existingItem.qty + 1 <= item.maxStock) {
                    existingItem.qty += 1;
                } else {
                    alert('Maksimal stok tercapai untuk item ini!');
                }
            } else {
                if (item.qty <= item.maxStock) {
                    cart.push(item);
                } else {
                    alert('Stok tidak mencukupi!');
                }
            }
            saveCart();
        }

        function updateCartItemQty(id, delta) {
            const item = cart.find(i => i.id === id);
            if (item) {
                const newQty = item.qty + delta;
                if (delta > 0 && newQty > item.maxStock) {
                    alert('Maksimal stok tercapai untuk item ini!');
                    return;
                }
                item.qty = newQty;
                if (item.qty <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
                saveCart();
            }
        }

        function updateCartUI() {
            const container = document.getElementById('cart-items');
            const countBadge = document.getElementById('cart-count');
            const totalEl = document.getElementById('cart-total');
            
            let totalQty = 0;
            let totalPrice = 0;
            container.innerHTML = '';

            if (cart.length === 0) {
                container.innerHTML = '<div class="text-center text-white/40 py-10">Keranjang masih kosong</div>';
            }

            cart.forEach(item => {
                totalQty += item.qty;
                totalPrice += (item.price * item.qty);

                const div = document.createElement('div');
                div.className = 'flex gap-4 bg-white/5 p-4 rounded-xl border border-white/10 relative group transition-colors hover:border-yellow-500/30';
                
                const variantText = item.variantName ? `<div class="text-[10px] uppercase tracking-wider text-yellow-500/80 mb-1">${item.variantName}</div>` : '';
                const imgHtml = item.image 
                    ? `<img src="${item.image}" class="w-16 h-16 object-cover rounded-lg border border-white/10 shrink-0">` 
                    : `<div class="w-16 h-16 bg-white/5 rounded-lg border border-white/10 flex items-center justify-center shrink-0"><svg class="w-6 h-6 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>`;

                const isMaxReached = item.qty >= item.maxStock;
                const plusBtnClass = isMaxReached 
                    ? "w-7 h-7 rounded-full bg-white/5 flex items-center justify-center text-white/20 cursor-not-allowed" 
                    : "w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-yellow-500 hover:text-black transition";

                div.innerHTML = `
                    ${imgHtml}
                    <div class="flex-1 min-w-0">
                        ${variantText}
                        <div class="text-white text-sm font-medium leading-tight mb-1 truncate">${item.name}</div>
                        <div class="text-yellow-500 text-sm font-bold">${formatRupiah(item.price)}</div>
                        
                        <div class="flex items-center gap-3 mt-3">
                            <button onclick="updateCartItemQty('${item.id}', -1)" class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-red-500 hover:text-white transition">-</button>
                            <span class="text-white text-sm w-4 text-center">${item.qty}</span>
                            <button onclick="updateCartItemQty('${item.id}', 1)" class="${plusBtnClass}" ${isMaxReached ? 'disabled' : ''}>+</button>
                        </div>
                    </div>
                `;
                container.appendChild(div);
            });

            countBadge.textContent = totalQty;
            totalEl.textContent = formatRupiah(totalPrice);
        }

        function checkout() {
            if (cart.length === 0) {
                alert('Keranjang belanja kosong');
                return;
            }

            let text = "Halo Ayo Renne, saya ingin memesan:\n\n";
            let total = 0;
            cart.forEach((item, index) => {
                let variantStr = item.variantName ? ` (${item.variantName})` : '';
                text += `${index + 1}. ${item.name}${variantStr}\n   ${item.qty}x @ ${formatRupiah(item.price)} = ${formatRupiah(item.price * item.qty)}\n`;
                total += (item.price * item.qty);
            });
            
            text += `\n*Total Belanja: ${formatRupiah(total)}*`;
            
            const phone = "6281234567890"; // Ganti dengan nomor WA store Ayo Renne
            const url = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank');
        }

        // Initialize UI on load
        updateCartUI();
    </script>
</body>
</html>
