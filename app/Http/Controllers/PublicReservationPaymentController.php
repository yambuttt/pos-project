<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicReservationPaymentController extends Controller
{
    public function payDp(Reservation $reservation, Request $request, MidtransService $midtrans)
    {
        $data = $request->validate([
            'payment_method' => ['required', 'in:qris,bca_va,bni_va,bri_va,permata_va'],
        ]);

        if (!in_array($reservation->status, ['pending_dp','draft'], true)) {
            return response()->json(['ok' => false, 'message' => 'Status tidak valid untuk bayar DP.'], 422);
        }
        if ($reservation->dp_amount <= 0) {
            return response()->json(['ok' => false, 'message' => 'DP amount tidak valid.'], 422);
        }

        return DB::transaction(function () use ($reservation, $data, $midtrans) {
            // bikin order id unik untuk midtrans
            $orderId = $reservation->midtrans_order_id ?: ('RSV-DP-' . $reservation->code);

            $charge = $midtrans->chargeCustom($orderId, (int) $reservation->dp_amount, $data['payment_method']);
            $instruction = $midtrans->extractInstruction($charge);

            $reservation->update([
                'midtrans_order_id' => $orderId,
                'midtrans_transaction_id' => $charge['transaction_id'] ?? $reservation->midtrans_transaction_id,
                'midtrans_transaction_status' => $charge['transaction_status'] ?? $reservation->midtrans_transaction_status,
                'midtrans_payment_type' => $charge['payment_type'] ?? $reservation->midtrans_payment_type,
                'midtrans_response' => $charge,
                'payment_expires_at' => $instruction['expires_at'] ?? $reservation->payment_expires_at,
            ]);

            return response()->json([
                'ok' => true,
                'payment' => $instruction, // qr_url / va_number / expires_at
            ]);
        });
    }

    public function status(Reservation $reservation, MidtransService $midtrans)
    {
        $payment = $reservation->midtrans_response
            ? $midtrans->extractInstruction($reservation->midtrans_response)
            : null;

        return response()->json([
            'ok' => true,
            'code' => $reservation->code,
            'status' => $reservation->status,
            'paid_amount' => (int) $reservation->paid_amount,
            'dp_amount' => (int) $reservation->dp_amount,
            'expires_at' => optional($reservation->payment_expires_at)?->toDateTimeString(),
            'qr_url' => $payment['qr_url'] ?? null,
        ]);
    }
}