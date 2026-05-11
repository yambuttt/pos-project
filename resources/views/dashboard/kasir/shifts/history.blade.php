@extends('layouts.kasir')
@section('title', 'Riwayat Shift')

@section('body')
<div class="max-w-7xl mx-auto space-y-8 animate-fade-up">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Riwayat <span class="text-accent-gold">Shift</span></h1>
            <p class="text-white/40 text-sm">Daftar shift yang telah diselesaikan.</p>
        </div>
        <a href="{{ route('kasir.dashboard') }}" class="glass-panel px-6 py-2.5 rounded-xl text-white/70 hover:text-white border border-white/10 text-sm font-bold transition-all duration-300 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="glass-panel rounded-[2.5rem] overflow-hidden border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/10">
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Waktu</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-white/40 text-right">Modal Awal</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-white/40 text-right">Sales Cash</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-white/40 text-right">Sales QRIS</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-white/40 text-right">Total Akhir</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-white/40 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($shifts as $shift)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-6">
                            <div class="text-sm font-bold text-white mb-1">{{ $shift->start_time->format('d M Y') }}</div>
                            <div class="text-[10px] text-white/30 font-black tracking-widest uppercase">
                                {{ $shift->start_time->format('H:i') }} - {{ $shift->end_time ? $shift->end_time->format('H:i') : 'Sekarang' }}
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            @if($shift->status === 'open')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-500/10 text-green-500 text-[10px] font-black uppercase tracking-widest border border-green-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 text-white/40 text-[10px] font-black uppercase tracking-widest border border-white/10">
                                    Selesai
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right font-medium text-white/60">
                            Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-6 text-right font-bold text-white">
                            Rp {{ number_format($shift->total_sales_cash, 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-6 text-right font-medium text-white/60">
                            Rp {{ number_format($shift->total_sales_non_cash, 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="text-sm font-black text-accent-gold">
                                Rp {{ number_format($shift->ending_cash ?? ($shift->starting_cash + $shift->total_sales_cash), 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <button onclick="viewShiftDetails({{ $shift->id }})" class="p-2.5 rounded-xl bg-white/5 text-white/40 hover:bg-accent-gold/20 hover:text-accent-gold transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4 text-white/20">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="text-sm font-bold uppercase tracking-[0.2em]">Belum ada riwayat shift</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($shifts->hasPages())
        <div class="px-8 py-6 border-t border-white/5">
            {{ $shifts->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Detail Shift Modal --}}
<div id="detailModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="glass-panel w-full max-w-2xl p-8 relative z-10 border-white/10 rounded-[2.5rem] animate-scale-up max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-bold">Detail <span class="text-accent-gold">Shift</span></h3>
            <button onclick="this.closest('#detailModal').classList.add('hidden')" class="text-white/30 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="modalLoading" class="py-20 flex flex-col items-center gap-4 text-white/20">
            <div class="w-10 h-10 border-4 border-accent-gold/20 border-t-accent-gold rounded-full animate-spin"></div>
            <div class="text-[10px] font-black uppercase tracking-widest">Memuat data...</div>
        </div>

        <div id="modalContent" class="hidden space-y-8">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                    <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Status</div>
                    <div id="detStatus" class="text-sm font-bold"></div>
                </div>
                <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                    <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Mulai</div>
                    <div id="detStart" class="text-sm font-bold text-white"></div>
                </div>
                <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                    <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Selesai</div>
                    <div id="detEnd" class="text-sm font-bold text-white"></div>
                </div>
                <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                    <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-1">Transaksi</div>
                    <div id="detTrx" class="text-sm font-bold text-white"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="glass-panel p-6 border-white/5">
                    <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-4">Ringkasan Kas</div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-white/50">Modal Awal</span>
                            <span id="detStartingCash" class="font-bold text-white"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-white/50">Sales (Cash)</span>
                            <span id="detSalesCash" class="font-bold text-white"></span>
                        </div>
                        <div class="h-px bg-white/10"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-white/50 text-sm font-bold">Total Akhir</span>
                            <span id="detEndingCash" class="text-lg font-black text-accent-gold"></span>
                        </div>
                    </div>
                </div>
                <div class="glass-panel p-6 border-white/5">
                    <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-4">Non-Tunai</div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-white/50">Sales (QRIS/Lainnya)</span>
                            <span id="detSalesNonCash" class="font-bold text-white"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-4 px-2">Item Terjual</div>
                <div class="bg-white/5 rounded-2xl overflow-hidden border border-white/5">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white/5">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-white/30">Produk</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-white/30 text-center">Qty</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-white/30 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detItemsTable" class="divide-y divide-white/5">
                            {{-- Inject by JS --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function viewShiftDetails(id) {
        const modal = document.getElementById('detailModal');
        const loader = document.getElementById('modalLoading');
        const content = document.getElementById('modalContent');
        
        modal.classList.remove('hidden');
        loader.classList.remove('hidden');
        content.classList.add('hidden');

        fetch(`/kasir/shift/${id}`)
            .then(res => res.json())
            .then(data => {
                const shift = data.shift;
                const items = data.items_sold;

                document.getElementById('detStatus').innerHTML = shift.status === 'open' 
                    ? '<span class="text-green-500">AKTIF</span>' 
                    : '<span class="text-white/40">SELESAI</span>';
                
                document.getElementById('detStart').innerText = formatTime(shift.start_time);
                document.getElementById('detEnd').innerText = shift.end_time ? formatTime(shift.end_time) : '-';
                document.getElementById('detTrx').innerText = shift.total_transactions + ' trx';
                
                document.getElementById('detStartingCash').innerText = formatIDR(shift.starting_cash);
                document.getElementById('detSalesCash').innerText = formatIDR(shift.total_sales_cash);
                document.getElementById('detEndingCash').innerText = formatIDR(shift.ending_cash || (parseFloat(shift.starting_cash) + parseFloat(shift.total_sales_cash)));
                document.getElementById('detSalesNonCash').innerText = formatIDR(shift.total_sales_non_cash);

                const tbody = document.getElementById('detItemsTable');
                tbody.innerHTML = '';
                items.forEach(it => {
                    tbody.innerHTML += `
                        <tr class="text-sm">
                            <td class="px-6 py-4 text-white font-medium">${it.product_name}</td>
                            <td class="px-6 py-4 text-white/50 text-center">${it.total_qty}</td>
                            <td class="px-6 py-4 text-white font-bold text-right">${formatIDR(it.total_amount)}</td>
                        </tr>
                    `;
                });

                loader.classList.add('hidden');
                content.classList.remove('hidden');
            });
    }

    function formatIDR(val) {
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    }

    function formatTime(iso) {
        const d = new Date(iso);
        return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
    }
</script>
@endsection
