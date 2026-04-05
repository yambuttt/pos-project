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
            'device_hash' => ['required','string','size:64'],
            'device_name' => ['nullable','string','max:80'],
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
            'mode' => ['required','in:in,out'],
            'qr_token' => ['required','string','size:64'],
            'device_hash' => ['required','string','size:64'],
            'lat' => ['required','numeric'],
            'lng' => ['required','numeric'],
            'selfie' => ['required','image','max:4096'], // 4MB
        ]);

        // 1) device harus approved
        $device = EmployeeDevice::where('user_id', auth()->id())
            ->where('device_hash', $data['device_hash'])
            ->whereNull('revoked_at')
            ->first();

        if (!$device || !$device->approved_at) {
            return response()->json(['ok' => false, 'message' => 'Device belum terverifikasi admin.'], 403);
        }

        // 2) geofence
        $okGeo = $this->isWithinRestaurant((float)$data['lat'], (float)$data['lng']);
        if (!$okGeo) {
            return response()->json(['ok' => false, 'message' => 'Lokasi di luar area restoran.'], 403);
        }

        // 3) qr token valid + mode cocok + belum dipakai + belum expired
        $qr = AttendanceQrToken::where('token', $data['qr_token'])->first();
        if (!$qr || !$qr->isValid() || $qr->mode !== $data['mode']) {
            return response()->json(['ok' => false, 'message' => 'QR tidak valid / sudah expired / salah mode.'], 422);
        }

        $today = now()->toDateString();
        $att = Attendance::firstOrCreate(
            ['user_id' => auth()->id(), 'date' => $today],
            ['ip' => $request->ip(), 'device' => substr((string) $request->userAgent(), 0, 180)]
        );

        // 4) aturan check-in/out (mirip controller lama, tapi tanpa face)
        if ($data['mode'] === 'in') {
            if ($att->check_in_at) return response()->json(['ok'=>false,'message'=>'Kamu sudah check-in hari ini.'], 422);
        } else {
            if (!$att->check_in_at) return response()->json(['ok'=>false,'message'=>'Kamu belum check-in.'], 422);
            if ($att->check_out_at) return response()->json(['ok'=>false,'message'=>'Kamu sudah check-out hari ini.'], 422);
        }

        // 5) simpan selfie
        $path = $this->storeSelfie($request->file('selfie'), $data['mode'], $today);

        // 6) commit attendance
        $att->device_hash = $data['device_hash'];

        if ($data['mode'] === 'in') {
            $att->check_in_at = now();
            $att->check_in_lat = $data['lat'];
            $att->check_in_lng = $data['lng'];
            $att->check_in_photo_path = $path;
            $att->check_in_qr_id = $qr->id;
        } else {
            $att->check_out_at = now();
            $att->check_out_lat = $data['lat'];
            $att->check_out_lng = $data['lng'];
            $att->check_out_photo_path = $path;
            $att->check_out_qr_id = $qr->id;
        }

        $att->save();

        // 7) mark qr as used (single-use)
        $qr->used_at = now();
        $qr->used_by = auth()->id();
        $qr->save();

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

        if ($rLat == 0.0 && $rLng == 0.0) return true; // fallback kalau env belum di-set

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

        $a = sin($dPhi/2) * sin($dPhi/2) +
             cos($phi1) * cos($phi2) *
             sin($dLam/2) * sin($dLam/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}