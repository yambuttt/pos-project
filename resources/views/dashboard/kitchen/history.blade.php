@extends('layouts.kitchen')
@section('title', 'Riwayat Masak')

@section('body')
  <div class="flex items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-semibold">Riwayat Masak</h1>
      <p class="text-sm text-slate-600">Ringkasan pesanan yang sudah selesai dimasak.</p>
    </div>
  </div>

  <div class="mt-4 rounded-[26px] border border-slate-200/70 bg-white/55 p-4 shadow-sm backdrop-blur-2xl">
    <form class="flex flex-col gap-3 sm:flex-row sm:items-end">
      <div>
        <label class="text-xs text-slate-600">Dari</label>
        <input type="date" name="from" value="{{ $from }}"
               class="mt-1 w-full rounded-xl border border-slate-200/70 bg-white/70 px-3 py-2 text-sm outline-none" />
      </div>
      <div>
        <label class="text-xs text-slate-600">Sampai</label>
        <input type="date" name="to" value="{{ $to }}"
               class="mt-1 w-full rounded-xl border border-slate-200/70 bg-white/70 px-3 py-2 text-sm outline-none" />
      </div>
      <button class="rounded-xl border border-slate-200/70 bg-white/70 px-4 py-2 text-sm font-semibold hover:bg-white/90 backdrop-blur-2xl">
        Filter
      </button>
    </form>

    <div class="mt-4 flex flex-wrap gap-3">
      <div class="rounded-2xl border border-slate-200/70 bg-white/70 px-4 py-3 backdrop-blur-2xl">
        <div class="text-xs text-slate-600">Total order selesai</div>
        <div class="text-xl font-semibold">{{ $totalOrdersDone }}</div>
      </div>
    </div>
  </div>

  <div class="mt-4 rounded-[26px] border border-slate-200/70 bg-white/55 p-4 shadow-sm backdrop-blur-2xl">
    <h2 class="text-sm font-semibold">Total porsi per menu</h2>

    <div class="mt-3 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-slate-600">
            <th class="py-2 pr-4">Menu</th>
            <th class="py-2 pr-4">Total porsi</th>
          </tr>
        </thead>
        <tbody class="align-top">
          @forelse($byProduct as $row)
            <tr class="border-t border-slate-200/70">
              <td class="py-2 pr-4 font-medium">{{ $row->product->name ?? ('Product#'.$row->product_id) }}</td>
              <td class="py-2 pr-4">{{ (int) $row->total_qty }}</td>
            </tr>
          @empty
            <tr class="border-t border-slate-200/70">
              <td class="py-3 text-slate-600" colspan="2">Belum ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection