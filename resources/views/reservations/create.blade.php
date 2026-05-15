<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservasi — Ayo Renne</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
        .stagger-4 { animation-delay: 0.4s; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        input:focus, select:focus, textarea:focus {
            border-color: rgba(251, 191, 36, 0.5) !important;
            background: rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.05);
        }

        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white' stroke-opacity='0.2' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.5rem center;
            background-size: 1rem;
        }
    </style>
</head>

<body class="min-h-screen relative overflow-x-hidden">
    {{-- Background Glow --}}
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_50%_-20%,rgba(251,191,36,0.1),transparent_70%)]"></div>
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_0%_100%,rgba(251,191,36,0.05),transparent_50%)]"></div>

  <div class="mx-auto max-w-7xl px-4 pb-24 pt-6 sm:px-5 lg:px-6">
    <header class="mb-12 flex items-center justify-between gap-6 animate-reveal">
        <div>
            <div class="mb-1 text-[10px] font-black uppercase tracking-[0.2em] text-yellow-400">Reservation Experience</div>
            <h1 class="font-heading text-3xl font-black text-white sm:text-4xl">Booking <span class="premium-gradient-text">Studio</span></h1>
            <p class="mt-2 text-xs font-medium text-white/40 uppercase tracking-widest leading-relaxed">Select your space, schedule your time, and enjoy the flavors</p>
        </div>

        <a href="/" class="glass flex h-12 w-12 items-center justify-center rounded-2xl text-white/40 hover:text-white hover:border-white/20 transition-all">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
    </header>

    @if ($errors->any())
      <div
        class="mb-5 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-100 whitespace-pre-line">
        ❌ {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('public.reservations.store') }}" id="reservationForm"
      class="grid grid-cols-1 gap-6 2xl:grid-cols-[1fr_.78fr]">
      @csrf

      {{-- LEFT --}}
      <section class="space-y-6 animate-reveal stagger-1">
        {{-- RESOURCE + CUSTOMER --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.4fr_1fr]">
          <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl">
            <div class="flex flex-wrap items-start justify-between gap-6">
              <div>
                <h2 class="text-lg font-bold text-white tracking-tight">Select Space</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Hall / Room / Table</p>
              </div>

              <div class="rounded-full bg-yellow-400/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-yellow-400 border border-yellow-400/20">
                Instant Booking
              </div>
            </div>

            <div class="mt-8">
                <div class="relative">
                    @if($resources->isEmpty())
                        <div class="rounded-2xl border border-yellow-400/20 bg-yellow-400/5 p-4 text-center">
                            <p class="text-xs font-black uppercase tracking-widest text-yellow-400">No Booking Spaces Available</p>
                            <p class="mt-1 text-[10px] font-medium text-white/40">Please contact admin to set up reservation resources.</p>
                        </div>
                    @else
                        <select id="resourceSelect" name="reservation_resource_id"
                            class="custom-select w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white outline-none transition-all">
                            @foreach($resources as $rs)
                            <option value="{{ $rs->id }}" data-type="{{ $rs->type }}" data-name="{{ $rs->name }}"
                                data-capacity="{{ (int) $rs->capacity }}" data-min="{{ (int) $rs->min_duration_minutes }}"
                                data-buffer="{{ (int) ($rs->buffer_minutes ?? 0) }}" data-hourly="{{ (int) ($rs->hourly_rate ?? 0) }}"
                                data-flat="{{ (int) ($rs->flat_rate ?? 0) }}" class="bg-black">
                                [{{ $rs->type }}] {{ $rs->name }} (cap {{ $rs->capacity }})
                            </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div class="glass-gold rounded-3xl p-6 border-dashed">
                <div class="text-[10px] font-black uppercase tracking-[0.18em] text-yellow-400/40">Space Info</div>
                <div class="mt-5 space-y-4">
                  <div class="flex items-center justify-between border-b border-white/5 pb-3">
                    <span class="text-[11px] font-bold text-white/30 uppercase tracking-widest">Type</span>
                    <span id="resourceTypeText" class="text-xs font-black text-white">-</span>
                  </div>
                  <div class="flex items-center justify-between border-b border-white/5 pb-3">
                    <span class="text-[11px] font-bold text-white/30 uppercase tracking-widest">Capacity</span>
                    <span id="resourceCapacityText" class="text-xs font-black text-white">-</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-white/30 uppercase tracking-widest">Min. Stay</span>
                    <span id="resourceMinText" class="text-xs font-black text-white">-</span>
                  </div>
                </div>
              </div>

              <div class="glass rounded-3xl p-6">
                <div class="text-[10px] font-black uppercase tracking-[0.18em] text-white/20">Rates</div>
                <div class="mt-5 space-y-4">
                  <div class="flex items-center justify-between border-b border-white/5 pb-3">
                    <span class="text-[11px] font-bold text-white/30 uppercase tracking-widest">Hourly</span>
                    <span id="resourceHourlyText" class="text-xs font-black text-yellow-400">-</span>
                  </div>
                  <div class="flex items-center justify-between border-b border-white/5 pb-3">
                    <span class="text-[11px] font-bold text-white/30 uppercase tracking-widest">Flat Rate</span>
                    <span id="resourceFlatText" class="text-xs font-black text-white">-</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-white/30 uppercase tracking-widest">Buffer</span>
                    <span id="resourceBufferText" class="text-xs font-black text-white">-</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white/[0.02] px-6 py-4 border border-white/5">
                <p class="text-[10px] font-medium leading-relaxed text-white/40 italic">
                    * Active rate: <span id="checkoutRateText" class="font-black text-white/80">-</span>.
                    Flat rates take priority over hourly rates where applicable.
                </p>
            </div>
          </div>

          <div class="space-y-6">
            <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl">
              <h2 class="mb-6 text-lg font-bold text-white tracking-tight">Customer Information</h2>
              <div class="space-y-6">
                <div>
                  <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Your Name</label>
                  <input name="customer_name" type="text" value="{{ old('customer_name') }}" placeholder="Enter your full name"
                    class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white transition-all outline-none" required />
                </div>

                <div>
                  <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Phone Number</label>
                  <input name="customer_phone" type="text" value="{{ old('customer_phone') }}" placeholder="+62 ..."
                    class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white transition-all outline-none" />
                </div>
              </div>
            </div>

            <div class="glass-gold rounded-[32px] p-8 relative overflow-hidden group">
                <div class="relative z-10">
                    <h3 class="text-sm font-black text-yellow-400 uppercase tracking-widest mb-2">Member Perk</h3>
                    <p class="text-xs font-medium text-white/50 leading-relaxed">Booked spaces are held for 15 mins after schedule start.</p>
                </div>
                <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-yellow-400/10 rounded-full blur-2xl group-hover:bg-yellow-400/20 transition-all"></div>
            </div>
          </div>
        </div>

        {{-- JADWAL --}}
        <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl">
          <div class="flex flex-wrap items-center justify-between gap-6">
            <div>
              <h2 class="text-lg font-bold text-white tracking-tight">Booking Schedule</h2>
              <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Set your date & duration</p>
            </div>

            <button type="button" id="openScheduleModal" {{ $resources->isEmpty() ? 'disabled' : '' }}
              class="group relative overflow-hidden rounded-2xl bg-yellow-400 px-6 py-3 text-[10px] font-black uppercase tracking-[0.2em] text-black hover:bg-yellow-300 transition-all shadow-xl shadow-yellow-400/10 {{ $resources->isEmpty() ? 'opacity-20 cursor-not-allowed' : '' }}">
              <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Select Schedule
              </span>
              <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/40 to-white/0 transition-transform duration-1000 group-hover:translate-x-full"></div>
            </button>
          </div>

          <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="glass flex flex-col items-center justify-center rounded-2xl p-5 border-dashed">
              <div class="text-[9px] font-black uppercase tracking-[0.2em] text-white/20">Arrival Time</div>
              <div id="summaryStart" class="mt-2 text-xs font-black text-white">-</div>
            </div>

            <div class="glass flex flex-col items-center justify-center rounded-2xl p-5 border-dashed">
              <div class="text-[9px] font-black uppercase tracking-[0.2em] text-white/20">Departure Time</div>
              <div id="summaryEnd" class="mt-2 text-xs font-black text-white">-</div>
            </div>

            <div class="glass-gold flex flex-col items-center justify-center rounded-2xl p-5 border-dashed">
              <div class="text-[9px] font-black uppercase tracking-[0.2em] text-yellow-400/40">Rental Est.</div>
              <div id="summaryRental" class="mt-2 text-sm font-black text-yellow-400">Rp 0</div>
            </div>
          </div>

          <p class="mt-6 text-[9px] font-bold uppercase tracking-widest text-white/20 text-center italic">
            * 50% Down Payment is required to secure your booking
          </p>

          <input type="hidden" name="start_date" id="startDateInput">
          <input type="hidden" name="start_time" id="startTimeInput">
          <input type="hidden" name="end_date" id="endDateInput">
          <input type="hidden" name="end_time" id="endTimeInput">
        </div>

        {{-- BUFFET --}}
        <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl">
          <div class="flex flex-wrap items-center justify-between gap-6 mb-8">
            <div>
              <h2 class="text-lg font-bold text-white tracking-tight">Buffet Packages</h2>
              <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Optional curated selections</p>
            </div>

            <div class="rounded-full bg-white/5 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-white/40 border border-white/5">
                Kitchen Prepared
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @foreach($buffetPackages as $bp)
              <label class="relative cursor-pointer group">
                <input type="radio" name="buffet_package_id" value="{{ $bp->id }}" data-price="{{ (int) $bp->price }}"
                    data-pricing-type="{{ $bp->pricing_type }}" data-min-pax="{{ (int) ($bp->min_pax ?? 0) }}"
                    class="peer hidden buffet-radio">
                <div class="h-full rounded-3xl border border-white/5 bg-white/[0.01] p-6 transition-all peer-checked:border-yellow-400 peer-checked:bg-yellow-400/10 group-hover:bg-white/[0.03]">
                  <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="min-w-0">
                      <div class="text-[15px] font-black uppercase tracking-tight text-white leading-tight">{{ $bp->name }}</div>
                      <div class="mt-1 flex items-center gap-2">
                        <span class="text-[10px] font-black text-yellow-400/60 uppercase tracking-widest">{{ $bp->pricing_type === 'per_pax' ? 'Per pax' : 'Per event' }}</span>
                        <span class="h-1 w-1 rounded-full bg-white/10"></span>
                        <span class="text-[10px] font-black text-white/40 uppercase tracking-widest">Rp {{ number_format($bp->price, 0, ',', '.') }}</span>
                      </div>
                    </div>
                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-white/10 peer-checked:border-yellow-400 transition-all">
                        <div class="h-2.5 w-2.5 rounded-full bg-yellow-400 opacity-0 peer-checked:opacity-100 transition-all scale-50 peer-checked:scale-100"></div>
                    </div>
                  </div>

                  <p class="text-[11px] font-medium leading-relaxed text-white/40 italic">
                    {{ $bp->notes ?: 'No description available for this package.' }}
                  </p>
                </div>
              </label>
            @endforeach
          </div>

          <div class="mt-8 max-w-sm">
            <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Number of Pax</label>
            <input type="number" name="pax" min="1" value="{{ old('pax') }}" placeholder="e.g. 30"
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white transition-all outline-none" />
          </div>
        </div>

        {{-- MENU REGULAR --}}
        <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl">
          <div class="flex flex-wrap items-center justify-between gap-6 mb-8">
            <div>
              <h2 class="text-lg font-bold text-white tracking-tight">Regular Menu Items</h2>
              <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Add individual flavors to your stay</p>
            </div>

            <button type="button" id="openMenuModal"
              class="group relative overflow-hidden rounded-2xl bg-white/5 px-6 py-3 text-[10px] font-black uppercase tracking-[0.2em] text-white hover:bg-white/10 transition-all border border-white/10">
              <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                Add Menu
              </span>
            </button>
          </div>

          <div id="selectedList" class="space-y-4"></div>
          <div id="itemsHidden"></div>
        </div>

        {{-- NOTES --}}
        <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl">
          <h2 class="mb-6 text-lg font-bold text-white tracking-tight text-nowrap">Special Instructions</h2>
          <label class="mb-3 block text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Anything else we should know?</label>
          <textarea name="notes" rows="4" placeholder="Dietary restrictions, preferred arrangements, etc."
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white transition-all outline-none">{{ old('notes') }}</textarea>
        </div>
      </section>

      {{-- RIGHT --}}
      <aside class="h-fit space-y-6 lg:sticky lg:top-8 animate-reveal stagger-2">
        <div class="glass overflow-hidden rounded-[32px] p-8 shadow-2xl">
            <div class="mb-8">
                <h2 class="text-lg font-bold text-white tracking-tight">Checkout Summary</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30 text-nowrap">Review your reservation details</p>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-widest text-white/30">
                    <span>Menu Total</span>
                    <span id="sumMenu" class="text-white">Rp 0</span>
                </div>

                <div id="checkoutDetails" class="pt-4 border-t border-white/5">
                    <!-- Dynamic order details will appear here -->
                </div>
                <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-widest text-white/30">
                    <span>Rental Fee</span>
                    <span id="sumRental" class="text-white">Rp 0</span>
                </div>
                <div class="rounded-2xl bg-white/[0.02] p-4 border border-white/5">
                    <div class="text-[9px] font-black uppercase tracking-widest text-white/20 mb-1">Rate Applied</div>
                    <div id="checkoutRateText" class="text-[10px] font-black text-white/60">-</div>
                </div>

                <div class="pt-6 border-t border-white/5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-black uppercase tracking-[0.2em] text-yellow-400">Grand Total</span>
                        <span id="sumGrand" class="text-2xl font-black tracking-tighter text-white">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-widest text-white/30">
                        <span>DP Required (50%)</span>
                        <span id="sumDp" class="text-yellow-400/80">Rp 0</span>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="group relative mt-8 w-full overflow-hidden rounded-[24px] bg-yellow-400 py-6 text-xs font-black uppercase tracking-[0.2em] text-black transition-all hover:bg-yellow-300 active:scale-95 shadow-[0_20px_50px_rgba(250,204,21,0.2)]">
                <span class="relative z-10 flex items-center justify-center gap-3">
                    Confirm Booking
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </span>
                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/40 to-white/0 transition-transform duration-1000 group-hover:translate-x-full"></div>
            </button>

            <p id="submitHint" class="mt-6 text-center text-[10px] font-bold uppercase tracking-[0.2em] text-white/20">
                Please select schedule and items to continue
            </p>
        </div>

        <div class="glass-gold rounded-3xl p-6 relative overflow-hidden group">
            <div class="relative z-10 flex items-center gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-yellow-400/10 text-yellow-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-yellow-400">Need Help?</h4>
                    <p class="text-[10px] font-medium text-white/40 mt-1">Contact us via WhatsApp for custom event arrangements.</p>
                </div>
            </div>
        </div>
      </aside>
    </form>
  </div>

  {{-- MODAL JADWAL --}}
  <div id="scheduleModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md"></div>
    <div class="relative mx-auto mt-[5vh] flex max-h-[90vh] w-[95%] max-w-4xl flex-col rounded-[32px] border border-white/10 bg-[#0d0d10] p-8 shadow-2xl animate-reveal">
      <div class="mb-8 flex shrink-0 items-start justify-between gap-6">
        <div>
          <h2 class="text-xl font-black text-white tracking-tight">Schedule Your Visit</h2>
          <p class="mt-1 text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Select date, time & duration</p>
        </div>

        <button type="button" id="closeScheduleModal"
          class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/5 bg-white/5 text-white/40 hover:text-white transition-all">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain pr-2 hide-scrollbar">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1.1fr_.9fr]">
          {{-- Kalender --}}
          <div class="glass rounded-3xl p-6">
            <div class="flex items-center justify-between mb-6">
              <div id="calTitle" class="text-sm font-black uppercase tracking-widest text-yellow-400">Month Year</div>
              <div class="flex items-center gap-2">
                <button type="button" id="calPrev" class="h-8 w-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 text-white/40 hover:text-white transition-all">←</button>
                <button type="button" id="calNext" class="h-8 w-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 text-white/40 hover:text-white transition-all">→</button>
              </div>
            </div>

            <div class="grid grid-cols-7 gap-2 mb-4">
              @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                <div class="text-center text-[9px] font-black uppercase tracking-widest text-white/20">{{ $day }}</div>
              @endforeach
            </div>

            <div id="calGrid" class="grid grid-cols-7 gap-2"></div>
          </div>

          {{-- Slot Jam --}}
          <div class="glass rounded-3xl p-6">
            <div class="mb-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-white">Select Time Slot</h3>
                <p id="slotMeta" class="mt-1 text-[9px] font-black uppercase tracking-widest text-white/20 italic">Select a date first</p>
            </div>

            <div class="max-h-[30vh] overflow-y-auto overscroll-contain pr-2 hide-scrollbar">
              <div id="slotGrid" class="grid grid-cols-3 gap-2"></div>
            </div>

            <div class="mt-8 pt-8 border-t border-white/5">
              <label class="mb-2 block text-[9px] font-black uppercase tracking-widest text-white/20">Duration</label>
              <select id="durationSelect"
                class="custom-select w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm font-bold text-white outline-none transition-all">
                <option value="" class="bg-black">Select time first</option>
              </select>
              <p class="mt-2 text-[9px] font-medium text-white/20 italic">* Min. duration depends on resource rules</p>
            </div>

            <button type="button" id="applySchedule"
              class="group relative mt-8 w-full overflow-hidden rounded-2xl bg-yellow-400 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-black transition-all hover:bg-yellow-300">
                <span class="relative z-10">Confirm Schedule</span>
                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/40 to-white/0 transition-transform duration-1000 group-hover:translate-x-full"></div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL MENU --}}
  <div id="menuModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md"></div>
    <div class="relative mx-auto mt-[5vh] flex max-h-[90vh] w-[95%] max-w-5xl flex-col rounded-[32px] border border-white/10 bg-[#0d0d10] p-8 shadow-2xl animate-reveal">
      <div class="mb-8 flex shrink-0 items-start justify-between gap-6">
        <div>
          <h2 class="text-xl font-black text-white tracking-tight">Add Regular Menu</h2>
          <p class="mt-1 text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Select from our kitchen favorites</p>
        </div>

        <button type="button" id="closeMenuModal"
          class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/5 bg-white/5 text-white/40 hover:text-white transition-all">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <div class="mb-6 flex gap-3">
        <div class="relative flex-1">
            <input id="menuSearch" placeholder="Search menu..."
                class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm font-bold text-white outline-none transition-all placeholder:text-white/10">
        </div>
        <button type="button" id="menuSearchBtn"
          class="rounded-2xl bg-white/10 px-8 py-4 text-[10px] font-black uppercase tracking-widest text-white hover:bg-white/15 transition-all">
          Search
        </button>
      </div>

      <p id="menuMeta" class="mb-4 text-[9px] font-black uppercase tracking-widest text-white/20">Loading...</p>

      <div class="min-h-0 flex-1 overflow-y-auto pr-2 hide-scrollbar">
        <div id="menuGrid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>
      </div>

      <div class="mt-8 flex shrink-0 items-center justify-between gap-6 border-t border-white/5 pt-8">
        <button type="button" id="loadMore"
          class="rounded-xl border border-white/5 bg-white/5 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-white/40 hover:text-white hover:bg-white/10 transition-all">
          Load More
        </button>

        <button type="button" id="applySelection"
          class="group relative overflow-hidden rounded-xl bg-yellow-400 px-10 py-3 text-[10px] font-black uppercase tracking-widest text-black hover:bg-yellow-300 transition-all">
            <span class="relative z-10">Done Selecting</span>
            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/40 to-white/0 transition-transform duration-1000 group-hover:translate-x-full"></div>
        </button>
      </div>
    </div>
  </div>

  <script>
    const fmtRp = (n) => 'Rp ' + (Math.round(n || 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    const DP_RATIO = {{ (float) config('reservations.dp_ratio', 0.5) }};

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
      const selected = document.querySelector('input[name="buffet_package_id"]:checked');
      if (!selected) return 0;

      const price = parseInt(selected.dataset.price || '0', 10);
      const pricingType = selected.dataset.pricingType || '';
      const paxInput = document.querySelector('input[name="pax"]');
      const pax = parseInt(paxInput?.value || '0', 10);

      if (pricingType === 'per_pax') {
        return price * Math.max(0, pax);
      }

      return price;
    }

    function renderSelectedList() {
      const box = document.getElementById('selectedList');
      const items = [...cart.values()].filter(x => x.qty > 0);

      if (items.length === 0) {
        box.innerHTML = `
            <div class="glass flex flex-col items-center justify-center rounded-3xl border-dashed py-12 text-center">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-white/5 text-white/10">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 italic">No regular items selected yet</p>
            </div>
        `;
        return;
      }

      box.innerHTML = '';

      items.forEach(it => {
        const row = document.createElement('div');
        row.className = 'glass group relative flex items-center justify-between gap-4 rounded-2xl p-4 transition-all hover:bg-white/[0.05] animate-reveal';
        row.innerHTML = `
            <div class="min-w-0 flex-1">
                <div class="text-xs font-black uppercase tracking-tight text-white line-clamp-1">${it.name}</div>
                <div class="mt-1 flex items-center gap-2">
                    <span class="text-[9px] font-black text-yellow-400/60 uppercase tracking-widest">${fmtRp(it.price)}</span>
                    <span class="h-1 w-1 rounded-full bg-white/10"></span>
                    <span class="text-[9px] font-black text-white/20 uppercase tracking-widest italic">Max ${it.max ?? '-'}</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" class="sel-minus flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-white/40 transition-all hover:bg-white/10 hover:text-white active:scale-90" data-id="${it.id}">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                </button>
                <div class="min-w-[20px] text-center text-xs font-black text-white">${it.qty}</div>
                <button type="button" class="sel-plus flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-400 text-black transition-all hover:bg-yellow-300 active:scale-90" data-id="${it.id}">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>
        `;
        box.appendChild(row);
      });

      box.querySelectorAll('.sel-minus').forEach(btn => {
        btn.onclick = () => {
          const id = parseInt(btn.dataset.id, 10);
          const it = cart.get(id);
          if (!it) return;
          it.qty = Math.max(0, it.qty - 1);
          cart.set(id, it);
          rebuildHiddenItems();
          renderSelectedList();
          updateTotals();
        };
      });

      box.querySelectorAll('.sel-plus').forEach(btn => {
        btn.onclick = () => {
          const id = parseInt(btn.dataset.id, 10);
          const it = cart.get(id);
          if (!it) return;
          const max = (it.max ?? 999999);
          it.qty = Math.min(max, it.qty + 1);
          cart.set(id, it);
          rebuildHiddenItems();
          renderSelectedList();
          updateTotals();
        };
      });
    }

    const scheduleModal = document.getElementById('scheduleModal');
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

      document.getElementById('resourceTypeText').textContent = type;
      document.getElementById('resourceCapacityText').textContent = capacity > 0 ? `${capacity} people` : '-';
      document.getElementById('resourceHourlyText').textContent = hourly > 0 ? fmtRp(hourly) : 'N/A';
      document.getElementById('resourceFlatText').textContent = flat > 0 ? fmtRp(flat) : 'N/A';
      document.getElementById('resourceMinText').textContent = min > 0 ? `${min} mins` : '-';
      document.getElementById('resourceBufferText').textContent = `${buffer} mins`;

      let rateText = '-';
      if (flat > 0 && hourly > 0) rateText = `${fmtRp(hourly)}/hr • Flat ${fmtRp(flat)}`;
      else if (flat > 0) rateText = `Flat ${fmtRp(flat)}`;
      else if (hourly > 0) rateText = `${fmtRp(hourly)}/hr`;

      [...document.querySelectorAll('#checkoutRateText')].forEach(el => el.textContent = rateText);
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

      if (selectedDate && selectedStart && selectedDuration) {
        const endHHMM = fromMin(parseHHMM(selectedStart) + selectedDuration);

        document.getElementById('summaryStart').textContent = `${selectedDate} ${selectedStart}`;
        document.getElementById('summaryEnd').textContent = `${selectedDate} ${endHHMM}`;

        document.getElementById('startDateInput').value = selectedDate;
        document.getElementById('startTimeInput').value = selectedStart;
        document.getElementById('endDateInput').value = selectedDate;
        document.getElementById('endTimeInput').value = endHHMM;

        document.getElementById('submitHint').textContent = 'Ready to reserve';
        document.getElementById('submitHint').classList.remove('text-white/20');
        document.getElementById('submitHint').classList.add('text-yellow-400');
      } else {
        document.getElementById('summaryStart').textContent = '-';
        document.getElementById('summaryEnd').textContent = '-';
        document.getElementById('startDateInput').value = '';
        document.getElementById('startTimeInput').value = '';
        document.getElementById('endDateInput').value = '';
        document.getElementById('endTimeInput').value = '';
        document.getElementById('submitHint').textContent = 'Please select schedule to continue';
        document.getElementById('submitHint').classList.add('text-white/20');
        document.getElementById('submitHint').classList.remove('text-yellow-400');
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
      updateCheckoutDetails();
    }

    function updateCheckoutDetails() {
      const detailBox = document.getElementById('checkoutDetails');
      if (!detailBox) return;
      detailBox.innerHTML = '';

      let hasDetails = false;

      // Buffet detail
      const buffet = document.querySelector('input[name="buffet_package_id"]:checked');
      if (buffet) {
        const label = buffet.closest('label');
        const name = label.querySelector('.text-\[15px\]').textContent;
        const row = document.createElement('div');
        row.className = 'flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-white/40 mb-2';
        row.innerHTML = `<span>${name}</span><span>${fmtRp(calcBuffetTotal())}</span>`;
        detailBox.appendChild(row);
        hasDetails = true;
      }

      // Regular items
      for (const it of cart.values()) {
        if (it.qty > 0) {
          const row = document.createElement('div');
          row.className = 'flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-white/40 mb-2';
          row.innerHTML = `<span>${it.name} x ${it.qty}</span><span>${fmtRp(it.price * it.qty)}</span>`;
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
          cell.className = 'h-10 rounded-xl bg-white/[0.02] text-white/5 cursor-default';
          cell.textContent = '';
          grid.appendChild(cell);
          continue;
        }

        const dateObj = new Date(y, m, dayNum);
        const iso = toISODate(dateObj);
        const isToday = iso === toISODate(new Date());

        cell.dataset.date = iso;
        cell.textContent = String(dayNum);
        cell.className = `h-10 rounded-xl text-xs font-black transition-all ${isToday ? 'text-yellow-400 border border-yellow-400/20 bg-yellow-400/5' : 'text-white/40 hover:text-white hover:bg-white/5 border border-white/5'}`;

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

      meta.textContent = `${selectedDate} • Select available time`;

      const open = parseHHMM(availability.open);
      const close = parseHHMM(availability.close);
      const minDur = parseInt(availability.min_duration_minutes || 60, 10);
      const step = Math.max(5, parseInt(availability.slot_minutes || 30, 10));
      const booked = availability.booked || [];

      let hasAvailable = false;

      for (let t = open; t + minDur <= close; t += step) {
        const hhmm = fromMin(t);
        const disabled = isOverlap(t, t + minDur, booked);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = hhmm;
        btn.disabled = disabled;
        btn.className = `rounded-xl py-3 text-[10px] font-black uppercase tracking-widest transition-all ${disabled ? 'bg-white/[0.02] text-white/10 cursor-not-allowed opacity-50' : 'bg-white/5 text-white/60 hover:bg-white/10 hover:text-white border border-white/5'}`;

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
        sel.innerHTML = `<option value="" class="bg-black text-white/20">Select time first</option>`;
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

    document.getElementById('openScheduleModal').onclick = () => {
      scheduleModal.classList.remove('hidden');
      renderCalendar();
      renderSlots();
      renderDurations();
    };

    document.getElementById('closeScheduleModal').onclick = () => {
      scheduleModal.classList.add('hidden');
    };

    document.getElementById('calPrev').onclick = () => {
      calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() - 1, 1);
      renderCalendar();
    };

    document.getElementById('calNext').onclick = () => {
      calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() + 1, 1);
      renderCalendar();
    };

    document.getElementById('applySchedule').onclick = () => {
      if (!selectedDate || !selectedStart || !selectedDuration) {
        return;
      }
      scheduleModal.classList.add('hidden');
      updateTotals();
    };

    document.getElementById('resourceSelect').addEventListener('change', () => {
      selectedDate = null;
      selectedStart = null;
      selectedDuration = null;
      availability = null;
      updateResourceMeta();
      updateTotals();
    });

    const menuModal = document.getElementById('menuModal');
    const menuGrid = document.getElementById('menuGrid');
    const menuMeta = document.getElementById('menuMeta');
    const loadMoreBtn = document.getElementById('loadMore');

    let page = 1;
    let lastPage = 1;
    let query = '';

    function openMenuModal() {
      menuModal.classList.remove('hidden');
      page = 1;
      lastPage = 1;
      query = '';
      document.getElementById('menuSearch').value = '';
      menuGrid.innerHTML = '';
      fetchMenuPage();
    }

    function closeMenuModal() {
      menuModal.classList.add('hidden');
    }

    document.getElementById('openMenuModal').onclick = openMenuModal;
    document.getElementById('closeMenuModal').onclick = closeMenuModal;

    document.getElementById('applySelection').onclick = () => {
      closeMenuModal();
      rebuildHiddenItems();
      renderSelectedList();
      updateTotals();
    };

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
      menuMeta.textContent = 'Discovering flavors...';

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
        menuMeta.textContent = `Showing items ${json.meta.from ?? 0}-${json.meta.to ?? 0} of ${json.meta.total}`;
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
          card.className = 'glass group overflow-hidden rounded-[28px] transition-all hover:bg-white/[0.05] animate-reveal';
          card.innerHTML = `
              <div class="relative h-32 overflow-hidden bg-white/5">
                  ${p.image_url
                      ? `<img src="${p.image_url}" class="h-full w-full object-cover opacity-60 group-hover:opacity-100 transition-all duration-500 scale-110 group-hover:scale-100">`
                      : `<div class="h-full w-full bg-gradient-to-br from-yellow-400/10 via-white/5 to-transparent"></div>`}
                  <div class="absolute top-4 right-4 rounded-full bg-black/40 backdrop-blur-md px-3 py-1 text-[10px] font-black text-yellow-400 border border-white/5">${fmtRp(p.price)}</div>
              </div>
              <div class="p-5">
                  <h4 class="text-xs font-black uppercase tracking-tight text-white line-clamp-1">${p.name}</h4>
                  <p class="mt-1 text-[9px] font-black text-white/20 uppercase tracking-widest">${p.category ?? 'Kitchen Selection'}</p>
                  <p class="mt-3 text-[10px] font-medium text-white/40 leading-relaxed line-clamp-2 italic">${p.description || 'No description available.'}</p>
                  
                  <div class="mt-5 flex items-center justify-between gap-4">
                      <div class="text-[9px] font-black uppercase tracking-widest ${disabled ? 'text-red-400/50' : 'text-white/20'}">
                          ${disabled ? 'Sold Out' : `Available: ${max}`}
                      </div>
                      <div class="flex items-center gap-2">
                          <button type="button" class="m-minus flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 text-white/40 transition-all hover:bg-white/10 hover:text-white active:scale-90" data-id="${p.id}" ${disabled ? 'disabled' : ''}>
                              <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                          </button>
                          <div class="min-w-[20px] text-center text-xs font-black text-white" id="q-${p.id}">${currentQty}</div>
                          <button type="button" class="m-plus flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-400 text-black transition-all hover:bg-yellow-300 active:scale-90" data-id="${p.id}" ${disabled ? 'disabled' : ''}>
                              <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
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
    };

    document.querySelectorAll('input[name="buffet_package_id"]').forEach(el => {
      el.addEventListener('change', updateTotals);
      
      // Allow unselect
      el.onclick = () => {
        if (el.dataset.wasChecked === 'true') {
          el.checked = false;
          el.dataset.wasChecked = 'false';
          updateTotals();
        } else {
          document.querySelectorAll('input[name="buffet_package_id"]').forEach(r => r.dataset.wasChecked = 'false');
          el.dataset.wasChecked = 'true';
        }
      };
    });

    const paxInput = document.querySelector('input[name="pax"]');
    if (paxInput) {
      paxInput.addEventListener('input', updateTotals);
    }

    renderSelectedList();
    updateResourceMeta();
    updateTotals();
  </script>
</body>
</html>