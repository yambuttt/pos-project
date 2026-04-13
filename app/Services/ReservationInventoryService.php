<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Reservation;
use App\Models\ReservationMaterialLock;
use Illuminate\Support\Collection;

class ReservationInventoryService
{
    public function multiplier(): int
    {
        return max(1, (int) config('reservations.min_stock_multiplier', 2));
    }

    /**
     * Preview kekurangan stok untuk rule reservasi:
     * available = stock_on_hand - reserved_stock - (min_stock * multiplier)
     *
     * @param array<int, float> $needs raw_material_id => qtyNeed
     * @return array<int, array<string, mixed>>
     */
    public function previewInsufficientForReservation(array $needs): array
    {
        $rawIds = collect(array_keys($needs))->values();
        if ($rawIds->isEmpty())
            return [];

        $materials = RawMaterial::whereIn('id', $rawIds)->get()->keyBy('id');

        $mul = $this->multiplier();
        $insufficient = [];

        foreach ($needs as $rid => $qtyNeed) {
            $m = $materials[$rid] ?? null;

            $stockOnHand = (float) ($m?->stock_on_hand ?? 0);
            $reservedStock = (float) ($m?->reserved_stock ?? 0);
            $minStock = (float) ($m?->min_stock ?? 0);

            $available = max(0, $stockOnHand - $reservedStock - ($minStock * $mul));

            if ($available + 1e-9 < (float) $qtyNeed) {
                $insufficient[] = [
                    'raw_material_id' => (int) $rid,
                    'name' => $m?->name ?? "Material#$rid",
                    'unit' => $m?->unit ?? '',
                    'need' => (float) $qtyNeed,
                    'available' => $available,
                    'stock' => $stockOnHand,
                    'reserved' => $reservedStock,
                    'min_effective' => $minStock * $mul,
                ];
            }
        }

        return $insufficient;
    }

    /**
     * Build kebutuhan bahan baku dari reservation REGULAR items.
     * Menggunakan relation Product->recipes (dan recipes->rawMaterial optional).
     *
     * @return array<int, float> raw_material_id => qtyNeed
     */
    public function buildNeedsFromReservation(Reservation $reservation): array
    {
        $reservation->loadMissing('items');

        $productIds = $reservation->items
            ->where('item_type', 'REGULAR_PRODUCT')
            ->pluck('item_id')
            ->filter()
            ->unique()
            ->values();

        if ($productIds->isEmpty())
            return [];

        $products = Product::with('recipes.rawMaterial')
            ->whereIn('id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $needs = [];

        foreach ($reservation->items as $it) {
            if ($it->item_type !== 'REGULAR_PRODUCT')
                continue;

            $p = $products[$it->item_id] ?? null;
            if (!$p || !$p->is_active) {
                throw new \RuntimeException("Produk tidak ditemukan / tidak aktif (ID {$it->item_id}).");
            }
            if ($p->recipes->count() === 0) {
                throw new \RuntimeException("Produk '{$p->name}' belum punya resep.");
            }

            $qty = (int) $it->qty;

            foreach ($p->recipes as $r) {
                $rid = (int) $r->raw_material_id;
                $needs[$rid] = ($needs[$rid] ?? 0) + ((float) $r->qty * $qty);
            }
        }

        return $needs;
    }

    /**
     * Validasi stok + lockForUpdate material yang dibutuhkan.
     * Rule reservasi:
     * available = stock_on_hand - reserved_stock - (min_stock * multiplier)
     */
    public function lockAndValidateMaterialsForReservation(array $needs): Collection
    {
        $rawIds = collect(array_keys($needs))->values();
        if ($rawIds->isEmpty())
            return collect();

        $materials = RawMaterial::whereIn('id', $rawIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $mul = $this->multiplier();
        $insufficient = [];

        foreach ($needs as $rid => $qtyNeed) {
            $m = $materials[$rid] ?? null;

            $stockOnHand = (float) ($m?->stock_on_hand ?? 0);
            $reservedStock = (float) ($m?->reserved_stock ?? 0);
            $minStock = (float) ($m?->min_stock ?? 0);

            $available = max(0, $stockOnHand - $reservedStock - ($minStock * $mul));

            if ($available + 1e-9 < (float) $qtyNeed) {
                $insufficient[] = [
                    'name' => $m?->name ?? "Material#$rid",
                    'unit' => $m?->unit ?? '',
                    'need' => (float) $qtyNeed,
                    'available' => $available,
                    'stock' => $stockOnHand,
                    'reserved' => $reservedStock,
                    'min_effective' => $minStock * $mul,
                ];
            }
        }

        if ($insufficient) {
            $msg = "Stok bahan kurang untuk RESERVASI (buffer min x{$mul}):\n";
            foreach ($insufficient as $x) {
                $msg .= "- {$x['name']} ({$x['unit']}): butuh {$x['need']}, tersedia {$x['available']} "
                    . "(stok {$x['stock']}, reserve {$x['reserved']}, min_effective {$x['min_effective']})\n";
            }
            throw new \RuntimeException($msg);
        }

        return $materials;
    }

    /**
     * DP paid (CONFIRMED) => lock stok (increase raw_material.reserved_stock)
     * + record per reservation locks.
     *
     * userId nullable (webhook) -> InventoryMovement.created_by bisa null
     */
    public function lockForReservation(Reservation $reservation, ?int $userId = null): void
    {
        if ($reservation->menu_type !== 'REGULAR')
            return;

        if ($reservation->status !== 'confirmed') {
            throw new \RuntimeException('Reservation harus status CONFIRMED untuk lock stok.');
        }

        // idempotent: kalau sudah ada lock rows, jangan lock dua kali
        if ($reservation->locks()->exists())
            return;

        $needs = $this->buildNeedsFromReservation($reservation);
        if (empty($needs))
            return;

        $materials = $this->lockAndValidateMaterialsForReservation($needs);

        foreach ($needs as $rid => $qtyNeed) {
            $m = $materials[$rid];

            $m->update([
                'reserved_stock' => (float) $m->reserved_stock + (float) $qtyNeed,
            ]);

            ReservationMaterialLock::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $m->id,
                'qty_locked' => (float) $qtyNeed,
                'qty_released' => 0,
                'qty_consumed' => 0,
            ]);

            InventoryMovement::create([
                'raw_material_id' => $m->id,
                'type' => 'reserve',
                'qty_in' => 0,
                'qty_out' => 0,
                'reference_type' => Reservation::class,
                'reference_id' => $reservation->id,
                'created_by' => $userId,
                'note' => 'Reserve (lock) for reservation ' . $reservation->code,
            ]);
        }

        $reservation->update([
            'stock_snapshot_at' => now(),
        ]);
    }

    /**
     * Cancel REGULAR => release all remaining lock back to reserved_stock pool.
     * release = locked - released - consumed
     */
    public function releaseReservationLocks(Reservation $reservation, ?int $userId = null): void
    {
        if ($reservation->menu_type !== 'REGULAR')
            return;

        $reservation->loadMissing('locks.rawMaterial');

        foreach ($reservation->locks as $lock) {
            $m = $lock->rawMaterial;
            if (!$m)
                continue;

            $locked = (float) $lock->qty_locked;
            $released = (float) $lock->qty_released;
            $consumed = (float) $lock->qty_consumed;

            $remaining = max(0, $locked - $released - $consumed);
            if ($remaining <= 0)
                continue;

            $m->update([
                'reserved_stock' => max(0, (float) $m->reserved_stock - $remaining),
            ]);

            $lock->update([
                'qty_released' => $released + $remaining,
            ]);

            InventoryMovement::create([
                'raw_material_id' => $m->id,
                'type' => 'release',
                'qty_in' => 0,
                'qty_out' => 0,
                'reference_type' => Reservation::class,
                'reference_id' => $reservation->id,
                'created_by' => $userId,
                'note' => 'Release lock (cancel) for reservation ' . $reservation->code,
            ]);
        }
    }

    /**
     * Checkout => consume locked stock:
     * stock_on_hand -= qty, reserved_stock -= qty
     * consume = locked - released - consumed
     */
    public function consumeOnCheckout(Reservation $reservation, ?int $userId = null): void
    {
        if ($reservation->menu_type !== 'REGULAR')
            return;

        $reservation->loadMissing('locks.rawMaterial');

        foreach ($reservation->locks as $lock) {
            $m = $lock->rawMaterial;
            if (!$m)
                continue;

            $locked = (float) $lock->qty_locked;
            $released = (float) $lock->qty_released;
            $consumed = (float) $lock->qty_consumed;

            $remaining = max(0, $locked - $released - $consumed);
            if ($remaining <= 0)
                continue;

            $stockOnHand = (float) $m->stock_on_hand;
            $reservedStock = (float) $m->reserved_stock;

            if ($reservedStock + 1e-9 < $remaining) {
                throw new \RuntimeException("Reserved stock '{$m->name}' tidak cukup saat checkout.");
            }
            if ($stockOnHand + 1e-9 < $remaining) {
                throw new \RuntimeException("Stock on hand '{$m->name}' tidak cukup saat checkout.");
            }

            $m->update([
                'stock_on_hand' => $stockOnHand - $remaining,
                'reserved_stock' => max(0, $reservedStock - $remaining),
            ]);

            $lock->update([
                'qty_consumed' => $consumed + $remaining,
            ]);

            InventoryMovement::create([
                'raw_material_id' => $m->id,
                'type' => 'commit_paid',
                'qty_in' => 0,
                'qty_out' => $remaining,
                'reference_type' => Reservation::class,
                'reference_id' => $reservation->id,
                'created_by' => $userId,
                'note' => 'Commit/consume on reservation checkout ' . $reservation->code,
            ]);
        }
    }
}