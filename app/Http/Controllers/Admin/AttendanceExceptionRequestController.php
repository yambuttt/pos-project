<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceExceptionRequest;
use App\Models\AttendanceQrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceExceptionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // pending/approved/rejected/all

        $q = AttendanceExceptionRequest::query()
            ->with(['user', 'deviceOwnerUser', 'reviewer'])
            ->latest();

        if ($status !== 'all') {
            $q->where('status', $status);
        }

        $items = $q->paginate(20)->withQueryString();

        return view('dashboard.admin.attendance_qr.exception_requests', compact('items', 'status'));
    }

    public function approve(\App\Models\AttendanceExceptionRequest $req)
    {
        if ($req->status !== 'pending') {
            return back()->with('ok', 'Pengajuan sudah diproses.');
        }

        try {
            \DB::transaction(function () use ($req) {
                $qrId = $req->attendance_qr_token_id;

                if (!$qrId) {
                    throw new \RuntimeException('QR token tidak tersimpan pada pengajuan.');
                }

                // attendance row (1 row per user per date)
                $attendance = \App\Models\Attendance::firstOrCreate(
                    [
                        'user_id' => $req->user_id,
                        'date' => $req->attendance_date->toDateString(),
                    ],
                    [
                        'ip' => null,
                        'device' => $req->user_agent,
                    ]
                );

                if ($req->mode === 'in') {
                    if ($attendance->check_in_at) {
                        throw new \RuntimeException('Pegawai sudah check-in.');
                    }

                    // ✅ jam mengikuti waktu pegawai mengajukan (bukan waktu admin approve)
                    $attendance->check_in_at = $req->created_at;
                    $attendance->check_in_lat = $req->lat;
                    $attendance->check_in_lng = $req->lng;
                    $attendance->check_in_photo_path = $req->photo_path;
                    $attendance->check_in_qr_id = $qrId;
                } else {
                    if (!$attendance->check_in_at) {
                        throw new \RuntimeException('Pegawai belum check-in.');
                    }
                    if ($attendance->check_out_at) {
                        throw new \RuntimeException('Pegawai sudah check-out.');
                    }

                    $attendance->check_out_at = $req->created_at;
                    $attendance->check_out_lat = $req->lat;
                    $attendance->check_out_lng = $req->lng;
                    $attendance->check_out_photo_path = $req->photo_path;
                    $attendance->check_out_qr_id = $qrId;
                }

                // catat device yang dipakai (milik orang lain)
                $attendance->device_hash = $req->device_hash;
                $attendance->device = $req->device_name ?: $req->user_agent;
                $attendance->save();

                // update status request
                $req->update([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);
            });

            return back()->with('ok', 'Pengajuan disetujui ✅');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function reject(Request $request, AttendanceExceptionRequest $req)
    {
        if ($req->status !== 'pending') {
            return back()->with('ok', 'Pengajuan sudah diproses.');
        }

        $data = $request->validate([
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        $req->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $data['review_note'] ?? null,
        ]);

        return back()->with('ok', 'Pengajuan ditolak.');
    }

    public function photo(\App\Models\AttendanceExceptionRequest $req)
    {
        $path = $req->photo_path;

        if (!$path) {
            abort(404);
        }

        // Samakan seperti AttendancePhotoController: stream via Laravel
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path);
    }
}