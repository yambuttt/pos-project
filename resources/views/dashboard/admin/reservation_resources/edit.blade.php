@extends('layouts.admin')
@section('title', 'Edit Resource Reservasi')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Edit Resource Reservasi</h1>
        <p class="text-sm text-white/70">{{ $resource->name }}</p>
      </div>
    </div>

    <a href="{{ route('admin.reservation_resources.index') }}"
      class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold hover:bg-white/10">
      ← Kembali
    </a>
  </div>

  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-red-300/20 bg-red-500/10 px-4 py-3 text-sm whitespace-pre-line">
      ❌ {{ $errors->first() }}
    </div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <form method="POST" action="{{ route('admin.reservation_resources.update', $resource) }}" class="space-y-5">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div>
          <div class="text-sm text-white/70">Tipe</div>
          <select name="type"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
            <option value="TABLE" @selected($resource->type==='TABLE')>TABLE</option>
            <option value="ROOM" @selected($resource->type==='ROOM')>ROOM</option>
            <option value="HALL" @selected($resource->type==='HALL')>HALL</option>
          </select>
        </div>
        <div>
          <div class="text-sm text-white/70">Kapasitas</div>
          <input type="number" name="capacity" value="{{ $resource->capacity }}" min="1"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
        </div>
      </div>

      <div>
        <div class="text-sm text-white/70">Nama</div>
        <input name="name" value="{{ $resource->name }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
      </div>

      <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div>
          <div class="text-sm text-white/70">Harga per jam (opsional)</div>
          <input type="number" name="hourly_rate" min="0" value="{{ $resource->hourly_rate ?? 0 }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
        </div>
        <div>
          <div class="text-sm text-white/70">Harga flat (opsional)</div>
          <input type="number" name="flat_rate" min="0" value="{{ $resource->flat_rate ?? 0 }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div>
          <div class="text-sm text-white/70">Min durasi (menit)</div>
          <input type="number" name="min_duration_minutes" value="{{ $resource->min_duration_minutes }}" min="15"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
        </div>
        <div>
          <div class="text-sm text-white/70">Buffer (menit)</div>
          <input type="number" name="buffer_minutes" value="{{ $resource->buffer_minutes }}" min="0"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
        </div>
      </div>

      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked($resource->is_active)>
        <span class="text-white/80">Aktif</span>
      </label>

      <button class="w-full rounded-2xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
        Update
      </button>
    </form>
  </div>
@endsection