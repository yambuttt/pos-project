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

        $checkins = \App\Models\Attendance::query()
            ->with('user')
            ->where('date', $date)
            ->whereNotNull('check_in_at')
            ->orderBy('check_in_at', 'asc')
            ->get()
            ->map(function ($a) {
                $a->work_duration = $this->formatDuration($a->check_in_at, $a->check_out_at);
                return $a;
            });

        $checkouts = \App\Models\Attendance::query()
            ->with('user')
            ->where('date', $date)
            ->whereNotNull('check_out_at')
            ->orderBy('check_out_at', 'asc')
            ->get()
            ->map(function ($a) {
                $a->work_duration = $this->formatDuration($a->check_in_at, $a->check_out_at);
                return $a;
            });

        return view('dashboard.admin.attendance_qr.history', [
            'date' => $date,
            'checkins' => $checkins,
            'checkouts' => $checkouts,
        ]);
    }

    /**
     * Format durasi kerja HH:MM.
     * Return null kalau belum lengkap.
     */
    private function formatDuration($checkInAt, $checkOutAt): ?string
    {
        if (!$checkInAt || !$checkOutAt)
            return null;

        $in = \Carbon\Carbon::parse($checkInAt);
        $out = \Carbon\Carbon::parse($checkOutAt);

        // kalau data aneh (checkout lebih kecil dari checkin), anggap null
        if ($out->lt($in))
            return null;

        $minutes = $in->diffInMinutes($out);
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return str_pad((string) $hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $mins, 2, '0', STR_PAD_LEFT);
    }
}