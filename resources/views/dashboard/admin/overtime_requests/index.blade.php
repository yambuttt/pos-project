@extends('layouts.admin')
@section('title', 'Pengajuan Lembur')

@section('body')
<div class="mx-auto w-full max-w-6xl">
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="text-lg font-semibold text-white">Pengajuan Lembur</div>
      <div class="mt-1 text-sm text-white/60">Approve hanya bisa sebelum jam selesai shift.</div>
    </div>

    <div class="flex flex-wrap gap-2">
      @php $filters = ['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','all'=>'Semua']; @endphp
      @foreach($filters as $k=>$label)
        <a href="{{ route('admin.overtime_requests.index', ['status'=>$k]) }}"
          class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-4 py-2 text-sm hover:bg-white/[0.08]
          {{ $status === $k ? 'text-yellow-400 border-yellow-500/35' : 'text-white/80' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>
  </div>

  @if (session('ok'))
    <div class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">{{ session('ok') }}</div>
  @endif
  @if (session('error'))
    <div class="mt-4 rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-100">{{ session('error') }}</div>
  @endif

  <div class="mt-6 space-y-4">
    @forelse($items as $it)
      <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
          <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <div class="text-sm font-semibold text-white">{{ $it->user?->name }}</div>
              <span class="rounded-full border border-white/10 bg-white/[0.03] px-2.5 py-1 text-xs text-white/70">
                {{ $it->date?->format('d M Y') }}
              </span>
              <span class="rounded-full border border-white/10 bg-white/[0.03] px-2.5 py-1 text-xs
                {{ $it->status==='pending'?'text-yellow-300':($it->status==='approved'?'text-emerald-200':'text-red-200') }}">
                {{ strtoupper($it->status) }}
              </span>
            </div>

            <div class="mt-2 text-xs text-white/70">
              Minta: <span class="text-white/85">{{ $it->requested_minutes }} menit</span>
              @if($it->approved_minutes)
                • Disetujui: <span class="text-white/85">{{ $it->approved_minutes }} menit</span>
              @endif
            </div>

            <div class="mt-2 text-xs text-white/60">{{ $it->reason ?? '-' }}</div>

            @if($it->status !== 'pending')
              <div class="mt-2 text-xs text-white/60" x-data="{ editing: false }">
                <div x-show="!editing" class="flex items-center gap-2">
                  <span>Catatan admin: <span class="text-white/80">{{ $it->review_note ?? '-' }}</span></span>
                  <button @click="editing = true" class="text-yellow-500 hover:underline text-[10px]">Edit</button>
                </div>
                <form x-show="editing" method="POST" action="{{ route('admin.overtime_requests.update_note', $it) }}" 
                  class="mt-1 flex items-center gap-2" @submit.prevent="$el.submit()">
                  @csrf
                  <input name="review_note" value="{{ $it->review_note }}" placeholder="Tulis catatan..."
                    class="flex-1 rounded-lg border border-white/15 bg-white/[0.04] px-2 py-1 text-[11px] text-white outline-none focus:border-yellow-500/35" />
                  <button type="submit" class="text-emerald-400 hover:underline text-[10px]">Simpan</button>
                  <button type="button" @click="editing = false" class="text-white/40 hover:underline text-[10px]">Batal</button>
                </form>
              </div>
            @endif
          </div>

          @if($it->status === 'pending')
            <div class="w-full shrink-0 lg:w-[340px]">
              <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                <form method="POST" class="space-y-3">
                  @csrf
                  <div class="text-[10px] uppercase tracking-wider text-white/40 font-semibold mb-1">Berikan Catatan (Opsional)</div>
                  <input name="review_note" placeholder="Contoh: Pekerjaan sudah selesai, oke."
                    class="w-full rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2.5 text-xs text-white placeholder:text-white/30 outline-none focus:border-yellow-500/35" />
                  
                  <div class="grid grid-cols-2 gap-2">
                    <button type="submit" formaction="{{ route('admin.overtime_requests.approve', $it) }}"
                      class="rounded-xl bg-yellow-500 px-3 py-2.5 text-xs font-bold text-black hover:bg-yellow-400 transition-colors">
                      Approve
                    </button>
                    <button type="submit" formaction="{{ route('admin.overtime_requests.reject', $it) }}"
                      class="rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2.5 text-xs text-white/85 hover:bg-white/[0.08] transition-colors">
                      Reject
                    </button>
                  </div>
                </form>
              </div>
            </div>
          @endif
        </div>
      </div>
    @empty
      <div class="text-sm text-white/60">Tidak ada data.</div>
    @endforelse
  </div>

  <div class="mt-6">{{ $items->links() }}</div>
</div>
@endsection