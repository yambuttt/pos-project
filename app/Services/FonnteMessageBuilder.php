<?php

namespace App\Services;

use App\Models\Sale;

class FonnteMessageBuilder
{
    public function invoiceCreated(Sale $sale, string $invoiceUrl): string
    {
        return implode("\n", [
            'Halo, pesanan kamu berhasil dibuat.',
            'Invoice: ' . $sale->invoice_no,
            'Total: Rp ' . number_format((int) $sale->total_amount, 0, ',', '.'),
            'Metode: ' . strtoupper((string) $sale->payment_method),
            'Status: ' . strtoupper((string) $sale->payment_status),
            'Lihat invoice: ' . $invoiceUrl,
        ]);
    }

    public function invoicePaid(Sale $sale, string $invoiceUrl): string
    {
        return implode("\n", [
            'Pembayaran berhasil diterima.',
            'Invoice: ' . $sale->invoice_no,
            'Total: Rp ' . number_format((int) $sale->total_amount, 0, ',', '.'),
            'Status: ' . strtoupper((string) $sale->payment_status),
            'Invoice: ' . $invoiceUrl,
            'Terima kasih sudah bertransaksi di Ayorenne Caffe & Resto.',
        ]);
    }

    public function kitchenReady(Sale $sale): string
    {
        return implode("\n", [
            'Pesanan kamu sudah siap.',
            'Invoice: ' . $sale->invoice_no,
            'Silakan ambil pesanan kamu. Terima kasih.',
        ]);
    }
}