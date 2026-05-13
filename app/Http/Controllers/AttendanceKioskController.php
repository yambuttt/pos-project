<?php

namespace App\Http\Controllers;

use App\Models\AttendanceQrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceKioskController extends Controller
{
    /**
     * Tampilan QR untuk Kiosk (Public with Key)
     */
    public function index(Request $request, $key)
    {
        $validKey = config('attendance.kiosk_key', 'pos123');
        
        if ($key !== $validKey) {
            abort(403, 'Invalid Kiosk Key');
        }

        return view('attendance.kiosk', compact('key'));
    }

    /**
     * API untuk ambil token QR (Public with Key)
     */
    public function token(Request $request)
    {
        $validKey = config('attendance.kiosk_key', 'pos123');
        $key = $request->query('key');

        if ($key !== $validKey) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $mode = $request->query('mode', 'in');
        if (!in_array($mode, ['in', 'out'])) {
            return response()->json(['error' => 'Invalid mode'], 400);
        }

        $ttl = (int) config('attendance.qr_ttl_seconds', 60);

        // ✅ Cari token aktif (belum dipakai & belum expired)
        $existing = AttendanceQrToken::query()
            ->where('mode', $mode)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->first();

        if ($existing) {
            return response()->json([
                'token' => $existing->token,
                'expires_in' => now()->diffInSeconds($existing->expires_at, false),
            ]);
        }

        // ✅ Buat token baru jika tidak ada yang aktif
        $token = AttendanceQrToken::create([
            'token' => hash('sha256', Str::random(64) . microtime(true)),
            'mode' => $mode,
            'expires_at' => now()->addSeconds($ttl),
            'created_by' => null, // Kiosk mode tidak ada created_by
        ]);

        return response()->json([
            'token' => $token->token,
            'expires_in' => now()->diffInSeconds($token->expires_at, false),
        ]);
    }
}
