@extends('dashboard.admin._reservation_layout')

@section('title', 'Resource Reservasi')
@section('page_title', 'Resource Reservasi')
@section('page_subtitle', 'Kelola meja / ruangan / hall')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <form class="flex flex-wrap gap-2" method="GET">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Cari nama..."
               class="px-3 py-2 border rounded w-64" />
        <select name="type" class="px-3 py-2 border rounded">
            <option value="">Semua</option>
            <option value="TABLE" @selected(($type ?? '')==='TABLE')>TABLE</option>
            <option value="ROOM" @selected(($type ?? '')==='ROOM')>ROOM</option>
            <option value="HALL" @selected(($type ?? '')==='HALL')>HALL</option>
        </select>
        <button class="px-3 py-2 rounded bg-gray-900 text-white">Filter</button>
    </form>

    <a href="{{ route('admin.reservation_resources.create') }}"
       class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
        + Tambah Resource
    </a>
</div>

<div class="overflow-x-auto">
<table class="w-full text-sm">
    <thead>
        <tr class="border-b bg-gray-50 text-left">
            <th class="p-2">Tipe</th>
            <th class="p-2">Nama</th>
            <th class="p-2">Kapasitas</th>
            <th class="p-2">Rate/Jam</th>
            <th class="p-2">Flat</th>
            <th class="p-2">Min Durasi</th>
            <th class="p-2">Buffer</th>
            <th class="p-2">Aktif</th>
            <th class="p-2"></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $r)
            <tr class="border-b">
                <td class="p-2">{{ $r->type }}</td>
                <td class="p-2 font-medium">{{ $r->name }}</td>
                <td class="p-2">{{ $r->capacity }}</td>
                <td class="p-2">{{ $r->hourly_rate ?? '-' }}</td>
                <td class="p-2">{{ $r->flat_rate ?? '-' }}</td>
                <td class="p-2">{{ $r->min_duration_minutes }} menit</td>
                <td class="p-2">{{ $r->buffer_minutes }} menit</td>
                <td class="p-2">{{ $r->is_active ? 'Ya' : 'Tidak' }}</td>
                <td class="p-2 text-right">
                    <a class="px-2 py-1 border rounded hover:bg-gray-50"
                       href="{{ route('admin.reservation_resources.edit', $r) }}">Edit</a>

                    <form class="inline" method="POST" action="{{ route('admin.reservation_resources.destroy', $r) }}"
                          onsubmit="return confirm('Hapus resource ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-2 py-1 border rounded hover:bg-gray-50">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td class="p-4 text-gray-600" colspan="9">Belum ada resource.</td></tr>
        @endforelse
    </tbody>
</table>
</div>

<div class="mt-4">{{ $rows->links() }}</div>
@endsection