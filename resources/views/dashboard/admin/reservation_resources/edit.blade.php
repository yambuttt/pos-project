@extends('dashboard.admin._reservation_layout')

@section('title', 'Edit Resource')
@section('page_title', 'Edit Resource')

@section('content')
<form method="POST" action="{{ route('admin.reservation_resources.update', $resource) }}" class="grid gap-4 max-w-2xl">
    @csrf
    @method('PUT')

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-sm">Tipe</label>
            <select name="type" class="w-full px-3 py-2 border rounded">
                <option value="TABLE" @selected($resource->type==='TABLE')>TABLE</option>
                <option value="ROOM" @selected($resource->type==='ROOM')>ROOM</option>
                <option value="HALL" @selected($resource->type==='HALL')>HALL</option>
            </select>
        </div>
        <div>
            <label class="text-sm">Kapasitas</label>
            <input type="number" name="capacity" value="{{ $resource->capacity }}" min="1" class="w-full px-3 py-2 border rounded">
        </div>
    </div>

    <div>
        <label class="text-sm">Nama</label>
        <input name="name" value="{{ $resource->name }}" class="w-full px-3 py-2 border rounded">
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-sm">Harga per jam (opsional)</label>
            <input type="number" name="hourly_rate" min="0" value="{{ $resource->hourly_rate ?? 0 }}" class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="text-sm">Harga flat (opsional)</label>
            <input type="number" name="flat_rate" min="0" value="{{ $resource->flat_rate ?? 0 }}" class="w-full px-3 py-2 border rounded">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-sm">Min durasi (menit)</label>
            <input type="number" name="min_duration_minutes" value="{{ $resource->min_duration_minutes }}" min="15" class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="text-sm">Buffer (menit)</label>
            <input type="number" name="buffer_minutes" value="{{ $resource->buffer_minutes }}" min="0" class="w-full px-3 py-2 border rounded">
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked($resource->is_active)>
        <span class="text-sm">Aktif</span>
    </div>

    <div class="flex gap-2">
        <button class="px-4 py-2 rounded bg-blue-600 text-white">Update</button>
        <a href="{{ route('admin.reservation_resources.index') }}" class="px-4 py-2 rounded border">Batal</a>
    </div>
</form>
@endsection