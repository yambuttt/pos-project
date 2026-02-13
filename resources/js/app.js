import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // =========================
    // Curtain open/close (Menu)
    // =========================
    const startBtn = document.getElementById('startOrderBtn');
    const closeBtn = document.getElementById('closeMenuBtn');

    const menu = document.getElementById('menu');
    const curtain = document.getElementById('curtain');
    const menuContent = document.getElementById('menuContent');
    const menuScroll = document.getElementById('menuScroll');

    let isAnimating = false;

    const openMenu = () => {
        if (!menu || !curtain || !menuContent || isAnimating) return;
        if (menu.dataset.state === 'open') return;

        isAnimating = true;

        menu.dataset.state = 'open';
        menu.classList.remove('pointer-events-none');
        menu.classList.add('pointer-events-auto');

        menuContent.classList.add('opacity-0', 'translate-y-4');
        menuContent.classList.remove('opacity-100', 'translate-y-0');

        curtain.classList.remove('translate-y-full');
        curtain.classList.add('translate-y-0');

        setTimeout(() => {
            menuContent.classList.remove('opacity-0', 'translate-y-4');
            menuContent.classList.add('opacity-100', 'translate-y-0');

            if (menuScroll) menuScroll.scrollTop = 0;
            isAnimating = false;
        }, 500);
    };

    const closeMenu = () => {
        if (!menu || !curtain || !menuContent || isAnimating) return;
        if (menu.dataset.state === 'closed') return;

        isAnimating = true;
        menu.dataset.state = 'closed';

        menuContent.classList.remove('opacity-100', 'translate-y-0');
        menuContent.classList.add('opacity-0', 'translate-y-4');

        setTimeout(() => {
            curtain.classList.remove('translate-y-0');
            curtain.classList.add('translate-y-full');

            setTimeout(() => {
                menu.classList.add('pointer-events-none');
                menu.classList.remove('pointer-events-auto');
                isAnimating = false;
            }, 500);
        }, 150);
    };

    startBtn?.addEventListener('click', openMenu);
    closeBtn?.addEventListener('click', closeMenu);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeMenu();
    });

    // =========================
    // Filter + Search
    // =========================
    const filterBar = document.getElementById('filterBar');
    const searchInput = document.getElementById('menuSearch');
    const items = Array.from(document.querySelectorAll('.menu-item'));
    const emptyState = document.getElementById('emptyState');

    let activeFilter = 'popular';

    const setActiveButton = (filter) => {
        const btns = Array.from(document.querySelectorAll('.filter-btn'));
        btns.forEach((btn) => {
            const isActive = btn.dataset.filter === filter;
            if (isActive) {
                btn.classList.add('bg-amber-400', 'text-zinc-950');
                btn.classList.remove('border', 'border-white/10', 'bg-white/5', 'text-white/80');
            } else {
                btn.classList.remove('bg-amber-400', 'text-zinc-950');
                btn.classList.add('border', 'border-white/10', 'bg-white/5', 'text-white/80');
            }
        });
    };

    const applyFilter = () => {
        const q = (searchInput?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        items.forEach((el) => {
            const category = (el.dataset.category || '').toLowerCase();
            const popular = (el.dataset.popular || 'false') === 'true';
            const name = (el.dataset.name || '').toLowerCase();

            let okByFilter = true;
            if (activeFilter === 'popular') okByFilter = popular;
            else okByFilter = category === activeFilter;

            const okBySearch = q === '' ? true : name.includes(q);
            const show = okByFilter && okBySearch;

            el.classList.toggle('hidden', !show);
            if (show) visibleCount++;
        });

        if (emptyState) emptyState.classList.toggle('hidden', visibleCount !== 0);
    };

    setActiveButton(activeFilter);
    applyFilter();

    filterBar?.addEventListener('click', (e) => {
        const btn = e.target.closest('.filter-btn');
        if (!btn) return;

        activeFilter = btn.dataset.filter || 'popular';
        setActiveButton(activeFilter);
        applyFilter();

        if (menuScroll) menuScroll.scrollTop = 0;
    });

    searchInput?.addEventListener('input', applyFilter);

    // =========================
    // Product Modal
    // =========================
    const modal = document.getElementById('productModal');
    const backdrop = document.getElementById('modalBackdrop');
    const panel = document.getElementById('modalPanel');
    const closeModalBtn = document.getElementById('closeModalBtn');

    const modalIcon = document.getElementById('modalIcon');
    const modalName = document.getElementById('modalName');
    const modalPrice = document.getElementById('modalPrice');
    const modalDesc = document.getElementById('modalDesc');
    const modalAddBtn = document.getElementById('modalAddBtn');

    let lastFocused = null;

    const formatRupiah = (num) => {
        const n = Number(num || 0);
        return 'Rp ' + n.toLocaleString('id-ID');
    };

    const openModal = (data) => {
        if (!modal || !backdrop || !panel) return;

        lastFocused = document.activeElement;

        if (modalIcon) modalIcon.textContent = data.icon || '☕';
        if (modalName) modalName.textContent = data.name || 'Product';
        if (modalPrice) modalPrice.textContent = formatRupiah(data.price);
        if (modalDesc) modalDesc.textContent = data.desc || '';

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');

        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');

            panel.classList.remove('opacity-0', 'translate-y-6', 'sm:scale-95');
            panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        });

        document.body.style.overflow = 'hidden';
        closeModalBtn?.focus?.();
    };

    const closeModal = () => {
        if (!modal || !backdrop || !panel) return;

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');

        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        panel.classList.add('opacity-0', 'translate-y-6', 'sm:scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            lastFocused?.focus?.();
        }, 200);
    };

    closeModalBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // =========================
    // Cart (UI only)
    // =========================
    const cartDrawer = document.getElementById('cartDrawer');
    const cartBackdrop = document.getElementById('cartBackdrop');
    const cartPanel = document.getElementById('cartPanel');
    const openCartBtn = document.getElementById('openCartBtn');
    const openCartIconBtn = document.getElementById('openCartIconBtn');
    const closeCartBtn = document.getElementById('closeCartBtn');

    const cartItemsEl = document.getElementById('cartItems');
    const cartEmptyEl = document.getElementById('cartEmpty');

    const cartSubtotalEl = document.getElementById('cartSubtotal');
    const cartTaxEl = document.getElementById('cartTax');
    const cartTotalEl = document.getElementById('cartTotal');

    const cartBadgeEl = document.getElementById('cartBadge');

    // cart state: { id: {name, price, icon, qty} }
    const cart = {
        americano: { name: 'Americano', price: 18000, icon: '☕', qty: 1 },
        latte: { name: 'Caffè Latte', price: 25000, icon: '🥛', qty: 1 },
    };


    const getCartCount = () =>
        Object.values(cart).reduce((sum, it) => sum + it.qty, 0);

    const getSubtotal = () =>
        Object.values(cart).reduce((sum, it) => sum + it.price * it.qty, 0);

    const updateBadge = () => {
        if (!cartBadgeEl) return;
        cartBadgeEl.textContent = String(getCartCount());
    };

    const renderCart = () => {
        if (!cartItemsEl || !cartEmptyEl) return;

        const entries = Object.entries(cart);
        const isEmpty = entries.length === 0;

        cartEmptyEl.classList.toggle('hidden', !isEmpty);
        cartItemsEl.innerHTML = '';

        // totals
        const subtotal = getSubtotal();
        const tax = Math.round(subtotal * 0.1);
        const total = subtotal + tax;

        if (cartSubtotalEl) cartSubtotalEl.textContent = formatRupiah(subtotal);
        if (cartTaxEl) cartTaxEl.textContent = formatRupiah(tax);
        if (cartTotalEl) cartTotalEl.textContent = formatRupiah(total);

        updateBadge();

        if (isEmpty) return;

        entries.forEach(([id, it]) => {
            const row = document.createElement('div');
            row.className =
                'rounded-2xl border border-white/10 bg-white/5 p-4 flex items-center gap-3';

            row.innerHTML = `
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-amber-400/30 to-white/5 flex items-center justify-center text-xl">
          ${it.icon || '☕'}
        </div>
        <div class="min-w-0 flex-1">
          <p class="truncate font-semibold">${it.name}</p>
          <p class="text-xs text-white/60 mt-1">${formatRupiah(it.price)} • x<span class="font-semibold">${it.qty}</span></p>
        </div>
        <div class="flex items-center gap-2">
          <button data-action="dec" data-id="${id}" class="qty-btn rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition" type="button">−</button>
          <button data-action="inc" data-id="${id}" class="qty-btn rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition" type="button">+</button>
          <button data-action="remove" data-id="${id}" class="qty-btn rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm hover:bg-white/10 transition" type="button">🗑️</button>
        </div>
      `;

            cartItemsEl.appendChild(row);
        });
    };

    const openCart = () => {
        if (!cartDrawer || !cartBackdrop || !cartPanel) return;

        cartDrawer.classList.remove('hidden');
        cartDrawer.setAttribute('aria-hidden', 'false');

        requestAnimationFrame(() => {
            cartBackdrop.classList.remove('opacity-0');
            cartBackdrop.classList.add('opacity-100');

            cartPanel.classList.remove('opacity-0', 'translate-y-6', 'sm:translate-x-8');
            cartPanel.classList.add('opacity-100', 'translate-y-0', 'sm:translate-x-0');
        });

        document.body.style.overflow = 'hidden';
        renderCart();
    };

    const closeCart = () => {
        if (!cartDrawer || !cartBackdrop || !cartPanel) return;

        cartBackdrop.classList.remove('opacity-100');
        cartBackdrop.classList.add('opacity-0');

        cartPanel.classList.remove('opacity-100', 'translate-y-0', 'sm:translate-x-0');
        cartPanel.classList.add('opacity-0', 'translate-y-6', 'sm:translate-x-8');

        setTimeout(() => {
            cartDrawer.classList.add('hidden');
            cartDrawer.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }, 200);
    };

    // =========================
    // Checkout Overlay
    // =========================
    const checkoutOverlay = document.getElementById('checkoutOverlay');
    const checkoutBackdrop = document.getElementById('checkoutBackdrop');
    const checkoutPanel = document.getElementById('checkoutPanel');
    const closeCheckoutBtn = document.getElementById('closeCheckoutBtn');

    const coSubtotal = document.getElementById('coSubtotal');
    const coTax = document.getElementById('coTax');
    const coTotal = document.getElementById('coTotal');

    const checkoutBtn = document.getElementById('checkoutBtn');
    const payNowBtn = document.getElementById('payNowBtn');

    const openCheckout = () => {
        if (!checkoutOverlay || !checkoutBackdrop || !checkoutPanel) return;

        // update summary dari cart
        const subtotal = getSubtotal();
        const tax = Math.round(subtotal * 0.1);
        const total = subtotal + tax;

        if (coSubtotal) coSubtotal.textContent = formatRupiah(subtotal);
        if (coTax) coTax.textContent = formatRupiah(tax);
        if (coTotal) coTotal.textContent = formatRupiah(total);

        // tampilkan overlay
        checkoutOverlay.classList.remove('hidden');
        checkoutOverlay.setAttribute('aria-hidden', 'false');

        requestAnimationFrame(() => {
            checkoutBackdrop.classList.remove('opacity-0');
            checkoutBackdrop.classList.add('opacity-100');

            checkoutPanel.classList.remove('opacity-0', 'translate-y-6', 'sm:scale-95');
            checkoutPanel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        });

        document.body.style.overflow = 'hidden';
    };

    const closeCheckout = () => {
        if (!checkoutOverlay || !checkoutBackdrop || !checkoutPanel) return;

        checkoutBackdrop.classList.remove('opacity-100');
        checkoutBackdrop.classList.add('opacity-0');

        checkoutPanel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        checkoutPanel.classList.add('opacity-0', 'translate-y-6', 'sm:scale-95');

        setTimeout(() => {
            checkoutOverlay.classList.add('hidden');
            checkoutOverlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }, 200);
    };

    checkoutBtn?.addEventListener('click', () => {
        // kalau cart kosong, jangan lanjut
        if (getCartCount() === 0) return;

        // tutup cart lalu buka checkout
        closeCart();
        setTimeout(openCheckout, 220);
    });

    closeCheckoutBtn?.addEventListener('click', closeCheckout);
    checkoutBackdrop?.addEventListener('click', closeCheckout);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && checkoutOverlay && !checkoutOverlay.classList.contains('hidden')) {
            closeCheckout();
        }
    });

    payNowBtn?.addEventListener('click', () => {
        const selected = document.querySelector('input[name="payMethod"]:checked');
        const method = selected?.value || 'qris';

        // tutup checkout lalu buka invoice
        closeCheckout();
        setTimeout(() => openInvoice({ method }), 220);
    });



    openCartBtn?.addEventListener('click', openCart);
    openCartIconBtn?.addEventListener('click', openCart);
    closeCartBtn?.addEventListener('click', closeCart);
    cartBackdrop?.addEventListener('click', closeCart);

    // cart item buttons (+/-/remove)
    cartItemsEl?.addEventListener('click', (e) => {
        const btn = e.target.closest('.qty-btn');
        if (!btn) return;

        const id = btn.dataset.id;
        const action = btn.dataset.action;
        if (!id || !action || !cart[id]) return;

        if (action === 'inc') cart[id].qty += 1;
        if (action === 'dec') cart[id].qty -= 1;
        if (action === 'remove') cart[id].qty = 0;

        if (cart[id].qty <= 0) delete cart[id];

        renderCart();
    });

    // =========================
    // Menu interactions: open modal OR add to cart
    // =========================
    const menuGrid = document.getElementById('menuGrid');

    const addToCartFromCard = (card) => {
        const id = (card.dataset.name || '').toLowerCase().replace(/\s+/g, '-');
        const name = card.dataset.name || 'Item';
        const price = Number(card.dataset.price || 0);
        const icon = card.dataset.icon || '☕';
        const desc = card.dataset.desc || '';

        if (!cart[id]) cart[id] = { name, price, icon, desc, qty: 0 };
        cart[id].qty += 1;

        renderCart();
    };

    menuGrid?.addEventListener('click', (e) => {
        const card = e.target.closest('.menu-item');
        if (!card || card.classList.contains('hidden')) return;

        const addBtn = e.target.closest('.add-btn');

        // klik tombol +Tambah -> add to cart (no modal)
        if (addBtn) {
            addToCartFromCard(card);
            return;
        }

        // klik card -> modal
        const data = {
            name: card.dataset.name,
            price: card.dataset.price,
            desc: card.dataset.desc,
            icon: card.dataset.icon,
        };
        openModal(data);
    });

    // modal add -> add to cart
    modalAddBtn?.addEventListener('click', () => {
        const name = modalName?.textContent || 'Item';
        // find matching card by name (simple)
        const card = items.find((el) => (el.dataset.name || '') === name);

        if (card) addToCartFromCard(card);

        closeModal();
    });

    // =========================
    // Invoice Overlay (UNPAID/PAID + per method)
    // =========================
    const invoiceOverlay = document.getElementById('invoiceOverlay');
    const invoiceBackdrop = document.getElementById('invoiceBackdrop');
    const invoicePanel = document.getElementById('invoicePanel');
    const closeInvoiceBtn = document.getElementById('closeInvoiceBtn');

    const invOrderId = document.getElementById('invOrderId');
    const invStatusBadge = document.getElementById('invStatusBadge');

    const invItems = document.getElementById('invItems');
    const invSubtotal = document.getElementById('invSubtotal');
    const invTax = document.getElementById('invTax');
    const invTotal = document.getElementById('invTotal');

    const invMethodTitle = document.getElementById('invMethodTitle');
    const invMethodIcon = document.getElementById('invMethodIcon');

    const invStatusTitle = document.getElementById('invStatusTitle');
    const invStatusDesc = document.getElementById('invStatusDesc');
    const invMethodContent = document.getElementById('invMethodContent');

    const invCopyBtn = document.getElementById('invCopyBtn');
    const invMarkPaidBtn = document.getElementById('invMarkPaidBtn');

    let invoiceState = {
        orderId: 'CS-0001',
        method: 'qris', // qris | va | cashier
        paid: false
    };

    const methodMeta = {
        qris: { title: 'QRIS', icon: '📱' },
        va: { title: 'Virtual Account', icon: '🏦' },
        cashier: { title: 'Bayar Tunai di Kasir', icon: '💵' },
    };

    const randomOrderId = () => {
        const n = Math.floor(1000 + Math.random() * 9000);
        return `CS-${n}`;
    };

    const renderInvoiceItems = () => {
        if (!invItems) return;
        invItems.innerHTML = '';

        const entries = Object.values(cart);
        entries.forEach((it) => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between text-sm';
            row.innerHTML = `
      <div class="flex items-center gap-2 min-w-0">
        <span class="text-lg">${it.icon || '☕'}</span>
        <span class="truncate">${it.name} <span class="text-white/60 text-xs">x${it.qty}</span></span>
      </div>
      <span class="text-white/80">${formatRupiah(it.price * it.qty)}</span>
    `;
            invItems.appendChild(row);
        });

        const subtotal = getSubtotal();
        const tax = Math.round(subtotal * 0.1);
        const total = subtotal + tax;

        if (invSubtotal) invSubtotal.textContent = formatRupiah(subtotal);
        if (invTax) invTax.textContent = formatRupiah(tax);
        if (invTotal) invTotal.textContent = formatRupiah(total);
    };

    const renderInvoiceMethod = () => {
        if (!invMethodTitle || !invMethodIcon || !invMethodContent) return;

        const meta = methodMeta[invoiceState.method] || methodMeta.qris;
        invMethodTitle.textContent = meta.title;
        invMethodIcon.textContent = meta.icon;

        // status
        if (invoiceState.paid) {
            invStatusBadge.className = 'rounded-full px-3 py-1 text-xs font-semibold border border-emerald-400/30 bg-emerald-400/15 text-emerald-200';
            invStatusBadge.textContent = 'PAID';
            invStatusTitle.textContent = 'Pembayaran Berhasil';
            invStatusDesc.textContent = 'Pesanan sudah dibayar. Silakan tunjukkan invoice ini ke kasir/barista.';
        } else {
            invStatusBadge.className = 'rounded-full px-3 py-1 text-xs font-semibold border border-amber-400/30 bg-amber-400/15 text-amber-200';
            invStatusBadge.textContent = 'UNPAID';
            invStatusTitle.textContent = 'Menunggu Pembayaran';
            invStatusDesc.textContent = 'Silakan selesaikan pembayaran untuk melanjutkan pesanan.';
        }

        // method content
        const subtotal = getSubtotal();
        const tax = Math.round(subtotal * 0.1);
        const total = subtotal + tax;

        const amountLine = `<div class="flex items-center justify-between text-sm">
      <span class="text-white/70">Nominal</span>
      <span class="font-semibold text-amber-300">${formatRupiah(total)}</span>
    </div>`;

        if (invoiceState.method === 'qris') {
            invMethodContent.innerHTML = invoiceState.paid
                ? `
        ${amountLine}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
          <p class="text-sm font-semibold">QRIS</p>
          <p class="mt-1 text-xs text-white/70">Status QR: <span class="font-semibold text-emerald-200">Sudah dibayar</span></p>
          <div class="mt-3 grid place-items-center rounded-2xl border border-white/10 bg-zinc-950/40 p-6">
            <div class="h-40 w-40 rounded-2xl border border-white/10 bg-white/5 grid place-items-center text-white/70 text-xs">
              QR (Paid)
            </div>
          </div>
          <p class="mt-3 text-xs text-white/60">Tunjukkan bukti ini ke kasir.</p>
        </div>
      `
                : `
        ${amountLine}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
          <p class="text-sm font-semibold">QRIS</p>
          <p class="mt-1 text-xs text-white/70">Scan QR di bawah untuk bayar.</p>
          <div class="mt-3 grid place-items-center rounded-2xl border border-white/10 bg-zinc-950/40 p-6">
            <div class="h-40 w-40 rounded-2xl border border-white/10 bg-white/5 grid place-items-center text-white/70 text-xs">
              QR (Unpaid)
            </div>
          </div>
          <p class="mt-3 text-xs text-white/60">Setelah berhasil, status akan menjadi <b>PAID</b>.</p>
        </div>
      `;
        }

        if (invoiceState.method === 'va') {
            const vaNumber = '8808 1234 5678 9012'; // dummy
            invMethodContent.innerHTML = invoiceState.paid
                ? `
        ${amountLine}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
          <p class="text-sm font-semibold">Virtual Account</p>
          <p class="mt-1 text-xs text-white/70">Status: <span class="font-semibold text-emerald-200">Sudah dibayar</span></p>
          <div class="mt-3 rounded-2xl border border-white/10 bg-zinc-950/40 p-4">
            <p class="text-xs text-white/60">Nomor VA</p>
            <p class="mt-1 text-lg font-semibold tracking-wider">${vaNumber}</p>
            <p class="mt-2 text-xs text-white/60">Bank: BCA / BRI / Mandiri (prototype)</p>
          </div>
          <p class="mt-3 text-xs text-white/60">Pembayaran diterima. Silakan tunjukkan invoice ini.</p>
        </div>
      `
                : `
        ${amountLine}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
          <p class="text-sm font-semibold">Virtual Account</p>
          <p class="mt-1 text-xs text-white/70">Lakukan transfer ke nomor VA berikut.</p>
          <div class="mt-3 rounded-2xl border border-white/10 bg-zinc-950/40 p-4">
            <p class="text-xs text-white/60">Nomor VA</p>
            <p class="mt-1 text-lg font-semibold tracking-wider">${vaNumber}</p>
            <p class="mt-2 text-xs text-white/60">Bank: BCA / BRI / Mandiri (prototype)</p>
          </div>
          <p class="mt-3 text-xs text-white/60">Setelah transfer sukses, status akan menjadi <b>PAID</b>.</p>
        </div>
      `;
        }

        if (invoiceState.method === 'cashier') {
            invMethodContent.innerHTML = invoiceState.paid
                ? `
        ${amountLine}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
          <p class="text-sm font-semibold">Bayar Tunai di Kasir</p>
          <p class="mt-1 text-xs text-white/70">Status: <span class="font-semibold text-emerald-200">Sudah dibayar di kasir</span></p>
          <div class="mt-3 rounded-2xl border border-white/10 bg-zinc-950/40 p-4">
            <p class="text-xs text-white/60">Catatan</p>
            <p class="mt-1 text-sm text-white/80">Kasir sudah menerima pembayaran. Pesanan bisa diproses.</p>
          </div>
        </div>
      `
                : `
        ${amountLine}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
          <p class="text-sm font-semibold">Bayar Tunai di Kasir</p>
          <p class="mt-1 text-xs text-white/70">Tunjukkan invoice ini ke kasir untuk melakukan pembayaran.</p>
          <div class="mt-3 rounded-2xl border border-white/10 bg-zinc-950/40 p-4">
            <p class="text-xs text-white/60">Instruksi</p>
            <ol class="mt-2 text-sm text-white/80 list-decimal pl-5 space-y-1">
              <li>Datang ke kasir</li>
              <li>Sebutkan Order ID: <b>${invoiceState.orderId}</b></li>
              <li>Bayar sesuai total</li>
            </ol>
          </div>
          <p class="mt-3 text-xs text-white/60">Setelah kasir konfirmasi, status menjadi <b>PAID</b>.</p>
        </div>
      `;
        }
    };

    const openInvoice = ({ method }) => {
        if (!invoiceOverlay || !invoiceBackdrop || !invoicePanel) return;

        invoiceState.orderId = randomOrderId();
        invoiceState.method = method || 'qris';
        invoiceState.paid = false; // default: belum bayar

        if (invOrderId) invOrderId.textContent = invoiceState.orderId;

        renderInvoiceItems();
        renderInvoiceMethod();

        invoiceOverlay.classList.remove('hidden');
        invoiceOverlay.setAttribute('aria-hidden', 'false');

        requestAnimationFrame(() => {
            invoiceBackdrop.classList.remove('opacity-0');
            invoiceBackdrop.classList.add('opacity-100');

            invoicePanel.classList.remove('opacity-0', 'translate-y-6', 'sm:scale-95');
            invoicePanel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        });

        document.body.style.overflow = 'hidden';
    };

    const closeInvoice = () => {
        if (!invoiceOverlay || !invoiceBackdrop || !invoicePanel) return;

        invoiceBackdrop.classList.remove('opacity-100');
        invoiceBackdrop.classList.add('opacity-0');

        invoicePanel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        invoicePanel.classList.add('opacity-0', 'translate-y-6', 'sm:scale-95');

        setTimeout(() => {
            invoiceOverlay.classList.add('hidden');
            invoiceOverlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }, 200);
    };

    closeInvoiceBtn?.addEventListener('click', closeInvoice);
    invoiceBackdrop?.addEventListener('click', closeInvoice);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && invoiceOverlay && !invoiceOverlay.classList.contains('hidden')) {
            closeInvoice();
        }
    });

    // copy info
    invCopyBtn?.addEventListener('click', async () => {
        const subtotal = getSubtotal();
        const tax = Math.round(subtotal * 0.1);
        const total = subtotal + tax;

        const text =
            `Coffe Shop Invoice
Order ID: ${invoiceState.orderId}
Metode: ${methodMeta[invoiceState.method].title}
Status: ${invoiceState.paid ? 'PAID' : 'UNPAID'}
Total: ${formatRupiah(total)}`;

        try {
            await navigator.clipboard.writeText(text);
            invCopyBtn.textContent = 'Tersalin ✅';
            setTimeout(() => (invCopyBtn.textContent = 'Salin Info'), 1200);
        } catch {
            invCopyBtn.textContent = 'Gagal menyalin';
            setTimeout(() => (invCopyBtn.textContent = 'Salin Info'), 1200);
        }
    });

    // demo paid toggle
    invMarkPaidBtn?.addEventListener('click', () => {
        invoiceState.paid = true;
        renderInvoiceMethod();
        invMarkPaidBtn.textContent = 'Sudah PAID ✅';
        setTimeout(() => (invMarkPaidBtn.textContent = 'Simulasi: Tandai Sudah Bayar'), 1400);
    });


    // init badge
    updateBadge();
    renderCart();

});
