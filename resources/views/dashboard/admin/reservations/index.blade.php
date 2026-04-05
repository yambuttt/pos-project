@extends('layouts.admin')
@section('title','Reservasi')

@section('body')
<div class="flex items-center justify-between gap-3">
  <div class="flex items-center gap-3">
    <button id="openMobileSidebar" type="button"
      class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
    <div>
      <h1 class="text-xl font-semibold">Reservasi</h1>
      <p class="text-sm text-white/70">Booking Meja / Ruangan + Pre-order menu</p>
    </div>
  </div>

  <a href="{{ route('admin.reservations.create') }}" class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">+ Buat Reservasi</a>
</div>

@if(session('success'))
  <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">✅ {{ session('success') }}</div>
@endif

<form method="GET" class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
  <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
    <div>
      <label class="text-xs text-white/70">Tanggal</label>
      <input type="date" name="date" value="{{ $date }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>
    <div>
      <label class="text-xs text-white/70">Status</label>
      <select name="status" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm">
        <option value="">Semua</option>
        @foreach(['pending','confirmed','arrived','completed','cancelled','rejected','no_show'] as $st)
          <option value="{{ $st }}" {{ $status===$st ? 'selected' : '' }}>{{ $st }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex items-end">
      <button class="w-full rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Terapkan Filter</button>
    </div>
  </div>
</form>

<div class="mt-5 overflow-hidden rounded-2xl border border-white/15 bg-white/10">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[1100px] text-left text-sm">
      <thead class="bg-white/10 text-xs text-white/70">
        <tr>
          <th class="px-4 py-3">Kode</th>
          <th class="px-4 py-3">Tanggal/Jam</th>
          <th class="px-4 py-3">Tipe</th>
          <th class="px-4 py-3">Customer</th>
          <th class="px-4 py-3">Jumlah</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/10">
        @forelse($reservations as $r)
          <tr class="hover:bg-white/5">
            <td class="px-4 py-3 font-semibold text-white/90">{{ $r->reservation_code }}</td>
            <td class="px-4 py-3 text-white/75">
              {{ \Carbon\Carbon::parse($r->reservation_date)->format('d M Y') }}
              <div class="text-xs text-white/60">{{ $r->start_time }} - {{ $r->end_time }} ({{ $r->duration_minutes }}m)</div>
            </td>
            <td class="px-4 py-3 text-white/75">{{ strtoupper($r->reservable_type) }}</td>
            <td class="px-4 py-3">
              <div class="font-medium">{{ $r->customer_name }}</div>
              <div class="text-xs text-white/60">{{ $r->customer_phone }}</div>
            </td>
            <td class="px-4 py-3 text-white/75">{{ $r->party_size }}</td>
            <td class="px-4 py-3">
              <span class="rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs font-semibold">{{ $r->status }}</span>
            </td>
            <td class="px-4 py-3">
              <a href="{{ route('admin.reservations.show',$r) }}" class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold hover:bg-white/15">Detail</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="px-4 py-6 text-center text-white/70">Belum ada reservasi.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">{{ $reservations->onEachSide(1)->links() }}</div>
@endsection
