<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ayo Renne — Overview Pesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>

<body class="min-h-screen text-white">
    <div class="fixed inset-0 -z-10 bg-gradient-to-b from-[#070708] to-[#0b0b0d]"></div>

    <div class="mx-auto max-w-3xl px-4 pb-20 pt-8">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <div class="text-xs tracking-[0.22em] uppercase text-white/80">Ayo Renne</div>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight">Overview Pesanan</h1>
                <p class="mt-1 text-sm text-white/65">Cek item kamu dulu, lalu isi keterangan sebelum checkout.</p>
            </div>

            <a href="/"
                class="rounded-xl border border-white/15 bg-black/20 px-4 py-2 text-sm font-semibold text-white/90 backdrop-blur-2xl hover:bg-white/10">
                ← Kembali
            </a>
        </div>

        <div class="rounded-[24px] border border-white/15 bg-white/10 p-4 shadow-2xl backdrop-blur-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-xs tracking-[0.22em] uppercase text-white/80">Item Pesanan</h2>
                <button type="button"
                    class="rounded-xl border border-white/15 bg-black/20 px-3 py-2 text-xs font-semibold text-white/85 backdrop-blur-2xl hover:bg-white/10"
                    onclick="clearAll()">Clear</button>
            </div>

            <div id="items" class="mt-4 flex flex-col gap-2"></div>

            <div class="mt-4 space-y-2 border-t border-white/10 pt-4 text-sm">
                <div class="flex items-center justify-between text-white/75">
                    <span>Subtotal</span>
                    <span class="font-semibold text-white/90" id="subtotal">Rp 0</span>
                </div>
                <div class="flex items-center justify-between text-white/75">
                    <span>Estimasi Pajak (11%)</span>
                    <span class="font-semibold text-white/90" id="tax">Rp 0</span>
                </div>
                <div class="flex items-center justify-between text-white/75">
                    <span>Total</span>
                    <span class="font-semibold text-white/95" id="total">Rp 0</span>
                </div>
            </div>
        </div>

        <div class="mt-4 rounded-[24px] border border-white/15 bg-white/10 p-4 shadow-2xl backdrop-blur-2xl">
            <h2 class="text-xs tracking-[0.22em] uppercase text-white/80">Keterangan</h2>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-xs text-white/60">Nama</label>
                    <input id="custName" type="text" placeholder="Nama kamu"
                        class="mt-2 w-full rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm text-white/90 placeholder:text-white/40 outline-none focus:border-white/30" />
                </div>

                @if($isDelivery)
                    <div class="mt-4 rounded-2xl border border-white/15 bg-black/20 p-4">
                        <div class="text-sm font-semibold text-yellow-300">Mode: Delivery</div>

                        <div class="mt-4 space-y-3">
                            <input id="deliveryPhone" type="text" placeholder="No HP"
                                class="w-full rounded-xl border border-white/15 bg-black/30 px-4 py-3 text-sm text-white/90 placeholder:text-white/40 outline-none" />

                            <textarea id="deliveryAddress" rows="3" placeholder="Alamat lengkap"
                                class="w-full rounded-xl border border-white/15 bg-black/30 px-4 py-3 text-sm text-white/90 placeholder:text-white/40 outline-none"></textarea>

                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="useMyLocation()"
                                    class="rounded-xl bg-yellow-400/90 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-300">
                                    Gunakan Lokasi Saya
                                </button>

                                <button type="button" onclick="openMapPicker()"
                                    class="rounded-xl border border-white/15 bg-black/20 px-4 py-2 text-sm font-semibold text-white/90 hover:bg-white/10">
                                    Pin Lokasi di Map
                                </button>
                            </div>

                            <div class="text-xs text-white/60">
                                Lokasi: <span id="locStatus">Belum dipilih</span>
                            </div>

                            <div class="text-xs text-white/60">
                                Jarak: <span id="deliveryDistance">-</span>
                            </div>

                            <div class="text-xs text-white/60">
                                Ongkir: <span id="deliveryFee">Rp 0</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- =========================
                BAGIAN MEJA (HANYA DINE IN)
                ========================= --}}
                @if(!$isDelivery)
                    <div class="sm:col-span-2">
                        <label class="text-xs text-white/60">Meja</label>

                        <select id="diningTableId" data-locked="{{ $lockedTable ? '1' : '0' }}"
                            data-locked-id="{{ $lockedTable?->id ?? '' }}"
                            class="mt-2 w-full rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm text-white/90 outline-none focus:border-white/30">
                            <option value="">— Pilih meja —</option>
                            @foreach(($tables ?? []) as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>

                        @if($lockedTable)
                            <p class="mt-2 text-xs text-green-200/90">
                                Meja terkunci dari QR: <b>{{ $lockedTable->name }}</b>
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <button
                class="mt-4 w-full rounded-xl bg-yellow-400/95 px-4 py-3 text-sm font-semibold text-black shadow-lg shadow-yellow-400/10 hover:bg-yellow-300"
                type="button" onclick="checkoutDb()">
                Checkout
            </button>

            <p class="mt-2 text-xs text-white/50">
                * Checkout akan menyimpan order ke database & masuk antrean kitchen.
            </p>
        </div>
    </div>

    <!-- MAP PICKER BACKDROP -->
    <div id="mapBackdrop" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-[2px]" onclick="closeMapPicker()">
    </div>

    <!-- MAP PICKER MODAL -->
    <div id="mapModal"
        class="fixed left-1/2 top-1/2 z-50 hidden w-[92%] max-w-[720px] -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-[24px] border border-white/15 bg-black/55 shadow-2xl backdrop-blur-2xl">
        <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
            <div class="text-sm font-semibold text-white/90">Pilih Lokasi Pengantaran</div>
            <button type="button"
                class="rounded-xl border border-white/15 bg-black/30 px-3 py-2 text-xs font-semibold text-white/90 hover:bg-white/10"
                onclick="closeMapPicker()">Tutup</button>
        </div>

        <div class="p-4">
            <div class="text-xs text-white/60 mb-2">
                Klik di peta untuk pasang pin. Kamu juga bisa order untuk orang lain.
            </div>

            <div id="map" class="h-[420px] w-full rounded-2xl border border-white/10"></div>

            <div class="mt-3 flex items-center justify-end gap-2">
                <button type="button"
                    class="rounded-xl border border-white/15 bg-black/25 px-4 py-2 text-sm text-white/90 hover:bg-white/10"
                    onclick="closeMapPicker()">Batal</button>
                <button type="button"
                    class="rounded-xl bg-yellow-400/95 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-300"
                    onclick="confirmPickedLocation()">Pakai Lokasi Ini</button>
            </div>
        </div>
    </div>

    <script>

        // ===== Delivery config =====
        const RATE_PER_KM = 3000;

        // TODO: set ini dari env / config server (contoh hardcode dulu)
        const STORE_LAT = {{ (float) env('STORE_LAT', 0) }};
        const STORE_LNG = {{ (float) env('STORE_LNG', 0) }};

        let deliveryLat = null;
        let deliveryLng = null;
        let deliveryDistanceKm = 0;
        let deliveryFee = 0;

        // ===== Haversine =====
        function toRad(v) { return v * Math.PI / 180; }

        function haversineKm(lat1, lng1, lat2, lng2) {
            const R = 6371;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function rupiah(n) {
            n = Math.round(Number(n) || 0);
            return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function applyDeliveryLocation(lat, lng) {
            deliveryLat = Number(lat);
            deliveryLng = Number(lng);

            deliveryDistanceKm = haversineKm(STORE_LAT, STORE_LNG, deliveryLat, deliveryLng);
            deliveryFee = Math.round(deliveryDistanceKm * RATE_PER_KM);

            const locStatus = document.getElementById('locStatus');
            if (locStatus) locStatus.textContent = `${deliveryLat.toFixed(6)}, ${deliveryLng.toFixed(6)}`;

            document.getElementById('deliveryDistance').textContent = `${deliveryDistanceKm.toFixed(2)} KM`;
            document.getElementById('deliveryFee').textContent = rupiah(deliveryFee);

            // update total UI: total = subtotal + tax + deliveryFee
            updateTotalWithDelivery();
        }

        // ===== Use my location (optional) =====
        function useMyLocation() {
            if (!navigator.geolocation) {
                alert('Browser tidak mendukung GPS. Silakan pin lokasi di map.');
                openMapPicker();
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    applyDeliveryLocation(pos.coords.latitude, pos.coords.longitude);
                },
                () => {
                    alert('Gagal mengambil lokasi. Silakan pin lokasi di map.');
                    openMapPicker();
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        // ===== Map Picker (Leaflet) =====
        let mapInstance = null;
        let mapMarker = null;
        let tempPickedLat = null;
        let tempPickedLng = null;

        function openMapPicker() {
            document.getElementById('mapBackdrop')?.classList.remove('hidden');
            document.getElementById('mapModal')?.classList.remove('hidden');

            setTimeout(() => {
                if (!mapInstance) {
                    // default center: toko
                    mapInstance = L.map('map').setView([STORE_LAT || -6.2, STORE_LNG || 106.8], 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(mapInstance);

                    mapInstance.on('click', function (e) {
                        tempPickedLat = e.latlng.lat;
                        tempPickedLng = e.latlng.lng;

                        if (mapMarker) mapMarker.setLatLng(e.latlng);
                        else mapMarker = L.marker(e.latlng).addTo(mapInstance);

                    });
                } else {
                    mapInstance.invalidateSize();
                }

                // kalau sudah pernah punya lokasi delivery, tampilkan marker di situ
                if (deliveryLat != null && deliveryLng != null) {
                    const ll = L.latLng(deliveryLat, deliveryLng);
                    tempPickedLat = deliveryLat;
                    tempPickedLng = deliveryLng;
                    if (mapMarker) mapMarker.setLatLng(ll);
                    else mapMarker = L.marker(ll).addTo(mapInstance);
                    mapInstance.setView(ll, 15);
                }
            }, 50);
        }

        function closeMapPicker() {
            document.getElementById('mapBackdrop')?.classList.add('hidden');
            document.getElementById('mapModal')?.classList.add('hidden');
        }

        function confirmPickedLocation() {
            if (tempPickedLat == null || tempPickedLng == null) {
                alert('Silakan klik peta untuk memasang pin.');
                return;
            }
            applyDeliveryLocation(tempPickedLat, tempPickedLng);
            closeMapPicker();
        }

        // ===== Update total UI =====
        // kamu sudah punya subtotal/tax/total di halaman.
        // Cara paling aman: ambil dari elemen yang sudah dihitung oleh render() kamu.
        // Kalau kamu punya variable totals sendiri, sesuaikan di sini.
        function parseRp(text) {
            // "Rp 26.640" -> 26640
            return Number(String(text || '').replace(/[^\d]/g, '') || 0);
        }

        function updateTotalWithDelivery() {
            // total sebelum ongkir ada di element #total
            const subtotalEl = document.getElementById('subtotal');
            const taxEl = document.getElementById('tax');
            const totalEl = document.getElementById('total');

            if (!subtotalEl || !taxEl || !totalEl) return;

            const subtotal = parseRp(subtotalEl.textContent);
            const tax = parseRp(taxEl.textContent);

            const newTotal = subtotal + tax + (deliveryFee || 0);
            totalEl.textContent = rupiah(newTotal);
        }
        // Keys harus sama dengan welcome.blade.php terbaru
        const CART_QTY_KEY = 'ayo_renne_cart_v1';                // qty map
        const CART_OVERVIEW_KEY = 'ayo_renne_order_overview_v1'; // detail map

        function loadOverview() {
            try { return JSON.parse(localStorage.getItem(CART_OVERVIEW_KEY) || '{}') || {}; }
            catch { return {}; }
        }

        function saveOverview(cart) {
            localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify(cart));
        }

        function syncQtyFromOverview(overview) {
            const qty = {};
            for (const [id, it] of Object.entries(overview)) {
                qty[id] = Number(it.qty) || 0;
            }
            localStorage.setItem(CART_QTY_KEY, JSON.stringify(qty));
        }

        function clearAll() {
            localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify({}));
            localStorage.setItem(CART_QTY_KEY, JSON.stringify({}));
            render();
        }

        function formatRp(n) {
            n = Math.round(Number(n) || 0);
            return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function computeTotals(overview) {
            let subtotal = 0;
            for (const it of Object.values(overview)) {
                subtotal += (Number(it.price) || 0) * (Number(it.qty) || 0);
            }
            const tax = Math.round(subtotal * 0.11);
            return { subtotal, tax, total: subtotal + tax };
        }

        function updateGrandTotal() {

            let subtotal = currentSubtotal; // pakai variable kamu yg sudah ada
            let tax = currentTax;

            let grandTotal = subtotal + tax + deliveryFee;

            document.getElementById('grandTotal').innerText =
                "Rp " + grandTotal.toLocaleString('id-ID');
        }

        function setQty(id, qty) {
            const cart = loadOverview();
            if (!cart[id]) return;

            qty = Number(qty) || 0;
            if (qty <= 0) delete cart[id];
            else cart[id].qty = qty;

            saveOverview(cart);
            syncQtyFromOverview(cart);
            render();
        }

        function setNote(id, note) {
            const cart = loadOverview();
            if (!cart[id]) return;

            cart[id].note = String(note ?? '');
            saveOverview(cart);
            // qty tidak berubah, tapi kita tetap sync biar konsisten
            syncQtyFromOverview(cart);
        }

        (function lockTableFromQr() {
            const sel = document.getElementById('diningTableId');
            if (!sel) return;

            const locked = sel.dataset.locked === '1';
            const lockedId = (sel.dataset.lockedId || '').trim();

            if (locked && lockedId) {
                sel.value = lockedId;
                sel.disabled = true;
            }
        })();

        function render() {
            const cart = loadOverview();
            const entries = Object.entries(cart);
            const wrap = document.getElementById('items');
            wrap.innerHTML = '';

            if (entries.length === 0) {
                wrap.innerHTML = `
          <div class="rounded-2xl border border-white/10 bg-black/20 px-4 py-4 text-center text-sm text-white/65 backdrop-blur-2xl">
            Keranjang kosong. Silakan pilih menu dulu 🙂
          </div>`;
                document.getElementById('subtotal').textContent = 'Rp 0';
                document.getElementById('tax').textContent = 'Rp 0';
                document.getElementById('total').textContent = 'Rp 0';
                return;
            }

            for (const [id, it] of entries) {
                const name = it.name || '-';
                const price = Number(it.price) || 0;
                const qty = Number(it.qty) || 0;
                const note = (typeof it.note === 'string') ? it.note : '';

                const row = document.createElement('div');
                row.className = "flex items-start justify-between gap-3 rounded-2xl border border-white/10 bg-black/20 px-4 py-3 backdrop-blur-2xl";
                row.innerHTML = `
          <div class="min-w-0 flex-1">
            <div class="truncate text-sm font-semibold text-white/90">${escapeHtml(name)}</div>
            <div class="mt-1 text-xs text-white/55">${formatRp(price)} × ${qty}</div>

            <div class="mt-2">
              <label class="block text-[11px] text-white/55">Catatan item (opsional)</label>
              <input
                type="text"
                value="${escapeHtml(note)}"
                oninput="setNote('${id}', this.value)"
                placeholder="Contoh: tanpa sambal / pedas sedang"
                class="mt-1 w-full rounded-xl border border-white/12 bg-black/25 px-3 py-2 text-sm text-white/90 placeholder:text-white/35 outline-none focus:border-white/25"
              />
            </div>
          </div>

          <div class="flex items-center gap-2 pt-1">
            <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/10"
                    onclick="setQty('${id}', ${qty - 1})" type="button">−</button>
            <div class="min-w-[20px] text-center text-sm font-bold text-white/95">${qty}</div>
            <button class="h-8 w-8 rounded-xl border border-white/12 bg-black/25 text-white/90 font-black hover:bg-white/10"
                    onclick="setQty('${id}', ${qty + 1})" type="button">+</button>
          </div>
        `;
                wrap.appendChild(row);
            }

            const t = computeTotals(cart);
            document.getElementById('subtotal').textContent = formatRp(t.subtotal);
            document.getElementById('tax').textContent = formatRp(t.tax);
            document.getElementById('total').textContent = formatRp(t.total);
        }

        async function checkoutDb() {
            const IS_DELIVERY = {{ $isDelivery ? 'true' : 'false' }};
            const cart = loadOverview();
            const entries = Object.entries(cart);
            if (IS_DELIVERY) {
                const phone = (document.getElementById('deliveryPhone')?.value || '').trim();
                const addr = (document.getElementById('deliveryAddress')?.value || '').trim();

                if (!phone) { alert('No HP wajib diisi.'); return; }
                if (!addr) { alert('Alamat wajib diisi.'); return; }
                if (deliveryLat == null || deliveryLng == null) {
                    alert('Silakan tentukan lokasi (Gunakan Lokasi Saya atau Pin di Map).');
                    return;
                }
            } else {
                const diningTableId = (document.getElementById('diningTableId').value || '').trim();
                if (!diningTableId) { alert('Meja wajib dipilih.'); return; }
            }

            if (entries.length === 0) { alert('Keranjang masih kosong.'); return; }

            const name = (document.getElementById('custName').value || '').trim();
            if (!name) { alert('Nama wajib diisi.'); return; }

            const diningTableId = (document.getElementById('diningTableId').value || '').trim();
            if (!diningTableId) { alert('Meja wajib dipilih.'); return; }

            const items = entries.map(([id, it]) => ({
                product_id: Number(id),
                qty: Number(it.qty) || 0,
                note: (it.note || '').trim() || null,
            })).filter(x => x.qty > 0);

            if (items.length === 0) { alert('Keranjang masih kosong.'); return; }

            const res = await fetch("{{ route('public.order.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    customer_name: name,
                    dining_table_id: Number(diningTableId),
                    items,
                    order_type: IS_DELIVERY ? 'delivery' : 'dine_in',
                    delivery_phone: IS_DELIVERY ? phone : null,
                    delivery_address: IS_DELIVERY ? addr : null,
                    delivery_lat: IS_DELIVERY ? deliveryLat : null,
                    delivery_lng: IS_DELIVERY ? deliveryLng : null,
                    delivery_distance_km: IS_DELIVERY ? Number(deliveryDistanceKm.toFixed(2)) : null,
                    delivery_fee: IS_DELIVERY ? deliveryFee : 0,
                })
            });

            const json = await res.json().catch(() => ({}));

            if (!res.ok || !json.ok) {
                alert(json.message || 'Checkout gagal.');
                return;
            }

            clearAll();
            localStorage.removeItem('ayo_renne_table_token_v1');
            alert('Order tersimpan! Invoice: ' + (json.invoice_no || '-'));
            window.location.href = '/';
        }

        // init: pastikan key qty ikut sinkron dari overview (kalau user reload di halaman ini)
        (function init() {
            const ov = loadOverview();
            syncQtyFromOverview(ov);
            render();
        })();
    </script>
</body>

</html>