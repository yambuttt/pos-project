<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DedupeAttendances extends Command
{
    protected $signature = 'attendance:dedupe {--dry-run : Hanya tampilkan, tanpa mengubah data}';
    protected $description = 'Gabungkan attendance yang duplikat (user_id + date) menjadi 1 baris paling lengkap';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $groups = DB::table('attendances')
            ->select('user_id', 'date', DB::raw('COUNT(*) as c'))
            ->groupBy('user_id', 'date')
            ->having('c', '>', 1)
            ->get();

        if ($groups->isEmpty()) {
            $this->info('Tidak ada duplikat.');
            return self::SUCCESS;
        }

        $this->info('Duplikat ditemukan: '.$groups->count().' grup');

        foreach ($groups as $g) {
            $rows = DB::table('attendances')
                ->where('user_id', $g->user_id)
                ->where('date', $g->date)
                ->orderBy('id', 'asc')
                ->get();

            // pilih target row: yang paling lengkap (punya check_in_at + check_out_at kalau ada)
            $target = $rows->first();
            foreach ($rows as $r) {
                $scoreT = ($target->check_in_at ? 1 : 0) + ($target->check_out_at ? 1 : 0);
                $scoreR = ($r->check_in_at ? 1 : 0) + ($r->check_out_at ? 1 : 0);
                if ($scoreR > $scoreT) $target = $r;
            }

            // merge field dari row lain ke target kalau target kosong
            $merged = (array) $target;

            foreach ($rows as $r) {
                if ($r->id == $target->id) continue;

                foreach ([
                    'check_in_at','check_out_at',
                    'check_in_lat','check_in_lng','check_out_lat','check_out_lng',
                    'check_in_photo_path','check_out_photo_path',
                    'check_in_qr_id','check_out_qr_id',
                    'device','ip','device_hash'
                ] as $col) {
                    if (empty($merged[$col]) && !empty($r->$col)) {
                        $merged[$col] = $r->$col;
                    }
                }
            }

            $ids = $rows->pluck('id')->all();
            $this->line("user_id={$g->user_id} date={$g->date} rows=".implode(',', $ids)." -> keep {$target->id}");

            if (!$dry) {
                DB::table('attendances')->where('id', $target->id)->update($merged);
                DB::table('attendances')->where('user_id', $g->user_id)->where('date', $g->date)->where('id', '!=', $target->id)->delete();
            }
        }

        $this->info($dry ? 'DRY RUN selesai (tidak ada perubahan).' : 'Dedupe selesai (data sudah dibersihkan).');
        return self::SUCCESS;
    }
}