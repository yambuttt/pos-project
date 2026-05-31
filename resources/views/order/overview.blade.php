<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ayo Renne — Premium Checkout</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        :root {
            --gold: #fbbf24;
            --obsidian: #070708;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--obsidian);
            color: white;
            -webkit-tap-highlight-color: transparent;
        }

        .font-heading { font-family: 'Outfit', sans-serif; }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-gold {
            background: rgba(251, 191, 36, 0.05);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(251, 191, 36, 0.2);
        }

        .premium-gradient-text {
            background: linear-gradient(135deg, #fff 0%, #fbbf24 50%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes reveal {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-reveal {
            animation: reveal 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        input:focus, select:focus, textarea:focus {
            border-color: rgba(251, 191, 36, 0.5) !important;
            background: rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.05);
        }

        @keyframes pulse-gold {
            0%, 100% {
                box-shadow: 0 4px 20px rgba(251, 191, 36, 0.2);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 4px 30px rgba(251, 191, 36, 0.6);
                transform: scale(1.02);
            }
        }

        .animate-pulse-gold {
            animation: pulse-gold 2s infinite ease-in-out;
        }
    </style>
</head>

<body class="min-h-screen relative overflow-x-hidden">
    {{-- Background Glow --}}
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_50%_-20%,rgba(251,191,36,0.1),transparent_70%)]"></div>
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_0%_100%,rgba(251,191,36,0.05),transparent_50%)]"></div>

    <div class="mx-auto max-w-4xl px-4 pb-20 pt-8">
        <header class="mb-10 flex items-center justify-between gap-6 animate-reveal">
            <div>
                <div class="mb-1 text-[10px] font-black uppercase tracking-[0.2em] text-yellow-400">Checkout Journey</div>
                <h1 class="font-heading text-3xl font-black text-white sm:text-4xl">Order <span class="premium-gradient-text">Overview</span></h1>
                <p class="mt-2 text-xs font-medium text-white/40 uppercase tracking-widest">Review your flavors before we cook</p>
            </div>

            <a href="/" class="glass flex h-12 w-12 items-center justify-center rounded-2xl text-white/40 hover:text-white hover:border-white/20 transition-all">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        </header>

        <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl animate-reveal stagger-1">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-white tracking-tight">Item Summary</h2>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/30">Selected Flavors</p>
                </div>
                <button type="button" onclick="clearAll()" class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 hover:text-white transition-colors">Clear All</button>
            </div>

            <div id="items" class="flex flex-col gap-6">
                {{-- Dynamic Items --}}
            </div>

            <div class="mt-10 space-y-4 border-t border-white/5 pt-8">
                <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-widest text-white/30">
                    <span>Subtotal</span>
                    <span class="text-white" id="subtotal">Rp 0</span>
                </div>
                <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-widest text-white/30">
                    <span>Service Tax (10%)</span>
                    <span class="text-white" id="tax">Rp 0</span>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-sm font-black uppercase tracking-[0.2em] text-yellow-400">Total Amount</span>
                    <span class="text-3xl font-black tracking-tighter text-white" id="total">Rp 0</span>
                </div>
            </div>
        </div>

        <div class="mt-6 space-y-6 animate-reveal stagger-2">
            <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl">
                <h2 class="mb-6 text-lg font-bold text-white tracking-tight">Customer Information</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Your Name</label>
                        <input id="custName" type="text" placeholder="Enter your name"
                            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white transition-all outline-none" />
                    </div>

                    @if($isDelivery)
                        <div class="relative overflow-hidden rounded-[32px] border border-yellow-400/20 bg-gradient-to-b from-yellow-400/[0.05] to-transparent p-8 backdrop-blur-xl shadow-2xl transition-all duration-300">
                            {{-- Decorative gold accent glow --}}
                            <div class="absolute -right-16 -top-16 -z-10 h-32 w-32 rounded-full bg-yellow-400/10 blur-2xl"></div>
                            
                            <div class="mb-6 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-yellow-400/10 text-yellow-400 shadow-[0_0_15px_rgba(250,204,21,0.15)]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-white">Delivery Details</h3>
                                        <p class="text-[9px] font-bold uppercase tracking-widest text-white/30">Premium Shipping Services</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 rounded-full bg-yellow-400/10 border border-yellow-400/20 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-yellow-400">
                                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-yellow-400"></span>
                                    Active Route
                                </div>
                            </div>

                            <div class="space-y-5">
                                {{-- Phone Input Group --}}
                                <div class="group relative">
                                    <label class="mb-1.5 block text-[9px] font-black uppercase tracking-widest text-white/40 group-focus-within:text-yellow-400 transition-colors ml-1">Phone Number</label>
                                    <input id="deliveryPhone" type="text" placeholder="e.g. 08123456789"
                                        class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-5 py-4 text-sm font-bold text-white transition-all outline-none focus:border-yellow-400/30 focus:bg-white/[0.04]" />
                                </div>

                                {{-- Address Input Group with Auto-detect status --}}
                                <div class="group relative">
                                    <label class="mb-1.5 block text-[9px] font-black uppercase tracking-widest text-white/40 group-focus-within:text-yellow-400 transition-colors ml-1">Full Delivery Address</label>
                                    <textarea id="deliveryAddress" rows="3" placeholder="Enter complete street name, house number, area..."
                                        class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-5 py-4 text-sm font-bold text-white transition-all outline-none focus:border-yellow-400/30 focus:bg-white/[0.04] resize-none"></textarea>
                                    
                                    {{-- Elegant Floating Geocoder Status Box --}}
                                    <div id="addressStatus" class="mt-2.5 hidden transition-all duration-300">
                                        <div class="flex items-center gap-2.5 rounded-2xl border bg-white/[0.01] px-4 py-3 text-[10px] font-bold">
                                            <div id="addressStatusIcon" class="h-4 w-4 shrink-0 rounded-full flex items-center justify-center"></div>
                                            <span id="addressStatusText" class="text-white/70 leading-normal"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <button type="button" onclick="useMyLocation()"
                                        class="group/btn flex items-center justify-center gap-2.5 rounded-2xl bg-white/[0.02] hover:bg-white/[0.05] border border-white/5 hover:border-white/10 px-4 py-4 text-[10px] font-black uppercase tracking-widest text-white active:scale-95 transition-all">
                                        <svg class="h-4 w-4 text-white/40 group-hover/btn:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        GPS Location
                                    </button>

                                    <button type="button" id="mapPickerBtn" onclick="openMapPicker()"
                                        class="flex items-center justify-center gap-2.5 rounded-2xl bg-yellow-400 hover:bg-yellow-300 border border-yellow-400/10 px-4 py-4 text-[10px] font-black uppercase tracking-widest text-black active:scale-95 transition-all shadow-[0_4px_20px_rgba(250,204,21,0.15)]">
                                        <svg class="h-4 w-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7l5-2.5 5.553 2.776a1 1 0 01.447.894v10.764a1 1 0 01-1.447.894L15 17l-6 3z"></path></svg>
                                        Pick on Map
                                    </button>
                                </div>

                                {{-- Distance & Fee Grid --}}
                                <div class="grid grid-cols-2 gap-4 pt-2">
                                    <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-white/[0.01] px-5 py-4 text-center">
                                        <div class="text-[9px] font-black uppercase tracking-widest text-white/30 mb-1">Distance</div>
                                        <div id="deliveryDistance" class="text-sm font-black text-white tracking-tight">-</div>
                                    </div>
                                    <div class="relative overflow-hidden rounded-2xl border border-yellow-400/10 bg-yellow-400/[0.02] px-5 py-4 text-center">
                                        <div class="text-[9px] font-black uppercase tracking-widest text-yellow-400/40 mb-1">Fee</div>
                                        <div id="deliveryFee" class="text-sm font-black text-yellow-400 tracking-tight">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!$isDelivery)
                        <div>
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Dining Table</label>
                            <div class="relative">
                                <select id="diningTableId" data-locked="{{ $lockedTable ? '1' : '0' }}"
                                    data-locked-id="{{ $lockedTable?->id ?? '' }}"
                                    class="w-full appearance-none rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white transition-all outline-none">
                                    <option value="" class="bg-black">— Select your table —</option>
                                    @foreach(($tables ?? []) as $t)
                                        <option value="{{ $t->id }}" class="bg-black">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute right-6 top-1/2 -translate-y-1/2 text-white/20">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            @if($lockedTable)
                                <p class="mt-3 flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-yellow-400/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-yellow-400"></span>
                                    Locked to QR: {{ $lockedTable->name }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl">
                <h2 class="mb-6 text-lg font-bold text-white tracking-tight">Payment Method</h2>
                
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="relative cursor-not-allowed group">
                        <input type="radio" name="paymentMethod" value="qris" disabled class="peer hidden">
                        <div class="flex h-full items-center gap-4 rounded-2xl border border-white/5 bg-white/[0.01] px-6 py-4 transition-all opacity-30">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-white/40">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-black text-white">QRIS</div>
                                <div class="text-[9px] font-bold uppercase tracking-widest text-white/30">Maintenance</div>
                            </div>
                        </div>
                    </label>

                    <label class="relative cursor-pointer group">
                        <input type="radio" name="paymentMethod" value="cash" checked class="peer hidden">
                        <div class="flex h-full items-center gap-4 rounded-2xl border border-white/5 bg-white/[0.01] px-6 py-4 transition-all peer-checked:border-yellow-400 peer-checked:bg-yellow-400/10 group-hover:bg-white/[0.03]">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-400/10 text-yellow-400 peer-checked:bg-yellow-400 peer-checked:text-black">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-black text-white">Cashier</div>
                                <div class="text-[9px] font-bold uppercase tracking-widest text-white/30">Pay at counter</div>
                            </div>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-yellow-400">
                                    <svg class="h-3 w-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-4">
                <button onclick="checkoutDb()" class="group relative w-full overflow-hidden rounded-[24px] bg-yellow-400 py-6 text-xs font-black uppercase tracking-[0.2em] text-black transition-all hover:bg-yellow-300 active:scale-95 shadow-[0_20px_50px_rgba(250,204,21,0.2)]">
                    <span class="relative z-10 flex items-center justify-center gap-3">
                        Place Your Order
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </span>
                    <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/40 to-white/0 transition-transform duration-1000 group-hover:translate-x-full"></div>
                </button>
                <p class="mt-6 text-center text-[10px] font-bold uppercase tracking-[0.2em] text-white/20">
                    * Order will be sent to kitchen immediately after checkout
                </p>
            </div>
        </div>
    </div>

    <!-- MAP PICKER MODAL -->
    <div id="mapBackdrop" class="fixed inset-0 z-[60] hidden bg-black/90 backdrop-blur-md transition-opacity duration-300" onclick="closeMapPicker()"></div>
    <div id="mapModal" class="fixed left-1/2 top-1/2 z-[70] hidden w-[92%] max-w-2xl -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-[40px] border border-white/10 bg-[#070708] shadow-2xl transition-all duration-500 scale-95 opacity-0" role="dialog">
        <div class="flex items-center justify-between p-8">
            <div>
                <h2 class="text-xl font-black text-white tracking-tight">Delivery <span class="text-yellow-400 font-heading italic">Location</span></h2>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/30 text-nowrap">Pin your address on the map</p>
            </div>
            <button onclick="closeMapPicker()" class="glass flex h-10 w-10 items-center justify-center rounded-full text-white/40 hover:text-white transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="px-8 pb-8">
            <div id="map" class="h-[400px] w-full rounded-3xl border border-white/5 bg-white/[0.02]"></div>

            <div class="mt-8 flex items-center justify-between gap-4">
                <div class="flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/20">Coordinate Status</p>
                    <p id="tempCoords" class="text-xs font-black text-white/40 italic mt-1 truncate">No location pinned</p>
                </div>
                <button type="button" onclick="confirmPickedLocation()" class="group relative overflow-hidden rounded-2xl bg-yellow-400 px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-black transition-all hover:bg-yellow-300 active:scale-95 shadow-xl shadow-yellow-400/10">
                    <span class="relative z-10">Confirm Location</span>
                    <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/40 to-white/0 transition-transform duration-1000 group-hover:translate-x-full"></div>
                </button>
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
        function haversinemeter(lat1, lat2, lat3, lng2) {
            const R = 6371e3;
        }

        function rupiah(n) {
            n = Math.round(Number(n) || 0);
            return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        async function applyDeliveryLocation(lat, lng) {
            deliveryLat = Number(lat);
            deliveryLng = Number(lng);

            const locStatus = document.getElementById('locStatus');
            if (locStatus) locStatus.textContent = `${deliveryLat.toFixed(6)}, ${deliveryLng.toFixed(6)}`;

            // Tampilkan indikator loading saat menghitung jarak riil
            document.getElementById('deliveryDistance').textContent = "Calculating...";
            document.getElementById('deliveryFee').textContent = "Calculating...";

            let isRealRoute = false;
            try {
                // Fetch actual road distance using OSRM
                const url = `https://router.project-osrm.org/route/v1/driving/${STORE_LNG},${STORE_LAT};${deliveryLng},${deliveryLat}?overview=false`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                    deliveryDistanceKm = data.routes[0].distance / 1000;
                    isRealRoute = true;
                } else {
                    console.warn("OSRM returned non-OK code, falling back to straight-line distance.");
                    deliveryDistanceKm = haversineKm(STORE_LAT, STORE_LNG, deliveryLat, deliveryLng);
                }
            } catch (error) {
                console.error("Failed to fetch road distance from OSRM, falling back to straight-line:", error);
                deliveryDistanceKm = haversineKm(STORE_LAT, STORE_LNG, deliveryLat, deliveryLng);
            }

            deliveryFee = Math.round(deliveryDistanceKm * RATE_PER_KM);

            document.getElementById('deliveryDistance').textContent = `${deliveryDistanceKm.toFixed(2)} KM${isRealRoute ? '' : ' (Est.)'}`;
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
        let storeMarker = null;
        let routeLineInstance = null;
        let tempPickedLat = null;
        let tempPickedLng = null;

        async function drawTempRoute(lat, lng) {
            let latLngs = [[STORE_LAT, STORE_LNG], [lat, lng]];
            let isRealRoute = false;

            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${STORE_LNG},${STORE_LAT};${lng},${lat}?overview=full&geometries=geojson`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                    latLngs = data.routes[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
                    isRealRoute = true;
                }
            } catch (error) {
                console.error("Failed to fetch road routing path:", error);
            }

            if (routeLineInstance) {
                mapInstance.removeLayer(routeLineInstance);
            }

            // Garis emas premium, jika fallback menggunakan dashed line
            routeLineInstance = L.polyline(latLngs, {
                color: '#fbbf24',
                weight: 5,
                opacity: 0.8,
                lineJoin: 'round',
                dashArray: isRealRoute ? null : '5, 10'
            }).addTo(mapInstance);

            const bounds = L.latLngBounds([
                [STORE_LAT, STORE_LNG],
                [lat, lng]
            ]);
            mapInstance.fitBounds(bounds, { padding: [50, 50] });
        }

        function openMapPicker() {
            const backdrop = document.getElementById('mapBackdrop');
            const modal = document.getElementById('mapModal');
            if (!backdrop || !modal) return;

            backdrop.classList.remove('hidden');
            setTimeout(() => backdrop.classList.add('opacity-100'), 10);
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('scale-95', 'opacity-0');
                modal.classList.add('scale-100', 'opacity-100');
            }, 10);

            setTimeout(() => {
                if (!mapInstance) {
                    mapInstance = L.map('map', { zoomControl: false }).setView([STORE_LAT || -6.2, STORE_LNG || 106.8], 14);
                    L.control.zoom({ position: 'bottomright' }).addTo(mapInstance);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(mapInstance);

                    mapInstance.on('click', function (e) {
                        tempPickedLat = e.latlng.lat;
                        tempPickedLng = e.latlng.lng;
                        document.getElementById('tempCoords').textContent = `${tempPickedLat.toFixed(6)}, ${tempPickedLng.toFixed(6)}`;
                        if (mapMarker) mapMarker.setLatLng(e.latlng);
                        else mapMarker = L.marker(e.latlng).addTo(mapInstance);

                        // Gambar rute jalan real-time saat pengguna klik peta
                        drawTempRoute(tempPickedLat, tempPickedLng);
                    });
                } else {
                    mapInstance.invalidateSize();
                }

                // Tambahkan marker restoran permanen
                if (!storeMarker) {
                    storeMarker = L.marker([STORE_LAT, STORE_LNG]).addTo(mapInstance)
                        .bindPopup("<strong style='color:#070708;'>Ayo Renne Restaurant</strong><br/><span style='color:#555;'>Titik Awal Toko</span>");
                }

                if (deliveryLat != null && deliveryLng != null) {
                    const ll = L.latLng(deliveryLat, deliveryLng);
                    tempPickedLat = deliveryLat;
                    tempPickedLng = deliveryLng;
                    document.getElementById('tempCoords').textContent = `${tempPickedLat.toFixed(6)}, ${tempPickedLng.toFixed(6)}`;
                    if (mapMarker) mapMarker.setLatLng(ll);
                    else mapMarker = L.marker(ll).addTo(mapInstance);

                    // Gambar rute yang tersimpan dan lakukan fit bounds
                    drawTempRoute(deliveryLat, deliveryLng);
                } else {
                    // Tampilkan popup di toko sebagai panduan
                    storeMarker.openPopup();
                }
            }, 300);
        }

        function closeMapPicker() {
            const backdrop = document.getElementById('mapBackdrop');
            const modal = document.getElementById('mapModal');
            if (!backdrop || !modal) return;

            backdrop.classList.remove('opacity-100');
            modal.classList.remove('scale-100', 'opacity-100');
            modal.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                backdrop.classList.add('hidden');
                modal.classList.add('hidden');
            }, 500);
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
            const tax = Math.round(subtotal * 0.10);
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
                    <div class="glass flex flex-col items-center justify-center rounded-[32px] p-12 text-center">
                        <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-yellow-400/10 text-yellow-400">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-white uppercase tracking-tight text-nowrap">Your cart is empty</h3>
                        <p class="text-xs font-medium text-white/30 uppercase tracking-widest text-nowrap">Select items to start ordering</p>
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
                row.className = "glass group relative flex flex-col lg:flex-row lg:items-center gap-6 rounded-[32px] p-6 transition-all hover:bg-white/[0.05]";
                row.innerHTML = `
                    <div class="flex flex-[1.5] items-center gap-4 min-w-0">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-white/5">
                            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200&q=80" class="h-full w-full object-cover opacity-60" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-[15px] font-black uppercase tracking-tight text-white leading-tight">${escapeHtml(name)}</h4>
                            <p class="mt-1 text-[11px] font-black text-yellow-400/60 uppercase tracking-widest">${formatRp(price)}</p>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col gap-2 lg:border-l lg:border-white/5 lg:pl-6">
                        <label class="text-[9px] font-black uppercase tracking-[0.2em] text-white/20">Chef Notes</label>
                        <input
                            type="text"
                            value="${escapeHtml(note)}"
                            oninput="setNote('${id}', this.value)"
                            placeholder="Add preferences..."
                            class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-xs font-bold text-white placeholder:text-white/20 outline-none transition-all focus:bg-white/[0.05]"
                        />
                    </div>

                    <div class="flex shrink-0 items-center justify-between lg:justify-end gap-8 pt-4 lg:pt-0 lg:border-l lg:border-white/5 lg:pl-6">
                        <div class="flex items-center gap-4">
                            <button onclick="setQty('${id}', ${qty - 1})" class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/5 text-white/40 transition-all hover:bg-white/10 hover:text-white active:scale-90">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                            </button>
                            <span class="min-w-[20px] text-center text-sm font-black text-white">${qty}</span>
                            <button onclick="setQty('${id}', ${qty + 1})" class="flex h-10 w-10 items-center justify-center rounded-2xl bg-yellow-400 text-black transition-all hover:bg-yellow-300 active:scale-90">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                        <div class="text-right min-w-[110px]">
                            <div class="text-[9px] font-black uppercase tracking-widest text-white/20">Subtotal</div>
                            <div class="text-[15px] font-black text-white tracking-tight">${formatRp(price * qty)}</div>
                        </div>
                    </div>
                `;
                wrap.appendChild(row);
            }

            const t = computeTotals(cart);
            document.getElementById('subtotal').textContent = formatRp(t.subtotal);
            document.getElementById('tax').textContent = formatRp(t.tax);
            document.getElementById('total').textContent = formatRp(t.total);
            updateTotalWithDelivery();
        }

        async function checkoutDb() {
            const IS_DELIVERY = {{ $isDelivery ? 'true' : 'false' }};
            const cart = loadOverview();
            const entries = Object.entries(cart);

            let phone = null;
            let addr = null;
            let diningTableId = null;

            if (IS_DELIVERY) {
                phone = (document.getElementById('deliveryPhone')?.value || '').trim();
                addr = (document.getElementById('deliveryAddress')?.value || '').trim();

                if (!phone) {
                    alert('No HP wajib diisi.');
                    return;
                }

                if (!addr) {
                    alert('Alamat wajib diisi.');
                    return;
                }

                if (deliveryLat == null || deliveryLng == null) {
                    alert('Silakan tentukan lokasi (Gunakan Lokasi Saya atau Pin di Map).');
                    return;
                }
            } else {
                diningTableId = (document.getElementById('diningTableId')?.value || '').trim();
                if (!diningTableId) {
                    alert('Meja wajib dipilih.');
                    return;
                }
            }

            if (entries.length === 0) {
                alert('Keranjang masih kosong.');
                return;
            }

            const name = (document.getElementById('custName')?.value || '').trim();
            if (!name) {
                alert('Nama wajib diisi.');
                return;
            }

            const selectedPaymentMethod =
                document.querySelector('input[name="paymentMethod"]:checked')?.value || 'cash';

            const items = entries.map(([id, it]) => ({
                product_id: Number(id),
                qty: Number(it.qty) || 0,
                note: (it.note || '').trim() || null,
            })).filter(x => x.qty > 0);

            if (items.length === 0) {
                alert('Keranjang masih kosong.');
                return;
            }

            const res = await fetch("{{ route('public.order.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    customer_name: name,
                    dining_table_id: IS_DELIVERY ? null : Number(diningTableId),
                    items,
                    order_type: IS_DELIVERY ? 'delivery' : 'dine_in',
                    payment_method: selectedPaymentMethod,

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

            if (json.redirect_url) {
                window.location.href = json.redirect_url;
                return;
            }

            alert('Order tersimpan! Invoice: ' + (json.invoice_no || '-'));
            window.location.href = '/';
        }
        // ===== Debounce helper =====
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // ===== Geocoding address =====
        const geocodeAddress = debounce(async (address) => {
            address = (address || '').trim();
            if (address.length < 5) {
                const statusWrap = document.getElementById('addressStatus');
                if (statusWrap) statusWrap.classList.add('hidden');
                return;
            }

            const statusWrap = document.getElementById('addressStatus');
            const statusIcon = document.getElementById('addressStatusIcon');
            const statusText = document.getElementById('addressStatusText');
            const mapPickerBtn = document.getElementById('mapPickerBtn');

            if (statusWrap) statusWrap.classList.remove('hidden');
            
            if (statusWrap && statusIcon && statusText) {
                // Loading state
                statusWrap.firstElementChild.className = "flex items-center gap-2.5 rounded-2xl border border-yellow-500/20 bg-yellow-500/5 px-4 py-3 text-[10px] font-bold transition-all duration-300";
                statusIcon.className = "h-4 w-4 shrink-0 rounded-full flex items-center justify-center text-yellow-400";
                statusIcon.innerHTML = `<svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>`;
                statusText.textContent = "🔍 Mencari koordinat alamat Anda secara otomatis...";
                statusText.className = "text-yellow-400 leading-normal";
            }

            try {
                // Bias search for Indonesia and locally in East Java if possible
                const query = encodeURIComponent(address + ", Jawa Timur");
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&limit=1&countrycodes=id`, {
                    headers: {
                        'Accept-Language': 'id',
                        'User-Agent': 'AyoRennePOSCheckout/1.0'
                    }
                });
                const data = await response.json();

                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);

                    // Apply coordinates immediately (updates distance, fee, and total)
                    await applyDeliveryLocation(lat, lng);

                    // Update map markers in background so picker matches
                    tempPickedLat = lat;
                    tempPickedLng = lng;

                    if (statusWrap && statusIcon && statusText) {
                        // Success State
                        statusWrap.firstElementChild.className = "flex items-center gap-2.5 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 px-4 py-3 text-[10px] font-bold transition-all duration-300";
                        statusIcon.className = "h-4 w-4 shrink-0 rounded-full flex items-center justify-center text-emerald-400 bg-emerald-500/10";
                        statusIcon.innerHTML = `<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>`;
                        statusText.textContent = "✨ Lokasi berhasil dipetakan! Silakan klik tombol 'Pick on Map' (kuning) untuk melihat rute emas dan konfirmasi ulang lokasi Anda.";
                        statusText.className = "text-emerald-400 leading-normal";
                    }

                    if (mapPickerBtn) mapPickerBtn.classList.add('animate-pulse-gold');
                } else {
                    // Try without "Jawa Timur" as fallback
                    const queryFallback = encodeURIComponent(address);
                    const responseFallback = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${queryFallback}&limit=1&countrycodes=id`, {
                        headers: {
                            'Accept-Language': 'id',
                            'User-Agent': 'AyoRennePOSCheckout/1.0'
                        }
                    });
                    const dataFallback = await responseFallback.json();

                    if (dataFallback && dataFallback.length > 0) {
                        const lat = parseFloat(dataFallback[0].lat);
                        const lng = parseFloat(dataFallback[0].lon);

                        await applyDeliveryLocation(lat, lng);
                        tempPickedLat = lat;
                        tempPickedLng = lng;

                        if (statusWrap && statusIcon && statusText) {
                            statusWrap.firstElementChild.className = "flex items-center gap-2.5 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 px-4 py-3 text-[10px] font-bold transition-all duration-300";
                            statusIcon.className = "h-4 w-4 shrink-0 rounded-full flex items-center justify-center text-emerald-400 bg-emerald-500/10";
                            statusIcon.innerHTML = `<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>`;
                            statusText.textContent = "✨ Lokasi berhasil dipetakan! Silakan klik tombol 'Pick on Map' (kuning) untuk melihat rute emas dan konfirmasi ulang lokasi Anda.";
                            statusText.className = "text-emerald-400 leading-normal";
                        }
                        if (mapPickerBtn) mapPickerBtn.classList.add('animate-pulse-gold');
                    } else {
                        if (statusWrap && statusIcon && statusText) {
                            // Not found State
                            statusWrap.firstElementChild.className = "flex items-center gap-2.5 rounded-2xl border border-red-500/20 bg-red-500/5 px-4 py-3 text-[10px] font-bold transition-all duration-300";
                            statusIcon.className = "h-4 w-4 shrink-0 rounded-full flex items-center justify-center text-red-400 bg-red-500/10";
                            statusIcon.innerHTML = `<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;
                            statusText.textContent = "⚠️ Alamat kurang spesifik / tidak terdeteksi. Silakan gunakan tombol 'Pick on Map' untuk memin lokasi manual Anda.";
                            statusText.className = "text-red-400 leading-normal";
                        }
                        if (mapPickerBtn) mapPickerBtn.classList.remove('animate-pulse-gold');
                    }
                }
            } catch (error) {
                console.error("Geocoding failed:", error);
                if (statusWrap && statusIcon && statusText) {
                    statusWrap.firstElementChild.className = "flex items-center gap-2.5 rounded-2xl border border-red-500/20 bg-red-500/5 px-4 py-3 text-[10px] font-bold transition-all duration-300";
                    statusIcon.className = "h-4 w-4 shrink-0 rounded-full flex items-center justify-center text-red-400 bg-red-500/10";
                    statusIcon.innerHTML = `<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;
                    statusText.textContent = "⚠️ Gagal menghubungkan ke pencari lokasi otomatis. Silakan gunakan tombol 'Pick on Map' secara manual.";
                    statusText.className = "text-red-400 leading-normal";
                }
                if (mapPickerBtn) mapPickerBtn.classList.remove('animate-pulse-gold');
            }
        }, 1000);

        // Bind geocoding to textarea input
        document.addEventListener('DOMContentLoaded', () => {
            const addressInput = document.getElementById('deliveryAddress');
            if (addressInput) {
                addressInput.addEventListener('input', (e) => {
                    geocodeAddress(e.target.value);
                });
            }
        });

        // init: pastikan key qty ikut sinkron dari overview (kalau user reload di halaman ini)
        (function init() {
            const ov = loadOverview();
            syncQtyFromOverview(ov);
            render();
        })();
    </script>
</body>

</html>