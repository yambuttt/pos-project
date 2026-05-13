<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Absensi - QR Dynamic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0b0b;
            color: white;
            overflow: hidden;
        }
        .gold-gradient {
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
        }
        .gold-text {
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">
    
    <!-- Background Decor -->
    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-yellow-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-yellow-600/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold gold-text mb-2 tracking-tight">KIOSK ABSENSI</h1>
        <p class="text-white/60 text-lg">Silakan scan QR Code melalui aplikasi pegawai untuk absensi.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-5xl">
        <!-- CHECK IN CARD -->
        <div class="glass-card rounded-[40px] p-8 flex flex-col items-center text-center">
            <div class="mb-6">
                <span class="px-5 py-2 rounded-full bg-emerald-500/20 text-emerald-400 text-sm font-bold tracking-widest uppercase">Check In</span>
            </div>
            
            <div id="qr-in-container" class="relative group">
                <div class="absolute -inset-1 gold-gradient rounded-3xl blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                <div id="qr-in" class="relative bg-white p-6 rounded-3xl shadow-2xl">
                    <!-- QR Placeholder -->
                </div>
            </div>

            <div class="mt-8 space-y-2">
                <div class="text-white/40 text-xs font-mono uppercase tracking-widest">Active Token</div>
                <div id="token-in" class="text-sm font-mono text-white/80">----------</div>
                <div class="flex items-center justify-center gap-2 mt-4">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <div id="exp-in" class="text-emerald-400 font-bold text-lg">--s</div>
                </div>
            </div>
        </div>

        <!-- CHECK OUT CARD -->
        <div class="glass-card rounded-[40px] p-8 flex flex-col items-center text-center">
            <div class="mb-6">
                <span class="px-5 py-2 rounded-full bg-orange-500/20 text-orange-400 text-sm font-bold tracking-widest uppercase">Check Out</span>
            </div>
            
            <div id="qr-out-container" class="relative group">
                <div class="absolute -inset-1 gold-gradient rounded-3xl blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                <div id="qr-out" class="relative bg-white p-6 rounded-3xl shadow-2xl">
                    <!-- QR Placeholder -->
                </div>
            </div>

            <div class="mt-8 space-y-2">
                <div class="text-white/40 text-xs font-mono uppercase tracking-widest">Active Token</div>
                <div id="token-out" class="text-sm font-mono text-white/80">----------</div>
                <div class="flex items-center justify-center gap-2 mt-4">
                    <div class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></div>
                    <div id="exp-out" class="text-orange-400 font-bold text-lg">--s</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-16 text-center">
        <div id="current-time" class="text-3xl font-bold tracking-widest mb-1 text-white/90">00:00:00</div>
        <div id="current-date" class="text-white/40 uppercase tracking-[0.3em] text-xs">Kamis, 14 Mei 2026</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        const kioskKey = "{{ $key }}";
        const qrInEl = document.getElementById("qr-in");
        const qrOutEl = document.getElementById("qr-out");

        async function fetchToken(mode) {
            const url = `/attendance/kiosk/token?key=${kioskKey}&mode=${mode}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            return await res.json();
        }

        function renderQr(el, token) {
            el.innerHTML = '';
            new QRCode(el, { text: token, width: 220, height: 220 });
        }

        async function refreshQr(mode) {
            try {
                const data = await fetchToken(mode);
                const el = mode === 'in' ? qrInEl : qrOutEl;
                renderQr(el, data.token);

                const infoToken = document.getElementById(`token-${mode}`);
                const infoExp = document.getElementById(`exp-${mode}`);
                
                if (infoToken) infoToken.textContent = data.token.substring(0, 24) + '...';
                if (infoExp) infoExp.textContent = `${data.expires_in}s`;
            } catch (e) {
                console.error(e);
            }
        }

        // Time and Date display
        function updateClock() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }

        setInterval(updateClock, 1000);
        updateClock();

        // Refresh tokens every second to update countdown
        setInterval(() => refreshQr('in'), 1000);
        setInterval(() => refreshQr('out'), 1000);

        // Initial render
        refreshQr('in');
        refreshQr('out');
    </script>
</body>
</html>
