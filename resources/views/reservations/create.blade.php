<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservasi Premium — Ayo Renne</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --gold-primary: #eab308;
            --gold-light: #fef08a;
            --gold-dark: #a16207;
            --obsidian-950: #020617;
            --obsidian-900: #0f172a;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--obsidian-950);
            color: white;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        .font-luxury { font-family: 'Playfair Display', serif; }

        .glass-panel {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(234, 179, 8, 0.1);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            background: rgba(234, 179, 8, 0.02);
            border-color: rgba(234, 179, 8, 0.2);
            transform: translateY(-2px);
        }

        .glass-card-active {
            background: rgba(234, 179, 8, 0.05) !important;
            border-color: var(--gold-primary) !important;
            box-shadow: 0 0 25px rgba(234, 179, 8, 0.15);
        }

        .gold-gradient-text {
            background: linear-gradient(to right, var(--gold-light), var(--gold-primary), var(--gold-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-luxury {
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark));
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-luxury:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -10px rgba(234, 179, 8, 0.3);
        }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        input:focus, select:focus, textarea:focus {
            border-color: rgba(234, 179, 8, 0.4) !important;
            background: rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 0 0 4px rgba(234, 179, 8, 0.05);
        }

        /* Step transition animations */
        .step-content {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-enter {
            opacity: 0;
            transform: translateY(20px);
        }

        .step-active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="min-h-screen relative overflow-x-hidden pb-12">
    {{-- Decorative Background Gradients --}}
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_50%_-20%,rgba(234,179,8,0.12),transparent_70%)]"></div>
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_0%_100%,rgba(234,179,8,0.06),transparent_50%)]"></div>
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_100%_100%,rgba(255,255,255,0.02),transparent_50%)]"></div>

    <div class="mx-auto max-w-6xl px-4 pt-8 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <header class="mb-12 flex items-center justify-between gap-6">
            <div>
                <div class="mb-2 text-[10px] font-black uppercase tracking-[0.3em] text-yellow-400">Exclusive Dining Journey</div>
                <h1 class="font-luxury text-4xl font-bold text-white tracking-tight sm:text-5xl">Booking <span class="gold-gradient-text italic font-medium">Studio</span></h1>
                <p class="mt-2 text-xs font-semibold text-white/30 uppercase tracking-[0.2em] leading-relaxed">Securing your masterpiece moment at Ayo Renne</p>
            </div>

            <a href="/" class="glass-card flex h-12 w-12 items-center justify-center rounded-2xl text-white/40 hover:text-white hover:border-white/20 hover:scale-105 transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        </header>

        {{-- Error Alerts --}}
        @if ($errors->any())
            <div class="mb-8 rounded-3xl border border-red-500/20 bg-red-500/10 px-6 py-4 text-sm text-red-100 flex items-center gap-4 animate-fade-in backdrop-blur-md">
                <div class="h-10 w-10 shrink-0 rounded-2xl bg-red-500/20 flex items-center justify-center text-red-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <span class="font-bold uppercase tracking-wider text-xs block text-red-400 mb-0.5">Ada masalah pada formulir</span>
                    <span class="text-xs font-medium text-white/70">{{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        {{-- Multi-step Wizard Checklist Indicators --}}
        <nav class="mb-12 glass-panel rounded-[2rem] p-4 sm:p-6 overflow-hidden">
            <div class="flex items-center justify-between max-w-4xl mx-auto relative">
                <!-- Step 1 Link -->
                <button type="button" onclick="goToStep(1)" class="flex flex-col items-center gap-2 group focus:outline-none z-10">
                    <div id="step-indicator-1" class="h-10 w-10 rounded-full border border-yellow-400 bg-yellow-400/20 text-yellow-400 flex items-center justify-center font-bold text-sm shadow-[0_0_15px_rgba(234,179,8,0.3)] transition-all">01</div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white">Space</span>
                </button>
                <div class="flex-1 h-[2px] bg-white/10 mx-2 relative -mt-5">
                    <div id="step-progress-1" class="absolute top-0 left-0 h-full bg-gradient-to-r from-yellow-400 to-yellow-600 w-0 transition-all duration-500"></div>
                </div>

                <!-- Step 2 Link -->
                <button type="button" onclick="goToStep(2)" class="flex flex-col items-center gap-2 group focus:outline-none z-10">
                    <div id="step-indicator-2" class="h-10 w-10 rounded-full border border-white/10 bg-white/5 text-white/40 flex items-center justify-center font-bold text-sm transition-all">02</div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30">Schedule</span>
                </button>
                <div class="flex-1 h-[2px] bg-white/10 mx-2 relative -mt-5">
                    <div id="step-progress-2" class="absolute top-0 left-0 h-full bg-gradient-to-r from-yellow-400 to-yellow-600 w-0 transition-all duration-500"></div>
                </div>

                <!-- Step 3 Link -->
                <button type="button" onclick="goToStep(3)" class="flex flex-col items-center gap-2 group focus:outline-none z-10">
                    <div id="step-indicator-3" class="h-10 w-10 rounded-full border border-white/10 bg-white/5 text-white/40 flex items-center justify-center font-bold text-sm transition-all">03</div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30">Flavors</span>
                </button>
                <div class="flex-1 h-[2px] bg-white/10 mx-2 relative -mt-5">
                    <div id="step-progress-3" class="absolute top-0 left-0 h-full bg-gradient-to-r from-yellow-400 to-yellow-600 w-0 transition-all duration-500"></div>
                </div>

                <!-- Step 4 Link -->
                <button type="button" onclick="goToStep(4)" class="flex flex-col items-center gap-2 group focus:outline-none z-10">
                    <div id="step-indicator-4" class="h-10 w-10 rounded-full border border-white/10 bg-white/5 text-white/40 flex items-center justify-center font-bold text-sm transition-all">04</div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30">Review</span>
                </button>
            </div>
        </nav>

        {{-- Form Wizard --}}
        <form method="POST" action="{{ route('public.reservations.store') }}" id="reservationForm">
            @csrf

            <!-- Hidden Inputs for Schedule -->
            <input type="hidden" name="start_date" id="startDateInput">
            <input type="hidden" name="start_time" id="startTimeInput">
            <input type="hidden" name="end_date" id="endDateInput">
            <input type="hidden" name="end_time" id="endTimeInput">
            <div id="itemsHidden"></div>

            {{-- STEP 1: Select Space & Customer --}}
            <div id="step-1" class="step-content">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Space Cards Selector -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="glass-panel rounded-[2.5rem] p-8">
                            <div class="flex items-center justify-between mb-8">
                                <div>
                                    <h2 class="text-xl font-bold tracking-tight">Select Space</h2>
                                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-white/30">Reserve your perfect location</p>
                                </div>
                                <span class="rounded-full bg-yellow-400/10 px-4 py-1.5 text-[9px] font-black uppercase tracking-widest text-yellow-400 border border-yellow-400/20">Instant booking</span>
                            </div>

                            @if($resources->isEmpty())
                                <div class="rounded-2xl border border-yellow-400/20 bg-yellow-400/5 p-6 text-center">
                                    <p class="text-sm font-bold uppercase tracking-widest text-yellow-400">No Booking Spaces Available</p>
                                    <p class="mt-2 text-xs font-medium text-white/40 leading-relaxed">Please contact restaurant management to initialize dining resources.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($resources as $rs)
                                        <div onclick="selectResource({{ $rs->id }})" id="resource-card-{{ $rs->id }}" 
                                            class="glass-card rounded-3xl p-6 cursor-pointer relative overflow-hidden group">
                                            <div class="flex justify-between items-start mb-4">
                                                <div>
                                                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-yellow-400/60 block mb-1">{{ $rs->type }}</span>
                                                    <h3 class="text-base font-extrabold text-white tracking-tight leading-tight">{{ $rs->name }}</h3>
                                                </div>
                                                <div class="h-6 w-6 rounded-full border border-white/10 flex items-center justify-center group-hover:border-yellow-400/40 transition-all select-indicator">
                                                    <div class="h-3 w-3 rounded-full bg-yellow-400 scale-0 transition-transform duration-300 pointer-events-none"></div>
                                                </div>
                                            </div>
                                            <div class="mt-6 space-y-2 text-[11px] font-medium text-white/50 border-t border-white/5 pt-4">
                                                <div class="flex justify-between">
                                                    <span>Capacity</span>
                                                    <span class="font-bold text-white">{{ $rs->capacity }} People</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Min Duration</span>
                                                    <span class="font-bold text-white">{{ $rs->min_duration_minutes }} Mins</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Hourly / Flat</span>
                                                    <span class="font-bold text-yellow-400">
                                                        @if($rs->flat_rate > 0)
                                                            Rp {{ number_format($rs->flat_rate, 0, ',', '.') }} (Flat)
                                                        @else
                                                            Rp {{ number_format($rs->hourly_rate, 0, ',', '.') }}/Hr
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <select id="resourceSelect" name="reservation_resource_id" class="hidden">
                                    @foreach($resources as $rs)
                                        <option value="{{ $rs->id }}" data-type="{{ $rs->type }}" data-name="{{ $rs->name }}"
                                            data-capacity="{{ (int) $rs->capacity }}" data-min="{{ (int) $rs->min_duration_minutes }}"
                                            data-buffer="{{ (int) ($rs->buffer_minutes ?? 0) }}" data-hourly="{{ (int) ($rs->hourly_rate ?? 0) }}"
                                            data-flat="{{ (int) ($rs->flat_rate ?? 0) }}">
                                            [{{ $rs->type }}] {{ $rs->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="space-y-6">
                        <div class="glass-panel rounded-[2.5rem] p-8">
                            <h2 class="text-xl font-bold tracking-tight mb-6">Contact Info</h2>
                            <div class="space-y-5">
                                <div>
                                    <label class="mb-2 block text-[9px] font-black uppercase tracking-[0.25em] text-white/30 ml-1">Your Name</label>
                                    <input name="customer_name" id="customerNameInput" type="text" value="{{ old('customer_name') }}" placeholder="Enter full name"
                                        class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white transition-all outline-none" required />
                                </div>
                                <div>
                                    <label class="mb-2 block text-[9px] font-black uppercase tracking-[0.25em] text-white/30 ml-1">Phone Number</label>
                                    <input name="customer_phone" id="customerPhoneInput" type="text" value="{{ old('customer_phone') }}" placeholder="+62 ..."
                                        class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white transition-all outline-none" />
                                </div>
                            </div>
                        </div>

                        <!-- Perks Info -->
                        <div class="glass-card rounded-[2.5rem] p-8 border-dashed relative overflow-hidden group">
                            <div class="relative z-10 flex gap-4 items-start">
                                <div class="h-10 w-10 shrink-0 rounded-2xl bg-yellow-400/10 flex items-center justify-center text-yellow-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.952 11.952 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black uppercase tracking-widest text-yellow-400 mb-1">Elite Privileges</h4>
                                    <p class="text-[10px] font-medium text-white/40 leading-relaxed italic">Your reserved space is held for a grace period of 15 minutes past the scheduled arrival time.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Booking Schedule (Integrated Calendar & Slots) --}}
            <div id="step-2" class="step-content hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Calendar Panel -->
                    <div class="lg:col-span-2 glass-panel rounded-[2.5rem] p-8">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-xl font-bold tracking-tight">Select Date</h2>
                                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-white/30">Choose arrival day</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="calPrev" class="h-10 w-10 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-white/60 hover:text-white transition-all">←</button>
                                <div id="calTitle" class="text-xs font-black uppercase tracking-widest text-yellow-400 px-3">Month Year</div>
                                <button type="button" id="calNext" class="h-10 w-10 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-white/60 hover:text-white transition-all">→</button>
                            </div>
                        </div>

                        <!-- Days of Week -->
                        <div class="grid grid-cols-7 gap-2 mb-4">
                            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                <div class="text-center text-[9px] font-black uppercase tracking-widest text-white/30">{{ $day }}</div>
                            @endforeach
                        </div>

                        <!-- Days Grid -->
                        <div id="calGrid" class="grid grid-cols-7 gap-2"></div>
                    </div>

                    <!-- Slot Grid & Duration -->
                    <div class="space-y-6">
                        <div class="glass-panel rounded-[2.5rem] p-8">
                            <h3 class="text-base font-bold tracking-tight mb-2">Available Slots</h3>
                            <p id="slotMeta" class="text-[9px] font-black uppercase tracking-widest text-white/20 italic mb-6">Select a date to unlock slots</p>

                            <div class="max-h-[220px] overflow-y-auto pr-1 hide-scrollbar">
                                <div id="slotGrid" class="grid grid-cols-3 gap-2"></div>
                            </div>

                            <div class="mt-8 pt-6 border-t border-white/5">
                                <label class="mb-2 block text-[9px] font-black uppercase tracking-widest text-white/30 ml-1">Length of Stay</label>
                                <select id="durationSelect"
                                    class="w-full appearance-none rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
                                    <option value="" class="bg-black">Select time slot first</option>
                                </select>
                            </div>
                        </div>

                        <!-- Schedule Highlights -->
                        <div class="glass-card rounded-[2.5rem] p-8 space-y-4">
                            <div class="flex justify-between items-center text-[11px] font-semibold text-white/40 uppercase tracking-widest">
                                <span>Arrival</span>
                                <span id="summaryStart" class="font-extrabold text-white">-</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] font-semibold text-white/40 uppercase tracking-widest">
                                <span>Departure</span>
                                <span id="summaryEnd" class="font-extrabold text-white">-</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] font-semibold text-white/40 uppercase tracking-widest pt-4 border-t border-white/5">
                                <span>Estimated Space Fee</span>
                                <span id="summaryRental" class="text-sm font-black text-yellow-400">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Flavors & Menu Selection --}}
            <div id="step-3" class="step-content hidden">
                <!-- Buffet Selection -->
                <div class="glass-panel rounded-[2.5rem] p-8 mb-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-bold tracking-tight">Buffet Banquet Packages</h2>
                            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-white/30">Exquisite buffet options for group catering</p>
                        </div>
                        <span class="rounded-full bg-white/5 px-4 py-1.5 text-[9px] font-black uppercase tracking-widest text-white/40 border border-white/5">Banquet</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($buffetPackages as $bp)
                            <div class="relative group buffet-card" data-id="{{ $bp->id }}">
                                <input type="checkbox" name="buffet_package_ids[]" id="bp_chk_{{ $bp->id }}" value="{{ $bp->id }}" data-price="{{ (int) $bp->price }}"
                                    data-pricing-type="{{ $bp->pricing_type }}" data-min-pax="{{ (int) ($bp->min_pax ?? 0) }}"
                                    class="peer hidden buffet-checkbox">
                                
                                <div class="h-full rounded-3xl border border-white/5 bg-white/[0.01] p-6 transition-all peer-checked:border-yellow-400 peer-checked:bg-yellow-400/5 group-hover:bg-white/[0.03] flex flex-col justify-between relative cursor-pointer card-content">
                                    <div>
                                        <div class="flex items-start justify-between gap-4 mb-3">
                                            <div>
                                                <h3 class="text-sm font-black uppercase tracking-tight text-white leading-tight">{{ $bp->name }}</h3>
                                                <div class="mt-1 flex items-center gap-2">
                                                    <span class="text-[9px] font-black text-yellow-400/60 uppercase tracking-widest">{{ $bp->pricing_type === 'per_pax' ? 'Per Pax' : 'Flat Event' }}</span>
                                                    <span class="h-1 w-1 rounded-full bg-white/10"></span>
                                                    <span class="text-[9px] font-black text-white/40 uppercase tracking-widest">Rp {{ number_format($bp->price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-white/10 peer-checked:border-yellow-400 transition-all select-indicator">
                                                <div class="h-2.5 w-2.5 rounded-full bg-yellow-400 scale-0 peer-checked:scale-100 transition-transform duration-300"></div>
                                            </div>
                                        </div>
                                        <p class="text-[10px] font-medium leading-relaxed text-white/40 italic">
                                            {{ $bp->notes ?: 'No description available for this package.' }}
                                        </p>
                                    </div>
                                    
                                    <div class="mt-4 pt-4 border-t border-white/5 flex flex-col gap-4 relative z-20">
                                        <!-- Pax / Qty input container, shown only when checked -->
                                        <div class="hidden items-center justify-between gap-2 buffet-qty-container bg-white/[0.02] border border-white/5 rounded-2xl px-4 py-3">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-white/40">
                                                {{ $bp->pricing_type === 'per_pax' ? 'Jumlah Pax' : 'Jumlah Paket' }}
                                            </span>
                                            <input type="number" name="buffet_packages[{{ $bp->id }}][pax]" 
                                                min="{{ max(1, (int) $bp->min_pax) }}" 
                                                value="{{ max(1, (int) $bp->min_pax) }}" 
                                                class="w-20 rounded-xl border border-white/10 bg-black/40 px-3 py-2 text-center text-xs font-bold text-white outline-none focus:border-yellow-400 transition-all buffet-qty-input"
                                                oninput="updateTotals();"
                                            >
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <button type="button" onclick="showBuffetDetails(event, {{ $bp->id }})" class="rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 px-4 py-2 text-[9px] font-black uppercase tracking-widest text-yellow-400 transition-all">
                                                Detail Paket
                                            </button>
                                            @if($bp->min_pax > 0)
                                                <span class="text-[9px] font-black text-white/20 uppercase tracking-widest">Min: {{ $bp->min_pax }} Pax</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 max-w-xs">
                        <label class="mb-2 block text-[9px] font-black uppercase tracking-[0.25em] text-white/30 ml-1">Number of Pax</label>
                        <input type="number" name="pax" min="1" value="{{ old('pax') }}" placeholder="Enter count of guests"
                            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white transition-all outline-none" />
                    </div>
                </div>

                <!-- Regular Menu Selection -->
                <div class="glass-panel rounded-[2.5rem] p-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                        <div>
                            <h2 class="text-xl font-bold tracking-tight">Regular A la Carte</h2>
                            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-white/30">Indulge in signature single plates</p>
                        </div>

                        <div class="flex gap-2">
                            <input id="menuSearch" placeholder="Search dish..."
                                class="rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-3 text-xs font-bold text-white outline-none transition-all placeholder:text-white/10 w-48">
                            <button type="button" id="menuSearchBtn"
                                class="rounded-2xl bg-white/5 border border-white/10 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-white hover:bg-white/10 transition-all">Search</button>
                        </div>
                    </div>

                    <p id="menuMeta" class="mb-4 text-[9px] font-black uppercase tracking-widest text-white/20 italic">Loading menu selections...</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" id="menuGrid"></div>

                    <div class="flex justify-between items-center border-t border-white/5 pt-6 gap-4">
                        <button type="button" id="loadMore"
                            class="rounded-xl border border-white/5 bg-white/5 px-6 py-3 text-[9px] font-black uppercase tracking-widest text-white/40 hover:text-white hover:bg-white/10 transition-all">
                            Load More
                        </button>
                        <span class="text-[9px] font-black uppercase tracking-widest text-white/20">Ayo Renne Kitchen Selection</span>
                    </div>
                </div>
            </div>

            {{-- STEP 4: Review, Details, & Checkout --}}
            <div id="step-4" class="step-content hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Receipt Breakdown -->
                    <div class="lg:col-span-2 glass-panel rounded-[2.5rem] p-8">
                        <h2 class="text-xl font-bold tracking-tight mb-8">Summary Receipt</h2>

                        <!-- Space selection summary -->
                        <div class="mb-6 pb-6 border-b border-white/5">
                            <div class="text-[9px] font-black uppercase tracking-[0.25em] text-yellow-400/60 mb-2">Reserved Space</div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-base font-bold" id="receiptSpaceName">-</h3>
                                    <p class="text-xs text-white/40 mt-1" id="receiptScheduleTime">-</p>
                                </div>
                                <span class="text-sm font-bold text-white" id="receiptRentalFee">Rp 0</span>
                            </div>
                        </div>

                        <!-- Selected items list -->
                        <div class="mb-6 pb-6 border-b border-white/5">
                            <div class="text-[9px] font-black uppercase tracking-[0.25em] text-yellow-400/60 mb-4">Buffet & A la Carte Orders</div>
                            <div id="selectedList" class="space-y-3"></div>
                        </div>

                        <!-- Special Instructions -->
                        <div>
                            <label class="mb-2 block text-[9px] font-black uppercase tracking-[0.25em] text-white/30 ml-1">Special Arrangements</label>
                            <textarea name="notes" rows="4" placeholder="Dietary restrictions, customized table decor preferences..."
                                class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-xs font-bold text-white transition-all outline-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Payment Calculations -->
                    <div class="space-y-6">
                        <div class="glass-panel rounded-[2.5rem] p-8">
                            <h2 class="text-xl font-bold tracking-tight mb-6">Payment Billing</h2>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-white/40">
                                    <span>Food & Drinks Total</span>
                                    <span id="sumMenu" class="text-white font-extrabold text-xs">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-white/40">
                                    <span>Rental Rates applied</span>
                                    <span id="sumRental" class="text-white font-extrabold text-xs">Rp 0</span>
                                </div>
                                
                                <div id="checkoutDetails" class="space-y-1.5 pt-3 border-t border-white/5 hidden text-[10px] font-semibold text-white/30"></div>

                                <div class="pt-6 border-t border-white/5 space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-black uppercase tracking-widest text-yellow-400">Total Estimation</span>
                                        <span id="sumGrand" class="text-xl font-black text-white tracking-tight">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-white/40">
                                        <span>Required Down Payment (50%)</span>
                                        <span id="sumDp" class="text-yellow-400 font-extrabold">Rp 0</span>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="group relative mt-6 w-full overflow-hidden rounded-2xl bg-yellow-400 py-5 text-xs font-black uppercase tracking-[0.2em] text-black transition-all hover:bg-yellow-300 active:scale-95 shadow-[0_20px_50px_rgba(250,204,21,0.2)]">
                                    <span class="relative z-10 flex items-center justify-center gap-2">
                                        SECURE BOOKING
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </span>
                                </button>
                                
                                <p id="submitHint" class="text-center text-[8px] font-black uppercase tracking-widest text-white/20 italic">
                                    Ensure all step details are verified before secure checkout.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Navigation Controls --}}
            <div class="flex justify-between items-center mt-8 pt-8 border-t border-white/5">
                <button type="button" id="prevBtn" onclick="prevStep()" class="hidden rounded-2xl bg-white/5 border border-white/10 px-8 py-4 text-[10px] font-black uppercase tracking-widest text-white/60 hover:text-white hover:bg-white/10 hover:scale-105 transition-all">
                    Back Step
                </button>
                <div class="flex-grow"></div>
                <button type="button" id="nextBtn" onclick="nextStep()" class="rounded-2xl bg-yellow-400 px-8 py-4 text-[10px] font-black uppercase tracking-widest text-black hover:bg-yellow-300 hover:scale-105 transition-all">
                    Next Step
                </button>
            </div>
        </form>
    </div>

    <script>
        // Core Config
        const fmtRp = (n) => 'Rp ' + (Math.round(n || 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        const DP_RATIO = {{ (float) config('reservations.dp_ratio', 0.5) }};

        // Form Validation & Wizard Logic
        let currentStep = 1;

        function updateStepWizard() {
            // Hide all step screens
            for (let i = 1; i <= 4; i++) {
                const el = document.getElementById(`step-${i}`);
                if (el) {
                    el.classList.add('hidden');
                    el.classList.remove('step-active');
                }
            }

            // Show active step
            const activeStepEl = document.getElementById(`step-${currentStep}`);
            if (activeStepEl) {
                activeStepEl.classList.remove('hidden');
                setTimeout(() => {
                    activeStepEl.classList.add('step-active');
                }, 50);
            }

            // Update bottom navigation buttons
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            if (currentStep === 1) {
                prevBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
            }

            if (currentStep === 4) {
                nextBtn.classList.add('hidden');
            } else {
                nextBtn.classList.remove('hidden');
            }

            // Update top progress & indicators
            for (let i = 1; i <= 4; i++) {
                const indicator = document.getElementById(`step-indicator-${i}`);
                const progress = document.getElementById(`step-progress-${i}`);
                
                if (i < currentStep) {
                    // Completed step
                    indicator.className = 'h-10 w-10 rounded-full border border-yellow-500 bg-yellow-500/10 text-yellow-400 flex items-center justify-center font-bold text-sm shadow-[0_0_10px_rgba(234,179,8,0.1)]';
                    indicator.innerHTML = '✓';
                    if (progress) progress.style.width = '100%';
                } else if (i === currentStep) {
                    // Active step
                    indicator.className = 'h-10 w-10 rounded-full border border-yellow-400 bg-yellow-400/20 text-yellow-400 flex items-center justify-center font-bold text-sm shadow-[0_0_15px_rgba(234,179,8,0.3)]';
                    indicator.innerHTML = `0${i}`;
                    if (progress) progress.style.width = '0%';
                } else {
                    // Future step
                    indicator.className = 'h-10 w-10 rounded-full border border-white/10 bg-white/5 text-white/40 flex items-center justify-center font-bold text-sm';
                    indicator.innerHTML = `0${i}`;
                    if (progress) progress.style.width = '0%';
                }
            }

            // Custom load menu if Step 3 is activated
            if (currentStep === 3) {
                openMenuSelection();
            }
        }

        function validateStep() {
            if (currentStep === 1) {
                const spaceId = document.getElementById('resourceSelect').value;
                const name = document.getElementById('customerNameInput').value.trim();
                
                if (!spaceId) {
                    alert('Please select a dining space to continue.');
                    return false;
                }
                if (!name) {
                    alert('Please enter your contact name.');
                    return false;
                }
            }
            if (currentStep === 2) {
                if (!selectedDate || !selectedStart || !selectedDuration) {
                    alert('Please finalize your schedule: Date, Start Time, and Duration must be defined.');
                    return false;
                }
            }
            return true;
        }

        function nextStep() {
            if (validateStep()) {
                currentStep++;
                updateStepWizard();
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateStepWizard();
            }
        }

        function goToStep(s) {
            // Allow stepping backward or jumping forward ONLY if validated
            if (s < currentStep) {
                currentStep = s;
                updateStepWizard();
            } else {
                while (currentStep < s) {
                    if (!validateStep()) break;
                    currentStep++;
                }
                updateStepWizard();
            }
        }

        // Custom Space Selector Card Binding
        function selectResource(id) {
            const selectEl = document.getElementById('resourceSelect');
            selectEl.value = id;
            
            // Remove active classes
            document.querySelectorAll('[id^="resource-card-"]').forEach(el => {
                el.classList.remove('glass-card-active');
                const check = el.querySelector('.select-indicator div');
                if (check) check.classList.remove('scale-100');
            });

            // Add active styling to clicked card
            const activeCard = document.getElementById(`resource-card-${id}`);
            if (activeCard) {
                activeCard.classList.add('glass-card-active');
                const check = activeCard.querySelector('.select-indicator div');
                if (check) check.classList.add('scale-100');
            }

            // Sync calendar and totals
            selectedDate = null;
            selectedStart = null;
            selectedDuration = null;
            availability = null;
            updateResourceMeta();
            updateTotals();
        }

        // DateTime Helpers
        function pad2(n) { return String(n).padStart(2, '0'); }
        function toISODate(d) { return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()); }
        function parseHHMM(hhmm) {
            const [h, m] = hhmm.split(':').map(Number);
            return h * 60 + m;
        }
        function fromMin(min) {
            const h = Math.floor(min / 60);
            const m = min % 60;
            return pad2(h) + ':' + pad2(m);
        }
        function isOverlap(startMin, endMin, booked) {
            return (booked || []).some(b => {
                const bs = parseHHMM(b.start);
                const be = parseHHMM(b.end);
                return startMin < be && endMin > bs;
            });
        }

        // Cart State & Products Selection
        const cart = new Map();

        function rebuildHiddenItems() {
            const wrap = document.getElementById('itemsHidden');
            wrap.innerHTML = '';
            let idx = 0;

            for (const it of cart.values()) {
                if (it.qty > 0) {
                    const a = document.createElement('input');
                    a.type = 'hidden';
                    a.name = `items[${idx}][product_id]`;
                    a.value = String(it.id);

                    const b = document.createElement('input');
                    b.type = 'hidden';
                    b.name = `items[${idx}][qty]`;
                    b.value = String(it.qty);

                    wrap.appendChild(a);
                    wrap.appendChild(b);
                    idx++;
                }
            }
        }

        function calcBuffetTotal() {
            let total = 0;
            const checked = document.querySelectorAll('.buffet-checkbox:checked');
            const paxInput = document.querySelector('input[name="pax"]');
            const globalPax = parseInt(paxInput?.value || '0', 10);

            checked.forEach(el => {
                const label = el.closest('.group');
                const qtyInput = label.querySelector('.buffet-qty-input');
                let pax = parseInt(qtyInput?.value || '0', 10);
                if (pax <= 0) {
                    pax = globalPax;
                }

                const price = parseInt(el.dataset.price || '0', 10);
                const pricingType = el.dataset.pricingType || '';
                if (pricingType === 'per_pax') {
                    total += price * Math.max(0, pax);
                } else {
                    total += price;
                }
            });

            return total;
        }

        function renderSelectedList() {
            const box = document.getElementById('selectedList');
            const items = [...cart.values()].filter(x => x.qty > 0);
            const buffetChecked = document.querySelectorAll('.buffet-checkbox:checked');

            if (items.length === 0 && buffetChecked.length === 0) {
                box.innerHTML = `
                    <div class="glass-card flex flex-col items-center justify-center rounded-3xl border-dashed py-8 text-center">
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/20 italic">No items or packages ordered yet</p>
                    </div>
                `;
                return;
            }

            box.innerHTML = '';

            // Render Buffet Packages if Selected
            buffetChecked.forEach(el => {
                const label = el.closest('.group');
                const name = label.querySelector('h3').textContent.trim();
                const price = parseInt(el.dataset.price || '0', 10);
                const pricingType = el.dataset.pricingType || '';
                const qtyInput = label.querySelector('.buffet-qty-input');
                const globalPaxInput = document.querySelector('input[name="pax"]');
                const globalPax = parseInt(globalPaxInput?.value || '0', 10);

                let pax = parseInt(qtyInput?.value || '0', 10);
                if (pax <= 0) {
                    pax = globalPax;
                }
                const total = pricingType === 'per_pax' ? price * Math.max(0, pax) : price;

                const row = document.createElement('div');
                row.className = 'glass-card flex items-center justify-between gap-4 rounded-2xl p-4';
                row.innerHTML = `
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-black uppercase tracking-tight text-white">${name} [Catering Package]</div>
                        <div class="mt-1 text-[9px] font-black text-yellow-400/60 uppercase tracking-widest">
                            ${pricingType === 'per_pax' ? `Per Pax • ${pax} guests` : 'Event Flat Rate'}
                        </div>
                    </div>
                    <span class="text-xs font-black text-white">${fmtRp(total)}</span>
                `;
                box.appendChild(row);
            });

            // Render A la Carte Items
            items.forEach(it => {
                const row = document.createElement('div');
                row.className = 'glass-card flex items-center justify-between gap-4 rounded-2xl p-4';
                row.innerHTML = `
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-black uppercase tracking-tight text-white">${it.name}</div>
                        <div class="mt-1 text-[9px] font-black text-yellow-400/60 uppercase tracking-widest">Qty: ${it.qty} x ${fmtRp(it.price)}</div>
                    </div>
                    <span class="text-xs font-black text-white">${fmtRp(it.price * it.qty)}</span>
                `;
                box.appendChild(row);
            });
        }

        // Integrated Calendar & Slots Engine
        let calMonth = new Date();
        let selectedDate = null;
        let availability = null;
        let selectedStart = null;
        let selectedDuration = null;

        function updateResourceMeta() {
            const opt = document.querySelector('#resourceSelect option:checked');
            if (!opt) return;

            const type = opt.dataset.type || '-';
            const capacity = parseInt(opt.dataset.capacity || '0', 10);
            const min = parseInt(opt.dataset.min || '0', 10);
            const buffer = parseInt(opt.dataset.buffer || '0', 10);
            const hourly = parseInt(opt.dataset.hourly || '0', 10);
            const flat = parseInt(opt.dataset.flat || '0', 10);

            let rateText = '-';
            if (flat > 0 && hourly > 0) rateText = `${fmtRp(hourly)}/hr • Flat ${fmtRp(flat)}`;
            else if (flat > 0) rateText = `Flat ${fmtRp(flat)}`;
            else if (hourly > 0) rateText = `${fmtRp(hourly)}/hr`;

            [...document.querySelectorAll('#checkoutRateText')].forEach(el => el.textContent = rateText);
            
            // Sync Receipt details
            document.getElementById('receiptSpaceName').textContent = opt.dataset.name || '-';
            document.getElementById('receiptRentalFee').textContent = fmtRp(calcRental());
        }

        function calcRental() {
            const opt = document.querySelector('#resourceSelect option:checked');
            const hourly = parseInt(opt?.dataset.hourly || '0', 10);
            const flat = parseInt(opt?.dataset.flat || '0', 10);

            if (!selectedDuration) return 0;
            if (flat > 0) return flat;
            if (hourly > 0) return hourly * Math.ceil(selectedDuration / 60);
            return 0;
        }

        function updateScheduleSummary() {
            const rental = calcRental();
            document.getElementById('summaryRental').textContent = fmtRp(rental);
            document.getElementById('sumRental').textContent = fmtRp(rental);
            document.getElementById('receiptRentalFee').textContent = fmtRp(rental);

            if (selectedDate && selectedStart && selectedDuration) {
                const endHHMM = fromMin(parseHHMM(selectedStart) + selectedDuration);

                document.getElementById('summaryStart').textContent = `${selectedDate} ${selectedStart}`;
                document.getElementById('summaryEnd').textContent = `${selectedDate} ${endHHMM}`;
                document.getElementById('receiptScheduleTime').textContent = `${selectedDate} • Time: ${selectedStart} - ${endHHMM} (${selectedDuration} mins)`;

                document.getElementById('startDateInput').value = selectedDate;
                document.getElementById('startTimeInput').value = selectedStart;
                document.getElementById('endDateInput').value = selectedDate;
                document.getElementById('endTimeInput').value = endHHMM;
            } else {
                document.getElementById('summaryStart').textContent = '-';
                document.getElementById('summaryEnd').textContent = '-';
                document.getElementById('receiptScheduleTime').textContent = 'Please finalize date & time';
                
                document.getElementById('startDateInput').value = '';
                document.getElementById('startTimeInput').value = '';
                document.getElementById('endDateInput').value = '';
                document.getElementById('endTimeInput').value = '';
            }
        }

        function updateTotals() {
            let regularTotal = 0;
            for (const it of cart.values()) {
                regularTotal += (it.price || 0) * (it.qty || 0);
            }

            const buffetTotal = calcBuffetTotal();
            const menuTotal = regularTotal + buffetTotal;
            const rental = calcRental();
            const grand = menuTotal + rental;

            document.getElementById('sumMenu').textContent = fmtRp(menuTotal);
            document.getElementById('sumGrand').textContent = fmtRp(grand);
            document.getElementById('sumDp').textContent = fmtRp(grand * DP_RATIO);

            updateScheduleSummary();
            renderSelectedList();
            updateCheckoutDetails();
        }

        function updateCheckoutDetails() {
            const detailBox = document.getElementById('checkoutDetails');
            if (!detailBox) return;
            detailBox.innerHTML = '';

            let hasDetails = false;

            // Buffet billing
            const buffetChecked = document.querySelectorAll('.buffet-checkbox:checked');
            const globalPaxInput = document.querySelector('input[name="pax"]');
            const globalPax = parseInt(globalPaxInput?.value || '0', 10);

            buffetChecked.forEach(el => {
                const label = el.closest('.group');
                const name = label.querySelector('h3').textContent.trim();
                const price = parseInt(el.dataset.price || '0', 10);
                const pricingType = el.dataset.pricingType || '';
                const qtyInput = label.querySelector('.buffet-qty-input');
                let pax = parseInt(qtyInput?.value || '0', 10);
                if (pax <= 0) {
                    pax = globalPax;
                }
                const total = pricingType === 'per_pax' ? price * Math.max(0, pax) : price;

                const row = document.createElement('div');
                row.className = 'flex justify-between items-center';
                row.innerHTML = `<span>• ${name} (${pax} pax)</span><span>${fmtRp(total)}</span>`;
                detailBox.appendChild(row);
                hasDetails = true;
            });

            // Regular items billing
            for (const it of cart.values()) {
                if (it.qty > 0) {
                    const row = document.createElement('div');
                    row.className = 'flex justify-between items-center';
                    row.innerHTML = `<span>• ${it.name} (x${it.qty})</span><span>${fmtRp(it.price * it.qty)}</span>`;
                    detailBox.appendChild(row);
                    hasDetails = true;
                }
            }

            detailBox.classList.toggle('hidden', !hasDetails);
        }

        function renderCalendar() {
            const title = document.getElementById('calTitle');
            const grid = document.getElementById('calGrid');
            grid.innerHTML = '';

            const y = calMonth.getFullYear();
            const m = calMonth.getMonth();

            title.textContent = calMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

            const first = new Date(y, m, 1);
            const last = new Date(y, m + 1, 0);
            const firstDay = ((first.getDay() + 6) % 7);
            const daysInMonth = last.getDate();

            for (let i = 0; i < 42; i++) {
                const dayNum = i - firstDay + 1;
                const cell = document.createElement('button');
                cell.type = 'button';

                if (dayNum < 1 || dayNum > daysInMonth) {
                    cell.disabled = true;
                    cell.className = 'h-10 rounded-xl bg-white/[0.01] text-white/5 cursor-default';
                    cell.textContent = '';
                    grid.appendChild(cell);
                    continue;
                }

                const dateObj = new Date(y, m, dayNum);
                const iso = toISODate(dateObj);
                const isToday = iso === toISODate(new Date());

                cell.dataset.date = iso;
                cell.textContent = String(dayNum);
                cell.className = `h-10 rounded-xl text-xs font-bold transition-all ${isToday ? 'text-yellow-400 border border-yellow-400/20 bg-yellow-400/5' : 'text-white/40 hover:text-white hover:bg-white/5 border border-white/5'}`;

                if (selectedDate === iso) {
                    cell.className = 'h-10 rounded-xl text-xs font-black bg-yellow-400 text-black shadow-lg shadow-yellow-400/20';
                }

                cell.onclick = async () => {
                    selectedDate = iso;
                    selectedStart = null;
                    selectedDuration = null;
                    availability = null;

                    await loadAvailabilityForDate();
                    renderCalendar();
                    renderSlots();
                    renderDurations();
                    updateTotals();
                };

                grid.appendChild(cell);
            }
        }

        async function loadAvailabilityForDate() {
            const resourceId = document.getElementById('resourceSelect').value;
            if (!resourceId) return;

            const url = new URL("{{ route('public.reservations.availability') }}", window.location.origin);
            url.searchParams.set('reservation_resource_id', resourceId);
            url.searchParams.set('date', selectedDate);

            try {
                const res = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!res.ok) {
                    const errorData = await res.json().catch(() => ({ message: 'Server error' }));
                    console.error('Availability Error:', errorData);
                    return;
                }
                
                availability = await res.json();
            } catch (err) {
                console.error('Fetch Error:', err);
            }
        }

        function renderSlots() {
            const meta = document.getElementById('slotMeta');
            const grid = document.getElementById('slotGrid');
            grid.innerHTML = '';

            if (!availability) {
                meta.textContent = 'Select a date first';
                return;
            }

            meta.textContent = `${selectedDate} • Select available arrival time`;

            const open = parseHHMM(availability.open);
            const close = parseHHMM(availability.close);
            const minDur = parseInt(availability.min_duration_minutes || 60, 10);
            const step = Math.max(5, parseInt(availability.slot_minutes || 30, 10));
            const booked = availability.booked || [];

            // Calculate cutoff for today (current local time + 30 minutes buffer)
            let cutoffMin = -999999;
            const todayStr = toISODate(new Date());
            if (selectedDate === todayStr) {
                const now = new Date();
                cutoffMin = now.getHours() * 60 + now.getMinutes() + 30;
            }

            // Auto-reset selectedStart if it is now in the past / within preparation buffer
            if (selectedStart && selectedDate === todayStr) {
                const startMins = parseHHMM(selectedStart);
                if (startMins < cutoffMin) {
                    selectedStart = null;
                    selectedDuration = null;
                }
            }

            let hasAvailable = false;

            for (let t = open; t + minDur <= close; t += step) {
                const hhmm = fromMin(t);
                // Slot is disabled if it overlaps with bookings OR is in the past / within prep buffer
                const disabled = isOverlap(t, t + minDur, booked) || (t < cutoffMin);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = hhmm;
                btn.disabled = disabled;
                btn.className = `rounded-xl py-3 text-[10px] font-black uppercase tracking-widest transition-all ${disabled ? 'bg-white/[0.02] text-white/10 cursor-not-allowed opacity-40' : 'bg-white/5 text-white/60 hover:bg-white/10 hover:text-white border border-white/5'}`;

                if (selectedStart === hhmm) {
                    btn.className = 'rounded-xl py-3 text-[10px] font-black uppercase tracking-widest bg-yellow-400 text-black shadow-lg shadow-yellow-400/20';
                }

                btn.onclick = () => {
                    selectedStart = hhmm;
                    selectedDuration = null;
                    renderSlots();
                    renderDurations();
                    updateTotals();
                };

                if (!disabled) hasAvailable = true;
                grid.appendChild(btn);
            }

            if (!hasAvailable) {
                meta.textContent = `Fully booked on ${selectedDate}`;
            }
        }

        function renderDurations() {
            const sel = document.getElementById('durationSelect');
            sel.innerHTML = '';

            if (!availability || !selectedStart) {
                sel.disabled = true;
                sel.innerHTML = `<option value="" class="bg-black text-white/20">Select time slot first</option>`;
                return;
            }

            sel.disabled = false;

            const start = parseHHMM(selectedStart);
            const close = parseHHMM(availability.close);
            const minDur = parseInt(availability.min_duration_minutes || 60, 10);
            const step = Math.max(5, parseInt(availability.slot_minutes || 30, 10));
            const booked = availability.booked || [];

            let first = true;

            for (let dur = minDur; start + dur <= close; dur += step) {
                if (isOverlap(start, start + dur, booked)) break;

                const opt = document.createElement('option');
                opt.value = String(dur);
                const h = Math.floor(dur / 60);
                const m = dur % 60;
                opt.textContent = (h > 0 ? `${h} hour ` : '') + (m > 0 ? `${m} mins` : '');
                opt.className = "bg-black";

                if (first) {
                    opt.selected = true;
                    selectedDuration = dur;
                    first = false;
                }

                sel.appendChild(opt);
            }

            if (first) {
                sel.innerHTML = `<option value="" class="bg-black">No duration available</option>`;
                selectedDuration = null;
                sel.disabled = true;
                updateTotals();
                return;
            }

            sel.onchange = () => {
                selectedDuration = parseInt(sel.value || '0', 10) || null;
                updateTotals();
            };
        }

        // Calendar Month Swiping
        document.getElementById('calPrev').onclick = () => {
            calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() - 1, 1);
            renderCalendar();
        };

        document.getElementById('calNext').onclick = () => {
            calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() + 1, 1);
            renderCalendar();
        };

        // A la Carte Menu Grid Engine
        const menuGrid = document.getElementById('menuGrid');
        const menuMeta = document.getElementById('menuMeta');
        const loadMoreBtn = document.getElementById('loadMore');

        let page = 1;
        let lastPage = 1;
        let query = '';

        function openMenuSelection() {
            page = 1;
            lastPage = 1;
            query = '';
            document.getElementById('menuSearch').value = '';
            menuGrid.innerHTML = '';
            fetchMenuPage();
        }

        document.getElementById('menuSearchBtn').onclick = () => {
            query = document.getElementById('menuSearch').value.trim();
            page = 1;
            menuGrid.innerHTML = '';
            fetchMenuPage();
        };

        document.getElementById('menuSearch').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('menuSearchBtn').click();
            }
        });

        loadMoreBtn.onclick = () => {
            if (page < lastPage) {
                page++;
                fetchMenuPage();
            }
        };

        async function fetchMenuPage() {
            menuMeta.textContent = 'Discovering master chef selections...';

            const url = new URL("{{ route('public.reservations.products') }}", window.location.origin);
            url.searchParams.set('page', String(page));
            url.searchParams.set('per_page', '12');
            if (query) url.searchParams.set('q', query);

            try {
                const res = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!res.ok) {
                    menuMeta.textContent = 'Failed to load menu items.';
                    return;
                }
                
                const json = await res.json();

                lastPage = json.meta.last_page || 1;
                menuMeta.textContent = `Showing dishes ${json.meta.from ?? 0}-${json.meta.to ?? 0} of ${json.meta.total}`;
                loadMoreBtn.disabled = page >= lastPage;
                loadMoreBtn.classList.toggle('opacity-20', page >= lastPage);

                (json.data || []).forEach(p => {
                    const max = (p.max_available ?? 0);
                    const disabled = max <= 0;
                    const currentQty = cart.get(p.id)?.qty || 0;

                    if (!cart.has(p.id)) {
                        cart.set(p.id, { id: p.id, name: p.name, price: p.price, max: max, qty: 0 });
                    } else {
                        const it = cart.get(p.id);
                        it.max = max;
                        it.name = p.name;
                        it.price = p.price;
                        cart.set(p.id, it);
                    }

                    const card = document.createElement('div');
                    card.className = 'glass-card group overflow-hidden rounded-[28px] transition-all hover:bg-white/[0.04]';
                    card.innerHTML = `
                        <div class="relative h-40 overflow-hidden bg-white/5">
                            ${p.image_url
                                ? `<img src="${p.image_url}" class="h-full w-full object-cover opacity-60 group-hover:opacity-90 transition-all duration-500 scale-105 group-hover:scale-100">`
                                : `<div class="h-full w-full bg-gradient-to-br from-yellow-400/10 via-white/5 to-transparent"></div>`}
                            <div class="absolute top-4 right-4 rounded-full bg-black/60 backdrop-blur-md px-3 py-1 text-[10px] font-black text-yellow-400 border border-white/5">${fmtRp(p.price)}</div>
                        </div>
                        <div class="p-5">
                            <h4 class="text-xs font-black uppercase tracking-tight text-white line-clamp-1">${p.name}</h4>
                            <p class="mt-1 text-[8px] font-black text-white/20 uppercase tracking-widest">${p.category ?? 'Kitchen Selection'}</p>
                            <p class="mt-3 text-[10px] font-medium text-white/40 leading-relaxed line-clamp-2 italic">${p.description || 'Exclusive culinary creation designed for high sensory fulfillment.'}</p>
                            
                            <div class="mt-5 flex items-center justify-between gap-4 border-t border-white/5 pt-4">
                                <div class="text-[8px] font-black uppercase tracking-widest ${disabled ? 'text-red-400/50' : 'text-white/20'}">
                                    ${disabled ? 'Sold Out' : `Available: ${max}`}
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="m-minus flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-white/45 transition-all hover:bg-white/15 hover:text-white active:scale-90" data-id="${p.id}" ${disabled ? 'disabled' : ''}>
                                        -
                                    </button>
                                    <div class="min-w-[18px] text-center text-xs font-black text-white" id="q-${p.id}">${currentQty}</div>
                                    <button type="button" class="m-plus flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-400 text-black transition-all hover:bg-yellow-300 active:scale-90" data-id="${p.id}" ${disabled ? 'disabled' : ''}>
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    menuGrid.appendChild(card);
                });
            } catch (err) {
                console.error('Menu Load Error:', err);
            }
        }

        // Menu card click handlers
        menuGrid.onclick = (e) => {
            const plus = e.target.closest('.m-plus');
            const minus = e.target.closest('.m-minus');
            if (!plus && !minus) return;

            const id = parseInt((plus || minus).dataset.id, 10);
            const it = cart.get(id);
            if (!it) return;

            const max = (it.max ?? 999999);
            if (plus) it.qty = Math.min(max, it.qty + 1);
            if (minus) it.qty = Math.max(0, it.qty - 1);

            cart.set(id, it);
            
            const qEl = document.getElementById('q-' + id);
            if (qEl) qEl.textContent = String(it.qty);

            rebuildHiddenItems();
            updateTotals();
        };

        // Buffet Selection Card Clicks
        document.querySelectorAll('.buffet-card').forEach(card => {
            const checkbox = card.querySelector('.buffet-checkbox');
            const content = card.querySelector('.card-content');
            
            content.addEventListener('click', (e) => {
                if (e.target.closest('.buffet-qty-container') || e.target.closest('button') || e.target.closest('input')) {
                    return;
                }
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            });
        });

        // Buffet Selection Binding
        document.querySelectorAll('.buffet-checkbox').forEach(el => {
            el.addEventListener('change', () => {
                const group = el.closest('.group');
                const box = group.querySelector('.h-full');
                const indicator = box.querySelector('.select-indicator div');
                const qtyContainer = box.querySelector('.buffet-qty-container');
                if (el.checked) {
                    box.classList.add('border-yellow-400', 'bg-yellow-400/5');
                    box.classList.remove('border-white/5', 'bg-white/[0.01]');
                    if (indicator) indicator.classList.add('scale-100');
                    if (qtyContainer) {
                        qtyContainer.classList.remove('hidden');
                        qtyContainer.classList.add('flex');
                    }
                } else {
                    box.classList.remove('border-yellow-400', 'bg-yellow-400/5');
                    box.classList.add('border-white/5', 'bg-white/[0.01]');
                    if (indicator) indicator.classList.remove('scale-100');
                    if (qtyContainer) {
                        qtyContainer.classList.add('hidden');
                        qtyContainer.classList.remove('flex');
                    }
                }
                updateTotals();
            });
        });

        const paxInput = document.querySelector('input[name="pax"]');
        if (paxInput) {
            paxInput.addEventListener('input', updateTotals);
        }

        const buffetPackagesData = @json($buffetPackages);

        function showBuffetDetails(event, id) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }
            
            const pkg = buffetPackagesData.find(p => p.id === id);
            if (!pkg) return;

            document.getElementById('buffetDetailsTitle').textContent = pkg.name;
            const listEl = document.getElementById('buffetDetailsList');
            listEl.innerHTML = '';

            if (!pkg.items || pkg.items.length === 0) {
                listEl.innerHTML = `
                    <div class="glass-card flex flex-col items-center justify-center rounded-3xl border-dashed py-8 text-center">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 italic">No items included in this package</p>
                    </div>
                `;
            } else {
                pkg.items.forEach(item => {
                    const productName = item.product ? item.product.name : 'Unknown Dish';
                    const productCat = item.product ? item.product.category : 'Banquet Dish';
                    
                    const card = document.createElement('div');
                    card.className = 'glass-card flex items-center justify-between gap-4 rounded-2xl p-4';
                    card.innerHTML = `
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-black uppercase tracking-tight text-white leading-tight">${productName}</h4>
                            <p class="mt-1 text-[8px] font-black text-yellow-400/60 uppercase tracking-widest">
                                Category: ${productCat} ${item.note ? `• ${item.note}` : ''}
                            </p>
                        </div>
                        <div class="rounded-full bg-white/5 border border-white/10 px-3 py-1 text-[9px] font-black text-white shrink-0">
                            x${item.qty} Qty
                        </div>
                    `;
                    listEl.appendChild(card);
                });
            }

            const modal = document.getElementById('buffetDetailsModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.transform').classList.remove('scale-95');
            }, 50);
        }

        function closeBuffetDetails() {
            const modal = document.getElementById('buffetDetailsModal');
            modal.classList.add('opacity-0');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Select first dining table card by default if exists
            const firstCard = document.querySelector('[id^="resource-card-"]');
            if (firstCard) {
                firstCard.click();
            }

            renderCalendar();
            updateStepWizard();
        });
    </script>

    {{-- Buffet Package Details Modal --}}
    <div id="buffetDetailsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/85 backdrop-blur-md transition-all duration-300 opacity-0">
        <div class="glass-panel w-full max-w-lg rounded-[2.5rem] p-8 border border-yellow-400/20 transform scale-95 transition-all duration-300 relative">
            <!-- Close Button -->
            <button type="button" onclick="closeBuffetDetails()" class="absolute top-6 right-6 h-10 w-10 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-white/60 hover:text-white border border-white/15 transition-all">
                ✕
            </button>

            <span class="text-[9px] font-black uppercase tracking-[0.25em] text-yellow-400 mb-1 block">Inclusions & Specialties</span>
            <h2 id="buffetDetailsTitle" class="font-luxury text-2xl font-bold text-white tracking-tight mb-6">Package Details</h2>

            <div class="max-h-[350px] overflow-y-auto pr-1 hide-scrollbar space-y-3 mb-8" id="buffetDetailsList">
                <!-- Dynamic items rendered by JS -->
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeBuffetDetails()" class="rounded-2xl bg-yellow-400 hover:bg-yellow-300 px-6 py-3.5 text-[10px] font-black uppercase tracking-widest text-black transition-all">
                    Close Details
                </button>
            </div>
        </div>
    </div>
</body>
</html>