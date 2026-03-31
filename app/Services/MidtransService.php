<?php

namespace App\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    protected string $serverKey;
    protected bool $isProduction;
    protected string $baseUrl;
    protected int $expiryMinutes;
    protected string $qrisAcquirer;
    protected ?string $clientKey;
    protected ?string $merchantId;

    public function __construct()
    {
        $this->serverKey = (string) config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');
        $this->merchantId = config('services.midtrans.merchant_id');
        $this->isProduction = (bool) config('services.midtrans.is_production', false);
        $this->expiryMinutes = (int) config('services.midtrans.expiry_minutes', 15);
        $this->qrisAcquirer = (string) config('services.midtrans.qris_acquirer', 'gopay');

        $this->baseUrl = $this->isProduction
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    public function charge(Sale $sale): array
    {
        $payload = $this->buildChargePayload($sale);

        return Http::withBasicAuth($this->serverKey, '')
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl . '/v2/charge', $payload)
            ->throw()
            ->json();
    }

    protected function buildChargePayload(Sale $sale): array
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $sale->midtrans_order_id ?: $sale->invoice_no,
                'gross_amount' => (int) $sale->total_amount,
            ],
            'custom_expiry' => [
                'expiry_duration' => $this->expiryMinutes,
                'unit' => 'minute',
            ],
        ];

        return match ($sale->payment_method) {
            'qris' => array_merge($payload, [
                'payment_type' => 'qris',
                'qris' => [
                    'acquirer' => $this->qrisAcquirer,
                ],
            ]),

            'bca_va' => array_merge($payload, [
                'payment_type' => 'bank_transfer',
                'bank_transfer' => [
                    'bank' => 'bca',
                ],
            ]),

            'bni_va' => array_merge($payload, [
                'payment_type' => 'bank_transfer',
                'bank_transfer' => [
                    'bank' => 'bni',
                ],
            ]),

            'bri_va' => array_merge($payload, [
                'payment_type' => 'bank_transfer',
                'bank_transfer' => [
                    'bank' => 'bri',
                ],
            ]),

            'permata_va' => array_merge($payload, [
                'payment_type' => 'bank_transfer',
                'bank_transfer' => [
                    'bank' => 'permata',
                ],
            ]),

            default => throw new \RuntimeException('Metode pembayaran Midtrans tidak didukung.'),
        };
    }

    public function extractInstruction(array $response): array
    {
        $paymentType = $response['payment_type'] ?? null;
        $actions = collect($response['actions'] ?? []);

        $qrUrl = optional($actions->firstWhere('name', 'generate-qr-code'))['url'] ?? null;

        $result = [
            'payment_type' => $paymentType,
            'transaction_status' => $response['transaction_status'] ?? null,
            'expires_at' => $response['expiry_time'] ?? null,
            'qr_url' => $qrUrl,
            'va_number' => null,
            'bank' => null,
            'bill_key' => $response['bill_key'] ?? null,
            'biller_code' => $response['biller_code'] ?? null,
        ];

        if (!empty($response['va_numbers'][0])) {
            $result['va_number'] = $response['va_numbers'][0]['va_number'] ?? null;
            $result['bank'] = $response['va_numbers'][0]['bank'] ?? null;
        }

        if (!empty($response['permata_va_number'])) {
            $result['va_number'] = $response['permata_va_number'];
            $result['bank'] = 'permata';
        }

        return $result;
    }

    public function isValidSignature(array $payload): bool
    {
        $expected = hash(
            'sha512',
            ($payload['order_id'] ?? '')
            . ($payload['status_code'] ?? '')
            . ($payload['gross_amount'] ?? '')
            . $this->serverKey
        );

        return hash_equals($expected, (string) ($payload['signature_key'] ?? ''));
    }

    public function storeChargeResponse(Sale $sale, array $response): array
    {
        $instruction = $this->extractInstruction($response);

        $sale->update([
            'midtrans_transaction_id' => $response['transaction_id'] ?? $sale->midtrans_transaction_id,
            'midtrans_transaction_status' => $response['transaction_status'] ?? $sale->midtrans_transaction_status,
            'midtrans_payment_type' => $response['payment_type'] ?? $sale->midtrans_payment_type,
            'midtrans_response' => $response,
            'payment_expires_at' => $instruction['expires_at'] ?? $sale->payment_expires_at,
        ]);

        return $instruction;
    }
}