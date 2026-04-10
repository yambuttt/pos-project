<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CheckoutCorrectionRequest;
use App\Services\ShiftResolverService;
use Illuminate\Http\Request;

class CheckoutCorrectionAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // pending/approved/rejected/all

        $q = CheckoutCorrectionRequest::query()
            ->with(['user', 'reviewer'])
            ->latest();

        if ($status !== 'all')
            $q->where('status', $status);

        $items = $q->paginate(20)->withQueryString();

        return view('dashboard.admin.checkout_corrections.index', compact('items', 'status'));
    }

    public function approve(\App\Models\CheckoutCorrectionRequest $req)
    {
        if ($req->status !== 'pending') {
            return back()->with('ok', 'Sudah diproses.');
        }

        $tz = config('app.timezone', 'Asia/Jakarta');
        $svc = app(\App\Services\ShiftResolverService::class);

        return \DB::transaction(function () use ($req, $svc, $tz) {
            $dateStr = $req->date->toDateString();

            // Cari attendance hari itu
            $att = \App\Models\Attendance::query()
                ->where('user_id', $req->user_id)
                ->where('date', $dateStr)
                ->first();

            if (!$att || !$att->check_in_at) {
                return back()->with('error', 'Attendance/check-in tidak ditemukan untuk tanggal ' . $dateStr);
            }

            if ($att->check_out_at) {
                // kalau sudah checkout, anggap selesai
                $req->forceFill([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ])->save();

                return back()->with('ok', 'Sudah ada checkout, koreksi ditandai approved.');
            }

            // Hitung jam selesai shift (end)
            $shift = $svc->resolveShiftForDate($req->user, $req->date);

            $shiftStart = \Carbon\Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);
            $shiftEnd = \Carbon\Carbon::parse($dateStr . ' ' . $shift->end_time, $tz);

            // kalau end melewati midnight
            if ($shiftEnd->lte($shiftStart))
                $shiftEnd->addDay();

            // ✅ PAKSA simpan (agar tidak kena fillable/guarded)
            $att->forceFill([
                'check_out_at' => $shiftEnd,
            ])->save();

            // Tandai request approved setelah attendance sukses tersimpan
            $req->forceFill([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ])->save();

            return back()->with('ok', 'Koreksi checkout disetujui ✅ Checkout diisi jam selesai shift.');
        });
    }

    public function reject(Request $request, CheckoutCorrectionRequest $req)
    {
        if ($req->status !== 'pending')
            return back()->with('ok', 'Sudah diproses.');

        $data = $request->validate([
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        $req->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $data['review_note'] ?? null,
        ]);

        return back()->with('ok', 'Koreksi checkout ditolak. Status hari itu akan dihitung ALPHA.');
    }
}