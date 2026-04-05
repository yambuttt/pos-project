<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;

class AttendancePhotoController extends Controller
{
    public function show(Attendance $attendance, string $type)
    {
        if (!in_array($type, ['in', 'out'], true)) {
            abort(404);
        }

        $path = $type === 'in'
            ? $attendance->check_in_photo_path
            : $attendance->check_out_photo_path;

        if (!$path) {
            abort(404);
        }

        // Path tersimpan seperti: "public/attendances/15/2026-04-05/checkin_....jpg"
        // Kita ubah jadi relative untuk disk "public": "attendances/15/2026-04-05/....jpg"
        $relative = str_starts_with($path, 'public/') ? substr($path, 7) : $path;

        if (!Storage::disk('public')->exists($relative)) {
            abort(404);
        }

        // Serve file via Laravel (bukan akses langsung /storage)
        return Storage::disk('public')->response($relative);
    }
}