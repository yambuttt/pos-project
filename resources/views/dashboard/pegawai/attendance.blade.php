@extends('layouts.pegawai')
@section('title', 'Absensi')

@section('page_label', 'Pegawai')
@section('page_title', 'Absensi (Face + Challenge)')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(!$profile)
        <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
            <div class="text-sm font-semibold">Wajah belum terdaftar</div>
            <div class="mt-2 text-sm text-white/70">
                Kamu harus daftar wajah dulu sebelum absen.
            </div>
            <a href="{{ route('pegawai.face.enroll') }}"
                class="mt-4 inline-flex rounded-xl bg-emerald-600/80 px-4 py-2 text-sm font-semibold hover:bg-emerald-500/80">
                Daftarkan Wajah
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[1.1fr_.9fr]">
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                <div class="text-sm font-semibold">Kamera</div>
                <div class="mt-1 text-xs text-white/70">
                    Lakukan challenge: <b>hadap kiri</b> lalu <b>hadap kanan</b>. Setelah lolos, baru verifikasi wajah.
                </div>

                <div class="mt-4 overflow-hidden rounded-2xl border border-white/15 bg-black/20">
                    <video id="video" autoplay playsinline class="w-full aspect-video object-cover"></video>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button id="btnStart"
                        class="rounded-xl bg-emerald-600/80 px-4 py-2 text-sm font-semibold hover:bg-emerald-500/80">
                        Nyalakan Kamera
                    </button>

                    <button id="btnCheckIn" disabled
                        class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15 disabled:opacity-50">
                        Check-in
                    </button>

                    <button id="btnCheckOut" disabled
                        class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15 disabled:opacity-50">
                        Check-out
                    </button>
                </div>

                <div id="status" class="mt-3 text-sm text-white/75">Status: siap.</div>
            </div>

            <div class="space-y-5">
                <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                    <div class="text-sm font-semibold">Status Hari Ini</div>
                    <div class="mt-3 text-sm text-white/75 space-y-1">
                        <div>Check-in: <b>{{ $attendance?->check_in_at?->format('H:i') ?? '--:--' }}</b></div>
                        <div>Check-out: <b>{{ $attendance?->check_out_at?->format('H:i') ?? '--:--' }}</b></div>
                    </div>
                    <div class="mt-4 rounded-2xl border border-white/15 bg-white/5 p-4 text-xs text-white/70">
                        Tips akurasi: cahaya cukup, wajah lurus, jangan terlalu dekat.
                    </div>
                </div>

                <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                    <div class="text-sm font-semibold">Challenge</div>
                    <div class="mt-2 text-sm text-white/70" id="challengeText">Belum mulai.</div>
                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-white/10">
                        <div id="challengeBar" class="h-2 w-0 bg-white/60 transition-all"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script>
        const MODEL_URL = "/face/models";

        const video = document.getElementById('video');
        const btnStart = document.getElementById('btnStart');
        const btnCheckIn = document.getElementById('btnCheckIn');
        const btnCheckOut = document.getElementById('btnCheckOut');
        const statusEl = document.getElementById('status');
        const challengeText = document.getElementById('challengeText');
        const challengeBar = document.getElementById('challengeBar');

        let modelsLoaded = false;
        let stream = null;
        let challengePassed = false;

        function setStatus(t) { if (statusEl) statusEl.textContent = "Status: " + t; }
        function setChallenge(t, pct) {
            if (challengeText) challengeText.textContent = t;
            if (challengeBar) challengeBar.style.width = (pct || 0) + "%";
        }
        function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

        // Detektor yang lebih stabil untuk HP
        let DETECTOR_OPTS = null;

        async function loadModels() {
            if (modelsLoaded) return;
            setStatus("loading model...");
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);

            DETECTOR_OPTS = new faceapi.TinyFaceDetectorOptions({
                inputSize: 320,
                scoreThreshold: 0.35
            });

            modelsLoaded = true;
            setStatus("model siap.");
        }

        async function startCamera() {
            await loadModels();

            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "user",
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                },
                audio: false
            });

            video.srcObject = stream;
            await video.play();

            // Warm-up kamera biar exposure stabil
            await new Promise(r => video.onloadedmetadata = r);
            await sleep(700);

            setStatus("kamera aktif. mulai challenge...");
            await runChallenge();
        }

        // === Pose detection sederhana dari landmark ===
        async function getPose() {
            const det = await faceapi
                .detectSingleFace(video, DETECTOR_OPTS)
                .withFaceLandmarks();

            if (!det) return null;

            const lm = det.landmarks;
            const leftEye = lm.getLeftEye();
            const rightEye = lm.getRightEye();
            const nose = lm.getNose();

            const eyeCenterX = (leftEye[0].x + rightEye[3].x) / 2;
            const noseTipX = nose[3].x;

            const diff = noseTipX - eyeCenterX;

            // lebih toleran supaya challenge tidak “sensitif”
            if (diff < -8) return "left";
            if (diff > 8) return "right";
            return "center";
        }

        async function runChallenge() {
            challengePassed = false;
            if (btnCheckIn) btnCheckIn.disabled = true;
            if (btnCheckOut) btnCheckOut.disabled = true;

            setChallenge("1/2: Hadap KIRI", 10);

            let okLeft = false;
            for (let i = 0; i < 45; i++) { // ~9 detik
                const pose = await getPose();
                if (pose === "left") { okLeft = true; break; }
                setChallenge("1/2: Hadap KIRI (posisikan wajah)", Math.min(55, 10 + i * 1.2));
                await sleep(200);
            }
            if (!okLeft) {
                setChallenge("Gagal: tidak terdeteksi hadap kiri. Coba lagi.", 0);
                setStatus("challenge gagal");
                return;
            }

            setChallenge("2/2: Hadap KANAN", 60);

            let okRight = false;
            for (let i = 0; i < 45; i++) {
                const pose = await getPose();
                if (pose === "right") { okRight = true; break; }
                setChallenge("2/2: Hadap KANAN (posisikan wajah)", Math.min(95, 60 + i * 0.8));
                await sleep(200);
            }
            if (!okRight) {
                setChallenge("Gagal: tidak terdeteksi hadap kanan. Coba lagi.", 0);
                setStatus("challenge gagal");
                return;
            }

            challengePassed = true;
            setChallenge("Challenge lolos ✅", 100);
            setStatus("siap verifikasi wajah.");
            if (btnCheckIn) btnCheckIn.disabled = false;
            if (btnCheckOut) btnCheckOut.disabled = false;
        }

        // === Ambil descriptor lebih stabil: retry + multi frame ===
        async function captureDescriptorRetry(maxTries = 10) {
            for (let i = 0; i < maxTries; i++) {
                const det = await faceapi
                    .detectSingleFace(video, DETECTOR_OPTS)
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (det) return Array.from(det.descriptor);
                await sleep(150);
            }
            return null;
        }

        async function captureMultiDescriptors(n = 3) {
            const arr = [];
            for (let i = 0; i < n; i++) {
                const d = await captureDescriptorRetry(12);
                if (!d) return null;
                arr.push(d);
                await sleep(200);
            }
            return arr;
        }

        async function submitAttendance(url) {
            if (!challengePassed) {
                setStatus("challenge belum lolos.");
                return;
            }

            setStatus("mengambil wajah (3 frame)...");
            const descriptors = await captureMultiDescriptors(3);

            if (!descriptors) {
                setStatus("wajah tidak terdeteksi. coba tempat lebih terang / wajah lebih dekat.");
                return;
            }

            setStatus("verifikasi & simpan...");
            const res = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ descriptors })
            });

            const json = await res.json().catch(() => ({}));

            if (!res.ok) {
                setStatus(json.message || "gagal verifikasi.");
                // ulang challenge biar tidak spam verifikasi
                await sleep(600);
                await runChallenge();
                return;
            }

            setStatus(`berhasil ✅ (distance: ${json.distance?.toFixed?.(4) ?? '-'})`);
            setTimeout(() => window.location.reload(), 900);
        }

        // === Bind tombol ===
        if (btnStart) btnStart.addEventListener('click', startCamera);
        if (btnCheckIn) btnCheckIn.addEventListener('click', () => submitAttendance("{{ route('pegawai.attendance.checkin') }}"));
        if (btnCheckOut) btnCheckOut.addEventListener('click', () => submitAttendance("{{ route('pegawai.attendance.checkout') }}"));
    </script>
@endsection