@extends('layouts.admin')
@section('title', 'Galeri')

@section('body')
    <!-- HEADER -->
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gold-gradient">Master Galeri</h1>
            <p class="text-sm text-white/40 font-medium">Kelola foto-foto momen di Ayo Renne untuk ditampilkan di landing page.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.gallery.create') }}"
                class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gold-primary via-gold-primary to-gold-dark px-6 py-3.5 text-xs font-black text-obsidian-950 uppercase tracking-widest shadow-xl shadow-gold-primary/20 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] hover:scale-[1.02] transition-all active:scale-95 border border-gold-light/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Foto Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-sm font-bold text-green-100">{{ session('success') }}</p>
        </div>
    @endif

    <!-- GALLERY GRID -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($items as $item)
            <div class="glass-panel rounded-[2rem] overflow-hidden group">
                <div class="relative aspect-square overflow-hidden">
                    <img src="{{ $item->imageUrl() }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="{{ $item->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-obsidian-950 via-transparent to-transparent opacity-60"></div>
                    
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest border {{ $item->is_active ? 'border-green-500/20 bg-green-500/10 text-green-400' : 'border-red-500/20 bg-red-500/10 text-red-400' }}">
                            {{ $item->is_active ? 'ACTIVE' : 'INACTIVE' }}
                        </span>
                    </div>

                    <div class="absolute bottom-4 left-6 right-6">
                        <h4 class="text-sm font-bold text-white truncate">{{ $item->title ?: 'Untitled Moment' }}</h4>
                        <p class="text-[10px] text-white/40 uppercase tracking-widest mt-1">Order: {{ $item->sort_order }}</p>
                    </div>
                </div>

                <div class="p-4 flex items-center justify-between gap-2 border-t border-white/5">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.gallery.edit', $item->id) }}"
                            class="p-2.5 rounded-xl bg-white/5 text-white/40 hover:text-blue-400 hover:bg-blue-400/10 transition-all"
                            title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.gallery.destroy', $item->id) }}"
                        onsubmit="return confirm('Hapus foto ini dari galeri?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="p-2.5 rounded-xl bg-white/5 text-white/40 hover:text-red-500 hover:bg-red-500/10 transition-all"
                            title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center glass-panel rounded-[3rem]">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-white/40 font-bold italic text-lg">Belum ada foto dalam galeri</p>
                    <a href="{{ route('admin.gallery.create') }}" class="text-gold-primary font-bold hover:underline">Unggah foto pertama Anda</a>
                </div>
            </div>
        @endforelse
    </section>
@endsection
