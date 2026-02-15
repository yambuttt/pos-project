@extends('layouts.admin')
@section('title','Stock Opname')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Stock Opname</h1>
        <p class="text-sm text-white/70">Koreksi stok fisik vs stok sistem (Draft → Posted)</p>
      </div>
    </div>

    <a href="{{ route('admin.opnames.create') }}"
       class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
      + Buat Opname
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">

    {{-- Desktop table --}}
    <div class="hidden sm:block overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[950px] text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Dibuat oleh</th>
              <th class="px-4 py-3">Posted oleh</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($opnames as $o)
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($o->opname_date)->format('d M Y') }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-xs">
                    {{ strtoupper($o->status) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-white/75">
                  {{ $o->creator?->name ?? '-' }}
                  <div class="text-xs text-white/60">{{ $o->creator?->email }}</div>
                </td>
                <td class="px-4 py-3 text-white/75">
                  {{ $o->poster?->name ?? '-' }}
                  <div class="text-xs text-white/60">{{ $o->posted_at ? \Carbon\Carbon::parse($o->posted_at)->format('d M Y H:i') : '-' }}</div>
                </td>
                <td class="px-4 py-3">
                  <a href="{{ route('admin.opnames.show', $o->id) }}"
                     class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs hover:bg-white/15">
                    Detail
                  </a>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="px-4 py-6 text-center text-white/70">Belum ada opname.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden space-y-3">
      @forelse($opnames as $o)
        <a href="{{ route('admin.opnames.show', $o->id) }}"
           class="block rounded-2xl border border-white/15 bg-white/10 p-4 hover:bg-white/15">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-semibold">{{ \Carbon\Carbon::parse($o->opname_date)->format('d M Y') }}</div>
              <div class="text-xs text-white/70">
                Status:
                <span class="font-semibold text-white/90">{{ strtoupper($o->status) }}</span>
              </div>
            </div>
            <div class="text-xs text-white/70">
              {{ $o->posted_at ? \Carbon\Carbon::parse($o->posted_at)->format('d M H:i') : '' }}
            </div>
          </div>

          <div class="mt-2 text-xs text-white/70">
            By: {{ $o->creator?->name ?? '-' }}
          </div>
        </a>
      @empty
        <div class="rounded-2xl border border-white/15 bg-white/10 p-6 text-center text-sm text-white/70">
          Belum ada opname.
        </div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $opnames->onEachSide(1)->links() }}
    </div>
  </div>
@endsection
