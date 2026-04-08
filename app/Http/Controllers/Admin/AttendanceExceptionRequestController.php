<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceExceptionRequest;
use App\Models\AttendanceQrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function approve(AttendanceExceptionRequest $req)
    {
        if ($req->status !== 'pending') {
            return back()->with('ok', 'Pengajuan sudah diproses.');
        }

        try {
            DB::transaction(function () use ($req) {

                // Lock QR token, pastikan masih valid & belum dipakai
                $qr = AttendanceQrToken::where('id', $req->attendance_qr_token_id)
                    ->lockForUpdate()
                    ->first();

                if (!$qr) {
                    throw new \RuntimeException('QR token tidak ditemukan.');
                }

                if ($qr->mode !== $req->mode) {
                    throw new \RuntimeException('QR token salah mode.');
                }

                if (!$qr->expires_at || now()->gte($qr->expires_at)) {
                    throw new \RuntimeException('QR token sudah expired.');
                }

                if ($qr->used_at) {
                    throw new \RuntimeException('QR token sudah digunakan.');
                }

                // Attendance row
                $attendance = Attendance::firstOrCreate(
                    ['user_id' => $req->user_id, 'date' => $req->attendance_date->toDateString()],
                    ['ip' => null, 'device' => $req->user_agent]
                );

                // Validasi state
                if ($req->mode === 'in') {
                    if ($attendance->check_in_at) {
                        throw new \RuntimeException('Pegawai sudah check-in.');
                    }

                    $attendance->check_in_at = $req->created_at;
                    $attendance->check_in_lat = $req->lat;
                    $attendance->check_in_lng = $req->lng;
                    $attendance->check_in_photo_path = $req->photo_path;
                    $attendance->check_in_qr_id = $qr->id;
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
                    $attendance->check_out_qr_id = $qr->id;
                }

                // Tetap simpan hash device yang dipakai (milik orang lain)
                $attendance->device_hash = $req->device_hash;
                $attendance->device = $req->device_name ?: $req->user_agent;
                $attendance->save();

                // Consume QR
                $qr->used_at = now();
                $qr->used_by = $req->user_id;
                $qr->save();

                // Update request
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
}