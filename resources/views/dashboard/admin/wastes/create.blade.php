@extends('layouts.admin')
@section('title', 'Buat Waste')

@section('body')
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button id="openMobileSidebar" type="button"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
            <div>
                <h1 class="text-xl font-semibold">Buat Waste / Stock Rusak</h1>
                <p class="text-sm text-white/70">STRICT: qty waste tidak boleh melebihi stok bahan</p>
            </div>
        </div>
        <a href="{{ route('admin.wastes.index') }}"
            class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
            ← Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mt-4 rounded-2xl border border-red-200/30 bg-red-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.wastes.store') }}"
        class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.2fr_0.8fr]">
        @csrf

        <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7 space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm text-white/80">Tanggal</label>
                    <input type="date" name="waste_date" value="{{ old('waste_date', date('Y-m-d')) }}"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40" />
                </div>

                <div>
                    <label class="text-sm text-white/80">Reason</label>
                    <select name="reason"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
                        <option value="">-- pilih --</option>
                        <option value="expired">Expired</option>
                        <option value="spoil">Basi/Spoiled</option>
                        <option value="spill">Tumpah/Spill</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-sm text-white/80">Catatan</label>
                <textarea name="note" rows="3"
                    class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
                    placeholder="Catatan waste...">{{ old('note') }}</textarea>
            </div>

            <div class="mt-2">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-semibold">Items</div>
                    <button type="button" id="addRow"
                        class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs backdrop-blur-xl hover:bg-white/15">
                        + Tambah Item
                    </button>
                </div>

                <div class="mt-3 space-y-3" id="itemsWrap"></div>

                <div
                    class="mt-4 flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                    <div class="text-sm text-white/80">Total Estimasi</div>
                    <div class="text-sm font-semibold" id="grandTotal">Rp 0</div>
                </div>

                <button
                    class="mt-4 w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
                    Simpan Waste
                </button>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
                <div class="text-sm font-semibold">STRICT Mode</div>
                <p class="mt-2 text-sm text-white/70">
                    Sistem akan menolak jika qty waste melebihi stok bahan saat ini.
                </p>
            </div>
        </div>
    </form>

    <script>
        (function () {
            const materials = @json($materialsJson);


            const itemsWrap = document.getElementById('itemsWrap');
            const addRowBtn = document.getElementById('addRow');
            const grandTotalEl = document.getElementById('grandTotal');

            function money(n) { n = Number(n || 0); return 'Rp ' + n.toLocaleString('id-ID'); }

            function recalc() {
                let total = 0;
                itemsWrap.querySelectorAll('[data-row]').forEach(row => {
                    const qty = Number(row.querySelector('[data-qty]').value || 0);
                    const cost = Number(row.querySelector('[data-cost]').value || 0);
                    const sub = qty * cost;
                    row.querySelector('[data-sub]').textContent = money(sub);
                    total += sub;
                });
                grandTotalEl.textContent = money(total);
            }

            function rowTemplate(idx) {
                const options = materials.map(m => `<option value="${m.id}">${m.name} (${m.unit}) • stok: ${m.stock}</option>`).join('');
                return `
                  <div data-row class="rounded-2xl border border-white/15 bg-white/10 p-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1.2fr_0.5fr_0.6fr_0.5fr] sm:items-end">
                      <div>
                        <label class="text-xs text-white/70">Bahan</label>
                        <select name="items[${idx}][raw_material_id]"
                          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none focus:border-white/40">
                          ${options}
                        </select>
                      </div>

                      <div>
                        <label class="text-xs text-white/70">Qty</label>
                        <input data-qty name="items[${idx}][qty]" type="number" step="0.01" value="1"
                          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none focus:border-white/40" />
                      </div>

                      <div>
                        <label class="text-xs text-white/70">Estimasi Cost</label>
                        <input data-cost name="items[${idx}][estimated_cost]" type="number" step="0.01" value="0"
                          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none focus:border-white/40" />
                      </div>

                      <div class="flex items-center justify-between sm:justify-end sm:gap-3">
                        <div class="text-sm font-semibold" data-sub>${money(0)}</div>
                        <button type="button" data-remove
                          class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs hover:bg-white/15">
                          Hapus
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