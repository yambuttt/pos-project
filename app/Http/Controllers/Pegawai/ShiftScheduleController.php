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
        $svc = app(ShiftResolverService::class);

        $start = Carbon::parse($data['start'])->startOfDay();
        $end = Carbon::parse($data['end'])->startOfDay();

        // Ambil override range (approved)
        $overrides = UserShiftOverride::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'approved')
            ->get()
            ->keyBy(fn($x) => $x->date->toDateString());

        $tz = config('app.timezone', 'Asia/Jakarta');
        $events = [];

        for ($d = $start->copy(); $d->lt($end); $d->addDay()) {
            $dateStr = $d->toDateString();

            $shift = $svc->resolveShiftForDate($user, $d);
            $isOverride = isset($overrides[$dateStr]);

            $shiftStart = Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);
            $shiftEnd = Carbon::parse($dateStr . ' ' . $shift->end_time, $tz);
            if ($shiftEnd->lte($shiftStart)) $shiftEnd->addDay();

            // Label pendek biar kebaca
            $title = ($isOverride ? 'OVR ' : '') . "{$shift->code} {$shift->start_time}-{$shift->end_time}";

            // Warna
            $bg = ($shift->code === 'A') ? '#22c55e' : '#f59e0b';
            $text = '#0b0b0b';

            if ($isOverride) {
                $bg = '#ef4444';
                $text = '#ffffff';
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
                    'is_override' => $isOverride,
                    'override_reason' => $isOverride ? ($overrides[$dateStr]->reason ?? null) : null,
                ],
            ];
        }

        return response()->json($events);
    }
}