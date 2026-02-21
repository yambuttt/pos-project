<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Rincian Belanja</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    .title { font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 6px; text-transform: uppercase; }
    .meta { margin: 6px 0 14px; }
    .meta div { margin: 2px 0; }

    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #222; padding: 6px 8px; vertical-align: top; }
    th { background: #f0f0f0; font-weight: 700; text-align: center; }

    .right { text-align: right; }
    .center { text-align: center; }
    .bold { font-weight: 700; }
  </style>
</head>
<body>

  <div class="title">RINCIAN BELANJA - PEMBELIAN BAHAN BAKU</div>

  <div class="meta">
    <div class="bold">
      BUYER: {{ $purchase->creator?->name ?? '-' }}
      @if($purchase->purchase_date)
        - {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d F Y') }}
      @endif
    </div>

    <div>
      Sumber:
      @if(($purchase->source_type ?? '') === 'supplier')
        {{ $purchase->supplier?->name ?? '-' }}
      @else
        {{ $purchase->source_name ?? '-' }}
      @endif
      ({{ strtoupper($purchase->source_type ?? '-') }})
    </div>

    <div>
      Invoice:
      <span class="bold">{{ $purchase->invoice_no ?? ('#'.$purchase->id) }}</span>
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
        <td class="right bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

</body>
</html>