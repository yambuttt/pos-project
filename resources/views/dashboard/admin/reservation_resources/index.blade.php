@extends('layouts.admin')
@section('title', 'Resource Reservasi')

@section('body')
  <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Resource Reservasi</h1>
        <p class="text-sm text-white/70">Kelola meja / ruangan / hall beserta kapasitas & harga.</p>
      </div>
    </div>

    <a href="{{ route('admin.reservation_resources.create') }}"
      class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
      + Tambah Resource
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
    <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Cari nama..."
          class="w-full sm:w-[300px] rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm outline-none placeholder:text-white/40 focus:border-white/40" />

        <select name="type"
          class="w-full sm:w-[180px] rounded-xl border border-white/20 bg-white/10 px-3 py-2.5 text-sm outline-none focus:border-white/40">
          <option value="">Semua</option>
          <option value="TABLE" @selected(($type ?? '')==='TABLE')>TABLE</option>
          <option value="ROOM" @selected(($type ?? '')==='ROOM')>ROOM</option>
          <option value="HALL" @selected(($type ?? '')==='HALL')>HALL</option>
        </select>

        <button class="rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold hover:bg-white/20">
          Filter
        </button>
      </div>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1050px] text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Tipe</th>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Kapasitas</th>
              <th class="px-4 py-3">Rate/Jam</th>
              <th class="px-4 py-3">Flat</th>
              <th class="px-4 py-3">Min Durasi</th>
              <th class="px-4 py-3">Buffer</th>
              <th class="px-4 py-3">Aktif</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($rows as $r)
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3">{{ $r->type }}</td>
                <td class="px-4 py-3 font-semibold">{{ $r->name }}</td>
                <td class="px-4 py-3">{{ $r->capacity }}</td>
                <td class="px-4 py-3">{{ $r->hourly_rate ?? '-' }}</td>
                <td class="px-4 py-3">{{ $r->flat_rate ?? '-' }}</td>
                <td class="px-4 py-3">{{ $r->min_duration_minutes }} menit</td>
                <td class="px-4 py-3">{{ $r->buffer_minutes }} menit</td>
                <td class="px-4 py-3">{{ $r->is_active ? 'Ya' : 'Tidak' }}</td>
                <td class="px-4 py-3 text-right">
                  <a href="{{ route('admin.reservation_resources.edit', $r) }}"
                    class="rounded-xl border border-white/15 bg-white/5 px-3 py-2 text-xs font-semibold hover:bg-white/10">Edit</a>

                  <form method="POST" action="{{ route('admin.reservation_resources.destroy', $r) }}" class="inline"
                    onsubmit="return confirm('Hapus resource ini?')">
                    @csrf
                    @method('DELETE')
                    <button
                      class="ml-2 rounded-xl border border-red-300/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100 hover:bg-red-500/15">
                      Hapus
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="9" class="px-4 py-8 text-center text-white/60">Belum ada resource.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
  </div>
@endsection