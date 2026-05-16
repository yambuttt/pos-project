@extends('layouts.admin')
@section('title', 'Buat Data Waste')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Input Data Waste</h1>
      <p class="text-sm text-white/40 font-medium italic">Catat pembuangan stok yang <span class="text-red-400 font-bold not-italic">rusak, basi, atau tidak layak jual.</span></p>
    </div>

    <a href="{{ route('admin.wastes.index') }}"
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
      <div class="flex flex-col">
        <p class="text-sm font-bold text-red-100">Gagal menyimpan data:</p>
        <ul class="list-disc list-inside text-[11px] text-red-300/80 mt-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.wastes.store') }}" class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_380px]">
    @csrf

    <div class="space-y-8">
      <!-- MAIN INFO PANEL -->
      <div class="glass-panel p-8 rounded-[2.5rem] space-y-8">
        <div class="flex items-center gap-3 mb-2">
           <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
           </div>
           <h3 class="text-lg font-bold text-white uppercase tracking-widest">Informasi Waste</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Tanggal Kejadian</label>
            <input type="date" name="waste_date" value="{{ old('waste_date', date('Y-m-d')) }}" required
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-red-500/30 focus:bg-white/[0.04] transition-all">
          </div>

          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Alasan (Reason)</label>
            <select name="reason" required
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-red-500/30 focus:bg-white/[0.04] transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23D4AF37%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right:1.5rem_center] bg-no-repeat">
              <option value="">-- Pilih Alasan --</option>
              <option value="expired" {{ old('reason') == 'expired' ? 'selected' : '' }}>Expired / Kadaluarsa</option>
              <option value="spoil" {{ old('reason') == 'spoil' ? 'selected' : '' }}>Basi / Spoiled</option>
              <option value="spill" {{ old('reason') == 'spill' ? 'selected' : '' }}>Tumpah / Pecah / Spill</option>
              <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Lainnya</option>
            </select>
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Keterangan Tambahan</label>
          <textarea name="note" rows="2" placeholder="Tulis kronologi singkat jika diperlukan..."
            class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-red-500/30 focus:bg-white/[0.04] transition-all">{{ old('note') }}</textarea>
        </div>
      </div>

      <!-- ITEMS PANEL -->
      <div class="glass-panel p-8 rounded-[2.5rem] space-y-6">
        <div class="flex items-center justify-between mb-2">
           <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                 </svg>
              </div>
              <h3 class="text-lg font-bold text-white uppercase tracking-widest">Detail Bahan Terbuang</h3>
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
       <div class="premium-card p-8 border-red-500/20 bg-red-500/[0.03] space-y-6">
          <h4 class="text-xs font-black text-red-400 uppercase tracking-[0.2em]">Estimasi Kerugian</h4>
          
          <div class="space-y-4">
             <div class="flex items-center justify-between">
                <span class="text-xs text-white/40 font-medium italic">Akumulasi Item</span>
                <span class="text-sm font-bold text-white" id="summarySubtotal">Rp 0</span>
             </div>
             <div class="pt-4 border-t border-white/5">
                <div class="flex flex-col gap-1">
                   <span class="text-[10px] text-red-400 font-black uppercase tracking-widest">Total Rugi</span>
                   <span class="text-3xl font-black text-white" id="grandTotal">Rp 0</span>
                </div>
             </div>
          </div>

          <button class="w-full rounded-2xl bg-gradient-to-r from-red-600 to-red-800 px-6 py-5 text-[11px] font-black text-white uppercase tracking-widest shadow-xl shadow-red-900/40 hover:scale-[1.02] transition-all active:scale-95 border border-red-400/20">
             Simpan Data Waste
          </button>
       </div>

       <div class="p-6 rounded-3xl border border-gold-primary/20 bg-gold-primary/5 space-y-4">
          <div class="flex items-center gap-3 text-gold-primary mb-2">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
             </svg>
             <h4 class="text-[10px] font-black uppercase tracking-[0.2em]">Peringatan Sistem</h4>
          </div>
          <p class="text-[10px] text-white/50 leading-relaxed font-medium">Sistem beroperasi dalam mode <span class="text-white font-bold italic">Strict.</span> Anda tidak diizinkan mencatat waste melebihi sisa stok bahan baku yang tersedia saat ini.</p>
       </div>

       <div class="glass-panel p-6 border-white/5 rounded-3xl flex items-center gap-4">
          <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white/40 font-black">
             {{ substr(auth()->user()->name, 0, 1) }}
          </div>
          <div class="overflow-hidden">
             <p class="text-[9px] font-black text-white/20 uppercase tracking-widest">Petugas</p>
             <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
          </div>
       </div>
    </div>
  </form>

  <script>
    (function () {
      const materials = @json($materialsJson);
      const itemsWrap = document.getElementById('itemsWrap');
      const addRowBtn = document.getElementById('addRow');
      const grandTotalEl = document.getElementById('grandTotal');
      const summarySubtotalEl = document.getElementById('summarySubtotal');

      function money(n) { 
        n = Number(n || 0); 
        return 'Rp ' + Math.round(n).toLocaleString('id-ID'); 
      }

      function recalc() {
        let total = 0;
        const rows = itemsWrap.querySelectorAll('[data-row]');
        rows.forEach(row => {
          const qtyInput = row.querySelector('[data-qty]');
          const costInput = row.querySelector('[data-cost]');
          const subText = row.querySelector('[data-sub]');

          const qty = parseFloat(qtyInput.value) || 0;
          const cost = parseFloat(costInput.value) || 0;
          const sub = qty * cost;
          
          if(subText) subText.textContent = money(sub);
          total += sub;
        });
        if(grandTotalEl) grandTotalEl.textContent = money(total);
        if(summarySubtotalEl) summarySubtotalEl.textContent = money(total);
      }

      function rowTemplate(idx) {
        const options = materials.map(m => `<option value="${m.id}" data-unitcost="${m.default_cost || 0}">${m.name} (${m.unit}) • Stok: ${m.stock}</option>`).join('');
        
        return `
          <div data-row class="premium-card p-6 border-white/5 group hover:border-red-500/20 transition-all">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-[1.5fr_1fr_1.2fr_auto] items-end">
              <div class="space-y-2">
                <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Pilih Bahan Baku</label>
                <select name="items[${idx}][raw_material_id]" required
                  class="material-select w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-red-500/20 transition-all appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23D4AF37%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:1.1rem_1.1rem] bg-[right:1rem_center] bg-no-repeat">
                  <option value="">-- Pilih Bahan --</option>
                  ${options}
                </select>
              </div>

              <div class="space-y-2">
                <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Jumlah Waste (Qty)</label>
                <input data-qty name="items[${idx}][qty]" type="number" step="0.01" value="1" required
                  class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-red-500/20 transition-all" />
              </div>

              <div class="space-y-2">
                <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Estimasi Biaya / Unit (Rp)</label>
                <input data-cost name="items[${idx}][estimated_cost]" type="number" step="0.01" value="0" required
                  class="w-full rounded-xl border border-white/5 bg-white/[0.02] px-4 py-3 text-sm text-white outline-none focus:border-red-500/20 transition-all" />
              </div>

              <div class="flex items-center gap-6">
                <div class="text-right flex-1 md:flex-none">
                   <p class="text-[8px] uppercase tracking-widest text-white/20 font-black mb-1">Sub-total Rugi</p>
                   <p class="text-sm font-black text-red-400" data-sub>${money(0)}</p>
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
        const temp = document.createElement('div');
        temp.innerHTML = rowTemplate(idx).trim();
        const row = temp.firstChild;
        itemsWrap.appendChild(row);

        const select = row.querySelector('.material-select');
        const qtyInp = row.querySelector('[data-qty]');
        const costInp = row.querySelector('[data-cost]');
        const removeBtn = row.querySelector('[data-remove]');

        select.addEventListener('change', (e) => {
           const opt = e.target.options[e.target.selectedIndex];
           const cost = opt.dataset.unitcost || 0;
           costInp.value = cost;
           recalc();
        });

        qtyInp.addEventListener('input', recalc);
        costInp.addEventListener('input', recalc);
        removeBtn.addEventListener('click', () => {
           row.remove();
           recalc();
        });

        idx++;
        recalc();
      }

      addRowBtn.addEventListener('click', addRow);
      
      // Initialize first row
      addRow();
    })();
  </script>
@endsection