<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\User;
use App\Models\UserShiftOverride;
use App\Models\UserShiftRotation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\LateAttendanceRequest;
class ShiftResolverService
{
    public function resolveShiftForDate(User $user, Carbon $date): Shift
    {
        $tz = config('app.timezone', 'Asia/Jakarta');
        $date = $date->copy()->setTimezone($tz)->startOfDay();

        // 1) override (approved) menang
        $ov = UserShiftOverride::query()
            ->where('user_id', $user->id)
            ->whereDate('date', $date->toDateString())
            ->where('status', 'approved')
            ->first();

        if ($ov) {
            $shift = Shift::find($ov->shift_id);
            if ($shift)
                return $shift;
        }

        // 2) rotation kalau user pakai scheme rotation
        if (($user->shift_scheme ?? 'fixed') === 'rotation') {
            $rot = UserShiftRotation::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->latest('id')
                ->first();

            if ($rot) {
                $shift = $this->resolveFromRotation($rot, $date);
                if ($shift)
                    return $shift;
            }
        }

        // 3) fallback ke default_shift_id
        if ($user->default_shift_id) {
            $shift = Shift::find($user->default_shift_id);
            if ($shift)
                return $shift;
        }

        // terakhir: fallback ke shift A jika ada (biar tidak blank)
        $shiftA = Shift::where('code', 'A')->first();
        if ($shiftA)
            return $shiftA;

        throw new \RuntimeException('Shift belum tersedia. Jalankan seeder shift.');
    }

    protected function resolveFromRotation(UserShiftRotation $rot, Carbon $date): ?Shift
    {
        $firstShift = Shift::find($rot->first_shift_id);
        if (!$firstShift)
            return null;

        // shift lain = A<->B (asumsi cuma 2 shift)
        $otherShift = Shift::where('id', '!=', $firstShift->id)
            ->orderBy('id', 'asc')
            ->first();

        if (!$otherShift)
            return $firstShift;

        $start = Carbon::parse($rot->start_date)->startOfDay();
        $d = $date->copy()->startOfDay();

        if ($d->lt($start)) {
            // sebelum start_date, pakai first shift aja
            return $firstShift;
        }

        $type = $rot->rotation_type;

        if ($type === 'daily_alternate') {
            $diffDays = $start->diffInDays($d);
            return ($diffDays % 2 === 0) ? $firstShift : $otherShift;
        }

        if ($type === 'weekly_alternate') {
            // patokan minggu mulai Monday (default)
            $weekStart = ($rot->week_starts_on === 'sunday') ? Carbon::SUNDAY : Carbon::MONDAY;

            $s = $start->copy()->startOfWeek($weekStart);
            $t = $d->copy()->startOfWeek($weekStart);

            $diffWeeks = (int) floor($s->diffInDays($t) / 7);
            return ($diffWeeks % 2 === 0) ? $firstShift : $otherShift;
        }

        // unknown type -> fallback
        return $firstShift;
    }

    /**
     * Return window times (Carbon) untuk mode in/out.
     */
    public function getWindow(User $user, string $mode, ?Carbon $now = null): array
    {
        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = ($now ?: now())->copy()->setTimezone($tz);

        $shift = $this->resolveShiftForDate($user, $now);

        $dateStr = $now->toDateString();

        $start = Carbon::parse($dateStr . ' ' . $shift->start_time, $tz);
        $end = Carbon::parse($dateStr . ' ' . $shift->end_time, $tz);

        // support kalau suatu saat ada shift melewati midnight
        if ($end->lte($start)) {
            $end->addDay();
        }

        if ($mode === 'in') {
            $from = $start->copy()->subMinutes((int) $shift->checkin_early_minutes);
            $to = $start->copy()->addMinutes((int) $shift->checkin_late_minutes); // normal: mentok jam start

            // ✅ EXTEND kalau ada pengajuan telat APPROVED untuk hari itu
            $late = LateAttendanceRequest::query()
                ->where('user_id', $user->id)
                ->whereDate('date', $start->toDateString())
                ->where('status', 'approved')
                ->first();

            if ($late && $late->allowed_until_time) {
                $lateTo = \Carbon\Carbon::parse(
                    $start->toDateString() . ' ' . $late->allowed_until_time,
                    $tz
                );

                // ✅ cap maksimum telat 120 menit dari start shift (jam 12 kalau shift A jam 10)
                $cap = $start->copy()->addMinutes(120);
                if ($lateTo->gt($cap))
                    $lateTo = $cap;

                // extend batas check-in kalau lebih besar dari normal
                if ($lateTo->gt($to))
                    $to = $lateTo;
            }

            return compact('shift', 'start', 'end', 'from', 'to');
        }

        if ($mode === 'out') {
            $from = $end->copy()->subMinutes((int) $shift->checkout_early_minutes);
            $to = $end->copy()->addMinutes((int) $shift->checkout_late_minutes);

            // ✅ Kalau telat approved, geser JAM MULAI & JAM AKHIR checkout sebesar menit telat
            $late = LateAttendanceRequest::query()
                ->where('user_id', $user->id)
                ->whereDate('date', $start->toDateString())
                ->where('status', 'approved')
                ->first();

            if ($late && $late->allowed_until_time) {
                // hitung menit telat dari start shift -> allowed_until_time
                $lateTo = \Carbon\Carbon::parse($start->toDateString() . ' ' . $late->allowed_until_time, $tz);

                // menit telat minimal 0, maksimal 120 (sesuai cap telat kamu)
                $lateMinutes = $start->diffInMinutes($lateTo, false);
                if ($lateMinutes < 0)
                    $lateMinutes = 0;
                if ($lateMinutes > 120)
                    $lateMinutes = 120;

                if ($lateMinutes > 0) {
                    // ✅ geser window checkout
                    $from = $from->copy()->addMinutes($lateMinutes);
                    $to = $to->copy()->addMinutes($lateMinutes);
                }
            }

            return compact('shift', 'start', 'end', 'from', 'to');
        }

        throw new \InvalidArgumentException('Mode harus in/out');
    }

    /**
     * Lempar exception kalau sekarang di luar window.
     */
    public function enforceWindow(User $user, string $mode, ?Carbon $now = null): void
    {
        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = ($now ?: now())->copy()->setTimezone($tz);

        $w = $this->getWindow($user, $mode, $now);

        if ($now->lt($w['from'])) {
            if ($mode === 'in') {
                throw new \RuntimeException("Belum waktunya check-in. Mulai {$w['from']->format('H:i')}.");
            }
            throw new \RuntimeException("Belum waktunya check-out. Mulai {$w['from']->format('H:i')}.");
        }

        if ($now->gt($w['to'])) {
            if ($mode === 'in') {
                throw new \RuntimeException("Check-in ditutup. Maksimal {$w['to']->format('H:i')}.");
            }
            throw new \RuntimeException("Check-out ditutup. Maksimal {$w['to']->format('H:i')}.");
        }
    }
}