<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk {{ $sale->invoice_no }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $paper = request('paper', '80'); // 58 atau 80
        $is58 = $paper === '58';
        $width = $is58 ? '58mm' : '80mm';

        $subtotal = (int) $sale->items->sum('subtotal');
        $tax = max(0, (int) $sale->total_amount - $subtotal - (int) ($sale->delivery_fee ?? 0));
        $cashierName = $sale->user->name ?? auth()->user()->name ?? 'Kasir';
        $logoPath = public_path('images/logo-ayorenne.png');
        $logoExists = file_exists($logoPath);
    @endphp

    <style>
        @page {
            size: {{ $width }} auto;
            margin: 0;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
            font-family: "Courier New", Courier, monospace;
            font-size: {{ $is58 ? '10px' : '11px' }};
            line-height: 1.35;
        }

        body {
            width: {{ $width }};
            margin: 0 auto;
            padding: 0;
        }

        .receipt {
            width: 100%;
            padding: {{ $is58 ? '8px' : '10px' }};
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: 700; }
        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .logo {
            text-align: center;
            margin-bottom: 6px;
        }

        .logo img {
            max-width: {{ $is58 ? '42px' : '54px' }};
            max-height: {{ $is58 ? '42px' : '54px' }};
            object-fit: contain;
        }

        .brand {
            text-align: center;
            margin-bottom: 2px;
            font-weight: 700;
            font-size: {{ $is58 ? '12px' : '13px' }};
        }

        .address, .muted {
            text-align: center;
            font-size: {{ $is58 ? '9px' : '10px' }};
        }

        .meta-table,
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td,
        .totals-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .meta-table td:first-child,
        .totals-table td:first-child {
            width: 42%;
        }

        .items {
            width: 100%;
        }

        .item {
            margin-bottom: 6px;
        }

        .item-name {
            font-weight: 700;
            word-break: break-word;
        }

        .item-note {
            font-size: {{ $is58 ? '8px' : '9px' }};
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            gap: 6px;
            white-space: nowrap;
        }

        .item-row .left {
            flex: 1;
        }

        .item-row .right {
            min-width: {{ $is58 ? '64px' : '84px' }};
            text-align: right;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
        }

        .print-btn {
            display: inline-block;
            margin: 10px auto;
            padding: 8px 12px;
            border: 1px solid #000;
            background: #fff;
            font-family: inherit;
            font-size: 12px;
            cursor: pointer;
        }

        @media print {
            .no-print { display: none !important; }
            body { width: {{ $width }}; }
        }
    </style>
</head>
<body>
    <div class="center no-print">
        <button class="print-btn" onclick="window.print()">Cetak Struk</button>
    </div>

    <div class="receipt">
        @if($logoExists)
            <div class="logo">
                <img src="{{ $logoPath }}" alt="Logo">
            </div>
        @endif

        <div class="brand">Ayorenne Caffe &amp; Resto</div>
        <div class="address">
            Jl. MT. Haryono No.58, Kademangan, Sentong,<br>
            Kec. Krejengan, Kabupaten Probolinggo
        </div>

        <div class="line"></div>

        <table class="meta-table">
            <tr>
                <td>Invoice#</td>
                <td>: {{ $sale->invoice_no }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $sale->created_at?->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Jam Pesan</td>
                <td>: {{ $sale->created_at?->format('H:i:s') }}</td>
            </tr>
            <tr>
                <td>Jam Bayar</td>
                <td>: {{ $sale->paid_at?->format('H:i:s') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ $cashierName }}</td>
            </tr>
            <tr>
                <td>Metode</td>
                <td>: {{ strtoupper($sale->payment_method ?? '-') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: {{ strtoupper($sale->payment_status ?? '-') }}</td>
            </tr>
            <tr>
                <td>Pesanan</td>
                <td>: {{ strtoupper(str_replace('_', ' ', $sale->order_type ?? '-')) }}</td>
            </tr>
            <tr>
                <td>Meja</td>
                <td>: {{ $sale->diningTable?->name ?? '-' }}</td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="items">
            @foreach ($sale->items as $item)
                <div class="item">
                    <div class="item-name">{{ $item->product->name ?? '-' }}</div>

                    @if($item->note)
                        <div class="item-note">Catatan: {{ $item->note }}</div>
                    @endif

                    <div class="item-row">
                        <div class="left">
                            {{ (int) $item->qty }} x {{ number_format((int) $item->price, 0, ',', '.') }}
                        </div>
                        <div class="right">
                            Rp {{ number_format((int) $item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="line"></div>

        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td class="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pajak</td>
                <td class="right">Rp {{ number_format($tax, 0, ',', '.') }}</td>
            </tr>

            @if(($sale->delivery_fee ?? 0) > 0)
            <tr>
                <td>Ongkir</td>
                <td class="right">Rp {{ number_format((int) $sale->delivery_fee, 0, ',', '.') }}</td>
            </tr>
            @endif

            <tr>
                <td class="bold">Total</td>
                <td class="right bold">Rp {{ number_format((int) $sale->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pembayaran</td>
                <td class="right">Rp {{ number_format((int) $sale->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="right">Rp {{ number_format((int) $sale->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="footer">
            <div class="bold">Terima kasih</div>
            <div>Invoice ini adalah bukti transaksi sah</div>
            <div>Simpan struk untuk pengecekan transaksi</div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(() => window.print(), 300);
        });
    </script>
</body>
</html>