<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $from = $request->query('from', now()->subDays(14)->toDateString());
        $to = $request->query('to', now()->toDateString());

        $rows = Attendance::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from, $to])
            ->orderByDesc('date')
            ->get();

        // inject duration string HH:MM:SS
        $rows->transform(function ($a) {
            $a->work_duration = null;
            if ($a->check_in_at && $a->check_out_at) {
                $in = \Carbon\Carbon::parse($a->check_in_at);
                $out = \Carbon\Carbon::parse($a->check_out_at);
                if ($out->gte($in)) {
                    $sec = $in->diffInSeconds($out);
                    $h = intdiv($sec, 3600);
                    $m = intdiv($sec % 3600, 60);
                    $s = $sec % 60;
                    $a->work_duration = str_pad((string)$h,2,'0',STR_PAD_LEFT).':'.
                                        str_pad((string)$m,2,'0',STR_PAD_LEFT).':'.
                                        str_pad((string)$s,2,'0',STR_PAD_LEFT);
                }
            }
            return $a;
        });

        return view('dashboard.pegawai.attendance_history', compact('from', 'to', 'rows'));
    }
}