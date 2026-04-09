<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\UserShiftOverride;
use App\Services\ShiftResolverService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftScheduleController extends Controller
{
    public function index()
    {
        return view('dashboard.pegawai.schedule');
    }

    /**
     * FullCalendar JSON feed
     */
    public function calendar(Request $request)
    {
        $data = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $user = auth()->user();
        $svc = app(\App\Services\ShiftResolverService::class);

        $start = \Carbon\Carbon::parse($data['start'])->startOfDay();
        $end = \Carbon\Carbon::parse($data['end'])->startOfDay(); // end exclusive

        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = now($tz);

        // 1) Ambil attendance pada range
        $attMap = \App\Models\Attendance::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->copy()->subDay()->toDateString()])
            ->get()
            ->keyBy(fn($a) => (string) $a->date);

        // 2) Ambil leave approved yang overlap range (cuti/sakit)
        $leaveRows = \App\Models\LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->get();

        // Expand leave range jadi map date => type (cuti/sakit)
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

        // 3) Override shift (biar tetap kebaca “OVR” di title)
        $overrides = \App\Models\UserShiftOverride::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'approved')
            ->get()
            ->keyBy(fn($x) => $x->date->toDateString());

        $events = [];

        for ($d = $start->copy(); $d->lt($end); $d->addDay()) {
            $dateStr = $d->toDateString();

            // resolve shift harian (fixed/rotation/override)
            $shift = $svc->resolveShiftForDate($user, $d);

            $isOverride = isset($overrides[$dateStr]);

            $shiftStart = \Carbon\Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);
            $shiftEnd = \Carbon\Carbon::parse($dateStr . ' ' . $shift->end_time, $tz);
            if ($shiftEnd->lte($shiftStart))
                $shiftEnd->addDay();

            // Hitung batas akhir check-in hari itu = "to" dari window in
            $wIn = $svc->getWindow($user, 'in', $shiftStart);
            $checkInTo = $wIn['to'];

            // Tentukan status kehadiran
            $status = 'SCHEDULE'; // default untuk future/ belum dievaluasi
            $statusLabel = '';    // text kecil
            $statusReason = null;

            // (1) CUTI/SAKIT menang paling atas
            if (isset($leaveMap[$dateStr])) {
                $t = $leaveMap[$dateStr]['type'];
                $status = ($t === 'sakit') ? 'SAKIT' : 'CUTI';
                $statusLabel = $status;
                $statusReason = $leaveMap[$dateStr]['reason'] ?? null;
            } else {
                // (2) HADIR kalau ada check-in
                $att = $attMap[$dateStr] ?? null;
                if ($att && $att->check_in_at) {
                    $status = 'HADIR';
                    $statusLabel = 'HADIR';
                } else {
                    // (3) ALPHA kalau sudah lewat batas check-in (hari lalu pasti alpha)
                    if ($dateStr < $now->toDateString()) {
                        $status = 'ALPHA';
                        $statusLabel = 'ALPHA';
                    } elseif ($dateStr === $now->toDateString() && $now->gt($checkInTo)) {
                        $status = 'ALPHA';
                        $statusLabel = 'ALPHA';
                    } else {
                        // masih future / belum lewat batas checkin
                        $status = 'SCHEDULE';
                        $statusLabel = '';
                    }
                }
            }

            // Title pendek biar kebaca: "A 10:00-19:00 • HADIR"
            $title = ($isOverride ? 'OVR ' : '') . "{$shift->code} {$shift->start_time}-{$shift->end_time}";
            if ($statusLabel)
                $title .= " • {$statusLabel}";

            // Warna:
            // Base shift: A=green, B=orange
            // Status overlay: HADIR=blue, ALPHA=gray, CUTI=purple, SAKIT=red
            // Override: pink (biar beda dari SAKIT)
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

            // Override hanya mengganti warna jika status masih schedule (biar status tetap dominan)
            if ($isOverride && $status === 'SCHEDULE') {
                $bg = '#f472b6'; // pink
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
                    'is_override' => $isOverride,
                    'override_reason' => $isOverride ? ($overrides[$dateStr]->reason ?? null) : null,
                ],
            ];
        }

        return response()->json($events);
    }
}