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

        $reservation->load(['resource','items','buffetStocks.rawMaterial','buffetMovements.rawMaterial']);

        $rawMaterials = RawMaterial::orderBy('name')->get(['id','name','unit','stock_on_hand']);

        return view('dashboard.admin.reservations.buffet_inventory', compact('reservation','rawMaterials'));
    }

    // 1) belanja untuk buffet (masuk ke inventory buffet saja)
    public function purchase(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required','exists:raw_materials,id'],
            'qty' => ['required','numeric','min:0.01'],
            'unit_cost' => ['nullable','numeric','min:0'],
            'note' => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $stock = ReservationBuffetStock::firstOrCreate(
                ['reservation_id' => $reservation->id, 'raw_material_id' => $data['raw_material_id']],
                ['qty_on_hand' => 0]
            );

            $stock->update([
                'qty_on_hand' => (float)$stock->qty_on_hand + (float)$data['qty'],
            ]);

            ReservationBuffetMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $data['raw_material_id'],
                'type' => 'purchase',
                'qty_in' => (float)$data['qty'],
                'qty_out' => 0,
                'unit_cost' => isset($data['unit_cost']) ? (float)$data['unit_cost'] : null,
                'note' => $data['note'] ?? 'Belanja bahan buffet',
                'created_by' => Auth::id(),
            ]);

            return back()->with('success','Belanja buffet dicatat.');
        });
    }

    // 2) transfer dari MAIN ke buffet (kurangi stock_on_hand main)
    public function transferFromMain(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required','exists:raw_materials,id'],
            'qty' => ['required','numeric','min:0.01'],
            'note' => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $rm = RawMaterial::lockForUpdate()->findOrFail($data['raw_material_id']);
            $qty = (float)$data['qty'];

            if ((float)$rm->stock_on_hand + 1e-9 < $qty) {
                throw new \RuntimeException("Stok MAIN '{$rm->name}' tidak cukup untuk transfer.");
            }

            $rm->update([
                'stock_on_hand' => (float)$rm->stock_on_hand - $qty,
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
            $stock->update(['qty_on_hand' => (float)$stock->qty_on_hand + $qty]);

            ReservationBuffetMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $rm->id,
                'type' => 'transfer_in',
                'qty_in' => $qty,
                'qty_out' => 0,
                'note' => $data['note'] ?? 'Transfer dari MAIN',
                'created_by' => Auth::id(),
            ]);

            return back()->with('success','Transfer dari MAIN berhasil.');
        });
    }

    // 3) return sisa dari buffet ke MAIN
    public function returnToMain(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required','exists:raw_materials,id'],
            'qty' => ['required','numeric','min:0.01'],
            'note' => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $qty = (float)$data['qty'];

            $stock = ReservationBuffetStock::lockForUpdate()
                ->where('reservation_id', $reservation->id)
                ->where('raw_material_id', $data['raw_material_id'])
                ->first();

            if (!$stock || (float)$stock->qty_on_hand + 1e-9 < $qty) {
                throw new \RuntimeException('Stok buffet tidak cukup untuk return.');
            }

            $stock->update(['qty_on_hand' => (float)$stock->qty_on_hand - $qty]);

            $rm = RawMaterial::lockForUpdate()->findOrFail($data['raw_material_id']);
            $rm->update(['stock_on_hand' => (float)$rm->stock_on_hand + $qty]);

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

            return back()->with('success','Return ke MAIN berhasil.');
        });
    }

    // 4) consume pemakaian buffet
    public function consume(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required','exists:raw_materials,id'],
            'qty' => ['required','numeric','min:0.01'],
            'note' => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $qty = (float)$data['qty'];

            $stock = ReservationBuffetStock::lockForUpdate()
                ->where('reservation_id', $reservation->id)
                ->where('raw_material_id', $data['raw_material_id'])
                ->first();

            if (!$stock || (float)$stock->qty_on_hand + 1e-9 < $qty) {
                throw new \RuntimeException('Stok buffet tidak cukup untuk consume.');
            }

            $stock->update(['qty_on_hand' => (float)$stock->qty_on_hand - $qty]);

            ReservationBuffetMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $data['raw_material_id'],
                'type' => 'consume',
                'qty_in' => 0,
                'qty_out' => $qty,
                'note' => $data['note'] ?? 'Pemakaian buffet',
                'created_by' => Auth::id(),
            ]);

            return back()->with('success','Consume buffet dicatat.');
        });
    }

    // 5) waste (buang)
    public function waste(Reservation $reservation, Request $request)
    {
        $this->guardBuffet($reservation);

        $data = $request->validate([
            'raw_material_id' => ['required','exists:raw_materials,id'],
            'qty' => ['required','numeric','min:0.01'],
            'note' => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($reservation, $data) {
            $qty = (float)$data['qty'];

            $stock = ReservationBuffetStock::lockForUpdate()
                ->where('reservation_id', $reservation->id)
                ->where('raw_material_id', $data['raw_material_id'])
                ->first();

            if (!$stock || (float)$stock->qty_on_hand + 1e-9 < $qty) {
                throw new \RuntimeException('Stok buffet tidak cukup untuk waste.');
            }

            $stock->update(['qty_on_hand' => (float)$stock->qty_on_hand - $qty]);

            ReservationBuffetMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $data['raw_material_id'],
                'type' => 'waste',
                'qty_in' => 0,
                'qty_out' => $qty,
                'note' => $data['note'] ?? 'Waste buffet',
                'created_by' => Auth::id(),
            ]);

            return back()->with('success','Waste buffet dicatat.');
        });
    }

    private function guardBuffet(Reservation $reservation): void
    {
        if ($reservation->menu_type !== 'BUFFET') {
            throw new \RuntimeException('Reservasi ini bukan BUFFET.');
        }
    }
}