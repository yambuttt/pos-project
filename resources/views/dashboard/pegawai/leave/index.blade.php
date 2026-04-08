@extends('layouts.pegawai')
@section('title', 'Pengajuan Cuti / Sakit')

@section('page_label', 'Pegawai')
@section('page_title', 'Pengajuan Cuti / Sakit')

@section('content')
  @if (session('ok'))
    <div class="mb-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
      {{ session('ok') }}
    </div>
  @endif
  @if (session('error'))
    <div class="mb-4 rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-100">
      {{ session('error') }}
    </div>
  @endif

  <div class="grid grid-cols-1 gap-5 lg:grid-cols-[1.1fr_.9fr]">
    {{-- Form --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
      <div class="text-sm font-semibold">Buat Pengajuan</div>
      <div class="mt-1 text-xs text-white/70">Sakit wajib lampirkan foto surat dokter.</div>

      <form class="mt-5 space-y-4" method="POST" action="{{ route('pegawai.leave.store') }}" enctype="multipart/form-data">
        @csrf

        <div>
          <label class="text-sm text-white/80">Jenis</label>
          <select id="leaveType" name="type"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
            <option value="cuti">Cuti</option>
            <option value="sakit">Sakit</option>
          </select>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <label class="text-sm text-white/80">Mulai</label>
            <input type="date" name="start_date"
              class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40" required />
          </div>
          <div>
            <label class="text-sm text-white/80">Sampai</label>
            <input type="date" name="end_date"
              class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40" required />
          </div>
        </div>

        <div>
          <label class="text-sm text-white/80">Keterangan (opsional)</label>
          <input name="reason"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
            placeholder="Contoh: keperluan keluarga / demam tinggi" />
        </div>

        <div id="doctorWrap" class="hidden">
          <label class="text-sm text-white/80">Foto Surat Dokter (wajib untuk sakit)</label>
          <input type="file" name="doctor_note" accept="image/*"
            class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40" />
          <div class="mt-2 text-xs text-white/60">Format gambar, max 4MB.</div>
        </div>

        <button class="w-full rounded-xl bg-yellow-500 px-5 py-3 text-sm font-semibold text-black hover:bg-yellow-400">
          Kirim Pengajuan
        </button>
      </form>
    </div>

    {{-- List --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
      <div class="text-sm font-semibold">Riwayat Pengajuan</div>

      <div class="mt-4 space-y-3">
        @forelse($items as $it)
          <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-semibold text-white">
                  {{ strtoupper($it->type) }}
                  <span class="ml-2 rounded-full border border-white/10 bg-white/[0.03] px-2 py-0.5 text-xs text-white/70">
                    {{ $it->status }}
                  </span>
                </div>
                <div class="mt-1 text-xs text-white/70">
                  {{ $it->start_date?->format('d M Y') }} — {{ $it->end_date?->format('d M Y') }}
                  • {{ $it->days_count }} hari
                </div>
                <div class="mt-1 text-xs text-white/60">{{ $it->reason ?? '-' }}</div>
                @if($it->status !== 'pending')
                  <div class="mt-2 text-xs text-white/60">
                    Catatan admin: <span class="text-white/80">{{ $it->review_note ?? '-' }}</span>
                  </div>
                @endif
              </div>
            </div>
          </div>
        @empty
          <div class="text-sm text-white/60">Belum ada pengajuan.</div>
        @endforelse
      </div>

      <div class="mt-4">
        {{ $items->links() }}
      </div>
    </div>
  </div>

  <script>
    (function () {
      const t = document.getElementById('leaveType');
      const w = document.getElementById('doctorWrap');

      function sync() {
        if (!t || !w) return;
        w.classList.toggle('hidden', t.value !== 'sakit');
      }
      if (t) t.addEventListener('change', sync);
      sync();
    })();
  </script>
@endsection