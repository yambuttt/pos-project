@extends('layouts.admin')
@section('title', 'Detail Reservasi')

@section('body')
    @php
        $remaining = max(0, (int) $reservation->grand_total - (int) $reservation->paid_amount);

        $finalQrUrl = null;
        if (is_array($reservation->midtrans_response ?? null)) {
            $actions = collect($reservation->midtrans_response['actions'] ?? []);
            $finalQrUrl = optional($actions->firstWhere('name', 'generate-qr-code'))['url'] ?? null;
        }
        $isFinalOrder = is_string($reservation->midtrans_order_id ?? null) && str_starts_with($reservation->midtrans_order_id, 'RSV-FINAL-');
        
        $statusColors = [
           'draft' => 'text-white/40 bg-white/5 border-white/10',
           'pending_dp' => 'text-yellow-500 bg-yellow-500/10 border-yellow-500/20',
           'confirmed' => 'text-blue-500 bg-blue-500/10 border-blue-500/20',
           'checked_in' => 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20',
           'completed' => 'text-green-500 bg-green-500/10 border-green-500/20',
           'cancelled' => 'text-red-500 bg-red-500/10 border-red-500/20',
           'no_show' => 'text-orange-500 bg-orange-500/10 border-orange-500/20',
        ];
        $colorClass = $statusColors[$reservation->status] ?? 'text-white bg-white/10 border-white/20';
    @endphp

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
                <div class="flex items-center gap-3 mb-1">
                   <h1 class="text-3xl font-bold text-gold-gradient">Detail Reservasi</h1>
                   <span class="px-2.5 py-1 rounded-lg border text-[9px] font-black uppercase tracking-widest {{ $colorClass }}">
                      {{ $reservation->status }}
                   </span>
                </div>
                <p class="text-sm text-white/40 font-medium italic">Kode Reservasi: <span class="text-white font-bold not-italic">#{{ $reservation->code }}</span></p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if (in_array($reservation->menu_type, ['BUFFET', 'MIXED']))
                <a href="{{ route('admin.reservations.buffet_inventory', $reservation) }}"
                    class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-6 py-3.5 text-xs font-black text-gold-primary uppercase tracking-widest hover:bg-gold-primary/5 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Kelola Buffet
                </a>
            @endif
            <a href="{{ route('admin.reservations.index') }}"
                class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-6 py-3.5 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
             <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
           </svg>
           <p class="text-sm font-bold text-green-100">{{ session('success') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
             <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
           </svg>
           <p class="text-sm font-bold text-red-100">{{ $errors->first() }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_400px]">
        {{-- LEFT COLUMN --}}
        <div class="space-y-8">
            <!-- QUICK INFO CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="premium-card p-6 border-white/5 bg-white/[0.02]">
                    <div class="flex items-center gap-3 mb-4">
                       <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                          </svg>
                       </div>
                       <h4 class="text-[9px] font-black text-white/20 uppercase tracking-widest">Data Customer</h4>
                    </div>
                    <div class="text-lg font-bold text-white mb-0.5">{{ $reservation->customer_name }}</div>
                    <div class="text-xs text-gold-primary/60 font-medium italic">{{ $reservation->customer_phone }}</div>
                </div>

                <div class="premium-card p-6 border-white/5 bg-white/[0.02]">
                    <div class="flex items-center gap-3 mb-4">
                       <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                          </svg>
                       </div>
                       <h4 class="text-[9px] font-black text-white/20 uppercase tracking-widest">Resource & Tempat</h4>
                    </div>
                    <div class="text-lg font-bold text-white mb-0.5">{{ $reservation->resource?->name }}</div>
                    <div class="text-xs text-blue-400/60 font-medium italic">[{{ $reservation->resource?->type }}] Kap: {{ $reservation->resource?->capacity }} pax</div>
                </div>

                <div class="premium-card p-6 border-white/5 bg-white/[0.02]">
                    <div class="flex items-center gap-3 mb-4">
                       <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                       </div>
                       <h4 class="text-[9px] font-black text-white/20 uppercase tracking-widest">Jadwal Reservasi</h4>
                    </div>
                    <div class="text-lg font-bold text-white mb-0.5 italic">{{ $reservation->start_at->format('d M Y') }}</div>
                    <div class="text-xs text-white/40 font-medium">{{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}</div>
                </div>

                <div class="premium-card p-6 border-white/5 bg-white/[0.02]">
                    <div class="flex items-center gap-3 mb-4">
                       <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                          </svg>
                       </div>
                       <h4 class="text-[9px] font-black text-white/20 uppercase tracking-widest">Tipe Menu</h4>
                    </div>
                    <div class="text-lg font-bold text-white mb-0.5 italic">{{ $reservation->menu_type }}</div>
                    <div class="text-xs text-white/40 font-medium">Pax: {{ $reservation->pax ?? '-' }}</div>
                </div>
            </div>

            <!-- ITEMS TABLE -->
            <div class="glass-panel p-8 rounded-[2.5rem]">
                <div class="flex items-center gap-3 mb-6">
                   <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                      </svg>
                   </div>
                   <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Item Pesanan</h3>
                </div>

                <div class="space-y-3">
                    @forelse($reservation->items as $it)
                        <div class="group flex items-center justify-between gap-4 p-4 rounded-[1.5rem] bg-white/[0.02] border border-white/5 hover:border-gold-primary/30 transition-all">
                            <div class="flex items-center gap-4">
                               <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white/20 border border-white/10 group-hover:border-gold-primary/30 transition-all">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                  </svg>
                               </div>
                               <div>
                                  <div class="text-sm font-bold text-white group-hover:text-gold-primary transition-colors">{{ $it->snapshot_name }}</div>
                                  <div class="text-[10px] text-white/30 uppercase tracking-widest mt-0.5">Quantity: {{ $it->qty }}x</div>
                               </div>
                            </div>
                            <div class="text-right">
                               <div class="text-sm font-black text-white italic">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-white/20 italic font-medium">Tidak ada item pesanan khusus.</div>
                    @endforelse
                </div>
            </div>

            <!-- MATERIAL LOCKS -->
            @if (in_array($reservation->menu_type, ['REGULAR', 'MIXED']))
                <div class="glass-panel p-8 rounded-[2.5rem]">
                    <div class="flex items-center gap-3 mb-6">
                       <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                          </svg>
                       </div>
                       <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Material Locks (Kitchen)</h3>
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-white/5 bg-black/20">
                      <div class="overflow-x-auto">
                          <table class="w-full text-left text-sm">
                              <thead class="bg-white/[0.03] text-[9px] uppercase tracking-widest text-white/30 font-black border-b border-white/5">
                                  <tr>
                                      <th class="px-8 py-5">Bahan Baku</th>
                                      <th class="px-6 py-5 text-center">Locked</th>
                                      <th class="px-6 py-5 text-center">Released</th>
                                      <th class="px-6 py-5 text-center font-bold text-white/60">Consumed</th>
                                  </tr>
                              </thead>
                              <tbody class="divide-y divide-white/5">
                                  @forelse($reservation->locks as $l)
                                      <tr class="group hover:bg-white/[0.02] transition-colors">
                                          <td class="px-8 py-6">
                                             <div class="text-xs font-bold text-white/90 group-hover:text-gold-primary transition-colors">{{ $l->rawMaterial?->name }}</div>
                                             <div class="text-[9px] text-white/20 uppercase tracking-tighter">{{ $l->rawMaterial?->unit }}</div>
                                          </td>
                                          <td class="px-6 py-6 text-center text-xs font-bold text-white/60">{{ (float)$l->qty_locked }}</td>
                                          <td class="px-6 py-6 text-center text-xs font-bold text-blue-400/60">{{ (float)$l->qty_released }}</td>
                                          <td class="px-6 py-6 text-center text-xs font-black text-emerald-400 italic">{{ (float)$l->qty_consumed }}</td>
                                      </tr>
                                  @empty
                                      <tr>
                                          <td colspan="4" class="px-8 py-12 text-center text-white/20 italic font-medium">Belum ada bahan baku yang di-lock untuk pesanan ini.</td>
                                      </tr>
                                  @endforelse
                              </tbody>
                          </table>
                      </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="space-y-8">
            <!-- BILLING SUMMARY -->
            <div class="premium-card p-8 border-gold-primary/20 bg-gold-primary/[0.03] space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                      </svg>
                   </div>
                   <h4 class="text-xs font-black text-gold-primary uppercase tracking-[0.2em]">Ringkasan Tagihan</h4>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between text-[11px] text-white/60 italic"><span>Biaya Menu</span><span class="font-bold">Rp {{ number_format($reservation->menu_total, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-[11px] text-white/60 italic"><span>Sewa Resource</span><span class="font-bold">Rp {{ number_format($reservation->rental_total, 0, ',', '.') }}</span></div>
                    
                    <div class="pt-6 border-t border-white/10">
                       <div class="flex flex-col gap-1 mb-6">
                          <span class="text-[10px] text-gold-primary font-black uppercase tracking-widest">Grand Total</span>
                          <span class="text-4xl font-black text-white italic tracking-tighter">Rp {{ number_format($reservation->grand_total, 0, ',', '.') }}</span>
                       </div>

                       <div class="grid grid-cols-2 gap-3">
                          <div class="p-3 rounded-2xl bg-white/5 border border-white/5">
                             <p class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Telah Dibayar</p>
                             <p class="text-xs font-bold text-emerald-400 italic">Rp {{ number_format($reservation->paid_amount, 0, ',', '.') }}</p>
                          </div>
                          <div class="p-3 rounded-2xl bg-white/5 border border-white/5">
                             <p class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Sisa Tagihan</p>
                             <p class="text-xs font-bold text-yellow-500 italic">Rp {{ number_format($remaining, 0, ',', '.') }}</p>
                          </div>
                       </div>
                    </div>
                </div>
            </div>

            <!-- PAYMENT HISTORY -->
            <div class="premium-card p-8 border-white/5 bg-white/[0.02]">
                <div class="flex items-center gap-3 mb-6">
                   <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white/30 border border-white/10">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                   </div>
                   <h4 class="text-[9px] font-black text-white/30 uppercase tracking-widest">Riwayat Pembayaran</h4>
                </div>

                <div class="space-y-3">
                    @forelse($reservation->payments as $p)
                        <div class="p-4 rounded-2xl border border-white/5 bg-black/20 group">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-[11px] font-black text-white uppercase tracking-tighter group-hover:text-gold-primary transition-colors">{{ $p->type }}</div>
                                <div class="text-sm font-black text-white">Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
                            </div>
                            <div class="flex items-center justify-between">
                               <div class="text-[9px] text-white/30 font-medium italic">{{ $p->method }} • {{ $p->status }}</div>
                               <div class="text-[9px] text-white/20">{{ optional($p->paid_at)->format('d/m/y H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-white/20 italic text-center py-4">Belum ada catatan pembayaran.</p>
                    @endforelse
                </div>
            </div>

            <!-- ACTION PANEL -->
            <div class="premium-card p-8 border-white/10 bg-white/[0.05] space-y-4">
                <h4 class="text-[10px] font-black text-white uppercase tracking-[0.2em] mb-4">Aksi Kelola Reservasi</h4>

                @if(in_array($reservation->status, ['pending_dp', 'draft'], true))
                    <div x-data="{ open: false }" class="space-y-3">
                       <button @click="open = !open" class="w-full rounded-2xl bg-emerald-500 py-4 text-xs font-black text-black uppercase tracking-widest shadow-xl shadow-emerald-500/20 hover:scale-[1.02] transition-all">
                          Konfirmasi DP Manual
                       </button>
                       <form x-show="open" x-collapse method="POST" action="{{ route('admin.reservations.dp_paid', $reservation) }}"
                           class="p-6 rounded-2xl bg-black/40 border border-white/10 space-y-4 animate-fade-in">
                           @csrf
                           <div class="space-y-1.5">
                               <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Metode Bayar</label>
                               <select name="method" class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none">
                                   <option value="CASH">CASH (Tunai)</option>
                                   <option value="QRIS">QRIS</option>
                                   <option value="MIDTRANS">Transfer Bank</option>
                               </select>
                           </div>
                           <div class="space-y-1.5">
                               <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Jumlah DP</label>
                               <input type="number" name="amount" min="1" value="{{ $reservation->dp_amount }}"
                                   class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-gold-primary/30">
                           </div>
                           <button class="w-full rounded-xl bg-white/10 py-3 text-[10px] font-black text-white uppercase tracking-widest hover:bg-white/20 transition-all">Simpan Konfirmasi</button>
                       </form>
                    </div>
                @endif

                @if($reservation->status === 'confirmed')
                    <form method="POST" action="{{ route('admin.reservations.check_in', $reservation) }}">
                        @csrf
                        <button class="w-full rounded-2xl bg-blue-600 py-4 text-xs font-black text-white uppercase tracking-widest shadow-xl shadow-blue-600/20 hover:scale-[1.02] transition-all active:scale-95">
                            Check-in Kunjungan
                        </button>
                    </form>
                @endif

                @if($reservation->status === 'checked_in')
                    <div x-data="{ open: false }" class="space-y-3">
                       <button @click="open = !open" class="w-full rounded-2xl bg-white/10 border border-white/10 py-4 text-xs font-black text-white uppercase tracking-widest hover:bg-white/20 transition-all">
                          Checkout & Pelunasan
                       </button>
                       <form x-show="open" x-collapse method="POST" action="{{ route('admin.reservations.checkout', $reservation) }}"
                           class="p-6 rounded-2xl bg-black/40 border border-white/10 space-y-4 animate-fade-in">
                           @csrf
                           <div class="space-y-1.5">
                               <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Metode Pelunasan</label>
                               <select name="method" class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none">
                                   <option value="CASH">CASH (Tunai)</option>
                                   <option value="QRIS">QRIS (Midtrans)</option>
                               </select>
                           </div>
                           <div class="space-y-1.5">
                               <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Sisa Pembayaran</label>
                               <input type="number" name="amount" min="1" value="{{ $remaining }}"
                                   class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-gold-primary/30">
                           </div>
                           <button class="w-full rounded-xl bg-gold-primary py-3 text-[10px] font-black text-obsidian-950 uppercase tracking-widest hover:scale-[1.02] transition-all">Selesaikan Transaksi</button>
                       </form>
                    </div>
                @endif

                @if(!in_array($reservation->status, ['completed', 'cancelled'], true))
                    <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}"
                        onsubmit="return confirm('Batalkan reservasi ini?')">
                        @csrf
                        <button class="w-full rounded-2xl bg-red-500/10 border border-red-500/20 py-4 text-xs font-black text-red-500 uppercase tracking-widest hover:bg-red-500/20 transition-all active:scale-95">
                            Batalkan Reservasi
                        </button>
                    </form>
                @endif
            </div>

            <!-- NOTES -->
            @if($reservation->note)
            <div class="premium-card p-8 border-white/5 bg-white/[0.02]">
               <h4 class="text-[9px] font-black text-white/20 uppercase tracking-widest mb-4">Catatan Reservasi</h4>
               <p class="text-xs text-white/60 leading-relaxed italic">"{{ $reservation->note }}"</p>
            </div>
            @endif
        </div>
    </div>
@endsection