<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Models\Sale;
use App\Services\MidtransService;
use App\Services\ReservationInventoryService;
use App\Services\SaleInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidtransWebhookController extends Controller
{
    public function handle(
        Request $request,
        MidtransService $midtrans,
        SaleInventoryService $inventory,
        ReservationInventoryService $reservationInventory
    ) {
        $payload = $request->all();

        if (!$midtrans->isValidSignature($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;

        // =========================
        // 1) Coba proses SALE dulu (sesuai controller existing kamu) :contentReference[oaicite:1]{index=1}
        // =========================
        $sale = Sale::where('midtrans_order_id', $orderId)
            ->orWhere('invoice_no', $orderId)
            ->first();

        if ($sale) {
            DB::transaction(function () use ($sale, $payload, $transactionStatus, $paymentType, $inventory) {
                $sale->update([
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? $sale->midtrans_transaction_id,
                    'midtrans_transaction_status' => $transactionStatus,
                    'midtrans_payment_type' => $paymentType ?? $sale->midtrans_payment_type,
                    'midtrans_response' => $payload,
                    'payment_expires_at' => $payload['expiry_time'] ?? $sale->payment_expires_at,
                ]);

                if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
                    $inventory->commitPaid($sale, null);

                    $sale->update([
                        'payment_status' => 'paid',
                        'status' => 'completed',
                        'paid_amount' => $sale->total_amount,
                        'change_amount' => 0,
                        'paid_at' => now(),
                        'kitchen_status' => 'new',
                    ]);
                    return;
                }

                if (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
                    $inventory->release($sale, null);

                    $sale->update([
                        'payment_status' => $transactionStatus === 'expire' ? 'expired' : 'failed',
                        'status' => $transactionStatus === 'expire' ? 'expired' : 'cancelled',
                    ]);
                }
            });

            return response()->json(['message' => 'OK']);
        }

        // =========================
        // 2) Kalau bukan SALE, coba proses RESERVATION (DP)
        // order_id yang kita generate: "RSV-DP-<reservation_code>"
        // =========================
        $reservationCodeFromOrder = null;
        if (is_string($orderId) && str_starts_with($orderId, 'RSV-DP-')) {
            $reservationCodeFromOrder = substr($orderId, strlen('RSV-DP-'));
        }

        $reservation = Reservation::where('midtrans_order_id', $orderId)
            ->when($reservationCodeFromOrder, function ($q) use ($reservationCodeFromOrder) {
                $q->orWhere('code', $reservationCodeFromOrder);
            })
            ->first();

        if (!$reservation) {
            return response()->json(['message' => 'Sale/Reservation not found'], 404);
        }

        DB::transaction(function () use ($reservation, $payload, $transactionStatus, $paymentType, $reservationInventory) {
            // simpan snapshot payload ke reservation
            $reservation->update([
                'midtrans_transaction_id' => $payload['transaction_id'] ?? $reservation->midtrans_transaction_id,
                'midtrans_transaction_status' => $transactionStatus,
                'midtrans_payment_type' => $paymentType ?? $reservation->midtrans_payment_type,
                'midtrans_response' => $payload,
                'payment_expires_at' => $payload['expiry_time'] ?? $reservation->payment_expires_at,
            ]);

            // DP sukses
            if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
                // idempotent: jangan double proses
                if (!$reservation->dp_paid_at) {
                    $reservation->update([
                        'status' => 'confirmed',
                        'dp_paid_at' => now(),
                        'paid_amount' => (int) $reservation->paid_amount + (int) $reservation->dp_amount,
                    ]);

                    ReservationPayment::create([
                        'reservation_id' => $reservation->id,
                        'type' => 'DP',
                        'amount' => (int) $reservation->dp_amount,
                        'method' => $paymentType ?? 'midtrans',
                        'status' => 'paid',
                        'reference' => $payload['transaction_id'] ?? null,
                        'paid_at' => now(),
                    ]);

                    // REGULAR: lock stok setelah DP sukses
                    // NOTE: pastikan method lockForReservation menerima userId nullable
                    $reservationInventory->lockForReservation($reservation->fresh(), null);
                }
                return;
            }

            // DP gagal/expire/cancel/deny
            if (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
                // karena lock stok terjadi hanya saat DP sukses,
                // disini cukup set cancelled/failed sesuai kebutuhanmu
                $reservation->update([
                    'status' => 'cancelled',
                ]);
            }
        });

        return response()->json(['message' => 'OK']);
    }
}