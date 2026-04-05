@extends('layouts.admin')
@section('title','Detail Reservasi')

@section('body')
<div class="flex items-center justify-between gap-3">
  <div class="flex items-center gap-3">
    <button id="openMobileSidebar" type="button"
      class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
    <div>
      <h1 class="text-xl font-semibold">Detail Reservasi</h1>
      <p class="text-sm text-white/70">{{ $reservation->reservation_code }}</p>
    </div>
  </div>

  <a href="{{ route('admin.reservations.index') }}" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15">Kembali</a>
</div>

@if(session('success'))
  <div class="mt-4 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-2xl">✅ {{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="mt-4 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm backdrop-blur-2xl">
    <div class="font-semibold mb-1">❌ Ada error:</div>
    <ul class="list-disc pl-5 text-white/80 space-y-1">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.1fr_.9fr]">
  <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <div class="text-sm font-semibold">Ringkasan</div>
    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 text-sm text-white/80">
      <div>
        <div class="text-xs text-white/60">Tanggal</div>
        <div class="font-semibold">{{ $reservation->reservation_date->format('d M Y') }}</div>
      </div>
      <div>
        <div class="text-xs text-white/60">Jam</div>
        <div class="font-semibold">{{ $reservation->start_time }} - {{ $reservation->end_time }} ({{ $reservation->duration_minutes }}m)</div>
      </div>
      <div>
        <div class="text-xs text-white/60">Customer</div>
        <div class="font-semibold">{{ $reservation->customer_name }}</div>
        <div class="text-xs text-white/60">{{ $reservation->customer_phone }}</div>
      </div>
      <div>
        <div class="text-xs text-white/60">Tipe</div>
        <div class="font-semibold">{{ strtoupper($reservation->reservable_type) }}</div>
        <div class="text-xs text-white/60">Resource ID: {{ $reservation->reservable_id }}</div>
      </div>
      <div>
        <div class="text-xs text-white/60">Jumlah Orang</div>
        <div class="font-semibold">{{ $reservation->party_size }}</div>
      </div>
      <div>
        <div class="text-xs text-white/60">Status</div>
        <div class="font-semibold">{{ $reservation->status }}</div>
      </div>
    </div>

    <div class="mt-5">
      <div class="text-sm font-semibold">Menu Pre-order</div>
      <div class="mt-2 overflow-hidden rounded-2xl border border-white/15">
        <table class="w-full text-left text-sm">
          <thead class="bg-white/10 text-xs text-white/70">
            <tr>
              <th class="px-4 py-3">Menu</th>
              <th class="px-4 py-3">Qty</th>
              <th class="px-4 py-3">Catatan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @forelse($reservation->items as $it)
              <tr>
                <td class="px-4 py-3 font-semibold text-white/90">{{ $it->product?->name }}</td>
                <td class="px-4 py-3 text-white/80">{{ $it->qty }}</td>
                <td class="px-4 py-3 text-white/70">{{ $it->note ?? '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="px-4 py-4 text-center text-white/70">Tidak ada pre-order.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <div class="text-sm font-semibold">Kebutuhan Bahan (Auto dari Resep/BOM)</div>
    <div class="mt-2 text-xs text-white/60">Required = total bahan yang dibutuhkan sesuai pre-order menu.</div>

    <div class="mt-3 overflow-hidden rounded-2xl border border-white/15">
      <table class="w-full text-left text-sm">
        <thead class="bg-white/10 text-xs text-white/70">
          <tr>
            <th class="px-4 py-3">Bahan</th>
            <th class="px-4 py-3">Required</th>
            <th class="px-4 py-3">Available (Saldo Reservasi)</th>
            <th class="px-4 py-3">Shortage</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
          @forelse($reservation->requirements as $req)
            @php
              $avail = (float) ($balances[$req->raw_material_id] ?? 0);
              $short = max(0, (float)$req->required_qty - $avail);
            @endphp
            <tr>
              <td class="px-4 py-3 font-semibold text-white/90">{{ $req->rawMaterial?->name }}</td>
              <td class="px-4 py-3 text-white/80">{{ number_format($req->required_qty, 2) }}</td>
              <td class="px-4 py-3 text-white/80">{{ number_format($avail, 2) }}</td>
              <td class="px-4 py-3 {{ $short>0 ? 'text-red-200' : 'text-emerald-200' }}">{{ number_format($short, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-4 py-4 text-center text-white/70">Belum ada kebutuhan bahan (pre-order kosong).</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-2">
  <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm font-semibold">Inventory Reservasi (Pergerakan Manual)</div>
        <div class="mt-1 text-xs text-white/60">Untuk allocate/return/consume/waste. (Allocate/Return mempengaruhi stok operasional)</div>
      </div>
    </div>

    <form method="POST" action="{{ route('admin.reservations.movements.store', $reservation) }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-4">
      @csrf
      <div class="sm:col-span-2">
        <label class="text-xs text-white/70">Bahan Baku</label>
        <select name="raw_material_id" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm">
          @foreach($materials as $m)
            <option value="{{ $m->id }}">{{ $m->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-xs text-white/70">Tipe</label>
        <select name="type" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm">
          <option value="allocate_from_main">Allocate dari Stok Operasional</option>
          <option value="return_to_main">Return ke Stok Operasional</option>
          <option value="consume">Consume (dipakai)</option>
          <option value="waste">Waste (terbuang)</option>
          <option value="purchase_in">Purchase_in (khusus catatan)</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-white/70">Qty</label>
        <input name="qty" type="number" step="0.01" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" placeholder="0.00" />
      </div>
      <div class="sm:col-span-4">
        <label class="text-xs text-white/70">Catatan</label>
        <input name="note" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" placeholder="optional" />
      </div>
      <div class="sm:col-span-4">
        <button class="rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Simpan Movement</button>
      </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/15">
      <table class="w-full text-left text-sm">
        <thead class="bg-white/10 text-xs text-white/70">
          <tr>
            <th class="px-4 py-3">Waktu</th>
            <th class="px-4 py-3">Bahan</th>
            <th class="px-4 py-3">Tipe</th>
            <th class="px-4 py-3">Qty</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
          @forelse($reservation->inventoryMovements->sortByDesc('id') as $mv)
            <tr>
              <td class="px-4 py-3 text-white/70">{{ $mv->created_at->format('d M H:i') }}</td>
              <td class="px-4 py-3 text-white/85">{{ $mv->rawMaterial?->name }}</td>
              <td class="px-4 py-3 text-white/70">{{ $mv->type }}</td>
              <td class="px-4 py-3 text-white/85">{{ number_format($mv->qty,2) }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-4 py-4 text-center text-white/70">Belum ada movement.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
    <div class="text-sm font-semibold">Input Pembelian untuk Reservasi (Masuk Purchases + Label Reservasi)</div>
    <div class="mt-1 text-xs text-white/60">Purchase akan tercatat di menu Purchases, dan bahan otomatis masuk ke inventory reservasi (per reservasi).</div>

    <form method="POST" action="{{ route('admin.reservations.purchases.store', $reservation) }}" class="mt-4 grid grid-cols-1 gap-3">
      @csrf
      <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
        <div>
          <label class="text-xs text-white/70">Source Type</label>
          <select name="source_type" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm">
            <option value="supplier">Supplier</option>
            <option value="external">External</option>
          </select>
        </div>
        <div>
          <label class="text-xs text-white/70">Supplier ID (optional)</label>
          <input name="supplier_id" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" placeholder="isi jika source=supplier" />
        </div>
        <div class="md:col-span-2">
          <label class="text-xs text-white/70">Source Name (optional)</label>
          <input name="source_name" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" placeholder="isi jika source=external" />
        </div>
        <div>
          <label class="text-xs text-white/70">Invoice No</label>
          <input name="invoice_no" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="text-xs text-white/70">Purchase Date</label>
          <input type="date" name="purchase_date" value="{{ now()->toDateString() }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
        </div>
      </div>

      <div>
        <label class="text-xs text-white/70">Catatan</label>
        <input name="note" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" placeholder="optional" />
      </div>

      <div class="rounded-2xl border border-white/15 bg-white/5 p-4">
        <div class="text-sm font-semibold">Item Pembelian</div>
        <div class="mt-1 text-xs text-white/60">Isi bahan baku yang dibeli untuk reservasi ini.</div>
        <div id="purchaseItems" class="mt-3 space-y-2"></div>
        <button type="button" id="addPurchaseItem" class="mt-3 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15">+ Tambah Item</button>
      </div>

      <button class="rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Buat Purchase Reservasi</button>
    </form>
  </div>
</div>

<script>
  const materials = @json($materials->map(fn($m)=>['id'=>$m->id,'name'=>$m->name]));
  const wrap = document.getElementById('purchaseItems');
  const addBtn = document.getElementById('addPurchaseItem');

  function rowTemplate(i){
    const opts = materials.map(m=>`<option value="${m.id}">${m.name}</option>`).join('');
    return `
      <div class="grid grid-cols-1 gap-2 rounded-xl border border-white/15 bg-white/5 p-3 md:grid-cols-[1.2fr_.6fr_.7fr_auto]">
        <div>
          <label class="text-xs text-white/70">Bahan</label>
          <select name="items[${i}][raw_material_id]" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm">${opts}</select>
        </div>
        <div>
          <label class="text-xs text-white/70">Qty</label>
          <input type="number" step="0.01" min="0.01" value="1" name="items[${i}][qty]" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="text-xs text-white/70">Unit Cost</label>
          <input type="number" step="0.01" min="0" value="0" name="items[${i}][unit_cost]" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
        </div>
        <div class="flex items-end">
          <button type="button" class="removeBtn rounded-xl border border-red-500/30 bg-red-500/10 px-3 py-2 text-xs font-semibold hover:bg-red-500/15">Hapus</button>
        </div>
      </div>
    `;
  }

  let idx = 0;
  addBtn.addEventListener('click', () => {
    const div = document.createElement('div');
    div.innerHTML = rowTemplate(idx++);
    const node = div.firstElementChild;
    wrap.appendChild(node);
    node.querySelector('.removeBtn').addEventListener('click', ()=> node.remove());
  });

  // add initial row
  addBtn.click();
</script>
@endsection
