@extends('layouts.admin')
@section('title', 'History Absensi')

@section('body')
    @php
        use Illuminate\Support\Facades\Storage;

        // helper untuk path foto yang disimpan sebagai "public/...."
        $photoUrl = function (?string $path) {
            if (!$path)
                return null;
            $clean = str_starts_with($path, 'public/') ? substr($path, 7) : $path;
            return asset('storage/' . $clean);
        };
        $workDuration = function ($checkInAt, $checkOutAt) {
            if (!$checkInAt || !$checkOutAt)
                return null;

            $seconds = \Carbon\Carbon::parse($checkOutAt)->diffInSeconds(\Carbon\Carbon::parse($checkInAt));
            if ($seconds < 0)
                return null;

            $hours = intdiv($seconds, 3600);
            $minutes = intdiv($seconds % 3600, 60);

            return str_pad((string) $hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
        };
    @endphp

    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="text-lg font-semibold text-white">History Absensi</div>
            <div class="mt-1 text-xs text-white/60">Pilih tanggal untuk melihat siapa saja yang check-in & check-out.</div>
        </div>

        <form method="GET" action="{{ route('admin.attendance.history') }}"
            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-2">
            <input type="date" name="date" value="{{ $date }}"
                class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-3 py-2 text-sm text-white outline-none" />

            <select name="user_id"
                class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-3 py-2 text-sm text-white outline-none">
                <option value="">Semua Pegawai</option>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" @selected((string) $selectedUserId === (string) $e->id)>
                        {{ $e->name }}{{ $e->email ? ' • ' . $e->email : '' }}
                    </option>
                @endforeach
            </select>

            <button class="rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
                Tampilkan
            </button>
        </form>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- TABLE CHECK-IN --}}
        <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
            <div class="flex items-center justify-between">
                <div class="text-sm font-semibold text-white">Check-in ({{ count($checkins) }})</div>
                <div class="text-xs text-white/60">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-white/70">
                            <th class="py-2 pr-4">Pegawai</th>
                            <th class="py-2 pr-4">Jam</th>
                            <th class="py-2 pr-4">Durasi</th>
                            <th class="py-2 pr-4">Lokasi</th>
                            <th class="py-2 pr-4">Foto</th>
                        </tr>
                    </thead>
                    <tbody class="text-white/85">
                        @forelse($checkins as $a)
                            @php
                                $url = $photoUrl($a->check_in_photo_path);
                            @endphp
                            <tr class="border-t border-white/10">
                                <td class="py-3 pr-4">
                                    <div class="font-semibold text-white">{{ $a->user->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-white/50">{{ $a->user->email ?? '' }}</div>
                                    <div class="mt-1 text-[11px] font-mono text-white/40">{{ $a->device_hash }}</div>
                                </td>

                                <td class="py-3 pr-4">
                                    <div class="font-semibold">{{ optional($a->check_in_at)->format('H:i:s') }}</div>
                                </td>
                                <td class="py-3 pr-4">
                                    @php $dur = $workDuration($a->check_in_at, $a->check_out_at); @endphp

                                    <div class="font-semibold">{{ $dur ?? '--' }}</div>

                                    @if(!$a->check_out_at)
                                        <div class="text-xs text-white/50">belum checkout</div>
                                    @endif
                                </td>

                                <td class="py-3 pr-4">
                                    <div class="text-xs">
                                        {{ $a->check_in_lat ?? '-' }}, {{ $a->check_in_lng ?? '-' }}
                                    </div>
                                    @if($a->check_in_lat && $a->check_in_lng)
  <button
    type="button"
    onclick="openMap({{ $a->check_in_lat }}, {{ $a->check_in_lng }})"
    class="text-xs text-yellow-400 hover:underline"
  >
    Lihat Map
  </button>
@endif
                                </td>

                                <td class="py-3 pr-4">
                                    @if($a->check_in_photo_path)
                                        <button type="button"
                                            onclick="openPhoto('{{ route('admin.attendance.photo', ['attendance' => $a->id, 'type' => 'in']) }}')"
                                            class="text-yellow-400 hover:underline text-xs">
                                            Lihat Foto
                                        </button>
                                    @else
                                        <span class="text-xs text-white/50">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-sm text-white/60">Belum ada check-in pada tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABLE CHECK-OUT --}}
        <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
            <div class="flex items-center justify-between">
                <div class="text-sm font-semibold text-white">Check-out ({{ count($checkouts) }})</div>
                <div class="text-xs text-white/60">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-white/70">
                            <th class="py-2 pr-4">Pegawai</th>
                            <th class="py-2 pr-4">Jam</th>
                            <th class="py-2 pr-4">Durasi</th>
                            <th class="py-2 pr-4">Lokasi</th>
                            <th class="py-2 pr-4">Foto</th>
                        </tr>
                    </thead>
                    <tbody class="text-white/85">
                        @forelse($checkouts as $a)
                            @php
                                $url = $photoUrl($a->check_out_photo_path);
                            @endphp
                            <tr class="border-t border-white/10">
                                <td class="py-3 pr-4">
                                    <div class="font-semibold text-white">{{ $a->user->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-white/50">{{ $a->user->email ?? '' }}</div>
                                    <div class="mt-1 text-[11px] font-mono text-white/40">{{ $a->device_hash }}</div>
                                </td>

                                <td class="py-3 pr-4">
                                    <div class="font-semibold">{{ optional($a->check_out_at)->format('H:i:s') }}</div>
                                </td>
                                <td class="py-3 pr-4">
                                    @php $dur = $workDuration($a->check_in_at, $a->check_out_at); @endphp
                                    {{ $a->work_duration ?? '--' }}
                                </td>

                                <td class="py-3 pr-4">
                                    <div class="text-xs">
                                        {{ $a->check_out_lat ?? '-' }}, {{ $a->check_out_lng ?? '-' }}
                                    </div>
                                    @if($a->check_out_lat && $a->check_out_lng)
  <button
    type="button"
    onclick="openMap({{ $a->check_out_lat }}, {{ $a->check_out_lng }})"
    class="text-xs text-yellow-400 hover:underline"
  >
    Lihat Map
  </button>
@endif
                                </td>

                                <td class="py-3 pr-4">
                                    @if($a->check_out_photo_path)
                                        <button type="button"
                                            onclick="openPhoto('{{ route('admin.attendance.photo', ['attendance' => $a->id, 'type' => 'out']) }}')"
                                            class="text-yellow-400 hover:underline text-xs">
                                            Lihat Foto
                                        </button>
                                    @else
                                        <span class="text-xs text-white/50">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-sm text-white/60">Belum ada check-out pada tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div id="photoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
        <div class="w-full max-w-3xl overflow-hidden rounded-2xl border border-white/10 bg-[#0f0f0f]">
            <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                <div class="text-sm font-semibold text-white">Foto Absensi</div>
                <button id="closePhotoModal"
                    class="rounded-lg border border-white/15 bg-white/[0.06] px-3 py-1.5 text-xs text-white/80 hover:bg-white/[0.10]">
                    Tutup
                </button>
            </div>
            <div class="p-4">
                <img id="photoModalImg" src="" alt="Foto Absensi" class="mx-auto max-h-[75vh] w-auto rounded-xl" />
            </div>
        </div>
    </div>

    <div id="mapModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
  <div class="w-full max-w-4xl overflow-hidden rounded-2xl border border-white/10 bg-[#0f0f0f]">
    <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
      <div class="text-sm font-semibold text-white">Lokasi Absensi</div>
      <button id="closeMapModal"
        class="rounded-lg border border-white/15 bg-white/[0.06] px-3 py-1.5 text-xs text-white/80 hover:bg-white/[0.10]">
        Tutup
      </button>
    </div>

    <div class="p-4">
      <div class="overflow-hidden rounded-xl border border-white/10">
        <iframe
          id="mapFrame"
          src=""
          width="100%"
          height="520"
          style="border:0;"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen
        ></iframe>
      </div>
      <div id="mapCoords" class="mt-2 text-xs text-white/60"></div>
    </div>
  </div>
</div>

    <script>
        const modal = document.getElementById('photoModal');
        const img = document.getElementById('photoModalImg');
        const btnClose = document.getElementById('closePhotoModal');

        function openPhoto(url) {
            img.src = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePhoto() {
            img.src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        btnClose.addEventListener('click', closePhoto);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closePhoto();
        });

        // expose global supaya bisa dipanggil dari onclick
        window.openPhoto = openPhoto;
    </script>

    <script>
  // MAP MODAL
  const mapModal = document.getElementById('mapModal');
  const mapFrame = document.getElementById('mapFrame');
  const mapCoords = document.getElementById('mapCoords');
  const closeMapBtn = document.getElementById('closeMapModal');

  function openMap(lat, lng){
    // Embed Google Maps dengan titik koordinat.
    // "q=lat,lng" akan menampilkan marker di titik itu.
    const url = `https://www.google.com/maps?q=${lat},${lng}&z=18&output=embed`;

    mapFrame.src = url;
    mapCoords.textContent = `Koordinat: ${lat}, ${lng}`;

    mapModal.classList.remove('hidden');
    mapModal.classList.add('flex');
  }

  function closeMap(){
    mapFrame.src = '';
    mapModal.classList.add('hidden');
    mapModal.classList.remove('flex');
  }

  closeMapBtn.addEventListener('click', closeMap);
  mapModal.addEventListener('click', (e) => { if(e.target === mapModal) closeMap(); });

  window.openMap = openMap;
</script>
@endsection