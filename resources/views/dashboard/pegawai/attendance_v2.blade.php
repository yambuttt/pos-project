@extends('layouts.pegawai')
@section('title', 'Absensi')

@section('page_label', 'Pegawai')
@section('page_title', 'Absensi (QR + Lokasi + Selfie)')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
        <div class="text-sm font-semibold">Status Hari Ini</div>
        <div class="mt-3 text-sm text-white/80 space-y-1">
            <div>Check-in: <b>{{ $attendance?->check_in_at?->format('H:i') ?? '--:--' }}</b></div>
            <div>Check-out: <b>{{ $attendance?->check_out_at?->format('H:i') ?? '--:--' }}</b></div>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.1fr_.9fr]">
        <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
            <div class="text-sm font-semibold">1) Verifikasi Device & Lokasi</div>
            <div class="mt-2 text-xs text-white/70">Aplikasi akan cek device terdaftar dan lokasi berada di area restoran.
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button id="btnInit"
                    class="rounded-xl bg-emerald-600/80 px-4 py-2 text-sm font-semibold hover:bg-emerald-500/80">
                    Mulai Verifikasi
                </button>
            </div>

            <div id="gateStatus" class="mt-3 text-sm text-white/80">Status: belum verifikasi.</div>
        </div>

        <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
            <div class="text-sm font-semibold">2) Scan QR + Selfie</div>
            <div class="mt-2 text-xs text-white/70">Scan QR dari admin, lalu ambil selfie dari kamera (bukan gallery).</div>

            <div id="qrReader" class="mt-4 overflow-hidden rounded-2xl border border-white/15 bg-black/20 p-3"></div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-white/15 bg-black/20">
                <video id="selfieVideo" autoplay playsinline class="w-full aspect-video object-cover hidden"></video>
                <canvas id="selfieCanvas" class="hidden"></canvas>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button id="btnCheckIn" disabled
                    class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15 disabled:opacity-50">
                    Check-in
                </button>
                <button id="btnCheckOut" disabled
                    class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15 disabled:opacity-50">
                    Check-out
                </button>
            </div>

            <div id="scanStatus" class="mt-3 text-sm text-white/80">Status: menunggu verifikasi.</div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // ambil status attendance dari server (blade)
        const HAS_CHECKIN = {{ $attendance?->check_in_at ? 'true' : 'false' }};
        const HAS_CHECKOUT = {{ $attendance?->check_out_at ? 'true' : 'false' }};

        let deviceHash = null;
        let geo = null; // {lat,lng}

        let reader = null;
        let selfieStream = null;

        let gateOk = false;
        let busy = false;            // mencegah double action
        let currentMode = null;      // 'in' atau 'out'
        let scannedToken = null;

        const gateStatus = document.getElementById('gateStatus');
        const scanStatus = document.getElementById('scanStatus');
        const btnInit = document.getElementById('btnInit');
        const btnCheckIn = document.getElementById('btnCheckIn');
        const btnCheckOut = document.getElementById('btnCheckOut');

        const video = document.getElementById('selfieVideo');
        const canvas = document.getElementById('selfieCanvas');

        function setGate(msg) { gateStatus.textContent = "Status: " + msg; }
        function setScan(msg) { scanStatus.textContent = "Status: " + msg; }

        function setButtonsAfterGate() {
            // default
            btnCheckIn.disabled = true;
            btnCheckOut.disabled = true;

            // kalau belum gate, tetap disabled
            if (!gateOk) return;

            // kalau sudah complete hari ini
            if (HAS_CHECKIN && HAS_CHECKOUT) {
                setScan("kamu sudah check-in & check-out hari ini.");
                return;
            }

            // kalau sudah check-in, hanya checkout yang aktif
            if (HAS_CHECKIN && !HAS_CHECKOUT) {
                btnCheckIn.disabled = true;
                btnCheckOut.disabled = false;
                setScan("silakan tekan Check-out untuk scan QR checkout.");
                return;
            }

            // kalau belum check-in, hanya checkin yang aktif
            if (!HAS_CHECKIN) {
                btnCheckIn.disabled = false;
                btnCheckOut.disabled = true;
                setScan("silakan tekan Check-in untuk scan QR checkin.");
                return;
            }
        }

        async function sha256Hex(str) {
            const enc = new TextEncoder().encode(str);
            const buf = await crypto.subtle.digest('SHA-256', enc);
            return Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2, '0')).join('');
        }

        function buildFingerprint() {
            // cukup untuk device locking operasional
            const parts = [
                navigator.userAgent,
                navigator.language,
                screen.width + "x" + screen.height,
                Intl.DateTimeFormat().resolvedOptions().timeZone
            ];
            return parts.join("|");
        }

        async function getGeo() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) return reject(new Error("Geolocation tidak didukung."));
                navigator.geolocation.getCurrentPosition(
                    (pos) => resolve({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
                    (err) => reject(err),
                    { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
                );
            });
        }

        async function initGate() {
            if (busy) return;
            busy = true;

            try {
                setGate("mengambil fingerprint device...");
                deviceHash = await sha256Hex(buildFingerprint());

                setGate("cek lokasi...");
                geo = await getGeo();

                setGate("cek device ke server...");
                const res = await fetch("{{ route('pegawai.attendance.device.init') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
                    body: JSON.stringify({ device_hash: deviceHash, device_name: "Web" })
                });
                const json = await res.json().catch(() => ({}));

                if (!res.ok) {
                    gateOk = false;
                    setGate(json.message || "device belum valid.");
                    setScan("tidak bisa lanjut sebelum device approved.");
                    setButtonsAfterGate();
                    return;
                }

                gateOk = true;
                setGate("device OK + lokasi OK ✅");
                setButtonsAfterGate();

            } catch (e) {
                gateOk = false;
                setGate("gagal ambil lokasi. aktifkan GPS & izin lokasi.");
                setScan("menunggu verifikasi.");
                setButtonsAfterGate();
            } finally {
                busy = false;
            }
        }

        // ---------- QR FLOW ----------
        async function startQrScanner(mode) {
            // mode = 'in' atau 'out'
            if (busy) return;
            if (!gateOk) { setScan("verifikasi device & lokasi dulu."); return; }

            busy = true;
            currentMode = mode;
            scannedToken = null;

            try {
                setScan(`mode ${mode === 'in' ? 'CHECK-IN' : 'CHECK-OUT'}: arahkan kamera ke QR admin...`);

                // pastikan kamera selfie mati kalau sebelumnya hidup
                stopSelfie();

                // start QR reader
                if (reader) {
                    await stopQrScanner();
                }

                reader = new Html5Qrcode("qrReader");
                await reader.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: 240 },
                    async (decodedText) => {
                        scannedToken = (decodedText || "").trim();
                        if (!scannedToken) return;

                        // stop QR segera agar tidak double trigger
                        await stopQrScanner();

                        setScan("QR valid ✅ mempersiapkan selfie...");

                        // lanjut selfie auto + countdown
                        await startSelfieAndCountdownThenSubmit();
                    },
                    () => { }
                );

            } catch (e) {
                setScan("kamera QR gagal dibuka. izinkan kamera.");
                busy = false;
            }
        }

        async function stopQrScanner() {
            try {
                if (reader) {
                    await reader.stop();
                    reader.clear();
                    reader = null;
                }
            } catch (e) { }
        }

        // ---------- SELFIE FLOW ----------
        async function startSelfieCamera() {
            if (selfieStream) return;

            selfieStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "user", width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });

            video.classList.remove("hidden");
            video.srcObject = selfieStream;
            await video.play();
        }

        function stopSelfie() {
            try {
                if (selfieStream) {
                    selfieStream.getTracks().forEach(t => t.stop());
                    selfieStream = null;
                }
                video.classList.add("hidden");
                video.srcObject = null;
            } catch (e) { }
        }

        function captureSelfieBlob() {
            const w = video.videoWidth || 640;
            const h = video.videoHeight || 480;
            canvas.width = w;
            canvas.height = h;
            const ctx = canvas.getContext("2d");
            ctx.drawImage(video, 0, 0, w, h);
            return new Promise(resolve => canvas.toBlob(resolve, "image/jpeg", 0.9));
        }

        async function countdown(seconds) {
            for (let i = seconds; i >= 1; i--) {
                setScan(`selfie otomatis dalam ${i}...`);
                await new Promise(r => setTimeout(r, 1000));
            }
        }

        async function startSelfieAndCountdownThenSubmit() {
            try {
                // validasi data wajib sebelum lanjut
                if (!deviceHash || !geo || !scannedToken || !currentMode) {
                    setScan("data belum lengkap. ulangi proses.");
                    busy = false;
                    return;
                }

                setScan("membuka kamera selfie...");
                await startSelfieCamera();

                await countdown(3);

                setScan("mengambil foto...");
                const blob = await captureSelfieBlob();
                if (!blob) {
                    setScan("gagal ambil foto.");
                    stopSelfie();
                    busy = false;
                    return;
                }

                setScan("mengirim absensi...");
                const ok = await submitAttendance(currentMode, scannedToken, blob);

                stopSelfie();

                if (ok) {
                    setScan(`berhasil ✅ ${currentMode === 'in' ? 'CHECK-IN' : 'CHECK-OUT'}`);
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    // submitAttendance sudah set message error
                    busy = false;
                }

            } catch (e) {
                setScan("error: " + (e?.message || "unknown"));
                stopSelfie();
                busy = false;
            }
        }

        // ---------- SUBMIT ----------
        async function submitAttendance(mode, qrToken, selfieBlob) {
            try {
                const fd = new FormData();
                fd.append("mode", mode);
                fd.append("qr_token", qrToken);
                fd.append("device_hash", deviceHash);
                fd.append("lat", geo.lat);
                fd.append("lng", geo.lng);
                fd.append("selfie", selfieBlob, "selfie.jpg");

                const res = await fetch("{{ route('pegawai.attendance.submit') }}", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": csrf },
                    body: fd
                });

                const json = await res.json().catch(() => ({}));

                if (!res.ok) {
                    setScan(json.message || "gagal.");
                    return false;
                }

                return true;
            } catch (e) {
                setScan("error submit: " + (e?.message || "unknown"));
                return false;
            }
        }

        // ---------- EVENTS ----------
        btnInit.addEventListener("click", initGate);

        btnCheckIn.addEventListener("click", () => {
            if (btnCheckIn.disabled) return;
            startQrScanner("in");
        });

        btnCheckOut.addEventListener("click", () => {
            if (btnCheckOut.disabled) return;
            startQrScanner("out");
        });

        // initial state
        setButtonsAfterGate();
    </script>
@endsection