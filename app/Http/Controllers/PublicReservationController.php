<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicReservationController extends Controller
{
    public function create()
    {
        $resources = ReservationResource::where('is_active', true)->orderBy('type')->orderBy('name')->get();

        // untuk REGULAR: pelanggan bisa pilih menu reguler
        $products = Product::where('is_active', true)->orderBy('name')->get(['id','name','price']);

        return view('reservations/create', compact('resources','products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reservation_resource_id' => ['required','exists:reservation_resources,id'],
            'customer_name' => ['required','string','max:120'],
            'customer_phone' => ['nullable','string','max:30'],

            // split date+time biar pasti bisa pilih jam di semua browser
            'start_date' => ['required','date'],
            'start_time' => ['required','date_format:H:i'],
            'end_date'   => ['required','date'],
            'end_time'   => ['required','date_format:H:i'],

            'pax' => ['nullable','integer','min:1'],
            'menu_type' => ['required','in:REGULAR,BUFFET'],

            // REGULAR
            'items' => ['nullable','array'],
            'items.*.product_id' => ['required_with:items','integer','exists:products,id'],
            'items.*.qty' => ['required_with:items','integer','min:1'],

            // BUFFET (sementara simple: input total menu manual dulu)
            'buffet_menu_total' => ['nullable','integer','min:0'],

            'notes' => ['nullable','string'],
        ]);

        $start = \Carbon\Carbon::parse($data['start_date'].' '.$data['start_time']);
        $end   = \Carbon\Carbon::parse($data['end_date'].' '.$data['end_time']);

        if ($end->lessThanOrEqualTo($start)) {
            return back()->withErrors(['end' => 'End harus setelah Start.'])->withInput();
        }

        $code = 'RSV-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $dpRatio = (float) config('reservations.dp_ratio', 0.5);

        return DB::transaction(function () use ($data, $start, $end, $code, $dpRatio) {

            // ambil resource + hitung buffer + cek bentrok
            $resource = ReservationResource::lockForUpdate()->findOrFail($data['reservation_resource_id']);
            $buffer = (int) ($resource->buffer_minutes ?? 0);

            $startWithBuffer = $start->copy()->subMinutes($buffer);
            $endWithBuffer   = $end->copy()->addMinutes($buffer);

            $conflict = Reservation::query()
                ->where('reservation_resource_id', $resource->id)
                ->whereIn('status', ['pending_dp','confirmed','checked_in'])
                ->where(function ($q) use ($startWithBuffer, $endWithBuffer) {
                    $q->where('start_at', '<', $endWithBuffer)
                      ->where('end_at', '>', $startWithBuffer);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw new \RuntimeException('Jadwal bentrok: resource sudah dibooking di waktu tersebut.');
            }

            // hitung rental otomatis:
            // - kalau flat_rate ada, pakai flat
            // - else kalau hourly_rate ada, pakai hourly * jam (dibulatkan ke atas per 60 menit)
            $rentalTotal = 0;
            $minutes = max(1, $start->diffInMinutes($end));
            if (!empty($resource->flat_rate)) {
                $rentalTotal = (int) $resource->flat_rate;
            } elseif (!empty($resource->hourly_rate)) {
                $hours = (int) ceil($minutes / 60);
                $rentalTotal = (int) $resource->hourly_rate * $hours;
            }

            $reservation = Reservation::create([
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

                if (count($items) === 0) {
                    throw new \RuntimeException('Untuk REGULAR, pilih minimal 1 menu.');
                }

                $productIds = collect($items)->pluck('product_id')->unique()->values();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($items as $it) {
                    $p = $products[$it['product_id']] ?? null;
                    if (!$p || !$p->is_active) {
                        throw new \RuntimeException('Ada menu yang tidak valid / nonaktif.');
                    }

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
            } else {
                // BUFFET: sementara input total manual (nanti kita ganti ke paket buffet + inventory buffet)
                $menuTotal = (int) ($data['buffet_menu_total'] ?? 0);

                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'item_type' => 'BUFFET_PACKAGE',
                    'item_id' => null,
                    'snapshot_name' => 'Buffet (Custom)',
                    'unit_price' => $menuTotal,
                    'qty' => 1,
                    'subtotal' => $menuTotal,
                    'meta' => [
                        'pax' => $reservation->pax,
                    ],
                ]);
            }

            $grand = $menuTotal + $rentalTotal;
            $dp = (int) round($grand * $dpRatio);

            $reservation->update([
                'menu_total' => $menuTotal,
                'grand_total' => $grand,
                'dp_amount' => $dp,
                'paid_amount' => 0,
            ]);

            // redirect ke halaman status reservasi (public)
            return redirect()->route('public.reservations.show', $reservation->code);
        });
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['resource','items']);
        return view('reservations/show', compact('reservation'));
    }
}