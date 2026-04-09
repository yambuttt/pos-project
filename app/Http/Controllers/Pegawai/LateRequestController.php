<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\LateAttendanceRequest;
use App\Services\ShiftResolverService;
use Illuminate\Http\Request;

class LateRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = now($tz);
        $dateStr = $now->toDateString();

        $svc = app(ShiftResolverService::class);
        $shift = $svc->resolveShiftForDate(auth()->user(), $now);

        // aturan: max telat sampai 120 menit dari start shift (contoh shift A 10:00 => 12:00)
        $maxLateMinutes = 120;

        $shiftStart = \Carbon\Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);
        $maxAllowed = $shiftStart->copy()->addMinutes($maxLateMinutes);

        // kalau sudah lewat batas maksimal telat, jangan boleh ajukan (sudah pasti alpha)
        if ($now->gt($maxAllowed)) {
            return response()->json([
                'ok' => false,
                'message' => 'Pengajuan telat sudah ditutup. Kamu sudah lewat batas maksimal telat.',
            ], 422);
        }

        // buat/replace pengajuan untuk hari ini
        LateAttendanceRequest::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $dateStr],
            [
                'reason' => $data['reason'] ?? null,
                'status' => 'pending',
                'allowed_until_time' => null, // diisi admin saat approve
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_note' => null,
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Pengajuan telat berhasil dikirim. Menunggu persetujuan admin.',
        ]);
    }
}