@extends('layouts.kasir')
@section('title', 'Transaksi Baru')

@section('body')
<style>
    /* Custom scrollbar for cart */
    .cart-scroll::-webkit-scrollbar { width: 4px; }
    .cart-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
    
    /* skeleton animation */
    @keyframes pulse-fast {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .animate-pulse-fast { animation: pulse-fast 1.2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
</style>

<div class="-mx-4 -mt-4 -mb-4 p-4 lg:p-6 min-h-[calc(100vh-4rem)] xl:h-screen xl:max-h-screen text-white font-sans flex flex-col xl:flex-row gap-6 xl:overflow-hidden">
    <!-- LEFT SIDE -->
    <div class="flex-1 flex flex-col min-w-0 xl:overflow-y-auto xl:pr-2 cart-scroll xl:pb-24">
        <!-- Header -->
        <div class="shrink-0 flex items-center justify-between gap-4">
            <div class="relative w-full max-w-md">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="search" placeholder="Search menu items..." class="w-full bg-white/5 border border-white/10 rounded-full pl-11 pr-4 py-3 text-sm text-white placeholder-white/40 focus:outline-none focus:border-white/30 transition-colors">
            </div>
            <div class="flex items-center gap-3">
                <button class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 transition text-white/70">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>
                <div class="w-10 h-10 rounded-full bg-white/20 overflow-hidden border border-white/10">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="mt-6 shrink-0 flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide" id="categoriesContainer">
            <!-- Rendered by JS -->
        </div>

        <!-- Products Grid -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 pb-10" id="productsGrid">
            <!-- Rendered by JS -->
        </div>
    </div>

    <!-- RIGHT SIDE (Cart) -->
    <div class="w-full xl:w-[400px] shrink-0 relative z-10 xl:h-full">
        <form id="saleForm" method="POST" action="{{ route('kasir.sales.store') }}" class="bg-white/5 backdrop-blur-2xl rounded-[24px] border border-white/10 flex flex-col xl:h-full shadow-2xl">
            @csrf
            <div id="saleFormData" class="hidden"></div>
            
            <div class="p-6 border-b border-white/5">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold">New Order</h2>
                    <a href="{{ route('kasir.sales.index') }}" class="text-xs text-white/40 hover:text-white transition bg-white/5 px-3 py-1.5 rounded-lg border border-white/10">Riwayat</a>
                </div>
                
                <div class="mt-4 flex gap-2 text-sm">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="order_type" value="takeaway" class="peer hidden" {{ old('order_type', 'takeaway') === 'takeaway' ? 'checked' : '' }}>
                        <div class="text-center py-2.5 rounded-xl border border-white/10 text-white/60 peer-checked:bg-blue-500/20 peer-checked:text-blue-400 peer-checked:border-blue-500 transition font-medium">Take Away</div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="order_type" value="dine_in" class="peer hidden" {{ old('order_type') === 'dine_in' ? 'checked' : '' }}>
                        <div class="text-center py-2.5 rounded-xl border border-white/10 text-white/60 peer-checked:bg-blue-500/20 peer-checked:text-blue-400 peer-checked:border-blue-500 transition font-medium">Dine In</div>
                    </label>
                </div>
                <div id="tableWrap" class="mt-3 hidden transition-all">
                    <select name="dining_table_id" id="diningTable" class="w-full rounded-xl border border-white/10 bg-black/20 px-4 py-3 text-sm outline-none focus:border-blue-500 text-white transition">
                        <option value="" class="text-black">— Select Table —</option>
                        @foreach(($tables ?? []) as $t)
                            <option value="{{ $t->id }}" class="text-black" {{ (string)old('dining_table_id') === (string)$t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="xl:flex-1 max-h-[45vh] xl:max-h-none overflow-y-auto cart-scroll p-4 sm:p-6 space-y-4" id="cart">
                <!-- Rendered by JS -->
            </div>

            <!-- Summary & Payment Details -->
            <div class="p-6 border-t border-white/10 bg-black/20 rounded-b-[24px]">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm text-white/50">
                        <span>Subtotal</span>
                        <span id="subtotalText" class="text-white/80">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm text-white/50">
                        <span>Tax (11%)</span>
                        <span id="taxText" class="text-white/80">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-white pt-2 border-t border-white/5">
                        <span>Total</span>
                        <span id="totalText">Rp 0</span>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div>
                        <select name="payment_method" id="paymentMethod" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm outline-none focus:border-blue-500 text-white transition">
                            <option value="cash" class="text-black" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="qris" class="text-black" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="bca_va" class="text-black" {{ old('payment_method') === 'bca_va' ? 'selected' : '' }}>VA BCA</option>
                            <option value="bni_va" class="text-black" {{ old('payment_method') === 'bni_va' ? 'selected' : '' }}>VA BNI</option>
                            <option value="bri_va" class="text-black" {{ old('payment_method') === 'bri_va' ? 'selected' : '' }}>VA BRI</option>
                            <option value="permata_va" class="text-black" {{ old('payment_method') === 'permata_va' ? 'selected' : '' }}>VA Permata</option>
                        </select>
                    </div>
                    <div>
                        <input name="paid_amount" id="paidAmount" type="number" min="0" value="{{ old('paid_amount', 0) }}" placeholder="Pay Amount" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm outline-none focus:border-blue-500 text-white transition placeholder-white/30">
                    </div>
                </div>
                <div class="mt-2 text-right text-xs text-white/40">
                    Change: <span id="changeText" class="text-white font-semibold">Rp 0</span>
                </div>

                <div class="mt-5 flex gap-3">
                    <button type="button" id="clearCart" class="flex-1 py-3.5 rounded-xl border border-white/10 text-white hover:bg-white/5 font-medium transition text-sm">Cancel</button>
                    <button type="submit" id="payBtn" disabled class="flex-[2] py-3.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-[0_4px_14px_rgba(37,99,235,0.4)] text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Checkout
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const productsData = @json($productsJson);
    const grid = document.getElementById('productsGrid');
    const categoriesContainer = document.getElementById('categoriesContainer');
    const search = document.getElementById('search');
    
    // Cart & form elements
    const cartMap = new Map();
    const cartEl = document.getElementById('cart');
    const formData = document.getElementById('saleFormData');
    const subtotalText = document.getElementById('subtotalText');
    const taxText = document.getElementById('taxText');
    const totalText = document.getElementById('totalText');
    const paidAmount = document.getElementById('paidAmount');
    const paymentMethod = document.getElementById('paymentMethod');
    const changeText = document.getElementById('changeText');
    const payBtn = document.getElementById('payBtn');
    const clearCart = document.getElementById('clearCart');
    const saleForm = document.getElementById('saleForm');
    
    const tableWrap = document.getElementById('tableWrap');
    const diningTable = document.getElementById('diningTable');
    
    let currentCategory = 'All Items';
    let searchQuery = '';
    
    const fmtRp = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    const escapeAttr = (str) => String(str ?? '').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
    
    function isMidtransMethod(method) {
        return ['qris', 'bca_va', 'bni_va', 'bri_va', 'permata_va'].includes(method);
    }
    
    // Toggle Dine in / Takeaway
    function syncOrderTypeUI() {
        const typeEl = document.querySelector('input[name="order_type"]:checked');
        const type = typeEl ? typeEl.value : 'takeaway';
        if (!tableWrap) return;
        if (type === 'dine_in') {
            tableWrap.classList.remove('hidden');
            if (diningTable) diningTable.disabled = false;
        } else {
            tableWrap.classList.add('hidden');
            if (diningTable) {
                diningTable.value = '';
                diningTable.disabled = true;
            }
        }
    }
    document.querySelectorAll('input[name="order_type"]').forEach(r => r.addEventListener('change', syncOrderTypeUI));
    syncOrderTypeUI();
    
    function initCategories() {
        let categories = new Set();
        productsData.forEach(p => {
            if (p.category) categories.add(p.category);
        });
        
        const cats = ['All Items', ...Array.from(categories)];
        categoriesContainer.innerHTML = '';
        
        cats.forEach(cat => {
            const btn = document.createElement('button');
            const isActive = currentCategory === cat;
            
            btn.className = `shrink-0 whitespace-nowrap px-5 py-2.5 rounded-xl text-sm font-medium transition ${isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'bg-white/5 border border-white/10 text-white/70 hover:bg-white/10 hover:text-white'}`;
            
            // Add icon depending on category (simple logic)
            let icon = '';
            if(cat === 'All Items') icon = '<svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>';
            
            btn.innerHTML = icon + cat;
            btn.onclick = () => {
                if(currentCategory !== cat) {
                    currentCategory = cat;
                    initCategories(); // re-render to update active state
                    renderProductsWithLoader();
                }
            };
            categoriesContainer.appendChild(btn);
        });
    }

    function renderSkeleton() {
        grid.innerHTML = '';
        for (let i = 0; i < 8; i++) {
            grid.innerHTML += `
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden flex flex-col p-3 animate-pulse-fast">
                    <div class="w-full h-32 sm:h-40 bg-white/5 rounded-xl mb-3"></div>
                    <div class="h-4 bg-white/10 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-white/5 rounded w-full mb-1"></div>
                    <div class="h-3 bg-white/5 rounded w-2/3 mb-4"></div>
                    <div class="mt-auto flex justify-between items-center">
                        <div class="h-5 bg-white/10 rounded w-1/3"></div>
                        <div class="h-8 w-8 rounded-full bg-white/10"></div>
                    </div>
                </div>
            `;
        }
    }
    
    function renderProductsWithLoader() {
        renderSkeleton();
        
        // Simulate network delay / animation for feeling like an app
        setTimeout(() => {
            renderProducts();
        }, 500);
    }
    
    function renderProducts() {
        grid.innerHTML = '';
        
        const filtered = productsData.filter(p => {
            const matchCat = currentCategory === 'All Items' || p.category === currentCategory;
            const matchSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase());
            return matchCat && matchSearch;
        });
        
        if (filtered.length === 0) {
            grid.innerHTML = '<div class="col-span-full text-center py-10 text-white/40">No products found</div>';
            return;
        }
        
        filtered.forEach(p => {
            const isSoldOut = Number(p.max_portions) <= 0;
            const cartItem = cartMap.get(p.id);
            const qtyInCart = cartItem ? cartItem.qty : 0;
            const isSelected = qtyInCart > 0;
            
            const card = document.createElement('div');
            card.className = `bg-white/5 backdrop-blur-xl rounded-2xl overflow-hidden flex flex-col transition-all duration-300 relative group
                ${isSoldOut ? 'opacity-50 grayscale' : 'hover:scale-[1.02] cursor-pointer hover:shadow-xl'}
                ${isSelected ? 'ring-1 ring-blue-500 bg-white/10' : 'border border-white/10'}
            `;
            
            // Image area
            let imgHtml = '';
            if (p.image_url) {
                imgHtml = `<img src="${p.image_url}" class="w-full h-32 sm:h-40 object-cover rounded-xl" alt="${escapeAttr(p.name)}">`;
            } else {
                imgHtml = `<div class="w-full h-32 sm:h-40 bg-black/40 rounded-xl flex items-center justify-center text-white/20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>`;
            }
            
            let badgeHtml = '';
            if (qtyInCart > 0) {
                badgeHtml = `<div class="absolute top-2 right-2 bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shadow-lg shadow-black/50 z-10">${qtyInCart}</div>`;
            } else if (isSoldOut) {
                 badgeHtml = `<div class="absolute top-2 right-2 bg-red-500/90 text-white px-2 py-0.5 rounded-full text-[10px] font-bold shadow-lg z-10 uppercase tracking-wide">Sold Out</div>`;
            }
            
            card.innerHTML = `
                <div class="p-2 pb-0 relative">
                    ${badgeHtml}
                    ${imgHtml}
                    ${isSoldOut ? '<div class="absolute inset-2 bg-black/40 rounded-xl"></div>' : ''}
                </div>
                <div class="p-3 sm:p-4 flex-1 flex flex-col">
                    <h3 class="text-sm sm:text-[15px] font-semibold text-white leading-tight mb-1">${escapeAttr(p.name)}</h3>
                    <p class="text-[11px] sm:text-xs text-white/50 line-clamp-2 mb-3 flex-1">${escapeAttr(p.description || 'No description')}</p>
                    <div class="flex items-center justify-between mt-auto">
                        <span class="font-bold text-white">${fmtRp(p.price)}</span>
                        <button type="button" class="w-8 h-8 rounded-full ${isSoldOut ? 'bg-white/5 text-white/20' : 'bg-blue-600 text-white hover:bg-blue-500'} flex items-center justify-center transition" ${isSoldOut ? 'disabled' : ''}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </button>
                    </div>
                </div>
            `;
            
            if (!isSoldOut) {
                card.onclick = () => addToCart(p);
            }
            
            grid.appendChild(card);
        });
    }
    
    // --- CART LOGIC ---
    function addToCart(p) {
        if (Number(p.max_portions || 0) <= 0) return;
        const item = cartMap.get(p.id) || { ...p, qty: 0, note: '' };
        item.qty += 1;
        cartMap.set(p.id, item);
        renderCart();
        renderProducts(); // re-render to update the quantity badge on grid
    }
    
    function changeQty(id, delta) {
        const item = cartMap.get(id);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) cartMap.delete(id);
        else cartMap.set(id, item);
        renderCart();
        renderProducts(); // update badges
    }
    
    function renderCart() {
        const TAX_RATE = 0.11;
        cartEl.innerHTML = '';
        formData.innerHTML = '';
        
        let subtotalAll = 0;
        let index = 0;
        
        if (cartMap.size === 0) {
            cartEl.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-white/20 pt-10">
                    <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p class="text-sm font-medium">Cart is empty</p>
                </div>
            `;
        }
        
        cartMap.forEach((item) => {
            const subtotal = item.price * item.qty;
            subtotalAll += subtotal;
            
            const row = document.createElement('div');
            row.className = 'border-b border-white/5 pb-4 last:border-0 last:pb-0';
            
            row.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="text-[15px] font-semibold text-white/90">${escapeAttr(item.name)}</div>
                        <div class="text-xs text-white/50 mt-1">${fmtRp(item.price)}</div>
                        <input type="text" placeholder="Add note (optional)..." value="${escapeAttr(item.note)}" data-note-for="${item.id}" class="mt-2 w-full bg-black/20 border border-white/5 rounded-lg px-2.5 py-1.5 text-xs text-white outline-none focus:border-white/20 placeholder-white/20 transition">
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <div class="text-[15px] font-semibold text-white">${fmtRp(subtotal)}</div>
                        <div class="flex items-center gap-1 bg-black/20 rounded-lg p-0.5 border border-white/5">
                            <button type="button" class="w-7 h-7 flex items-center justify-center rounded-md text-white/70 hover:bg-white/10 hover:text-white transition" onclick="changeQty(${item.id}, -1)">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </button>
                            <span class="w-6 text-center text-sm font-semibold text-white">${item.qty}</span>
                            <button type="button" class="w-7 h-7 flex items-center justify-center rounded-md text-white/70 hover:bg-white/10 hover:text-white transition" onclick="changeQty(${item.id}, 1)">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Input note binding
            setTimeout(() => {
                const noteInput = row.querySelector(`input[data-note-for="${item.id}"]`);
                if(noteInput) {
                    noteInput.addEventListener('input', (e) => {
                        const val = e.target.value ?? '';
                        const it = cartMap.get(item.id);
                        if (it) {
                            it.note = val;
                            const hidden = formData.querySelector(`input[data-note-hidden-for="${item.id}"]`);
                            if (hidden) hidden.value = val;
                        }
                    });
                }
            }, 0);
            
            cartEl.appendChild(row);
            
            // Hidden Form Data for submission
            formData.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
                <input type="hidden" name="items[${index}][note]" value="${escapeAttr(item.note)}" data-note-hidden-for="${item.id}">
            `);
            index++;
        });
        
        const tax = Math.round(subtotalAll * TAX_RATE);
        const grandTotal = subtotalAll + tax;
        
        subtotalText.textContent = fmtRp(subtotalAll);
        taxText.textContent = fmtRp(tax);
        totalText.textContent = fmtRp(grandTotal);
        
        payBtn.disabled = cartMap.size === 0;
        
        const method = (paymentMethod?.value || 'cash');
        if (isMidtransMethod(method)) {
            paidAmount.value = String(grandTotal);
            paidAmount.setAttribute('readonly', 'readonly');
            paidAmount.classList.add('opacity-50');
            changeText.textContent = fmtRp(0);
        } else {
            paidAmount.removeAttribute('readonly');
            paidAmount.classList.remove('opacity-50');
            const paid = Number(paidAmount.value || 0);
            const change = Math.max(0, paid - grandTotal);
            changeText.textContent = fmtRp(change);
        }
    }
    
    // Listeners
    search.addEventListener('input', (e) => {
        searchQuery = e.target.value;
        renderProducts(); // don't show skeleton for search to make it snappy
    });
    
    paidAmount.addEventListener('input', renderCart);
    paymentMethod.addEventListener('change', renderCart);
    
    clearCart.addEventListener('click', () => {
        cartMap.clear();
        renderCart();
        renderProducts();
    });
    
    saleForm.addEventListener('submit', async (e) => {
        const method = paymentMethod?.value || 'cash';
        if (!isMidtransMethod(method)) return;
        
        e.preventDefault();
        const form = new FormData(saleForm);
        payBtn.disabled = true;
        payBtn.innerHTML = '<span class="animate-pulse">Processing...</span>';
        
        try {
            const res = await fetch(saleForm.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: form,
            });
            const data = await res.json();
            if (!res.ok || !data.ok) throw new Error(data.message || 'Payment failed.');
            window.location.href = data.redirect_url;
        } catch (err) {
            alert(err.message || 'Error processing payment.');
            payBtn.disabled = false;
            payBtn.innerHTML = 'Checkout';
        }
    });

    // Initialize
    initCategories();
    renderProductsWithLoader();
    renderCart();

</script>
@endsection
