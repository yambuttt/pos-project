@extends('layouts.admin')
@section('title', 'Buat Reservasi Baru')

@section('body')
    <!-- HEADER -->
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="flex items-center gap-4">
            <button id="openMobileSidebar" type="button"
                class="inline-flex lg:hidden items-center justify-center w-10 h-10 rounded-xl border border-white/10 bg-white/5 text-white/70 hover:bg-white/10 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
            <div>
                <h1 class="text-3xl font-bold text-gold-gradient">Buat Reservasi</h1>
                <p class="text-sm text-white/40 font-medium italic">Input manual oleh admin <span class="text-gold-primary font-bold not-italic">(Pending DP).</span></p>
            </div>
        </div>

        <a href="{{ route('admin.reservations.index') }}"
            class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-6 py-3.5 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="mb-8 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
             <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
           </svg>
           <p class="text-sm font-bold text-red-100 whitespace-pre-line">{{ $errors->first() }}</p>
        </div>
    @endif

    <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-gold-primary/5 blur-[100px] rounded-full"></div>
        
        <form method="POST" action="{{ route('admin.reservations.store') }}" class="relative z-10 space-y-10">
            @csrf

            <!-- SECTION 1: RESOURCE & MENU -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                   </div>
                   <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Pilihan Tempat & Menu</h3>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Pilih Resource (Meja/Room)</label>
                        <select name="reservation_resource_id"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none"
                            required>
                            @foreach($resources as $rs)
                                <option value="{{ $rs->id }}">
                                    [{{ $rs->type }}] {{ $rs->name }} (kap {{ $rs->capacity }} pax)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Tipe Layanan</label>
                        <select id="menuType" name="menu_type"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none"
                            required>
                            <option value="REGULAR">REGULAR (Auto lock stok)</option>
                            <option value="BUFFET">BUFFET (Paket Inventory)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: CUSTOMER INFO -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                   </div>
                   <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Data Pelanggan</h3>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Nama Customer</label>
                        <input name="customer_name" value="{{ old('customer_name') }}" placeholder="Masukkan nama lengkap..."
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all"
                            required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">No. WhatsApp / HP</label>
                        <input name="customer_phone" value="{{ old('customer_phone') }}" placeholder="0812xxxx (opsional)"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: JADWAL -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                   </div>
                   <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Waktu & Pax</h3>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-2 lg:col-span-1">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Waktu Mulai</label>
                        <input type="datetime-local" name="start_at" value="{{ old('start_at') }}"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all"
                            required>
                    </div>
                    <div class="space-y-2 lg:col-span-1">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Waktu Selesai</label>
                        <input type="datetime-local" name="end_at" value="{{ old('end_at') }}"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all"
                            required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Jumlah Pax</label>
                        <input type="number" name="pax" min="1" value="{{ old('pax') }}" placeholder="Contoh: 10"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Total Rental (Rp)</label>
                        <input type="number" name="rental_total" min="0" value="{{ old('rental_total', 0) }}"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-gold-primary font-bold outline-none focus:border-gold-primary/30 transition-all">
                    </div>
                </div>
            </div>

            <!-- SECTION 4: ITEMS / PAKET (DYNAMIC) -->
            <div id="buffetPackageWrap" class="hidden animate-fade-in premium-card p-8 border-gold-primary/10 bg-gold-primary/[0.02]">
                <div class="flex items-center gap-3 mb-6">
                   <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                      </svg>
                   </div>
                   <div>
                      <h4 class="text-xs font-black text-white uppercase tracking-[0.2em]">Pilihan Paket Buffet</h4>
                      <p class="text-[10px] text-white/30 italic">Pilih paket menu untuk reservasi buffet.</p>
                   </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Nama Paket</label>
                    <select name="buffet_package_id"
                        class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-4 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none">
                        <option value="">-- Pilih Paket Buffet --</option>
                        @foreach($buffetPackages as $bp)
                            <option value="{{ $bp->id }}">
                                {{ $bp->name }} —
                                {{ $bp->pricing_type === 'per_pax' ? 'per pax' : 'per event' }}
                                (Rp {{ number_format($bp->price, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="regularWrap" class="animate-fade-in premium-card p-8 border-white/5 bg-white/[0.02]">
                <div class="flex items-center justify-between gap-4 mb-8">
                   <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                         </svg>
                      </div>
                      <div>
                         <h4 class="text-xs font-black text-white uppercase tracking-[0.2em]">Regular Items</h4>
                         <p class="text-[10px] text-white/30 italic">Tambah produk spesifik untuk reservasi regular.</p>
                      </div>
                   </div>
                   <button type="button" onclick="addRow()"
                       class="rounded-xl border border-gold-primary/20 bg-gold-primary/10 px-4 py-2 text-[10px] font-black text-gold-primary uppercase tracking-widest hover:bg-gold-primary hover:text-black transition-all active:scale-95">
                       + Item
                   </button>
                </div>

                <div id="itemsWrap" class="space-y-3">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                        <div class="md:col-span-8">
                           <select name="items[0][product_id]"
                               class="w-full rounded-xl border border-white/5 bg-black/40 px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none">
                               <option value="">-- Pilih Produk --</option>
                               @foreach($products as $p)
                                   <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                               @endforeach
                           </select>
                        </div>
                        <div class="md:col-span-3">
                           <input type="number" name="items[0][qty]" min="1" value="1"
                               class="w-full rounded-xl border border-white/5 bg-black/40 px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30">
                        </div>
                        <div class="md:col-span-1">
                           <!-- Spacer for symmetry -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- NOTES -->
            <div class="space-y-2">
                <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="4" placeholder="Contoh: Request khusus dekorasi, alergi makanan, dll..."
                    class="w-full rounded-2xl border border-white/5 bg-white/[0.03] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all">{{ old('notes') }}</textarea>
            </div>

            <button class="w-full rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark py-5 text-sm font-black text-obsidian-950 uppercase tracking-[0.3em] shadow-2xl shadow-gold-primary/20 hover:shadow-[0_0_30px_rgba(212,175,55,0.4)] hover:scale-[1.01] transition-all active:scale-[0.98] border border-gold-light/20">
                Simpan Reservasi (Pending DP)
            </button>
        </form>
    </div>

    <script>
        let idx = 1;
        function addRow() {
            const wrap = document.getElementById('itemsWrap');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 gap-3 md:grid-cols-12 animate-fade-in';
            row.innerHTML = `
                <div class="md:col-span-8">
                   <select name="items[${idx}][product_id]"
                       class="w-full rounded-xl border border-white/5 bg-black/40 px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 appearance-none">
                       <option value="">-- Pilih Produk --</option>
                       @foreach($products as $p)
                           <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                       @endforeach
                   </select>
                </div>
                <div class="md:col-span-3">
                   <input type="number" name="items[${idx}][qty]" min="1" value="1"
                       class="w-full rounded-xl border border-white/5 bg-black/40 px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30">
                </div>
                <div class="md:col-span-1">
                   <button type="button" onclick="this.parentElement.parentElement.remove()"
                       class="w-full h-full rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                       </svg>
                   </button>
                </div>
            `;
            wrap.appendChild(row);
            idx++;
        }

        function syncBuffetUI() {
            const mt = document.getElementById('menuType').value;
            document.getElementById('buffetPackageWrap').classList.toggle('hidden', mt !== 'BUFFET');
            document.getElementById('regularWrap').classList.toggle('hidden', mt !== 'REGULAR');
        }
        document.getElementById('menuType').addEventListener('change', syncBuffetUI);
        syncBuffetUI();
    </script>
@endsection