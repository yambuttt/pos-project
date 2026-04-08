<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // pending/approved/rejected/all

        $q = LeaveRequest::query()
            ->with(['user', 'reviewer'])
            ->latest();

        if ($status !== 'all') {
            $q->where('status', $status);
        }

        $items = $q->paginate(15)->withQueryString();

        return view('dashboard.admin.leave_requests.index', compact('items', 'status'));
    }

    public function approve(LeaveRequest $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('ok', 'Pengajuan sudah diproses.');
        }

        $leave->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('ok', 'Pengajuan disetujui ✅');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('ok', 'Pengajuan sudah diproses.');
        }

        $data = $request->validate([
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        $leave->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $data['review_note'] ?? null,
        ]);

        return back()->with('ok', 'Pengajuan ditolak.');
    }

    public function doctorNote(LeaveRequest $leave)
    {
        if (!$leave->doctor_note_path) abort(404);

        $disk = Storage::disk('local');
        if (!$disk->exists($leave->doctor_note_path)) abort(404);

        return $disk->response($leave->doctor_note_path);
    }
}