<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Services\MidtransService;
use App\Services\SaleInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidtransWebhookController extends Controller
{
    public function handle(
        Request $request,
        MidtransService $midtrans,
        SaleInventoryService $inventory
    ) {
        $payload = $request->all();

        if (!$midtrans->isValidSignature($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;

        $sale = Sale::where('midtrans_order_id', $orderId)
            ->orWhere('invoice_no', $orderId)
            ->first();

        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        DB::transaction(function () use ($sale, $payload, $transactionStatus, $paymentType, $inventory) {
            $sale->update([
                'midtrans_transaction_id' => $payload['transaction_id'] ?? $sale->midtrans_transaction_id,
                'midtrans_transaction_status' => $transactionStatus,
                'midtrans_payment_type' => $paymentType ?? $sale->midtrans_payment_type,
                'midtrans_response' => $payload,
                'payment_expires_at' => $payload['expiry_time'] ?? $sale->payment_expires_at,
            ]);

            if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
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
}