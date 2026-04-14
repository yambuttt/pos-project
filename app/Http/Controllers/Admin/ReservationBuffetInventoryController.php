<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\RawMaterial;
use App\Models\Reservation;
use App\Models\ReservationBuffetMovement;
use App\Models\ReservationBuffetStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationBuffetInventoryController extends Controller
{
    public function show(Reservation $reservation)
    {
        if ($reservation->menu_type !== 'BUFFET') {
            return redirect()->route('admin.reservations.show', $reservation)
                ->withErrors(['buffet' => 'Reservasi ini bukan BUFFET.']);
        }

        $reservation->load([
            'resource',
            'items',
            'buffetStocks.rawMaterial',
            'buffetMovements.rawMaterial',
        ]);

        $rawMaterials = \App\Models\RawMaterial::orderBy('name')->get(['id', 'name', 'unit', 'stock_on_hand']);

        /**
         * ============================
         * HITUNG KEBUTUHAN BAHAN (ESTIMASI) DARI PAKET + RECIPES
         * ============================
         */
        $needRows = [];

        $pkgItem = $reservation->items->firstWhere('item_type', 'BUFFET_PACKAGE');
        if ($pkgItem && $pkgItem->item_id) {
            $pkg = \App\Models\BuffetPackage::with(['items.product.recipes.rawMaterial'])
                ->find($pkgItem->item_id);

            if ($pkg) {
                // multiplier: kalau per_pax -> dikali pax, kalau per_event -> 1
                $pax = (int) ($reservation->pax ?? 0);
                $mult = $pkg->pricing_type === 'per_pax' ? max(1, $pax) : 1;

                // map stok buffet saat ini: raw_material_id => qty_on_hand
                $buffetStockMap = $reservation->buffetStocks->keyBy('raw_material_id');

                // Akumulasi kebutuhan bahan
                $needs = []; // raw_material_id => qty_need
                $meta = [];  // raw_material_id => ['name','unit']

                foreach ($pkg->items as $pi) {
                    $product = $pi->product;
                    if (!$product)
                        continue;

                    // Qty item paket:
                    // - kalau per_pax: dianggap qty per pax => total = qty * pax
                    // - kalau per_event: total = qty (sekali event)
                    $productServings = (int) $pi->qty * $mult;

                    foreach ($product->recipes as $r) {
                        $rid = (int) $r->raw_material_id;
                        $qtyNeed = (float) $r->qty * $productServings;

                        $needs[$rid] = ($needs[$rid] ?? 0) + $qtyNeed;

                        $rm = $r->rawMaterial;
                        if ($rm) {
                            $meta[$rid] = ['name' => $rm->name, 'unit' => $rm->unit, 'main_stock' => (float) $rm->stock_on_hand];
                        }
                    }
                }

                // format rows untuk view
                foreach ($needs as $rid => $qtyNeed) {
                    $inBuffet = (float) ($buffetStockMap[$rid]->qty_on_hand ?? 0);
                    $remaining = max(0, $qtyNeed - $inBuffet);

                    $needRows[] = [
                        'raw_material_id' => $rid,
                        'name' => $meta[$rid]['name'] ?? "Material #{$rid}",
                        'unit' => $meta[$rid]['unit'] ?? '',
                        'need' => $qtyNeed,
                        'in_buffet' => $inBuffet,
                        'remaining' => $remaining,
                        'main_stock' => $meta[$rid]['main_stock'] ?? 0,
                    ];
                }

                // sort: yang masih kurang taruh atas
                usort($needRows, function ($a, $b) {
                    return ($b['remaining'] <=> $a['remaining']);
                });
            }
        }

        return view('dashboard.admin.reservations.buffet_inventory', compact('reservation', 'rawMaterials', 'needRows'));
    }
    // 1) belanja untuk buffet (masuk ke inventory buffet saja)
    public function purchase(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $stock = ReservationBuffetStock::firstOrCreate(
                ['reservation_id' => $reservation->id, 'raw_material_id' => $data['raw_material_id']],
                ['qty_on_hand' => 0]
            );

            $stock->update([
                'qty_on_hand' => (float) $stock->qty_on_hand + (float) $data['qty'],
            ]);

            ReservationBuffetMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $data['raw_material_id'],
                'type' => 'purchase',
                'qty_in' => (float) $data['qty'],
                'qty_out' => 0,
                'unit_cost' => isset($data['unit_cost']) ? (float) $data['unit_cost'] : null,
                'note' => $data['note'] ?? 'Belanja bahan buffet',
                'created_by' => Auth::id(),
            ]);

            return back()->with('success', 'Belanja buffet dicatat.');
        });
    }

    // 2) transfer dari MAIN ke buffet (kurangi stock_on_hand main)
    public function transferFromMain(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $rm = RawMaterial::lockForUpdate()->findOrFail($data['raw_material_id']);
            $qty = (float) $data['qty'];

            if ((float) $rm->stock_on_hand + 1e-9 < $qty) {
                throw new \RuntimeException("Stok MAIN '{$rm->name}' tidak cukup untuk transfer.");
            }

            $rm->update([
                'stock_on_hand' => (float) $rm->stock_on_hand - $qty,
            ]);

            // log main inventory movement
            InventoryMovement::create([
                'raw_material_id' => $rm->id,
                'type' => 'adjustment',
                'qty_in' => 0,
                'qty_out' => $qty,
                'reference_type' => Reservation::class,
                'reference_id' => $reservation->id,
                'created_by' => Auth::id(),
                'note' => 'Transfer to BUFFET reservation ' . $reservation->code,
            ]);

            $stock = ReservationBuffetStock::firstOrCreate(
                ['reservation_id' => $reservation->id, 'raw_material_id' => $rm->id],
                ['qty_on_hand' => 0]
            );
            $stock->update(['qty_on_hand' => (float) $stock->qty_on_hand + $qty]);

            ReservationBuffetMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $rm->id,
                'type' => 'transfer_in',
                'qty_in' => $qty,
                'qty_out' => 0,
                'note' => $data['note'] ?? 'Transfer dari MAIN',
                'created_by' => Auth::id(),
            ]);

            return back()->with('success', 'Transfer dari MAIN berhasil.');
        });
    }

    // 3) return sisa dari buffet ke MAIN
    public function returnToMain(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $qty = (float) $data['qty'];

            $stock = ReservationBuffetStock::lockForUpdate()
                ->where('reservation_id', $reservation->id)
                ->where('raw_material_id', $data['raw_material_id'])
                ->first();

            if (!$stock || (float) $stock->qty_on_hand + 1e-9 < $qty) {
                throw new \RuntimeException('Stok buffet tidak cukup untuk return.');
            }

            $stock->update(['qty_on_hand' => (float) $stock->qty_on_hand - $qty]);

            $rm = RawMaterial::lockForUpdate()->findOrFail($data['raw_material_id']);
            $rm->update(['stock_on_hand' => (float) $rm->stock_on_hand + $qty]);

            InventoryMovement::create([
                'raw_material_id' => $rm->id,
                'type' => 'adjustment',
                'qty_in' => $qty,
                'qty_out' => 0,
                'reference_type' => Reservation::class,
                'reference_id' => $reservation->id,
                'created_by' => Auth::id(),
                'note' => 'Return from BUFFET reservation ' . $reservation->code,
            ]);

            ReservationBuffetMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $rm->id,
                'type' => 'return_to_main',
                'qty_in' => 0,
                'qty_out' => $qty,
                'note' => $data['note'] ?? 'Return sisa ke MAIN',
                'created_by' => Auth::id(),
            ]);

            return back()->with('success', 'Return ke MAIN berhasil.');
        });
    }

    // 4) consume pemakaian buffet
    public function consume(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $qty = (float) $data['qty'];

            $stock = ReservationBuffetStock::lockForUpdate()
                ->where('reservation_id', $reservation->id)
                ->where('raw_material_id', $data['raw_material_id'])
                ->first();

            if (!$stock || (float) $stock->qty_on_hand + 1e-9 < $qty) {
                throw new \RuntimeException('Stok buffet tidak cukup untuk consume.');
            }

            $stock->update(['qty_on_hand' => (float) $stock->qty_on_hand - $qty]);

            ReservationBuffetMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $data['raw_material_id'],
                'type' => 'consume',
                'qty_in' => 0,
                'qty_out' => $qty,
                'note' => $data['note'] ?? 'Pemakaian buffet',
                'created_by' => Auth::id(),
            ]);

            return back()->with('success', 'Consume buffet dicatat.');
        });
    }

    // 5) waste (buang)
    public function waste(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $qty = (float) $data['qty'];

            $stock = ReservationBuffetStock::lockForUpdate()
                ->where('reservation_id', $reservation->id)
                ->where('raw_material_id', $data['raw_material_id'])
                ->first();

            if (!$stock || (float) $stock->qty_on_hand + 1e-9 < $qty) {
                throw new \RuntimeException('Stok buffet tidak cukup untuk waste.');
            }

            $stock->update(['qty_on_hand' => (float) $stock->qty_on_hand - $qty]);

            ReservationBuffetMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $data['raw_material_id'],
                'type' => 'waste',
                'qty_in' => 0,
                'qty_out' => $qty,
                'note' => $data['note'] ?? 'Waste buffet',
                'created_by' => Auth::id(),
            ]);

            return back()->with('success', 'Waste buffet dicatat.');
        });
    }

    private function guardBuffet(Reservation $reservation): void
    {
        if ($reservation->menu_type !== 'BUFFET') {
            throw new \RuntimeException('Reservasi ini bukan BUFFET.');
        }
    }
}