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

        $rows = \App\Models\Attendance::query()
            ->with('user')
            ->where('date', $date)
            ->orderBy('id', 'asc')
            ->get();

        // Merge per user_id untuk menyatukan check-in dan check-out
        $merged = $rows->groupBy('user_id')->map(function ($items) {
            $first = $items->first();

            $checkInAt = $items->pluck('check_in_at')->filter()->sort()->first();
            $checkOutAt = $items->pluck('check_out_at')->filter()->sort()->last();

            $first->check_in_at = $checkInAt;
            $first->check_out_at = $checkOutAt;

            $first->check_in_lat = $items->pluck('check_in_lat')->filter()->first();
            $first->check_in_lng = $items->pluck('check_in_lng')->filter()->first();
            $first->check_out_lat = $items->pluck('check_out_lat')->filter()->first();
            $first->check_out_lng = $items->pluck('check_out_lng')->filter()->first();

            $first->check_in_photo_path = $items->pluck('check_in_photo_path')->filter()->first();
            $first->check_out_photo_path = $items->pluck('check_out_photo_path')->filter()->first();

            $first->device_hash = $items->pluck('device_hash')->filter()->last();

            return $first;
        })->values();

        $checkins = $merged->filter(fn($a) => !is_null($a->check_in_at))->sortBy('check_in_at')->values();
        $checkouts = $merged->filter(fn($a) => !is_null($a->check_out_at))->sortBy('check_out_at')->values();

        return view('dashboard.admin.attendance_qr.history', [
            'date' => $date,
            'checkins' => $checkins,
            'checkouts' => $checkouts,
        ]);
    }
}