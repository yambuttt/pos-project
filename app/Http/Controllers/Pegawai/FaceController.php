<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\FaceProfile;
use Illuminate\Http\Request;

class FaceController extends Controller
{
    public function showEnroll()
    {
        $profile = FaceProfile::where('user_id', auth()->id())->first();
        return view('dashboard.pegawai.face_enroll', compact('profile'));
    }

    public function storeEnroll(Request $request)
    {
        $data = $request->validate([
            'descriptors' => ['required', 'array', 'min:3'],
            'descriptors.*' => ['array'],
        ]);

        // basic sanitize: pastikan descriptor berisi angka
        $descriptors = array_map(function ($d) {
            return array_map('floatval', $d);
        }, $data['descriptors']);

        FaceProfile::updateOrCreate(
            ['user_id' => auth()->id()],
            ['descriptors' => $descriptors, 'enrolled_at' => now()]
        );

        return response()->json(['ok' => true]);
    }
}