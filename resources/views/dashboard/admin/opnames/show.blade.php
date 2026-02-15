@extends('layouts.admin')
@section('title','Detail Stock Opname')

@section('body')
  <div class="flex items-start justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>

      <div>
        <h1 class="text-xl font-semibold">Detail Stock Opname</h1>
        <p class="text-sm text-white/70">
          Tanggal: {{ \Carbon\Carbon::parse($opname->opname_date)->format('d M Y') }}
          • Status: <b>{{ strtoupper($opname->status) }}</b>
        </p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.opnames.index') }}"
         class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
        ← Kembali
      </a>

      @if($opname->status === 'draft')
        <form method="POST" action="{{ route('admin.opnames.post', $opname->id) }}">
          @csrf
          <button class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
            POST (Apply ke Stok)
          </button>
        </form>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mt-4 rounded-2xl border border-red-200/30 bg-red-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.2fr_0.8fr]">
    {{-- LEFT: items --}}
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-sm font-semibold">Items</div>
          <div class="text-xs text-white/60">Selisih = fisik - sistem</div>
        </div>
        <div class="text-xs text-white/70">
          Total item: <b>{{ $opname->items->count() }}</b>
        </div>
      </div>

      <div class="mt-4 hidden sm:block overflow-hidden rounded-2xl border border-white/15">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[950px] text-left text-sm">
            <thead class="bg-white/10 text-xs text-white/70">
              <tr>
                <th class="px-4 py-3">Bahan</th>
                <th class="px-4 py-3">Unit</th>
                <th class="px-4 py-3">Sistem</th>
                <th class="px-4 py-3">Fisik</th>
                <th class="px-4 py-3">Selisih</th>
                <th class="px-4 py-3">Note</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              @foreach($opname->items as $it)
                @php $diff = (float)$it->difference; @endphp
                <tr class="hover:bg-white/5">
                  <td class="px-4 py-3 font-medium">{{ $it->rawMaterial?->name }}</td>
                  <td class="px-4 py-3 text-white/70">{{ $it->rawMaterial?->unit }}</td>
                  <td class="px-4 py-3 text-white/80">{{ $it->system_qty }}</td>
                  <td class="px-4 py-3 text-white/80">{{ $it->physical_qty }}</td>
                  <td class="px-4 py-3 font-semibold {{ $diff > 0 ? 'text-emerald-200' : ($diff < 0 ? 'text-red-200' : 'text-white/80') }}">
                    {{ $it->difference }}
                  </td>
                  <td class="px-4 py-3 text-white/70">{{ $it->note ?? '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-4 space-y-3 sm:hidden">
        @foreach($opname->items as $it)
          @php $diff = (float)$it->difference; @endphp
          <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-sm font-semibold">{{ $it->rawMaterial?->name }}</div>
                <div class="text-xs text-white/70">Unit: {{ $it->rawMaterial?->unit }}</div>
              </div>
              <div class="text-xs text-white/70">
                Selisih:
                <span class="font-semibold {{ $diff > 0 ? 'text-emerald-200' : ($diff < 0 ? 'text-red-200' : 'text-white/80') }}">
                  {{ $it->difference }}
                </span>
              </div>
            </div>
            <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-white/70">
              <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                Sistem: <b class="text-white/90">{{ $it->system_qty }}</b>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                Fisik: <b class="text-white/90">{{ $it->physical_qty }}</b>
              </div>
            </div>
            @if($it->note)
              <div class="mt-2 text-xs text-white/70">Note: {{ $it->note }}</div>
            @endif
          </div>
        @endforeach
      </div>
    </div>

    {{-- RIGHT: meta --}}
    <div class="space-y-5">
      <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
        <div class="text-sm font-semibold">Dibuat oleh</div>
        <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 p-4">
          <div class="text-sm font-semibold">{{ $opname->creator?->name ?? '-' }}</div>
          <div class="text-xs text-white/70">{{ $opname->creator?->email }}</div>
        </div>
      </div>

      <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
        <div class="text-sm font-semibold">Posting</div>
        <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 p-4">
          <div class="text-sm font-semibold">{{ $opname->poster?->name ?? '-' }}</div>
          <div class="text-xs text-white/70">
            {{ $opname->posted_at ? \Carbon\Carbon::parse($opname->posted_at)->format('d M Y H:i') : 'Belum diposting' }}
          </div>
        </div>
      </div>

      @if($opname->note)
        <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
          <div class="text-sm font-semibold">Catatan</div>
          <p class="mt-2 text-sm text-white/70">{{ $opname->note }}</p>
        </div>
      @endif
    </div>
  </div>
@endsection
