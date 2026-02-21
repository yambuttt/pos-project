<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\User;
use Illuminate\Support\Carbon;

class DummyPurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        /*
        |--------------------------------------------------------------------------
        | 1️⃣  BAHAN BAKU DAPUR
        |--------------------------------------------------------------------------
        */
        $materials = [
            ['name' => 'Beras', 'unit' => 'kg', 'default_unit_cost' => 14000],
            ['name' => 'Ayam', 'unit' => 'kg', 'default_unit_cost' => 45000],
            ['name' => 'Telur', 'unit' => 'kg', 'default_unit_cost' => 28000],
            ['name' => 'Minyak Goreng', 'unit' => 'liter', 'default_unit_cost' => 18000],
            ['name' => 'Cabai Merah', 'unit' => 'kg', 'default_unit_cost' => 38000],
            ['name' => 'Bawang Putih', 'unit' => 'kg', 'default_unit_cost' => 22000],
            ['name' => 'Gas LPG 12kg', 'unit' => 'tabung', 'default_unit_cost' => 210000],
            ['name' => 'Air Galon', 'unit' => 'galon', 'default_unit_cost' => 6000],
        ];

        foreach ($materials as $m) {
            RawMaterial::firstOrCreate(
                ['name' => $m['name']],
                [
                    'unit' => $m['unit'],
                    'stock' => 0,
                    'minimum_stock' => 10,
                    'default_unit_cost' => $m['default_unit_cost'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣  PURCHASE 1
        |--------------------------------------------------------------------------
        */

        $purchase1 = Purchase::create([
            'purchase_date' => Carbon::now()->subDays(3),
            'invoice_no' => 'INV-DUMMY-001',
            'source_type' => 'external',
            'source_name' => 'Pasar Minggu',
            'created_by' => $admin?->id,
        ]);

        $this->addItem($purchase1, 'Beras', 50, 14500);
        $this->addItem($purchase1, 'Ayam', 20, 46000);
        $this->addItem($purchase1, 'Cabai Merah', 10, 39000);

        /*
        |--------------------------------------------------------------------------
        | 3️⃣  PURCHASE 2
        |--------------------------------------------------------------------------
        */

        $purchase2 = Purchase::create([
            'purchase_date' => Carbon::now()->subDays(1),
            'invoice_no' => 'INV-DUMMY-002',
            'source_type' => 'external',
            'source_name' => 'Tokopedia',
            'created_by' => $admin?->id,
        ]);

        $this->addItem($purchase2, 'Gas LPG 12kg', 3, 215000);
        $this->addItem($purchase2, 'Minyak Goreng', 25, 18500);
        $this->addItem($purchase2, 'Air Galon', 10, 6500);

        /*
        |--------------------------------------------------------------------------
        | 4️⃣  PURCHASE 3
        |--------------------------------------------------------------------------
        */

        $purchase3 = Purchase::create([
            'purchase_date' => Carbon::now(),
            'invoice_no' => 'INV-DUMMY-003',
            'source_type' => 'external',
            'source_name' => 'Pasar Induk',
            'created_by' => $admin?->id,
        ]);

        $this->addItem($purchase3, 'Telur', 30, 29000);
        $this->addItem($purchase3, 'Bawang Putih', 15, 23000);
        $this->addItem($purchase3, 'Ayam', 15, 47000);
    }

    private function addItem($purchase, $materialName, $qty, $unitCost)
    {
        $material = RawMaterial::where('name', $materialName)->first();

        $subtotal = $qty * $unitCost;

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'raw_material_id' => $material->id,
            'qty' => $qty,
            'unit_cost' => $unitCost,
            'subtotal' => $subtotal,
        ]);

        // Tambah stok
        $material->increment('stock', $qty);
    }
}