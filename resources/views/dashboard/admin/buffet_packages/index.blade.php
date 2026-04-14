@extends('layouts.admin')
@section('title','Paket Buffet')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Paket Buffet</h1>
        <p class="text-sm text-white/70">Kelola paket & isi paket (menu apa saja).</p>
      </div>
    </div>

    <a href="{{ route('admin.buffet_packages.create') }}"
      class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
      + Tambah Paket
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-500/10 px-4 py-3 text-sm">✅ {{ session('success') }}</div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <form method="GET" class="flex gap-2">
      <input name="q" value="{{ $q ?? '' }}" placeholder="Cari paket..."
        class="w-full sm:w-[320px] rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm outline-none placeholder:text-white/40">
      <button class="rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold hover:bg-white/20">Filter</button>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Tipe Harga</th>
              <th class="px-4 py-3">Harga</th>
              <th class="px-4 py-3">Min Pax</th>
              <th class="px-4 py-3">Aktif</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($rows as $r)
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3 font-semibold">{{ $r->name }}</td>
                <td class="px-4 py-3">{{ $r->pricing_type }}</td>
                <td class="px-4 py-3">Rp {{ number_format($r->price,0,',','.') }}</td>
                <td class="px-4 py-3">{{ $r->min_pax ?? '-' }}</td>
                <td class="px-4 py-3">{{ $r->is_active ? 'Ya' : 'Tidak' }}</td>
                <td class="px-4 py-3 text-right">
                  <a href="{{ route('admin.buffet_packages.edit', $r) }}"
                    class="rounded-xl border border-white/15 bg-white/5 px-3 py-2 text-xs font-semibold hover:bg-white/10">
                    Edit
                  </a>
                  <form method="POST" action="{{ route('admin.buffet_packages.destroy', $r) }}" class="inline"
                    onsubmit="return confirm('Hapus paket ini?')">
                    @csrf @method('DELETE')
                    <button class="ml-2 rounded-xl border border-red-300/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100 hover:bg-red-500/15">
                      Hapus
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="px-4 py-8 text-center text-white/60">Belum ada paket.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
  </div>
@endsection