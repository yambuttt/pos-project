@extends('layouts.admin')
@section('title','Tambah Paket Buffet')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Tambah Paket Buffet</h1>
        <p class="text-sm text-white/70">Buat paket dulu, isi item setelahnya.</p>
      </div>
    </div>
    <a href="{{ route('admin.buffet_packages.index') }}"
      class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold hover:bg-white/10">← Kembali</a>
  </div>

  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-red-300/20 bg-red-500/10 px-4 py-3 text-sm">❌ {{ $errors->first() }}</div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <form method="POST" action="{{ route('admin.buffet_packages.store') }}" class="space-y-4">
      @csrf
      <div>
        <div class="text-sm text-white/70">Nama Paket</div>
        <input name="name" value="{{ old('name') }}"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none" required>
      </div>

      <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div>
          <div class="text-sm text-white/70">Pricing Type</div>
          <select name="pricing_type"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
            <option value="per_pax">per_pax</option>
            <option value="per_event">per_event</option>
          </select>
        </div>
        <div>
          <div class="text-sm text-white/70">Harga (Rp)</div>
          <input type="number" name="price" min="0" value="{{ old('price',0) }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none" required>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div>
          <div class="text-sm text-white/70">Min Pax (opsional)</div>
          <input type="number" name="min_pax" min="1" value="{{ old('min_pax') }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
        </div>
        <label class="flex items-center gap-2 text-sm mt-7">
          <input type="checkbox" name="is_active" value="1" checked>
          <span class="text-white/80">Aktif</span>
        </label>
      </div>

      <div>
        <div class="text-sm text-white/70">Notes</div>
        <textarea name="notes" rows="3"
          class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">{{ old('notes') }}</textarea>
      </div>

      <button class="w-full rounded-2xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
        Simpan
      </button>
    </form>
  </div>
@endsection