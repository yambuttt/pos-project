@extends('layouts.admin')
@section('title', 'Reservasi')

@section('body')
  <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Reservasi</h1>
        <p class="text-sm text-white/70">Kelola reservasi: DP, check-in, checkout, cancel.</p>
      </div>
    </div>

    <a href="{{ route('admin.reservations.create') }}"
      class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
      + Buat Reservasi
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-500/10 px-4 py-3 text-sm">
      ✅ {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-red-300/20 bg-red-500/10 px-4 py-3 text-sm whitespace-pre-line">
      ❌ {{ $errors->first() }}
    </div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <form method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Cari kode / nama / HP..."
          class="w-full sm:w-[320px] rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm outline-none placeholder:text-white/40 focus:border-white/40" />

        <select name="status"
          class="w-full sm:w-[200px] rounded-xl border border-white/20 bg-white/10 px-3 py-2.5 text-sm outline-none focus:border-white/40">
          <option value="">Semua status</option>
          @foreach (['draft','pending_dp','confirmed','checked_in','completed','cancelled','no_show'] as $st)
            <option value="{{ $st }}" @selected(($status ?? '')===$st)>{{ $st }}</option>
          @endforeach
        </select>

        <button class="rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold hover:bg-white/20">
          Filter
        </button>
      </div>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1100px] text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Kode</th>
              <th class="px-4 py-3">Customer</th>
              <th class="px-4 py-3">Resource</th>
              <th class="px-4 py-3">Waktu</th>
              <th class="px-4 py-3">Menu</th>
              <th class="px-4 py-3">Total</th>
              <th class="px-4 py-3">DP</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse ($rows as $r)
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3 font-semibold">{{ $r->code }}</td>
                <td class="px-4 py-3">
                  <div class="font-semibold">{{ $r->customer_name }}</div>
                  <div class="text-xs text-white/60">{{ $r->customer_phone }}</div>
                </td>
                <td class="px-4 py-3 text-white/80">{{ $r->resource?->name }}</td>
                <td class="px-4 py-3 text-white/80">
                  <div>{{ $r->start_at->format('d M Y H:i') }}</div>
                  <div class="text-xs text-white/60">{{ $r->end_at->format('d M Y H:i') }}</div>
                </td>
                <td class="px-4 py-3">{{ $r->menu_type }}</td>
                <td class="px-4 py-3 font-semibold">Rp {{ number_format($r->grand_total,0,',','.') }}</td>
                <td class="px-4 py-3">Rp {{ number_format($r->dp_amount,0,',','.') }}</td>
                <td class="px-4 py-3">{{ $r->status }}</td>
                <td class="px-4 py-3 text-right">
                  <a href="{{ route('admin.reservations.show', $r) }}"
                    class="rounded-xl border border-white/15 bg-white/5 px-3 py-2 text-xs font-semibold hover:bg-white/10">
                    Detail
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-4 py-8 text-center text-white/60">Belum ada reservasi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
  </div>
@endsection