@extends('layouts.admin')
@section('title', 'Absensi QR')

@section('body')
<div class="flex items-center justify-between">
  <div class="text-lg font-semibold text-white">Absensi QR (Dinamis)</div>
  <a href="{{ route('admin.attendance.devices') }}" class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-4 py-2 text-sm text-yellow-500 hover:bg-white/[0.08]">
    Kelola Device
  </a>
</div>

@if(session('ok'))
  <div class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
    {{ session('ok') }}
  </div>
@endif

<div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">
  <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
    <div class="text-sm font-semibold text-white">QR Check-in</div>
    <div class="mt-2 text-xs text-white/60">Scan oleh pegawai. Berlaku singkat & single-use.</div>

    <div class="mt-4 flex items-center gap-6">
      <div id="qr-in" class="rounded-2xl bg-white p-3"></div>
      <div class="text-sm text-white/70">
        Token: <span class="font-mono text-white">{{ $in?->token ?? '-' }}</span><br>
        Exp: <b>{{ $in?->expires_at?->format('H:i:s') ?? '-' }}</b>
      </div>
    </div>

    <form class="mt-4" method="POST" action="{{ route('admin.attendance.qr.regenerate') }}">
      @csrf
      <input type="hidden" name="mode" value="in">
      <button class="rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
        Regenerate QR Check-in
      </button>
    </form>
  </div>

  <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
    <div class="text-sm font-semibold text-white">QR Check-out</div>

    <div class="mt-4 flex items-center gap-6">
      <div id="qr-out" class="rounded-2xl bg-white p-3"></div>
      <div class="text-sm text-white/70">
        Token: <span class="font-mono text-white">{{ $out?->token ?? '-' }}</span><br>
        Exp: <b>{{ $out?->expires_at?->format('H:i:s') ?? '-' }}</b>
      </div>
    </div>

    <form class="mt-4" method="POST" action="{{ route('admin.attendance.qr.regenerate') }}">
      @csrf
      <input type="hidden" name="mode" value="out">
      <button class="rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
        Regenerate QR Check-out
      </button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
  const inToken = @json($in?->token);
  const outToken = @json($out?->token);

  if (inToken) new QRCode(document.getElementById("qr-in"), { text: inToken, width: 180, height: 180 });
  else document.getElementById("qr-in").innerHTML = "<div class='text-xs'>Belum ada QR</div>";

  if (outToken) new QRCode(document.getElementById("qr-out"), { text: outToken, width: 180, height: 180 });
  else document.getElementById("qr-out").innerHTML = "<div class='text-xs'>Belum ada QR</div>";
</script>
@endsection