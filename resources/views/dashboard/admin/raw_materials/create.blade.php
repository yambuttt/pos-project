@extends('layouts.admin')
@section('title','Tambah Bahan')

@section('body')
<h1 class="text-xl font-semibold">Tambah Bahan Baku</h1>

<form method="POST" action="{{ route('admin.raw_materials.store') }}"
      class="mt-5 space-y-4 rounded-2xl border border-white/15 bg-white/10 p-6 backdrop-blur-2xl">
    @csrf

    <div>
        <label class="text-sm">Nama</label>
        <input name="name" class="mt-2 w-full rounded-xl bg-white/10 border border-white/20 px-4 py-2" />
    </div>

    <div>
        <label class="text-sm">Unit (gram/ml/pcs)</label>
        <input name="unit" class="mt-2 w-full rounded-xl bg-white/10 border border-white/20 px-4 py-2" />
    </div>

    <div>
        <label class="text-sm">Stok Awal</label>
        <input name="stock_on_hand" type="number" step="0.01"
               class="mt-2 w-full rounded-xl bg-white/10 border border-white/20 px-4 py-2" />
    </div>

    <div>
        <label class="text-sm">Minimum Stock</label>
        <input name="min_stock" type="number" step="0.01"
               class="mt-2 w-full rounded-xl bg-white/10 border border-white/20 px-4 py-2" />
    </div>

    <div>
        <label class="text-sm">Harga Default per Unit</label>
        <input name="default_cost" type="number" step="0.01"
               class="mt-2 w-full rounded-xl bg-white/10 border border-white/20 px-4 py-2" />
    </div>

    <button class="w-full rounded-xl bg-blue-600/85 py-3 font-semibold hover:bg-blue-500/85">
        Simpan
    </button>
</form>
@endsection
