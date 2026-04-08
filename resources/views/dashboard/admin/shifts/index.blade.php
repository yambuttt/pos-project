@extends('layouts.admin')
@section('title', 'Shift Pegawai')

@section('body')
  <div class="mx-auto w-full max-w-6xl">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="text-lg font-semibold text-white">Shift Pegawai</div>
        <div class="mt-1 text-sm text-white/60">Atur skema shift (fixed/rotation) dan override (tukeran) per tanggal.</div>
      </div>

      <form class="flex gap-2" method="GET" action="{{ route('admin.shifts.index') }}">
        <input name="q" value="{{ $q }}"
          class="w-[220px] rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-sm text-white placeholder:text-white/40 outline-none focus:border-yellow-500/35"
          placeholder="Cari pegawai..." />
        <button class="rounded-xl border border-white/15 bg-white/[0.04] px-4 py-2 text-sm text-white/85 hover:bg-white/[0.08]">
          Cari
        </button>
      </form>
    </div>

    @if (session('ok'))
      <div class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
        {{ session('ok') }}
      </div>
    @endif
    @if (session('error'))
      <div class="mt-4 rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-100">
        {{ session('error') }}
      </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      @forelse($employees as $u)
        <a href="{{ route('admin.shifts.edit', $u) }}"
           class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 hover:bg-white/[0.03] transition">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="text-sm font-semibold text-white truncate">{{ $u->name }}</div>
              <div class="mt-1 text-xs text-white/60 truncate">{{ $u->email }}</div>
            </div>
            <span class="shrink-0 rounded-full border border-white/10 bg-white/[0.03] px-2.5 py-1 text-xs text-white/70">
              {{ strtoupper($u->shift_scheme ?? 'fixed') }}
            </span>
          </div>

          <div class="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
            <div class="text-xs text-white/60">Default shift</div>
            <div class="mt-1 text-sm text-white/85">
              @php
                $ds = $shifts->firstWhere('id', $u->default_shift_id);
              @endphp
              {{ $ds?->name ?? '-' }}
            </div>
          </div>

          <div class="mt-3 text-xs text-white/60">Klik untuk edit</div>
        </a>
      @empty
        <div class="text-sm text-white/60">Tidak ada pegawai.</div>
      @endforelse
    </div>

    <div class="mt-6">
      {{ $employees->links() }}
    </div>
  </div>
@endsection