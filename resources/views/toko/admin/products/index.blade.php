@extends('toko.layouts.admin')

@section('title', 'Manajemen Produk Toko')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-wide">Katalog Produk</h2>
            <p class="text-white/50 text-sm mt-1">Kelola data produk retail, harga, dan varian.</p>
        </div>
        <a href="{{ route('toko.products.create') }}" class="bg-yellow-500 hover:bg-yellow-400 text-black px-4 py-2 rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(234,179,8,0.3)] transition-all">
            + Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-white/5 border-b border-white/5 text-white/50 uppercase tracking-widest text-[10px]">
                    <th class="px-6 py-4 font-semibold">Produk</th>
                    <th class="px-6 py-4 font-semibold">Kategori</th>
                    <th class="px-6 py-4 font-semibold">SKU</th>
                    <th class="px-6 py-4 font-semibold text-right">Harga Utama</th>
                    <th class="px-6 py-4 font-semibold text-center">Stok</th>
                    <th class="px-6 py-4 font-semibold text-center">Varian</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-white/80">
                @forelse($products as $product)
                <tr class="hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4 font-medium text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center overflow-hidden shrink-0">
                            @if($product->image_url)
                                <img src="{{ asset('storage/' . $product->image_url) }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-5 h-5 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @endif
                        </div>
                        {{ $product->name }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-white/5 rounded text-xs">{{ $product->category->name ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-white/50 font-mono text-xs">{{ $product->sku ?? '-' }}</td>
                    <td class="px-6 py-4 text-right font-mono text-yellow-500">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($product->has_variants)
                            <span class="text-white/40 text-xs italic">Lihat Varian</span>
                        @else
                            <span class="{{ $product->stock <= 5 ? 'text-red-400' : 'text-green-400' }} font-bold">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($product->has_variants)
                            <span class="px-2 py-1 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full text-[10px] font-bold">YA</span>
                        @else
                            <span class="px-2 py-1 bg-white/5 text-white/30 rounded-full text-[10px]">TIDAK</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('toko.products.edit', $product) }}" class="text-yellow-500 hover:text-yellow-400 p-2 inline-block">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                        <form action="{{ route('toko.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 p-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-white/30">Belum ada data produk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $products->links() }}
    </div>

</div>
@endsection
