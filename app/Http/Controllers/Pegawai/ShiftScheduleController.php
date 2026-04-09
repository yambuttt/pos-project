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

        // 1) Attendance map (date -> attendance)
        $attMap = \App\Models\Attendance::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->copy()->subDay()->toDateString()])
            ->get()
            ->keyBy(fn($a) => (string) $a->date);

        // 2) Leave approved overlap range (cuti/sakit) -> expand per tanggal
        $leaveRows = \App\Models\LeaveRequest::query()
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
                    'type' => $lr->type,   // cuti/sakit
                    'reason' => $lr->reason,
                ];
            }
        }

        // 3) Override shift (biar title bisa kasih OVR)
        $overrides = \App\Models\UserShiftOverride::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'approved')
            ->get()
            ->keyBy(fn($x) => $x->date->toDateString());

        // 4) ✅ Checkout correction requests (pending/approved/rejected)
        $ccMap = \App\Models\CheckoutCorrectionRequest::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->copy()->subDay()->toDateString()])
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

            // window check-in/out (pakai resolver agar ikut telat approved)
            $wIn = $svc->getWindow($user, 'in', $shiftStart);
            $wOut = $svc->getWindow($user, 'out', $shiftStart); // ok dipakai untuk batas checkout juga

            $checkInTo = $wIn['to'];
            $checkOutTo = $wOut['to'];

            // status default
            $status = 'SCHEDULE'; // future / belum dievaluasi
            $statusReason = null;

            // 1) CUTI/SAKIT menang paling atas
            if (isset($leaveMap[$dateStr])) {
                $t = $leaveMap[$dateStr]['type'];
                $status = ($t === 'sakit') ? 'SAKIT' : 'CUTI';
                $statusReason = $leaveMap[$dateStr]['reason'] ?? null;
            } else {
                $att = $attMap[$dateStr] ?? null;

                // 2) Tidak ada check-in -> ALPHA jika lewat batas check-in
                if (!$att || !$att->check_in_at) {
                    if ($dateStr < $now->toDateString()) {
                        $status = 'ALPHA';
                    } elseif ($dateStr === $now->toDateString() && $now->gt($checkInTo)) {
                        $status = 'ALPHA';
                    } else {
                        $status = 'SCHEDULE';
                    }
                } else {
                    // 3) Ada check-in
                    if ($att->check_out_at) {
                        $status = 'HADIR';
                    } else {
                        // 4) check-in ada, checkout belum ada
                        $cc = $ccMap[$dateStr] ?? null;

                        if ($cc && $cc->status === 'rejected') {
                            // ✅ sesuai request kamu: reject => dianggap ALPHA
                            $status = 'ALPHA';
                            $statusReason = $cc->review_note ?? 'Koreksi checkout ditolak';
                        } else {
                            // pending koreksi => INCOMPLETE
                            if ($cc && $cc->status === 'pending') {
                                $status = 'INCOMPLETE';
                                $statusReason = 'Menunggu persetujuan koreksi checkout';
                            } else {
                                // belum ajukan koreksi: tentukan ONGOING / INCOMPLETE berdasarkan batas checkout
                                if ($dateStr < $now->toDateString()) {
                                    // hari sudah lewat -> incomplete (lupa checkout)
                                    $status = 'INCOMPLETE';
                                    $statusReason = 'Checkout tidak dilakukan';
                                } elseif ($dateStr === $now->toDateString()) {
                                    if ($now->gt($checkOutTo)) {
                                        $status = 'INCOMPLETE';
                                        $statusReason = 'Checkout tidak dilakukan';
                                    } else {
                                        $status = 'ONGOING';
                                    }
                                } else {
                                    // future (harusnya tidak terjadi karena check-in sudah ada), fallback
                                    $status = 'ONGOING';
                                }
                            }
                        }
                    }
                }
            }

            // title ringkas
            $title = ($isOverride ? 'OVR ' : '') . "{$shift->code} {$shift->start_time}-{$shift->end_time}";
            if ($status !== 'SCHEDULE' && $status !== 'ONGOING')
                $title .= " • {$status}";
            if ($status === 'ONGOING')
                $title .= " • ON";

            // warna
            // base shift
            $bg = ($shift->code === 'A') ? '#22c55e' : '#f59e0b';
            $text = '#0b0b0b';

            // status override color (status dominan)
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
            } // amber
            if ($status === 'ONGOING') {
                $bg = '#14b8a6';
                $text = '#0b0b0b';
            } // teal

            // override shift (hanya kalau tidak ada status khusus)
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
                    'is_override' => $isOverride,
                    'override_reason' => $isOverride ? ($overrides[$dateStr]->reason ?? null) : null,
                ],
            ];
        }

        return response()->json($events);
    }
}