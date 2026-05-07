@extends('toko.layouts.admin')

@section('title', 'Pusat Inventory Toko')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-fade-up">

    <!-- Header & Quick Actions -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div>
            <h2 class="text-3xl font-display font-bold text-white tracking-tight">Pusat Inventory</h2>
            <p class="text-white/50 text-sm mt-1 max-w-xl">Buku besar pergerakan stok toko. Catat barang masuk, keluar, dan pembuangan (waste) di satu tempat.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openModal('in')" class="bg-green-500/10 border border-green-500/30 hover:bg-green-500 hover:text-black text-green-400 px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-[0_0_15px_rgba(34,197,94,0.1)] hover:shadow-[0_0_20px_rgba(34,197,94,0.4)] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                + Barang Masuk
            </button>
            <button onclick="openModal('out')" class="bg-blue-500/10 border border-blue-500/30 hover:bg-blue-500 hover:text-white text-blue-400 px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-[0_0_15px_rgba(59,130,246,0.1)] hover:shadow-[0_0_20px_rgba(59,130,246,0.4)] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                - Barang Keluar
            </button>
            <button onclick="openModal('waste')" class="bg-red-500/10 border border-red-500/30 hover:bg-red-500 hover:text-white text-red-400 px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-[0_0_15px_rgba(239,68,68,0.1)] hover:shadow-[0_0_20px_rgba(239,68,68,0.4)] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Catat Waste
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <!-- Table Buku Besar -->
    <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Riwayat Pergerakan
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5 text-white/50 uppercase tracking-widest text-[10px]">
                        <th class="px-6 py-4 font-semibold">Waktu</th>
                        <th class="px-6 py-4 font-semibold">Jenis</th>
                        <th class="px-6 py-4 font-semibold">Produk / Item</th>
                        <th class="px-6 py-4 font-semibold text-center">Qty</th>
                        <th class="px-6 py-4 font-semibold text-center">Stok (Sblm -> Sdh)</th>
                        <th class="px-6 py-4 font-semibold">Oleh / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-white/80">
                    @forelse($movements as $mov)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4 text-white/50 text-xs">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            @if($mov->type == 'in')
                                <span class="px-2 py-1 bg-green-500/10 text-green-400 border border-green-500/20 rounded text-[10px] font-bold">MASUK</span>
                            @elseif($mov->type == 'out')
                                <span class="px-2 py-1 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded text-[10px] font-bold">KELUAR</span>
                            @elseif($mov->type == 'waste')
                                <span class="px-2 py-1 bg-red-500/10 text-red-400 border border-red-500/20 rounded text-[10px] font-bold">WASTE</span>
                            @elseif($mov->type == 'sale')
                                <span class="px-2 py-1 bg-purple-500/10 text-purple-400 border border-purple-500/20 rounded text-[10px] font-bold">TERJUAL</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 rounded text-[10px] font-bold">ADJUST</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-white">
                            @if($mov->item_type == \App\Models\TokoProduct::class)
                                {{ $mov->item->name ?? 'Unknown Product' }}
                                <span class="text-[10px] text-white/30 ml-2">(Produk)</span>
                            @else
                                {{ $mov->item->product->name ?? 'Unknown' }} - <span class="text-yellow-500">{{ $mov->item->name ?? 'Variant' }}</span>
                                <span class="text-[10px] text-white/30 ml-2">(Varian)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-white">{{ $mov->qty }}</td>
                        <td class="px-6 py-4 text-center font-mono text-xs">
                            <span class="text-white/40">{{ $mov->stock_before }}</span>
                            <span class="text-yellow-500 mx-2">→</span>
                            <span class="text-white">{{ $mov->stock_after }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white text-xs font-semibold">{{ $mov->creator->name ?? 'System' }}</div>
                            <div class="text-white/40 text-[10px] truncate max-w-[200px]" title="{{ $mov->notes }}">{{ $mov->notes ?? '-' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-white/30">Belum ada riwayat pergerakan inventaris.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-white/5">
            {{ $movements->links() }}
        </div>
    </div>

</div>

<!-- Universal Action Modal -->
<div id="actionModal" class="hidden fixed inset-0 z-[60] bg-black/80 flex items-center justify-center p-4">
    <div class="bg-[#121212] border border-white/10 rounded-3xl w-full max-w-lg shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden transform transition-all">
        
        <div id="modalHeader" class="px-6 py-5 border-b border-white/5 flex justify-between items-center bg-gradient-to-r">
            <h3 class="text-xl font-bold text-white flex items-center gap-3" id="modalTitle">
                Judul Modal
            </h3>
            <button onclick="closeModal()" class="text-white/50 hover:text-white transition-colors bg-black/20 p-2 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>

        <form action="{{ route('toko.movements.action') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="action_type" id="actionType">

            <!-- Item Selection (AlpineJS for dynamic variant loading) -->
            <div x-data="inventoryForm()">
                
                <h4 class="text-sm font-bold text-white mb-3">Daftar Barang</h4>
                <div class="space-y-4 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                    <template x-for="(item, index) in items" :key="item.id">
                        <div class="bg-black/40 p-4 rounded-xl border border-white/5 space-y-3 relative">
                            <!-- Tombol Hapus Baris -->
                            <button type="button" @click="if(items.length > 1) items.splice(index, 1)" class="absolute top-3 right-3 text-white/30 hover:text-red-500 transition-colors" x-show="items.length > 1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>

                            <!-- Select Product -->
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-white/50 mb-1">Produk Induk <span class="text-red-500">*</span></label>
                                <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="item.variant_id = ''" required class="w-full bg-black/50 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-yellow-500 outline-none appearance-none">
                                    <option value="">-- Pilih Produk --</option>
                                    <template x-for="p in products" :key="p.id">
                                        <option :value="p.id" x-text="p.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="flex gap-3">
                                <!-- Select Variant (Shown only if product has variants) -->
                                <div class="flex-1" x-show="hasVariants(item.product_id)" x-collapse>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-yellow-500/70 mb-1">Varian <span class="text-red-500">*</span></label>
                                    <select :name="'items['+index+'][variant_id]'" x-model="item.variant_id" :required="hasVariants(item.product_id)" class="w-full bg-black border border-yellow-500/30 rounded-lg px-3 py-2 text-white text-sm focus:border-yellow-500 outline-none appearance-none">
                                        <option value="">-- Pilih Varian --</option>
                                        <template x-for="v in getVariants(item.product_id)" :key="v.id">
                                            <option :value="v.id" x-text="v.name + ' (Stok: ' + v.stock + ')'"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Qty -->
                                <div class="w-24 shrink-0">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-white/50 mb-1">Qty <span class="text-red-500">*</span></label>
                                    <input type="number" :name="'items['+index+'][qty]'" x-model="item.qty" min="1" required class="w-full bg-black/50 border border-white/10 rounded-lg px-3 py-2 text-white text-center text-sm focus:border-yellow-500 outline-none">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" @click="items.push({id: Date.now(), product_id: '', variant_id: '', qty: 1})" class="text-yellow-500 hover:text-yellow-400 text-sm font-bold flex items-center gap-2 mt-4 bg-yellow-500/10 px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Baris Barang (Bulk)
                </button>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-white/50 mb-2">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Cth: Barang dari supplier A, atau Barang kadaluarsa..." class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 outline-none transition-all"></textarea>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-white/70 hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                <button type="submit" id="submitBtn" class="flex-1 px-4 py-3 rounded-xl text-black font-bold shadow-lg transition-colors">Simpan Catatan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('inventoryForm', () => ({
            products: @json($products),
            items: [{id: Date.now(), product_id: '', variant_id: '', qty: 1}],
            hasVariants(productId) { 
                const p = this.products.find(x => x.id == productId);
                return p && (p.has_variants == 1 || p.has_variants === true); 
            },
            getVariants(productId) {
                const p = this.products.find(x => x.id == productId);
                return p ? p.variants : [];
            }
        }));
    });

    function openModal(type) {
        const modal = document.getElementById('actionModal');
        const header = document.getElementById('modalHeader');
        const title = document.getElementById('modalTitle');
        const btn = document.getElementById('submitBtn');
        document.getElementById('actionType').value = type;

        // Reset classes
        header.className = 'px-6 py-5 border-b border-white/5 flex justify-between items-center bg-gradient-to-r';
        btn.className = 'flex-1 px-4 py-3 rounded-xl text-black font-bold shadow-lg transition-colors';

        if (type === 'in') {
            title.innerHTML = '<svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> Input Barang Masuk';
            header.classList.add('from-green-500/20', 'to-transparent');
            btn.classList.add('bg-green-500', 'hover:bg-green-400');
            btn.innerText = 'Simpan Barang Masuk';
        } else if (type === 'out') {
            title.innerHTML = '<svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg> Catat Barang Keluar';
            header.classList.add('from-blue-500/20', 'to-transparent');
            btn.classList.add('bg-blue-500', 'hover:bg-blue-400', 'text-white');
            btn.innerText = 'Simpan Barang Keluar';
        } else if (type === 'waste') {
            title.innerHTML = '<svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Catat Produk Waste';
            header.classList.add('from-red-500/20', 'to-transparent');
            btn.classList.add('bg-red-500', 'hover:bg-red-400', 'text-white');
            btn.innerText = 'Simpan Data Waste';
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('actionModal').classList.add('hidden');
    }
</script>
@endsection
