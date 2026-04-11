<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\OvertimeRequest;
use App\Services\ShiftResolverService;
use Illuminate\Http\Request;

class OvertimeRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'minutes' => ['required', 'integer', 'in:60,120,180'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = now($tz);
        $dateStr = $now->toDateString();

        // harus sudah check-in dan belum checkout
        $att = Attendance::query()
            ->where('user_id', auth()->id())
            ->whereDate('date', $dateStr)
            ->first();

        if (!$att || !$att->check_in_at) {
            return response()->json(['ok'=>false,'message'=>'Kamu harus check-in dulu sebelum ajukan lembur.'], 422);
        }
        if ($att->check_out_at) {
            return response()->json(['ok'=>false,'message'=>'Kamu sudah checkout. Tidak bisa ajukan lembur.'], 422);
        }

        // cegah dobel request pending/approved
        $exists = OvertimeRequest::query()
            ->where('user_id', auth()->id())
            ->whereDate('date', $dateStr)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($exists) {
            return response()->json([
                'ok'=>false,
                'message'=>'Pengajuan lembur hari ini sudah ada ('.strtoupper($exists->status).').'
            ], 422);
        }

        // optional: boleh ajukan kapan saja setelah check-in (sesuai jawabanmu)
        OvertimeRequest::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $dateStr],
            [
                'requested_minutes' => (int)$data['minutes'],
                'approved_minutes' => null,
                'reason' => $data['reason'] ?? null,
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_note' => null,
            ]
        );

        return response()->json(['ok'=>true,'message'=>'Pengajuan lembur terkirim. Menunggu persetujuan admin.']);
    }
}