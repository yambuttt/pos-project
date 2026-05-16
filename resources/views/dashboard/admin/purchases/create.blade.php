@extends('layouts.admin')
@section('title', 'Buat Purchase')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Buat Purchase Baru</h1>
      <p class="text-sm text-white/40 font-medium">Input stok masuk dari supplier atau pembelian external lainnya.</p>
    </div>

    <a href="{{ route('admin.purchases.index') }}"
      class="flex items-center gap-2 rounded-2xl bg-white/5 px-6 py-3.5 text-xs font-black text-white border border-white/10 hover:bg-white/10 transition-all active:scale-95 uppercase tracking-widest">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Batal & Kembali
    </a>
  </div>

  @if($errors->any())
    <div class="mb-6 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-red-100">{{ $errors->first() }}</p>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.purchases.store') }}" class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_380px]">
    @csrf

    <div class="space-y-8">
      <!-- MAIN INFO PANEL -->
      <div class="glass-panel p-8 rounded-[2.5rem] space-y-8">
        <div class="flex items-center gap-3 mb-2">
           <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
           </div>
           <h3 class="text-lg font-bold text-white uppercase tracking-widest">Informasi Dasar</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Tanggal Transaksi</label>
            <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>

          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Nomor Faktur / Invoice</label>
            <input name="invoice_no" value="{{ old('invoice_no') }}" placeholder="INV/2026/05/..."
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>

          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Tipe Sumber</label>
            <select id="source_type" name="source_type"
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23D4AF37%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1.5rem_center] bg-no-repeat">
              <option value="external" {{ old('source_type') === 'external' ? 'selected' : '' }}>External (Pasar / Random)</option>
              <option value="supplier" {{ old('source_type') === 'supplier' ? 'selected' : '' }}>Official Supplier</option>
            </select>
          </div>

          <div id="supplier_wrap" class="space-y-2 hidden">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Pilih Supplier</label>
            <select name="supplier_id"
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23D4AF37%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1.5rem_center] bg-no-repeat">
              <option value="">-- Cari Supplier --</option>
              @foreach($suppliers as $s)
                <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>

          <div id="external_wrap" class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Nama Sumber External</label>
            <input name="source_name" value="{{ old('source_name') }}" placeholder="Contoh: Pasar Tradisional"
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Catatan Internal</label>
          <textarea name="note" rows="2" placeholder="Tulis catatan penting jika ada..."
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">{{ old('note') }}</textarea>
        </div>
      </div>

      <!-- ITEMS PANEL -->
      <div class="glass-panel p-8 rounded-[2.5rem] space-y-6">
        <div class="flex items-center justify-between mb-2">
           <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                 </svg>
              </div>
              <h3 class="text-lg font-bold text-white uppercase tracking-widest">Detail Item Barang</h3>
           </div>
           
           <button type="button" id="addRow"
             class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gold-primary text-obsidian-950 text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-gold-primary/20 hover:scale-[1.02] transition-all active:scale-95">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
             </svg>
             Tambah Baris
           </button>
        </div>

        <div class="space-y-4" id="itemsWrap">
           <!-- DYNAMIC ROWS -->
        </div>
      </div>
    </div>

    <!-- SIDEBAR SUMMARY -->
    <div class="space-y-8">
       <div class="premium-card p-8 border-gold-primary/20 bg-gold-primary/[0.03] space-y-6">
          <h4 class="text-xs font-black text-gold-primary uppercase tracking-[0.2em]">Ringkasan Total</h4>
          
          <div class="space-y-4">
             <div class="flex items-center justify-between">
                <span class="text-xs text-white/40 font-medium">Sub-total</span>
                <span class="text-sm font-bold text-white" id="summarySubtotal">Rp 0</span>
             </div>
             <div class="flex items-center justify-between">
                <span class="text-xs text-white/40 font-medium">Pajak / Lainnya</span>
                <span class="text-sm font-bold text-white">Rp 0</span>
             </div>
             <div class="pt-4 border-t border-white/5">
                <div class="flex flex-col gap-1">
                   <span class="text-[10px] text-gold-primary font-black uppercase tracking-widest">Total Pembelian</span>
                   <span class="text-3xl font-black text-white" id="grandTotal">Rp 0</span>
                </div>
             </div>
          </div>

          <button class="w-full rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-5 text-[11px] font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
             Simpan Transaksi
          </button>
       </div>

       <div class="glass-panel p-6 border-white/5 rounded-3xl space-y-4">
          <h4 class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">Petugas Input</h4>
          <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/[0.02] border border-white/5">
             <div class="w-10 h-10 rounded-full bg-gold-primary flex items-center justify-center text-obsidian-950 font-black">
                {{ substr(auth()->user()->name, 0, 1) }}
             </div>
             <div class="overflow-hidden">
                <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-white/30 truncate">{{ auth()->user()->email }}</p>
             </div>
          </div>
       </div>

       <div class="p-6 rounded-3xl border border-white/5 bg-white/5">
          <h4 class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-3">Informasi Sistem</h4>
          <ul class="space-y-3">
             <li class="flex items-start gap-3">
                <div class="mt-1 w-1.5 h-1.5 rounded-full bg-gold-primary shrink-0"></div>
                <p class="text-[10px] text-white/40 font-medium leading-relaxed">Stok bahan baku akan bertambah secara otomatis setelah transaksi disimpan.</p>
             </li>
             <li class="flex items-start gap-3">
                <div class="mt-1 w-1.5 h-1.5 rounded-full bg-gold-primary shrink-0"></div>
                <p class="text-[10px] text-white/40 font-medium leading-relaxed">Sistem akan mencatat riwayat pergerakan stok (*Stock Movements*) sebagai referensi audit.</p>
             </li>
          </ul>
       </div>
    </div>
  </form>

  <script>
    (function () {
      const sourceType = document.getElementById('source_type');
      const supplierWrap = document.getElementById('supplier_wrap');
      const externalWrap = document.getElementById('external_wrap');

      function syncSourceUI() {
        const val = sourceType.value;
        if (val === 'supplier') {
          supplierWrap.classList.remove('hidden');
          externalWrap.classList.add('hidden');
        } else {
          externalWrap.classList.remove('hidden');
          supplierWrap.classList.add('hidden');
        }
      }
      sourceType.addEventListener('change', syncSourceUI);
      syncSourceUI();

      const materials = @json($materials->map(fn($m)=>[
        'id'=>$m->id,'name'=>$m->name,'unit'=>$m->unit
      ])->values());

      const itemsWrap = document.getElementById('itemsWrap');
      const addRowBtn = document.getElementById('addRow');
      const grandTotalEl = document.getElementById('grandTotal');
      const summarySubtotalEl = document.getElementById('summarySubtotal');

      function money(n){
        n = Number(n||0);
        return 'Rp ' + n.toLocaleString('id-ID');
      }

      function recalc() {
        let total = 0;
        itemsWrap.querySelectorAll('[data-row]').forEach(row => {
          const qty = Number(row.querySelector('[data-qty]').value || 0);
          const unitCost = Number(row.querySelector('[data-unitcost]').value || 0);
          const sub = qty * unitCost;
          row.querySelector('[data-subtotal]').textContent = money(sub);
          total += sub;
        });
        grandTotalEl.textContent = money(total);
        summarySubtotalEl.textContent = money(total);
      }

      function rowTemplate(idx) {
        const options = materials.map(m => `<option value="${m.id}">${m.name} (${m.unit})</option>`).join('');
        return `
          <div data-row class="premium-card p-6 border-white/5 group hover:border-gold-primary/20 transition-all">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-[1.5fr_1fr_1.2fr_auto] items-end">
              <div class="space-y-2">
                <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Pilih Bahan Baku</label>
                <select name="items[${idx}][raw_material_id]" required
                  class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-gold-primary/20 transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23D4AF37%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.1rem_1.1rem] bg-[right_1rem_center] bg-no-repeat">
                  <option value="">-- Pilih Bahan --</option>
                  ${options}
                </select>
              </div>

              <div class="space-y-2">
                <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Jumlah (Qty)</label>
                <input data-qty name="items[${idx}][qty]" type="number" step="0.01" value="1"
                  class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-gold-primary/20 transition-all" />
              </div>

              <div class="space-y-2">
                <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Harga Satuan (Rp)</label>
                <input data-unitcost name="items[${idx}][unit_cost]" type="number" step="0.01" value="0"
                  class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-gold-primary/20 transition-all" />
              </div>

              <div class="flex items-center gap-6">
                <div class="text-right flex-1 md:flex-none">
                   <p class="text-[8px] uppercase tracking-widest text-white/20 font-black mb-1">Sub-total</p>
                   <p class="text-sm font-black text-gold-primary" data-subtotal>${money(0)}</p>
                </div>
                <button type="button" data-remove
                  class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        `;
      }

      let idx = 0;
      function addRow() {
        itemsWrap.insertAdjacentHTML('beforeend', rowTemplate(idx));
        const last = itemsWrap.lastElementChild;

        last.querySelector('[data-remove]').addEventListener('click', () => {
          last.remove();
          recalc();
        });

        last.querySelectorAll('input').forEach(inp => inp.addEventListener('input', recalc));
        idx++;
        recalc();
      }

      addRowBtn.addEventListener('click', addRow);
      addRow();
    })();
  </script>
@endsection
