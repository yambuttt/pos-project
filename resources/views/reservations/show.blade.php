<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Reservasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#050505] text-white">
    <div class="max-w-3xl mx-auto px-5 py-10">
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-white/60 text-sm">Kode Reservasi</div>
                    <div class="text-2xl font-bold">{{ $reservation->code }}</div>
                    <div class="text-white/60 text-sm mt-1">Status: <span
                            class="text-yellow-400 font-semibold">{{ $reservation->status }}</span></div>
                </div>
                <a href="/" class="px-4 py-2 rounded-xl border border-white/15 hover:bg-white/5">Beranda</a>
            </div>

            <div class="text-sm text-white/80">
                <div><span class="text-white/60">Nama:</span> {{ $reservation->customer_name }}
                    ({{ $reservation->customer_phone }})</div>
                <div><span class="text-white/60">Resource:</span> {{ $reservation->resource?->name }}
                    [{{ $reservation->resource?->type }}]</div>
                <div><span class="text-white/60">Waktu:</span> {{ $reservation->start_at->format('d M Y H:i') }} →
                    {{ $reservation->end_at->format('d M Y H:i') }}</div>
                <div><span class="text-white/60">Menu Type:</span> {{ $reservation->menu_type }}</div>
            </div>

            <div class="border-t border-white/10 pt-4 text-sm">
                <div class="flex justify-between"><span class="text-white/60">Menu</span><span>Rp
                        {{ number_format($reservation->menu_total) }}</span></div>
                <div class="flex justify-between"><span class="text-white/60">Sewa</span><span>Rp
                        {{ number_format($reservation->rental_total) }}</span></div>
                <div class="flex justify-between font-semibold text-base mt-1"><span>Total</span><span>Rp
                        {{ number_format($reservation->grand_total) }}</span></div>
                <div class="flex justify-between mt-2">
                    <span class="text-white/60">DP (50%)</span>
                    <span class="text-yellow-400 font-semibold">Rp {{ number_format($reservation->dp_amount) }}</span>
                </div>
            </div>

            <div class="border-t border-white/10 pt-4">
                <div class="font-semibold mb-2">Rincian</div>
                <ul class="list-disc ml-5 text-sm text-white/75">
                    @foreach($reservation->items as $it)
                        <li>{{ $it->snapshot_name }} x{{ $it->qty }} — Rp {{ number_format($it->subtotal) }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="border-t border-white/10 pt-4">
                @if ($reservation->status === 'pending_dp')
                    <div class="flex flex-col gap-3">
                        <div class="text-sm text-white/70">Bayar DP via Midtrans (QRIS/VA)</div>

                        <div class="flex gap-2">
                            <select id="payMethod" class="px-3 py-2 rounded-xl bg-black/30 border border-white/10">
                                <option value="qris">QRIS</option>
                                <option value="bca_va">BCA VA</option>
                                <option value="bni_va">BNI VA</option>
                                <option value="bri_va">BRI VA</option>
                                <option value="permata_va">Permata VA</option>
                            </select>

                            <button id="payBtn" class="px-4 py-2 rounded-xl bg-yellow-500 text-black font-semibold">
                                Bayar DP
                            </button>
                        </div>

                        <div id="payInfo" class="text-sm text-white/80"></div>
                    </div>
                @endif
            </div>


        </div>
    </div>

</body>
<script>
    const payBtn = document.getElementById('payBtn');
    if (payBtn) {
        payBtn.addEventListener('click', async () => {
            const method = document.getElementById('payMethod').value;
            const res = await fetch("{{ route('public.reservations.pay_dp', $reservation->code) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ payment_method: method })
            });
            const json = await res.json();
            if (!json.ok) {
                document.getElementById('payInfo').innerText = json.message || 'Gagal membuat pembayaran.';
                return;
            }
            const p = json.payment || {};
            if (p.qr_url) {
                document.getElementById('payInfo').innerHTML =
                    `Scan QR ini: <a class="text-yellow-400 underline" target="_blank" href="${p.qr_url}">Buka QR</a><br>Expires: ${p.expires_at || '-'}`;
            } else if (p.va_number) {
                document.getElementById('payInfo').innerText =
                    `VA ${p.bank || ''}: ${p.va_number} (Expires: ${p.expires_at || '-'})`;
            } else {
                document.getElementById('payInfo').innerText = 'Instruksi pembayaran dibuat.';
            }
        });
    }
</script>
<script>
(async function poll() {
  const currentStatus = "{{ $reservation->status }}";
  if (currentStatus !== 'pending_dp') return;

  async function tick() {
    try {
      const res = await fetch("{{ route('public.reservations.status', $reservation->code) }}");
      const j = await res.json();
      if (j.ok && j.status && j.status !== 'pending_dp') {
        location.reload(); // atau update UI manual
        return;
      }
    } catch (e) {}
    setTimeout(tick, 4000);
  }
  tick();
})();
</script>

</html>