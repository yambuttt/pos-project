<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\FaceProfile;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // threshold makin kecil makin ketat (umumnya 0.45 - 0.60 tergantung kondisi)
    private float $threshold = 0.40;

    public function index()
    {
        $profile = FaceProfile::where('user_id', auth()->id())->first();

        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('date', $today)
            ->first();

        return view('dashboard.pegawai.attendance', compact('profile', 'attendance'));
    }

    public function checkIn(Request $request)
    {
        return $this->handleAttendance($request, 'in');
    }

    public function checkOut(Request $request)
    {
        return $this->handleAttendance($request, 'out');
    }

    private function handleAttendance(Request $request, string $mode)
    {
        $data = $request->validate([
            'descriptor' => ['required', 'array'],
        ]);

        $profile = FaceProfile::where('user_id', auth()->id())->first();
        if (!$profile) {
            return response()->json(['ok' => false, 'message' => 'Wajah belum didaftarkan.'], 422);
        }

        $live = array_map('floatval', $data['descriptor']);
        $best = $this->bestDistance($live, $profile->descriptors ?? []);

        if ($best === null) {
            return response()->json(['ok' => false, 'message' => 'Data wajah tidak valid.'], 422);
        }

        if ($best > $this->threshold) {
            return response()->json([
                'ok' => false,
                'message' => 'Verifikasi gagal. Coba pencahayaan lebih terang & posisikan wajah lurus.',
                'distance' => $best
            ], 422);
        }

        $today = now()->toDateString();
        $att = Attendance::firstOrCreate(
            ['user_id' => auth()->id(), 'date' => $today],
            ['ip' => $request->ip(), 'device' => substr((string)$request->userAgent(), 0, 180)]
        );

        if ($mode === 'in') {
            if ($att->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-in hari ini.'], 422);
            }
            $att->check_in_at = now();
        } else {
            if (!$att->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu belum check-in.'], 422);
            }
            if ($att->check_out_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-out hari ini.'], 422);
            }
            $att->check_out_at = now();
        }

        $att->match_distance = $best;
        $att->save();

        return response()->json(['ok' => true, 'distance' => $best]);
    }

    private function bestDistance(array $live, array $storedDescriptors): ?float
    {
        if (count($live) < 32) return null; // minimal sanity

        $best = null;
        foreach ($storedDescriptors as $d) {
            if (!is_array($d) || count($d) !== count($live)) continue;
            $dist = $this->euclidean($live, array_map('floatval', $d));
            $best = $best === null ? $dist : min($best, $dist);
        }
        return $best;
    }

    private function euclidean(array $a, array $b): float
    {
        $sum = 0.0;
        $n = min(count($a), count($b));
        for ($i=0; $i<$n; $i++) {
            $diff = $a[$i] - $b[$i];
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }
}