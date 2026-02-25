@extends('layouts.pegawai')
@section('title','Dashboard Pegawai')

@section('page_label','Pegawai')
@section('page_title','Dashboard Absensi')

@section('content')
  {{-- Summary cards --}}
  <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-[22px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
      <div class="text-xs text-white/70">Status Hari Ini</div>
      <div class="mt-1 text-xl font-semibold">Belum Check-in</div>
      <div class="mt-2 text-xs text-white/70">Ini masih layout, logic absensi menyusul.</div>
    </div>

    <div class="rounded-[22px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
      <div class="text-xs text-white/70">Jam Masuk</div>
      <div class="mt-1 text-xl font-semibold">--:--</div>
    </div>

    <div class="rounded-[22px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
      <div class="text-xs text-white/70">Jam Pulang</div>
      <div class="mt-1 text-xl font-semibold">--:--</div>
    </div>

    <div class="rounded-[22px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
      <div class="text-xs text-white/70">Total Kehadiran (bulan ini)</div>
      <div class="mt-1 text-xl font-semibold">0 hari</div>
    </div>
  </section>

  {{-- Actions + history --}}
  <section class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1fr_1fr]">
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
      <div class="text-sm font-semibold">Aksi Absensi</div>
      <div class="mt-1 text-xs text-white/70">Tombol ini belum ada backend, baru layout.</div>

      <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <button class="rounded-2xl border border-white/20 bg-white/10 px-4 py-4 text-left hover:bg-white/15">
          <div class="text-sm font-semibold">✅ Check-in</div>
          <div class="mt-1 text-xs text-white/70">Catat jam masuk</div>
        </button>

        <button class="rounded-2xl border border-white/20 bg-white/10 px-4 py-4 text-left hover:bg-white/15">
          <div class="text-sm font-semibold">🏁 Check-out</div>
          <div class="mt-1 text-xs text-white/70">Catat jam pulang</div>
        </button>
      </div>

      <div class="mt-5 rounded-2xl border border-white/15 bg-white/5 p-4 text-xs text-white/70">
        Rencana: tambahkan lokasi (opsional), catatan kerja, dan approval admin.
      </div>
    </div>

    <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
      <div class="text-sm font-semibold">Riwayat Absensi</div>
      <div class="mt-1 text-xs text-white/70">Placeholder tabel.</div>

      <div class="mt-4 overflow-hidden rounded-2xl border border-white/15">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[520px] text-left text-sm">
            <thead class="bg-white/10 text-xs text-white/70">
              <tr>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Masuk</th>
                <th class="px-4 py-3">Pulang</th>
                <th class="px-4 py-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr>
                <td class="px-4 py-3 text-white/80">--</td>
                <td class="px-4 py-3 text-white/80">--</td>
                <td class="px-4 py-3 text-white/80">--</td>
                <td class="px-4 py-3 text-white/70">Belum ada data</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>
@endsection