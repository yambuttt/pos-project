<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\RawMaterial;
use App\Models\Reservation;
use App\Models\ReservationInventoryMovement;
use App\Models\ReservationItem;
use App\Models\Room;
use App\Services\ReservationMaterialPlannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date');
        $status = $request->query('status');

        $q = Reservation::query()->latest();
        if ($date) $q->where('reservation_date', $date);
        if ($status) $q->where('status', $status);

        $reservations = $q->paginate(10)->withQueryString();

        return view('dashboard.admin.reservations.index', compact('reservations', 'date', 'status'));
    }

    public function create()
    {
        $tables = DiningTable::query()->where('is_active', true)->orderBy('name')->get();
        $rooms = Room::query()->where('is_active', true)->orderBy('name')->get();
        $products = Product::query()->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.admin.reservations.create', compact('tables','rooms','products'));
    }

    public function store(Request $request, ReservationMaterialPlannerService $planner)
    {
        $data = $request->validate([
            'reservable_type' => ['required', Rule::in(['table','room'])],
            'reservable_id' => ['required','integer'],

            'customer_name' => ['required','string','max:190'],
            'customer_phone' => ['required','string','max:40'],
            'party_size' => ['required','integer','min:1'],

            'reservation_date' => ['required','date'],
            'start_time' => ['required'],
            'duration_minutes' => ['required','integer','min:15','max:1440'],

            'note' => ['nullable','string'],

            'items' => ['nullable','array'],
            'items.*.product_id' => ['required_with:items','integer','exists:products,id'],
            'items.*.qty' => ['required_with:items','integer','min:1'],
            'items.*.note' => ['nullable','string','max:190'],
        ]);

        // validate reservable exists
        if ($data['reservable_type'] === 'table') {
            if (!DiningTable::where('id', $data['reservable_id'])->exists()) {
                return back()->withErrors(['reservable_id' => 'Meja tidak ditemukan.'])->withInput();
            }
        } else {
            if (!Room::where('id', $data['reservable_id'])->exists()) {
                return back()->withErrors(['reservable_id' => 'Ruangan tidak ditemukan.'])->withInput();
            }
        }

        $start = $data['start_time'];
        $end = date('H:i:s', strtotime($start) + ((int)$data['duration_minutes'] * 60));

        // overlap check
        $overlap = Reservation::where('reservable_type', $data['reservable_type'])
            ->where('reservable_id', $data['reservable_id'])
            ->where('reservation_date', $data['reservation_date'])
            ->whereIn('status', ['pending','confirmed','arrived'])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['start_time' => 'Slot bentrok. Pilih jam lain atau resource lain.'])->withInput();
        }

        DB::transaction(function () use ($data, $start, $end, &$reservation, $planner) {
            $reservation = Reservation::create([
                'reservation_code' => 'RSV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'reservable_type' => $data['reservable_type'],
                'reservable_id' => $data['reservable_id'],
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'party_size' => $data['party_size'],
                'reservation_date' => $data['reservation_date'],
                'start_time' => $start,
                'end_time' => $end,
                'duration_minutes' => $data['duration_minutes'],
                'status' => 'confirmed',
                'source' => 'admin',
                'note' => $data['note'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach (($data['items'] ?? []) as $it) {
                $p = Product::find($it['product_id']);
                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'product_id' => $p->id,
                    'qty' => (int)$it['qty'],
                    'price_snapshot' => (float) ($p->price ?? 0),
                    'note' => $it['note'] ?? null,
                ]);
            }

            $planner->rebuildRequirements($reservation);
        });

        return redirect()->route('admin.reservations.show', $reservation)->with('success', 'Reservasi berhasil dibuat.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load([
            'items.product',
            'requirements.rawMaterial',
            'inventoryMovements.rawMaterial',
            'creator'
        ]);

        // compute balances per raw material
        $balances = [];
        foreach ($reservation->inventoryMovements as $m) {
            $rmId = $m->raw_material_id;
            $in = in_array($m->type, ['purchase_in','allocate_from_main'], true) ? (float)$m->qty : 0.0;
            $out = in_array($m->type, ['consume','return_to_main','waste'], true) ? (float)$m->qty : 0.0;
            $balances[$rmId] = ($balances[$rmId] ?? 0) + $in - $out;
        }

        // list materials for input
        $materials = RawMaterial::query()->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.admin.reservations.show', compact('reservation','balances','materials'));
    }

    public function addMovement(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'raw_material_id' => ['required','integer','exists:raw_materials,id'],
            'type' => ['required', Rule::in(['purchase_in','allocate_from_main','consume','return_to_main','waste'])],
            'qty' => ['required','numeric','gt:0'],
            'note' => ['nullable','string'],
        ]);

        DB::transaction(function () use ($data, $reservation) {
            ReservationInventoryMovement::create([
                'reservation_id' => $reservation->id,
                'raw_material_id' => $data['raw_material_id'],
                'type' => $data['type'],
                'qty' => (float)$data['qty'],
                'note' => $data['note'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // If movement touches operational stock, adjust raw_materials + add InventoryMovement (type adjustment)
            if (in_array($data['type'], ['allocate_from_main','return_to_main'], true)) {
                $qty = (float) $data['qty'];

                if ($data['type'] === 'allocate_from_main') {
                    RawMaterial::where('id', $data['raw_material_id'])
                        ->update(['stock_on_hand' => DB::raw('stock_on_hand - ' . $qty)]);

                    \App\Models\InventoryMovement::create([
                        'raw_material_id' => $data['raw_material_id'],
                        'type' => 'adjustment',
                        'qty_in' => 0,
                        'qty_out' => $qty,
                        'reference_type' => Reservation::class,
                        'reference_id' => $reservation->id,
                        'created_by' => Auth::id(),
                        'note' => 'Allocate to reservation ' . $reservation->reservation_code,
                    ]);
                } else {
                    RawMaterial::where('id', $data['raw_material_id'])
                        ->update(['stock_on_hand' => DB::raw('stock_on_hand + ' . $qty)]);

                    \App\Models\InventoryMovement::create([
                        'raw_material_id' => $data['raw_material_id'],
                        'type' => 'adjustment',
                        'qty_in' => $qty,
                        'qty_out' => 0,
                        'reference_type' => Reservation::class,
                        'reference_id' => $reservation->id,
                        'created_by' => Auth::id(),
                        'note' => 'Return from reservation ' . $reservation->reservation_code,
                    ]);
                }
            }
        });

        return back()->with('success', 'Pergerakan inventory reservasi disimpan.');
    }

    /**
     * Create an operational Purchase labeled for this reservation,
     * then also record reservation inventory purchase_in movements.
     */
    public function createPurchase(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'source_type' => ['required', Rule::in(['supplier', 'external'])],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'source_name' => ['nullable', 'string', 'max:190'],
            'invoice_no' => ['nullable', 'string', 'max:190'],
            'purchase_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'integer', 'exists:raw_materials,id'],
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        // keep the same extra validations as PurchaseController
        if ($data['source_type'] === 'supplier' && empty($data['supplier_id'])) {
            return back()->withErrors(['supplier_id' => 'Pilih supplier jika source type = supplier.'])->withInput();
        }
        if ($data['source_type'] === 'external' && empty($data['source_name'])) {
            return back()->withErrors(['source_name' => 'Isi sumber pembelian.'])->withInput();
        }

        DB::transaction(function () use ($data, $reservation, &$purchase) {
            $purchase = Purchase::create([
                'reservation_id' => $reservation->id,
                'purpose' => 'reservation',
                'source_type' => $data['source_type'],
                'supplier_id' => $data['source_type'] === 'supplier' ? $data['supplier_id'] : null,
                'source_name' => $data['source_type'] === 'external' ? $data['source_name'] : null,
                'invoice_no' => $data['invoice_no'] ?? null,
                'purchase_date' => $data['purchase_date'],
                'total_amount' => 0,
                'created_by' => Auth::id(),
                'note' => $data['note'] ?? null,
            ]);

            $grandTotal = 0;

            foreach ($data['items'] as $item) {
                $qty = (float) $item['qty'];
                $unitCost = isset($item['unit_cost']) ? (float) $item['unit_cost'] : 0;
                $subtotal = $qty * $unitCost;
                $grandTotal += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'qty' => $qty,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                // Tambah stok operasional (existing behavior)
                RawMaterial::where('id', $item['raw_material_id'])
                    ->update(['stock_on_hand' => DB::raw('stock_on_hand + ' . $qty)]);

                // Log movement operasional: purchase
                \App\Models\InventoryMovement::create([
                    'raw_material_id' => $item['raw_material_id'],
                    'type' => 'purchase',
                    'qty_in' => $qty,
                    'qty_out' => 0,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'created_by' => Auth::id(),
                    'note' => 'Purchase (Reservation ' . $reservation->reservation_code . ')',
                ]);

                // Record into reservation inventory as purchase_in
                ReservationInventoryMovement::create([
                    'reservation_id' => $reservation->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'type' => 'purchase_in',
                    'qty' => $qty,
                    'note' => 'Purchase for reservation (Purchase #' . $purchase->id . ')',
                    'created_by' => Auth::id(),
                ]);

                // Then allocate from main to reservation (reduce operational stock) to avoid double count
                RawMaterial::where('id', $item['raw_material_id'])
                    ->update(['stock_on_hand' => DB::raw('stock_on_hand - ' . $qty)]);

                \App\Models\InventoryMovement::create([
                    'raw_material_id' => $item['raw_material_id'],
                    'type' => 'adjustment',
                    'qty_in' => 0,
                    'qty_out' => $qty,
                    'reference_type' => Reservation::class,
                    'reference_id' => $reservation->id,
                    'created_by' => Auth::id(),
                    'note' => 'Allocate to reservation ' . $reservation->reservation_code . ' (from purchase)',
                ]);

                ReservationInventoryMovement::create([
                    'reservation_id' => $reservation->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'type' => 'allocate_from_main',
                    'qty' => $qty,
                    'note' => 'Auto allocate from main (from purchase)',
                    'created_by' => Auth::id(),
                ]);
            }

            $purchase->update(['total_amount' => $grandTotal]);
        });

        return redirect()->route('admin.purchases.show', $purchase)->with('success', 'Purchase reservasi dibuat & inventory reservasi terisi.');
    }
}
