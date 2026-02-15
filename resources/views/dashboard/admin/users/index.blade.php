@extends('layouts.admin')
@section('title','User (Kasir)')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
        ☰
      </button>

      <div>
        <h1 class="text-xl font-semibold">User (Kasir)</h1>
        <p class="text-sm text-white/70">Kelola akun kasir yang dibuat oleh admin.</p>
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
                  <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-xs">
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

    <div class="mt-4">
      {{ $cashiers->onEachSide(1)->links() }}
    </div>
  </div>
@endsection
