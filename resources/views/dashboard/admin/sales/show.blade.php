@extends('layouts.admin')
@section('title', 'Detail Transaksi')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Detail Transaksi</h1>
        <p class="text-sm text-white/40 font-medium italic">Rincian invoice <span class="text-white font-bold not-italic">#{{ $sale->invoice_no ?? $sale->id }}</span></p>
      </div>
    </div>

    <a href="{{ route('admin.sales.index') }}"
      class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-xs font-black text-white border border-white/10 hover:bg-white/10 transition-all active:scale-95 uppercase tracking-widest">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Riwayat
    </a>
  </div>

  <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_400px]">
    {{-- LEFT: ITEMS LIST --}}
    <div class="space-y-8">
      <div class="glass-panel p-8 rounded-[2.5rem] relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-gold-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
           </svg>
        </div>

        <div class="flex items-center gap-3 mb-8">
           <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
           </div>
           <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Daftar Item Belanja</h3>
        </div>

        <div class="overflow-hidden rounded-3xl border border-white/5 bg-black/20">
          <table class="w-full text-left">
            <thead class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-white/30 font-black border-b border-white/5">
              <tr>
                <th class="px-8 py-5">Nama Produk</th>
                <th class="px-6 py-5 text-center">Kuantitas</th>
                <th class="px-6 py-5 text-right">Harga Satuan</th>
                <th class="px-8 py-5 text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              @forelse($sale->items ?? [] as $it)
                <tr class="group hover:bg-white/[0.02] transition-colors">
                  <td class="px-8 py-6">
                    <div class="flex items-center gap-4">
                       <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/20 group-hover:border-gold-primary/30 transition-all">
                          @if($it->product?->image_url)
                             <img src="{{ $it->product->image_url }}" class="w-full h-full object-cover rounded-2xl" alt="">
                          @else
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                             </svg>
                          @endif
                       </div>
                       <div>
                          <div class="text-sm font-bold text-white/90">{{ $it->product->name ?? 'Produk Dihapus' }}</div>
                          <div class="text-[10px] text-white/30 uppercase tracking-widest mt-0.5">{{ $it->product->category?->name ?? 'Kategori' }}</div>
                       </div>
                    </div>
                  </td>
                  <td class="px-6 py-6 text-center">
                    <span class="px-3 py-1 rounded-lg bg-white/5 text-xs font-black text-white/60 border border-white/5">
                       {{ $it->qty ?? 0 }}x
                    </span>
                  </td>
                  <td class="px-6 py-6 text-right text-[11px] font-medium text-white/40 italic">Rp {{ number_format($it->price ?? 0, 0, ',', '.') }}</td>
                  <td class="px-8 py-6 text-right text-sm font-black text-white">Rp {{ number_format($it->subtotal ?? 0, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="px-8 py-20 text-center text-sm text-white/20 italic font-medium">Data item tidak ditemukan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- RIGHT: SUMMARY & INFO --}}
    <div class="space-y-8">
       <!-- SERVICE & LAYANAN DETAIL -->
       <div class="premium-card p-8 border-white/5 bg-white/[0.02] relative overflow-hidden group">
          <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
          
          <div class="flex items-center gap-3 mb-6">
             <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/40 border border-white/10 group-hover:border-gold-primary/30 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
             </div>
             <div>
                <h4 class="text-[9px] font-black text-white/20 uppercase tracking-widest">Jenis Layanan</h4>
                <p class="text-sm font-bold text-white italic">Detail Pesanan</p>
             </div>
          </div>

          <div class="flex flex-wrap gap-2 mb-6">
             @if(($sale->order_type ?? '') === 'dine_in')
                <span class="inline-flex items-center gap-2 rounded-xl border border-gold-primary/20 bg-gold-primary/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-gold-primary">
                   🍽️ DINE IN
                </span>
             @elseif(($sale->order_type ?? '') === 'delivery')
                <span class="inline-flex items-center gap-2 rounded-xl border border-amber-500/20 bg-amber-500/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-amber-500 animate-pulse">
                   🚚 DELIVERY
                </span>
             @else
                <span class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-white/40">
                   🥡 TAKE AWAY
                </span>
             @endif
          </div>
          
          <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-3">
             @if(($sale->order_type ?? '') === 'dine_in')
                <div class="flex items-center justify-between">
                   <span class="text-[9px] text-white/30 uppercase font-black">Meja</span>
                   <span class="text-[10px] text-gold-primary font-black uppercase tracking-wider bg-gold-primary/10 px-2.5 py-0.5 rounded-lg border border-gold-primary/15">
                      {{ $sale->diningTable?->name ?? ('Meja #' . ($sale->dining_table_id ?? '-')) }}
                   </span>
                </div>
             @elseif(($sale->order_type ?? '') === 'delivery')
                <div class="space-y-3">
                   @php
                      $customerNameRaw = trim($sale->user?->name ?? '');
                      $customerName = $customerNameRaw ? str_replace('(Tamu)', '', $customerNameRaw) : '';
                   @endphp
                   @if($customerName)
                      <div class="flex items-center justify-between pb-2 border-b border-white/5">
                         <span class="text-[9px] text-white/30 uppercase font-black">Penerima</span>
                         <span class="text-[10px] text-white/80 font-bold">{{ $customerName }}</span>
                      </div>
                   @endif
                   @if($sale->delivery_phone)
                      <div class="flex items-center justify-between pb-2 border-b border-white/5">
                         <span class="text-[9px] text-white/30 uppercase font-black">Telepon</span>
                         <span class="text-[10px] text-white/80 font-bold">{{ $sale->delivery_phone }}</span>
                      </div>
                   @endif
                   @if($sale->delivery_distance_km)
                      <div class="flex items-center justify-between pb-2 border-b border-white/5">
                         <span class="text-[9px] text-white/30 uppercase font-black">Jarak</span>
                         <span class="text-[10px] text-white/80 font-bold">{{ number_format($sale->delivery_distance_km, 2) }} KM</span>
                      </div>
                   @endif
                   <div class="flex flex-col gap-1">
                      <span class="text-[9px] text-white/30 uppercase font-black">Alamat Detail</span>
                      <span class="text-[10px] text-white/70 leading-relaxed font-medium">{{ $sale->delivery_address ?? '-' }}</span>
                   </div>
                   @if($sale->delivery_lat && $sale->delivery_lng)
                      <div class="pt-2 border-t border-white/5">
                         <a href="https://www.google.com/maps?q={{ urlencode($sale->delivery_lat) }},{{ urlencode($sale->delivery_lng) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 text-[10px] font-bold text-gold-primary hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Buka Peta Lokasi
                         </a>
                      </div>
                   @endif
                </div>
             @else
                <div class="text-center py-2">
                   <p class="text-[10px] text-white/40 italic font-medium">Customer akan mengambil pesanan langsung di restaurant.</p>
                </div>
             @endif
          </div>
       </div>

       <!-- CASHIER INFO -->
       <div class="premium-card p-8 border-white/5 bg-white/[0.02] relative overflow-hidden group">
          <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
          
          <div class="flex items-center gap-3 mb-6">
             <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/40 border border-white/10 group-hover:border-gold-primary/30 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
             </div>
             <div>
                <h4 class="text-[9px] font-black text-white/20 uppercase tracking-widest">Petugas Kasir</h4>
                <p class="text-sm font-bold text-white italic">{{ $sale->cashier->name ?? 'Unknown System' }}</p>
             </div>
          </div>
          
          <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-2">
             <div class="flex items-center justify-between">
                <span class="text-[9px] text-white/30 uppercase font-black">Email</span>
                <span class="text-[10px] text-white/60 font-bold">{{ $sale->cashier->email ?? '-' }}</span>
             </div>
             <div class="flex items-center justify-between pt-2 border-t border-white/5">
                <span class="text-[9px] text-white/30 uppercase font-black">Waktu Transaksi</span>
                <span class="text-[10px] text-white/60 font-bold italic">{{ $sale->created_at?->format('d M Y • H:i') }}</span>
             </div>
          </div>
       </div>

       <!-- PAYMENT SUMMARY -->
       @php
         $dpp = (float) ($sale->items?->sum('subtotal') ?? 0);
         $deliveryFee = (float) ($sale->delivery_fee ?? 0);
         $tax = max(0, (float) ($sale->total_amount ?? 0) - $dpp - $deliveryFee);
       @endphp
       <div class="premium-card p-8 border-gold-primary/20 bg-gold-primary/[0.03] space-y-6">
          <h4 class="text-xs font-black text-gold-primary uppercase tracking-[0.2em]">Rincian Pembayaran</h4>
          
          <div class="space-y-4">
             <div class="flex items-center justify-between text-[11px] text-white/60">
                <span class="font-medium italic">Dasar Pengenaan Pajak (DPP)</span>
                <span class="font-bold">Rp {{ number_format($dpp, 0, ',', '.') }}</span>
             </div>
             <div class="flex items-center justify-between text-[11px] text-white/60">
                <span class="font-medium italic">Pajak Pertambahan Nilai</span>
                <span class="font-bold">Rp {{ number_format($tax, 0, ',', '.') }}</span>
             </div>
             @if($deliveryFee > 0)
             <div class="flex items-center justify-between text-[11px] text-white/60">
                <span class="font-medium italic">Ongkos Kirim (Delivery)</span>
                <span class="font-bold">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</span>
             </div>
             @endif
             
             <div class="pt-6 border-t border-white/10">
                <div class="flex flex-col gap-1">
                   <span class="text-[10px] text-gold-primary font-black uppercase tracking-widest">Total Bayar (Nett)</span>
                   <span class="text-4xl font-black text-white italic tracking-tighter">Rp {{ number_format($sale->total_amount ?? 0, 0, ',', '.') }}</span>
                </div>
             </div>

             <div class="grid grid-cols-2 gap-4 pt-6 border-t border-white/5">
                <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                   <p class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Diterima</p>
                   <p class="text-xs font-bold text-white italic">Rp {{ number_format($sale->paid_amount ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                   <p class="text-[8px] font-black text-white/20 uppercase tracking-widest mb-1">Kembalian</p>
                   <p class="text-xs font-bold text-gold-primary italic">Rp {{ number_format($sale->change_amount ?? 0, 0, ',', '.') }}</p>
                </div>
             </div>
          </div>
       </div>

       <!-- NOTES -->
       @if($sale->note)
       <div class="premium-card p-8 border-white/5 bg-white/[0.02] space-y-4">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white/30 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
             </div>
             <h4 class="text-[9px] font-black text-white/30 uppercase tracking-widest">Catatan Transaksi</h4>
          </div>
          <p class="text-[11px] text-white/60 leading-relaxed italic">"{{ $sale->note }}"</p>
       </div>
       @endif
    </div>
  </div>
@endsection
