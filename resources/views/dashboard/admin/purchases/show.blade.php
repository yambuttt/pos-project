@extends('layouts.admin')
@section('title', 'Detail Purchase')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <div class="flex items-center gap-3 mb-1">
        <h1 class="text-3xl font-bold text-gold-gradient">Detail Transaksi</h1>
        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/5 text-[10px] font-black text-gold-primary uppercase tracking-widest">
          #{{ $purchase->id }}
        </span>
      </div>
      <p class="text-sm text-white/40 font-medium italic">Faktur: <span class="text-white/60 font-bold not-italic">{{ $purchase->invoice_no ?? 'Tanpa Nomor Invoice' }}</span></p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.purchases.export.pdf', $purchase) }}"
        class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-xs font-black text-white border border-white/10 hover:bg-white/10 transition-all active:scale-95 uppercase tracking-widest">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gold-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
        </svg>
        Export PDF
      </a>
      <a href="{{ route('admin.purchases.index') }}"
        class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-xs font-black text-white border border-white/10 hover:bg-white/10 transition-all active:scale-95 uppercase tracking-widest">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_380px]">
    <!-- ITEMS LIST -->
    <div class="space-y-6">
      <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/5">
        <!-- DESKTOP TABLE -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-white/40 font-black border-b border-white/5">
                <th class="px-8 py-6">Nama Bahan</th>
                <th class="px-6 py-6">Kuantitas</th>
                <th class="px-6 py-6">Harga Satuan</th>
                <th class="px-8 py-6 text-right">Sub-total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              @forelse($purchase->items ?? [] as $it)
                <tr class="group hover:bg-white/[0.01] transition-colors">
                  <td class="px-8 py-6">
                    <div class="text-sm font-bold text-white">{{ $it->rawMaterial->name ?? '-' }}</div>
                    <div class="text-[10px] text-white/30 font-bold uppercase tracking-tight">{{ $it->rawMaterial->unit ?? '' }}</div>
                  </td>
                  <td class="px-6 py-6">
                    <span class="text-sm font-bold text-white/80">
                      {{ rtrim(rtrim(number_format($it->qty ?? 0, 3, '.', ''), '0'), '.') }}
                    </span>
                    <span class="text-[10px] text-white/30 ml-1">{{ $it->rawMaterial->unit ?? '' }}</span>
                  </td>
                  <td class="px-6 py-6 text-sm font-medium text-white/60">
                    Rp {{ number_format($it->unit_cost ?? 0, 0, ',', '.') }}
                  </td>
                  <td class="px-8 py-6 text-right">
                    <div class="text-sm font-black text-gold-primary italic">Rp {{ number_format($it->subtotal ?? 0, 0, ',', '.') }}</div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="px-8 py-10 text-center text-white/30 italic">Tidak ada item terdaftar.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- MOBILE LIST -->
        <div class="md:hidden divide-y divide-white/5">
           @foreach($purchase->items ?? [] as $it)
             <div class="p-6 space-y-4">
                <div class="flex items-start justify-between gap-4">
                   <div>
                      <h4 class="text-sm font-bold text-white">{{ $it->rawMaterial->name ?? '-' }}</h4>
                      <p class="text-[10px] text-white/30 font-bold uppercase tracking-widest">{{ $it->rawMaterial->unit ?? '' }}</p>
                   </div>
                   <div class="text-right">
                      <p class="text-sm font-black text-gold-primary italic">Rp {{ number_format($it->subtotal ?? 0, 0, ',', '.') }}</p>
                      <p class="text-[9px] text-white/20 uppercase tracking-tighter mt-1">Sub-total</p>
                   </div>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                   <div class="flex flex-col">
                      <span class="text-[8px] uppercase tracking-[0.2em] text-white/30 font-black mb-0.5">Quantity</span>
                      <span class="text-xs font-bold text-white/80">{{ rtrim(rtrim(number_format($it->qty ?? 0, 3, '.', ''), '0'), '.') }} {{ $it->rawMaterial->unit ?? '' }}</span>
                   </div>
                   <div class="text-right flex flex-col">
                      <span class="text-[8px] uppercase tracking-[0.2em] text-white/30 font-black mb-0.5">Unit Price</span>
                      <span class="text-xs font-bold text-white/80">Rp {{ number_format($it->unit_cost ?? 0, 0, ',', '.') }}</span>
                   </div>
                </div>
             </div>
           @endforeach
        </div>
      </div>

      <!-- NOTES -->
      <div class="glass-panel p-8 rounded-[2.5rem] border-white/5">
         <h4 class="text-[10px] font-black text-gold-primary uppercase tracking-[0.2em] mb-4">Catatan Transaksi</h4>
         <p class="text-sm text-white/60 leading-relaxed italic">
            "{{ $purchase->note ?? 'Tidak ada catatan tambahan untuk transaksi ini.' }}"
         </p>
      </div>
    </div>

    <!-- SUMMARY SIDEBAR -->
    <div class="space-y-8">
       <!-- SOURCE INFO -->
       <div class="premium-card p-8 border-white/5 space-y-6">
          <h4 class="text-xs font-black text-gold-primary uppercase tracking-[0.2em]">Informasi Sumber</h4>
          
          <div class="space-y-4">
             <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-white/40">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                   </svg>
                </div>
                <div>
                   <p class="text-[9px] uppercase tracking-widest text-white/30 font-black mb-1">Sumber Pembelian</p>
                   <p class="text-sm font-bold text-white">
                      @if($purchase->source_type === 'supplier')
                        {{ $purchase->supplier?->name ?? '-' }}
                      @else
                        {{ $purchase->source_name ?? '-' }}
                      @endif
                   </p>
                   <span class="text-[9px] text-gold-primary/60 font-black uppercase tracking-[0.2em]">{{ $purchase->source_type ?? 'External' }}</span>
                </div>
             </div>

             <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-white/40">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                   </svg>
                </div>
                <div>
                   <p class="text-[9px] uppercase tracking-widest text-white/30 font-black mb-1">Tanggal Input</p>
                   <p class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</p>
                </div>
             </div>
          </div>
       </div>

       <!-- TOTAL BOX -->
       <div class="premium-card p-8 border-gold-primary/30 bg-gold-primary/5 space-y-6">
          <div class="flex flex-col gap-1">
             <span class="text-[10px] text-gold-primary font-black uppercase tracking-[0.2em]">Total Transaksi</span>
             <span class="text-4xl font-black text-white">Rp {{ number_format($purchase->total_amount ?? 0, 0, ',', '.') }}</span>
          </div>

          <div class="space-y-3 pt-6 border-t border-white/5">
             <div class="flex items-center justify-between text-[10px] uppercase tracking-widest font-black">
                <span class="text-white/30">Sub-total Item</span>
                <span class="text-white/60">Rp {{ number_format($purchase->items?->sum('subtotal') ?? 0, 0, ',', '.') }}</span>
             </div>
             @php
                $diff = (float)$purchase->total_amount - (float)($purchase->items?->sum('subtotal') ?? 0);
             @endphp
             @if(abs($diff) > 1)
               <div class="flex items-center justify-between text-[10px] uppercase tracking-widest font-black">
                  <span class="text-white/30">Selisih/Lainnya</span>
                  <span class="text-gold-primary/60">Rp {{ number_format($diff, 0, ',', '.') }}</span>
               </div>
             @endif
          </div>
       </div>

       <!-- ADMIN INFO -->
       <div class="glass-panel p-6 border-white/5 rounded-3xl flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-gold-primary/10 flex items-center justify-center text-gold-primary font-black shadow-inner">
             {{ substr($purchase->creator?->name ?? 'A', 0, 1) }}
          </div>
          <div class="overflow-hidden">
             <p class="text-[9px] font-black text-white/20 uppercase tracking-widest mb-0.5">Petugas Input</p>
             <p class="text-sm font-bold text-white truncate">{{ $purchase->creator?->name ?? 'System Admin' }}</p>
             <p class="text-[10px] text-white/30 font-medium italic truncate">{{ $purchase->created_at?->format('d M Y, H:i') }}</p>
          </div>
       </div>
    </div>
  </div>
@endsection