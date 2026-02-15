@extends('layouts.admin')
@section('title', 'Raw Materials')

@section('body')
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button id="openMobileSidebar" type="button"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
                ☰
            </button>

            <h1 class="text-xl font-semibold">Bahan Baku</h1>
        </div>

        <a href="{{ route('admin.raw_materials.create') }}"
            class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
            + Tambah Bahan
        </a>
    </div>


    @if(session('success'))
        <div class="mt-4 rounded-xl bg-green-500/10 border border-green-400/30 p-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($materials as $m)
            <div class="rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-2xl">
                <div class="text-lg font-semibold">{{ $m->name }}</div>
                <div class="text-sm text-white/70 mt-1">
                    Stok: {{ $m->stock_on_hand }} {{ $m->unit }}
                </div>

                @if($m->stock_on_hand <= $m->min_stock)
                    <div class="mt-2 text-xs text-red-300 font-semibold">
                        ⚠ Low Stock
                    </div>
                @endif

                <div class="mt-3 text-xs text-white/60">
                    Min: {{ $m->min_stock }} {{ $m->unit }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $materials->links() }}
    </div>
@endsection