<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceQrToken;
use App\Models\EmployeeDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceV2Controller extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', auth()->id())->where('date', $today)->first();
        $shiftSvc = app(\App\Services\ShiftResolverService::class);
        $winIn = $shiftSvc->getWindow(auth()->user(), 'in', now());
        $winOut = $shiftSvc->getWindow(auth()->user(), 'out', now());

        return view('dashboard.pegawai.attendance_v2', compact('attendance', 'winIn', 'winOut'));
    }

    // dipanggil saat page load: cek device hash, kalau belum ada => buat pending
    public function initDevice(Request $request)
    {
        $data = $request->validate([
            'device_hash' => ['required', 'string', 'size:64'],
            'device_name' => ['nullable', 'string', 'max:80'],
            // TAMBAH: lokasi
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        $device = EmployeeDevice::firstOrCreate(
            ['user_id' => auth()->id(), 'device_hash' => $data['device_hash']],
            [
                'device_name' => $data['device_name'] ?? null,
                'user_agent' => substr((string) $request->userAgent(), 0, 180),
            ]
        );

        // UPDATE tiap init (biar tidak nyangkut "Web" selamanya)
        $incomingName = trim((string) ($data['device_name'] ?? ''));
        if ($incomingName !== '') {
            $device->device_name = $incomingName;
        }

        $ua = substr((string) $request->userAgent(), 0, 180);
        if ($ua !== '') {
            $device->user_agent = $ua;
        }

        $device->last_seen_at = now();
        $device->save();

        if ($device->revoked_at) {
            return response()->json([
                'ok' => false,
                'status' => 'revoked',
                'message' => 'Device kamu dicabut. Hubungi admin.'
            ], 403);
        }

        if (!$device->approved_at) {
            return response()->json([
                'ok' => false,
                'status' => 'pending',
                'message' => 'Device belum di-approve admin.'
            ], 403);
        }

        // TAMBAH: geofence di step verifikasi
        if (!$this->isWithinRestaurant((float) $data['lat'], (float) $data['lng'])) {
            return response()->json([
                'ok' => false,
                'status' => 'out_of_area',
                'message' => 'Lokasi di luar area restoran.'
            ], 403);
        }

        return response()->json(['ok' => true, 'status' => 'approved']);
    }

    public function lookupDeviceOwner(Request $request)
    {
        $data = $request->validate([
            'device_hash' => ['required', 'string', 'size:64'],
            'device_name' => ['nullable', 'string', 'max:80'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        // geofence tetap wajib
        if (!$this->isWithinRestaurant((float) $data['lat'], (float) $data['lng'])) {
            return response()->json([
                'ok' => false,
                'status' => 'out_of_area',
                'message' => 'Lokasi di luar area restoran.',
            ], 403);
        }

        $device = \App\Models\EmployeeDevice::with('user')
            ->where('device_hash', $data['device_hash'])
            ->whereNull('revoked_at')
            ->first();

        if (!$device) {
            return response()->json([
                'ok' => false,
                'status' => 'not_registered',
                'message' => 'Device ini tidak terdaftar sebagai device absensi manapun.',
            ], 422);
        }

        if ((int) $device->user_id === (int) auth()->id()) {
            return response()->json([
                'ok' => false,
                'status' => 'same_user',
                'message' => 'Device ini milik kamu. Gunakan Absensi Normal.',
            ], 422);
        }

        // kalau device owner belum approved, jangan boleh dipakai untuk darurat (biar tidak jadi celah)
        if (!$device->approved_at) {
            return response()->json([
                'ok' => false,
                'status' => 'owner_device_not_approved',
                'message' => 'Device ini belum di-approve admin (milik pegawai lain). Tidak bisa digunakan.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'status' => 'ok',
            'owner' => [
                'user_id' => $device->user_id,
                'name' => $device->user?->name,
                'email' => $device->user?->email,
                'device_name' => $device->device_name,
            ],
        ]);
    }

    public function submitException(Request $request)
    {
        $data = $request->validate([
            'mode' => ['required', 'in:in,out'],
            'qr_token' => ['required', 'string'],
            'device_hash' => ['required', 'string', 'size:64'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'selfie' => ['required', 'image', 'max:4096'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);
        try {
            app(\App\Services\ShiftResolverService::class)->enforceWindow(auth()->user(), $data['mode'], now());
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        // 1) geofence wajib
        if (!$this->isWithinRestaurant((float) $data['lat'], (float) $data['lng'])) {
            return response()->json(['ok' => false, 'message' => 'Lokasi di luar area restoran.'], 403);
        }

        // 2) device owner harus ada & bukan user ini
        $ownerDevice = \App\Models\EmployeeDevice::with('user')
            ->where('device_hash', $data['device_hash'])
            ->whereNull('revoked_at')
            ->first();

        if (!$ownerDevice) {
            return response()->json(['ok' => false, 'message' => 'Device ini tidak terdaftar.'], 422);
        }

        if ((int) $ownerDevice->user_id === (int) auth()->id()) {
            return response()->json(['ok' => false, 'message' => 'Device ini milik kamu. Gunakan Absensi Normal.'], 422);
        }

        // Owner device harus sudah approved (biar tidak jadi celah)
        if (!$ownerDevice->approved_at) {
            return response()->json(['ok' => false, 'message' => 'Device pemilik belum di-approve admin.'], 422);
        }

        $today = now()->toDateString();

        // 3) aturan check-in/out mengikuti attendance existing
        $att = \App\Models\Attendance::where('user_id', auth()->id())
            ->where('date', $today)
            ->first();

        if ($data['mode'] === 'in') {
            if ($att?->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-in hari ini.'], 422);
            }
        } else {
            if (!$att?->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu belum check-in.'], 422);
            }
            if ($att?->check_out_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-out hari ini.'], 422);
            }
        }

        // 4) cegah spam pengajuan pending
        $pendingExists = \App\Models\AttendanceExceptionRequest::query()
            ->where('user_id', auth()->id())
            ->where('attendance_date', $today)
            ->where('mode', $data['mode'])
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return response()->json(['ok' => false, 'message' => 'Pengajuan masih pending. Tunggu persetujuan admin.'], 422);
        }

        // 5) VALIDASI QR + LOCK & CONSUME SAAT SUBMIT (ini kunci agar admin bisa approve kapan saja)
        $qrId = null;

        try {
            DB::transaction(function () use ($data, &$qrId) {
                $qr = \App\Models\AttendanceQrToken::where('token', $data['qr_token'])
                    ->lockForUpdate()
                    ->first();

                if (!$qr) {
                    throw new \RuntimeException('QR tidak valid.');
                }
                if ($qr->mode !== $data['mode']) {
                    throw new \RuntimeException('QR salah mode.');
                }
                if (!$qr->expires_at || now()->gte($qr->expires_at)) {
                    throw new \RuntimeException('QR token sudah expired.');
                }
                if ($qr->used_at) {
                    throw new \RuntimeException('QR sudah digunakan.');
                }

                // ✅ consume SEKARANG (bukan saat admin approve)
                $qr->used_at = now();
                $qr->used_by = auth()->id();
                $qr->save();

                $qrId = $qr->id;
            });
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        // 6) simpan selfie + watermark (pakai timestamp konsisten)
        $now = now();

        $path = $this->storeSelfieWithWatermark(
            $request->file('selfie'),
            $data['mode'],
            $today,
            (float) $data['lat'],
            (float) $data['lng'],
            $now
        );

        // 7) create pengajuan pending
        \App\Models\AttendanceExceptionRequest::create([
            'user_id' => auth()->id(),
            'attendance_date' => $today,
            'mode' => $data['mode'],

            'device_hash' => $data['device_hash'],
            'device_owner_device_id' => $ownerDevice->id,
            'device_owner_user_id' => $ownerDevice->user_id,
            'device_name' => $ownerDevice->device_name,
            'user_agent' => substr((string) $request->userAgent(), 0, 180),

            'lat' => $data['lat'],
            'lng' => $data['lng'],

            'attendance_qr_token_id' => $qrId,
            'photo_path' => $path,

            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Pengajuan absensi via device pegawai lain berhasil dikirim dan menunggu persetujuan admin.',
            'device_owner' => $ownerDevice->user?->name,
        ]);
    }
    private function storeSelfieWithWatermark(
        UploadedFile $file,
        string $mode,
        string $date,
        float $lat,
        float $lng,
        Carbon $takenAt
    ): string {
        $userId = auth()->id();
        $name = $mode === 'in' ? 'checkin' : 'checkout';

        // kita paksa jpg biar konsisten
        $filename = $name . '_' . $takenAt->format('His') . '.jpg';

        // simpan dulu
        $path = $file->storeAs("public/attendances/{$userId}/{$date}", $filename);

        // lalu tempel watermark (overwrite file yang sama)
        $employeeName = (string) (auth()->user()->name ?? 'Unknown');
        $this->applyWatermarkToStoredImage($path, $employeeName, $mode, $takenAt, $lat, $lng);

        return $path;
    }

    private function applyWatermarkToStoredImage(
        string $path,
        string $employeeName,
        string $mode,
        \Carbon\Carbon $takenAt,
        float $lat,
        float $lng
    ): void {
        $disk = \Illuminate\Support\Facades\Storage::disk('local');

        if (!$disk->exists($path)) {
            return;
        }

        $abs = $disk->path($path);
        $raw = @file_get_contents($abs);
        if ($raw === false)
            return;

        $img = @imagecreatefromstring($raw);
        if (!$img)
            return;

        $w = imagesx($img);
        $h = imagesy($img);

        // =========================
        // 1) Siapkan teks watermark
        // =========================
        $lines = [];
        $lines[] = 'Nama: ' . $employeeName;
        $lines[] = 'Mode: ' . strtoupper($mode) . '  •  ' . $takenAt->format('Y-m-d H:i:s');
        $lines[] = 'Lokasi: ' . number_format($lat, 6, '.', '') . ', ' . number_format($lng, 6, '.', '');
        $lines[] = 'Maps: https://www.google.com/maps?q=' . $lat . ',' . $lng;

        // font bawaan GD
        $font = 3; // 1..5
        $charH = imagefontheight($font);

        $padding = max(14, (int) round(min($w, $h) * 0.02));
        $lineGap = 6;

        // =========================
        // 2) Load logo (PNG) dari public/
        // =========================
        $logoPath = public_path('images/landing/logo-ayo-renne.png');
        $logo = null;

        if (is_file($logoPath)) {
            $logo = @imagecreatefrompng($logoPath);
            if ($logo) {
                imagealphablending($logo, true);
                imagesavealpha($logo, true);
            }
        }

        // ukuran logo (maks 18% lebar foto, atau max 120px)
        $logoW = 0;
        $logoH = 0;
        $logoMarginRight = 0;

        if ($logo) {
            $srcW = imagesx($logo);
            $srcH = imagesy($logo);

            $maxW = min((int) round($w * 0.18), 120);
            $scale = $maxW / max(1, $srcW);

            $logoW = max(1, (int) round($srcW * $scale));
            $logoH = max(1, (int) round($srcH * $scale));

            $logoMarginRight = $padding; // jarak logo ke teks
        }

        // =========================
        // 3) Hitung tinggi box watermark
        // =========================
        $textBlockH = (count($lines) * $charH) + ((count($lines) - 1) * $lineGap);
        $contentH = max($textBlockH, $logoH); // box harus muat logo atau teks
        $boxH = $padding + $contentH + $padding;

        $y1 = max(0, $h - $boxH);
        $y2 = $h;

        // warna
        imagealphablending($img, true);
        imagesavealpha($img, true);

        $bg = imagecolorallocatealpha($img, 0, 0, 0, 60); // hitam transparan
        $white = imagecolorallocatealpha($img, 255, 255, 255, 0);

        // background rectangle
        imagefilledrectangle($img, 0, $y1, $w, $y2, $bg);

        // =========================
        // 4) Tempel logo di kanan bawah dalam box
        // =========================
        $textX = $padding;

        if ($logo && $logoW > 0 && $logoH > 0) {
            // posisi logo: kanan bawah (di dalam box)
            $dstX = $w - $padding - $logoW;
            $dstY = $y1 + (int) round(($boxH - $logoH) / 2);

            // copy resample
            imagecopyresampled(
                $img,
                $logo,
                $dstX,
                $dstY,
                0,
                0,
                $logoW,
                $logoH,
                imagesx($logo),
                imagesy($logo)
            );

            // batasi area teks supaya tidak nabrak logo
            $maxTextWidth = $dstX - $logoMarginRight;
            // kalau terlalu sempit, geser teks sedikit ke kiri tetap aja; (font GD susah wrap rapi)
            // minimal kita pastikan start X aman:
            $textX = $padding;
        }

        // =========================
        // 5) Tulis teks watermark
        // =========================
        $y = $y1 + $padding + (int) round((max($logoH, $textBlockH) - $textBlockH) / 2);

        foreach ($lines as $t) {
            imagestring($img, $font, $textX, $y, $t, $white);
            $y += $charH + $lineGap;
        }

        if ($logo) {
            imagedestroy($logo);
        }

        // =========================
        // 6) Simpan ulang (overwrite)
        // =========================
        @imagejpeg($img, $abs, 88);
        imagedestroy($img);
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'mode' => ['required', 'in:in,out'],
            'qr_token' => ['required', 'string'],
            'device_hash' => ['required', 'string', 'size:64'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'selfie' => ['required', 'image', 'max:4096'],
        ]);

        try {
            app(\App\Services\ShiftResolverService::class)->enforceWindow(auth()->user(), $data['mode'], now());
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        // 1) device harus approved
        $device = \App\Models\EmployeeDevice::where('user_id', auth()->id())
            ->where('device_hash', $data['device_hash'])
            ->whereNull('revoked_at')
            ->first();

        if (!$device || !$device->approved_at) {
            return response()->json(['ok' => false, 'message' => 'Device belum terverifikasi admin.'], 403);
        }

        // 2) geofence
        if (!$this->isWithinRestaurant((float) $data['lat'], (float) $data['lng'])) {
            return response()->json(['ok' => false, 'message' => 'Lokasi di luar area restoran.'], 403);
        }

        // 3) VALIDASI QR TOKEN (DB + single-use)
        try {
            \DB::transaction(function () use ($data) {
                $qr = \App\Models\AttendanceQrToken::where('token', $data['qr_token'])
                    ->lockForUpdate()
                    ->first();

                if (!$qr) {
                    throw new \RuntimeException('QR tidak valid.');
                }

                if ($qr->mode !== $data['mode']) {
                    throw new \RuntimeException('QR salah mode.');
                }

                if (!$qr->expires_at || now()->gte($qr->expires_at)) {
                    throw new \RuntimeException('QR sudah expired.');
                }

                if ($qr->used_at) {
                    throw new \RuntimeException('QR sudah digunakan.');
                }

                // tandai used (single-use global)
                $qr->used_at = now();
                $qr->used_by = auth()->id();
                $qr->save();
            });
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        // 4) ambil attendance hari ini (1 row per user per date)
        $today = now()->toDateString();
        $att = \App\Models\Attendance::firstOrCreate(
            ['user_id' => auth()->id(), 'date' => $today],
            ['ip' => $request->ip(), 'device' => substr((string) $request->userAgent(), 0, 180)]
        );

        // aturan check-in/out
        if ($data['mode'] === 'in') {
            if ($att->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-in hari ini.'], 422);
            }
        } else {
            if (!$att->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu belum check-in.'], 422);
            }
            if ($att->check_out_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-out hari ini.'], 422);
            }
        }

        // 5) simpan selfie + watermark otomatis
        $now = now(); // pakai 1 timestamp yang konsisten

        $path = $this->storeSelfieWithWatermark(
            $request->file('selfie'),
            $data['mode'],
            $today,
            (float) $data['lat'],
            (float) $data['lng'],
            $now
        );

        // 6) commit attendance
        $att->device_hash = $data['device_hash'];

        if ($data['mode'] === 'in') {
            $att->check_in_at = $now;
            $att->check_in_lat = $data['lat'];
            $att->check_in_lng = $data['lng'];
            $att->check_in_photo_path = $path;
        } else {
            $att->check_out_at = $now;
            $att->check_out_lat = $data['lat'];
            $att->check_out_lng = $data['lng'];
            $att->check_out_photo_path = $path;
        }

        $att->save();

        return response()->json(['ok' => true]);
    }
    private function storeSelfie($file, string $mode, string $date): string
    {
        $userId = auth()->id();
        $name = $mode === 'in' ? 'checkin' : 'checkout';
        $filename = $name . '_' . now()->format('His') . '.jpg';

        return $file->storeAs("public/attendances/{$userId}/{$date}", $filename);
    }

    private function isWithinRestaurant(float $lat, float $lng): bool
    {
        $rLat = (float) config('attendance.restaurant_lat');
        $rLng = (float) config('attendance.restaurant_lng');
        $radius = (int) config('attendance.radius_m', 120);

        if ($rLat == 0.0 && $rLng == 0.0)
            return true; // fallback kalau env belum di-set

        $d = $this->haversineMeters($lat, $lng, $rLat, $rLng);
        return $d <= $radius;
    }

    private function haversineMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371000;
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $dPhi = deg2rad($lat2 - $lat1);
        $dLam = deg2rad($lon2 - $lon1);

        $a = sin($dPhi / 2) * sin($dPhi / 2) +
            cos($phi1) * cos($phi2) *
            sin($dLam / 2) * sin($dLam / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }
}