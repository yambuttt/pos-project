@extends('layouts.admin')
@section('title', 'Buat Stock Opname')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Buat Stock Opname</h1>
        <p class="text-sm text-white/70">Simpan sebagai Draft dulu, lalu POST untuk apply ke stok</p>
      </div>
    </div>

    <a href="{{ route('admin.opnames.index') }}"
      class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
      ← Kembali
    </a>
  </div>

  @if ($errors->any())
    <div class="mt-4 rounded-2xl border border-red-200/30 bg-red-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.opnames.store') }}"
    class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.2fr_0.8fr]">
    @csrf

    {{-- ✅ SINGLE SOURCE SUBMIT (JANGAN DIHAPUS) --}}
    <div id="opnameFormData" class="hidden"></div>

    {{-- LEFT --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label class="text-sm text-white/80">Tanggal Opname</label>
          <input type="date" name="opname_date" value="{{ old('opname_date', date('Y-m-d')) }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40" />
        </div>

        <div>
          <label class="text-sm text-white/80">Catatan</label>
          <input name="note" value="{{ old('note') }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
            placeholder="Opsional..." />
        </div>
      </div>

      <div class="mt-5 flex items-center justify-between">
        <div>
          <div class="text-sm font-semibold">Daftar Bahan</div>
          <div class="text-xs text-white/60">Isi stok fisik, sistem akan hitung selisih</div>
        </div>

        <button type="button" id="fillAll"
          class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs hover:bg-white/15">
          Isi fisik = sistem
        </button>
      </div>

      {{-- Desktop table --}}
      <div class="mt-4 hidden sm:block overflow-hidden rounded-2xl border border-white/15">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[920px] text-left text-sm">
            <thead class="bg-white/10 text-xs text-white/70">
              <tr>
                <th class="px-4 py-3">Bahan</th>
                <th class="px-4 py-3">Unit</th>
                <th class="px-4 py-3">Sistem</th>
                <th class="px-4 py-3">Fisik</th>
                <th class="px-4 py-3">Selisih</th>
                <th class="px-4 py-3">Note</th>
              </tr>
            </thead>
            <tbody id="rowsDesktop" class="divide-y divide-white/10"></tbody>
          </table>
        </div>
      </div>

      {{-- Mobile cards --}}
      <div id="rowsMobile" class="mt-4 space-y-3 sm:hidden"></div>

      <button
        class="mt-5 w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
        Simpan Draft
      </button>
    </div>

    {{-- RIGHT --}}
    <div class="space-y-5">
      <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
        <div class="text-sm font-semibold">Info</div>
        <p class="mt-2 text-sm text-white/70">
          • Draft tidak mengubah stok<br />
          • POST akan mengubah stok + membuat inventory movement type <b>opname</b><br />
          • Selisih = fisik - sistem
        </p>
      </div>

      <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
        <div class="text-sm font-semibold">Ringkasan Selisih</div>

        <div class="mt-3 flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
          <div class="text-sm text-white/70">Total (+)</div>
          <div class="text-sm font-semibold" id="sumPlus">0</div>
        </div>

        <div class="mt-2 flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
          <div class="text-sm text-white/70">Total (-)</div>
          <div class="text-sm font-semibold" id="sumMinus">0</div>
        </div>
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

        // hidden submit (single source)
        const submitPhysical = getSubmitEl(`[data-submit-physical="${index}"]`);
        const submitNote = getSubmitEl(`[data-submit-note="${index}"]`);

        function update() {
          const system = Number(systemEl.value || 0);
          const physical = Number(physicalEl.value || 0);
          const diff = physical - system;

          if (submitPhysical) submitPhysical.value = physical;

          diffEl.value = diff;

          if (diffTextEl) {
            diffTextEl.textContent = fmt(diff);
            diffTextEl.classList.toggle('text-emerald-200', diff > 0);
            diffTextEl.classList.toggle('text-red-200', diff < 0);
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
          tr.innerHTML = `
            <td class="px-4 py-3 font-medium">${m.name}</td>
            <td class="px-4 py-3 text-white/70">${m.unit}</td>

            <td class="px-4 py-3">
              <input data-system type="number" step="0.01" value="${m.system_qty}"
                class="w-32 rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none"
                readonly>
            </td>

            <td class="px-4 py-3">
              <input data-physical type="number" step="0.01" min="0" value="${m.system_qty}"
                class="w-32 rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none focus:border-white/40">
            </td>

            <td class="px-4 py-3">
              <input data-diff type="hidden" value="0">
              <span data-difftext class="text-sm font-semibold text-white/80">0</span>
            </td>

            <td class="px-4 py-3">
              <input data-note type="text"
                class="w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
                placeholder="Opsional...">
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
          card.className = 'rounded-2xl border border-white/15 bg-white/10 p-4';
          card.innerHTML = `
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-sm font-semibold">${m.name}</div>
                <div class="text-xs text-white/70">Unit: ${m.unit}</div>
              </div>
              <div class="text-xs text-white/70">
                Sistem: <b>${fmt(m.system_qty)}</b>
              </div>
            </div>

            <input data-system type="hidden" value="${m.system_qty}">

            <div class="mt-3 grid grid-cols-2 gap-3">
              <div>
                <div class="text-xs text-white/70">Fisik</div>
                <input data-physical type="number" step="0.01" min="0" value="${m.system_qty}"
                  class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none focus:border-white/40">
              </div>

              <div>
                <div class="text-xs text-white/70">Selisih</div>
                <input data-diff type="hidden" value="0">
                <div data-difftext class="mt-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-sm font-semibold text-white/80">0</div>
              </div>
            </div>

            <div class="mt-3">
              <div class="text-xs text-white/70">Note</div>
              <input data-note type="text"
                class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
                placeholder="Opsional...">
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

      // Fill physical = system
      const fillAllBtn = document.getElementById('fillAll');
      if (fillAllBtn) {
        fillAllBtn.addEventListener('click', () => {
          document.querySelectorAll('[data-physical]').forEach((inp) => {
            // cari system terdekat dalam row/card yang sama
            const root = inp.closest('tr') || inp.closest('div');
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
