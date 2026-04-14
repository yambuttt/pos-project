<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Services\ReservationInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $date = $request->get('date') ?: now()->toDateString();

        $rows = Reservation::query()
            ->with('resource')
            ->whereDate('start_at', $date)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('code', 'like', "%$q%")
                    ->orWhere('customer_name', 'like', "%$q%")
                    ->orWhere('customer_phone', 'like', "%$q%");
            })
            ->orderBy('start_at')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.kasir.reservations.index', compact('rows', 'q', 'date'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['resource', 'items', 'payments', 'locks.rawMaterial']);
        $remaining = max(0, (int) $reservation->grand_total - (int) $reservation->paid_amount);

        return view('dashboard.kasir.reservations.show', compact('reservation', 'remaining'));
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
            if ($reservation->status !== 'checked_in') {
                throw new \RuntimeException('Hanya reservasi CHECKED_IN yang bisa checkout.');
            }

            $remaining = max(0, (int) $reservation->grand_total - (int) $reservation->paid_amount);
            if ($remaining <= 0) {
                throw new \RuntimeException('Tagihan sudah lunas.');
            }

            if ((int) $data['amount'] !== $remaining) {
                throw new \RuntimeException("Jumlah pembayaran harus tepat Rp {$remaining}.");
            }

            // ======================
            // CASH = langsung paid + complete (seperti sekarang)
            // ======================
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

            // ======================
            // QRIS = Midtrans (buat charge, tunggu webhook settlement)
            // ======================
            $orderId = 'RSV-FINAL-' . $reservation->code;

            // idempotent: kalau sudah ada payment FINAL pending untuk order ini, jangan buat lagi
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
                    'reference' => $orderId, // simpan order_id agar gampang dicari
                    'paid_at' => null,
                ]);
            }

            $charge = $midtrans->chargeCustom($orderId, (int) $remaining, 'qris'); // :contentReference[oaicite:8]{index=8}
            $instruction = $midtrans->extractInstruction($charge); // :contentReference[oaicite:9]{index=9}

            $reservation->update([
                'midtrans_order_id' => $orderId,
                'midtrans_transaction_id' => $charge['transaction_id'] ?? $reservation->midtrans_transaction_id,
                'midtrans_transaction_status' => $charge['transaction_status'] ?? $reservation->midtrans_transaction_status,
                'midtrans_payment_type' => $charge['payment_type'] ?? $reservation->midtrans_payment_type,
                'midtrans_response' => $charge,
                'payment_expires_at' => $instruction['expires_at'] ?? $reservation->payment_expires_at,
            ]);

            // jangan consume stok & jangan completed di sini
            return back()->with('success', 'QRIS Midtrans dibuat. Silakan scan QR untuk pelunasan.');
        });
    }

    public function status(\App\Models\Reservation $reservation)
    {
        // minimal data yang dibutuhkan untuk polling
        return response()->json([
            'status' => $reservation->status,
            'paid_amount' => (int) $reservation->paid_amount,
            'grand_total' => (int) $reservation->grand_total,
            'midtrans_transaction_status' => $reservation->midtrans_transaction_status,
        ]);
    }
}