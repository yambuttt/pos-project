<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Rincian Belanja (Bulk)</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    .title { font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 6px; text-transform: uppercase; }
    .sub { text-align: center; font-size: 11px; color: #444; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #222; padding: 6px 8px; vertical-align: top; }
    th { background: #f0f0f0; font-weight: 700; text-align: center; }
    .right { text-align: right; }
    .center { text-align: center; }
    .bold { font-weight: 700; }
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

  <table>
    <thead>
      <tr>
        <th style="width:40px;">No</th>
        <th style="width:90px;">Tanggal</th>
        <th style="width:110px;">Invoice</th>
        <th style="width:130px;">Sumber</th>
        <th>Nama Barang</th>
        <th style="width:70px;">Satuan</th>
        <th style="width:70px;">Qty</th>
        <th style="width:100px;">Harga Satuan</th>
        <th style="width:110px;">Jumlah</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $i => $r)
        <tr>
          <td class="center">{{ $i + 1 }}</td>
          <td class="center">{{ \Carbon\Carbon::parse($r['date'])->format('d M Y') }}</td>
          <td class="center"><b>{{ $r['invoice'] }}</b></td>
          <td>{{ $r['source'] }}</td>
          <td>{{ $r['raw_name'] }}</td>
          <td class="center">{{ $r['unit'] }}</td>
          <td class="right">{{ rtrim(rtrim(number_format($r['qty'], 3, '.', ''), '0'), '.') }}</td>
          <td class="right">Rp {{ number_format($r['unit_cost'], 0, ',', '.') }}</td>
          <td class="right">Rp {{ number_format($r['subtotal'], 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="9" class="center">Tidak ada data.</td>
        </tr>
      @endforelse

      <tr>
        <td colspan="8" class="right bold">Jumlah Total</td>
        <td class="right bold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

</body>
</html>