<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationPayment;
use App\Models\ReservationResource;
use App\Models\Product;
use App\Services\ReservationInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $status = trim((string) $request->get('status'));

        $rows = Reservation::query()
            ->with('resource')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('code', 'like', "%$q%")
                    ->orWhere('customer_name', 'like', "%$q%")
                    ->orWhere('customer_phone', 'like', "%$q%");
            })
            ->when($status !== '', fn($qq) => $qq->where('status', $status))
            ->orderBy('start_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.admin.reservations.index', compact('rows', 'q', 'status'));
    }

    public function create()
    {
        $resources = ReservationResource::where('is_active', true)->orderBy('type')->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price']);

        return view('dashboard.admin.reservations.create', compact('resources', 'products'));
    }

    public function store(Request $request, ReservationInventoryService $inv)
    {
        $data = $request->validate([
            'reservation_resource_id' => ['required', 'exists:reservation_resources,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'pax' => ['nullable', 'integer', 'min:1'],
            'menu_type' => ['required', 'in:REGULAR,BUFFET'],

            // REGULAR items
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required_with:items', 'integer', 'min:1'],

            'rental_total' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        // generate code sederhana
        $code = 'RSV-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $dpRatio = (float) config('reservations.dp_ratio', 0.5);

        return DB::transaction(function () use ($data, $code, $dpRatio) {

            // ============================================================
            // (5) CEK BENTROK JADWAL RESOURCE + BUFFER
            // ============================================================
            $start = \Carbon\Carbon::parse($data['start_at']);
            $end = \Carbon\Carbon::parse($data['end_at']);

            /** @var \App\Models\ReservationResource $resource */
            $resource = \App\Models\ReservationResource::lockForUpdate()
                ->findOrFail($data['reservation_resource_id']);

            $buffer = (int) ($resource->buffer_minutes ?? 0);

            // kalau ada buffer, kita longgarkan window check
            $startWithBuffer = $start->copy()->subMinutes($buffer);
            $endWithBuffer = $end->copy()->addMinutes($buffer);

            $conflict = \App\Models\Reservation::query()
                ->where('reservation_resource_id', $resource->id)
                ->whereIn('status', ['pending_dp', 'confirmed', 'checked_in']) // yang dianggap "aktif"
                ->where(function ($q) use ($startWithBuffer, $endWithBuffer) {
                    // overlap rule: start < existing_end AND end > existing_start
                    $q->where('start_at', '<', $endWithBuffer)
                        ->where('end_at', '>', $startWithBuffer);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw new \RuntimeException('Jadwal bentrok: resource sudah dibooking di rentang waktu tersebut.');
            }
            // ============================================================

            $menuTotal = 0;

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
                'rental_total' => (int) ($data['rental_total'] ?? 0),
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            if ($reservation->menu_type === 'REGULAR') {
                $items = $data['items'] ?? [];

                // buang baris item kosong (product_id kosong)
                $items = array_values(array_filter($items, fn($it) => !empty($it['product_id'])));

                if (count($items) === 0) {
                    throw new \RuntimeException('Menu REGULAR wajib pilih minimal 1 item.');
                }

                $productIds = collect($items)->pluck('product_id')->unique()->values();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($items as $it) {
                    $p = $products[$it['product_id']] ?? null;
                    if (!$p) {
                        throw new \RuntimeException("Produk tidak ditemukan (ID {$it['product_id']}).");
                    }
                    if (!$p->is_active) {
                        throw new \RuntimeException("Produk '{$p->name}' sedang nonaktif.");
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
            }

            $grand = $menuTotal + (int) $reservation->rental_total;
            $dp = (int) round($grand * $dpRatio);

            $reservation->update([
                'menu_total' => $menuTotal,
                'grand_total' => $grand,
                'dp_amount' => $dp,
                'paid_amount' => 0,
            ]);

            return redirect()
                ->route('admin.reservations.show', $reservation)
                ->with('success', "Reservasi dibuat. Kode: {$reservation->code}. Menunggu DP.");
        });
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['resource', 'items', 'payments', 'locks.rawMaterial']);
        return view('dashboard.admin.reservations.show', compact('reservation'));
    }

    /**
     * Mark DP paid (manual dulu). Nanti bisa dihubungkan Midtrans DP.
     * Jika REGULAR => auto lock stok (min*2 rule).
     */
    public function markDpPaid(Reservation $reservation, Request $request, ReservationInventoryService $inv)
    {
        $data = $request->validate([
            'method' => ['required', 'in:CASH,QRIS,MIDTRANS'],
            'amount' => ['required', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:120'],
        ]);

        return DB::transaction(function () use ($reservation, $data, $inv) {
            if (!in_array($reservation->status, ['pending_dp', 'draft'], true)) {
                throw new \RuntimeException('Status reservasi tidak valid untuk DP paid.');
            }

            $reservation->update([
                'status' => 'confirmed',
                'dp_paid_at' => now(),
                'paid_amount' => (int) $reservation->paid_amount + (int) $data['amount'],
            ]);

            ReservationPayment::create([
                'reservation_id' => $reservation->id,
                'type' => 'DP',
                'amount' => (int) $data['amount'],
                'method' => $data['method'],
                'status' => 'paid',
                'reference' => $data['reference'] ?? null,
                'paid_at' => now(),
            ]);

            // lock stok kalau REGULAR
            $inv->lockForReservation($reservation->fresh(), Auth::id());

            return back()->with('success', 'DP berhasil dicatat, reservasi CONFIRMED.');
        });
    }

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return back()->withErrors(['checkin' => 'Hanya reservasi CONFIRMED yang bisa check-in.']);
        }

        $reservation->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        return back()->with('success', 'Check-in berhasil.');
    }

    public function checkout(
        Reservation $reservation,
        Request $request,
        ReservationInventoryService $inv,
        \App\Services\MidtransService $midtrans
    ) {
        $data = $request->validate([
            'method' => ['required', 'in:CASH,QRIS'],
            'amount' => ['required', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:120'],
        ]);

        return DB::transaction(function () use ($reservation, $data, $inv, $midtrans) {
            if (!in_array($reservation->status, ['checked_in'], true)) {
                throw new \RuntimeException('Hanya reservasi CHECKED_IN yang bisa checkout.');
            }

            $remaining = max(0, (int) $reservation->grand_total - (int) $reservation->paid_amount);
            if ($remaining <= 0) {
                throw new \RuntimeException('Tagihan sudah lunas.');
            }

            if ((int) $data['amount'] !== $remaining) {
                throw new \RuntimeException("Jumlah pembayaran harus tepat Rp {$remaining}.");
            }

            if ($data['method'] === 'CASH') {
                ReservationPayment::create([
                    'reservation_id' => $reservation->id,
                    'type' => 'FINAL',
                    'amount' => (int) $data['amount'],
                    'method' => 'CASH',
                    'status' => 'paid',
                    'reference' => $data['reference'] ?? null,
                    'paid_at' => now(),
                ]);

                $reservation->update([
                    'paid_amount' => (int) $reservation->paid_amount + (int) $data['amount'],
                ]);

                $inv->consumeOnCheckout($reservation->fresh(), Auth::id());

                $reservation->update([
                    'status' => 'completed',
                    'checked_out_at' => now(),
                ]);

                return back()->with('success', 'Checkout CASH selesai. Reservasi COMPLETED.');
            }

            // QRIS via Midtrans
            $orderId = 'RSV-FINAL-' . $reservation->code;

            $alreadyPending = ReservationPayment::where('reservation_id', $reservation->id)
                ->where('type', 'FINAL')
                ->where('status', 'pending')
                ->where('reference', $orderId)
                ->exists();

            if (!$alreadyPending) {
                ReservationPayment::create([
                    'reservation_id' => $reservation->id,
                    'type' => 'FINAL',
                    'amount' => (int) $remaining,
                    'method' => 'MIDTRANS_QRIS',
                    'status' => 'pending',
                    'reference' => $orderId,
                    'paid_at' => null,
                ]);
            }

            $charge = $midtrans->chargeCustom($orderId, (int) $remaining, 'qris'); // :contentReference[oaicite:10]{index=10}
            $instruction = $midtrans->extractInstruction($charge);

            $reservation->update([
                'midtrans_order_id' => $orderId,
                'midtrans_transaction_id' => $charge['transaction_id'] ?? $reservation->midtrans_transaction_id,
                'midtrans_transaction_status' => $charge['transaction_status'] ?? $reservation->midtrans_transaction_status,
                'midtrans_payment_type' => $charge['payment_type'] ?? $reservation->midtrans_payment_type,
                'midtrans_response' => $charge,
                'payment_expires_at' => $instruction['expires_at'] ?? $reservation->payment_expires_at,
            ]);

            return back()->with('success', 'QRIS Midtrans dibuat. Tunggu settlement dari Midtrans.');
        });
    }

    /**
     * Cancel:
     * - REGULAR: auto release lock
     * - BUFFET: tidak auto (manual nanti)
     */
    public function cancel(Reservation $reservation, ReservationInventoryService $inv)
    {
        return DB::transaction(function () use ($reservation, $inv) {
            if (in_array($reservation->status, ['completed'], true)) {
                throw new \RuntimeException('Reservasi COMPLETED tidak bisa dibatalkan.');
            }

            $reservation->update([
                'status' => 'cancelled',
            ]);

            // auto release untuk REGULAR
            $inv->releaseReservationLocks($reservation->fresh(), Auth::id());

            return back()->with('success', 'Reservasi dibatalkan.');
        });
    }
}