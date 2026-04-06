@extends('layouts.admin')
@section('title', 'Device Absensi')

@section('body')
  <div class="mx-auto w-full max-w-6xl">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="text-lg font-semibold text-white">Device Absensi (Approval)</div>
        <div class="mt-1 text-sm text-white/60">
          Menampilkan nama pegawai, urutan device (Device #), dan memungkinkan admin memberi nama device.
        </div>
      </div>

      <a href="{{ route('admin.attendance.qr') }}"
        class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-4 py-2 text-sm text-yellow-500 hover:bg-white/[0.08]">
        Kembali ke QR
      </a>
    </div>

    @if (session('ok'))
      <div class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
        {{ session('ok') }}
      </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
      {{-- PENDING --}}
      <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
        <div class="flex items-center justify-between">
          <div class="text-sm font-semibold text-white">Pending</div>
          <span class="rounded-full border border-white/10 bg-white/[0.03] px-2.5 py-1 text-xs text-white/70">
            {{ $pending->count() }}
          </span>
        </div>

        <div class="mt-3 space-y-3">
          @forelse ($pending as $d)
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-sm text-white/80">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-semibold text-white">
                    {{ $d->user?->name ?? 'User #' . $d->user_id }}
                    <span class="ml-2 rounded-full border border-white/10 bg-white/[0.03] px-2 py-0.5 text-xs text-white/70">
                      Device #{{ $deviceNo[$d->id] ?? '-' }}
                    </span>
                  </div>
                  <div class="mt-0.5 text-xs text-white/60">{{ $d->user?->email ?? '-' }}</div>
                </div>

                <div class="text-xs text-white/55">
                  {{ $d->created_at?->format('d M Y H:i') }}
                </div>
              </div>

              <div class="mt-3 rounded-xl border border-white/10 bg-black/20 p-3">
                <div class="text-xs text-white/60">Device Hash</div>
                <div class="mt-1 break-all text-xs font-mono text-white/70">{{ $d->device_hash }}</div>

                <div class="mt-3 text-xs text-white/60">User Agent</div>
                <div class="mt-1 break-words text-xs text-white/70">{{ $d->user_agent ?? '-' }}</div>

                <div class="mt-3 text-xs text-white/60">Last Seen</div>
                <div class="mt-1 text-xs text-white/70">{{ $d->last_seen_at?->format('d M Y H:i') ?? '-' }}</div>
              </div>

              <div class="mt-3">
                <div class="text-xs text-white/60">Nama Device (opsional)</div>
                <form method="POST" action="{{ route('admin.attendance.devices.rename', $d->id) }}" class="mt-2 flex gap-2">
                  @csrf
                  <input name="device_name" value="{{ $d->device_name ?? '' }}"
                    placeholder="contoh: HP Adrian / Tablet Depan"
                    class="w-full rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white placeholder:text-white/40 outline-none focus:border-yellow-500/35" />
                  <button class="shrink-0 rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/85 hover:bg-white/[0.08]">
                    Simpan
                  </button>
                </form>
              </div>

              <div class="mt-3 flex gap-2">
                <form method="POST" action="{{ route('admin.attendance.devices.approve', $d->id) }}">
                  @csrf
                  <button class="rounded-xl bg-yellow-500 px-3 py-2 text-xs font-semibold text-black hover:bg-yellow-400">
                    Approve
                  </button>
                </form>

                <form method="POST" action="{{ route('admin.attendance.devices.revoke', $d->id) }}">
                  @csrf
                  <button class="rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/80 hover:bg-white/[0.08]">
                    Tolak/Cabut
                  </button>
                </form>
              </div>
            </div>
          @empty
            <div class="text-sm text-white/60">Tidak ada pending.</div>
          @endforelse
        </div>
      </div>

      {{-- APPROVED --}}
      <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
        <div class="flex items-center justify-between">
          <div class="text-sm font-semibold text-white">Approved</div>
          <span class="rounded-full border border-white/10 bg-white/[0.03] px-2.5 py-1 text-xs text-white/70">
            {{ $approved->count() }}
          </span>
        </div>

        <div class="mt-3 space-y-3">
          @forelse ($approved as $d)
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-sm text-white/80">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-semibold text-white">
                    {{ $d->user?->name ?? 'User #' . $d->user_id }}
                    <span class="ml-2 rounded-full border border-white/10 bg-white/[0.03] px-2 py-0.5 text-xs text-white/70">
                      Device #{{ $deviceNo[$d->id] ?? '-' }}
                    </span>
                  </div>
                  <div class="mt-0.5 text-xs text-white/60">{{ $d->user?->email ?? '-' }}</div>
                  <div class="mt-1 text-xs text-white/55">
                    Nama device: <span class="text-white/80">{{ $d->device_name ?? '-' }}</span>
                  </div>
                </div>

                <div class="text-xs text-white/55">
                  Approved: {{ $d->approved_at?->format('d M Y H:i') ?? '-' }}
                </div>
              </div>

              <div class="mt-3 rounded-xl border border-white/10 bg-black/20 p-3">
                <div class="text-xs text-white/60">Device Hash</div>
                <div class="mt-1 break-all text-xs font-mono text-white/70">{{ $d->device_hash }}</div>
              </div>

              <div class="mt-3">
                <div class="text-xs text-white/60">Edit Nama Device</div>
                <form method="POST" action="{{ route('admin.attendance.devices.rename', $d->id) }}" class="mt-2 flex gap-2">
                  @csrf
                  <input name="device_name" value="{{ $d->device_name ?? '' }}"
                    placeholder="contoh: HP Adrian / Tablet Depan"
                    class="w-full rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white placeholder:text-white/40 outline-none focus:border-yellow-500/35" />
                  <button class="shrink-0 rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/85 hover:bg-white/[0.08]">
                    Simpan
                  </button>
                </form>
              </div>

              <div class="mt-3">
                <form method="POST" action="{{ route('admin.attendance.devices.revoke', $d->id) }}">
                  @csrf
                  <button class="rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/80 hover:bg-white/[0.08]">
                    Cabut
                  </button>
                </form>
              </div>
            </div>
          @empty
            <div class="text-sm text-white/60">Tidak ada approved.</div>
          @endforelse
        </div>
      </div>

      {{-- REVOKED --}}
      <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
        <div class="flex items-center justify-between">
          <div class="text-sm font-semibold text-white">Revoked (terakhir)</div>
          <span class="rounded-full border border-white/10 bg-white/[0.03] px-2.5 py-1 text-xs text-white/70">
            {{ $revoked->count() }}
          </span>
        </div>

        <div class="mt-3 space-y-3">
          @forelse ($revoked as $d)
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-xs text-white/70">
              <div class="font-semibold text-white/85">
                {{ $d->user?->name ?? 'User #' . $d->user_id }}
                <span class="ml-2 rounded-full border border-white/10 bg-white/[0.03] px-2 py-0.5 text-[11px] text-white/70">
                  Device #{{ $deviceNo[$d->id] ?? '-' }}
                </span>
              </div>
              <div class="mt-1 text-white/55">{{ $d->user?->email ?? '-' }}</div>
              <div class="mt-2 text-white/60">
                Dicabut: {{ $d->revoked_at?->format('d M Y H:i') ?? '-' }} • Nama device: {{ $d->device_name ?? '-' }}
              </div>
            </div>
          @empty
            <div class="text-sm text-white/60">Kosong.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection