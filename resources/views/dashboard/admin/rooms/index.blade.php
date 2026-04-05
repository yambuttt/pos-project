@extends('layouts.admin')
@section('title','Ruangan')

@section('body')
<div class="flex items-center justify-between gap-3">
  <div class="flex items-center gap-3">
    <button id="openMobileSidebar" type="button"
      class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
    <div>
      <h1 class="text-xl font-semibold">Ruangan</h1>
      <p class="text-sm text-white/70">Kelola ruangan yang bisa di-reservasi.</p>
    </div>
  </div>

  <a href="{{ route('admin.rooms.create') }}" class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">+ Tambah Ruangan</a>
</div>

@if(session('success'))
  <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">✅ {{ session('success') }}</div>
@endif

<div class="mt-5 overflow-hidden rounded-2xl border border-white/15 bg-white/10">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[900px] text-left text-sm">
      <thead class="bg-white/10 text-xs text-white/70">
        <tr>
          <th class="px-4 py-3">Nama</th>
          <th class="px-4 py-3">Lokasi</th>
          <th class="px-4 py-3">Kapasitas</th>
          <th class="px-4 py-3">Aktif</th>
          <th class="px-4 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/10">
        @forelse($rooms as $r)
          <tr class="hover:bg-white/5">
            <td class="px-4 py-3 font-semibold text-white/90">{{ $r->name }}</td>
            <td class="px-4 py-3 text-white/75">{{ $r->location_label ?? '-' }}</td>
            <td class="px-4 py-3 text-white/75">{{ $r->capacity_min }} - {{ $r->capacity_max }}</td>
            <td class="px-4 py-3">{!! $r->is_active ? '<span class="text-emerald-200">Aktif</span>' : '<span class="text-white/60">Nonaktif</span>' !!}</td>
            <td class="px-4 py-3">
              <a href="{{ route('admin.rooms.edit',$r) }}" class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold hover:bg-white/15">Edit</a>
              <form method="POST" action="{{ route('admin.rooms.destroy',$r) }}" class="inline">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('Hapus ruangan ini?')" class="ml-2 rounded-xl border border-red-500/30 bg-red-500/10 px-3 py-2 text-xs font-semibold hover:bg-red-500/15">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-4 py-6 text-center text-white/70">Belum ada ruangan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">{{ $rooms->onEachSide(1)->links() }}</div>
@endsection
