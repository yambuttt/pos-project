<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\FaceProfile;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{

    private float $threshold = 0.45;

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
            // kirim 3 descriptor dari client
            'descriptors' => ['required', 'array', 'min:2'],
            'descriptors.*' => ['array'],
        ]);

        $profile = FaceProfile::where('user_id', auth()->id())->first();
        if (!$profile) {
            return response()->json(['ok' => false, 'message' => 'Wajah belum didaftarkan.'], 422);
        }

        $bests = [];

        foreach ($data['descriptors'] as $desc) {
            $live = array_map('floatval', $desc);
            $d = $this->bestDistance($live, $profile->descriptors ?? []);
            if ($d !== null)
                $bests[] = $d;
        }

        if (count($bests) < 2) {
            return response()->json(['ok' => false, 'message' => 'Data wajah tidak valid.'], 422);
        }

        // pakai median biar tahan noise (1 frame jelek tidak langsung menang)
        sort($bests);
        $best = $bests[(int) floor(count($bests) / 2)];

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
            ['ip' => $request->ip(), 'device' => substr((string) $request->userAgent(), 0, 180)]
        );

        if ($mode === 'in') {
            if ($att->check_in_at) {
                // sudah check-in hari ini
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
        // descriptor face-api biasanya 128 dimensi — wajib valid
        if (count($live) !== 128)
            return null;

        $best = null;

        foreach ($storedDescriptors as $d) {
            if (!is_array($d) || count($d) !== 128)
                continue;
            $dist = $this->euclidean($live, array_map('floatval', $d));
            $best = $best === null ? $dist : min($best, $dist);
        }

        return $best;
    }

    private function euclidean(array $a, array $b): float
    {
        $sum = 0.0;
        $n = min(count($a), count($b));
        for ($i = 0; $i < $n; $i++) {
            $diff = $a[$i] - $b[$i];
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }
}