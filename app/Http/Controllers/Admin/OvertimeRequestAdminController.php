<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OvertimeRequest;
use App\Services\ShiftResolverService;
use Illuminate\Http\Request;

class OvertimeRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // pending/approved/rejected/all

        $q = OvertimeRequest::query()
            ->with(['user','reviewer'])
            ->latest();

        if ($status !== 'all') $q->where('status', $status);

        $items = $q->paginate(20)->withQueryString();

        return view('dashboard.admin.overtime_requests.index', compact('items','status'));
    }

    public function approve(OvertimeRequest $req)
    {
        if ($req->status !== 'pending') return back()->with('ok', 'Sudah diproses.');

        $tz = config('app.timezone','Asia/Jakarta');
        $now = now($tz);

        $svc = app(ShiftResolverService::class);
        $shift = $svc->resolveShiftForDate($req->user, $req->date);

        $dateStr = $req->date->toDateString();
        $shiftStart = \Carbon\Carbon::parse($dateStr.' '.$shift->start_time, $tz);
        $shiftEnd   = \Carbon\Carbon::parse($dateStr.' '.$shift->end_time, $tz);
        if ($shiftEnd->lte($shiftStart)) $shiftEnd->addDay();

        // ✅ RULE kamu: admin harus approve sebelum jam selesai shift
        if ($now->gte($shiftEnd)) {
            return back()->with('error', 'Gagal approve: sudah melewati jam selesai shift.');
        }

        $mins = (int) $req->requested_minutes;
        if (!in_array($mins, [60,120,180], true)) $mins = 60;

        $req->update([
            'status' => 'approved',
            'approved_minutes' => $mins,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('ok', "Lembur disetujui ✅ ({$mins} menit)");
    }

    public function reject(Request $request, OvertimeRequest $req)
    {
        if ($req->status !== 'pending') return back()->with('ok', 'Sudah diproses.');

        $data = $request->validate([
            'review_note' => ['nullable','string','max:500'],
        ]);

        $req->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $data['review_note'] ?? null,
        ]);

        return back()->with('ok', 'Pengajuan lembur ditolak.');
    }
}