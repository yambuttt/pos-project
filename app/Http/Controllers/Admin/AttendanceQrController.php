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

        $ttl = (int) config('attendance.qr_ttl_seconds', 15);

        // (Opsional tapi recommended) matikan token lama yang belum dipakai untuk mode ini
        AttendanceQrToken::where('mode', $data['mode'])
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);

        $token = AttendanceQrToken::create([
            'token' => hash('sha256', \Illuminate\Support\Str::random(64) . microtime(true)),
            'mode' => $data['mode'],
            'expires_at' => now()->addSeconds($ttl),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'token' => $token->token,
            'expires_in' => now()->diffInSeconds($token->expires_at, false),
        ]);
    }
}