@extends('layouts.admin')
@section('title', 'Buat Stock Opname')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Formulir Stock Opname</h1>
      <p class="text-sm text-white/40 font-medium italic">Koreksi perbedaan stok fisik vs catatan sistem. Simpan sebagai <span class="text-gold-primary font-bold not-italic">Draft</span> sebelum finalisasi.</p>
    </div>

    <a href="{{ route('admin.opnames.index') }}"
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

  <form method="POST" action="{{ route('admin.opnames.store') }}" class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_380px]">
    @csrf

    {{-- ✅ SINGLE SOURCE SUBMIT (HIDDEN DATA) --}}
    <div id="opnameFormData" class="hidden"></div>

    <div class="space-y-8">
      <!-- MAIN INFO PANEL -->
      <div class="glass-panel p-8 rounded-[2.5rem] space-y-8">
        <div class="flex items-center gap-3 mb-2">
           <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
           </div>
           <h3 class="text-lg font-bold text-white uppercase tracking-widest">Informasi Opname</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Tanggal Pemeriksaan</label>
            <input type="date" name="opname_date" value="{{ old('opname_date', date('Y-m-d')) }}" required
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>

          <div class="space-y-2">
            <label class="text-[10px] uppercase tracking-widest text-white/40 font-bold ml-1">Catatan Dokumen</label>
            <input name="note" value="{{ old('note') }}" placeholder="Opsional: Keterangan singkat..."
              class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-6 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 focus:bg-white/[0.04] transition-all">
          </div>
        </div>
      </div>

      <!-- ITEMS PANEL -->
      <div class="glass-panel p-8 rounded-[2.5rem] space-y-6">
        <div class="flex items-center justify-between mb-2">
           <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                 </svg>
              </div>
              <h3 class="text-lg font-bold text-white uppercase tracking-widest">Daftar Inventori</h3>
           </div>
           
           <button type="button" id="fillAll"
             class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black text-white uppercase tracking-[0.2em] hover:bg-white/10 transition-all active:scale-95">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gold-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
             </svg>
             Fisik = Sistem
           </button>
        </div>

        <!-- DESKTOP LIST -->
        <div class="hidden md:block overflow-hidden rounded-3xl border border-white/5">
           <table class="w-full text-left text-sm">
              <thead class="bg-white/[0.03] text-[9px] uppercase tracking-widest text-white/30 font-black">
                 <tr>
                    <th class="px-6 py-4">Bahan Baku</th>
                    <th class="px-6 py-4">Stok Sistem</th>
                    <th class="px-6 py-4">Stok Fisik</th>
                    <th class="px-6 py-4">Selisih</th>
                    <th class="px-6 py-4">Catatan Per Baris</th>
                 </tr>
              </thead>
              <tbody id="rowsDesktop" class="divide-y divide-white/5">
                 <!-- DYNAMIC ROWS -->
              </tbody>
           </table>
        </div>

        <!-- MOBILE LIST -->
        <div id="rowsMobile" class="md:hidden space-y-4">
           <!-- DYNAMIC CARDS -->
        </div>

        <button class="w-full rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-5 text-[11px] font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
           Simpan Sebagai Draft
        </button>
      </div>
    </div>

    <!-- SIDEBAR SUMMARY -->
    <div class="space-y-8">
       <div class="premium-card p-8 border-gold-primary/20 bg-gold-primary/[0.03] space-y-6">
          <h4 class="text-xs font-black text-gold-primary uppercase tracking-[0.2em]">Ringkasan Koreksi</h4>
          
          <div class="space-y-4">
             <div class="flex items-center justify-between">
                <span class="text-xs text-emerald-400/60 font-medium">Total Selisih (+)</span>
                <span class="text-sm font-black text-emerald-400" id="sumPlus">0</span>
             </div>
             <div class="flex items-center justify-between">
                <span class="text-xs text-red-400/60 font-medium">Total Selisih (-)</span>
                <span class="text-sm font-black text-red-400" id="sumMinus">0</span>
             </div>
             <div class="pt-6 border-t border-white/5">
                <div class="flex items-start gap-3">
                   <div class="mt-1 w-1.5 h-1.5 rounded-full bg-gold-primary shrink-0"></div>
                   <p class="text-[10px] text-white/40 font-medium leading-relaxed italic">Draft tidak akan mengubah stok sistem sampai Anda menekan tombol <span class="text-white font-bold not-italic">POST</span> di halaman detail.</p>
                </div>
             </div>
          </div>
       </div>

       <div class="glass-panel p-6 border-white/5 rounded-3xl space-y-4">
          <h4 class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">Cara Kerja Selisih</h4>
          <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
             <p class="text-[11px] text-white/50 font-bold text-center italic tracking-wider">Fisik - Sistem = Selisih</p>
          </div>
          <ul class="space-y-2">
             <li class="flex items-center gap-2 text-[9px] text-white/30">
                <span class="w-1 h-1 rounded-full bg-emerald-400"></span>
                <span>Positif (+): Stok Fisik berlebih</span>
             </li>
             <li class="flex items-center gap-2 text-[9px] text-white/30">
                <span class="w-1 h-1 rounded-full bg-red-400"></span>
                <span>Negatif (-): Stok Fisik kurang</span>
             </li>
          </ul>
       </div>
    </div>
  </form>

  <script>
    (function () {
      const materials = @json($materialsJson);

      const rowsDesktop = document.getElementById('rowsDesktop');
      const rowsMobile = document.getElementById('rowsMobile');
      const formDataWrap = document.getElementById('opnameFormData');

      const sumPlusEl = document.getElementById('sumPlus');
      const sumMinusEl = document.getElementById('sumMinus');

      function fmt(n) {
        n = Number(n || 0);
        return n.toLocaleString('id-ID', { maximumFractionDigits: 2 });
      }

      function recalcSummary() {
        let plus = 0, minus = 0;
        document.querySelectorAll('[data-diff]').forEach(el => {
          const val = Number(el.value || 0);
          if (val > 0) plus += val;
          if (val < 0) minus += Math.abs(val);
        });
        sumPlusEl.textContent = fmt(plus);
        sumMinusEl.textContent = fmt(minus);
      }

      function getSubmitEl(selector) {
        return document.querySelector(selector);
      }

      function bindCalc(rowEl, index) {
        const systemEl = rowEl.querySelector('[data-system]');
        const physicalEl = rowEl.querySelector('[data-physical]');
        const noteEl = rowEl.querySelector('[data-note]');
        const diffEl = rowEl.querySelector('[data-diff]');
        const diffTextEl = rowEl.querySelector('[data-difftext]');

        const submitPhysical = getSubmitEl(`[data-submit-physical="${index}"]`);
        const submitNote = getSubmitEl(`[data-submit-note="${index}"]`);

        function update() {
          const system = Number(systemEl.value || 0);
          const physical = Number(physicalEl.value || 0);
          const diff = physical - system;

          if (submitPhysical) submitPhysical.value = physical;
          diffEl.value = diff;

          if (diffTextEl) {
            diffTextEl.textContent = (diff > 0 ? '+' : '') + fmt(diff);
            diffTextEl.classList.remove('text-emerald-400', 'text-red-400', 'text-white/20');
            if (diff > 0) diffTextEl.classList.add('text-emerald-400');
            else if (diff < 0) diffTextEl.classList.add('text-red-400');
            else diffTextEl.classList.add('text-white/20');
          }
          recalcSummary();
        }

        physicalEl.addEventListener('input', update);
        if (noteEl) {
          noteEl.addEventListener('input', () => {
            if (submitNote) submitNote.value = noteEl.value;
          });
        }
        update();
      }

      function renderHiddenInputs() {
        if (!formDataWrap) return;
        formDataWrap.innerHTML = '';
        materials.forEach((m, i) => {
          formDataWrap.insertAdjacentHTML('beforeend', `
            <input type="hidden" name="items[${i}][raw_material_id]" value="${m.id}">
            <input type="hidden" name="items[${i}][physical_qty]" data-submit-physical="${i}" value="${m.system_qty}">
            <input type="hidden" name="items[${i}][note]" data-submit-note="${i}" value="">
          `);
        });
      }

      function renderDesktop() {
        if (!rowsDesktop) return;
        rowsDesktop.innerHTML = '';
        materials.forEach((m, i) => {
          const tr = document.createElement('tr');
          tr.className = "group hover:bg-white/[0.01] transition-colors";
          tr.innerHTML = `
            <td class="px-6 py-4">
               <div class="text-sm font-bold text-white">${m.name}</div>
               <div class="text-[10px] text-white/30 uppercase font-black tracking-tighter">${m.unit}</div>
            </td>
            <td class="px-6 py-4">
               <input data-system type="hidden" value="${m.system_qty}">
               <div class="text-sm font-black text-white/60 italic">${fmt(m.system_qty)}</div>
            </td>
            <td class="px-6 py-4">
              <input data-physical type="number" step="0.01" min="0" value="${m.system_qty}"
                class="w-24 rounded-xl border border-white/5 bg-white/[0.03] px-3 py-2.5 text-sm text-white font-bold outline-none focus:border-gold-primary/30 transition-all">
            </td>
            <td class="px-6 py-4">
              <input data-diff type="hidden" value="0">
              <span data-difftext class="text-sm font-black italic">0</span>
            </td>
            <td class="px-6 py-4">
              <input data-note type="text"
                class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-3 py-2.5 text-xs text-white/70 outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all"
                placeholder="Catatan...">
            </td>
          `;
          rowsDesktop.appendChild(tr);
          bindCalc(tr, i);
        });
      }

      function renderMobile() {
        if (!rowsMobile) return;
        rowsMobile.innerHTML = '';
        materials.forEach((m, i) => {
          const card = document.createElement('div');
          card.className = 'premium-card p-6 border-white/5 group transition-all';
          card.innerHTML = `
            <div class="flex items-start justify-between mb-4">
               <div>
                  <h4 class="text-sm font-bold text-white">${m.name}</h4>
                  <p class="text-[10px] text-white/30 font-black uppercase tracking-widest">${m.unit}</p>
               </div>
               <div class="text-right">
                  <p class="text-[8px] uppercase tracking-widest text-white/20 font-black">Sistem</p>
                  <p class="text-sm font-black text-white/60">${fmt(m.system_qty)}</p>
               </div>
            </div>

            <input data-system type="hidden" value="${m.system_qty}">

            <div class="grid grid-cols-2 gap-4 mb-4">
               <div class="space-y-1.5">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Fisik</label>
                  <input data-physical type="number" step="0.01" min="0" value="${m.system_qty}"
                    class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3 text-sm text-white font-bold outline-none focus:border-gold-primary/30 transition-all">
               </div>
               <div class="space-y-1.5 text-right">
                  <label class="text-[9px] uppercase tracking-widest text-white/40 font-black mr-1">Selisih</label>
                  <input data-diff type="hidden" value="0">
                  <div class="px-4 py-3 rounded-xl bg-white/[0.03] border border-white/5">
                     <span data-difftext class="text-sm font-black italic">0</span>
                  </div>
               </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Catatan Baris</label>
              <input data-note type="text"
                class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3 text-xs text-white/70 outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all"
                placeholder="Keterangan per bahan...">
            </div>
          `;
          rowsMobile.appendChild(card);
          bindCalc(card, i);
        });
      }

      function renderAll() {
        renderHiddenInputs();
        renderDesktop();
        renderMobile();
        recalcSummary();
      }

      const fillAllBtn = document.getElementById('fillAll');
      if (fillAllBtn) {
        fillAllBtn.addEventListener('click', () => {
          document.querySelectorAll('[data-physical]').forEach((inp) => {
            const root = inp.closest('tr') || inp.closest('div.premium-card');
            const systemEl = root?.querySelector('[data-system]');
            const systemVal = Number(systemEl?.value || 0);
            inp.value = systemVal;
            inp.dispatchEvent(new Event('input'));
          });
        });
      }

      renderAll();
    })();
  </script>
@endsection
