<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#050505] text-white">
    <div class="max-w-4xl mx-auto px-5 py-10">
        <div class="flex items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-bold">Reservasi</h1>
                <p class="text-white/60 text-sm">Pilih meja/ruangan, tanggal & jam, lalu tentukan menu.</p>
            </div>
            <a href="/" class="px-4 py-2 rounded-xl border border-white/15 hover:bg-white/5">← Kembali</a>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-xl border border-red-500/30 bg-red-500/10 text-red-200">
                <div class="font-semibold mb-1">Terjadi error:</div>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('public.reservations.store') }}"
            class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 space-y-5">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-white/70">Pilih Resource</label>
                    <select name="reservation_resource_id"
                        class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10" required>
                        @foreach($resources as $rs)
                            <option value="{{ $rs->id }}">[{{ $rs->type }}] {{ $rs->name }} (kap {{ $rs->capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-white/70">Jenis Menu</label>
                    <select id="menuType" name="menu_type"
                        class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10" required>
                        <option value="REGULAR">REGULAR (menu biasa)</option>
                        <option value="BUFFET">BUFFET (paket)</option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-white/70">Nama</label>
                    <input name="customer_name" value="{{ old('customer_name') }}"
                        class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10" required>
                </div>
                <div>
                    <label class="text-sm text-white/70">No HP (opsional)</label>
                    <input name="customer_phone" value="{{ old('customer_phone') }}"
                        class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm text-white/70">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                            class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10" required>
                    </div>
                    <div>
                        <label class="text-sm text-white/70">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}"
                            class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm text-white/70">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                            class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10" required>
                    </div>
                    <div>
                        <label class="text-sm text-white/70">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}"
                            class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10" required>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-white/70">Pax (wajib untuk Buffet per pax)</label>
                    <input type="number" name="pax" min="1" value="{{ old('pax') }}"
                        class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10">
                </div>
                <div class="text-sm text-white/50 flex items-end">
                    Rental dihitung otomatis dari harga resource (flat / per jam).
                </div>
            </div>

            {{-- REGULAR items --}}
            <div id="regularWrap" class="space-y-3">
                <div class="font-semibold">Pilih Menu (REGULAR)</div>
                <div id="itemsWrap" class="space-y-2">
                    <div class="grid md:grid-cols-3 gap-2">
                        <select name="items[0][product_id]"
                            class="px-3 py-2 rounded-xl bg-black/30 border border-white/10">
                            <option value="">-- pilih produk --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price) }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="items[0][qty]" min="1" value="1"
                            class="px-3 py-2 rounded-xl bg-black/30 border border-white/10">
                        <button type="button" onclick="addRow()"
                            class="px-3 py-2 rounded-xl border border-white/15 hover:bg-white/5">+ tambah</button>
                    </div>
                </div>
                <div class="text-xs text-white/50">
                    Catatan: stok REGULAR akan di-lock setelah DP dibayar (lebih aman untuk stok).
                </div>
            </div>

            {{-- BUFFET package --}}
            <div id="buffetWrap" class="hidden space-y-2">
                <div class="font-semibold">Pilih Paket Buffet</div>
                <div>
                    <label class="text-sm text-white/70">Paket</label>
                    <select name="buffet_package_id"
                        class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10">
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
                <div class="text-xs text-white/50">
                    Bahan buffet dikelola oleh admin di menu "Buffet Inventory" (belanja / transfer / return).
                </div>
            </div>

            <div>
                <label class="text-sm text-white/70">Catatan (opsional)</label>
                <textarea name="notes" rows="3"
                    class="w-full mt-1 px-3 py-2 rounded-xl bg-black/30 border border-white/10">{{ old('notes') }}</textarea>
            </div>

            <button class="w-full py-3 rounded-2xl bg-yellow-500 text-black font-semibold hover:bg-yellow-400">
                Buat Reservasi (lanjut bayar DP)
            </button>
        </form>
    </div>

    <script>
        let idx = 1;

        function addRow() {
            const wrap = document.getElementById('itemsWrap');
            const row = document.createElement('div');
            row.className = 'grid md:grid-cols-3 gap-2';
            row.innerHTML = `
    <select name="items[${idx}][product_id]" class="px-3 py-2 rounded-xl bg-black/30 border border-white/10">
      <option value="">-- pilih produk --</option>
      @foreach($products as $p)
        <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price) }})</option>
      @endforeach
    </select>
    <input type="number" name="items[${idx}][qty]" min="1" value="1"
           class="px-3 py-2 rounded-xl bg-black/30 border border-white/10">
    <button type="button" onclick="this.parentElement.remove()"
            class="px-3 py-2 rounded-xl border border-white/15 hover:bg-white/5">hapus</button>
  `;
            wrap.appendChild(row);
            idx++;
        }

        function syncMenuType() {
            const mt = document.getElementById('menuType').value;

            const regularWrap = document.getElementById('regularWrap');
            const buffetWrap = document.getElementById('buffetWrap');

            // show/hide
            regularWrap.classList.toggle('hidden', mt !== 'REGULAR');
            buffetWrap.classList.toggle('hidden', mt !== 'BUFFET');

            // IMPORTANT: disable inputs supaya tidak ikut terkirim
            regularWrap.querySelectorAll('select, input, textarea, button').forEach(el => {
                // tombol tambah/hapus juga ikut disable biar konsisten
                if (el.type === 'button') el.disabled = (mt !== 'REGULAR');
                else el.disabled = (mt !== 'REGULAR');
            });

            buffetWrap.querySelectorAll('select, input, textarea').forEach(el => {
                el.disabled = (mt !== 'BUFFET');
            });
        }

        document.getElementById('menuType').addEventListener('change', syncMenuType);
        syncMenuType();
    </script>
</body>

</html>