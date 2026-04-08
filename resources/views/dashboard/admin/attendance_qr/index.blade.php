@extends('layouts.admin')
@section('title', 'Absensi QR')

@section('body')
    <div class="flex items-center justify-between">
        <div class="text-lg font-semibold text-white">Absensi QR (Dinamis)</div>
        <a href="{{ route('admin.attendance.devices') }}"
            class="rounded-xl border border-yellow-500/20 bg-white/[0.04] px-4 py-2 text-sm text-yellow-500 hover:bg-white/[0.08]">
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
                    Token: <span id="token-in" class="font-mono text-white">-</span><br>
                    Exp: <b id="exp-in">-</b>
                </div>
            </div>

            <form class="mt-4" method="POST" action="{{ route('admin.attendance.qr.regenerate') }}">
                @csrf
                <input type="hidden" name="mode" value="in">
                <!-- <button class="rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
                            Regenerate QR Check-in
                        </button> -->
            </form>
        </div>

        <div class="rounded-[24px] border border-yellow-500/16 bg-[#121212]/90 p-5">
            <div class="text-sm font-semibold text-white">QR Check-out</div>

            <div class="mt-4 flex items-center gap-6">
                <div id="qr-out" class="rounded-2xl bg-white p-3"></div>
                <div class="text-sm text-white/70">
                    Token: <span id="token-out" class="font-mono text-white">-</span><br>
                    Exp: <b id="exp-out">-</b>
                </div>
            </div>

            <form class="mt-4" method="POST" action="{{ route('admin.attendance.qr.regenerate') }}">
                @csrf
                <input type="hidden" name="mode" value="out">
                <!-- <button class="rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
                            Regenerate QR Check-out
                        </button> -->
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
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        const qrInEl = document.getElementById("qr-in");
        const qrOutEl = document.getElementById("qr-out");

        let qrInObj = null;
        let qrOutObj = null;

        async function fetchToken(mode) {
            const url = `{{ route('admin.attendance.qr.token') }}?mode=${mode}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            return await res.json();
        }

        function renderQr(el, objRefName, token) {
            el.innerHTML = '';
            const qr = new QRCode(el, { text: token, width: 180, height: 180 });
            return qr;
        }

        async function refreshQr(mode) {
            try {
                const data = await fetchToken(mode);
                const el = mode === 'in' ? qrInEl : qrOutEl;
                renderQr(el, mode === 'in' ? 'qrInObj' : 'qrOutObj', data.token);

                const infoToken = document.getElementById(`token-${mode}`);
                const infoExp = document.getElementById(`exp-${mode}`);
                if (infoToken) infoToken.textContent = data.token.slice(0, 18) + '…';
                if (infoExp) infoExp.textContent = `${data.expires_in}s`;
            } catch (e) {
                console.error(e);
            }
        }

        // refresh tiap 10-20 detik sesuai config ttl (ambil dari backend? simplest: interval 10)
        setInterval(() => refreshQr('in'), 1000);
        setInterval(() => refreshQr('out'), 1000);

        // initial render
        refreshQr('in');
        refreshQr('out');
    </script>
@endsection