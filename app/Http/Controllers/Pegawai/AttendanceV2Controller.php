<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceQrToken;
use App\Models\EmployeeDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceV2Controller extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', auth()->id())->where('date', $today)->first();

        return view('dashboard.pegawai.attendance_v2', compact('attendance'));
    }

    // dipanggil saat page load: cek device hash, kalau belum ada => buat pending
    public function initDevice(Request $request)
    {
        $data = $request->validate([
            'device_hash' => ['required', 'string', 'size:64'],
            'device_name' => ['nullable', 'string', 'max:80'],
        ]);

        $device = EmployeeDevice::firstOrCreate(
            ['user_id' => auth()->id(), 'device_hash' => $data['device_hash']],
            [
                'device_name' => $data['device_name'] ?? null,
                'user_agent' => substr((string) $request->userAgent(), 0, 180),
            ]
        );

        $device->last_seen_at = now();
        $device->save();

        if ($device->revoked_at) {
            return response()->json(['ok' => false, 'status' => 'revoked', 'message' => 'Device kamu dicabut. Hubungi admin.'], 403);
        }

        if (!$device->approved_at) {
            return response()->json(['ok' => false, 'status' => 'pending', 'message' => 'Device belum di-approve admin.'], 403);
        }

        return response()->json(['ok' => true, 'status' => 'approved']);
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

        // 3) VALIDASI QR TOKEN (SIGNED + TTL) - TANPA DB
        $tokenParts = explode('.', $data['qr_token'], 2);
        if (count($tokenParts) !== 2) {
            return response()->json(['ok' => false, 'message' => 'QR tidak valid.'], 422);
        }

        [$b64, $sig] = $tokenParts;

        $json = base64_decode(strtr($b64, '-_', '+/'), true);
        if (!$json) {
            return response()->json(['ok' => false, 'message' => 'QR tidak valid.'], 422);
        }

        $expectedSig = hash_hmac('sha256', $json, config('app.key'));
        if (!hash_equals($expectedSig, $sig)) {
            return response()->json(['ok' => false, 'message' => 'QR tidak valid.'], 422);
        }

        $payload = json_decode($json, true);
        if (!is_array($payload)) {
            return response()->json(['ok' => false, 'message' => 'QR tidak valid.'], 422);
        }

        $modeInToken = $payload['m'] ?? null;
        $slot = isset($payload['s']) ? (int) $payload['s'] : -1;

        if ($modeInToken !== $data['mode']) {
            return response()->json(['ok' => false, 'message' => 'QR salah mode.'], 422);
        }

        $ttl = (int) config('attendance.qr_ttl_seconds', 15);
        $nowSlot = (int) floor(time() / $ttl);

        if (!in_array($slot, [$nowSlot, $nowSlot - 1], true)) {
            return response()->json(['ok' => false, 'message' => 'QR sudah expired.'], 422);
        }

        // anti replay ringan (cache, bukan DB)
        $cacheKey = "att_qr_used:{$data['mode']}:{$data['device_hash']}:{$slot}";
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return response()->json(['ok' => false, 'message' => 'QR sudah digunakan.'], 422);
        }
        \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addMinutes(2));

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

        // 5) simpan selfie (tetap ke disk default kamu)
        $path = $this->storeSelfie($request->file('selfie'), $data['mode'], $today);

        // 6) commit attendance
        $att->device_hash = $data['device_hash'];

        if ($data['mode'] === 'in') {
            $att->check_in_at = now();
            $att->check_in_lat = $data['lat'];
            $att->check_in_lng = $data['lng'];
            $att->check_in_photo_path = $path;
        } else {
            $att->check_out_at = now();
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