<?php

namespace App\Http\Controllers;

use App\Models\BuffetPackage;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationResource;
use App\Services\ReservationInventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
class PublicReservationController extends Controller
{
    public function create()
    {
        $resources = \App\Models\ReservationResource::where('is_active', true)
            ->orderBy('type')->orderBy('name')->get();

        $buffetPackages = \App\Models\BuffetPackage::where('is_active', true)
            ->orderBy('name')->get();

        return view('reservations/create', compact('resources', 'buffetPackages'));
    }

    public function products(Request $request)
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:6', 'max:24'],
        ]);

        $q = trim((string) ($data['q'] ?? ''));
        $perPage = (int) ($data['per_page'] ?? 12);

        // Ambil produk aktif saja. Jangan panggil method berat di Blade.
        $query = \App\Models\Product::query()
            ->where('is_active', true)
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
            })
            ->orderBy('category')
            ->orderBy('name');

        $page = $query->paginate($perPage)->withQueryString();

        // Buat response ringan. Max availability:
        // - kalau kamu sudah punya $product->maxServingsFromStock() dan itu aman dipanggil,
        //   pakai itu. Karena ini hanya 12 item/page, tidak berat.
        $items = $page->getCollection()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (int) $p->price,
                'category' => $p->category,
                'description' => $p->description,
                'image_url' => method_exists($p, 'imageUrl') ? $p->imageUrl() : null,
                'max_available' => method_exists($p, 'maxServingsFromStock') ? (int) $p->maxServingsFromStock() : null,
            ];
        })->values();

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }

    /**
     * JSON availability untuk 1 tanggal:
     * - booked intervals
     * - open/close
     * - slot minutes
     * - min duration
     * - buffer
     */
    public function availability(Request $request)
    {
        $data = $request->validate([
            'reservation_resource_id' => ['required', 'exists:reservation_resources,id'],
            'date' => ['required', 'date'],
        ]);

        $resource = ReservationResource::findOrFail($data['reservation_resource_id']);

        $date = Carbon::parse($data['date'])->startOfDay();
        $open = config('reservation_hours.open', '10:00');
        $close = config('reservation_hours.close', '22:00');
        $slot = (int) config('reservation_hours.slot_minutes', 30);

        $dayStart = Carbon::parse($date->toDateString() . ' ' . $open);
        $dayEnd = Carbon::parse($date->toDateString() . ' ' . $close);

        $buffer = (int) ($resource->buffer_minutes ?? 0);

        $booked = Reservation::query()
            ->where('reservation_resource_id', $resource->id)
            ->whereIn('status', ['pending_dp', 'confirmed', 'checked_in'])
            ->where(function ($q) use ($dayStart, $dayEnd) {
                $q->where('start_at', '<', $dayEnd)->where('end_at', '>', $dayStart);
            })
            ->orderBy('start_at')
            ->get(['start_at', 'end_at'])
            ->map(function ($r) use ($buffer) {
                return [
                    'start' => Carbon::parse($r->start_at)->subMinutes($buffer)->format('H:i'),
                    'end' => Carbon::parse($r->end_at)->addMinutes($buffer)->format('H:i'),
                ];
            })
            ->values();

        return response()->json([
            'date' => $date->toDateString(),
            'open' => $open,
            'close' => $close,
            'slot_minutes' => $slot,
            'min_duration_minutes' => (int) $resource->min_duration_minutes,
            'buffer_minutes' => $buffer,
            'booked' => $booked,
        ]);
    }

    /**
     * JSON availability untuk range 14 hari (buat badge "penuh").
     * Return: { dates: [{date, is_full}] }
     */
    public function availabilityRange(Request $request)
    {
        $data = $request->validate([
            'reservation_resource_id' => ['required', 'exists:reservation_resources,id'],
            'start_date' => ['required', 'date'],
            'days' => ['nullable', 'integer', 'min:1', 'max:31'],
        ]);

        $resource = ReservationResource::findOrFail($data['reservation_resource_id']);
        $days = (int) ($data['days'] ?? 14);

        $open = config('reservation_hours.open', '10:00');
        $close = config('reservation_hours.close', '22:00');
        $slot = (int) config('reservation_hours.slot_minutes', 30);
        $buffer = (int) ($resource->buffer_minutes ?? 0);
        $minDur = (int) $resource->min_duration_minutes;

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $out = [];

        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i);
            $dayStart = Carbon::parse($d->toDateString() . ' ' . $open);
            $dayEnd = Carbon::parse($d->toDateString() . ' ' . $close);

            $booked = Reservation::query()
                ->where('reservation_resource_id', $resource->id)
                ->whereIn('status', ['pending_dp', 'confirmed', 'checked_in'])
                ->where(function ($q) use ($dayStart, $dayEnd) {
                    $q->where('start_at', '<', $dayEnd)->where('end_at', '>', $dayStart);
                })
                ->get(['start_at', 'end_at'])
                ->map(function ($r) use ($buffer) {
                    return [
                        'start' => Carbon::parse($r->start_at)->subMinutes($buffer),
                        'end' => Carbon::parse($r->end_at)->addMinutes($buffer),
                    ];
                });

            // cek apakah ada slot start yang memungkinkan min dur
            $hasSlot = false;
            $t = $dayStart->copy();
            while ($t->copy()->addMinutes($minDur)->lte($dayEnd)) {
                $startT = $t->copy();
                $endMin = $t->copy()->addMinutes($minDur);

                $overlap = $booked->contains(function ($b) use ($startT, $endMin) {
                    return $startT->lt($b['end']) && $endMin->gt($b['start']);
                });

                if (!$overlap) {
                    $hasSlot = true;
                    break;
                }
                $t->addMinutes($slot);
            }

            $out[] = [
                'date' => $d->toDateString(),
                'is_full' => !$hasSlot,
            ];
        }

        return response()->json(['dates' => $out]);
    }

    public function store(Request $request, ReservationInventoryService $invService)
    {
        // menu_type sekarang tidak dipilih user. Kita infer dari isi form.
        $data = $request->validate([
            'reservation_resource_id' => ['required', 'exists:reservation_resources,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],

            'start_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_date' => ['required', 'date'],
            'end_time' => ['required', 'date_format:H:i'],

            'pax' => ['nullable', 'integer', 'min:1'],

            // REGULAR (optional)
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'items.*.qty' => ['nullable', 'integer', 'min:1'],

            // BUFFET (optional)
            'buffet_package_id' => ['nullable', 'integer', 'exists:buffet_packages,id'],

            'notes' => ['nullable', 'string'],
        ]);

        $items = $data['items'] ?? [];
        $items = array_values(array_filter($items, fn($it) => !empty($it['product_id']) && !empty($it['qty'])));

        $hasRegular = count($items) > 0;
        $hasBuffet = !empty($data['buffet_package_id']);

        if (!$hasRegular && !$hasBuffet) {
            return back()->withErrors(['menu' => 'Pilih minimal salah satu: Paket Buffet atau Menu Regular.'])->withInput();
        }

        $menuType = $hasRegular && $hasBuffet ? 'MIXED' : ($hasRegular ? 'REGULAR' : 'BUFFET');

        $start = Carbon::parse($data['start_date'] . ' ' . $data['start_time']);
        $end = Carbon::parse($data['end_date'] . ' ' . $data['end_time']);

        if ($end->lessThanOrEqualTo($start)) {
            return back()->withErrors(['end' => 'End harus setelah Start.'])->withInput();
        }

        $resource = ReservationResource::findOrFail($data['reservation_resource_id']);

        // min durasi
        $minutes = $start->diffInMinutes($end);
        if ($minutes < (int) $resource->min_duration_minutes) {
            return back()->withErrors(['end' => "Durasi minimal {$resource->min_duration_minutes} menit."])->withInput();
        }

        // jam operasional dan 1 hari
        $open = config('reservation_hours.open', '10:00');
        $close = config('reservation_hours.close', '22:00');
        if ($start->toDateString() !== $end->toDateString()) {
            return back()->withErrors(['end' => 'Reservasi harus dalam hari yang sama.'])->withInput();
        }
        if ($start->format('H:i') < $open || $end->format('H:i') > $close) {
            return back()->withErrors(['start' => "Reservasi hanya jam {$open}–{$close}."])->withInput();
        }

        // slot minutes
        $slot = (int) config('reservation_hours.slot_minutes', 30);
        if ($slot > 0) {
            if (($start->minute % $slot) !== 0 || ($end->minute % $slot) !== 0) {
                return back()->withErrors(['start' => "Waktu harus kelipatan {$slot} menit."])->withInput();
            }
        }

        // Pre-check stok hanya untuk REGULAR part (buffet tidak cek stok)
        if ($hasRegular) {
            $productIds = collect($items)->pluck('product_id')->unique()->values();

            $products = Product::with('recipes')
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

        // Validasi buffet: paket ada, cek min pax kalau per_pax
        if ($hasBuffet) {
            $pkg = BuffetPackage::findOrFail((int) $data['buffet_package_id']);
            $pax = (int) ($data['pax'] ?? 0);

            if ($pkg->min_pax && $pax < (int) $pkg->min_pax) {
                return back()->withErrors(['pax' => "Paket '{$pkg->name}' minimal pax {$pkg->min_pax}."])->withInput();
            }
            if ($pkg->pricing_type === 'per_pax' && $pax <= 0) {
                return back()->withErrors(['pax' => "Paket '{$pkg->name}' butuh pax."])->withInput();
            }
        }

        $code = 'RSV-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $dpRatio = (float) config('reservations.dp_ratio', 0.5);

        return DB::transaction(function () use ($data, $items, $hasRegular, $hasBuffet, $menuType, $start, $end, $code, $dpRatio, $resource) {

            // cek bentrok + buffer
            $resource = ReservationResource::lockForUpdate()->findOrFail($resource->id);
            $buffer = (int) ($resource->buffer_minutes ?? 0);

            $startWithBuffer = $start->copy()->subMinutes($buffer);
            $endWithBuffer = $end->copy()->addMinutes($buffer);

            $conflict = Reservation::query()
                ->where('reservation_resource_id', $resource->id)
                ->whereIn('status', ['pending_dp', 'confirmed', 'checked_in'])
                ->where(function ($q) use ($startWithBuffer, $endWithBuffer) {
                    $q->where('start_at', '<', $endWithBuffer)->where('end_at', '>', $startWithBuffer);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw new \RuntimeException('Jadwal bentrok: resource sudah dibooking.');
            }

            // rental otomatis
            $rentalTotal = 0;
            $minutes = max(1, $start->diffInMinutes($end));
            if (!empty($resource->flat_rate))
                $rentalTotal = (int) $resource->flat_rate;
            elseif (!empty($resource->hourly_rate))
                $rentalTotal = (int) $resource->hourly_rate * (int) ceil($minutes / 60);

            $reservation = Reservation::create([
                'code' => $code,
                'reservation_resource_id' => $resource->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'start_at' => $start,
                'end_at' => $end,
                'pax' => $data['pax'] ?? null,
                'menu_type' => $menuType, // REGULAR / BUFFET / MIXED
                'status' => 'pending_dp',
                'rental_total' => $rentalTotal,
                'notes' => $data['notes'] ?? null,
                'created_by' => null,
            ]);

            $menuTotal = 0;

            // REGULAR part
            if ($hasRegular) {
                $productIds = collect($items)->pluck('product_id')->unique()->values();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($items as $it) {
                    $p = $products[$it['product_id']] ?? null;
                    if (!$p || !$p->is_active)
                        throw new \RuntimeException('Ada menu REGULAR invalid.');

                    $qty = (int) $it['qty'];
                    $unit = (int) $p->price;
                    $sub = $unit * $qty;
                    $menuTotal += $sub;

                    ReservationItem::create([
                        'reservation_id' => $reservation->id,
                        'item_type' => 'REGULAR_PRODUCT',
                        'item_id' => $p->id,
                        'snapshot_name' => $p->name,
                        'unit_price' => $unit,
                        'qty' => $qty,
                        'subtotal' => $sub,
                    ]);
                }
            }

            // BUFFET part (no stock check)
            if ($hasBuffet) {
                $pkg = BuffetPackage::with('items.product')->lockForUpdate()->findOrFail((int) $data['buffet_package_id']);
                $pax = (int) ($reservation->pax ?? 0);

                if ($pkg->pricing_type === 'per_pax') {
                    $buffetTotal = (int) $pkg->price * max(1, $pax);
                    $pkgQty = max(1, $pax);
                } else {
                    $buffetTotal = (int) $pkg->price;
                    $pkgQty = 1;
                }

                $menuTotal += $buffetTotal;

                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'item_type' => 'BUFFET_PACKAGE',
                    'item_id' => $pkg->id,
                    'snapshot_name' => $pkg->name,
                    'unit_price' => (int) $pkg->price,
                    'qty' => $pkgQty,
                    'subtotal' => $buffetTotal,
                    'meta' => [
                        'pricing_type' => $pkg->pricing_type,
                        'min_pax' => $pkg->min_pax,
                        'pax' => $pax ?: null,
                        'notes' => $pkg->notes ?? null,
                    ],
                ]);

                foreach ($pkg->items as $it) {
                    ReservationItem::create([
                        'reservation_id' => $reservation->id,
                        'item_type' => 'BUFFET_ITEM',
                        'item_id' => $it->product_id,
                        'snapshot_name' => $it->product?->name ?? 'Item Paket',
                        'unit_price' => 0,
                        'qty' => (int) $it->qty,
                        'subtotal' => 0,
                        'meta' => ['included' => true, 'note' => $it->note],
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