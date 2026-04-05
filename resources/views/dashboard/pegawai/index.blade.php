@extends('layouts.pegawai')
@section('title','Dashboard Pegawai')

@section('page_label','Pegawai')
@section('page_title','Dashboard')

@section('content')
@php
  $att = $todayAttendance;
  $status = 'Belum Check-in';
  $badgeClass = 'border-white/15 bg-white/[0.03] text-white/80';

  if($att?->check_in_at && !$att?->check_out_at){
    $status = 'Sudah Check-in';
    $badgeClass = 'border-emerald-300/20 bg-emerald-500/10 text-emerald-100';
  }
  if($att?->check_in_at && $att?->check_out_at){
    $status = 'Sudah Check-out';
    $badgeClass = 'border-yellow-300/20 bg-yellow-500/10 text-yellow-100';
  }
@endphp

<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
    <div class="text-xs uppercase tracking-[0.16em] text-white/45">Status Hari Ini</div>
    <div class="mt-2 flex items-center justify-between gap-3">
      <div class="text-2xl font-semibold text-white">{{ $status }}</div>
      <span class="rounded-full border px-3 py-1 text-[11px] font-semibold {{ $badgeClass }}">
        {{ $att?->date ?? now()->format('Y-m-d') }}
      </span>
    </div>
    <div class="mt-2 text-sm text-white/55">
      Gunakan menu <span class="text-yellow-400 font-semibold">Absensi</span> untuk check-in / check-out.
    </div>
  </div>

  <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
    <div class="text-xs uppercase tracking-[0.16em] text-white/45">Jam Masuk</div>
    <div class="mt-2 text-3xl font-semibold text-white">
      {{ $att?->check_in_at ? \Carbon\Carbon::parse($att->check_in_at)->format('H:i') : '--:--' }}
    </div>
    <div class="mt-2 text-sm text-white/55">Waktu check-in tercatat</div>
  </div>

  <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
    <div class="text-xs uppercase tracking-[0.16em] text-white/45">Jam Pulang</div>
    <div class="mt-2 text-3xl font-semibold text-white">
      {{ $att?->check_out_at ? \Carbon\Carbon::parse($att->check_out_at)->format('H:i') : '--:--' }}
    </div>
    <div class="mt-2 text-sm text-white/55">Waktu check-out tercatat</div>
  </div>

  <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
    <div class="text-xs uppercase tracking-[0.16em] text-white/45">Durasi Hari Ini</div>
    <div class="mt-2 text-3xl font-semibold text-white">{{ $durationToday ?? '--:--:--' }}</div>
    <div class="mt-2 text-sm text-white/55">Hitung dari check-in → check-out</div>
  </div>
</section>

<section class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.1fr_.9fr]">
  <div class="rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl sm:p-6">
    <div class="flex items-start justify-between gap-3">
      <div>
        <div class="text-lg font-semibold text-white">Aksi Cepat</div>
        <div class="mt-1 text-sm text-white/60">Masuk ke halaman absensi untuk verifikasi device + lokasi + scan QR + selfie.</div>
      </div>
      <div class="rounded-xl border border-yellow-500/16 bg-white/[0.03] px-3 py-2 text-sm text-white/80">
        Kehadiran bulan ini: <span class="font-semibold text-white">{{ $totalMonth }}</span> hari
      </div>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
      <a href="{{ route('pegawai.attendance') }}"
        class="rounded-2xl border border-yellow-500/16 bg-white/[0.03] px-5 py-4 hover:bg-white/[0.06]">
        <div class="text-sm font-semibold text-white">🔒 Buka Absensi</div>
        <div class="mt-1 text-xs text-white/60">Check-in / Check-out</div>
      </a>

      <a href="{{ route('pegawai.attendance.history') }}"
        class="rounded-2xl border border-yellow-500/16 bg-white/[0.03] px-5 py-4 hover:bg-white/[0.06]">
        <div class="text-sm font-semibold text-white">🗓️ Riwayat Absensi</div>
        <div class="mt-1 text-xs text-white/60">Lihat semua catatan absensi kamu</div>
      </a>
    </div>

    <div class="mt-5 rounded-2xl border border-yellow-500/12 bg-white/[0.02] p-4 text-xs text-white/60">
      Catatan: jika device kamu belum di-approve admin, status absensi akan tertolak sampai admin approve.
    </div>
  </div>

  <div class="rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl sm:p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-lg font-semibold text-white">Riwayat Terakhir</div>
        <div class="mt-1 text-sm text-white/60">10 data terakhir.</div>
      </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-yellow-500/12">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
          <thead class="bg-white/[0.03] text-xs text-white/60">
            <tr>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Masuk</th>
              <th class="px-4 py-3">Pulang</th>
              <th class="px-4 py-3">Durasi</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-yellow-500/10">
            @forelse($latest as $a)
              @php
                $dur = null;
                if($a->check_in_at && $a->check_out_at){
                  $in = \Carbon\Carbon::parse($a->check_in_at);
                  $out = \Carbon\Carbon::parse($a->check_out_at);
                  if($out->gte($in)){
                    $sec = $in->diffInSeconds($out);
                    $h = intdiv($sec, 3600);
                    $m = intdiv($sec % 3600, 60);
                    $s = $sec % 60;
                    $dur = str_pad((string)$h,2,'0',STR_PAD_LEFT).':'.str_pad((string)$m,2,'0',STR_PAD_LEFT).':'.str_pad((string)$s,2,'0',STR_PAD_LEFT);
                  }
                }
                $st = 'Belum check-in';
                if($a->check_in_at && !$a->check_out_at) $st = 'Sudah check-in';
                if($a->check_in_at && $a->check_out_at) $st = 'Selesai';
              @endphp
              <tr class="hover:bg-white/[0.02]">
                <td class="px-4 py-3 text-white/85">{{ \Carbon\Carbon::parse($a->date)->format('d M Y') }}</td>
                <td class="px-4 py-3 text-white/80">{{ $a->check_in_at ? \Carbon\Carbon::parse($a->check_in_at)->format('H:i:s') : '--' }}</td>
                <td class="px-4 py-3 text-white/80">{{ $a->check_out_at ? \Carbon\Carbon::parse($a->check_out_at)->format('H:i:s') : '--' }}</td>
                <td class="px-4 py-3 text-white/80">{{ $dur ?? '--' }}</td>
                <td class="px-4 py-3 text-white/70">{{ $st }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-6 text-center text-white/60">Belum ada data absensi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>
@endsection