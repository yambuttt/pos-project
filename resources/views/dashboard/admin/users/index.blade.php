@extends('layouts.admin')
@section('title', 'User (Kasir)')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
        ☰
      </button>
      <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-2xl">
        <div class="text-xs text-white/70">User (Kasir)</div>
        <div class="text-sm font-semibold">Kelola akun kasir yang dibuat oleh admin.</div>
      </div>

    </div>

    <a href="{{ route('admin.cashiers.create') }}"
      class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
      + Buat Kasir
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="text-sm font-semibold">Daftar Kasir</div>
      <div class="text-xs text-white/70">Total: {{ $cashiers->total() }}</div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        {{-- MOBILE LIST (lebih enak daripada table) --}}
        <div class="mt-4 space-y-3 sm:hidden">
          @forelse($cashiers as $c)
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <div class="text-sm font-semibold">{{ $c->name }}</div>
                  <div class="text-xs text-white/75">{{ $c->email }}</div>
                </div>

                <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-xs">
                  {{ $c->role }}
                </span>
              </div>

              <div class="mt-3 grid grid-cols-1 gap-2 text-xs text-white/75">
                <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                  <div class="text-white/60">Dibuat oleh</div>
                  <div class="mt-0.5 text-white/90">
                    {{ $c->creator?->name ?? '-' }}
                    <span class="text-white/60">({{ $c->creator?->email ?? '-' }})</span>
                  </div>
                </div>

                <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                  <div class="text-white/60">Dibuat</div>
                  <div class="mt-0.5 text-white/90">
                    {{ $c->created_at?->format('d M Y H:i') }}
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="rounded-2xl border border-white/15 bg-white/10 p-6 text-center text-sm text-white/70">
              Belum ada akun kasir.
            </div>
          @endforelse
        </div>

        {{-- TABLE (tablet/desktop) --}}
        <div class="mt-4 hidden sm:block overflow-hidden rounded-2xl border border-white/15">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
              <thead class="bg-white/10 text-xs text-white/70">
                <tr>
                  <th class="px-4 py-3">Nama</th>
                  <th class="px-4 py-3">Email</th>
                  <th class="px-4 py-3">Role</th>
                  <th class="px-4 py-3">Dibuat oleh</th>
                  <th class="px-4 py-3">Dibuat</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/10">
                @forelse($cashiers as $c)
                  <tr class="hover:bg-white/5">
                    <td class="px-4 py-3 font-medium">{{ $c->name }}</td>
                    <td class="px-4 py-3 text-white/80">{{ $c->email }}</td>
                    <td class="px-4 py-3">
                      <span
                        class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-xs">
                        {{ $c->role }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-white/80">
                      {{ $c->creator?->name ?? '-' }}
                      <div class="text-xs text-white/60">{{ $c->creator?->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-white/70 text-xs">
                      {{ $c->created_at?->format('d M Y H:i') }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-white/70">
                      Belum ada akun kasir.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

    <div class="mt-4">
      {{ $cashiers->onEachSide(1)->links() }}
    </div>
  </div>
@endsection