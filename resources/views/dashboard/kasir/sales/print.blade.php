<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            background: #e5e7eb;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 18mm 16mm;
        }

        .print-bar {
            display: flex;
            justify-content: center;
            padding: 14px 0;
            background: #d1d5db;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .print-btn {
            border: 0;
            background: #111827;
            color: #fff;
            border-radius: 10px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .invoice-wrap {
            border: 1px solid #d1d5db;
            border-radius: 18px;
            padding: 22px;
        }

        .topline {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .logo-box {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            border: 1px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fff;
            flex-shrink: 0;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-fallback {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .brand-title {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.15;
            color: #111827;
            margin: 0;
        }

        .brand-sub {
            margin-top: 6px;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.55;
            max-width: 430px;
        }

        .invoice-meta-top {
            text-align: right;
        }

        .invoice-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: #6b7280;
        }

        .invoice-number {
            margin-top: 6px;
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .status-badge {
            display: inline-block;
            margin-top: 10px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-expired,
        .status-failed,
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .divider {
            border-top: 2px dashed #d1d5db;
            margin: 18px 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 28px;
        }

        .info-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px 16px;
        }

        .info-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #6b7280;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-row .label {
            color: #4b5563;
        }

        .info-row .value {
            font-weight: 700;
            text-align: right;
            color: #111827;
        }

        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #6b7280;
            margin-bottom: 10px;
            font-weight: 800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6b7280;
            padding: 12px 10px;
            border-bottom: 1.8px solid #d1d5db;
            background: #f9fafb;
        }

        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #eceff3;
            vertical-align: top;
            font-size: 14px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .item-name {
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .item-note {
            font-size: 12px;
            color: #6b7280;
        }

        .summary-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .summary-box {
            width: 100%;
            max-width: 360px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            padding: 14px 16px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .summary-row:last-child {
            margin-bottom: 0;
        }

        .summary-row.total {
            border-top: 1.8px dashed #d1d5db;
            padding-top: 12px;
            margin-top: 12px;
            font-size: 16px;
            font-weight: 800;
        }

        .summary-row .label {
            color: #374151;
        }

        .summary-row .value {
            font-weight: 800;
            color: #111827;
        }

        .footer-note {
            margin-top: 20px;
            border-top: 2px dashed #d1d5db;
            padding-top: 14px;
            text-align: center;
        }

        .footer-note .thanks {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .footer-note .small {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }

        @media print {
            body { background: #fff; }
            .print-bar { display: none !important; }
            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
            }
            .invoice-wrap {
                border: 0;
                border-radius: 0;
                padding: 0;
            }
            @page {
                size: A4;
                margin: 14mm;
            }
        }
    </style>
</head>
<body>
    <div class="print-bar no-print">
        <button class="print-btn" onclick="window.print()">Cetak Invoice</button>
    </div>

    @php
        $subtotal = (int) $sale->items->sum('subtotal');
        $tax = max(0, (int) $sale->total_amount - $subtotal - (int) ($sale->delivery_fee ?? 0));
        $cashierName = $sale->user->name ?? auth()->user()->name ?? 'Kasir';
        $status = strtolower((string) ($sale->payment_status ?? 'pending'));
        $statusClass = match ($status) {
            'paid' => 'status-paid',
            'expired' => 'status-expired',
            'failed' => 'status-failed',
            'cancelled' => 'status-cancelled',
            default => 'status-pending',
        };

        // Ganti path ini jika file logo restaurant kamu ada di lokasi lain.
        $logoPath = public_path('images/landing/logo-ayo-renne.png');
        $logoExists = file_exists($logoPath);
    @endphp

    <div class="page">
        <div class="invoice-wrap">
            <div class="topline">
                <div class="brand">
                    <div class="logo-box">
                        @if($logoExists)
                            <img src="{{ $logoPath }}" alt="Logo Ayorenne Caffe & Resto">
                        @else
                            <div class="logo-fallback">AR</div>
                        @endif
                    </div>

                    <div>
                        <h1 class="brand-title">Ayorenne Caffe &amp; Resto</h1>
                        <div class="brand-sub">
                            Jl. MT. Haryono No.58, Kademangan, Sentong, Kec. Krejengan, Kabupaten Probolinggo
                        </div>
                    </div>
                </div>

                <div class="invoice-meta-top">
                    <div class="invoice-label">Invoice</div>
                    <div class="invoice-number">{{ $sale->invoice_no }}</div>
                    <div class="status-badge {{ $statusClass }}">
                        {{ strtoupper($sale->payment_status ?? 'pending') }}
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-title">Informasi Transaksi</div>

                    <div class="info-row">
                        <span class="label">Tanggal</span>
                        <span class="value">{{ $sale->created_at?->format('d M Y') }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Jam Pesan</span>
                        <span class="value">{{ $sale->created_at?->format('H:i:s') }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Jam Bayar</span>
                        <span class="value">{{ $sale->paid_at?->format('H:i:s') ?? '-' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Jenis Pesanan</span>
                        <span class="value">{{ strtoupper(str_replace('_', ' ', $sale->order_type ?? '-')) }}</span>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-title">Informasi Layanan</div>

                    <div class="info-row">
                        <span class="label">Meja</span>
                        <span class="value">{{ $sale->diningTable?->name ?? '-' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Kasir</span>
                        <span class="value">{{ $cashierName }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Metode Bayar</span>
                        <span class="value">{{ strtoupper($sale->payment_method ?? '-') }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Status</span>
                        <span class="value">{{ strtoupper($sale->payment_status ?? '-') }}</span>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="section-title">Detail Pesanan</div>

            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center" style="width: 70px;">Qty</th>
                        <th class="text-right" style="width: 130px;">Harga</th>
                        <th class="text-right" style="width: 150px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>
                                <div class="item-name">{{ $item->product->name ?? '-' }}</div>
                                @if ($item->note)
                                    <div class="item-note">Catatan: {{ $item->note }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ (int) $item->qty }}</td>
                            <td class="text-right">Rp {{ number_format((int) $item->price, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format((int) $item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary-wrap">
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="label">Subtotal</span>
                        <span class="value">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="summary-row">
                        <span class="label">Pajak</span>
                        <span class="value">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                    </div>

                    @if(($sale->delivery_fee ?? 0) > 0)
                        <div class="summary-row">
                            <span class="label">Ongkir</span>
                            <span class="value">Rp {{ number_format((int) $sale->delivery_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="summary-row total">
                        <span class="label">Total</span>
                        <span class="value">Rp {{ number_format((int) $sale->total_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="summary-row">
                        <span class="label">Dibayar</span>
                        <span class="value">Rp {{ number_format((int) $sale->paid_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="summary-row">
                        <span class="label">Kembalian</span>
                        <span class="value">Rp {{ number_format((int) $sale->change_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="footer-note">
                <div class="thanks">Terima Kasih</div>
                <div class="small">
                    Invoice ini merupakan bukti transaksi sah.<br>
                    Simpan invoice ini untuk keperluan komplain atau pengecekan transaksi.
                </div>
            </div>
        </div>
    </div>
</body>
</html>