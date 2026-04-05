<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceQrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceQrController extends Controller
{
    public function index()
    {
        $in = AttendanceQrToken::where('mode', 'in')->whereNull('used_at')->orderByDesc('id')->first();
        $out = AttendanceQrToken::where('mode', 'out')->whereNull('used_at')->orderByDesc('id')->first();

        return view('dashboard.admin.attendance_qr.index', compact('in', 'out'));
    }

    public function regenerate(Request $request)
    {
        $data = $request->validate([
            'mode' => ['required', 'in:in,out'],
        ]);

        $ttl = config('attendance.qr_ttl_seconds', 60);

        $token = AttendanceQrToken::create([
            'token' => hash('sha256', Str::random(64) . microtime(true)),
            'mode' => $data['mode'],
            'expires_at' => now()->addSeconds($ttl),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.attendance.qr')->with('ok', "QR {$data['mode']} dibuat (berlaku {$ttl} detik).");
    }

    public function token(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'mode' => ['required', 'in:in,out'],
        ]);

        $ttl = (int) config('attendance.qr_ttl_seconds', 15);  // set 10-20 detik
        $slot = (int) floor(time() / $ttl);                    // time slot berubah tiap ttl detik

        $payload = [
            'm' => $data['mode'],
            's' => $slot,
            'v' => 1, // versioning kalau nanti format berubah
        ];

        $json = json_encode($payload);
        $sig = hash_hmac('sha256', $json, config('app.key'));

        // token final (ringkas)
        $token = rtrim(strtr(base64_encode($json), '+/', '-_'), '=') . '.' . $sig;

        return response()->json([
            'token' => $token,
            'expires_in' => $ttl - (time() % $ttl),
            'slot' => $slot,
        ]);
    }
}