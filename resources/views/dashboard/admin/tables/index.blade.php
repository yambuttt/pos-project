@extends('layouts.admin')
@section('title', 'Meja')

@section('body')
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button id="openMobileSidebar" type="button"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
                ☰
            </button>

            <div>
                <h1 class="text-xl font-semibold">Meja</h1>
                <p class="text-xs text-white/70">Atur jumlah & nama meja (nomor / nama bebas).</p>
            </div>
        </div>

        <a href="{{ route('admin.tables.create') }}"
            class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
            + Tambah Meja
        </a>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-xl bg-green-500/10 border border-green-400/30 p-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($tables as $t)
            <div class="rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-2xl">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-lg font-semibold">{{ $t->name }}</div>
                        <div class="mt-1 text-xs">
                            Status:
                            <span class="{{ $t->is_active ? 'text-green-200' : 'text-red-200' }}">
                                {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.tables.edit', $t->id) }}"
                            class="rounded-xl border border-white/15 bg-white/5 px-3 py-2 text-xs hover:bg-white/10">
                            Edit
                        </a>

                        <form method="POST" action="{{ route('admin.tables.destroy', $t->id) }}"
                              onsubmit="return confirm('Hapus meja ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-xl border border-red-300/20 bg-red-500/10 px-3 py-2 text-xs text-red-100 hover:bg-red-500/15">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-3 text-[11px] text-white/60">
                    Dibuat: {{ $t->created_at?->format('d M Y H:i') }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $tables->links() }}
    </div>
@endsection