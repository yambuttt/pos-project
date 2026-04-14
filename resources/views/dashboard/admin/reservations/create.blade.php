@extends('layouts.admin')
@section('title', 'Buat Reservasi')

@section('body')
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button id="openMobileSidebar" type="button"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">☰</button>
            <div>
                <h1 class="text-xl font-semibold">Buat Reservasi</h1>
                <p class="text-sm text-white/70">Input manual oleh admin (pending DP).</p>
            </div>
        </div>

        <a href="{{ route('admin.reservations.index') }}"
            class="rounded-xl border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold hover:bg-white/10">
            ← Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="mt-4 rounded-2xl border border-red-300/20 bg-red-500/10 px-4 py-3 text-sm whitespace-pre-line">
            ❌ {{ $errors->first() }}
        </div>
    @endif

    <div class="mt-5 rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
        <form method="POST" action="{{ route('admin.reservations.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div>
                    <div class="text-sm text-white/70">Resource</div>
                    <select name="reservation_resource_id"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40"
                        required>
                        @foreach($resources as $rs)
                            <option value="{{ $rs->id }}">
                                [{{ $rs->type }}] {{ $rs->name }} (kap {{ $rs->capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="text-sm text-white/70">Menu Type</div>
                    <select id="menuType" name="menu_type"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40"
                        required>
                        <option value="REGULAR">REGULAR (auto lock stok saat DP)</option>
                        <option value="BUFFET">BUFFET (paket + inventory)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div>
                    <div class="text-sm text-white/70">Nama Customer</div>
                    <input name="customer_name" value="{{ old('customer_name') }}"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
                        required>
                </div>
                <div>
                    <div class="text-sm text-white/70">No HP (opsional)</div>
                    <input name="customer_phone" value="{{ old('customer_phone') }}"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div>
                    <div class="text-sm text-white/70">Start</div>
                    <input type="datetime-local" name="start_at" value="{{ old('start_at') }}"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40"
                        required>
                </div>
                <div>
                    <div class="text-sm text-white/70">End</div>
                    <input type="datetime-local" name="end_at" value="{{ old('end_at') }}"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40"
                        required>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div>
                    <div class="text-sm text-white/70">Pax (wajib untuk paket per_pax)</div>
                    <input type="number" name="pax" min="1" value="{{ old('pax') }}"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
                </div>
                <div>
                    <div class="text-sm text-white/70">Rental total (Rp)</div>
                    <input type="number" name="rental_total" min="0" value="{{ old('rental_total', 0) }}"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
                </div>
            </div>

            {{-- BUFFET package --}}
            <div id="buffetPackageWrap" class="hidden rounded-2xl border border-white/15 bg-white/5 p-4">
                <div class="font-semibold">Paket Buffet</div>
                <div class="mt-2 text-xs text-white/60">Pilih paket. Setelah reservasi dibuat, kelola bahan di menu Buffet
                    Inventory.</div>

                <div class="mt-4">
                    <div class="text-sm text-white/70">Pilih Paket</div>
                    <select name="buffet_package_id"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
                        <option value="">-- pilih paket --</option>
                        @foreach($buffetPackages as $bp)
                            <option value="{{ $bp->id }}">
                                {{ $bp->name }} —
                                {{ $bp->pricing_type === 'per_pax' ? 'per pax' : 'per event' }}
                                (Rp {{ number_format($bp->price, 0, ',', '.') }})
                                {{ $bp->min_pax ? ' | min pax ' . $bp->min_pax : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- REGULAR items --}}
            <div id="regularWrap" class="rounded-2xl border border-white/15 bg-white/5 p-4">
                <div class="font-semibold">Items (REGULAR)</div>
                <div class="mt-1 text-xs text-white/60">
                    Saat DP dibayar, sistem lock bahan baku pakai rule min_stock x2.
                </div>

                <div id="itemsWrap" class="mt-4 space-y-2">
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                        <select name="items[0][product_id]"
                            class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
                            <option value="">-- pilih produk --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>

                        <input type="number" name="items[0][qty]" min="1" value="1"
                            class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">

                        <button type="button" onclick="addRow()"
                            class="rounded-xl border border-white/15 bg-white/5 px-4 py-3 text-sm font-semibold hover:bg-white/10">
                            + tambah
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <div class="text-sm text-white/70">Notes (opsional)</div>
                <textarea name="notes" rows="3"
                    class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40">{{ old('notes') }}</textarea>
            </div>

            <button class="w-full rounded-2xl bg-blue-600/85 px-5 py-3 text-sm font-semibold hover:bg-blue-500/85">
                Simpan (Pending DP)
            </button>
        </form>
    </div>

    <script>
        let idx = 1;
        function addRow() {
            const wrap = document.getElementById('itemsWrap');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 gap-2 md:grid-cols-3';
            row.innerHTML = `
            <select name="items[${idx}][product_id]"
              class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
              <option value="">-- pilih produk --</option>
              @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
              @endforeach
            </select>
            <input type="number" name="items[${idx}][qty]" min="1" value="1"
              class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none focus:border-white/40">
            <button type="button" onclick="this.parentElement.remove()"
              class="rounded-xl border border-white/15 bg-white/5 px-4 py-3 text-sm font-semibold hover:bg-white/10">
              hapus
            </button>
          `;
            wrap.appendChild(row);
            idx++;
        }

        function syncBuffetUI() {
            const mt = document.getElementById('menuType').value;
            document.getElementById('buffetPackageWrap').classList.toggle('hidden', mt !== 'BUFFET');
            document.getElementById('regularWrap').classList.toggle('hidden', mt !== 'REGULAR');
        }
        document.getElementById('menuType').addEventListener('change', syncBuffetUI);
        syncBuffetUI();
    </script>
@endsection