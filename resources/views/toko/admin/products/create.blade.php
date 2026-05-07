@extends('toko.layouts.admin')

@section('title', 'Tambah Produk Toko')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-up">

    <div class="flex items-center gap-4">
        <a href="{{ route('toko.products.index') }}" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-white tracking-wide">Tambah Produk</h2>
            <p class="text-white/50 text-sm mt-1">Masukkan informasi detail produk retail.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('toko.products.store') }}" method="POST" class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 md:p-8 shadow-xl space-y-6" x-data="{ hasVariants: false, variants: [{id: 1}] }">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Produk -->
            <div class="md:col-span-2">
                <label class="block text-xs uppercase tracking-widest text-white/50 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition-all">
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-xs uppercase tracking-widest text-white/50 mb-2">Kategori</label>
                <select name="toko_category_id" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 outline-none appearance-none">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- SKU -->
            <div>
                <label class="block text-xs uppercase tracking-widest text-white/50 mb-2">SKU (Barcode)</label>
                <input type="text" name="sku" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 outline-none transition-all">
            </div>

            <!-- Harga Default -->
            <div x-show="!hasVariants">
                <label class="block text-xs uppercase tracking-widest text-white/50 mb-2">Harga <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-white/30 text-sm">Rp</span>
                    </div>
                    <input type="number" name="price" :required="!hasVariants" class="w-full bg-black/50 border border-white/10 rounded-xl pl-12 pr-4 py-3 text-white text-sm focus:border-yellow-500 outline-none transition-all">
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="md:col-span-2">
                <label class="block text-xs uppercase tracking-widest text-white/50 mb-2">Deskripsi Produk</label>
                <textarea name="description" rows="4" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-yellow-500 outline-none transition-all"></textarea>
            </div>
        </div>

        <hr class="border-white/5">

        <!-- Variant Toggle -->
        <div>
            <label class="flex items-center gap-3 cursor-pointer group w-max">
                <div class="relative">
                    <input type="checkbox" name="has_variants" value="1" x-model="hasVariants" class="sr-only">
                    <div class="w-10 h-6 bg-white/10 rounded-full shadow-inner transition-colors" :class="hasVariants ? 'bg-yellow-500' : ''"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform" :class="hasVariants ? 'translate-x-4' : ''"></div>
                </div>
                <span class="text-sm font-semibold text-white/70 group-hover:text-white transition-colors">Produk ini memiliki banyak Varian (Ukuran, Warna, dsb)</span>
            </label>
        </div>

        <!-- Variants Section -->
        <div x-show="hasVariants" x-collapse class="bg-black/30 border border-white/5 rounded-2xl p-6 space-y-4">
            <h4 class="text-sm font-bold text-white mb-4">Daftar Varian</h4>
            
            <template x-for="(variant, index) in variants" :key="variant.id">
                <div class="flex flex-col sm:flex-row gap-4 items-end bg-black/50 p-4 rounded-xl border border-white/5">
                    <div class="flex-1 w-full">
                        <label class="block text-[10px] uppercase tracking-widest text-white/40 mb-1">Nama Varian</label>
                        <input type="text" :name="'variants['+index+'][name]'" placeholder="Mis: Merah - L" class="w-full bg-black border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-yellow-500 outline-none">
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-[10px] uppercase tracking-widest text-white/40 mb-1">SKU Varian</label>
                        <input type="text" :name="'variants['+index+'][sku]'" placeholder="SKU-VAR-01" class="w-full bg-black border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-yellow-500 outline-none">
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-[10px] uppercase tracking-widest text-white/40 mb-1">Harga Varian</label>
                        <input type="number" :name="'variants['+index+'][price]'" placeholder="0" class="w-full bg-black border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-yellow-500 outline-none">
                    </div>
                    <button type="button" @click="variants.splice(index, 1)" class="w-full sm:w-auto px-3 py-2 text-red-400 hover:bg-red-500/10 rounded-lg text-sm transition-colors border border-transparent hover:border-red-500/30">
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </template>

            <button type="button" @click="variants.push({id: Date.now()})" class="text-yellow-500 hover:text-yellow-400 text-sm font-bold flex items-center gap-2 mt-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Baris Varian
            </button>
        </div>

        <div class="pt-6 flex justify-end">
            <button type="submit" class="bg-yellow-500 text-black px-8 py-3 rounded-xl font-bold hover:bg-yellow-400 transition-colors shadow-[0_0_15px_rgba(234,179,8,0.3)]">
                Simpan Produk
            </button>
        </div>
    </form>

</div>
@endsection
