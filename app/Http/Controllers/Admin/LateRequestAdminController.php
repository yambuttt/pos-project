<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LateAttendanceRequest;
use App\Services\ShiftResolverService;
use Illuminate\Http\Request;

class LateRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // pending/approved/rejected/all

        $q = LateAttendanceRequest::query()
            ->with(['user', 'reviewer'])
            ->latest();

        if ($status !== 'all') $q->where('status', $status);

        $items = $q->paginate(20)->withQueryString();

        return view('dashboard.admin.late_requests.index', compact('items', 'status'));
    }

    public function approve(Request $request, LateAttendanceRequest $req)
    {
        if ($req->status !== 'pending') return back()->with('ok', 'Pengajuan sudah diproses.');

        // default: approve sampai shiftStart + 120 menit (contoh jam 12:00)
        $tz = config('app.timezone', 'Asia/Jakarta');
        $svc = app(ShiftResolverService::class);
        $shift = $svc->resolveShiftForDate($req->user, $req->date);

        $dateStr = $req->date->toDateString();
        $shiftStart = \Carbon\Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);

        $maxLateMinutes = 120;
        $allowedUntil = $shiftStart->copy()->addMinutes($maxLateMinutes)->format('H:i:s');

        $req->update([
            'status' => 'approved',
            'allowed_until_time' => $allowedUntil,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('ok', 'Pengajuan telat disetujui ✅ (maks check-in sampai ' . substr($allowedUntil,0,5) . ')');
    }

    public function reject(Request $request, LateAttendanceRequest $req)
    {
        if ($req->status !== 'pending') return back()->with('ok', 'Pengajuan sudah diproses.');

        $data = $request->validate([
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        $req->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $data['review_note'] ?? null,
        ]);

        return back()->with('ok', 'Pengajuan telat ditolak.');
    }
}