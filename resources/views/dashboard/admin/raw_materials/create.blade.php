@extends('layouts.admin')
@section('title','Tambah Bahan Baku')

@section('body')
  {{-- TOP BAR --}}
  <div class="flex items-start justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden"
        title="Menu">
        ☰
      </button>

      <div>
        <h1 class="text-xl font-semibold">Tambah Bahan Baku</h1>
        <p class="text-sm text-white/70">Isi data bahan baku untuk inventory. Stok awal akan tercatat sebagai adjustment.</p>
      </div>
    </div>

    <a href="{{ route('admin.raw_materials.index') }}"
      class="shrink-0 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
      ← Kembali
    </a>
  </div>

  {{-- ERRORS --}}
  @if ($errors->any())
    <div class="mt-4 whitespace-pre-line rounded-2xl border border-red-200/30 bg-red-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.raw_materials.store') }}"
    class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.15fr_.85fr]">
    @csrf

    {{-- LEFT: MAIN FORM --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
      <div class="flex items-center justify-between gap-3">
        <div>
          <div class="text-sm font-semibold">Informasi Bahan</div>
          <div class="text-xs text-white/60">Wajib isi nama, unit, dan stok awal.</div>
        </div>

        <span class="hidden sm:inline-flex rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs text-white/70">
          RM • Inventory
        </span>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Nama --}}
        <div class="sm:col-span-2">
          <label class="text-sm text-white/80">Nama</label>
          <input
            name="name"
            value="{{ old('name') }}"
            placeholder="Contoh: Beras, Gula, Ayam Fillet"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
            required
            autofocus
          />
          <p class="mt-1 text-xs text-white/55">Gunakan nama yang konsisten agar mudah dicari.</p>
        </div>

        {{-- Unit --}}
        <div>
          <label class="text-sm text-white/80">Unit</label>
          <input
            name="unit"
            value="{{ old('unit') }}"
            placeholder="kg / gram / ml / pcs"
            list="unit_list"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
            required
          />
          <datalist id="unit_list">
            <option value="kg"></option>
            <option value="gram"></option>
            <option value="liter"></option>
            <option value="ml"></option>
            <option value="pcs"></option>
            <option value="pack"></option>
            <option value="box"></option>
            <option value="tabung"></option>
            <option value="galon"></option>
          </datalist>
          <p class="mt-1 text-xs text-white/55">Contoh: kg, gram, ml, pcs.</p>
        </div>

        {{-- Default Cost --}}
        <div>
          <label class="text-sm text-white/80">Harga Default / Unit (opsional)</label>
          <input
            name="default_cost"
            type="number"
            min="0"
            step="0.01"
            value="{{ old('default_cost') }}"
            placeholder="Contoh: 15000"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
          />
          <p class="mt-1 text-xs text-white/55">Dipakai sebagai referensi biaya jika kamu butuh.</p>
        </div>

        {{-- Stock --}}
        <div>
          <label class="text-sm text-white/80">Stok Awal</label>
          <input
            name="stock_on_hand"
            type="number"
            min="0"
            step="0.01"
            value="{{ old('stock_on_hand', 0) }}"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40"
            required
          />
          <p class="mt-1 text-xs text-white/55">Jika &gt; 0, sistem akan membuat log “Initial stock”.</p>
        </div>

        {{-- Min Stock --}}
        <div>
          <label class="text-sm text-white/80">Minimum Stock (opsional)</label>
          <input
            name="min_stock"
            type="number"
            min="0"
            step="0.01"
            value="{{ old('min_stock') }}"
            placeholder="Contoh: 10"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
          />
          <p class="mt-1 text-xs text-white/55">Untuk penanda “Low Stock” di list bahan.</p>
        </div>
      </div>

      {{-- ACTIONS (desktop) --}}
      <div class="mt-6 hidden sm:flex items-center justify-end gap-2">
        <a href="{{ route('admin.raw_materials.index') }}"
          class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
          Batal
        </a>
        <button
          class="rounded-xl bg-blue-600/85 px-5 py-2 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
          Simpan
        </button>
      </div>
    </div>

    {{-- RIGHT: SUMMARY / HELP --}}
    <aside class="space-y-5">
      <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
        <div class="text-sm font-semibold">Ringkasan</div>
        <div class="mt-2 space-y-2 text-sm text-white/70">
          <div class="flex items-center justify-between gap-3">
            <span>Nama</span>
            <span class="text-white/50">wajib</span>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span>Unit</span>
            <span class="text-white/50">wajib</span>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span>Stok Awal</span>
            <span class="text-white/50">wajib</span>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span>Minimum Stock</span>
            <span class="text-white/50">opsional</span>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span>Harga Default</span>
            <span class="text-white/50">opsional</span>
          </div>
        </div>

        <div class="mt-4 rounded-2xl border border-white/15 bg-white/5 p-4 text-xs text-white/70">
          Tips:
          <ul class="mt-2 list-disc space-y-1 pl-4">
            <li>Gunakan unit konsisten (misal semua cairan: ml atau liter).</li>
            <li>Set minimum stock untuk bahan yang sering habis agar mudah dipantau.</li>
            <li>Stok awal &gt; 0 akan masuk ke ledger sebagai “Initial stock”.</li>
          </ul>
        </div>
      </div>

      {{-- ACTIONS (mobile sticky) --}}
      <div class="sm:hidden sticky bottom-3">
        <div class="rounded-2xl border border-white/20 bg-white/10 p-3 backdrop-blur-2xl">
          <div class="flex gap-2">
            <a href="{{ route('admin.raw_materials.index') }}"
              class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-semibold backdrop-blur-xl hover:bg-white/15 text-center">
              Batal
            </a>
            <button
              class="w-full rounded-xl bg-blue-600/85 px-4 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
              Simpan
            </button>
          </div>
        </div>
      </div>
    </aside>
  </form>
@endsection