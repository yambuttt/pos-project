<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CheckoutCorrectionRequest;
use App\Services\ShiftResolverService;
use Illuminate\Http\Request;

class CheckoutCorrectionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $tz = config('app.timezone','Asia/Jakarta');
        $now = now($tz);
        $dateStr = $now->toDateString();

        // pastikan attendance hari ini ada check-in dan belum checkout
        $att = Attendance::query()
            ->where('user_id', auth()->id())
            ->where('date', $dateStr)
            ->first();

        if (!$att || !$att->check_in_at) {
            return response()->json(['ok'=>false,'message'=>'Kamu belum check-in hari ini.'], 422);
        }
        if ($att->check_out_at) {
            return response()->json(['ok'=>false,'message'=>'Kamu sudah checkout. Tidak perlu koreksi.'], 422);
        }

        // boleh ajukan setelah shift selesai (biar tidak spam di awal)
        $svc = app(ShiftResolverService::class);
        $wOut = $svc->getWindow(auth()->user(), 'out', $now);

        // wOut['from'] == end shift (mulai boleh checkout normal)
        if ($now->lt($wOut['from'])) {
            return response()->json(['ok'=>false,'message'=>'Koreksi checkout hanya bisa diajukan setelah jam selesai shift.'], 422);
        }

        // cegah double request jika pending/approved
        $exists = CheckoutCorrectionRequest::query()
            ->where('user_id', auth()->id())
            ->where('date', $dateStr)
            ->whereIn('status', ['pending','approved'])
            ->first();

        if ($exists) {
            return response()->json([
                'ok'=>false,
                'message'=>'Pengajuan koreksi checkout hari ini sudah ada ('.strtoupper($exists->status).').'
            ], 422);
        }

        CheckoutCorrectionRequest::updateOrCreate(
            ['user_id'=>auth()->id(), 'date'=>$dateStr],
            ['reason'=>$data['reason'], 'status'=>'pending', 'reviewed_by'=>null, 'reviewed_at'=>null, 'review_note'=>null]
        );

        return response()->json(['ok'=>true,'message'=>'Pengajuan koreksi checkout terkirim. Menunggu admin.']);
    }
}