<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        // default: hari ini
        $date = $request->query('date', now()->toDateString());

        // check-in table
        $checkins = Attendance::query()
            ->with('user')
            ->whereDate('date', $date)
            ->whereNotNull('check_in_at')
            ->orderBy('check_in_at', 'asc')
            ->get();

        // check-out table
        $checkouts = Attendance::query()
            ->with('user')
            ->whereDate('date', $date)
            ->whereNotNull('check_out_at')
            ->orderBy('check_out_at', 'asc')
            ->get();

        return view('dashboard.admin.attendance_qr.history', [
            'date' => $date,
            'checkins' => $checkins,
            'checkouts' => $checkouts,
        ]);
    }
}