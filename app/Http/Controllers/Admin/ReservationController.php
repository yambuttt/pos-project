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

        return view('dashboard.admin.reservations.index', compact('rows','q','status'));
    }

    public function create()
    {
        $resources = ReservationResource::where('is_active', true)->orderBy('type')->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get(['id','name','price']);

        return view('dashboard.admin.reservations.create', compact('resources','products'));
    }

    public function store(Request $request, ReservationInventoryService $inv)
    {
        $data = $request->validate([
            'reservation_resource_id' => ['required','exists:reservation_resources,id'],
            'customer_name' => ['required','string','max:120'],
            'customer_phone' => ['nullable','string','max:30'],
            'start_at' => ['required','date'],
            'end_at' => ['required','date','after:start_at'],
            'pax' => ['nullable','integer','min:1'],
            'menu_type' => ['required','in:REGULAR,BUFFET'],

            // REGULAR items
            'items' => ['nullable','array'],
            'items.*.product_id' => ['required_with:items','integer','exists:products,id'],
            'items.*.qty' => ['required_with:items','integer','min:1'],

            'rental_total' => ['nullable','integer','min:0'],
            'notes' => ['nullable','string'],
        ]);

        // generate code sederhana
        $code = 'RSV-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $dpRatio = (float) config('reservations.dp_ratio', 0.5);

        return DB::transaction(function () use ($data, $code, $dpRatio) {
            $menuTotal = 0;

            $reservation = Reservation::create([
                'code' => $code,
                'reservation_resource_id' => $data['reservation_resource_id'],
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'pax' => $data['pax'] ?? null,
                'menu_type' => $data['menu_type'],
                'status' => 'pending_dp',
                'rental_total' => (int) ($data['rental_total'] ?? 0),
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            if ($reservation->menu_type === 'REGULAR') {
                $items = $data['items'] ?? [];
                if (count($items) === 0) {
                    throw new \RuntimeException('Menu REGULAR wajib pilih minimal 1 item.');
                }

                $productIds = collect($items)->pluck('product_id')->unique()->values();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($items as $it) {
                    $p = $products[$it['product_id']] ?? null;
                    if (!$p) continue;

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
        $reservation->load(['resource','items','payments','locks.rawMaterial']);
        return view('dashboard.admin.reservations.show', compact('reservation'));
    }

    /**
     * Mark DP paid (manual dulu). Nanti bisa dihubungkan Midtrans DP.
     * Jika REGULAR => auto lock stok (min*2 rule).
     */
    public function markDpPaid(Reservation $reservation, Request $request, ReservationInventoryService $inv)
    {
        $data = $request->validate([
            'method' => ['required','in:CASH,QRIS,MIDTRANS'],
            'amount' => ['required','integer','min:1'],
            'reference' => ['nullable','string','max:120'],
        ]);

        return DB::transaction(function () use ($reservation, $data, $inv) {
            if (!in_array($reservation->status, ['pending_dp','draft'], true)) {
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

    public function checkout(Reservation $reservation, Request $request, ReservationInventoryService $inv)
    {
        $data = $request->validate([
            'method' => ['required','in:CASH,QRIS'],
            'amount' => ['required','integer','min:1'],
            'reference' => ['nullable','string','max:120'],
        ]);

        return DB::transaction(function () use ($reservation, $data, $inv) {
            if (!in_array($reservation->status, ['checked_in'], true)) {
                throw new \RuntimeException('Hanya reservasi CHECKED_IN yang bisa checkout.');
            }

            // catat pembayaran final
            ReservationPayment::create([
                'reservation_id' => $reservation->id,
                'type' => 'FINAL',
                'amount' => (int) $data['amount'],
                'method' => $data['method'],
                'status' => 'paid',
                'reference' => $data['reference'] ?? null,
                'paid_at' => now(),
            ]);

            $reservation->update([
                'paid_amount' => (int) $reservation->paid_amount + (int) $data['amount'],
            ]);

            // kalau REGULAR: consume bahan lock
            $inv->consumeOnCheckout($reservation->fresh(), Auth::id());

            $reservation->update([
                'status' => 'completed',
                'checked_out_at' => now(),
            ]);

            return back()->with('success', 'Checkout selesai. Reservasi COMPLETED.');
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