@extends('layouts.admin')
@section('title', 'Tambah Meja')

@section('body')
    <div class="flex items-center gap-3">
        <button id="openMobileSidebar" type="button"
            class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
            ☰
        </button>
        <h1 class="text-xl font-semibold">Tambah Meja</h1>
    </div>

    @if ($errors->any())
        <div class="mt-4 whitespace-pre-line rounded-2xl border border-red-200/20 bg-red-500/10 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tables.store') }}"
        class="mt-5 space-y-4 rounded-2xl border border-white/15 bg-white/10 p-6 backdrop-blur-2xl">
        @csrf

        <div>
            <label class="text-sm">Nama / Nomor Meja</label>
            <input name="name" value="{{ old('name') }}"
                placeholder="Contoh: 1, 2, VIP, A1, Meja Depan"
                class="mt-2 w-full rounded-xl bg-white/10 border border-white/20 px-4 py-2 outline-none focus:border-white/30" />
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-white/20 bg-white/10">
            Aktif
        </label>

        <button class="w-full rounded-xl bg-blue-600/85 py-3 font-semibold hover:bg-blue-500/85">
            Simpan
        </button>
    </form>
@endsection