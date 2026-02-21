<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Rincian Belanja (Gabungan)</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }

    .title { font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 6px; text-transform: uppercase; }
    .sub { text-align: center; font-size: 11px; color: #444; margin-bottom: 14px; }

    .summary { margin: 10px 0 18px; }
    .summary-table { width: 100%; border-collapse: collapse; }
    .summary-table td { border: 1px solid #222; padding: 8px 10px; }
    .summary-table .label { width: 200px; font-weight: 700; background: #f0f0f0; }
    .right { text-align: right; }
    .center { text-align: center; }
    .bold { font-weight: 700; }

    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #222; padding: 6px 8px; vertical-align: top; }
    th { background: #f0f0f0; font-weight: 700; text-align: center; }

    .meta { margin: 6px 0 12px; }
    .meta div { margin: 2px 0; }

    .page-break { page-break-after: always; }
  </style>
</head>
<body>

  <div class="title">RINCIAN BELANJA - PEMBELIAN BAHAN BAKU (GABUNGAN)</div>

  <div class="sub">
    Dicetak: {{ $meta['printed_at']->format('d M Y H:i') }}
    @if($meta['from'] || $meta['to'])
      | Periode:
      {{ $meta['from'] ? \Carbon\Carbon::parse($meta['from'])->format('d M Y') : '-' }}
      s/d
      {{ $meta['to'] ? \Carbon\Carbon::parse($meta['to'])->format('d M Y') : '-' }}
    @endif
    | Total Purchase: {{ $meta['count_purchase'] }}
  </div>

  {{-- Summary paling atas --}}
  <div class="summary">
    <table class="summary-table">
      <tr>
        <td class="label">Total Purchase</td>
        <td class="right bold">{{ $meta['count_purchase'] }}</td>
      </tr>
      <tr>
        <td class="label">Grand Total Belanja</td>
        <td class="right bold">Rp {{ number_format($meta['grand_total'], 0, ',', '.') }}</td>
      </tr>
    </table>
  </div>

  {{-- Per Purchase (dipisah seperti export satuan) --}}
  @forelse($purchases as $idx => $purchase)
    @php
      $purchaseTotal = (float) ($purchase->items?->sum('subtotal') ?? 0);
      $sourceLabel = ($purchase->source_type === 'supplier')
        ? ($purchase->supplier?->name ?? '-')
        : ($purchase->source_name ?? '-');
    @endphp

    <div class="title" style="font-size:13px; margin-top:18px;">
      RINCIAN BELANJA - PEMBELIAN BAHAN BAKU
    </div>

    <div class="meta">
      <div class="bold">
        BUYER: {{ $purchase->creator?->name ?? '-' }}
        -
        {{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d F Y') : '-' }}
      </div>

      <div>
        Sumber: {{ $sourceLabel }} ({{ strtoupper($purchase->source_type ?? '-') }})
      </div>

      <div>
        Invoice: <span class="bold">{{ $purchase->invoice_no ?? ('#'.$purchase->id) }}</span>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width:40px;">No</th>
          <th>Nama Barang</th>
          <th style="width:90px;">Satuan</th>
          <th style="width:70px;">Qty</th>
          <th style="width:110px;">Harga Satuan</th>
          <th style="width:120px;">Jumlah</th>
        </tr>
      </thead>
      <tbody>
        @forelse(($purchase->items ?? []) as $i => $it)
          <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $it->rawMaterial?->name ?? '-' }}</td>
            <td class="center">{{ $it->rawMaterial?->unit ?? '-' }}</td>
            <td class="right">
              {{ rtrim(rtrim(number_format($it->qty ?? 0, 3, '.', ''), '0'), '.') }}
            </td>
            <td class="right">Rp {{ number_format($it->unit_cost ?? 0, 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($it->subtotal ?? 0, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="center">Tidak ada item.</td>
          </tr>
        @endforelse

        <tr>
          <td colspan="5" class="right bold">Jumlah Total</td>
          <td class="right bold">Rp {{ number_format($purchaseTotal, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>

    {{-- Biar rapi: setiap purchase beda halaman (kecuali terakhir) --}}
    @if($idx < $purchases->count() - 1)
      <div class="page-break"></div>
    @endif

  @empty
    <div class="center">Tidak ada data.</div>
  @endforelse

</body>
</html>