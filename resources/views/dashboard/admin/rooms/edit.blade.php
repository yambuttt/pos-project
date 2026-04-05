@extends('layouts.admin')
@section('title','Edit Ruangan')

@section('body')
<div class="flex items-center justify-between gap-3">
  <div class="flex items-center gap-3">
    <button id="openMobileSidebar" type="button"
      class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
    <div>
      <h1 class="text-xl font-semibold">Edit Ruangan</h1>
      <p class="text-sm text-white/70">{{ $room->name }}</p>
    </div>
  </div>

  <a href="{{ route('admin.rooms.index') }}" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15">Kembali</a>
</div>

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

<form method="POST" action="{{ route('admin.rooms.update',$room) }}" class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
  @csrf
  @method('PUT')

  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
      <label class="text-xs text-white/70">Nama</label>
      <input name="name" value="{{ old('name',$room->name) }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>
    <div>
      <label class="text-xs text-white/70">Lokasi</label>
      <input name="location_label" value="{{ old('location_label',$room->location_label) }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>
    <div>
      <label class="text-xs text-white/70">Kapasitas Minimal</label>
      <input type="number" name="capacity_min" value="{{ old('capacity_min',$room->capacity_min) }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>
    <div>
      <label class="text-xs text-white/70">Kapasitas Maksimal</label>
      <input type="number" name="capacity_max" value="{{ old('capacity_max',$room->capacity_max) }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>
  </div>

  <div class="mt-4">
    <label class="inline-flex items-center gap-2 text-sm text-white/80">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active',$room->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-white/30 bg-white/10" />
      Aktif
    </label>
  </div>

  <div class="mt-4">
    <label class="text-xs text-white/70">Catatan</label>
    <textarea name="note" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" rows="3">{{ old('note',$room->note) }}</textarea>
  </div>

  <button class="mt-5 rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
    Update
  </button>
</form>
@endsection
