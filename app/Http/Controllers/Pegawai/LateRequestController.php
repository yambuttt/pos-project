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
            'requested_until_time' => ['required', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:500'],
            'evidence' => ['required', 'image', 'max:4096'], // wajib
        ]);

        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = now($tz);
        $dateStr = $now->toDateString();

        $svc = app(\App\Services\ShiftResolverService::class);
        $shift = $svc->resolveShiftForDate(auth()->user(), $now);

        $shiftStart = \Carbon\Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);


// ✅ RULE BARU: Pengajuan telat hanya boleh SEBELUM jam mulai shift
        if ($now->gte($shiftStart)) {
            return response()->json([
                'ok' => false,
                'message' => 'Pengajuan telat ditutup karena sudah melewati jam mulai shift.',
            ], 422);
        }

        // ✅ cap maksimal telat: start + 120 menit
        $cap = $shiftStart->copy()->addMinutes(120);

        // ✅ kalau sudah lewat cap, pengajuan ditutup
        if ($now->gt($cap)) {
            return response()->json([
                'ok' => false,
                'message' => 'Pengajuan telat sudah ditutup. Kamu sudah lewat batas maksimal telat.',
            ], 422);
        }

        // requested time dari pegawai
        $requested = \Carbon\Carbon::parse($dateStr . ' ' . $data['requested_until_time'], $tz);

        // tidak boleh minta sebelum shift start
        if ($requested->lt($shiftStart)) {
            return response()->json([
                'ok' => false,
                'message' => 'Jam maksimal tidak boleh sebelum jam mulai shift.',
            ], 422);
        }

        // tidak boleh lebih dari cap
        if ($requested->gt($cap)) {
            return response()->json([
                'ok' => false,
                'message' => 'Maksimal telat hanya sampai ' . $cap->format('H:i') . '.',
            ], 422);
        }

        // simpan bukti (wajib)
        $file = $request->file('evidence');
        $dir = 'public/late_requests/' . auth()->id() . '/' . $dateStr;
        $name = 'evidence_' . now()->format('His') . '_' . \Illuminate\Support\Str::random(6) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($dir, $name, 'local');

        \App\Models\LateAttendanceRequest::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $dateStr],
            [
                'requested_until_time' => $data['requested_until_time'],
                'reason' => $data['reason'] ?? null,
                'evidence_path' => $path,
                'status' => 'pending',
                'allowed_until_time' => null,
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