<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        // Ambil semua attendance pada tanggal tsb
        $rows = \App\Models\Attendance::query()
            ->with('user')
            ->where('date', $date) // pakai where biasa, karena kolomnya memang DATE :contentReference[oaicite:4]{index=4}
            ->orderBy('id', 'asc')
            ->get();

        // Gabungkan per user_id supaya check-in & check-out nyatu
        $merged = $rows->groupBy('user_id')->map(function ($items) {
            $first = $items->first();

            $checkInAt = $items->pluck('check_in_at')->filter()->sort()->first();
            $checkOutAt = $items->pluck('check_out_at')->filter()->sort()->last();

            // pilih foto / lokasi yang tersedia
            $checkInPhoto = $items->pluck('check_in_photo_path')->filter()->first();
            $checkOutPhoto = $items->pluck('check_out_photo_path')->filter()->first();

            $checkInLat = $items->pluck('check_in_lat')->filter()->first();
            $checkInLng = $items->pluck('check_in_lng')->filter()->first();
            $checkOutLat = $items->pluck('check_out_lat')->filter()->first();
            $checkOutLng = $items->pluck('check_out_lng')->filter()->first();

            // device hash (ambil yang paling baru yang ada)
            $deviceHash = $items->pluck('device_hash')->filter()->last();

            // pakai model Attendance pertama sebagai "wadah" supaya view tidak banyak berubah
            $first->check_in_at = $checkInAt;
            $first->check_out_at = $checkOutAt;

            $first->check_in_photo_path = $checkInPhoto;
            $first->check_out_photo_path = $checkOutPhoto;

            $first->check_in_lat = $checkInLat;
            $first->check_in_lng = $checkInLng;
            $first->check_out_lat = $checkOutLat;
            $first->check_out_lng = $checkOutLng;

            $first->device_hash = $deviceHash;

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