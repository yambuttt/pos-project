@extends('layouts.admin')
@section('title','Purchases')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Purchases</h1>
        <p class="text-sm text-white/70">Barang masuk: supplier / pasar / random</p>
      </div>
    </div>

    <a href="{{ route('admin.purchases.create') }}"
       class="rounded-xl bg-blue-600/85 px-4 py-2 text-sm font-semibold hover:bg-blue-500/85">
      + Buat Purchase
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <div class="hidden sm:block overflow-hidden rounded-2xl border border-white/15">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Sumber</th>
              <th class="px-4 py-3">Invoice</th>
              <th class="px-4 py-3">Total</th>
              <th class="px-4 py-3">Dibuat oleh</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($purchases as $p)
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3 text-white/85">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}</td>
                <td class="px-4 py-3">
                  @if($p->source_type === 'supplier')
                    <div class="font-medium">{{ $p->supplier?->name }}</div>
                    <div class="text-xs text-white/60">Supplier</div>
                  @else
                    <div class="font-medium">{{ $p->source_name }}</div>
                    <div class="text-xs text-white/60">External</div>
                  @endif
                </td>
                <td class="px-4 py-3 text-white/75">{{ $p->invoice_no ?? '-' }}</td>
                <td class="px-4 py-3 font-semibold">Rp {{ number_format($p->total_amount,0,',','.') }}</td>
                <td class="px-4 py-3 text-white/75">
                  {{ $p->creator?->name ?? '-' }}
                  <div class="text-xs text-white/60">{{ $p->creator?->email }}</div>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="px-4 py-6 text-center text-white/70">Belum ada purchase.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Mobile card list --}}
    <div class="sm:hidden space-y-3">
      @forelse($purchases as $p)
        <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-semibold">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}</div>
              <div class="text-xs text-white/70">
                {{ $p->source_type === 'supplier' ? ($p->supplier?->name ?? '-') : ($p->source_name ?? '-') }}
                • <span class="uppercase">{{ $p->source_type }}</span>
              </div>
            </div>
            <div class="text-sm font-semibold">Rp {{ number_format($p->total_amount,0,',','.') }}</div>
          </div>

          <div class="mt-2 text-xs text-white/70">
            Invoice: {{ $p->invoice_no ?? '-' }} <br/>
            By: {{ $p->creator?->name ?? '-' }}
          </div>
        </div>
      @empty
        <div class="rounded-2xl border border-white/15 bg-white/10 p-6 text-center text-sm text-white/70">
          Belum ada purchase.
        </div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $purchases->onEachSide(1)->links() }}
    </div>
  </div>
@endsection
