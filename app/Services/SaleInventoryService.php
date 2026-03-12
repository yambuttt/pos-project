<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Sale;
use Illuminate\Support\Collection;

class SaleInventoryService
{
    public function prepareFromItems(array $items): array
    {
        $productIds = collect($items)->pluck('product_id')->unique()->values();

        $products = Product::with('recipes.rawMaterial')
            ->whereIn('id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $subtotal = 0;
        $needs = [];

        foreach ($items as $it) {
            $product = $products[$it['product_id']] ?? null;

            if (!$product || !$product->is_active) {
                throw new \RuntimeException('Produk tidak ditemukan / tidak aktif.');
            }

            if ($product->recipes->count() === 0) {
                throw new \RuntimeException("Produk '{$product->name}' belum punya resep.");
            }

            $qty = (int) $it['qty'];
            $subtotal += ((int) $product->price * $qty);

            foreach ($product->recipes as $recipe) {
                $rid = (int) $recipe->raw_material_id;
                $needs[$rid] = ($needs[$rid] ?? 0) + ((float) $recipe->qty * $qty);
            }
        }

        return [$products, $needs, $subtotal];
    }

    public function lockAndValidateMaterials(array $needs): Collection
    {
        $rawIds = collect(array_keys($needs))->values();

        $materials = RawMaterial::whereIn('id', $rawIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $insufficient = [];

        foreach ($needs as $rid => $qtyNeed) {
            $m = $materials[$rid] ?? null;
            $stock = (float) ($m?->stock_on_hand ?? 0);

            if ($stock + 1e-9 < (float) $qtyNeed) {
                $insufficient[] = [
                    'name' => $m?->name ?? "Material#$rid",
                    'unit' => $m?->unit ?? '',
                    'need' => $qtyNeed,
                    'stock' => $stock,
                ];
            }
        }

        if ($insufficient) {
            $msg = "Stok bahan kurang:\n";
            foreach ($insufficient as $x) {
                $msg .= "- {$x['name']} ({$x['unit']}): butuh {$x['need']}, stok {$x['stock']}\n";
            }
            throw new \RuntimeException($msg);
        }

        return $materials;
    }

    public function reserve(array $needs, Collection $materials, Sale $sale, int $userId): void
    {
        foreach ($needs as $rid => $qtyNeed) {
            $m = $materials[$rid];

            $m->update([
                'stock_on_hand' => (float) $m->stock_on_hand - (float) $qtyNeed,
            ]);

            InventoryMovement::create([
                'raw_material_id' => $m->id,
                'type' => 'sale',
                'qty_in' => 0,
                'qty_out' => $qtyNeed,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'created_by' => $userId,
                'note' => 'Sale reserve: ' . $sale->invoice_no,
            ]);
        }
    }

    public function release(Sale $sale, ?int $userId = null): void
    {
        if ($sale->stock_released_at) {
            return;
        }

        $sale->loadMissing('items.product.recipes');

        $needs = [];

        foreach ($sale->items as $item) {
            foreach ($item->product->recipes as $recipe) {
                $rid = (int) $recipe->raw_material_id;
                $needs[$rid] = ($needs[$rid] ?? 0) + ((float) $recipe->qty * (int) $item->qty);
            }
        }

        $rawIds = collect(array_keys($needs))->values();

        $materials = RawMaterial::whereIn('id', $rawIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($needs as $rid => $qtyNeed) {
            $m = $materials[$rid];

            $m->update([
                'stock_on_hand' => (float) $m->stock_on_hand + (float) $qtyNeed,
            ]);

            InventoryMovement::create([
                'raw_material_id' => $m->id,
                'type' => 'payment_release',
                'qty_in' => $qtyNeed,
                'qty_out' => 0,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'created_by' => $userId,
                'note' => 'Release pending payment: ' . $sale->invoice_no,
            ]);
        }

        $sale->update([
            'stock_released_at' => now(),
        ]);
    }
}