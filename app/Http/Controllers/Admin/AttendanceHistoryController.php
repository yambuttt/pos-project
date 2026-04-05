<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceHistoryController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $userId = $request->query('user_id'); // optional

        // ambil list pegawai untuk dropdown filter
        // asumsi tabel users punya kolom "role" (pegawai/admin/kasir/kitchen)
        $employees = \App\Models\User::query()
            ->where('role', 'pegawai')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $checkinsQuery = \App\Models\Attendance::query()
            ->with('user')
            ->where('date', $date)
            ->whereNotNull('check_in_at');

        $checkoutsQuery = \App\Models\Attendance::query()
            ->with('user')
            ->where('date', $date)
            ->whereNotNull('check_out_at');

        if (!empty($userId)) {
            $checkinsQuery->where('user_id', $userId);
            $checkoutsQuery->where('user_id', $userId);
        }

        $checkins = $checkinsQuery->orderBy('check_in_at', 'asc')->get()->map(function ($a) {
            $a->work_duration = $this->formatDuration($a->check_in_at, $a->check_out_at);
            return $a;
        });

        $checkouts = $checkoutsQuery->orderBy('check_out_at', 'asc')->get()->map(function ($a) {
            $a->work_duration = $this->formatDuration($a->check_in_at, $a->check_out_at);
            return $a;
        });

        return view('dashboard.admin.attendance_qr.history', [
            'date' => $date,
            'selectedUserId' => $userId,
            'employees' => $employees,
            'checkins' => $checkins,
            'checkouts' => $checkouts,
        ]);
    }

    /**
     * HH:MM:SS
     */
    private function formatDuration($checkInAt, $checkOutAt): ?string
    {
        if (!$checkInAt || !$checkOutAt)
            return null;

        $in = \Carbon\Carbon::parse($checkInAt);
        $out = \Carbon\Carbon::parse($checkOutAt);
        if ($out->lt($in))
            return null;

        $seconds = $in->diffInSeconds($out);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        return str_pad((string) $hours, 2, '0', STR_PAD_LEFT) . ':' .
            str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) . ':' .
            str_pad((string) $secs, 2, '0', STR_PAD_LEFT);
    }
}