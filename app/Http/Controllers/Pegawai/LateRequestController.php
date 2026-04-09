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
            'evidence' => ['required', 'image', 'max:4096'],
        ]);

        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = now($tz);
        $dateStr = $now->toDateString();

        $svc = app(\App\Services\ShiftResolverService::class);
        $shift = $svc->resolveShiftForDate(auth()->user(), $now);

        // batas maksimal telat: 120 menit dari shift start (mis shift A 10:00 -> max 12:00)
        $maxLateMinutes = 120;

        $shiftStart = \Carbon\Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);
        $cap = $shiftStart->copy()->addMinutes($maxLateMinutes);

        // requested time dari pegawai
        $requested = \Carbon\Carbon::parse($dateStr . ' ' . $data['requested_until_time'], $tz);

        // tidak boleh minta sebelum start shift
        if ($requested->lt($shiftStart)) {
            return response()->json(['ok' => false, 'message' => 'Jam maksimal tidak boleh sebelum jam mulai shift.'], 422);
        }

        // tidak boleh melebihi cap (12:00)
        if ($requested->gt($cap)) {
            return response()->json(['ok' => false, 'message' => 'Maksimal telat hanya sampai ' . $cap->format('H:i') . '.'], 422);
        }

        // kalau sudah lewat cap, pengajuan ditutup
        if ($now->gt($cap)) {
            return response()->json(['ok' => false, 'message' => 'Pengajuan telat sudah ditutup. Kamu sudah lewat batas maksimal telat.'], 422);
        }

        $path = null;
        if ($request->hasFile('evidence')) {
            // simpan ke disk local (aman, bisa distream via controller admin jika perlu)
            $file = $request->file('evidence');
            $dir = 'public/late_requests/' . auth()->id() . '/' . $dateStr;
            $name = 'evidence_' . now()->format('His') . '_' . \Illuminate\Support\Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($dir, $name, 'local');
        }

        \App\Models\LateAttendanceRequest::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $dateStr],
            [
                'requested_until_time' => $data['requested_until_time'],
                'reason' => $data['reason'] ?? null,
                'evidence_path' => $path,
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