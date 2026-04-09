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
        $status = $request->query('status','pending'); // pending/approved/rejected/all

        $q = CheckoutCorrectionRequest::query()
            ->with(['user','reviewer'])
            ->latest();

        if ($status !== 'all') $q->where('status', $status);

        $items = $q->paginate(20)->withQueryString();

        return view('dashboard.admin.checkout_corrections.index', compact('items','status'));
    }

    public function approve(CheckoutCorrectionRequest $req)
    {
        if ($req->status !== 'pending') return back()->with('ok','Sudah diproses.');

        // isi checkout = jam selesai shift (end shift)
        $svc = app(ShiftResolverService::class);
        $tz = config('app.timezone','Asia/Jakarta');

        $dateStr = $req->date->toDateString();
        $shift = $svc->resolveShiftForDate($req->user, $req->date);
        $end = \Carbon\Carbon::parse($dateStr.' '.$shift->end_time, $tz);
        $start = \Carbon\Carbon::parse($dateStr.' '.$shift->start_time, $tz);
        if ($end->lte($start)) $end->addDay();

        $att = Attendance::query()
            ->where('user_id', $req->user_id)
            ->where('date', $dateStr)
            ->first();

        if (!$att || !$att->check_in_at) return back()->with('error','Attendance/check-in tidak ditemukan.');
        if ($att->check_out_at) return back()->with('ok','Sudah checkout, tidak perlu koreksi.');

        $att->update([
            'check_out_at' => $end, // jam selesai shift
        ]);

        $req->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('ok','Koreksi checkout disetujui ✅ Checkout diisi jam selesai shift.');
    }

    public function reject(Request $request, CheckoutCorrectionRequest $req)
    {
        if ($req->status !== 'pending') return back()->with('ok','Sudah diproses.');

        $data = $request->validate([
            'review_note' => ['nullable','string','max:500'],
        ]);

        $req->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $data['review_note'] ?? null,
        ]);

        return back()->with('ok','Koreksi checkout ditolak. Status hari itu akan dihitung ALPHA.');
    }
}