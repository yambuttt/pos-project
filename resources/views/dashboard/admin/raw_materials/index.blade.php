@extends('layouts.admin')
@section('title', 'Raw Materials')

@section('body')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex items-center gap-3">
            <button id="openMobileSidebar" type="button"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
                ☰
            </button>

            <div>
                <h1 class="text-xl font-semibold">Bahan Baku</h1>
                <p class="text-sm text-white/70">Kelola stok, minimum stok, dan harga default bahan baku.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.raw_materials.create') }}"
                class="rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
                + Tambah Bahan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-2xl border border-emerald-200/30 bg-emerald-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-[24px] border border-white/15 bg-white/10 p-5 backdrop-blur-2xl">
            <div class="text-xs uppercase tracking-[0.18em] text-white/45">Total Bahan</div>
            <div class="mt-3 text-3xl font-semibold">{{ $totalMaterials }}</div>
            <div class="mt-2 text-sm text-white/60">Jumlah item bahan baku terdaftar</div>
        </div>

        <div class="rounded-[24px] border border-red-400/20 bg-red-500/10 p-5 backdrop-blur-2xl">
            <div class="text-xs uppercase tracking-[0.18em] text-red-200/70">Low Stock</div>
            <div class="mt-3 text-3xl font-semibold text-red-100">{{ $lowStockCount }}</div>
            <div class="mt-2 text-sm text-red-100/70">Item yang perlu segera direstok</div>
        </div>

        <div class="rounded-[24px] border border-yellow-500/20 bg-yellow-500/10 p-5 backdrop-blur-2xl">
            <div class="text-xs uppercase tracking-[0.18em] text-yellow-100/70">Estimasi Nilai Stok</div>
            <div class="mt-3 text-3xl font-semibold text-yellow-100">Rp {{ number_format((float) $totalStockValue, 0, ',', '.') }}</div>
            <div class="mt-2 text-sm text-yellow-100/70">Berdasarkan harga default per unit</div>
        </div>
    </div>

    <div class="mt-5 rounded-[26px] border border-white/15 bg-white/10 p-4 backdrop-blur-2xl">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="w-full md:max-w-md">
                <input
                    name="q"
                    value="{{ $q }}"
                    placeholder="Cari bahan baku atau unit..."
                    class="w-full rounded-xl border border-white/15 bg-black/20 px-4 py-3 text-sm outline-none placeholder:text-white/35 focus:border-yellow-500/30"
                >
            </div>

            <div class="flex items-center gap-2">
                <button
                    class="rounded-xl bg-white/10 px-4 py-3 text-sm font-semibold hover:bg-white/15">
                    Cari
                </button>

                @if($q)
                    <a href="{{ route('admin.raw_materials.index') }}"
                        class="rounded-xl border border-white/15 bg-white/5 px-4 py-3 text-sm font-semibold hover:bg-white/10">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-2">
        @forelse($materials as $m)
            @php
                $isLow = (float) $m->stock_on_hand <= (float) ($m->min_stock ?? 0);
                $stockPercent = ((float) ($m->min_stock ?? 0) > 0)
                    ? min(100, max(8, ((float) $m->stock_on_hand / (float) $m->min_stock) * 100))
                    : 100;
            @endphp

            <div class="rounded-[26px] border {{ $isLow ? 'border-red-400/25 bg-red-500/10' : 'border-white/15 bg-white/10' }} p-5 backdrop-blur-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-semibold">{{ $m->name }}</h2>

                            @if($isLow)
                                <span class="rounded-full border border-red-300/25 bg-red-500/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-red-200">
                                    Low Stock
                                </span>
                            @else
                                <span class="rounded-full border border-emerald-300/20 bg-emerald-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-emerald-200">
                                    Aman
                                </span>
                            @endif
                        </div>

                        <div class="mt-1 text-sm text-white/60">
                            Unit: <span class="text-white/85">{{ $m->unit }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.raw_materials.edit', $m) }}"
                            class="rounded-xl border border-yellow-500/25 bg-yellow-500/10 px-3 py-2 text-xs font-semibold text-yellow-100 hover:bg-yellow-500/15">
                            Edit
                        </a>

                        <form method="POST" action="{{ route('admin.raw_materials.destroy', $m) }}"
                            onsubmit="return confirm('Hapus bahan baku ini?')">
                            @csrf
                            @method('DELETE')
                            <button
                                class="rounded-xl border border-red-300/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100 hover:bg-red-500/15">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <div class="text-[11px] uppercase tracking-[0.14em] text-white/45">Stok</div>
                        <div class="mt-2 text-lg font-semibold">{{ $m->stock_on_hand }}</div>
                        <div class="text-xs text-white/55">{{ $m->unit }}</div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <div class="text-[11px] uppercase tracking-[0.14em] text-white/45">Minimum</div>
                        <div class="mt-2 text-lg font-semibold">{{ $m->min_stock ?? 0 }}</div>
                        <div class="text-xs text-white/55">{{ $m->unit }}</div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <div class="text-[11px] uppercase tracking-[0.14em] text-white/45">Harga Default</div>
                        <div class="mt-2 text-lg font-semibold">Rp {{ number_format((float) ($m->default_cost ?? 0), 0, ',', '.') }}</div>
                        <div class="text-xs text-white/55">per {{ $m->unit }}</div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                        <div class="text-[11px] uppercase tracking-[0.14em] text-white/45">Nilai Stok</div>
                        <div class="mt-2 text-lg font-semibold">
                            Rp {{ number_format((float) $m->stock_on_hand * (float) ($m->default_cost ?? 0), 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-white/55">estimasi</div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex items-center justify-between text-xs text-white/55">
                        <span>Status stok</span>
                        <span>{{ $isLow ? 'Perlu restok' : 'Masih aman' }}</span>
                    </div>

                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/10">
                        <div
                            class="h-full rounded-full {{ $isLow ? 'bg-red-400' : 'bg-yellow-400' }}"
                            style="width: {{ $stockPercent }}%">
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-[26px] border border-white/15 bg-white/10 px-6 py-10 text-center backdrop-blur-2xl">
                <div class="text-lg font-semibold">Belum ada bahan baku</div>
                <p class="mt-2 text-sm text-white/60">Tambahkan bahan baku pertama untuk mulai mengelola inventory.</p>

                <a href="{{ route('admin.raw_materials.create') }}"
                    class="mt-5 inline-flex rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
                    + Tambah Bahan
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $materials->links() }}
    </div>
@endsection