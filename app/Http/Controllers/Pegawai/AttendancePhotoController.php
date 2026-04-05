<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;

class AttendancePhotoController extends Controller
{
    public function show(Attendance $attendance, string $type)
    {
        // pegawai hanya boleh akses foto miliknya
        if ($attendance->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($type, ['in', 'out'], true)) {
            abort(404);
        }

        $path = $type === 'in'
            ? $attendance->check_in_photo_path
            : $attendance->check_out_photo_path;

        if (!$path) abort(404);

        // Di hosting kamu, file tersimpan di disk local => storage/app/private (lihat kasus kamu sebelumnya)
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) abort(404);

        return $disk->response($path);
    }
}