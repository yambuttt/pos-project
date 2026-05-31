<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Ayo Renne — Experience Premium Dining</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand-gold: #FACC15;
            --brand-gold-muted: rgba(250, 204, 21, 0.15);
            --bg-deep: #070708;
            --bg-card: rgba(255, 255, 255, 0.04);
        }

        body {
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-gold {
            background: rgba(250, 204, 21, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(250, 204, 21, 0.15);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
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

        .premium-gradient-text {
            background: linear-gradient(135deg, #FFF 0%, #FACC15 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: rgba(250, 204, 21, 0.4);
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.5);
        }
    </style>
</head>

<body class="min-h-screen bg-[#070708] text-white selection:bg-yellow-400 selection:text-black">
    {{-- Background Elements --}}
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(250,204,21,0.1),transparent_40%),radial-gradient(circle_at_bottom_right,rgba(255,255,255,0.05),transparent_40%)]"></div>
    <div class="fixed -left-20 -top-20 -z-10 h-96 w-96 rounded-full bg-yellow-400/10 blur-[100px] animate-pulse"></div>
    <div class="fixed -bottom-20 -right-20 -z-10 h-96 w-96 rounded-full bg-white/5 blur-[100px] animate-pulse" style="animation-delay: 2s"></div>

    <div class="mx-auto max-w-7xl px-4 pb-28 pt-6 sm:px-6 lg:px-8">
        {{-- Navigation Bar --}}
        <nav class="mb-8 flex items-center justify-between gap-4 animate-reveal">
            <div class="flex items-center gap-4">
                <div class="group relative">
                    <div class="absolute -inset-1 rounded-2xl bg-gradient-to-r from-yellow-400 to-yellow-600 opacity-25 blur transition duration-1000 group-hover:opacity-50"></div>
                    <div class="relative flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl border border-white/10 bg-black shadow-2xl">
                        <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne" class="h-10 w-10 object-contain transition-transform duration-500 group-hover:scale-110">
                    </div>
                </div>
                <div class="hidden sm:block">
                    <h1 class="text-lg font-black uppercase tracking-[0.2em] text-white">Ayo <span class="text-yellow-400">Renne</span></h1>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">Exclusive Dining Experience</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="glass-gold hidden items-center gap-3 rounded-full px-5 py-2.5 text-[11px] font-black uppercase tracking-[0.15em] text-yellow-100/80 sm:flex">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-yellow-400"></span>
                    </span>
                    <span id="timeGreeting">Welcome back</span>
                </div>
                <button onclick="openCart()" class="glass flex h-12 w-12 items-center justify-center rounded-2xl transition-all hover:border-yellow-400/50 hover:bg-white/5">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </button>
            </div>
        </nav>

        <header class="mb-12 grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="glass relative overflow-hidden rounded-[32px] p-8 sm:p-12 lg:col-span-8 animate-reveal stagger-1">
                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-yellow-400/10 blur-[80px]"></div>
                
                <div class="relative z-10">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-yellow-400/20 bg-yellow-400/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-yellow-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-400"></span>
                        Freshly Prepared Today
                    </div>
                    
                    <h2 class="mb-6 text-4xl font-black leading-[1.1] tracking-tight text-white sm:text-6xl">
                        Discover the Art of <br/>
                        <span class="premium-gradient-text italic">Fine Dining</span>
                    </h2>
                    
                    <p class="mb-8 max-w-xl text-sm leading-relaxed text-white/50 sm:text-base">
                        Explore our curated selection of signature dishes and handcrafted beverages. 
                        Every ingredient is chosen with precision to ensure an unforgettable culinary journey.
                    </p>
                    
                    <div class="flex flex-wrap items-center gap-4">
                        <button onclick="document.getElementById('menu-section').scrollIntoView({ behavior: 'smooth' })" class="group relative inline-flex items-center justify-center overflow-hidden rounded-2xl bg-yellow-400 px-8 py-4 text-xs font-black uppercase tracking-widest text-black transition-all hover:bg-yellow-300 active:scale-95">
                            <span class="relative z-10">Start Ordering</span>
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/40 to-white/0 transition-transform duration-1000 group-hover:translate-x-full"></div>
                        </button>
                        <button onclick="openCart()" class="glass-gold px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-white/5 active:scale-95">
                            View Cart
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-6 lg:col-span-4">
                <div class="glass flex-1 rounded-[32px] p-8 animate-reveal stagger-2">
                    <div class="mb-4 text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Order Info</div>
                    <h3 class="mb-4 text-xl font-bold text-white">Seamless Experience</h3>
                    <ul class="space-y-4">
                        @foreach(['Smart Search', 'Touch Optimized', 'Live Tracking'] as $info)
                        <li class="flex items-center gap-3 text-xs font-medium text-white/60">
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-yellow-400/10 text-yellow-400">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            {{ $info }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="glass-gold rounded-[32px] p-8 animate-reveal stagger-3">
                    <div class="mb-2 flex items-center justify-between">
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-yellow-400/60">Status</div>
                        <div class="h-2 w-2 animate-pulse rounded-full bg-yellow-400"></div>
                    </div>
                    <div class="text-2xl font-black text-white" id="menuCount">0 Items</div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/30 mt-1">Available to order</p>
                </div>
            </div>
        </header>

        <section id="menu-section" class="relative z-10 animate-reveal" style="animation-delay: 0.4s">
            <div class="mb-10 flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-md">
                    <h2 class="text-3xl font-black text-white">Our Signature <span class="text-yellow-400">Menu</span></h2>
                    <p class="mt-2 text-sm font-medium text-white/40 uppercase tracking-widest">Select your favorites from our collection</p>
                </div>

                <div class="relative w-full lg:max-w-md">
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-yellow-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input id="searchInput" type="text" placeholder="Search our flavors..." 
                           class="w-full rounded-[20px] border border-white/10 bg-white/[0.03] py-5 pl-14 pr-20 text-sm font-bold text-white placeholder:text-white/20 outline-none transition-all focus:border-yellow-400/50 focus:bg-white/[0.05] focus:ring-4 focus:ring-yellow-400/5 shadow-2xl">
                    <button id="clearSearch" class="absolute right-4 top-1/2 -translate-y-1/2 hidden glass px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-white/60 hover:text-white">Clear</button>
                </div>
            </div>

            <div class="sticky top-4 z-30 mb-10">
                <div id="filters" class="glass flex gap-2 overflow-x-auto p-2 rounded-[24px] hide-scrollbar shadow-2xl">
                    <button class="pill active whitespace-nowrap rounded-2xl border border-yellow-400 bg-yellow-400 px-8 py-3.5 text-[10px] font-black uppercase tracking-[0.2em] text-black shadow-lg shadow-yellow-400/20 transition-all"
                            data-cat="__all" type="button">All Flavors</button>
                    @foreach($categories as $c)
                        <button class="pill whitespace-nowrap rounded-2xl border border-white/5 bg-white/[0.02] px-8 py-3.5 text-[10px] font-black uppercase tracking-[0.2em] text-white/40 transition-all hover:bg-white/[0.06] hover:text-white"
                                data-cat="{{ $c }}" type="button">{{ $c }}</button>
                    @endforeach
                </div>
            </div>

            <div class="mb-6 flex items-center justify-between">
                <div class="glass px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest text-white/40">
                    <span id="searchMeta">Showing all items</span>
                </div>
            </div>
            <div id="menuGrid" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $p)
                    @php
                        $max = (int) $p->maxServingsFromStock();
                        $sellable = $max > 0;
                        $cat = trim((string) ($p->category ?? 'Lainnya'));
                        $img = $p->imageUrl();
                    @endphp

                    <article class="card-hover group relative flex flex-col overflow-hidden rounded-[32px] border border-white/5 bg-white/[0.02] p-2 shadow-2xl transition-all animate-reveal"
                             data-cat="{{ $cat }}" data-id="{{ $p->id }}" data-name="{{ $p->name }}"
                             data-price="{{ (int) $p->price }}" data-desc="{{ e($p->description ?? '') }}"
                             data-img="{{ $img ? e($img) : '' }}" onclick="openProductModal({{ $p->id }})">
                        
                        <div class="relative aspect-square overflow-hidden rounded-[26px] bg-white/[0.02]">
                            @if($img)
                                <img src="{{ $img }}" alt="{{ $p->name }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-yellow-400/10 to-transparent">
                                    <svg class="h-12 w-12 text-white/5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            
                            <div class="absolute right-3 top-3">
                                <div class="glass-gold rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-widest text-yellow-400">
                                    {{ $cat }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-4">
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <h3 class="line-clamp-1 text-sm font-bold text-white group-hover:text-yellow-400 transition-colors uppercase tracking-tight">{{ $p->name }}</h3>
                            </div>
                            
                            <p class="mb-4 line-clamp-2 text-[11px] leading-relaxed text-white/30 font-medium">
                                {{ $p->description ?: 'Discover our signature taste prepared with passion.' }}
                            </p>

                            <div class="mt-auto flex items-center justify-between gap-3 pt-2">
                                <div class="text-base font-black text-white">
                                    <span class="text-xs text-yellow-400">Rp</span> {{ number_format((int) $p->price, 0, ',', '.') }}
                                </div>

                                <button type="button" onclick="event.stopPropagation(); addToCart({{ $p->id }});"
                                        @if(!$sellable) disabled @endif 
                                        class="flex h-10 w-10 items-center justify-center rounded-xl transition-all
                                               @if($sellable)
                                                   bg-yellow-400 text-black shadow-lg shadow-yellow-400/20 hover:bg-yellow-300 active:scale-90
                                               @else
                                                   cursor-not-allowed bg-white/5 text-white/10
                                               @endif">
                                    @if($sellable)
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @endif
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            </div>
        </section>
    </div>

    {{-- Floating Cart Action --}}
    <div id="fabCart" class="fixed bottom-8 left-1/2 z-40 w-full max-w-md -translate-x-1/2 px-4 transition-all duration-500 sm:bottom-10 animate-reveal" style="animation-delay: 0.6s">
        <button onclick="openCart()" class="group relative flex w-full items-center justify-between overflow-hidden rounded-[24px] bg-yellow-400 p-2 shadow-[0_20px_50px_rgba(250,204,21,0.3)] transition-all hover:bg-yellow-300 active:scale-95">
            <div class="flex items-center gap-4 pl-4 text-black">
                <div class="relative">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <div id="cartBadge" class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-black text-[10px] font-black text-yellow-400">0</div>
                </div>
                <div class="text-left">
                    <div class="text-[10px] font-black uppercase tracking-widest opacity-40">Your Selection</div>
                    <div class="text-sm font-black">View Cart Summary</div>
                </div>
            </div>
            <div class="glass flex h-14 items-center px-6 rounded-2xl bg-black/5 border-none">
                <span id="mobileCartTotal" class="text-sm font-black text-black">Rp 0</span>
            </div>
        </button>
    </div>

    {{-- Cart Backdrop --}}
    <div id="cartBackdrop" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm transition-opacity duration-300" onclick="closeCart()"></div>

    {{-- Cart Drawer --}}
    <aside id="cartDrawer" class="fixed right-0 top-0 z-50 flex h-full w-full max-w-lg translate-x-full flex-col bg-[#070708] shadow-[-20px_0_60px_rgba(0,0,0,0.5)] transition-transform duration-500 ease-[cubic-bezier(0.2,0.8,0.2,1)]" role="dialog">
        <div class="flex flex-col h-full border-l border-white/5">
            <div class="flex items-center justify-between p-8">
                <div>
                    <h2 class="text-2xl font-black text-white tracking-tight">Your <span class="text-yellow-400 font-heading italic">Order</span></h2>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/30">Review your selection below</p>
                </div>
                <button onclick="closeCart()" class="glass flex h-12 w-12 items-center justify-center rounded-2xl text-white/40 hover:text-white hover:border-white/20 transition-all">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div id="cartList" class="flex-1 overflow-y-auto px-8 space-y-4 hide-scrollbar">
                {{-- Dynamic Items --}}
            </div>

            <div class="p-8">
                <div class="glass rounded-[32px] p-8 space-y-6">
                    <div class="space-y-3">
                        <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-white/30">
                            <span>Subtotal</span>
                            <span id="subtotal" class="text-white">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-white/30">
                            <span>Tax (10%)</span>
                            <span id="tax" class="text-white">Rp 0</span>
                        </div>
                        <div class="h-px bg-white/5 my-4"></div>
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-black uppercase tracking-[0.2em] text-yellow-400">Total Bill</span>
                            <span id="total" class="text-3xl font-black text-white tracking-tighter tracking-tight">Rp 0</span>
                        </div>
                    </div>

                    <button onclick="goToOverview()" class="group relative w-full overflow-hidden rounded-2xl bg-yellow-400 py-5 text-xs font-black uppercase tracking-[0.2em] text-black transition-all hover:bg-yellow-300 active:scale-95 shadow-xl shadow-yellow-400/10">
                        <span class="relative z-10">Proceed to Checkout</span>
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/40 to-white/0 transition-transform duration-1000 group-hover:translate-x-full"></div>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    {{-- Product Modal --}}
    <div id="productBackdrop" class="fixed inset-0 z-50 hidden bg-black/90 backdrop-blur-md transition-opacity duration-300" onclick="closeProductModal()"></div>
    <div id="productModal" class="fixed left-1/2 top-1/2 z-50 hidden w-[92%] max-w-xl -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-[40px] border border-white/10 bg-[#070708] shadow-2xl transition-all duration-500 scale-95 opacity-0" role="dialog">
        <div class="relative aspect-[4/3] overflow-hidden">
            <div id="pmImageWrap" class="h-full w-full bg-white/[0.02]">
                <img id="pmImage" class="hidden h-full w-full object-cover" alt="">
                <div id="pmNoImage" class="flex h-full w-full items-center justify-center bg-gradient-to-br from-yellow-400/10 to-transparent">
                    <svg class="h-20 w-20 text-white/5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <button onclick="closeProductModal()" class="glass absolute right-6 top-6 flex h-10 w-10 items-center justify-center rounded-full text-white/40 hover:text-white transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-8 sm:p-10">
            <div class="mb-6 flex items-start justify-between gap-6">
                <div class="flex-1">
                    <div id="pmCategory" class="mb-2 text-[10px] font-black uppercase tracking-[0.2em] text-yellow-400/60">Category</div>
                    <h3 id="pmName" class="text-3xl font-black text-white leading-tight">Product Name</h3>
                </div>
                <div id="pmPrice" class="text-2xl font-black text-white">Rp 0</div>
            </div>

            <p id="pmDesc" class="mb-10 text-sm leading-relaxed text-white/40 font-medium">Description</p>

            <button id="pmAddBtn" class="group relative w-full overflow-hidden rounded-2xl bg-yellow-400 py-5 text-xs font-black uppercase tracking-[0.2em] text-black transition-all hover:bg-yellow-300 active:scale-95 shadow-xl shadow-yellow-400/10">
                <span class="relative z-10">Add to Order</span>
                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/40 to-white/0 transition-transform duration-1000 group-hover:translate-x-full"></div>
            </button>
        </div>
    </div>

    <script>
        // ===== Greeting =====
        (function () {
            const h = new Date().getHours();
            let t = 'Selamat datang';
            if (h >= 4 && h < 11) t = 'Selamat pagi 👋';
            else if (h >= 11 && h < 15) t = 'Selamat siang 👋';
            else if (h >= 15 && h < 18) t = 'Selamat sore 👋';
            else t = 'Selamat malam 👋';

            const el = document.getElementById('timeGreeting');
            if (el) el.textContent = t;
        })();

        // ===== Menu count =====
        (function () {
            const n = document.querySelectorAll('#menuGrid [data-id]').length;
            const c = document.getElementById('menuCount');
            if (c) c.textContent = n + ' item tersedia';
        })();

        // ===== Search + Filter =====
        let activeCategory = '__all';

        function applyFilters() {
            const q = (document.getElementById('searchInput')?.value || '').trim().toLowerCase();
            const cards = Array.from(document.querySelectorAll('#menuGrid [data-id]'));

            let visible = 0;
            for (const card of cards) {
                const cat = (card.getAttribute('data-cat') || '').trim();
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const desc = (card.getAttribute('data-desc') || '').toLowerCase();

                const matchCat = activeCategory === '__all' || cat === activeCategory;
                const matchText = !q || name.includes(q) || desc.includes(q);

                const show = matchCat && matchText;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            }

            const meta = document.getElementById('searchMeta');
            if (meta) {
                if (!q && activeCategory === '__all') meta.textContent = 'Semua menu ditampilkan';
                else meta.textContent = `Menampilkan ${visible} item`;
            }

            const clearBtn = document.getElementById('clearSearch');
            if (clearBtn) clearBtn.classList.toggle('hidden', q.length === 0);
        }

        document.getElementById('filters')?.addEventListener('click', (e) => {
            const pill = e.target.closest('.pill');
            if (!pill) return;

            document.querySelectorAll('#filters .pill').forEach((p) => {
                p.classList.remove('active', 'border-yellow-300/30', 'bg-yellow-400/12', 'shadow-[0_0_0_6px_rgba(250,204,21,0.08)]');
                p.classList.add('border-white/12', 'bg-black/20');
            });

            pill.classList.add('active', 'border-yellow-300/30', 'bg-yellow-400/12', 'shadow-[0_0_0_6px_rgba(250,204,21,0.08)]');
            pill.classList.remove('border-white/12', 'bg-black/20');

            activeCategory = pill.getAttribute('data-cat') || '__all';
            applyFilters();
        });

        document.getElementById('searchInput')?.addEventListener('input', applyFilters);
        document.getElementById('clearSearch')?.addEventListener('click', () => {
            const input = document.getElementById('searchInput');
            if (input) input.value = '';
            applyFilters();
        });

        // ===== Storage Keys =====
        const CART_QTY_KEY = 'ayo_renne_cart_v1';
        const CART_OVERVIEW_KEY = 'ayo_renne_order_overview_v1';
        const TABLE_TOKEN_KEY = 'ayo_renne_table_token_v1';

        // ===== Capture table token =====
        (function captureTableTokenFromUrl() {
            const u = new URL(window.location.href);
            const token = (u.searchParams.get('table') || '').trim();

            if (token) {
                localStorage.setItem(TABLE_TOKEN_KEY, token);
                u.searchParams.delete('table');
                window.history.replaceState({}, '', u.toString());
            }
        })();

        function loadQtyCart() {
            let raw = {};
            try { raw = JSON.parse(localStorage.getItem(CART_QTY_KEY) || '{}') || {}; }
            catch { raw = {}; }

            const qtyCart = {};
            const overviewCart = {};
            let foundDetail = false;

            for (const [id, v] of Object.entries(raw)) {
                if (typeof v === 'number') {
                    qtyCart[id] = v;
                } else if (v && typeof v === 'object') {
                    foundDetail = true;
                    qtyCart[id] = Number(v.qty) || 0;
                    overviewCart[id] = {
                        name: String(v.name || ''),
                        price: Number(v.price) || 0,
                        qty: Number(v.qty) || 0,
                    };
                }
            }

            if (foundDetail) {
                localStorage.setItem(CART_QTY_KEY, JSON.stringify(qtyCart));
                localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify(overviewCart));
            }

            return qtyCart;
        }

        function saveQtyCart(cart) {
            localStorage.setItem(CART_QTY_KEY, JSON.stringify(cart));
        }

        function cartItemCount(cart) {
            return Object.values(cart).reduce((a, b) => a + (Number(b) || 0), 0);
        }

        function updateCartBadge() {
            const count = cartItemCount(loadQtyCart());
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = String(count);

            const mobileTotal = document.getElementById('mobileCartTotal');
            if (mobileTotal) {
                const totals = computeTotals(loadQtyCart());
                mobileTotal.textContent = formatRp(totals.total);
            }

            const fab = document.getElementById('fabCart');
            if (fab) {
                if (count > 0) {
                    fab.classList.remove('translate-y-32', 'opacity-0');
                    fab.classList.add('translate-y-0', 'opacity-100');
                } else {
                    fab.classList.add('translate-y-32', 'opacity-0');
                    fab.classList.remove('translate-y-0', 'opacity-100');
                }
            }
        }

        function formatRp(n) {
            n = Math.round(Number(n) || 0);
            return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function getProductData(id) {
            const el = document.querySelector(`#menuGrid [data-id="${id}"]`);
            if (!el) return null;
            return {
                id: Number(id),
                name: el.getAttribute('data-name'),
                price: Number(el.getAttribute('data-price') || 0),
                img: el.getAttribute('data-img') || '',
            };
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function computeTotals(qtyCart) {
            let subtotal = 0;
            for (const [id, qty] of Object.entries(qtyCart)) {
                const p = getProductData(id);
                if (!p) continue;
                subtotal += p.price * (Number(qty) || 0);
            }
            const tax = Math.round(subtotal * 0.10);
            const total = subtotal + tax;
            return { subtotal, tax, total };
        }

        function addToCart(id) {
            const p = getProductData(id);
            if (!p) return;

            const cart = loadQtyCart();
            cart[id] = (cart[id] || 0) + 1;
            saveQtyCart(cart);
            renderCart();
        }

        function setQty(id, qty) {
            const cart = loadQtyCart();
            qty = Number(qty) || 0;

            if (qty <= 0) delete cart[id];
            else cart[id] = qty;

            saveQtyCart(cart);
            renderCart();
        }

        function clearCart() {
            saveQtyCart({});
            localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify({}));
            renderCart();
        }

        // ===== Drawer =====
        function openCart() {
            const backdrop = document.getElementById('cartBackdrop');
            const drawer = document.getElementById('cartDrawer');
            if (!backdrop || !drawer) return;

            backdrop.classList.remove('hidden');
            setTimeout(() => backdrop.classList.add('opacity-100'), 10);
            drawer.classList.remove('translate-x-full');
            renderCart();
        }

        function closeCart() {
            const backdrop = document.getElementById('cartBackdrop');
            const drawer = document.getElementById('cartDrawer');
            if (!backdrop || !drawer) return;

            backdrop.classList.remove('opacity-100');
            drawer.classList.add('translate-x-full');
            setTimeout(() => backdrop.classList.add('hidden'), 300);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeCart();
                closeProductModal();
            }
        });

        function renderCart() {
            const cart = loadQtyCart();
            const list = document.getElementById('cartList');
            if (!list) return;

            list.innerHTML = '';
            const entries = Object.entries(cart);

            if (entries.length === 0) {
                list.innerHTML = `
                    <div class="glass flex flex-col items-center justify-center rounded-[32px] p-12 text-center">
                        <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-yellow-400/10 text-yellow-400">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-white uppercase tracking-tight">Your cart is empty</h3>
                        <p class="text-xs font-medium text-white/30 uppercase tracking-widest">Select items to start ordering</p>
                    </div>
                `;
            } else {
                for (const [id, qty] of entries) {
                    const p = getProductData(id);
                    if (!p) continue;

                    const item = document.createElement('div');
                    item.className = 'glass group relative flex items-center gap-4 rounded-[24px] p-3 transition-all hover:bg-white/[0.05]';
                    item.innerHTML = `
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-white/5">
                            <img src="${escapeHtml(p.img || '')}" class="h-full w-full object-cover opacity-60" 
                                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22white%22 stroke-opacity=%220.1%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22black%22/><path d=%22M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z%22/></svg>'">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-[13px] font-black uppercase tracking-tight text-white">${escapeHtml(p.name)}</div>
                            <div class="mt-1 text-[10px] font-black text-yellow-400/60 uppercase tracking-widest">${formatRp(p.price)}</div>
                        </div>
                        <div class="flex items-center gap-3 pr-2">
                            <button onclick="setQty(${p.id}, ${Number(qty) - 1})" class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/5 text-white/40 transition-all hover:bg-white/10 hover:text-white active:scale-90">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                            </button>
                            <span class="min-w-[20px] text-center text-xs font-black text-white">${qty}</span>
                            <button onclick="setQty(${p.id}, ${Number(qty) + 1})" class="flex h-8 w-8 items-center justify-center rounded-xl bg-yellow-400 text-black transition-all hover:bg-yellow-300 active:scale-90">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                    `;
                    list.appendChild(item);
                }
            }

            const t = computeTotals(cart);
            document.getElementById('subtotal').textContent = formatRp(t.subtotal);
            document.getElementById('tax').textContent = formatRp(t.tax);
            document.getElementById('total').textContent = formatRp(t.total);

            updateCartBadge();
        }

        function goToOverview() {
            const qtyCart = loadQtyCart();
            const entries = Object.entries(qtyCart);
            if (entries.length === 0) {
                alert('Keranjang masih kosong.');
                return;
            }

            const overview = {};
            for (const [id, qty] of entries) {
                const p = getProductData(id);
                if (!p) continue;
                overview[id] = {
                    name: p.name,
                    price: p.price,
                    qty: Number(qty) || 0
                };
            }

            localStorage.setItem(CART_OVERVIEW_KEY, JSON.stringify(overview));

            const token = (localStorage.getItem(TABLE_TOKEN_KEY) || '').trim();
            const url = token
                ? ('/order/overview?table=' + encodeURIComponent(token))
                : '/order/overview';

            window.location.href = url;
        }

        // ===== Product Modal =====
        function openProductModal(id) {
            const el = document.querySelector(`#menuGrid [data-id="${id}"]`);
            if (!el) return;

            const name = el.getAttribute('data-name') || '';
            const cat = el.getAttribute('data-cat') || '';
            const price = Number(el.getAttribute('data-price') || 0);
            const desc = el.getAttribute('data-desc') || '';
            const img = el.getAttribute('data-img') || '';

            document.getElementById('pmName').textContent = name;
            document.getElementById('pmCategory').textContent = cat;
            document.getElementById('pmPrice').textContent = formatRp(price);
            document.getElementById('pmDesc').textContent = desc.trim() ? desc : 'Discover our signature taste prepared with passion.';

            const imgEl = document.getElementById('pmImage');
            const noImg = document.getElementById('pmNoImage');

            if (img) {
                imgEl.src = img;
                imgEl.classList.remove('hidden');
                noImg.classList.add('hidden');
            } else {
                imgEl.classList.add('hidden');
                noImg.classList.remove('hidden');
            }

            const pmBtn = document.getElementById('pmAddBtn');
            const sellable = !el.querySelector('button[disabled]');
            pmBtn.disabled = !sellable;
            pmBtn.onclick = () => { addToCart(id); closeProductModal(); };

            const backdrop = document.getElementById('productBackdrop');
            const modal = document.getElementById('productModal');
            
            backdrop.classList.remove('hidden');
            setTimeout(() => backdrop.classList.add('opacity-100'), 10);
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('scale-95', 'opacity-0');
                modal.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeProductModal() {
            const backdrop = document.getElementById('productBackdrop');
            const modal = document.getElementById('productModal');
            
            backdrop.classList.remove('opacity-100');
            modal.classList.remove('scale-100', 'opacity-100');
            modal.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                backdrop.classList.add('hidden');
                modal.classList.add('hidden');
            }, 300);
        }

        // init
        applyFilters();
        renderCart();
    </script>
</body>

</html>