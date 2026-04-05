@extends('layouts.admin')
@section('title', 'Device Absensi')

@section('body')
<div class="flex items-center justify-between">
  <div class="text-lg font-semibold text-white">Device Absensi (Approval)</div>
  <a href="{{ route('admin.attendance.qr') }}" class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-4 py-2 text-sm text-yellow-500 hover:bg-white/[0.08]">
    Kembali ke QR
  </a>
</div>

@if(session('ok'))
  <div class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
    {{ session('ok') }}
  </div>
@endif

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
  <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
    <div class="text-sm font-semibold text-white">Pending</div>
    <div class="mt-3 space-y-3">
      @forelse($pending as $d)
        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-sm text-white/80">
          <div class="font-semibold text-white">User ID: {{ $d->user_id }}</div>
          <div class="mt-1 text-xs font-mono text-white/60">{{ $d->device_hash }}</div>
          <div class="mt-1 text-xs text-white/50">{{ $d->device_name ?? '-' }}</div>

          <div class="mt-3 flex gap-2">
            <form method="POST" action="{{ route('admin.attendance.devices.approve', $d->id) }}">
              @csrf
              <button class="rounded-xl bg-yellow-500 px-3 py-2 text-xs font-semibold text-black hover:bg-yellow-400">Approve</button>
            </form>
            <form method="POST" action="{{ route('admin.attendance.devices.revoke', $d->id) }}">
              @csrf
              <button class="rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/80 hover:bg-white/[0.08]">Tolak/Cabut</button>
            </form>
          </div>
        </div>
      @empty
        <div class="text-sm text-white/60">Tidak ada pending.</div>
      @endforelse
    </div>
  </div>

  <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
    <div class="text-sm font-semibold text-white">Approved</div>
    <div class="mt-3 space-y-3">
      @forelse($approved as $d)
        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-sm text-white/80">
          <div class="font-semibold text-white">User ID: {{ $d->user_id }}</div>
          <div class="mt-1 text-xs font-mono text-white/60">{{ $d->device_hash }}</div>
          <div class="mt-3">
            <form method="POST" action="{{ route('admin.attendance.devices.revoke', $d->id) }}">
              @csrf
              <button class="rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/80 hover:bg-white/[0.08]">Cabut</button>
            </form>
          </div>
        </div>
      @empty
        <div class="text-sm text-white/60">Tidak ada approved.</div>
      @endforelse
    </div>
  </div>

  <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
    <div class="text-sm font-semibold text-white">Revoked (terakhir)</div>
    <div class="mt-3 space-y-3">
      @forelse($revoked as $d)
        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-xs text-white/70">
          User {{ $d->user_id }} • {{ $d->revoked_at?->format('d M H:i') }}
        </div>
      @empty
        <div class="text-sm text-white/60">Kosong.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection