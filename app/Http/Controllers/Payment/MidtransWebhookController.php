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
        // 1) Coba proses SALE dulu (existing)
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
        // 2) Kalau bukan SALE, proses RESERVATION (DP / FINAL)
        // order_id:
        // - RSV-DP-<code>
        // - RSV-FINAL-<code>
        // =========================
        $reservationCodeFromOrder = null;
        $isDp = false;
        $isFinal = false;

        if (is_string($orderId) && str_starts_with($orderId, 'RSV-DP-')) {
            $reservationCodeFromOrder = substr($orderId, strlen('RSV-DP-'));
            $isDp = true;
        }

        if (is_string($orderId) && str_starts_with($orderId, 'RSV-FINAL-')) {
            $reservationCodeFromOrder = substr($orderId, strlen('RSV-FINAL-'));
            $isFinal = true;
        }

        // Cari reservation by midtrans_order_id atau by code (kalau order pakai prefix)
        $reservation = Reservation::where('midtrans_order_id', $orderId)
            ->when($reservationCodeFromOrder, function ($q) use ($reservationCodeFromOrder) {
                $q->orWhere('code', $reservationCodeFromOrder);
            })
            ->first();

        if (!$reservation) {
            return response()->json(['message' => 'Sale/Reservation not found'], 404);
        }

        DB::transaction(function () use (
            $reservation,
            $payload,
            $transactionStatus,
            $paymentType,
            $reservationInventory,
            $orderId,
            $isDp,
            $isFinal
        ) {
            // simpan snapshot payload ke reservation
            $reservation->update([
                'midtrans_transaction_id' => $payload['transaction_id'] ?? $reservation->midtrans_transaction_id,
                'midtrans_transaction_status' => $transactionStatus,
                'midtrans_payment_type' => $paymentType ?? $reservation->midtrans_payment_type,
                'midtrans_response' => $payload,
                'payment_expires_at' => $payload['expiry_time'] ?? $reservation->payment_expires_at,
            ]);

            // ======================
            // DP sukses
            // ======================
            if ($isDp && in_array($transactionStatus, ['capture', 'settlement'], true)) {
                // idempotent: jangan double
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

                    // lock stok setelah DP sukses (REGULAR)
                    $reservationInventory->lockForReservation($reservation->fresh(), null);
                }

                return;
            }

            // DP gagal/expire/cancel/deny => cancel reservation
            if ($isDp && in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
                $reservation->update([
                    'status' => 'cancelled',
                ]);
                return;
            }

            // ======================
            // FINAL sukses (pelunasan)
            // ======================
            if ($isFinal && in_array($transactionStatus, ['capture', 'settlement'], true)) {
                // Hitung sisa sekarang (idempotent safe)
                $remaining = max(0, (int) $reservation->grand_total - (int) $reservation->paid_amount);
                if ($remaining <= 0) {
                    return;
                }

                // Update payment FINAL pending menjadi paid (kalau ada)
                $pending = ReservationPayment::where('reservation_id', $reservation->id)
                    ->where('type', 'FINAL')
                    ->where('status', 'pending')
                    ->where('reference', $orderId) // reference kita pakai order_id (RSV-FINAL-...)
                    ->first();

                $payAmount = $pending ? (int) $pending->amount : $remaining;

                if ($pending) {
                    $pending->update([
                        'status' => 'paid',
                        'method' => $paymentType ?? 'midtrans',
                        'reference' => $payload['transaction_id'] ?? $pending->reference,
                        'paid_at' => now(),
                    ]);
                } else {
                    ReservationPayment::create([
                        'reservation_id' => $reservation->id,
                        'type' => 'FINAL',
                        'amount' => (int) $payAmount,
                        'method' => $paymentType ?? 'midtrans',
                        'status' => 'paid',
                        'reference' => $payload['transaction_id'] ?? null,
                        'paid_at' => now(),
                    ]);
                }

                $reservation->update([
                    'paid_amount' => (int) $reservation->paid_amount + (int) $payAmount,
                ]);

                // Consume stok baru dilakukan setelah settlement final
                $reservationInventory->consumeOnCheckout($reservation->fresh(), null);

                $reservation->update([
                    'status' => 'completed',
                    'checked_out_at' => now(),
                ]);

                return;
            }

            // FINAL gagal/expire/cancel/deny => jangan cancel reservation (tetap checked_in),
            // cukup tandai payment pending jadi failed/expired
            if ($isFinal && in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
                ReservationPayment::where('reservation_id', $reservation->id)
                    ->where('type', 'FINAL')
                    ->where('status', 'pending')
                    ->where('reference', $orderId)
                    ->update([
                        'status' => $transactionStatus === 'expire' ? 'expired' : 'failed',
                    ]);

                return;
            }
        });

        return response()->json(['message' => 'OK']);
    }
}