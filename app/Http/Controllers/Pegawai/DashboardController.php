<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $todayAttendance = Attendance::query()
            ->where('user_id', $userId)
            ->where('date', $today)
            ->first();

        // total hadir bulan ini = count hari yang punya check_in_at
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $totalMonth = Attendance::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->whereNotNull('check_in_at')
            ->count();

        $latest = Attendance::query()
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        // durasi hari ini (HH:MM:SS)
        $durationToday = null;
        if ($todayAttendance?->check_in_at && $todayAttendance?->check_out_at) {
            $in = \Carbon\Carbon::parse($todayAttendance->check_in_at);
            $out = \Carbon\Carbon::parse($todayAttendance->check_out_at);
            if ($out->gte($in)) {
                $sec = $in->diffInSeconds($out);
                $h = intdiv($sec, 3600);
                $m = intdiv($sec % 3600, 60);
                $s = $sec % 60;
                $durationToday = str_pad((string)$h, 2, '0', STR_PAD_LEFT) . ':' .
                                 str_pad((string)$m, 2, '0', STR_PAD_LEFT) . ':' .
                                 str_pad((string)$s, 2, '0', STR_PAD_LEFT);
            }
        }

        return view('dashboard.pegawai.index', compact(
            'todayAttendance',
            'durationToday',
            'totalMonth',
            'latest'
        ));
    }
}