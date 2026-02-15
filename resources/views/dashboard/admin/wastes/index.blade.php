@extends('layouts.admin')
@section('title','Waste')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Waste / Stock Rusak</h1>
        <p class="text-sm text-white/70">Barang keluar karena basi/tumpah/expired</p>
      </div>
    </div>

    <a href="{{ route('admin.wastes.create') }}"
       class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
      + Buat Waste
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    {{-- Desktop table --}}
    <div class="hidden sm:block overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Reason</th>
              <th class="px-4 py-3">Total Estimasi</th>
              <th class="px-4 py-3">Dibuat oleh</th>
              <th class="px-4 py-3">Catatan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($wastes as $w)
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($w->waste_date)->format('d M Y') }}</td>
                <td class="px-4 py-3 text-white/80">{{ $w->reason ?? '-' }}</td>
                <td class="px-4 py-3 font-semibold">Rp {{ number_format($w->total_estimated_cost,0,',','.') }}</td>
                <td class="px-4 py-3 text-white/75">
                  {{ $w->creator?->name ?? '-' }}
                  <div class="text-xs text-white/60">{{ $w->creator?->email }}</div>
                </td>
                <td class="px-4 py-3 text-white/70">{{ $w->note ?? '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="px-4 py-6 text-center text-white/70">Belum ada waste.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden space-y-3">
      @forelse($wastes as $w)
        <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-semibold">{{ \Carbon\Carbon::parse($w->waste_date)->format('d M Y') }}</div>
              <div class="text-xs text-white/70">Reason: {{ $w->reason ?? '-' }}</div>
            </div>
            <div class="text-sm font-semibold">Rp {{ number_format($w->total_estimated_cost,0,',','.') }}</div>
          </div>
          <div class="mt-2 text-xs text-white/70">
            By: {{ $w->creator?->name ?? '-' }}<br/>
            {{ $w->note ?? '' }}
          </div>
        </div>
      @empty
        <div class="rounded-2xl border border-white/15 bg-white/10 p-6 text-center text-sm text-white/70">
          Belum ada waste.
        </div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $wastes->onEachSide(1)->links() }}
    </div>
  </div>
@endsection
