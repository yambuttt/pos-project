@extends('layouts.kasir')
@section('title','Riwayat Transaksi')

@section('body')
  <div class="max-w-7xl mx-auto space-y-8 animate-fade-up" x-data="transactionHistory()">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-bold tracking-tight mb-1 text-white">Riwayat Transaksi</h1>
        <p class="text-white/40 text-sm">Daftar transaksi yang dilakukan melalui terminal ini.</p>
      </div>
      <a href="{{ route('kasir.sales.create') }}" class="btn-premium-primary text-sm px-8">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        Transaksi Baru
      </a>
    </div>

    @if(session('success'))
      <div class="rounded-2xl border border-accent-gold/20 bg-accent-gold/5 px-6 py-4 text-sm text-accent-gold flex items-center gap-3 animate-fade-up">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ session('success') }}
      </div>
    @endif

    {{-- Transactions Table --}}
    <div class="premium-card overflow-hidden border-white/5">
      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-white/5 bg-white/[0.02]">
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Waktu</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Invoice</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Metode</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Total</th>
              <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            @forelse($sales as $s)
              <tr class="hover:bg-white/[0.02] transition-colors group">
                <td class="px-6 py-6">
                  <div class="text-xs font-bold text-white/80">{{ $s->created_at->format('d M Y') }}</div>
                  <div class="text-[10px] text-white/20 mt-0.5 font-bold">{{ $s->created_at->format('H:i') }}</div>
                </td>
                <td class="px-6 py-6">
                  <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent-gold shadow-[0_0_8px_rgba(234,179,8,0.5)]"></span>
                    <span class="text-xs font-bold tracking-tight text-white">{{ $s->invoice_no }}</span>
                  </div>
                </td>
                <td class="px-6 py-6">
                  <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-white/40 group-hover:text-accent-gold transition-colors">
                    {{ str_replace('_', ' ', strtoupper($s->payment_method)) }}
                  </span>
                </td>
                <td class="px-6 py-6">
                  <div class="text-sm font-black text-accent-gold">Rp {{ number_format($s->total_amount,0,',','.') }}</div>
                </td>
                <td class="px-6 py-6">
                  <button @click="openModal({{ $s->id }})" class="btn-premium-glass px-4 py-2 text-[10px] rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Lihat Detail
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-20 text-center">
                  <div class="flex flex-col items-center opacity-10">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <p class="text-sm font-black uppercase tracking-[0.2em]">Belum ada transaksi</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-8">
      {{ $sales->links() }}
    </div>

    {{-- Transaction Detail Modal --}}
    <div x-show="showModal" style="display:none" class="fixed inset-0 z-[100] flex items-start justify-center p-4 lg:pl-72 pt-12 lg:pt-20">
        <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-black/95 backdrop-blur-md" @click="showModal = false"></div>
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="translate-y-8 opacity-0 scale-95" 
             x-transition:enter-end="translate-y-0 opacity-100 scale-100"
             class="relative bg-black border border-white/10 w-full max-w-2xl rounded-[2.5rem] overflow-hidden flex flex-col shadow-2xl max-h-[90vh]">
            
            {{-- Modal Header --}}
            <div class="p-6 lg:p-8 border-b border-white/5 flex items-start justify-between shrink-0">
                <div>
                    <h3 class="text-2xl font-bold tracking-tight text-white mb-2">Detail Transaksi</h3>
                    <div class="flex items-center gap-3">
                        <span class="text-accent-gold font-mono text-sm tracking-widest" x-text="selectedSale?.invoice_no"></span>
                        <span class="px-2 py-1 rounded bg-white/5 text-[10px] font-black uppercase tracking-widest text-white/40" x-text="selectedSale?.payment_method?.replace('_', ' ')"></span>
                    </div>
                </div>
                <button @click="showModal = false" class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center text-white/40 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 lg:p-8 flex-1 overflow-y-auto custom-scrollbar">
                <div class="space-y-6">
                    {{-- Basic Info --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Status Pembayaran</div>
                            <div class="text-sm font-bold" :class="selectedSale?.payment_status === 'paid' ? 'text-green-400' : 'text-yellow-400'" x-text="selectedSale?.payment_status?.toUpperCase()"></div>
                        </div>
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Tipe Pesanan</div>
                            <div class="text-sm font-bold text-white uppercase" x-text="selectedSale?.order_type?.replace('_', ' ')"></div>
                        </div>
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Status Dapur</div>
                            <div class="text-sm font-bold text-white uppercase" x-text="selectedSale?.kitchen_status"></div>
                        </div>
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                            <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Kasir</div>
                            <div class="text-sm font-bold text-white">{{ explode(' ', auth()->user()->name)[0] }}</div>
                        </div>
                    </div>

                    {{-- Items List --}}
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest text-white/40 mb-4">Daftar Pesanan</h4>
                        <div class="space-y-3">
                            <template x-for="item in selectedSale?.items" :key="item.id">
                                <div class="flex items-center justify-between p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                                    <div class="flex-1">
                                        <div class="font-bold text-white text-sm" x-text="item.product?.name || 'Produk Dihapus'"></div>
                                        <div class="text-[10px] text-white/40 mt-1" x-show="item.note">Catatan: <span x-text="item.note"></span></div>
                                    </div>
                                    <div class="text-right flex items-center gap-4">
                                        <div class="text-xs text-white/40 font-bold">x<span x-text="item.qty"></span></div>
                                        <div class="text-sm font-bold text-accent-gold w-24" x-text="fmtRp(item.subtotal)"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer (Total) --}}
            <div class="p-6 lg:p-8 bg-white/5 border-t border-white/5 shrink-0 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-widest text-white/40 mb-1">Total Dibayar</div>
                    <div class="text-sm font-bold text-white" x-text="fmtRp(selectedSale?.paid_amount)"></div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] font-black uppercase tracking-widest text-white/40 mb-1">Total Tagihan</div>
                    <div class="text-2xl font-black text-accent-gold" x-text="fmtRp(selectedSale?.total_amount)"></div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <script>
    function transactionHistory() {
        return {
            sales: @json($sales->items()),
            showModal: false,
            selectedSale: null,

            openModal(id) {
                this.selectedSale = this.sales.find(s => s.id === id);
                this.showModal = true;
            },

            fmtRp(v) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(v || 0);
            }
        }
    }
  </script>
@endsection


