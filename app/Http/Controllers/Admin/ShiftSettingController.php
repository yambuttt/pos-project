<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\User;
use App\Models\UserShiftOverride;
use App\Models\UserShiftRotation;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ShiftSettingController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $employees = User::query()
            ->where('role', 'pegawai')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($x) use ($q) {
                    $x->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $shifts = Shift::where('is_active', true)->orderBy('code')->get();

        return view('dashboard.admin.shifts.index', compact('employees', 'shifts', 'q'));
    }

    public function edit(User $user)
    {
        abort_unless($user->role === 'pegawai', 404);

        $shifts = Shift::where('is_active', true)->orderBy('code')->get();

        $rotation = UserShiftRotation::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $overrides = UserShiftOverride::query()
            ->with('shift')
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        return view('dashboard.admin.shifts.edit', compact('user', 'shifts', 'rotation', 'overrides'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'pegawai', 404);

        $data = $request->validate([
            'shift_scheme' => ['required', 'in:fixed,rotation'],
            'default_shift_id' => ['nullable', 'exists:shifts,id'],

            // rotation fields (optional kalau scheme=fixed)
            'rotation_type' => ['nullable', 'in:daily_alternate,weekly_alternate'],
            'rotation_start_date' => ['nullable', 'date'],
            'rotation_first_shift_id' => ['nullable', 'exists:shifts,id'],
            'rotation_week_starts_on' => ['nullable', 'in:monday,sunday'],
            'rotation_is_active' => ['nullable', 'boolean'],
        ]);

        // update user fields
        $user->shift_scheme = $data['shift_scheme'];
        $user->default_shift_id = $data['default_shift_id'] ?? null;
        $user->save();

        // handle rotation
        if ($data['shift_scheme'] === 'rotation') {
            // minimal validation for rotation config
            if (
                empty($data['rotation_type']) ||
                empty($data['rotation_start_date']) ||
                empty($data['rotation_first_shift_id'])
            ) {
                return back()->with('error', 'Untuk skema Rotation, rotation_type, start_date, dan first_shift wajib diisi.');
            }

            // matikan rotation aktif lama
            UserShiftRotation::where('user_id', $user->id)->where('is_active', true)->update(['is_active' => false]);

            UserShiftRotation::create([
                'user_id' => $user->id,
                'rotation_type' => $data['rotation_type'],
                'start_date' => $data['rotation_start_date'],
                'first_shift_id' => $data['rotation_first_shift_id'],
                'week_starts_on' => $data['rotation_week_starts_on'] ?? 'monday',
                'is_active' => true,
            ]);
        } else {
            // scheme fixed → nonaktifkan rotation aktif
            UserShiftRotation::where('user_id', $user->id)->where('is_active', true)->update(['is_active' => false]);
        }

        return redirect()
            ->route('admin.shifts.edit', $user)
            ->with('ok', 'Setting shift berhasil diperbarui.');
    }

    public function storeOverride(Request $request, User $user)
    {
        abort_unless($user->role === 'pegawai', 404);

        $data = $request->validate([
            'date' => ['required', 'date'],
            'shift_id' => ['required', 'exists:shifts,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        // Upsert (karena unique user_id+date)
        UserShiftOverride::updateOrCreate(
            ['user_id' => $user->id, 'date' => $data['date']],
            [
                'shift_id' => $data['shift_id'],
                'status' => 'approved',
                'reason' => $data['reason'] ?? null,
                'created_by' => auth()->id(),
            ]
        );

        return back()->with('ok', 'Override shift tersimpan.');
    }

    public function deleteOverride(UserShiftOverride $override)
    {
        $override->delete();
        return back()->with('ok', 'Override dihapus.');
    }

    public function calendar(\Illuminate\Http\Request $request, \App\Models\User $user)
    {
        abort_unless($user->role === 'pegawai', 404);

        $data = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $start = \Carbon\Carbon::parse($data['start'])->startOfDay();
        $end = \Carbon\Carbon::parse($data['end'])->startOfDay();

        $svc = app(\App\Services\ShiftResolverService::class);

        // ambil semua override dalam range biar hemat query
        $overrides = \App\Models\UserShiftOverride::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'approved')
            ->get()
            ->keyBy(fn($x) => $x->date->toDateString());

        $events = [];

        // FullCalendar minta end exclusive, jadi kita iterasi sampai < end
        for ($d = $start->copy(); $d->lt($end); $d->addDay()) {
            $dateStr = $d->toDateString();

            // resolve shift harian (override menang otomatis di service,
            // tapi kita pakai $overrides untuk label "Override")
            $shift = $svc->resolveShiftForDate($user, $d);

            $isOverride = isset($overrides[$dateStr]);

            // start/end waktu shift utk event calendar
            $tz = config('app.timezone', 'Asia/Jakarta');
            $shiftStart = \Carbon\Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);
            $shiftEnd = \Carbon\Carbon::parse($dateStr . ' ' . $shift->end_time, $tz);
            if ($shiftEnd->lte($shiftStart))
                $shiftEnd->addDay();

            // warna per shift
            $color = $shift->code === 'A' ? '#22c55e' : '#f59e0b'; // hijau / kuning (boleh kamu ubah)
            $textColor = '#0b0b0b';

            // override -> lebih “tegas”
            if ($isOverride) {
                $color = '#ef4444'; // merah
                $textColor = '#ffffff';
            }

            $title = ($isOverride ? 'OVR ' : '') . "{$shift->code} {$shift->start_time}-{$shift->end_time}";

            $events[] = [
                'id' => $dateStr,
                'title' => $title,
                'start' => $shiftStart->toIso8601String(),
                'end' => $shiftEnd->toIso8601String(),
                'allDay' => false,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => $textColor,
                'extendedProps' => [
                    'shift_code' => $shift->code,
                    'shift_name' => $shift->name,
                    'is_override' => $isOverride,
                    'override_reason' => $isOverride ? ($overrides[$dateStr]->reason ?? null) : null,
                ],
            ];
        }

        return response()->json($events);
    }
}