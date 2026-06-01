@extends('layouts.pegawai')
@section('title','Riwayat Absensi')

@section('page_label','Pegawai')
@section('page_title','Riwayat Absensi')

@section('content')
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
  <div>
    <div class="text-lg font-semibold text-white">Riwayat Absensi</div>
    <div class="mt-1 text-sm text-white/60">Filter tanggal untuk melihat histori check-in / check-out kamu.</div>
  </div>

  <form method="GET" action="{{ route('pegawai.attendance.history') }}" class="w-full lg:w-auto flex flex-col gap-2.5 sm:flex-row sm:items-end lg:items-center">
    <div class="grid grid-cols-2 gap-2.5 w-full sm:w-auto">
      <div class="flex flex-col gap-1">
        <span class="text-[10px] font-semibold uppercase tracking-wider text-yellow-500/70 sm:hidden">Dari Tanggal</span>
        <input type="date" name="from" value="{{ $from }}"
          class="w-full rounded-xl border border-yellow-500/20 bg-white/[0.04] px-3.5 py-2.5 text-sm text-white outline-none focus:border-yellow-500/40 focus:bg-white/[0.07] transition-all" />
      </div>
      <div class="flex flex-col gap-1">
        <span class="text-[10px] font-semibold uppercase tracking-wider text-yellow-500/70 sm:hidden">Sampai Tanggal</span>
        <input type="date" name="to" value="{{ $to }}"
          class="w-full rounded-xl border border-yellow-500/20 bg-white/[0.04] px-3.5 py-2.5 text-sm text-white outline-none focus:border-yellow-500/40 focus:bg-white/[0.07] transition-all" />
      </div>
    </div>
    <button class="w-full sm:w-auto rounded-xl bg-yellow-500 hover:bg-yellow-400 px-5 py-2.5 text-sm font-semibold text-black transition-all active:scale-[0.98] flex items-center justify-center gap-2 shrink-0">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
      Tampilkan
    </button>
  </form>
</div>

<div class="mt-5 rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-4 md:p-5 backdrop-blur-xl">
  <!-- DESKTOP VIEW (hidden on mobile, visible on md upwards) -->
  <div class="hidden md:block overflow-hidden rounded-2xl border border-yellow-500/12">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[980px] text-left text-sm">
        <thead class="bg-white/[0.03] text-xs text-white/60">
          <tr>
            <th class="px-4 py-3">Tanggal</th>
            <th class="px-4 py-3">Masuk</th>
            <th class="px-4 py-3">Pulang</th>
            <th class="px-4 py-3">Durasi</th>
            <th class="px-4 py-3">Lokasi Masuk</th>
            <th class="px-4 py-3">Lokasi Pulang</th>
            <th class="px-4 py-3">Foto Masuk</th>
            <th class="px-4 py-3">Foto Pulang</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-yellow-500/10">
          @forelse($rows as $a)
            <tr class="hover:bg-white/[0.02]">
              <td class="px-4 py-3 text-white/85">{{ \Carbon\Carbon::parse($a->date)->format('d M Y') }}</td>
              <td class="px-4 py-3 text-white/80">{{ $a->check_in_at ? \Carbon\Carbon::parse($a->check_in_at)->format('H:i:s') : '--' }}</td>
              <td class="px-4 py-3 text-white/80">{{ $a->check_out_at ? \Carbon\Carbon::parse($a->check_out_at)->format('H:i:s') : '--' }}</td>
              <td class="px-4 py-3 text-white/80">{{ $a->work_duration ?? '--' }}</td>

              <td class="px-4 py-3 text-white/80">
                {{ $a->check_in_lat ?? '-' }}, {{ $a->check_in_lng ?? '-' }}
                @if($a->check_in_lat && $a->check_in_lng)
                  <div>
                    <button type="button" onclick="openMap({{ $a->check_in_lat }}, {{ $a->check_in_lng }})"
                      class="text-xs text-yellow-400 hover:underline">Lihat Map</button>
                  </div>
                @endif
              </td>

              <td class="px-4 py-3 text-white/80">
                {{ $a->check_out_lat ?? '-' }}, {{ $a->check_out_lng ?? '-' }}
                @if($a->check_out_lat && $a->check_out_lng)
                  <div>
                    <button type="button" onclick="openMap({{ $a->check_out_lat }}, {{ $a->check_out_lng }})"
                      class="text-xs text-yellow-400 hover:underline">Lihat Map</button>
                  </div>
                @endif
              </td>

              <td class="px-4 py-3">
                @if($a->check_in_photo_path)
                  <button type="button"
                    onclick="openPhoto('{{ route('pegawai.attendance.photo', ['attendance'=>$a->id, 'type'=>'in']) }}')"
                    class="text-xs text-yellow-400 hover:underline">Lihat Foto</button>
                @else
                  <span class="text-xs text-white/50">-</span>
                @endif
              </td>

              <td class="px-4 py-3">
                @if($a->check_out_photo_path)
                  <button type="button"
                    onclick="openPhoto('{{ route('pegawai.attendance.photo', ['attendance'=>$a->id, 'type'=>'out']) }}')"
                    class="text-xs text-yellow-400 hover:underline">Lihat Foto</button>
                @else
                  <span class="text-xs text-white/50">-</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-4 py-8 text-center text-white/60">Belum ada data absensi.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- MOBILE VIEW (hidden on desktop/tablet, visible on mobile) -->
  <div class="block md:hidden space-y-4">
    @forelse($rows as $a)
      <div class="rounded-2xl border border-yellow-500/10 bg-white/[0.02] p-4 hover:border-yellow-500/20 transition-all duration-300">
        <!-- Card Header -->
        <div class="flex items-center justify-between gap-2 border-b border-white/[0.04] pb-3 mb-3">
          <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-500/10 text-yellow-500">
              <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <div>
              <div class="text-sm font-semibold text-white">{{ \Carbon\Carbon::parse($a->date)->format('d M Y') }}</div>
              <div class="text-[10px] text-white/50">{{ \Carbon\Carbon::parse($a->date)->translatedFormat('l') }}</div>
            </div>
          </div>
          
          <div>
            @if($a->work_duration)
              <span class="rounded-lg bg-yellow-500/10 px-2 py-0.5 text-[10px] font-semibold text-yellow-400 border border-yellow-500/20">
                {{ $a->work_duration }}
              </span>
            @else
              <span class="rounded-lg bg-white/[0.04] px-2 py-0.5 text-[10px] font-medium text-white/40 border border-white/5">
                --
              </span>
            @endif
          </div>
        </div>

        <!-- Card Body (Grid 2 Kolom) -->
        <div class="grid grid-cols-2 gap-3 text-xs">
          <!-- Check-in Detail -->
          <div class="rounded-xl bg-white/[0.01] border border-white/[0.03] p-3 flex flex-col justify-between min-h-[110px]">
            <div>
              <div class="flex items-center gap-1 text-white/40 mb-1 font-medium">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                <span>Masuk</span>
              </div>
              <div class="text-sm font-bold text-white">
                {{ $a->check_in_at ? \Carbon\Carbon::parse($a->check_in_at)->format('H:i:s') : '--:--:--' }}
              </div>
            </div>
            
            @if($a->check_in_at)
              <div class="mt-2.5 space-y-1.5 pt-2 border-t border-white/[0.04]">
                <!-- Foto -->
                <div class="flex items-center justify-between gap-1">
                  <span class="text-white/40 text-[10px]">Foto:</span>
                  @if($a->check_in_photo_path)
                    <button type="button"
                      onclick="openPhoto('{{ route('pegawai.attendance.photo', ['attendance'=>$a->id, 'type'=>'in']) }}')"
                      class="text-[11px] font-bold text-yellow-400 hover:text-yellow-300 transition">
                      Lihat Foto
                    </button>
                  @else
                    <span class="text-white/30 text-[10px]">-</span>
                  @endif
                </div>
                
                <!-- Map -->
                <div class="flex items-center justify-between gap-1">
                  <span class="text-white/40 text-[10px]">Lokasi:</span>
                  @if($a->check_in_lat && $a->check_in_lng)
                    <button type="button" onclick="openMap({{ $a->check_in_lat }}, {{ $a->check_in_lng }})"
                      class="text-[11px] font-bold text-yellow-400 hover:text-yellow-300 transition">
                      Lihat Map
                    </button>
                  @else
                    <span class="text-white/30 text-[10px]">-</span>
                  @endif
                </div>
              </div>
            @endif
          </div>

          <!-- Check-out Detail -->
          <div class="rounded-xl bg-white/[0.01] border border-white/[0.03] p-3 flex flex-col justify-between min-h-[110px]">
            <div>
              <div class="flex items-center gap-1 text-white/40 mb-1 font-medium">
                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                <span>Pulang</span>
              </div>
              <div class="text-sm font-bold text-white">
                {{ $a->check_out_at ? \Carbon\Carbon::parse($a->check_out_at)->format('H:i:s') : '--:--:--' }}
              </div>
            </div>
            
            @if($a->check_out_at)
              <div class="mt-2.5 space-y-1.5 pt-2 border-t border-white/[0.04]">
                <!-- Foto -->
                <div class="flex items-center justify-between gap-1">
                  <span class="text-white/40 text-[10px]">Foto:</span>
                  @if($a->check_out_photo_path)
                    <button type="button"
                      onclick="openPhoto('{{ route('pegawai.attendance.photo', ['attendance'=>$a->id, 'type'=>'out']) }}')"
                      class="text-[11px] font-bold text-yellow-400 hover:text-yellow-300 transition">
                      Lihat Foto
                    </button>
                  @else
                    <span class="text-white/30 text-[10px]">-</span>
                  @endif
                </div>
                
                <!-- Map -->
                <div class="flex items-center justify-between gap-1">
                  <span class="text-white/40 text-[10px]">Lokasi:</span>
                  @if($a->check_out_lat && $a->check_out_lng)
                    <button type="button" onclick="openMap({{ $a->check_out_lat }}, {{ $a->check_out_lng }})"
                      class="text-[11px] font-bold text-yellow-400 hover:text-yellow-300 transition">
                      Lihat Map
                    </button>
                  @else
                    <span class="text-white/30 text-[10px]">-</span>
                  @endif
                </div>
              </div>
            @else
              @if($a->check_in_at)
                <div class="mt-2.5 flex items-center justify-center py-1.5 bg-yellow-500/[0.03] rounded-lg border border-yellow-500/10">
                  <span class="text-[9px] text-yellow-500/70 font-semibold tracking-wide uppercase">Belum Checkout</span>
                </div>
              @endif
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="rounded-2xl border border-white/5 bg-white/[0.01] p-8 text-center text-white/50 text-sm">
        Belum ada data absensi untuk rentang tanggal ini.
      </div>
    @endforelse
  </div>
</div>

{{-- MODAL FOTO --}}
<div id="photoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
  <div class="w-full max-w-3xl overflow-hidden rounded-2xl border border-white/10 bg-[#0f0f0f]">
    <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
      <div class="text-sm font-semibold text-white">Foto Absensi</div>
      <button id="closePhotoModal" class="rounded-lg border border-white/15 bg-white/[0.06] px-3 py-1.5 text-xs text-white/80 hover:bg-white/[0.10]">
        Tutup
      </button>
    </div>
    <div class="p-4">
      <img id="photoModalImg" src="" alt="Foto Absensi" class="mx-auto max-h-[75vh] w-auto rounded-xl" />
    </div>
  </div>
</div>

{{-- MODAL MAP --}}
<div id="mapModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
  <div class="w-full max-w-4xl overflow-hidden rounded-2xl border border-white/10 bg-[#0f0f0f]">
    <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
      <div class="text-sm font-semibold text-white">Lokasi Absensi</div>
      <button id="closeMapModal" class="rounded-lg border border-white/15 bg-white/[0.06] px-3 py-1.5 text-xs text-white/80 hover:bg-white/[0.10]">
        Tutup
      </button>
    </div>
    <div class="p-4">
      <div class="overflow-hidden rounded-xl border border-white/10">
        <iframe id="mapFrame" src="" width="100%" height="520" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
      </div>
      <div id="mapCoords" class="mt-2 text-xs text-white/60"></div>
    </div>
  </div>
</div>

<script>
  // FOTO
  const photoModal = document.getElementById('photoModal');
  const photoImg = document.getElementById('photoModalImg');
  const closePhoto = document.getElementById('closePhotoModal');

  function openPhoto(url){
    photoImg.src = url;
    photoModal.classList.remove('hidden');
    photoModal.classList.add('flex');
  }
  function closePhotoModal(){
    photoImg.src = '';
    photoModal.classList.add('hidden');
    photoModal.classList.remove('flex');
  }
  closePhoto.addEventListener('click', closePhotoModal);
  photoModal.addEventListener('click', (e)=>{ if(e.target === photoModal) closePhotoModal(); });

  // MAP
  const mapModal = document.getElementById('mapModal');
  const mapFrame = document.getElementById('mapFrame');
  const mapCoords = document.getElementById('mapCoords');
  const closeMap = document.getElementById('closeMapModal');

  function openMap(lat, lng){
    mapFrame.src = `https://www.google.com/maps?q=${lat},${lng}&z=18&output=embed`;
    mapCoords.textContent = `Koordinat: ${lat}, ${lng}`;
    mapModal.classList.remove('hidden');
    mapModal.classList.add('flex');
  }
  function closeMapModal(){
    mapFrame.src = '';
    mapModal.classList.add('hidden');
    mapModal.classList.remove('flex');
  }
  closeMap.addEventListener('click', closeMapModal);
  mapModal.addEventListener('click', (e)=>{ if(e.target === mapModal) closeMapModal(); });

  window.openPhoto = openPhoto;
  window.openMap = openMap;
</script>
@endsection