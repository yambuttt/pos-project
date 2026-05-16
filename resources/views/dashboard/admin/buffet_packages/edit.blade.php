@extends('layouts.admin')
@section('title', 'Edit Paket Buffet')

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
        <h1 class="text-3xl font-bold text-gold-gradient">Edit Paket</h1>
        <p class="text-sm text-white/40 font-medium italic">Mengelola konfigurasi <span class="text-gold-primary font-bold not-italic">{{ $pkg->name }}</span></p>
      </div>
    </div>

    <a href="{{ route('admin.buffet_packages.index') }}"
      class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-6 py-3.5 text-xs font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali
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

  <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1.2fr_1fr]">
    <!-- LEFT: MAIN INFO -->
    <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] h-fit relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-gold-primary/5 blur-[100px] rounded-full"></div>
        
        <form method="POST" action="{{ route('admin.buffet_packages.update', $pkg) }}" class="relative z-10 space-y-8">
            @csrf @method('PUT')

            <div class="space-y-6">
                <div class="flex items-center gap-3">
                   <div class="w-8 h-8 rounded-lg bg-gold-primary/10 flex items-center justify-center text-gold-primary border border-gold-primary/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                   </div>
                   <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Informasi Dasar</h3>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Nama Paket</label>
                    <input type="text" name="name" value="{{ $pkg->name }}"
                        class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all"
                        required>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Tipe Harga</label>
                        <select name="pricing_type" class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all appearance-none">
                          <option value="per_pax" @selected($pkg->pricing_type==='per_pax')>PER PAX (Per Orang)</option>
                          <option value="per_event" @selected($pkg->pricing_type==='per_event')>PER EVENT (Per Acara)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Harga (Rp)</label>
                        <input type="number" name="price" value="{{ $pkg->price }}"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-gold-primary font-bold outline-none focus:border-gold-primary/30 transition-all"
                            required>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Min Pax (opsional)</label>
                        <input type="number" name="min_pax" value="{{ $pkg->min_pax }}" placeholder="-"
                            class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-gold-primary/30 transition-all">
                    </div>
                    <div class="flex items-end pb-3">
                        <label class="flex items-center gap-3 cursor-pointer group">
                           <div class="relative">
                              <input type="checkbox" name="is_active" value="1" @checked($pkg->is_active) class="sr-only peer">
                              <div class="w-10 h-5 bg-white/10 rounded-full peer peer-checked:bg-gold-primary transition-all"></div>
                              <div class="absolute top-1 left-1 w-3 h-3 bg-white/40 rounded-full peer-checked:translate-x-5 peer-checked:bg-obsidian-950 transition-all"></div>
                           </div>
                           <span class="text-[10px] font-black text-white/40 uppercase tracking-widest group-hover:text-white transition-colors">Aktifkan Paket</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] uppercase tracking-widest text-white/40 font-black ml-1">Notes / Deskripsi</label>
                    <textarea name="notes" rows="3" placeholder="Informasi tambahan paket..."
                        class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-4 text-sm text-white outline-none placeholder:text-white/20 focus:border-gold-primary/30 transition-all">{{ $pkg->notes }}</textarea>
                </div>
            </div>

            <button class="w-full rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark py-4 text-sm font-black text-obsidian-950 uppercase tracking-[0.3em] shadow-2xl shadow-gold-primary/20 hover:shadow-[0_0_30px_rgba(212,175,55,0.4)] hover:scale-[1.01] transition-all active:scale-[0.98]">
                Update Konfigurasi
            </button>
        </form>
    </div>

    <!-- RIGHT: PACKAGE ITEMS -->
    <div class="space-y-6">
       <!-- ADD ITEM FORM -->
       <div class="glass-panel p-8 rounded-[2.5rem] relative overflow-hidden">
          <div class="flex items-center gap-3 mb-6">
             <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
             </div>
             <div>
                <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Tambah Item Paket</h3>
                <p class="text-[9px] text-white/30 italic">Masukkan produk yang termasuk dalam paket ini.</p>
             </div>
          </div>

          <form method="POST" action="{{ route('admin.buffet_packages.items.store', $pkg) }}" class="space-y-4">
            @csrf
            <div class="space-y-2">
               <label class="text-[8px] uppercase tracking-widest text-white/20 font-black ml-1">Pilih Produk</label>
               <select name="product_id" class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-emerald-500/30 transition-all appearance-none">
                 @foreach($products as $p)
                   <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                 @endforeach
               </select>
            </div>

            <div class="grid grid-cols-3 gap-4">
               <div class="col-span-1 space-y-2">
                  <label class="text-[8px] uppercase tracking-widest text-white/20 font-black ml-1">Quantity</label>
                  <input type="number" name="qty" min="1" value="1"
                    class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none focus:border-emerald-500/30 transition-all">
               </div>
               <div class="col-span-2 space-y-2">
                  <label class="text-[8px] uppercase tracking-widest text-white/20 font-black ml-1">Note (opsional)</label>
                  <input name="note" placeholder="Contoh: Tanpa pedas..."
                    class="w-full rounded-xl border border-white/5 bg-white/[0.03] px-4 py-3.5 text-sm text-white outline-none placeholder:text-white/20 focus:border-emerald-500/30 transition-all">
               </div>
            </div>

            <button class="w-full rounded-xl bg-emerald-500 py-3.5 text-[10px] font-black text-obsidian-950 uppercase tracking-widest hover:scale-[1.02] transition-all shadow-lg shadow-emerald-500/20">
               Tambah / Perbarui Item
            </button>
          </form>
       </div>

       <!-- ITEMS LIST TABLE -->
       <div class="glass-panel p-8 rounded-[2.5rem] relative overflow-hidden">
          <div class="flex items-center gap-3 mb-6">
             <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white/40 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
             </div>
             <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Daftar Isi Paket</h3>
          </div>

          <div class="overflow-hidden rounded-2xl border border-white/5 bg-black/20">
             <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                   <thead class="bg-white/[0.03] text-[9px] uppercase tracking-widest text-white/30 font-black border-b border-white/5">
                      <tr>
                         <th class="px-6 py-4">Produk</th>
                         <th class="px-4 py-4 text-center">Qty</th>
                         <th class="px-6 py-4 text-right">Aksi</th>
                      </tr>
                   </thead>
                   <tbody class="divide-y divide-white/5">
                      @forelse($pkg->items as $it)
                        <tr class="group hover:bg-white/[0.01] transition-colors">
                           <td class="px-6 py-4">
                              <div class="text-xs font-bold text-white">{{ $it->product?->name }}</div>
                              @if($it->note)
                                 <div class="text-[9px] text-white/30 italic mt-0.5">"{{ $it->note }}"</div>
                              @endif
                           </td>
                           <td class="px-4 py-4 text-center">
                              <span class="text-xs font-black text-gold-primary italic">{{ $it->qty }}</span>
                           </td>
                           <td class="px-6 py-4 text-right">
                              <form method="POST" action="{{ route('admin.buffet_packages.items.destroy', [$pkg, $it]) }}"
                                onsubmit="return confirm('Hapus item ini?')">
                                @csrf @method('DELETE')
                                <button class="w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/20 text-red-500 flex items-center justify-center ml-auto hover:bg-red-500 hover:text-white transition-all">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                   </svg>
                                </button>
                              </form>
                           </td>
                        </tr>
                      @empty
                        <tr>
                           <td colspan="3" class="px-6 py-8 text-center text-white/20 italic text-xs">Belum ada item dalam paket.</td>
                        </tr>
                      @endforelse
                   </tbody>
                </table>
             </div>
          </div>
       </div>
    </div>
  </div>
@endsection