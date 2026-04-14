@extends('layouts.admin')
@section('title','Buffet Inventory')

@section('body')
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button id="openMobileSidebar" type="button"
        class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
      <div>
        <h1 class="text-xl font-semibold">Buffet Inventory</h1>
        <p class="text-sm text-white/70">{{ $reservation->code }} • {{ $reservation->customer_name }}</p>
      </div>
    </div>

    <a href="{{ route('admin.reservations.show', $reservation) }}"
      class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold hover:bg-white/10">
      ← Kembali
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-500/10 px-4 py-3 text-sm">✅ {{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-red-300/20 bg-red-500/10 px-4 py-3 text-sm whitespace-pre-line">❌ {{ $errors->first() }}</div>
  @endif

  <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1fr_.95fr]">
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
      <div class="font-semibold">Stok Buffet (Per Reservasi)</div>
      <div class="mt-3 overflow-hidden rounded-2xl border border-white/15">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[750px] text-left text-sm">
            <thead class="bg-white/10 text-xs text-white/70">
              <tr>
                <th class="px-4 py-3">Bahan</th>
                <th class="px-4 py-3">Unit</th>
                <th class="px-4 py-3">Qty Buffet</th>
                <th class="px-4 py-3">Stok Main</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              @forelse($reservation->buffetStocks as $s)
                <tr class="hover:bg-white/5">
                  <td class="px-4 py-3 font-semibold">{{ $s->rawMaterial?->name }}</td>
                  <td class="px-4 py-3">{{ $s->rawMaterial?->unit }}</td>
                  <td class="px-4 py-3">{{ $s->qty_on_hand }}</td>
                  <td class="px-4 py-3">{{ $s->rawMaterial?->stock_on_hand }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-white/60">Belum ada stok buffet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-6 font-semibold">Movements</div>
      <div class="mt-3 overflow-hidden rounded-2xl border border-white/15">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="bg-white/10 text-xs text-white/70">
              <tr>
                <th class="px-4 py-3">Waktu</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Bahan</th>
                <th class="px-4 py-3">IN</th>
                <th class="px-4 py-3">OUT</th>
                <th class="px-4 py-3">Note</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              @forelse($reservation->buffetMovements->sortByDesc('id') as $m)
                <tr class="hover:bg-white/5">
                  <td class="px-4 py-3 text-white/70">{{ $m->created_at->format('d M Y H:i') }}</td>
                  <td class="px-4 py-3 font-semibold">{{ $m->type }}</td>
                  <td class="px-4 py-3">{{ $m->rawMaterial?->name }}</td>
                  <td class="px-4 py-3">{{ $m->qty_in }}</td>
                  <td class="px-4 py-3">{{ $m->qty_out }}</td>
                  <td class="px-4 py-3 text-white/70">{{ $m->note }}</td>
                </tr>
              @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-white/60">Belum ada movement.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6 space-y-5">
      <div class="font-semibold">Aksi Buffet Inventory</div>

      {{-- Purchase --}}
      <form method="POST" action="{{ route('admin.reservations.buffet_inventory.purchase', $reservation) }}"
        class="rounded-2xl border border-white/15 bg-white/5 p-4 space-y-3">
        @csrf
        <div class="font-semibold text-sm">Belanja (Masuk Buffet)</div>
        <select name="raw_material_id" class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
          @foreach($rawMaterials as $rm)
            <option value="{{ $rm->id }}">{{ $rm->name }} (main: {{ $rm->stock_on_hand }})</option>
          @endforeach
        </select>
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
          <input name="qty" type="number" step="0.01" min="0.01" placeholder="Qty"
            class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none" required>
          <input name="unit_cost" type="number" step="0.01" min="0" placeholder="Unit cost (opsional)"
            class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
        </div>
        <input name="note" placeholder="Note (opsional)"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
        <button class="w-full rounded-xl bg-emerald-500/90 px-5 py-3 text-sm font-semibold text-black hover:bg-emerald-400/90">
          Simpan Belanja
        </button>
      </form>

      {{-- Transfer from main --}}
      <form method="POST" action="{{ route('admin.reservations.buffet_inventory.transfer_from_main', $reservation) }}"
        class="rounded-2xl border border-white/15 bg-white/5 p-4 space-y-3">
        @csrf
        <div class="font-semibold text-sm">Transfer dari MAIN → Buffet</div>
        <select name="raw_material_id" class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
          @foreach($rawMaterials as $rm)
            <option value="{{ $rm->id }}">{{ $rm->name }} (main: {{ $rm->stock_on_hand }})</option>
          @endforeach
        </select>
        <input name="qty" type="number" step="0.01" min="0.01" placeholder="Qty"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none" required>
        <input name="note" placeholder="Note (opsional)"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
        <button class="w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
          Transfer
        </button>
      </form>

      {{-- Return to main --}}
      <form method="POST" action="{{ route('admin.reservations.buffet_inventory.return_to_main', $reservation) }}"
        class="rounded-2xl border border-white/15 bg-white/5 p-4 space-y-3">
        @csrf
        <div class="font-semibold text-sm">Return Buffet → MAIN</div>
        <select name="raw_material_id" class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
          @foreach($rawMaterials as $rm)
            <option value="{{ $rm->id }}">{{ $rm->name }}</option>
          @endforeach
        </select>
        <input name="qty" type="number" step="0.01" min="0.01" placeholder="Qty"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none" required>
        <input name="note" placeholder="Note (opsional)"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
        <button class="w-full rounded-xl bg-white/15 px-5 py-3 text-sm font-semibold hover:bg-white/20">
          Return
        </button>
      </form>

      {{-- Consume --}}
      <form method="POST" action="{{ route('admin.reservations.buffet_inventory.consume', $reservation) }}"
        class="rounded-2xl border border-white/15 bg-white/5 p-4 space-y-3">
        @csrf
        <div class="font-semibold text-sm">Consume (Pemakaian Buffet)</div>
        <select name="raw_material_id" class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
          @foreach($rawMaterials as $rm)
            <option value="{{ $rm->id }}">{{ $rm->name }}</option>
          @endforeach
        </select>
        <input name="qty" type="number" step="0.01" min="0.01" placeholder="Qty"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none" required>
        <input name="note" placeholder="Note (opsional)"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
        <button class="w-full rounded-xl bg-yellow-500 text-black px-5 py-3 text-sm font-semibold hover:bg-yellow-400">
          Consume
        </button>
      </form>

      {{-- Waste --}}
      <form method="POST" action="{{ route('admin.reservations.buffet_inventory.waste', $reservation) }}"
        class="rounded-2xl border border-white/15 bg-white/5 p-4 space-y-3">
        @csrf
        <div class="font-semibold text-sm">Waste (Buang)</div>
        <select name="raw_material_id" class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
          @foreach($rawMaterials as $rm)
            <option value="{{ $rm->id }}">{{ $rm->name }}</option>
          @endforeach
        </select>
        <input name="qty" type="number" step="0.01" min="0.01" placeholder="Qty"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none" required>
        <input name="note" placeholder="Note (opsional)"
          class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none">
        <button class="w-full rounded-xl border border-red-300/20 bg-red-500/10 px-5 py-3 text-sm font-semibold text-red-100 hover:bg-red-500/15">
          Waste
        </button>
      </form>

    </div>
  </div>
@endsection