<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // upsert by code
        DB::table('shifts')->upsert([
            [
                'code' => 'A',
                'name' => 'Shift A (10:00 - 19:00)',
                'start_time' => '10:00:00',
                'end_time' => '19:00:00',
                'checkin_early_minutes' => 120,
                'checkin_late_minutes' => 0,
                'checkout_early_minutes' => 0,
                'checkout_late_minutes' => 90,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'B',
                'name' => 'Shift B (13:00 - 22:00)',
                'start_time' => '13:00:00',
                'end_time' => '22:00:00',
                'checkin_early_minutes' => 120,
                'checkin_late_minutes' => 0,
                'checkout_early_minutes' => 0,
                'checkout_late_minutes' => 90,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['code'], [
            'name',
            'start_time',
            'end_time',
            'checkin_early_minutes',
            'checkin_late_minutes',
            'checkout_early_minutes',
            'checkout_late_minutes',
            'is_active',
            'updated_at',
        ]);
    }
}