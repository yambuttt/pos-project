<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ReservationInventoryService;
use App\Models\BuffetPackage;
class PublicReservationController extends Controller
{
    public function create()
    {
        $resources = ReservationResource::where('is_active', true)->orderBy('type')->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price']);

        $buffetPackages = BuffetPackage::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'pricing_type', 'price', 'min_pax']);

        return view('reservations/create', compact('resources', 'products', 'buffetPackages'));
    }
    public function store(Request $request, \App\Services\ReservationInventoryService $invService)
    {
        $data = $request->validate([
            'reservation_resource_id' => ['required', 'exists:reservation_resources,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],

            'start_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_date' => ['required', 'date'],
            'end_time' => ['required', 'date_format:H:i'],

            'pax' => ['nullable', 'integer', 'min:1'],
            'menu_type' => ['required', 'in:REGULAR,BUFFET'],

            // REGULAR
            'items' => ['exclude_unless:menu_type,REGULAR', 'array', 'min:1'],
            'items.*.product_id' => ['exclude_unless:menu_type,REGULAR', 'required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['exclude_unless:menu_type,REGULAR', 'required', 'integer', 'min:1'],

            'buffet_package_id' => ['exclude_unless:menu_type,BUFFET', 'required', 'integer', 'exists:buffet_packages,id'],

            'notes' => ['nullable', 'string'],
        ]);

        $start = \Carbon\Carbon::parse($data['start_date'] . ' ' . $data['start_time']);
        $end = \Carbon\Carbon::parse($data['end_date'] . ' ' . $data['end_time']);

        if ($end->lessThanOrEqualTo($start)) {
            return back()->withErrors(['end' => 'End harus setelah Start.'])->withInput();
        }

        // Ambil resource untuk validasi rules (min durasi & jam operasional)
        $resource = \App\Models\ReservationResource::findOrFail($data['reservation_resource_id']);

        // min durasi
        $minutes = $start->diffInMinutes($end);
        if ($minutes < (int) $resource->min_duration_minutes) {
            return back()->withErrors([
                'end' => "Durasi minimal untuk resource ini adalah {$resource->min_duration_minutes} menit."
            ])->withInput();
        }

        // jam operasional
        $open = config('reservation_hours.open', '10:00');
        $close = config('reservation_hours.close', '22:00');

        if ($start->toDateString() !== $end->toDateString()) {
            return back()->withErrors(['end' => 'Reservasi harus dalam hari yang sama.'])->withInput();
        }

        $startTime = $start->format('H:i');
        $endTime = $end->format('H:i');

        if ($startTime < $open || $endTime > $close) {
            return back()->withErrors([
                'start' => "Reservasi hanya bisa di jam operasional {$open}–{$close}."
            ])->withInput();
        }

        // slot minutes (opsional)
        $slot = (int) config('reservation_hours.slot_minutes', 0);
        if ($slot > 0) {
            if (($start->minute % $slot) !== 0 || ($end->minute % $slot) !== 0) {
                return back()->withErrors([
                    'start' => "Waktu harus kelipatan {$slot} menit."
                ])->withInput();
            }
        }

        // Pre-check stok REGULAR (buffer min x2)
        if ($data['menu_type'] === 'REGULAR') {
            $items = $data['items'] ?? [];
            $items = array_values(array_filter($items, fn($it) => !empty($it['product_id'])));

            if (count($items) === 0) {
                return back()->withErrors(['items' => 'Untuk REGULAR, pilih minimal 1 menu.'])->withInput();
            }

            $productIds = collect($items)->pluck('product_id')->unique()->values();
            $products = \App\Models\Product::with('recipes')
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            $needs = [];

            foreach ($items as $it) {
                $p = $products[$it['product_id']] ?? null;
                if (!$p || !$p->is_active) {
                    return back()->withErrors(['items' => 'Ada menu yang tidak valid / nonaktif.'])->withInput();
                }
                if ($p->recipes->count() === 0) {
                    return back()->withErrors(['items' => "Produk '{$p->name}' belum punya resep."])->withInput();
                }

                $qty = (int) $it['qty'];
                foreach ($p->recipes as $r) {
                    $rid = (int) $r->raw_material_id;
                    $needs[$rid] = ($needs[$rid] ?? 0) + ((float) $r->qty * $qty);
                }
            }

            $insuf = $invService->previewInsufficientForReservation($needs);
            if (!empty($insuf)) {
                $mul = $invService->multiplier();
                $msg = "Stok tidak cukup untuk reservasi (buffer min x{$mul}):\n";
                foreach ($insuf as $x) {
                    $msg .= "- {$x['name']} ({$x['unit']}): butuh {$x['need']}, tersedia {$x['available']}\n";
                }
                return back()->withErrors(['items' => $msg])->withInput();
            }
        }

        // Validasi BUFFET: wajib pilih paket
        if ($data['menu_type'] === 'BUFFET') {
            if (empty($data['buffet_package_id'])) {
                return back()->withErrors(['buffet_package_id' => 'Pilih paket buffet.'])->withInput();
            }
        }

        $code = 'RSV-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $dpRatio = (float) config('reservations.dp_ratio', 0.5);

        return DB::transaction(function () use ($data, $start, $end, $code, $dpRatio, $resource) {

            // cek bentrok + buffer
            $resource = \App\Models\ReservationResource::lockForUpdate()->findOrFail($resource->id);
            $buffer = (int) ($resource->buffer_minutes ?? 0);

            $startWithBuffer = $start->copy()->subMinutes($buffer);
            $endWithBuffer = $end->copy()->addMinutes($buffer);

            $conflict = \App\Models\Reservation::query()
                ->where('reservation_resource_id', $resource->id)
                ->whereIn('status', ['pending_dp', 'confirmed', 'checked_in'])
                ->where(function ($q) use ($startWithBuffer, $endWithBuffer) {
                    $q->where('start_at', '<', $endWithBuffer)
                        ->where('end_at', '>', $startWithBuffer);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw new \RuntimeException('Jadwal bentrok: resource sudah dibooking di waktu tersebut.');
            }

            // rental otomatis
            $rentalTotal = 0;
            $minutes = max(1, $start->diffInMinutes($end));
            if (!empty($resource->flat_rate)) {
                $rentalTotal = (int) $resource->flat_rate;
            } elseif (!empty($resource->hourly_rate)) {
                $hours = (int) ceil($minutes / 60);
                $rentalTotal = (int) $resource->hourly_rate * $hours;
            }

            $reservation = \App\Models\Reservation::create([
                'code' => $code,
                'reservation_resource_id' => $resource->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'start_at' => $start,
                'end_at' => $end,
                'pax' => $data['pax'] ?? null,
                'menu_type' => $data['menu_type'],
                'status' => 'pending_dp',
                'rental_total' => $rentalTotal,
                'notes' => $data['notes'] ?? null,
                'created_by' => null,
            ]);

            $menuTotal = 0;

            if ($reservation->menu_type === 'REGULAR') {
                $items = $data['items'] ?? [];
                $items = array_values(array_filter($items, fn($it) => !empty($it['product_id'])));

                $productIds = collect($items)->pluck('product_id')->unique()->values();
                $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($items as $it) {
                    $p = $products[$it['product_id']] ?? null;
                    if (!$p || !$p->is_active) {
                        throw new \RuntimeException('Ada menu yang tidak valid / nonaktif.');
                    }

                    $qty = (int) $it['qty'];
                    $unit = (int) $p->price;
                    $sub = $unit * $qty;
                    $menuTotal += $sub;

                    \App\Models\ReservationItem::create([
                        'reservation_id' => $reservation->id,
                        'item_type' => 'REGULAR_PRODUCT',
                        'item_id' => $p->id,
                        'snapshot_name' => $p->name,
                        'unit_price' => $unit,
                        'qty' => $qty,
                        'subtotal' => $sub,
                    ]);
                }
            } else {
                // BUFFET: pakai paket
                $pkg = \App\Models\BuffetPackage::with('items.product')
                    ->lockForUpdate()
                    ->findOrFail((int) $data['buffet_package_id']);

                $pax = (int) ($reservation->pax ?? 0);

                if ($pkg->min_pax && $pax < (int) $pkg->min_pax) {
                    throw new \RuntimeException("Paket '{$pkg->name}' minimal pax {$pkg->min_pax}.");
                }

                if ($pkg->pricing_type === 'per_pax') {
                    if ($pax <= 0) {
                        throw new \RuntimeException("Paket '{$pkg->name}' butuh pax.");
                    }
                    $menuTotal = (int) $pkg->price * $pax;
                    $pkgQty = $pax; // qty = pax
                } else {
                    $menuTotal = (int) $pkg->price;
                    $pkgQty = 1;
                }

                \App\Models\ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'item_type' => 'BUFFET_PACKAGE',
                    'item_id' => $pkg->id,
                    'snapshot_name' => $pkg->name,
                    'unit_price' => (int) $pkg->price,
                    'qty' => $pkgQty,
                    'subtotal' => $menuTotal,
                    'meta' => [
                        'pricing_type' => $pkg->pricing_type,
                        'min_pax' => $pkg->min_pax,
                        'pax' => $pax ?: null,
                    ],
                ]);

                // breakdown item paket (informasi saja, subtotal 0)
                foreach ($pkg->items as $it) {
                    \App\Models\ReservationItem::create([
                        'reservation_id' => $reservation->id,
                        'item_type' => 'BUFFET_ITEM',
                        'item_id' => $it->product_id,
                        'snapshot_name' => $it->product?->name ?? 'Item Paket',
                        'unit_price' => 0,
                        'qty' => (int) $it->qty,
                        'subtotal' => 0,
                        'meta' => [
                            'included' => true,
                            'note' => $it->note,
                        ],
                    ]);
                }
            }

            $grand = $menuTotal + $rentalTotal;
            $dp = (int) round($grand * $dpRatio);

            $reservation->update([
                'menu_total' => $menuTotal,
                'grand_total' => $grand,
                'dp_amount' => $dp,
                'paid_amount' => 0,
            ]);

            return redirect()->route('public.reservations.show', $reservation->code);
        });
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['resource', 'items']);
        return view('reservations/show', compact('reservation'));
    }
}