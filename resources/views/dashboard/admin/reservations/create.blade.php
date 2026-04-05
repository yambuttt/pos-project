@extends('layouts.admin')
@section('title','Buat Reservasi')

@section('body')
<div class="flex items-center justify-between gap-3">
  <div class="flex items-center gap-3">
    <button id="openMobileSidebar" type="button"
      class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
    <div>
      <h1 class="text-xl font-semibold">Buat Reservasi</h1>
      <p class="text-sm text-white/70">Meja / Ruangan + Pre-order menu.</p>
    </div>
  </div>

  <a href="{{ route('admin.reservations.index') }}" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15">Kembali</a>
</div>

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

<form method="POST" action="{{ route('admin.reservations.store') }}" class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
  @csrf

  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
      <label class="text-xs text-white/70">Tipe</label>
      <select id="reservableType" name="reservable_type" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm">
        <option value="table" {{ old('reservable_type')==='table' ? 'selected' : '' }}>Meja</option>
        <option value="room" {{ old('reservable_type')==='room' ? 'selected' : '' }}>Ruangan</option>
      </select>
    </div>

    <div>
      <label class="text-xs text-white/70">Resource</label>
      <select id="reservableId" name="reservable_id" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm">
        @foreach($tables as $t)
          <option data-type="table" value="{{ $t->id }}" {{ old('reservable_id')==$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
        @endforeach
        @foreach($rooms as $r)
          <option data-type="room" value="{{ $r->id }}" {{ old('reservable_id')==$r->id ? 'selected' : '' }}>{{ $r->name }}</option>
        @endforeach
      </select>
      <div class="mt-1 text-xs text-white/60">Pilihan akan otomatis filter sesuai tipe.</div>
    </div>

    <div>
      <label class="text-xs text-white/70">Nama Customer</label>
      <input name="customer_name" value="{{ old('customer_name') }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>

    <div>
      <label class="text-xs text-white/70">No. HP/WA</label>
      <input name="customer_phone" value="{{ old('customer_phone') }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>

    <div>
      <label class="text-xs text-white/70">Jumlah Orang</label>
      <input type="number" name="party_size" value="{{ old('party_size',2) }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>

    <div>
      <label class="text-xs text-white/70">Tanggal Reservasi</label>
      <input type="date" name="reservation_date" value="{{ old('reservation_date', now()->toDateString()) }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>

    <div>
      <label class="text-xs text-white/70">Jam Mulai</label>
      <input type="time" name="start_time" value="{{ old('start_time','18:00') }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
    </div>

    <div>
      <label class="text-xs text-white/70">Durasi (menit)</label>
      <input type="number" name="duration_minutes" value="{{ old('duration_minutes',90) }}" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
      <div class="mt-1 text-xs text-white/60">Bisa disesuaikan per reservasi.</div>
    </div>
  </div>

  <div class="mt-4">
    <label class="text-xs text-white/70">Catatan</label>
    <textarea name="note" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" rows="3">{{ old('note') }}</textarea>
  </div>

  <div class="mt-6 rounded-2xl border border-white/15 bg-white/5 p-4">
    <div class="text-sm font-semibold">Pre-order Menu (optional)</div>
    <div class="mt-1 text-xs text-white/60">Tidak dibatasi stok. Sistem akan hitung kebutuhan bahan dari resep/BOM.</div>

    <div id="itemsWrap" class="mt-3 space-y-2"></div>

    <button type="button" id="addItemBtn" class="mt-3 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15">
      + Tambah Menu
    </button>
  </div>

  <button class="mt-5 rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">Simpan Reservasi</button>
</form>

<script>
  // filter resource by type
  const typeSel = document.getElementById('reservableType');
  const idSel = document.getElementById('reservableId');

  function applyType(){
    const t = typeSel.value;
    [...idSel.options].forEach(opt => {
      const ok = opt.dataset.type === t;
      opt.hidden = !ok;
      opt.disabled = !ok;
    });
    // ensure selected is valid
    const first = [...idSel.options].find(o => !o.hidden);
    if(first) idSel.value = first.value;
  }
  typeSel.addEventListener('change', applyType);
  applyType();

  // dynamic items
  const products = @json($products->map(fn($p)=>['id'=>$p->id,'name'=>$p->name]));
  const wrap = document.getElementById('itemsWrap');
  const addBtn = document.getElementById('addItemBtn');

  function rowTemplate(idx){
    const opts = products.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
    return `
      <div class="grid grid-cols-1 gap-2 rounded-xl border border-white/15 bg-white/5 p-3 md:grid-cols-[1.3fr_.6fr_1fr_auto]">
        <div>
          <label class="text-xs text-white/70">Menu</label>
          <select name="items[${idx}][product_id]" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm">${opts}</select>
        </div>
        <div>
          <label class="text-xs text-white/70">Qty</label>
          <input type="number" min="1" value="1" name="items[${idx}][qty]" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="text-xs text-white/70">Catatan</label>
          <input name="items[${idx}][note]" class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm" placeholder="contoh: no pedas" />
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
</script>
@endsection
