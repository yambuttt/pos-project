<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rincian Belanja - Pembelian Bahan Baku (Gabungan)</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .center { text-align: center; }
        .muted { color: #555; }
        h1 { font-size: 16px; margin: 0 0 6px 0; letter-spacing: .5px; }
        h2 { font-size: 13px; margin: 18px 0 6px 0; letter-spacing: .3px; }

        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #222; padding: 7px 8px; vertical-align: top; }
        th { background: #f2f2f2; }

        .no-border { border: none !important; }
        .summary td { font-weight: bold; }
        .right { text-align: right; }
        .bold { font-weight: bold; }

        .info { margin: 8px 0 10px; line-height: 1.4; }
        .spacer { height: 16px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

<div class="center">
    <h1>RINCIAN BELANJA - PEMBELIAN BAHAN BAKU (GABUNGAN)</h1>
    <div class="muted">
        Dicetak: {{ $printedAt->format('d M Y H:i') }} | Total Purchase: {{ $totalPurchases }}
    </div>
</div>

<div class="spacer"></div>

<table class="summary">
    <tr>
        <td style="width: 50%">Total Purchase</td>
        <td class="right">{{ $totalPurchases }}</td>
    </tr>
    <tr>
        <td>Grand Total Belanja</td>
        <td class="right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
    </tr>
</table>

@foreach ($purchases as $purchase)
    <div class="spacer"></div>

    <div class="center">
        <h2>RINCIAN BELANJA - PEMBELIAN BAHAN BAKU</h2>
    </div>

    <div class="info">
        <div><span class="bold">BUYER:</span> {{ optional($purchase->creator)->name ?? '-' }} - {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d F Y') }}</div>

        @php
            $sourceLabel = '-';
            if ($purchase->source_type === 'supplier') {
                $sourceLabel = (optional($purchase->supplier)->name ?? '-') . ' (SUPPLIER)';
            } elseif ($purchase->source_type === 'external') {
                $sourceLabel = ($purchase->source_name ?? '-') . ' (EXTERNAL)';
            }
        @endphp

        <div><span class="bold">Sumber:</span> {{ $sourceLabel }}</div>
        <div><span class="bold">Invoice:</span> {{ $purchase->invoice_no ?? $purchase->id }}</div>
        @if (!empty($purchase->note))
            <div><span class="bold">Catatan:</span> {{ $purchase->note }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 7%">No</th>
                <th>Nama Barang</th>
                <th style="width: 14%">Satuan</th>
                <th style="width: 14%" class="right">Qty</th>
                <th style="width: 18%" class="right">Harga Satuan</th>
                <th style="width: 18%" class="right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $total = 0; @endphp
            @foreach ($purchase->items as $item)
                @php
                    $rm = $item->rawMaterial;
                    $total += (float) $item->subtotal;
                @endphp
                <tr>
                    <td class="center">{{ $no++ }}</td>
                    <td>
                        {{ $rm->name ?? '-' }}
                    </td>
                    <td class="center">{{ $rm->unit ?? '-' }}</td>
                    <td class="right">{{ rtrim(rtrim(number_format((float)$item->qty, 3, ',', '.'), '0'), ',') }}</td>
                    <td class="right">Rp {{ number_format((float)$item->unit_cost, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format((float)$item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="5" class="right bold">Jumlah Total</td>
                <td class="right bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- page break kalau bukan purchase terakhir --}}
    @if (!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>