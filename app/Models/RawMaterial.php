<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'stock_on_hand',
        'reserved_stock',
        'min_stock',
        'default_cost',
        'is_active',
    ];

    protected static function booted()
    {
        static::updated(function (RawMaterial $material) {
            // Check if stock_on_hand was changed
            if ($material->wasChanged('stock_on_hand')) {
                $oldStock = (float) $material->getOriginal('stock_on_hand');
                $newStock = (float) $material->stock_on_hand;
                $minStock = (float) ($material->min_stock ?? 0);

                // Send alert only if the stock was reduced and is at or below the minimum stock limit
                if ($newStock <= $minStock && $newStock < $oldStock) {
                    try {
                        $fonnte = app(\App\Services\FonnteService::class);
                        if ($fonnte->isEnabled()) {
                            $msg = implode("\n", [
                                "⚠️ *NOTIFIKASI STOK MINIMUM* ⚠️",
                                "",
                                "Bahan baku berikut telah mencapai atau berada di bawah batas minimum stok!",
                                "",
                                "• *Nama Bahan* : " . $material->name,
                                "• *Stok Saat Ini* : " . number_format($newStock, 2) . " " . $material->unit,
                                "• *Batas Minimum* : " . number_format($minStock, 2) . " " . $material->unit,
                                "",
                                "Mohon segera melakukan pemesanan ulang (restock) bahan baku ini agar operasional restoran tetap berjalan lancar.",
                                "Terima kasih."
                            ]);
                            $fonnte->sendToDefaultGroup($msg);
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error("Fonnte Alert Error for '{$material->name}': " . $e->getMessage());
                    }
                }
            }
        });
    }

    protected $casts = [
        'stock_on_hand' => 'float',
        'reserved_stock' => 'float',
        'min_stock' => 'float',
        'default_cost' => 'float',
        'is_active' => 'boolean',
    ];

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getAvailableStockAttribute(): float
    {
        return max(0, (float) $this->stock_on_hand - (float) $this->reserved_stock);
    }
}