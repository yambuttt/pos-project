@extends('layouts.admin')
@section('title','Inventory Movements')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
        ☰
      </button>
      <div>
        <h1 class="text-xl font-semibold">Inventory Movements</h1>
        <p class="text-sm text-white/70">Ledger pergerakan stok (IN / OUT / ADJ)</p>
      </div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
      <div>
        <div class="text-xs text-white/70">Bahan</div>
        <select name="raw_material_id"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none focus:border-white/40">
          <option value="">Semua</option>
          @foreach($materials as $m)
            <option value="{{ $m->id }}" @selected((int)$materialId === (int)$m->id)>{{ $m->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <div class="text-xs text-white/70">Type</div>
        <select name="type"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none focus:border-white/40">
          <option value="">Semua</option>
          @foreach(['purchase'=>'purchase','waste'=>'waste','opname'=>'opname','adjustment'=>'adjustment'] as $k=>$v)
            <option value="{{ $k }}" @selected($type===$k)>{{ strtoupper($v) }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <div class="text-xs text-white/70">Dari</div>
        <input type="date" name="date_from" value="{{ $dateFrom }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none focus:border-white/40">
      </div>

      <div>
        <div class="text-xs text-white/70">Sampai</div>
        <input type="date" name="date_to" value="{{ $dateTo }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm outline-none focus:border-white/40">
      </div>

      <div class="flex items-end gap-2">
        <button
          class="w-full rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
          Filter
        </button>

        <a href="{{ route('admin.inventory-movements.index') }}"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15 text-center">
          Reset
        </a>
      </div>
    </form>
  </div>

  {{-- Table --}}
  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm font-semibold">Ledger</div>
        <div class="text-xs text-white/60">Menampilkan {{ $movements->count() }} baris</div>
      </div>
      <div class="text-xs text-white/60">
        Tips: pilih 1 bahan + range tanggal biar running balance makin meaningful.
      </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Waktu</th>
              <th class="px-4 py-3">Bahan</th>
              <th class="px-4 py-3">Type</th>
              <th class="px-4 py-3">IN</th>
              <th class="px-4 py-3">OUT</th>
              <th class="px-4 py-3">Saldo (running)</th>
              <th class="px-4 py-3">Dibuat oleh</th>
              <th class="px-4 py-3">Note</th>
              <th class="px-4 py-3">Ref</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($movements as $mv)
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3 text-white/80">
                  {{ $mv->created_at->format('d M Y H:i') }}
                </td>
                <td class="px-4 py-3 font-medium">
                  {{ $mv->rawMaterial?->name ?? '-' }}
                  <div class="text-xs text-white/60">{{ $mv->rawMaterial?->unit ?? '' }}</div>
                </td>
                <td class="px-4 py-3">
                  <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs">
                    {{ strtoupper($mv->type) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-emerald-200 font-semibold">
                  {{ number_format((float)$mv->qty_in, 2, '.', '') }}
                </td>
                <td class="px-4 py-3 text-red-200 font-semibold">
                  {{ number_format((float)$mv->qty_out, 2, '.', '') }}
                </td>
                <td class="px-4 py-3 font-semibold">
                  {{ number_format((float)$mv->running_balance, 2, '.', '') }}
                </td>
                <td class="px-4 py-3">
                  {{ $mv->creator?->name ?? 'Admin' }}
                  <div class="text-xs text-white/60">{{ $mv->creator?->email }}</div>
                </td>
                <td class="px-4 py-3 text-white/70">
                  {{ $mv->note ?? '-' }}
                </td>
                <td class="px-4 py-3 text-white/60 text-xs">
                  {{ class_basename($mv->reference_type) }} #{{ $mv->reference_id }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-4 py-8 text-center text-white/60">
                  Belum ada movement.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">
      {{ $movements->links() }}
    </div>
  </div>
@endsection
