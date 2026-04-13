@extends('dashboard.admin._reservation_layout')

@section('title', 'Buat Reservasi')
@section('page_title', 'Buat Reservasi')

@section('content')
<form method="POST" action="{{ route('admin.reservations.store') }}" class="grid gap-4">
    @csrf

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-sm">Resource</label>
            <select name="reservation_resource_id" class="w-full px-3 py-2 border rounded" required>
                @foreach ($resources as $rs)
                    <option value="{{ $rs->id }}">
                        [{{ $rs->type }}] {{ $rs->name }} (kap: {{ $rs->capacity }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm">Menu Type</label>
            <select name="menu_type" class="w-full px-3 py-2 border rounded" required>
                <option value="REGULAR">REGULAR (auto lock stok saat DP)</option>
                <option value="BUFFET">BUFFET (stok manual)</option>
            </select>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-sm">Nama Customer</label>
            <input name="customer_name" class="w-full px-3 py-2 border rounded" required>
        </div>
        <div>
            <label class="text-sm">No HP (opsional)</label>
            <input name="customer_phone" class="w-full px-3 py-2 border rounded">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-sm">Start</label>
            <input type="datetime-local" name="start_at" class="w-full px-3 py-2 border rounded" required>
        </div>
        <div>
            <label class="text-sm">End</label>
            <input type="datetime-local" name="end_at" class="w-full px-3 py-2 border rounded" required>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-sm">Pax (opsional)</label>
            <input type="number" name="pax" min="1" class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="text-sm">Rental total (Rp)</label>
            <input type="number" name="rental_total" min="0" value="0" class="w-full px-3 py-2 border rounded">
        </div>
    </div>

    <div class="border rounded p-3">
        <div class="font-semibold mb-2">Items (untuk REGULAR)</div>
        <div class="text-xs text-gray-600 mb-3">
            Pilih produk + qty. Saat DP dibayar, sistem akan auto lock bahan baku pakai aturan min_stock x2.
        </div>

        <div class="grid gap-2" id="itemsWrap">
            <div class="grid sm:grid-cols-3 gap-2">
                <select name="items[0][product_id]" class="px-3 py-2 border rounded">
                    <option value="">-- pilih produk --</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ number_format($p->price) }})</option>
                    @endforeach
                </select>
                <input type="number" name="items[0][qty]" min="1" value="1" class="px-3 py-2 border rounded">
                <button type="button" onclick="addRow()" class="px-3 py-2 border rounded hover:bg-gray-50">+ tambah</button>
            </div>
        </div>
    </div>

    <div>
        <label class="text-sm">Notes (opsional)</label>
        <textarea name="notes" class="w-full px-3 py-2 border rounded" rows="3"></textarea>
    </div>

    <div class="flex gap-2">
        <button class="px-4 py-2 rounded bg-blue-600 text-white">Simpan (Pending DP)</button>
        <a href="{{ route('admin.reservations.index') }}" class="px-4 py-2 rounded border">Batal</a>
    </div>
</form>

<script>
let idx = 1;
function addRow() {
  const wrap = document.getElementById('itemsWrap');
  const row = document.createElement('div');
  row.className = 'grid sm:grid-cols-3 gap-2';
  row.innerHTML = `
    <select name="items[${idx}][product_id]" class="px-3 py-2 border rounded">
      <option value="">-- pilih produk --</option>
      @foreach ($products as $p)
        <option value="{{ $p->id }}">{{ $p->name }} ({{ number_format($p->price) }})</option>
      @endforeach
    </select>
    <input type="number" name="items[${idx}][qty]" min="1" value="1" class="px-3 py-2 border rounded">
    <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 border rounded hover:bg-gray-50">hapus</button>
  `;
  wrap.appendChild(row);
  idx++;
}
</script>
@endsection