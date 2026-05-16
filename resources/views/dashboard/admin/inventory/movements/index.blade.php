@extends('layouts.admin')
@section('title', 'Inventory Movements')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gold-gradient">Inventory Movements</h1>
      <p class="text-sm text-white/40 font-medium italic">Ledger riwayat pergerakan stok bahan baku <span class="text-white/60 font-bold not-italic">(IN / OUT / ADJ).</span></p>
    </div>
  </div>

  <!-- FILTERS -->
  <section class="glass-panel p-8 rounded-[2.5rem] mb-10">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
      <div class="space-y-2">
        <label class="text-[10px] uppercase tracking-widest text-white/40 font-black ml-1">Pilih Bahan</label>
        <select name="raw_material_id"
          class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-4 py-3 text-xs text-white outline-none focus:border-gold-primary/30 transition-all appearance-none">
          <option value="">Semua Bahan</option>
          @foreach($materials as $m)
            <option value="{{ $m->id }}" @selected((int)$materialId === (int)$m->id)>{{ $m->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="space-y-2">
        <label class="text-[10px] uppercase tracking-widest text-white/40 font-black ml-1">Tipe Movement</label>
        <select name="type"
          class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-4 py-3 text-xs text-white outline-none focus:border-gold-primary/30 transition-all appearance-none">
          <option value="">Semua Tipe</option>
          @foreach([
            'purchase' => 'purchase',
            'waste' => 'waste',
            'opname' => 'opname',
            'adjustment' => 'adjustment',
            'reserve' => 'reserve',
            'release' => 'release',
            'commit_paid' => 'commit paid',
            'sale' => 'sale',
          ] as $k=>$v)
            <option value="{{ $k }}" @selected($type===$k)>{{ strtoupper($v) }}</option>
          @endforeach
        </select>
      </div>

      <div class="space-y-2">
        <label class="text-[10px] uppercase tracking-widest text-white/40 font-black ml-1">Dari Tanggal</label>
        <input type="date" name="date_from" value="{{ $dateFrom }}"
          class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-4 py-3 text-xs text-white outline-none focus:border-gold-primary/30 transition-all">
      </div>

      <div class="space-y-2">
        <label class="text-[10px] uppercase tracking-widest text-white/40 font-black ml-1">Sampai Tanggal</label>
        <input type="date" name="date_to" value="{{ $dateTo }}"
          class="w-full rounded-2xl border border-white/5 bg-white/[0.02] px-4 py-3 text-xs text-white outline-none focus:border-gold-primary/30 transition-all">
      </div>

      <div class="flex gap-2">
        <button class="flex-1 rounded-2xl bg-gold-primary px-4 py-3 text-[10px] font-black text-obsidian-950 uppercase tracking-widest shadow-lg shadow-gold-primary/20 hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
          Filter Data
        </button>
        <a href="{{ route('admin.inventory-movements.index') }}"
          class="flex-1 rounded-2xl bg-white/5 px-4 py-3 text-[10px] font-black text-white uppercase tracking-widest border border-white/10 hover:bg-white/10 text-center transition-all">
          Reset
        </a>
      </div>
    </form>
  </section>

  <!-- DATA TABLE / CARDS -->
  <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/5">
    <!-- DESKTOP TABLE -->
    <div class="hidden lg:block overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-white/[0.03] text-[10px] uppercase tracking-widest text-white/40 font-black border-b border-white/5">
            <th class="px-8 py-6">Waktu & Bahan</th>
            <th class="px-6 py-6">Tipe</th>
            <th class="px-6 py-6 text-emerald-400">IN</th>
            <th class="px-6 py-6 text-red-400">OUT</th>
            <th class="px-6 py-6">Saldo Stok</th>
            <th class="px-6 py-6 text-right whitespace-nowrap">Ref & Admin</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @forelse($movements as $mv)
            <tr class="group hover:bg-white/[0.01] transition-colors text-sm">
              <td class="px-8 py-6">
                 <div class="font-bold text-white mb-1">{{ $mv->rawMaterial?->name ?? '-' }}</div>
                 <div class="flex items-center gap-2">
                    <span class="text-[10px] text-white/20 font-medium">{{ $mv->created_at->format('d/m/Y H:i') }}</span>
                    <span class="w-1 h-1 rounded-full bg-white/10"></span>
                    <span class="text-[10px] text-gold-primary/40 font-bold uppercase tracking-tighter italic">{{ $mv->rawMaterial?->unit ?? '' }}</span>
                 </div>
              </td>
              <td class="px-6 py-6">
                <span class="px-2 py-0.5 rounded-md bg-white/5 border border-white/10 text-[9px] font-black text-white/60 uppercase tracking-widest">
                   {{ $mv->type }}
                </span>
              </td>
              <td class="px-6 py-6">
                @if($mv->qty_in > 0)
                  <span class="font-black text-emerald-400">+{{ number_format((float)$mv->qty_in, 2, '.', '') }}</span>
                @else
                  <span class="text-white/10">-</span>
                @endif
              </td>
              <td class="px-6 py-6">
                @if($mv->qty_out > 0)
                  <span class="font-black text-red-400">-{{ number_format((float)$mv->qty_out, 2, '.', '') }}</span>
                @else
                  <span class="text-white/10">-</span>
                @endif
              </td>
              <td class="px-6 py-6 font-black text-white">
                 {{ number_format((float)$mv->running_balance, 2, '.', '') }}
              </td>
              <td class="px-8 py-6 text-right">
                 <div class="text-[10px] font-bold text-white/60 mb-0.5">{{ $mv->creator?->name ?? 'System' }}</div>
                 <div class="text-[9px] text-white/20 italic uppercase tracking-tighter">{{ class_basename($mv->reference_type) }} #{{ $mv->reference_id }}</div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-8 py-20 text-center text-white/20 italic">Belum ada riwayat pergerakan stok.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- MOBILE LIST -->
    <div class="lg:hidden divide-y divide-white/5">
       @forelse($movements as $mv)
         <div class="p-6 space-y-4">
            <div class="flex items-start justify-between gap-4">
               <div>
                  <h4 class="text-sm font-bold text-white">{{ $mv->rawMaterial?->name ?? '-' }}</h4>
                  <p class="text-[10px] text-white/30 font-bold uppercase tracking-widest mt-0.5">{{ $mv->created_at->format('d M, H:i') }}</p>
               </div>
               <div class="text-right">
                  <span class="px-2 py-0.5 rounded-md bg-white/5 border border-white/10 text-[8px] font-black text-white/40 uppercase tracking-widest">{{ $mv->type }}</span>
               </div>
            </div>

            <div class="grid grid-cols-3 gap-2 py-3 border-y border-white/5">
               <div class="flex flex-col gap-1">
                  <span class="text-[8px] uppercase tracking-widest text-white/20 font-black">IN</span>
                  <span class="text-xs font-black {{ $mv->qty_in > 0 ? 'text-emerald-400' : 'text-white/10' }}">{{ $mv->qty_in > 0 ? '+' . number_format($mv->qty_in, 2) : '0' }}</span>
               </div>
               <div class="flex flex-col gap-1">
                  <span class="text-[8px] uppercase tracking-widest text-white/20 font-black">OUT</span>
                  <span class="text-xs font-black {{ $mv->qty_out > 0 ? 'text-red-400' : 'text-white/10' }}">{{ $mv->qty_out > 0 ? '-' . number_format($mv->qty_out, 2) : '0' }}</span>
               </div>
               <div class="flex flex-col gap-1 text-right">
                  <span class="text-[8px] uppercase tracking-widest text-white/20 font-black">SALDO</span>
                  <span class="text-xs font-black text-gold-primary">{{ number_format($mv->running_balance, 2) }}</span>
               </div>
            </div>

            <div class="flex items-center justify-between">
               <span class="text-[9px] text-white/20 italic uppercase tracking-tighter">{{ class_basename($mv->reference_type) }} #{{ $mv->reference_id }}</span>
               <span class="text-[9px] font-bold text-white/40 uppercase tracking-widest">By: {{ $mv->creator?->name ?? 'System' }}</span>
            </div>
         </div>
       @empty
         <div class="p-10 text-center text-white/30 italic text-xs">Belum ada data.</div>
       @endforelse
    </div>
  </div>

  <div class="mt-8">
    {{ $movements->links() }}
  </div>
@endsection
