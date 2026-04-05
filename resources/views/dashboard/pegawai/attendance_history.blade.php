@extends('layouts.pegawai')
@section('title','Riwayat Absensi')

@section('page_label','Pegawai')
@section('page_title','Riwayat Absensi')

@section('content')
<div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
  <div>
    <div class="text-lg font-semibold text-white">Riwayat Absensi</div>
    <div class="mt-1 text-sm text-white/60">Filter tanggal untuk melihat histori check-in / check-out kamu.</div>
  </div>

  <form method="GET" action="{{ route('pegawai.attendance.history') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
    <input type="date" name="from" value="{{ $from }}"
      class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-3 py-2 text-sm text-white outline-none" />
    <input type="date" name="to" value="{{ $to }}"
      class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-3 py-2 text-sm text-white outline-none" />
    <button class="rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
      Tampilkan
    </button>
  </form>
</div>

<div class="mt-5 rounded-[28px] border border-yellow-500/16 bg-[#121212]/90 p-5 backdrop-blur-xl">
  <div class="overflow-hidden rounded-2xl border border-yellow-500/12">
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