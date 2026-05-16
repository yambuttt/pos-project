@extends('layouts.admin')
@section('title','Buffet Inventory')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div class="flex items-center gap-4">
      <button id="openMobileSidebar" type="button"
        class="inline-flex lg:hidden items-center justify-center w-10 h-10 rounded-xl border border-white/10 bg-white/5 text-white/70 hover:bg-white/10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
      <div>
        <h1 class="text-3xl font-bold text-gold-gradient">Buffet Inventory</h1>
        <p class="text-sm text-white/40 font-medium italic">{{ $reservation->customer_name }} <span class="text-white font-bold not-italic">#{{ $reservation->code }}</span></p>
      </div>
    </div>

    <a href="{{ route('admin.reservations.show', $reservation) }}"
      class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-6 py-3.5 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Detail
    </a>
  </div>

  @if(session('success'))
    <div class="mb-6 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
      </svg>
      <p class="text-sm font-bold text-green-100">{{ session('success') }}</p>
    </div>
  @endif

  @if($errors->any())
    <div class="mb-6 animate-fade-in rounded-2xl border border-red-500/20 bg-red-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      <p class="text-sm font-bold text-red-100">{{ $errors->first() }}</p>
    </div>
  @endif

  <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_420px]">
    {{-- LEFT COLUMN --}}
    <div class="space-y-8">
      <!-- STOK BUFFET TABLE -->
      <div class="glass-panel p-8 rounded-[2.5rem]">
        <div class="flex items-center gap-3 mb-8">
           <div class="w-10 h-10 rounded-xl bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
           </div>
           <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Stok Buffet Saat Ini</h3>
        </div>

        <div class="overflow-hidden rounded-3xl border border-white/5 bg-black/20">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-white/[0.03] text-[9px] uppercase tracking-widest text-white/30 font-black border-b border-white/5">
                <tr>
                  <th class="px-8 py-5">Bahan Baku</th>
                  <th class="px-6 py-5 text-center">Satuan</th>
                  <th class="px-6 py-5 text-center font-bold text-white/60">Qty On-Hand</th>
                  <th class="px-8 py-5 text-right">Main Stock</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @forelse($reservation->buffetStocks as $s)
                  <tr class="group hover:bg-white/[0.02] transition-colors">
                    <td class="px-8 py-6">
                      <div class="text-xs font-bold text-white group-hover:text-gold-primary transition-colors">{{ $s->rawMaterial?->name }}</div>
                    </td>
                    <td class="px-6 py-6 text-center">
                       <span class="text-[10px] text-white/30 font-black uppercase">{{ $s->rawMaterial?->unit }}</span>
                    </td>
                    <td class="px-6 py-6 text-center text-xs font-black text-white italic">{{ (float)$s->qty_on_hand }}</td>
                    <td class="px-8 py-6 text-right text-xs font-medium text-white/40 italic">{{ (float)$s->rawMaterial?->stock_on_hand }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="px-8 py-16 text-center text-white/20 italic font-medium">Stok buffet masih kosong untuk reservasi ini.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- KEBUTUHAN BAHAN -->
      @if(!empty($needRows))
        <div class="glass-panel p-8 rounded-[2.5rem]">
          <div class="flex items-center gap-3 mb-2">
             <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
             </div>
             <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Kebutuhan Bahan (Estimasi)</h3>
          </div>
          <p class="text-[10px] text-white/30 font-medium italic mb-8 px-1">Dihitung berdasarkan paket menu dan recipe produk.</p>

          <div class="overflow-hidden rounded-3xl border border-white/5 bg-black/20">
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead class="bg-white/[0.03] text-[9px] uppercase tracking-widest text-white/30 font-black border-b border-white/5">
                  <tr>
                    <th class="px-8 py-5">Bahan</th>
                    <th class="px-6 py-5 text-center">Need</th>
                    <th class="px-6 py-5 text-center">Used</th>
                    <th class="px-6 py-5 text-center">In Buffet</th>
                    <th class="px-8 py-5 text-right font-bold text-white/60">Gap / Remaining</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                  @foreach($needRows as $n)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                      <td class="px-8 py-6">
                        <div class="text-xs font-bold text-white group-hover:text-blue-400 transition-colors">{{ $n['name'] }}</div>
                        <div class="text-[9px] text-white/20 uppercase tracking-tighter">{{ $n['unit'] }}</div>
                      </td>
                      <td class="px-6 py-6 text-center text-[11px] font-medium text-white/40 italic">{{ rtrim(rtrim(number_format($n['need'],2,'.',''), '0'), '.') }}</td>
                      <td class="px-6 py-6 text-center text-[11px] font-medium text-white/40 italic">{{ rtrim(rtrim(number_format($n['consumed'] ?? 0,2,'.',''), '0'), '.') }}</td>
                      <td class="px-6 py-6 text-center text-[11px] font-medium text-white/40 italic">{{ rtrim(rtrim(number_format($n['in_buffet'],2,'.',''), '0'), '.') }}</td>
                      <td class="px-8 py-6 text-right">
                        @if(($n['remaining'] ?? 0) > 0)
                          <span class="text-xs font-black text-yellow-500 italic">
                            -{{ rtrim(rtrim(number_format($n['remaining'],2,'.',''), '0'), '.') }}
                          </span>
                        @else
                          <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-black text-emerald-500 uppercase tracking-widest">OK</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @endif

      <!-- MOVEMENTS TABLE -->
      <div class="glass-panel p-8 rounded-[2.5rem]">
        <div class="flex items-center gap-3 mb-8">
           <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
           </div>
           <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Log Pergerakan Stok</h3>
        </div>

        <div class="overflow-hidden rounded-3xl border border-white/5 bg-black/20">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-white/[0.03] text-[9px] uppercase tracking-widest text-white/30 font-black border-b border-white/5">
                <tr>
                  <th class="px-8 py-5">Waktu</th>
                  <th class="px-6 py-5">Tipe Aktivitas</th>
                  <th class="px-6 py-5">Bahan Baku</th>
                  <th class="px-6 py-5 text-center">IN</th>
                  <th class="px-6 py-5 text-center">OUT</th>
                  <th class="px-8 py-5 text-right">Catatan</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                @forelse($reservation->buffetMovements->sortByDesc('id') as $m)
                  <tr class="group hover:bg-white/[0.02] transition-colors">
                    <td class="px-8 py-6 text-[10px] text-white/40 italic">{{ $m->created_at->format('d M, H:i') }}</td>
                    <td class="px-6 py-6">
                       <span class="text-[9px] font-black uppercase tracking-widest text-white/60">{{ $m->type }}</span>
                    </td>
                    <td class="px-6 py-6 text-xs font-bold text-white/80 group-hover:text-gold-primary transition-colors">{{ $m->rawMaterial?->name }}</td>
                    <td class="px-6 py-6 text-center text-xs font-black text-emerald-400 italic">{{ (float)$m->qty_in }}</td>
                    <td class="px-6 py-6 text-center text-xs font-black text-red-400 italic">{{ (float)$m->qty_out }}</td>
                    <td class="px-8 py-6 text-right text-[10px] text-white/30 italic max-w-[150px] truncate">"{{ $m->note ?? '-' }}"</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="px-8 py-12 text-center text-white/20 italic font-medium">Belum ada aktivitas stok tercatat.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- RIGHT COLUMN (ACTIONS) --}}
    <div class="space-y-6">
       <h4 class="text-xs font-black text-white/30 uppercase tracking-[0.3em] px-4">Kelola Inventori</h4>

       <!-- PURCHASE -->
       <div class="premium-card p-6 border-emerald-500/20 bg-emerald-500/[0.03] space-y-4">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
             </div>
             <h5 class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Belanja Langsung (In-Buffet)</h5>
          </div>

          <form method="POST" action="{{ route('admin.reservations.buffet_inventory.purchase', $reservation) }}" class="space-y-3">
            @csrf
            <select name="raw_material_id" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
              @foreach($actionMaterials as $rm)
                <option value="{{ $rm->id }}">{{ $rm->name }} (main: {{ (float)$rm->stock_on_hand }})</option>
              @endforeach
            </select>
            <div class="grid grid-cols-2 gap-3">
               <input name="qty" type="number" step="0.01" min="0.01" placeholder="Qty" class="rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none" required>
               <input name="unit_cost" type="number" step="0.01" min="0" placeholder="Biaya (ops)" class="rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
            </div>
            <input name="note" placeholder="Catatan belanja..." class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
            <button class="w-full rounded-xl bg-emerald-500 py-3 text-[10px] font-black text-black uppercase tracking-widest hover:scale-[1.02] transition-all">Simpan Belanja</button>
          </form>
       </div>

       <!-- TRANSFER -->
       <div class="premium-card p-6 border-blue-500/20 bg-blue-500/[0.03] space-y-4">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
             </div>
             <h5 class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Transfer MAIN → BUFFET</h5>
          </div>

          <form method="POST" action="{{ route('admin.reservations.buffet_inventory.transfer_from_main', $reservation) }}" class="space-y-3">
            @csrf
            <select name="raw_material_id" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
              @foreach($actionMaterials as $rm)
                <option value="{{ $rm->id }}">{{ $rm->name }} (main: {{ (float)$rm->stock_on_hand }})</option>
              @endforeach
            </select>
            <input name="qty" type="number" step="0.01" min="0.01" placeholder="Jumlah Qty" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none" required>
            <input name="note" placeholder="Catatan transfer..." class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
            <button class="w-full rounded-xl bg-blue-600 py-3 text-[10px] font-black text-white uppercase tracking-widest hover:scale-[1.02] transition-all">Lakukan Transfer</button>
          </form>
       </div>

       <!-- RETURN -->
       <div class="premium-card p-6 border-white/10 bg-white/[0.02] space-y-4">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z" />
                </svg>
             </div>
             <h5 class="text-[10px] font-black text-white/40 uppercase tracking-widest">Return BUFFET → MAIN</h5>
          </div>

          <form method="POST" action="{{ route('admin.reservations.buffet_inventory.return_to_main', $reservation) }}" class="space-y-3">
            @csrf
            <select name="raw_material_id" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
              @foreach($actionMaterials as $rm)
                <option value="{{ $rm->id }}">{{ $rm->name }}</option>
              @endforeach
            </select>
            <input name="qty" type="number" step="0.01" min="0.01" placeholder="Jumlah Qty" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none" required>
            <input name="note" placeholder="Alasan return..." class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
            <button class="w-full rounded-xl bg-white/10 py-3 text-[10px] font-black text-white uppercase tracking-widest hover:bg-white/20 transition-all">Selesaikan Return</button>
          </form>
       </div>

       <!-- CONSUME -->
       <div class="premium-card p-6 border-yellow-500/20 bg-yellow-500/[0.03] space-y-4">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-500 border border-yellow-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
             </div>
             <h5 class="text-[10px] font-black text-yellow-500 uppercase tracking-widest">Consume (Gunakan Bahan)</h5>
          </div>

          <form method="POST" action="{{ route('admin.reservations.buffet_inventory.consume', $reservation) }}" class="space-y-3">
            @csrf
            <select name="raw_material_id" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
              @foreach($actionMaterials as $rm)
                <option value="{{ $rm->id }}">{{ $rm->name }}</option>
              @endforeach
            </select>
            <input name="qty" type="number" step="0.01" min="0.01" placeholder="Jumlah Qty" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none" required>
            <input name="note" placeholder="Catatan penggunaan..." class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
            <button class="w-full rounded-xl bg-yellow-500 py-3 text-[10px] font-black text-black uppercase tracking-widest hover:scale-[1.02] transition-all">Catat Pemakaian</button>
          </form>
       </div>

       <!-- WASTE -->
       <div class="premium-card p-6 border-red-500/20 bg-red-500/[0.03] space-y-4">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
             </div>
             <h5 class="text-[10px] font-black text-red-500 uppercase tracking-widest">Waste (Buang Bahan)</h5>
          </div>

          <form method="POST" action="{{ route('admin.reservations.buffet_inventory.waste', $reservation) }}" class="space-y-3">
            @csrf
            <select name="raw_material_id" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
              @foreach($actionMaterials as $rm)
                <option value="{{ $rm->id }}">{{ $rm->name }}</option>
              @endforeach
            </select>
            <input name="qty" type="number" step="0.01" min="0.01" placeholder="Jumlah Qty" class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none" required>
            <input name="note" placeholder="Alasan pembuangan..." class="w-full rounded-xl border border-white/5 bg-white/[0.05] px-4 py-3 text-xs text-white outline-none">
            <button class="w-full rounded-xl bg-red-500 py-3 text-[10px] font-black text-white uppercase tracking-widest hover:scale-[1.02] transition-all">Catat Waste</button>
          </form>
       </div>
    </div>
  </div>
@endsection