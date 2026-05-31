<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceQrToken;
use App\Models\EmployeeDevice;
use App\Models\LeaveRequest;
use App\Models\LateAttendanceRequest;
use App\Models\CheckoutCorrectionRequest;
use App\Models\OvertimeRequest;
use App\Models\AttendanceExceptionRequest;
use App\Services\ShiftResolverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PegawaiApiController extends Controller
{
    /**
     * 1. API LOGIN
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'ok' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        if ($user->role !== 'pegawai') {
            return response()->json([
                'ok' => false,
                'message' => 'Akses ditolak. Akun Anda bukan pegawai.'
            ], 403);
        }

        $token = $user->createToken('pegawai-mobile-token')->plainTextToken;

        return response()->json([
            'ok' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'business_type' => $user->business_type,
            ]
        ]);
    }

    /**
     * 2. API LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Logout berhasil.'
        ]);
    }

    /**
     * 3. ME (Profile Details)
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('defaultShift');

        return response()->json([
            'ok' => true,
            'user' => $user
        ]);
    }

    /**
     * 4. EMPLOYEE DASHBOARD
     */
    public function dashboard(Request $request)
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $todayAttendance = Attendance::query()
            ->where('user_id', $userId)
            ->where('date', $today)
            ->first();

        // total hadir bulan ini = count hari yang punya check_in_at
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $totalMonth = Attendance::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->whereNotNull('check_in_at')
            ->count();

        $latest = Attendance::query()
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        // durasi hari ini (HH:MM:SS)
        $durationToday = null;
        if ($todayAttendance?->check_in_at && $todayAttendance?->check_out_at) {
            $in = \Carbon\Carbon::parse($todayAttendance->check_in_at);
            $out = \Carbon\Carbon::parse($todayAttendance->check_out_at);
            if ($out->gte($in)) {
                $sec = $in->diffInSeconds($out);
                $h = intdiv($sec, 3600);
                $m = intdiv($sec % 3600, 60);
                $s = $sec % 60;
                $durationToday = str_pad((string)$h, 2, '0', STR_PAD_LEFT) . ':' .
                                 str_pad((string)$m, 2, '0', STR_PAD_LEFT) . ':' .
                                 str_pad((string)$s, 2, '0', STR_PAD_LEFT);
            }
        }

        // UX server time offset
        $serverNowMs = now()->timestamp * 1000;

        return response()->json([
            'ok' => true,
            'today_attendance' => $todayAttendance,
            'duration_today' => $durationToday,
            'total_month_attendance' => $totalMonth,
            'server_now_ms' => $serverNowMs,
            'latest_attendances' => $latest
        ]);
    }

    /**
     * 5. ATTENDANCE CONFIG & RANGE (absensi index)
     */
    public function attendanceIndex(Request $request)
    {
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', auth()->id())->where('date', $today)->first();

        $svc = app(ShiftResolverService::class);
        $winIn = $svc->getWindow(auth()->user(), 'in', now());
        $winOut = $svc->getWindow(auth()->user(), 'out', now());

        $serverNowMs = now()->timestamp * 1000;

        $ui = [
            'shift_name' => $winIn['shift']->name,
            'shift_code' => $winIn['shift']->code,
            'server_now_ms' => $serverNowMs,

            'in' => [
                'from_ms' => $winIn['from']->timestamp * 1000,
                'to_ms' => $winIn['to']->timestamp * 1000,
                'start_ms' => $winIn['start']->timestamp * 1000,
                'end_ms' => $winIn['end']->timestamp * 1000,
            ],
            'out' => [
                'from_ms' => $winOut['from']->timestamp * 1000,
                'to_ms' => $winOut['to']->timestamp * 1000,
                'start_ms' => $winOut['start']->timestamp * 1000,
                'end_ms' => $winOut['end']->timestamp * 1000,
            ],
        ];

        return response()->json([
            'ok' => true,
            'attendance' => $attendance,
            'ui' => $ui
        ]);
    }

    /**
     * 6. INITIALIZE DEVICE (checks registered state & geofence)
     */
    public function initDevice(Request $request)
    {
        $data = $request->validate([
            'device_hash' => ['required', 'string', 'size:64'],
            'fingerprint_hash' => ['required', 'string', 'size:64'],
            'device_name' => ['nullable', 'string', 'max:80'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        $existingDevice = EmployeeDevice::where('user_id', auth()->id())
            ->where('device_hash', $data['device_hash'])
            ->first();

        if (!$existingDevice) {
            $recovered = EmployeeDevice::where('user_id', auth()->id())
                ->where('fingerprint_hash', $data['fingerprint_hash'])
                ->whereNotNull('approved_at')
                ->whereNull('revoked_at')
                ->latest()
                ->first();

            if ($recovered) {
                $recovered->last_seen_at = now();
                $recovered->save();

                return response()->json([
                    'ok' => true,
                    'status' => 'approved_recovered',
                    'recovered_device_hash' => $recovered->device_hash
                ]);
            }
        }

        $device = EmployeeDevice::firstOrCreate(
            ['user_id' => auth()->id(), 'device_hash' => $data['device_hash']],
            [
                'device_name' => $data['device_name'] ?? null,
                'user_agent' => substr((string) $request->userAgent(), 0, 180),
                'fingerprint_hash' => $data['fingerprint_hash'],
            ]
        );

        $device->fingerprint_hash = $data['fingerprint_hash'];
        $incomingName = trim((string) ($data['device_name'] ?? ''));
        if ($incomingName !== '') {
            $device->device_name = $incomingName;
        }

        $ua = substr((string) $request->userAgent(), 0, 180);
        if ($ua !== '') {
            $device->user_agent = $ua;
        }

        $device->last_seen_at = now();
        $device->save();

        if ($device->revoked_at) {
            return response()->json([
                'ok' => false,
                'status' => 'revoked',
                'message' => 'Device kamu dicabut. Hubungi admin.'
            ], 403);
        }

        if (!$device->approved_at) {
            return response()->json([
                'ok' => false,
                'status' => 'pending',
                'message' => 'Device belum di-approve admin.'
            ], 403);
        }

        if (!$this->isWithinRestaurant((float) $data['lat'], (float) $data['lng'])) {
            return response()->json([
                'ok' => false,
                'status' => 'out_of_area',
                'message' => 'Lokasi di luar area restoran.'
            ], 403);
        }

        return response()->json(['ok' => true, 'status' => 'approved']);
    }

    /**
     * 7. SUBMIT ATTENDANCE (check-in / check-out with QR and selfie watermark)
     */
    public function submitAttendance(Request $request)
    {
        $data = $request->validate([
            'mode' => ['required', 'in:in,out'],
            'qr_token' => ['required', 'string'],
            'device_hash' => ['required', 'string', 'size:64'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'selfie' => ['required', 'image', 'max:4096'],
        ]);

        try {
            app(ShiftResolverService::class)->enforceWindow(auth()->user(), $data['mode'], now());
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        $device = EmployeeDevice::where('user_id', auth()->id())
            ->where('device_hash', $data['device_hash'])
            ->whereNull('revoked_at')
            ->first();

        if (!$device || !$device->approved_at) {
            return response()->json(['ok' => false, 'message' => 'Device belum terverifikasi admin.'], 403);
        }

        if (!$this->isWithinRestaurant((float) $data['lat'], (float) $data['lng'])) {
            return response()->json(['ok' => false, 'message' => 'Lokasi di luar area restoran.'], 403);
        }

        try {
            \DB::transaction(function () use ($data) {
                $qr = AttendanceQrToken::where('token', $data['qr_token'])
                    ->lockForUpdate()
                    ->first();

                if (!$qr) {
                    throw new \RuntimeException('QR tidak valid.');
                }
                if ($qr->mode !== $data['mode']) {
                    throw new \RuntimeException('QR salah mode.');
                }
                if (!$qr->expires_at || now()->gte($qr->expires_at)) {
                    throw new \RuntimeException('QR sudah expired.');
                }
                if ($qr->used_at) {
                    throw new \RuntimeException('QR sudah digunakan.');
                }

                $qr->used_at = now();
                $qr->used_by = auth()->id();
                $qr->save();
            });
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        $today = now()->toDateString();
        $att = Attendance::firstOrCreate(
            ['user_id' => auth()->id(), 'date' => $today],
            ['ip' => $request->ip(), 'device' => substr((string) $request->userAgent(), 0, 180)]
        );

        if ($data['mode'] === 'in') {
            if ($att->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-in hari ini.'], 422);
            }
        } else {
            if (!$att->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu belum check-in.'], 422);
            }
            if ($att->check_out_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-out hari ini.'], 422);
            }
        }

        $now = now();
        $path = $this->storeSelfieWithWatermark(
            $request->file('selfie'),
            $data['mode'],
            $today,
            (float) $data['lat'],
            (float) $data['lng'],
            $now
        );

        $att->device_hash = $data['device_hash'];

        if ($data['mode'] === 'in') {
            $att->check_in_at = $now;
            $att->check_in_lat = $data['lat'];
            $att->check_in_lng = $data['lng'];
            $att->check_in_photo_path = $path;
        } else {
            $att->check_out_at = $now;
            $att->check_out_lat = $data['lat'];
            $att->check_out_lng = $data['lng'];
            $att->check_out_photo_path = $path;
        }

        $att->save();

        return response()->json(['ok' => true, 'message' => 'Absensi berhasil disimpan.']);
    }

    /**
     * 8. EMERGENCY / EXCEPTION DEVICE LOOKUP
     */
    public function lookupDeviceOwner(Request $request)
    {
        $data = $request->validate([
            'device_hash' => ['required', 'string', 'size:64'],
            'fingerprint_hash' => ['required', 'string', 'size:64'],
            'device_name' => ['nullable', 'string', 'max:80'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        if (!$this->isWithinRestaurant((float) $data['lat'], (float) $data['lng'])) {
            return response()->json([
                'ok' => false,
                'status' => 'out_of_area',
                'message' => 'Lokasi di luar area restoran.',
            ], 403);
        }

        $device = EmployeeDevice::with('user')
            ->where('device_hash', $data['device_hash'])
            ->whereNull('revoked_at')
            ->first();

        if (!$device) {
            $device = EmployeeDevice::with('user')
                ->where('fingerprint_hash', $data['fingerprint_hash'])
                ->whereNotNull('approved_at')
                ->whereNull('revoked_at')
                ->first();

            if ($device) {
                return response()->json([
                    'ok' => true,
                    'status' => 'recovered_exception',
                    'recovered_device_hash' => $device->device_hash,
                ]);
            }
        }

        if (!$device) {
            return response()->json([
                'ok' => false,
                'status' => 'not_registered',
                'message' => 'Device ini tidak terdaftar sebagai device absensi manapun.',
            ], 422);
        }

        if ((int) $device->user_id === (int) auth()->id()) {
            return response()->json([
                'ok' => false,
                'status' => 'same_user',
                'message' => 'Device ini milik kamu. Gunakan Absensi Normal.',
            ], 422);
        }

        if (!$device->approved_at) {
            return response()->json([
                'ok' => false,
                'status' => 'owner_device_not_approved',
                'message' => 'Device ini belum di-approve admin (milik pegawai lain). Tidak bisa digunakan.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'status' => 'ok',
            'owner' => [
                'user_id' => $device->user_id,
                'name' => $device->user?->name,
                'email' => $device->user?->email,
                'device_name' => $device->device_name,
            ],
        ]);
    }

    /**
     * 9. SUBMIT EMERGENCY EXCEPTION ATTENDANCE (using other employee's device)
     */
    public function submitException(Request $request)
    {
        $data = $request->validate([
            'mode' => ['required', 'in:in,out'],
            'qr_token' => ['required', 'string'],
            'device_hash' => ['required', 'string', 'size:64'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'selfie' => ['required', 'image', 'max:4096'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            app(ShiftResolverService::class)->enforceWindow(auth()->user(), $data['mode'], now());
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        if (!$this->isWithinRestaurant((float) $data['lat'], (float) $data['lng'])) {
            return response()->json(['ok' => false, 'message' => 'Lokasi di luar area restoran.'], 403);
        }

        $ownerDevice = EmployeeDevice::with('user')
            ->where('device_hash', $data['device_hash'])
            ->whereNull('revoked_at')
            ->first();

        if (!$ownerDevice) {
            return response()->json(['ok' => false, 'message' => 'Device ini tidak terdaftar.'], 422);
        }

        if ((int) $ownerDevice->user_id === (int) auth()->id()) {
            return response()->json(['ok' => false, 'message' => 'Device ini milik kamu. Gunakan Absensi Normal.'], 422);
        }

        if (!$ownerDevice->approved_at) {
            return response()->json(['ok' => false, 'message' => 'Device pemilik belum di-approve admin.'], 422);
        }

        $today = now()->toDateString();
        $att = Attendance::where('user_id', auth()->id())
            ->where('date', $today)
            ->first();

        if ($data['mode'] === 'in') {
            if ($att?->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-in hari ini.'], 422);
            }
        } else {
            if (!$att?->check_in_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu belum check-in.'], 422);
            }
            if ($att?->check_out_at) {
                return response()->json(['ok' => false, 'message' => 'Kamu sudah check-out hari ini.'], 422);
            }
        }

        $pendingExists = AttendanceExceptionRequest::query()
            ->where('user_id', auth()->id())
            ->where('attendance_date', $today)
            ->where('mode', $data['mode'])
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return response()->json(['ok' => false, 'message' => 'Pengajuan masih pending. Tunggu persetujuan admin.'], 422);
        }

        $qrId = null;
        try {
            DB::transaction(function () use ($data, &$qrId) {
                $qr = AttendanceQrToken::where('token', $data['qr_token'])
                    ->lockForUpdate()
                    ->first();

                if (!$qr) {
                    throw new \RuntimeException('QR tidak valid.');
                }
                if ($qr->mode !== $data['mode']) {
                    throw new \RuntimeException('QR salah mode.');
                }
                if (!$qr->expires_at || now()->gte($qr->expires_at)) {
                    throw new \RuntimeException('QR token sudah expired.');
                }
                if ($qr->used_at) {
                    throw new \RuntimeException('QR sudah digunakan.');
                }

                $qr->used_at = now();
                $qr->used_by = auth()->id();
                $qr->save();

                $qrId = $qr->id;
            });
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        $now = now();
        $path = $this->storeSelfieWithWatermark(
            $request->file('selfie'),
            $data['mode'],
            $today,
            (float) $data['lat'],
            (float) $data['lng'],
            $now
        );

        AttendanceExceptionRequest::create([
            'user_id' => auth()->id(),
            'attendance_date' => $today,
            'mode' => $data['mode'],
            'device_hash' => $data['device_hash'],
            'device_owner_device_id' => $ownerDevice->id,
            'device_owner_user_id' => $ownerDevice->user_id,
            'device_name' => $ownerDevice->device_name,
            'user_agent' => substr((string) $request->userAgent(), 0, 180),
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'attendance_qr_token_id' => $qrId,
            'photo_path' => $path,
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Pengajuan absensi via device pegawai lain berhasil dikirim dan menunggu persetujuan admin.',
            'device_owner' => $ownerDevice->user?->name,
        ]);
    }

    /**
     * 10. ROSTER / SHIFT SCHEDULE (FullCalendar Events payload)
     */
    public function getSchedule(Request $request)
    {
        $data = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $user = auth()->user();
        $svc = app(ShiftResolverService::class);

        $start = \Carbon\Carbon::parse($data['start'])->startOfDay();
        $end = \Carbon\Carbon::parse($data['end'])->startOfDay();

        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = now($tz);

        $attMap = Attendance::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->copy()->subDay()->toDateString()])
            ->get()
            ->keyBy(function ($a) {
                return $a->date instanceof \Carbon\Carbon
                    ? $a->date->toDateString()
                    : \Carbon\Carbon::parse($a->date)->toDateString();
            });

        $leaveRows = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->get();

        $leaveMap = [];
        foreach ($leaveRows as $lr) {
            $d1 = $lr->start_date->copy();
            $d2 = $lr->end_date->copy();
            for ($d = $d1; $d->lte($d2); $d->addDay()) {
                $leaveMap[$d->toDateString()] = [
                    'type' => $lr->type,
                    'reason' => $lr->reason,
                ];
            }
        }

        $overrides = \App\Models\UserShiftOverride::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'approved')
            ->get()
            ->keyBy(fn($x) => $x->date->toDateString());

        $ccMap = CheckoutCorrectionRequest::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->copy()->subDay()->toDateString()])
            ->get()
            ->keyBy(fn($x) => $x->date->toDateString());

        $events = [];

        for ($d = $start->copy(); $d->lt($end); $d->addDay()) {
            $dateStr = $d->toDateString();

            $shift = $svc->resolveShiftForDate($user, $d);
            $isOverride = isset($overrides[$dateStr]);

            $shiftStart = \Carbon\Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);
            $shiftEnd = \Carbon\Carbon::parse($dateStr . ' ' . $shift->end_time, $tz);
            if ($shiftEnd->lte($shiftStart))
                $shiftEnd->addDay();

            $wIn = $svc->getWindow($user, 'in', $shiftStart);
            $wOut = $svc->getWindow($user, 'out', $shiftStart);

            $checkInTo = $wIn['to'];
            $checkOutTo = $wOut['to'];

            $status = 'SCHEDULE';
            $statusReason = null;

            if (isset($leaveMap[$dateStr])) {
                $t = $leaveMap[$dateStr]['type'];
                $status = ($t === 'sakit') ? 'SAKIT' : 'CUTI';
                $statusReason = $leaveMap[$dateStr]['reason'] ?? null;
            } else {
                $att = $attMap[$dateStr] ?? null;

                if (!$att || !$att->check_in_at) {
                    if ($dateStr < $now->toDateString()) {
                        $status = 'ALPHA';
                    } elseif ($dateStr === $now->toDateString() && $now->gt($checkInTo)) {
                        $status = 'ALPHA';
                    } else {
                        $status = 'SCHEDULE';
                    }
                } else {
                    if ($att->check_out_at) {
                        $status = 'HADIR';
                    } else {
                        $cc = $ccMap[$dateStr] ?? null;

                        if ($cc && $cc->status === 'rejected') {
                            $status = 'ALPHA';
                            $statusReason = $cc->review_note ?? 'Koreksi checkout ditolak';
                        } else {
                            if ($cc && $cc->status === 'pending') {
                                $status = 'INCOMPLETE';
                                $statusReason = 'Menunggu persetujuan koreksi checkout';
                            } else {
                                if ($dateStr < $now->toDateString()) {
                                    $status = 'ALPHA';
                                    $statusReason = 'Lupa checkout (Hari terlewati)';
                                } elseif ($dateStr === $now->toDateString()) {
                                    if ($now->gt($checkOutTo)) {
                                        $status = 'ALPHA';
                                        $statusReason = 'Lupa checkout (Batas waktu habis)';
                                    } else {
                                        $status = 'ONGOING';
                                    }
                                } else {
                                    $status = 'ONGOING';
                                }
                            }
                        }
                    }
                }
            }

            $title = ($isOverride ? 'OVR ' : '') . "{$shift->code} {$shift->start_time}-{$shift->end_time}";
            if ($status !== 'SCHEDULE' && $status !== 'ONGOING')
                $title .= " • {$status}";
            if ($status === 'ONGOING')
                $title .= " • ON";

            $bg = ($shift->code === 'A') ? '#22c55e' : '#f59e0b';
            $text = '#0b0b0b';

            if ($status === 'HADIR') {
                $bg = '#3b82f6';
                $text = '#ffffff';
            }
            if ($status === 'ALPHA') {
                $bg = '#6b7280';
                $text = '#ffffff';
            }
            if ($status === 'CUTI') {
                $bg = '#a855f7';
                $text = '#ffffff';
            }
            if ($status === 'SAKIT') {
                $bg = '#ef4444';
                $text = '#ffffff';
            }
            if ($status === 'INCOMPLETE') {
                $bg = '#fbbf24';
                $text = '#0b0b0b';
            }
            if ($status === 'ONGOING') {
                $bg = '#14b8a6';
                $text = '#0b0b0b';
            }

            if ($isOverride && $status === 'SCHEDULE') {
                $bg = '#f472b6';
                $text = '#0b0b0b';
            }

            $events[] = [
                'id' => $dateStr,
                'title' => $title,
                'start' => $shiftStart->toIso8601String(),
                'end' => $shiftEnd->toIso8601String(),
                'allDay' => false,
                'backgroundColor' => $bg,
                'borderColor' => $bg,
                'textColor' => $text,
                'extendedProps' => [
                    'shift_name' => $shift->name,
                    'shift_code' => $shift->code,
                    'status' => $status,
                    'status_reason' => $statusReason,
                    'check_in_at' => ($att && $att->check_in_at) ? $att->check_in_at->format('H:i:s') : null,
                    'check_out_at' => ($att && $att->check_out_at) ? $att->check_out_at->format('H:i:s') : null,
                    'is_override' => $isOverride,
                    'override_reason' => $isOverride ? ($overrides[$dateStr]->reason ?? null) : null,
                ],
            ];
        }

        return response()->json(['ok' => true, 'events' => $events]);
    }

    /**
     * 11. GET LEAVE REQUESTS
     */
    public function getLeaveRequests(Request $request)
    {
        $items = LeaveRequest::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return response()->json([
            'ok' => true,
            'items' => $items
        ]);
    }

    /**
     * 12. SUBMIT LEAVE REQUEST (cuti / sakit)
     */
    public function submitLeaveRequest(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:cuti,sakit'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:500'],
            'doctor_note' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($data['type'] === 'sakit' && !$request->hasFile('doctor_note')) {
            return response()->json([
                'ok' => false,
                'message' => 'Untuk Sakit wajib melampirkan foto surat dokter.'
            ], 422);
        }

        $path = null;

        if ($request->hasFile('doctor_note')) {
            $file = $request->file('doctor_note');
            $dir = 'public/leave_requests/' . auth()->id() . '/' . now()->toDateString();
            $name = 'doctor_' . now()->format('His') . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($dir, $name, 'local');
        }

        $leave = LeaveRequest::create([
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'] ?? null,
            'doctor_note_path' => $path,
            'status' => 'pending',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Pengajuan berhasil dikirim dan menunggu persetujuan admin.',
            'item' => $leave
        ], 201);
    }

    /**
     * 13. SUBMIT LATE REQUEST
     */
    public function submitLateRequest(Request $request)
    {
        $data = $request->validate([
            'requested_until_time' => ['required', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:500'],
            'evidence' => ['required', 'image', 'max:4096'],
        ]);

        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = now($tz);
        $dateStr = $now->toDateString();

        $svc = app(ShiftResolverService::class);
        $shift = $svc->resolveShiftForDate(auth()->user(), $now);

        $shiftStart = \Carbon\Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);

        if ($now->gte($shiftStart)) {
            return response()->json([
                'ok' => false,
                'message' => 'Pengajuan telat ditutup karena sudah melewati jam mulai shift.',
            ], 422);
        }

        $cap = $shiftStart->copy()->addMinutes(120);

        if ($now->gt($cap)) {
            return response()->json([
                'ok' => false,
                'message' => 'Pengajuan telat sudah ditutup. Kamu sudah lewat batas maksimal telat.',
            ], 422);
        }

        $requested = \Carbon\Carbon::parse($dateStr . ' ' . $data['requested_until_time'], $tz);

        if ($requested->lt($shiftStart)) {
            return response()->json([
                'ok' => false,
                'message' => 'Jam maksimal tidak boleh sebelum jam mulai shift.',
            ], 422);
        }

        if ($requested->gt($cap)) {
            return response()->json([
                'ok' => false,
                'message' => 'Maksimal telat hanya sampai ' . $cap->format('H:i') . '.',
            ], 422);
        }

        $file = $request->file('evidence');
        $dir = 'public/late_requests/' . auth()->id() . '/' . $dateStr;
        $name = 'evidence_' . now()->format('His') . '_' . \Illuminate\Support\Str::random(6) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($dir, $name, 'local');

        $late = LateAttendanceRequest::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $dateStr],
            [
                'requested_until_time' => $data['requested_until_time'],
                'reason' => $data['reason'] ?? null,
                'evidence_path' => $path,
                'status' => 'pending',
                'allowed_until_time' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_note' => null,
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Pengajuan telat berhasil dikirim. Menunggu persetujuan admin.',
            'item' => $late
        ]);
    }

    /**
     * 14. SUBMIT CHECKOUT CORRECTION
     */
    public function submitCheckoutCorrection(Request $request)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $tz = config('app.timezone','Asia/Jakarta');
        $now = now($tz);
        $dateStr = $now->toDateString();

        $att = Attendance::query()
            ->where('user_id', auth()->id())
            ->where('date', $dateStr)
            ->first();

        if (!$att || !$att->check_in_at) {
            return response()->json(['ok' => false, 'message' => 'Kamu belum check-in hari ini.'], 422);
        }
        if ($att->check_out_at) {
            return response()->json(['ok' => false, 'message' => 'Kamu sudah checkout. Tidak perlu koreksi.'], 422);
        }

        $svc = app(ShiftResolverService::class);
        $wOut = $svc->getWindow(auth()->user(), 'out', $now);

        if ($now->lt($wOut['from'])) {
            return response()->json(['ok' => false, 'message' => 'Koreksi checkout hanya bisa diajukan setelah jam selesai shift.'], 422);
        }

        $exists = CheckoutCorrectionRequest::query()
            ->where('user_id', auth()->id())
            ->where('date', $dateStr)
            ->whereIn('status', ['pending','approved'])
            ->first();

        if ($exists) {
            return response()->json([
                'ok' => false,
                'message' => 'Pengajuan koreksi checkout hari ini sudah ada ('.strtoupper($exists->status).').'
            ], 422);
        }

        $correction = CheckoutCorrectionRequest::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $dateStr],
            ['reason' => $data['reason'], 'status' => 'pending', 'reviewed_by' => null, 'reviewed_at' => null, 'review_note' => null]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Pengajuan koreksi checkout terkirim. Menunggu admin.',
            'item' => $correction
        ]);
    }

    /**
     * 15. SUBMIT OVERTIME REQUEST
     */
    public function submitOvertimeRequest(Request $request)
    {
        $data = $request->validate([
            'minutes' => ['required', 'integer', 'in:60,120,180'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = now($tz);
        $dateStr = $now->toDateString();

        $att = Attendance::query()
            ->where('user_id', auth()->id())
            ->whereDate('date', $dateStr)
            ->first();

        if (!$att || !$att->check_in_at) {
            return response()->json(['ok' => false, 'message' => 'Kamu harus check-in dulu sebelum ajukan lembur.'], 422);
        }
        if ($att->check_out_at) {
            return response()->json(['ok' => false, 'message' => 'Kamu sudah checkout. Tidak bisa ajukan lembur.'], 422);
        }

        $exists = OvertimeRequest::query()
            ->where('user_id', auth()->id())
            ->whereDate('date', $dateStr)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($exists) {
            return response()->json([
                'ok' => false,
                'message' => 'Pengajuan lembur hari ini sudah ada ('.strtoupper($exists->status).').'
            ], 422);
        }

        $overtime = OvertimeRequest::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $dateStr],
            [
                'requested_minutes' => (int)$data['minutes'],
                'approved_minutes' => null,
                'reason' => $data['reason'] ?? null,
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_note' => null,
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Pengajuan lembur terkirim. Menunggu persetujuan admin.',
            'item' => $overtime
        ]);
    }

    /**
     * 16. ATTENDANCE HISTORY LIST (paginated/filtered)
     */
    public function getAttendanceHistory(Request $request)
    {
        $userId = auth()->id();

        $from = $request->query('from', now()->subDays(14)->toDateString());
        $to = $request->query('to', now()->toDateString());

        $rows = Attendance::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from, $to])
            ->orderByDesc('date')
            ->paginate(15);

        // inject duration string HH:MM:SS
        $rows->getCollection()->transform(function ($a) {
            $a->work_duration = null;
            if ($a->check_in_at && $a->check_out_at) {
                $in = \Carbon\Carbon::parse($a->check_in_at);
                $out = \Carbon\Carbon::parse($a->check_out_at);
                if ($out->gte($in)) {
                    $sec = $in->diffInSeconds($out);
                    $h = intdiv($sec, 3600);
                    $m = intdiv($sec % 3600, 60);
                    $s = $sec % 60;
                    $a->work_duration = str_pad((string)$h,2,'0',STR_PAD_LEFT).':'.
                                        str_pad((string)$m,2,'0',STR_PAD_LEFT).':'.
                                        str_pad((string)$s,2,'0',STR_PAD_LEFT);
                }
            }
            return $a;
        });

        return response()->json([
            'ok' => true,
            'from' => $from,
            'to' => $to,
            'history' => $rows
        ]);
    }

    /**
     * 17. SECURE PHOTO STREAMING
     */
    public function streamAttendancePhoto(Attendance $attendance, string $type)
    {
        if ($attendance->user_id !== auth()->id()) {
            return response()->json(['ok' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if (!in_array($type, ['in', 'out'], true)) {
            return response()->json(['ok' => false, 'message' => 'Jenis foto tidak valid.'], 404);
        }

        $path = $type === 'in' ? $attendance->check_in_photo_path : $attendance->check_out_photo_path;

        if (!$path) return response()->json(['ok' => false, 'message' => 'Foto tidak ditemukan.'], 404);

        $disk = Storage::disk('local');
        if (!$disk->exists($path)) return response()->json(['ok' => false, 'message' => 'File tidak ada di server.'], 404);

        return $disk->response($path);
    }

    public function streamLeaveDoctorNote(LeaveRequest $leave)
    {
        if ($leave->user_id !== auth()->id()) {
            return response()->json(['ok' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $path = $leave->doctor_note_path;
        if (!$path) return response()->json(['ok' => false, 'message' => 'Foto surat dokter tidak ditemukan.'], 404);

        $disk = Storage::disk('local');
        if (!$disk->exists($path)) return response()->json(['ok' => false, 'message' => 'File tidak ada.'], 404);

        return $disk->response($path);
    }

    public function streamLateEvidence(LateAttendanceRequest $req)
    {
        if ($req->user_id !== auth()->id()) {
            return response()->json(['ok' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $path = $req->evidence_path;
        if (!$path) return response()->json(['ok' => false, 'message' => 'Bukti telat tidak ditemukan.'], 404);

        $disk = Storage::disk('local');
        if (!$disk->exists($path)) return response()->json(['ok' => false, 'message' => 'File tidak ada.'], 404);

        return $disk->response($path);
    }

    /**
     * PRIVATE GEOFENCE HELPERS
     */
    private function storeSelfieWithWatermark(
        UploadedFile $file,
        string $mode,
        string $date,
        float $lat,
        float $lng,
        Carbon $takenAt
    ): string {
        $userId = auth()->id();
        $name = $mode === 'in' ? 'checkin' : 'checkout';
        $filename = $name . '_' . $takenAt->format('His') . '.jpg';

        $path = $file->storeAs("public/attendances/{$userId}/{$date}", $filename);

        $employeeName = (string) (auth()->user()->name ?? 'Unknown');
        $this->applyWatermarkToStoredImage($path, $employeeName, $mode, $takenAt, $lat, $lng);

        return $path;
    }

    private function applyWatermarkToStoredImage(
        string $path,
        string $employeeName,
        string $mode,
        \Carbon\Carbon $takenAt,
        float $lat,
        float $lng
    ): void {
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) return;

        $abs = $disk->path($path);
        $raw = @file_get_contents($abs);
        if ($raw === false) return;

        $img = @imagecreatefromstring($raw);
        if (!$img) return;

        $w = imagesx($img);
        $h = imagesy($img);

        $lines = [
            'Nama: ' . $employeeName,
            'Mode: ' . strtoupper($mode) . '  •  ' . $takenAt->format('Y-m-d H:i:s'),
            'Lokasi: ' . number_format($lat, 6, '.', '') . ', ' . number_format($lng, 6, '.', ''),
            'Maps: https://www.google.com/maps?q=' . $lat . ',' . $lng
        ];

        $font = 3;
        $charH = imagefontheight($font);
        $padding = max(14, (int) round(min($w, $h) * 0.02));
        $lineGap = 6;

        $logoPath = public_path('images/landing/logo-ayo-renne.png');
        $logo = null;

        if (is_file($logoPath)) {
            $logo = @imagecreatefrompng($logoPath);
            if ($logo) {
                imagealphablending($logo, true);
                imagesavealpha($logo, true);
            }
        }

        $logoW = 0;
        $logoH = 0;
        $logoMarginRight = 0;

        if ($logo) {
            $srcW = imagesx($logo);
            $srcH = imagesy($logo);

            $maxW = min((int) round($w * 0.18), 120);
            $scale = $maxW / max(1, $srcW);

            $logoW = max(1, (int) round($srcW * $scale));
            $logoH = max(1, (int) round($srcH * $scale));
            $logoMarginRight = $padding;
        }

        $textBlockH = (count($lines) * $charH) + ((count($lines) - 1) * $lineGap);
        $contentH = max($textBlockH, $logoH);
        $boxH = $padding + $contentH + $padding;

        $y1 = max(0, $h - $boxH);
        $y2 = $h;

        imagealphablending($img, true);
        imagesavealpha($img, true);

        $bg = imagecolorallocatealpha($img, 0, 0, 0, 60);
        $white = imagecolorallocatealpha($img, 255, 255, 255, 0);

        imagefilledrectangle($img, 0, $y1, $w, $y2, $bg);

        $textX = $padding;

        if ($logo && $logoW > 0 && $logoH > 0) {
            $dstX = $w - $padding - $logoW;
            $dstY = $y1 + (int) round(($boxH - $logoH) / 2);

            imagecopyresampled($img, $logo, $dstX, $dstY, 0, 0, $logoW, $logoH, imagesx($logo), imagesy($logo));
        }

        $y = $y1 + $padding + (int) round((max($logoH, $textBlockH) - $textBlockH) / 2);

        foreach ($lines as $t) {
            imagestring($img, $font, $textX, $y, $t, $white);
            $y += $charH + $lineGap;
        }

        if ($logo) imagedestroy($logo);

        @imagejpeg($img, $abs, 88);
        imagedestroy($img);
    }

    private function isWithinRestaurant(float $lat, float $lng): bool
    {
        $rLat = (float) config('attendance.restaurant_lat');
        $rLng = (float) config('attendance.restaurant_lng');
        $radius = (int) config('attendance.radius_m', 120);

        if ($rLat == 0.0 && $rLng == 0.0) return true;

        $d = $this->haversineMeters($lat, $lng, $rLat, $rLng);
        return $d <= $radius;
    }

    private function haversineMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371000;
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $dPhi = deg2rad($lat2 - $lat1);
        $dLam = deg2rad($lon2 - $lon1);

        $a = sin($dPhi / 2) * sin($dPhi / 2) +
            cos($phi1) * cos($phi2) *
            sin($dLam / 2) * sin($dLam / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }
}
