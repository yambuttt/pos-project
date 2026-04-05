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

        /**
         * PENTING:
         * Di project kamu, disk "local" root = storage/app/private :contentReference[oaicite:1]{index=1}
         * Selfie tersimpan sebagai: "public/attendances/15/2026-04-05/xxx.jpg"
         * Jadi file fisiknya ada di: storage/app/private/public/attendances/...
         */
        $disk = Storage::disk('local');

        // JANGAN di-strip "public/" karena memang foldernya ada di private/public/...
        if (!$disk->exists($path)) {
            abort(404);
        }

        // Stream via Laravel (tidak tergantung symlink /storage)
        return $disk->response($path);
    }
}