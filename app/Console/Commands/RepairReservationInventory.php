<?php

namespace App\Console\Commands;

use App\Models\InventoryMovement;
use App\Models\RawMaterial;
use App\Models\Reservation;
use App\Models\ReservationMaterialLock;
use App\Services\ReservationInventoryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairReservationInventory extends Command
{
    protected $signature = 'reservations:repair-inventory
        {reservation? : ID atau kode reservasi tertentu}
        {--apply : Jalankan perubahan. Tanpa opsi ini hanya dry-run}';

    protected $description = 'Repair lock/consume inventory untuk reservasi REGULAR/MIXED yang belum punya material locks';

    public function handle(ReservationInventoryService $inventory): int
    {
        $apply = (bool) $this->option('apply');
        $target = $this->argument('reservation');

        $query = Reservation::query()
            ->whereIn('menu_type', ['REGULAR', 'MIXED'])
            ->whereIn('status', ['confirmed', 'checked_in', 'completed'])
            ->whereDoesntHave('locks');

        if ($target) {
            $query->where(function ($q) use ($target) {
                $q->where('code', $target);

                if (ctype_digit((string) $target)) {
                    $q->orWhere('id', (int) $target);
                }
            });
        }

        $reservations = $query->orderBy('id')->get();

        if ($reservations->isEmpty()) {
            $this->info('Tidak ada reservasi yang perlu direpair.');
            return self::SUCCESS;
        }

        $this->info(($apply ? 'APPLY' : 'DRY RUN') . ": {$reservations->count()} reservasi kandidat.");

        foreach ($reservations as $reservation) {
            $needs = $inventory->buildNeedsFromReservation($reservation);
            $needText = collect($needs)
                ->map(fn ($qty, $rid) => "material#{$rid}={$qty}")
                ->implode(', ');

            $this->line("Reservation {$reservation->id} {$reservation->code} [{$reservation->status}] needs: {$needText}");

            if (!$apply) {
                continue;
            }

            DB::transaction(function () use ($reservation, $inventory) {
                $lockedReservation = Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();

                if ($lockedReservation->locks()->exists()) {
                    return;
                }

                if (in_array($lockedReservation->status, ['confirmed', 'checked_in'], true)) {
                    $inventory->lockForReservation($lockedReservation, null);
                    return;
                }

                $needs = $inventory->buildNeedsFromReservation($lockedReservation);
                if (empty($needs)) {
                    return;
                }

                $materials = RawMaterial::whereIn('id', array_keys($needs))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($needs as $rawMaterialId => $qtyNeed) {
                    $material = $materials[$rawMaterialId] ?? null;

                    if (!$material) {
                        throw new \RuntimeException("Material ID {$rawMaterialId} tidak ditemukan.");
                    }

                    $qtyNeed = (float) $qtyNeed;
                    $stockOnHand = (float) $material->stock_on_hand;

                    if ($stockOnHand + 1e-9 < $qtyNeed) {
                        throw new \RuntimeException("Stock on hand '{$material->name}' tidak cukup untuk repair completed reservation {$lockedReservation->code}.");
                    }

                    ReservationMaterialLock::create([
                        'reservation_id' => $lockedReservation->id,
                        'raw_material_id' => $material->id,
                        'qty_locked' => $qtyNeed,
                        'qty_released' => 0,
                        'qty_consumed' => $qtyNeed,
                    ]);

                    InventoryMovement::create([
                        'raw_material_id' => $material->id,
                        'type' => 'reserve',
                        'qty_in' => 0,
                        'qty_out' => 0,
                        'reference_type' => Reservation::class,
                        'reference_id' => $lockedReservation->id,
                        'created_by' => null,
                        'note' => 'Repair reserve for completed reservation ' . $lockedReservation->code,
                    ]);

                    $material->update([
                        'stock_on_hand' => $stockOnHand - $qtyNeed,
                    ]);

                    InventoryMovement::create([
                        'raw_material_id' => $material->id,
                        'type' => 'commit_paid',
                        'qty_in' => 0,
                        'qty_out' => $qtyNeed,
                        'reference_type' => Reservation::class,
                        'reference_id' => $lockedReservation->id,
                        'created_by' => null,
                        'note' => 'Repair consume for completed reservation ' . $lockedReservation->code,
                    ]);
                }

                $lockedReservation->update([
                    'stock_snapshot_at' => $lockedReservation->stock_snapshot_at ?? now(),
                ]);
            });
        }

        $this->info($apply ? 'Repair selesai.' : 'Dry-run selesai. Jalankan lagi dengan --apply untuk mengubah data.');
        return self::SUCCESS;
    }
}
