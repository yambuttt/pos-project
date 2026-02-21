@extends('layouts.admin')
@section('title', 'Purchases')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Purchases</h1>
        <p class="text-sm text-white/70">Barang masuk: supplier / pasar / random</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.purchases.create') }}"
        class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
        + Buat Purchase
      </a>

      {{-- Tombol export lama (GET bulk) boleh kamu hapus kalau sudah pakai POST export baru --}}
      <a href="{{ route('admin.purchases.export.pdf.bulk', request()->query()) }}"
        class="rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold hover:bg-white/20">
        Export PDF (Gabungan)
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
      <div class="font-semibold mb-1">❌ Ada error:</div>
      <ul class="list-disc pl-5 text-white/80 space-y-1">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- ✅ FORM EXPORT (Selected / Range / Period) --}}
  <form method="POST" action="{{ route('admin.purchases.exportPdf') }}" class="mt-5">
    @csrf

    {{-- mode akan diubah via tombol --}}
    <input type="hidden" name="mode" id="exportMode" value="selected">

    {{-- Panel export --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">

        {{-- LEFT: export selected --}}
        <div class="flex flex-wrap items-center gap-2">
          <div class="text-sm font-semibold text-white/90">Export PDF:</div>

          <button type="submit"
            onclick="document.getElementById('exportMode').value='selected'"
            class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
            Export yang dipilih
          </button>

          <span class="text-xs text-white/60">
            (centang purchase di tabel / kartu)
          </span>
        </div>

        {{-- RIGHT: export by period --}}
        <div class="flex flex-wrap items-end gap-2">
          <div>
            <label class="block text-xs text-white/70 mb-1">Periode</label>
            <select name="period"
              class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm text-white/90 backdrop-blur-xl">
              <option value="">Pilih…</option>
              <option value="daily">Harian</option>
              <option value="weekly">Mingguan</option>
              <option value="monthly">Bulanan</option>
              <option value="yearly">Tahunan</option>
            </select>
          </div>

          <div>
            <label class="block text-xs text-white/70 mb-1">Tanggal acuan</label>
            <input type="date" name="anchor_date"
              class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm text-white/90 backdrop-blur-xl">
          </div>

          <button type="submit"
            onclick="document.getElementById('exportMode').value='period'"
            class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
            Export Periode
          </button>
        </div>
      </div>

      {{-- export by range --}}
      <div class="mt-4 flex flex-wrap items-end gap-2">
        <div class="text-sm font-semibold text-white/90">Rentang tanggal:</div>

        <div>
          <label class="block text-xs text-white/70 mb-1">Dari</label>
          <input type="date" name="from"
            class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm text-white/90 backdrop-blur-xl">
        </div>

        <div>
          <label class="block text-xs text-white/70 mb-1">Sampai</label>
          <input type="date" name="to"
            class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm text-white/90 backdrop-blur-xl">
        </div>

        <button type="submit"
          onclick="document.getElementById('exportMode').value='range'"
          class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
          Export Rentang
        </button>
      </div>

      {{-- ✅ TABLE (Desktop) --}}
      <div class="mt-5 hidden sm:block overflow-hidden rounded-2xl border border-white/15">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[980px] text-left text-sm">
            <thead class="bg-white/10 text-xs text-white/70">
              <tr>
                <th class="px-4 py-3 w-[50px]">
                  <input id="checkAll" type="checkbox"
                    class="h-4 w-4 rounded border-white/30 bg-white/10">
                </th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Sumber</th>
                <th class="px-4 py-3">Invoice</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Dibuat oleh</th>
                <th class="px-4 py-3">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              @forelse($purchases as $p)
                <tr class="hover:bg-white/5">
                  <td class="px-4 py-3">
                    <input type="checkbox" name="purchase_ids[]" value="{{ $p->id }}"
                      class="rowCheck h-4 w-4 rounded border-white/30 bg-white/10">
                  </td>

                  <td class="px-4 py-3 text-white/85">
                    {{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}
                  </td>

                  <td class="px-4 py-3">
                    @if($p->source_type === 'supplier')
                      <div class="font-medium">{{ $p->supplier?->name }}</div>
                      <div class="text-xs text-white/60">Supplier</div>
                    @else
                      <div class="font-medium">{{ $p->source_name }}</div>
                      <div class="text-xs text-white/60">External</div>
                    @endif
                  </td>

                  <td class="px-4 py-3 text-white/75">{{ $p->invoice_no ?? '-' }}</td>

                  <td class="px-4 py-3 font-semibold">
                    Rp {{ number_format($p->total_amount, 0, ',', '.') }}
                  </td>

                  <td class="px-4 py-3 text-white/75">
                    {{ $p->creator?->name ?? '-' }}
                    <div class="text-xs text-white/60">{{ $p->creator?->email }}</div>
                  </td>

                  <td class="px-4 py-3">
                    <a href="{{ route('admin.purchases.show', $p) }}"
                      class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold backdrop-blur-xl hover:bg-white/15">
                      Detail
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="px-4 py-6 text-center text-white/70">Belum ada purchase.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- ✅ Mobile card list --}}
      <div class="mt-5 sm:hidden space-y-3">
        @forelse($purchases as $p)
          <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-start gap-3">
                <input type="checkbox" name="purchase_ids[]" value="{{ $p->id }}"
                  class="rowCheck h-4 w-4 rounded border-white/30 bg-white/10 mt-1">

                <div>
                  <div class="text-sm font-semibold">
                    {{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}
                  </div>
                  <div class="text-xs text-white/70">
                    {{ $p->source_type === 'supplier' ? ($p->supplier?->name ?? '-') : ($p->source_name ?? '-') }}
                    • <span class="uppercase">{{ $p->source_type }}</span>
                  </div>
                </div>
              </div>

              <div class="text-sm font-semibold">
                Rp {{ number_format($p->total_amount, 0, ',', '.') }}
              </div>
            </div>

            <div class="mt-2 text-xs text-white/70">
              Invoice: {{ $p->invoice_no ?? '-' }} <br />
              By: {{ $p->creator?->name ?? '-' }}
            </div>

            <div class="mt-3">
              <a href="{{ route('admin.purchases.show', $p) }}"
                class="inline-flex rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold backdrop-blur-xl hover:bg-white/15">
                Detail
              </a>
            </div>
          </div>
        @empty
          <div class="rounded-2xl border border-white/15 bg-white/10 p-6 text-center text-sm text-white/70">
            Belum ada purchase.
          </div>
        @endforelse
      </div>

      <div class="mt-4">
        {{ $purchases->onEachSide(1)->links() }}
      </div>
    </div>
  </form>

  <script>
    // Desktop: check all
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
      checkAll.addEventListener('change', function () {
        document.querySelectorAll('.rowCheck').forEach(cb => cb.checked = checkAll.checked);
      });
    }
  </script>
@endsection