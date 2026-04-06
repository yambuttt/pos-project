@extends('layouts.pegawai')
@section('title', 'Absensi')

@section('page_label', 'Pegawai')
@section('page_title', 'Absensi (QR + Lokasi + Selfie)')

@section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
    <div class="text-sm font-semibold">Status Hari Ini</div>
    <div class="mt-3 space-y-1 text-sm text-white/80">
      <div>Check-in: <b>{{ $attendance?->check_in_at?->format('H:i') ?? '--:--' }}</b></div>
      <div>Check-out: <b>{{ $attendance?->check_out_at?->format('H:i') ?? '--:--' }}</b></div>
    </div>
  </div>

  <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.1fr_.9fr]">
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
      <div class="text-sm font-semibold">1) Verifikasi Device & Lokasi</div>
      <div class="mt-2 text-xs text-white/70">
        Aplikasi akan cek device terdaftar dan lokasi berada di area restoran.
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <button id="btnInit" class="rounded-xl bg-emerald-600/80 px-4 py-2 text-sm font-semibold hover:bg-emerald-500/80">
          Mulai Verifikasi
        </button>
      </div>

      <div id="gateStatus" class="mt-3 text-sm text-white/80">Status: belum verifikasi.</div>
    </div>

    <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
      <div class="text-sm font-semibold">2) Scan QR + Selfie</div>
      <div class="mt-2 text-xs text-white/70">
        Tekan tombol Check-in / Check-out untuk membuka kamera (popup fullscreen).
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

  {{-- CAMERA MODAL (FULLSCREEN) --}}
  <div id="camModal" class="fixed inset-0 z-50 hidden bg-black/80">
    <div class="flex h-full w-full flex-col">
      <div class="flex items-center justify-between border-b border-white/10 bg-black/40 px-4 py-3 backdrop-blur-xl">
        <div>
          <div id="camTitle" class="text-sm font-semibold text-white">Kamera</div>
          <div id="camSubtitle" class="text-xs text-white/60">...</div>
        </div>
        <button id="camCloseBtn"
          class="rounded-xl border border-white/15 bg-white/[0.06] px-3 py-2 text-xs text-white/80 hover:bg-white/[0.10]">
          Tutup
        </button>
      </div>

      <div class="flex-1 overflow-hidden p-4">
        <div class="mx-auto w-full max-w-xl">
          {{-- QR reader (kamera belakang) --}}
          <div id="qrWrap" class="hidden overflow-hidden rounded-2xl border border-white/10 bg-black/40">
            <div id="qrReader" class="w-full"></div>
          </div>

          {{-- Selfie (kamera depan) --}}
          <div id="selfieWrap" class="hidden overflow-hidden rounded-2xl border border-white/10 bg-black/40">
            <div class="relative">
              <video id="selfieVideo" autoplay playsinline class="aspect-video w-full object-cover"></video>

              <!-- overlay untuk face landmarks -->
              <canvas id="faceOverlay" class="absolute inset-0 h-full w-full"></canvas>

              <!-- hint text -->
              <div id="faceHint"
                class="absolute bottom-3 left-3 rounded-xl border border-white/15 bg-black/40 px-3 py-2 text-xs text-white/85 backdrop-blur-xl">
                Menyiapkan deteksi wajah…
              </div>
            </div>

            <canvas id="selfieCanvas" class="hidden"></canvas>
          </div>

          <div class="mt-3 rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3">
            <div id="camStatus" class="text-sm text-white/85">Menyiapkan kamera…</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/html5-qrcode"></script>
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js"></script>

  <script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // status attendance dari server (blade)
    const HAS_CHECKIN = {{ $attendance?->check_in_at ? 'true' : 'false' }};
    const HAS_CHECKOUT = {{ $attendance?->check_out_at ? 'true' : 'false' }};

    let deviceHash = null;
    let geo = null; // {lat,lng}

    let reader = null;
    let selfieStream = null;

    let faceMesh = null;
    let faceDetectRunning = false;
    let faceOk = false;
    let faceOkStreak = 0;     // hitung frame berturut-turut terdeteksi
    const FACE_OK_NEED = 6;   // butuh 6 frame (~0.2-0.5s) biar stabil

    let gateOk = false;
    let busy = false;
    let currentMode = null;      // 'in' atau 'out'
    let scannedToken = null;

    const gateStatus = document.getElementById('gateStatus');
    const scanStatus = document.getElementById('scanStatus');
    const btnInit = document.getElementById('btnInit');
    const btnCheckIn = document.getElementById('btnCheckIn');
    const btnCheckOut = document.getElementById('btnCheckOut');

    // modal elements
    const camModal = document.getElementById('camModal');
    const camTitle = document.getElementById('camTitle');
    const camSubtitle = document.getElementById('camSubtitle');
    const camStatus = document.getElementById('camStatus');
    const camCloseBtn = document.getElementById('camCloseBtn');
    const qrWrap = document.getElementById('qrWrap');
    const selfieWrap = document.getElementById('selfieWrap');

    const video = document.getElementById('selfieVideo');
    const canvas = document.getElementById('selfieCanvas');
    const faceOverlay = document.getElementById('faceOverlay');
    const faceHint = document.getElementById('faceHint');

    function setGate(msg) { gateStatus.textContent = "Status: " + msg; }
    function setScan(msg) { scanStatus.textContent = "Status: " + msg; }
    function setCamStatus(msg) { camStatus.textContent = msg; }

    function setFaceHint(msg, ok = false) {
      if (!faceHint) return;
      faceHint.textContent = msg;
      faceHint.style.borderColor = ok ? 'rgba(16,185,129,.35)' : 'rgba(255,255,255,.15)';
    }

    function resizeOverlayToVideo() {
      if (!faceOverlay) return;
      const w = video.videoWidth || 640;
      const h = video.videoHeight || 480;
      faceOverlay.width = w;
      faceOverlay.height = h;
    }

    function openCamModal(title, subtitle) {
      camTitle.textContent = title || 'Kamera';
      camSubtitle.textContent = subtitle || '';
      setCamStatus('Menyiapkan kamera…');

      camModal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeCamModal() {
      camModal.classList.add('hidden');
      document.body.style.overflow = '';

      stopQrScanner();
      stopSelfie();

      qrWrap.classList.add('hidden');
      selfieWrap.classList.add('hidden');
      setCamStatus('');
      busy = false;
    }

    camCloseBtn.addEventListener('click', closeCamModal);
    camModal.addEventListener('click', (e) => { if (e.target === camModal) closeCamModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !camModal.classList.contains('hidden')) closeCamModal(); });

    function setButtonsAfterGate() {
      btnCheckIn.disabled = true;
      btnCheckOut.disabled = true;

      if (!gateOk) return;

      if (HAS_CHECKIN && HAS_CHECKOUT) {
        setScan("kamu sudah check-in & check-out hari ini.");
        return;
      }

      if (HAS_CHECKIN && !HAS_CHECKOUT) {
        btnCheckOut.disabled = false;
        setScan("silakan tekan Check-out untuk scan QR checkout.");
        return;
      }

      if (!HAS_CHECKIN) {
        btnCheckIn.disabled = false;
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

    async function detectDeviceName() {
      const ua = navigator.userAgent || '';

      let platform = 'Web';
      if (/Android/i.test(ua)) platform = 'Android';
      else if (/iPhone|iPad|iPod/i.test(ua)) platform = 'iOS';
      else if (/Windows/i.test(ua)) platform = 'Windows';
      else if (/Macintosh/i.test(ua)) platform = 'macOS';
      else if (/Linux/i.test(ua)) platform = 'Linux';

      let androidVer = '';
      const av = ua.match(/Android\s([\d\.]+)/i);
      if (av && av[1]) androidVer = av[1];

      let model = '';
      if (platform === 'Android') {
        const m = ua.match(/Android\s[\d\.]+;\s([^;)]*?)\sBuild\//i) || ua.match(/Android\s[\d\.]+;\s([^;)]*)/i);
        if (m && m[1]) model = m[1].trim();
      }

      let browser = '';
      if (/Edg\/([\d\.]+)/i.test(ua)) browser = 'Edge ' + (ua.match(/Edg\/([\d\.]+)/i)?.[1]?.split('.')[0] || '');
      else if (/Chrome\/([\d\.]+)/i.test(ua) && !/Edg\//i.test(ua)) browser = 'Chrome ' + (ua.match(/Chrome\/([\d\.]+)/i)?.[1]?.split('.')[0] || '');
      else if (/Firefox\/([\d\.]+)/i.test(ua)) browser = 'Firefox ' + (ua.match(/Firefox\/([\d\.]+)/i)?.[1]?.split('.')[0] || '');
      else if (/Safari\/([\d\.]+)/i.test(ua) && /Version\/([\d\.]+)/i.test(ua)) browser = 'Safari ' + (ua.match(/Version\/([\d\.]+)/i)?.[1]?.split('.')[0] || '');

      try {
        if (navigator.userAgentData?.getHighEntropyValues) {
          const v = await navigator.userAgentData.getHighEntropyValues(['platform', 'model']);
          if (v?.platform) platform = v.platform;
          if (v?.model) model = v.model;
        }
      } catch (e) {}

      const parts = [];
      parts.push(platform + (androidVer ? ` ${androidVer}` : ''));
      if (model) parts.push(model);
      if (browser) parts.push(browser);

      return parts.join(' • ').replace(/\s+/g, ' ').trim() || 'Web';
    }

    async function detectDeviceNameAsync() {
      // FIX: base harus string (bukan Promise)
      let base = await detectDeviceName();
      try {
        if (navigator.userAgentData && navigator.userAgentData.getHighEntropyValues) {
          const v = await navigator.userAgentData.getHighEntropyValues(['platform', 'model', 'uaFullVersion']);
          if (v && v.platform) {
            const parts = [];
            parts.push(v.platform);
            if (v.model) parts.push(v.model);
            if (v.uaFullVersion) parts.push('UA ' + v.uaFullVersion);
            base = parts.join(' • ');
          }
        }
      } catch (e) {}
      return base || 'Web';
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
        const deviceName = await detectDeviceNameAsync();

        const res = await fetch("{{ route('pegawai.attendance.device.init') }}", {
          method: "POST",
          headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
          body: JSON.stringify({
            device_hash: deviceHash,
            device_name: deviceName,
            lat: geo.lat,
            lng: geo.lng
          })
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

    // ---------- QR FLOW (IN MODAL) ----------
    async function startQrScanner(mode) {
      if (busy) return;
      if (!gateOk) { setScan("verifikasi device & lokasi dulu."); return; }

      busy = true;
      currentMode = mode;
      scannedToken = null;

      openCamModal(
        mode === 'in' ? 'Scan QR Check-in' : 'Scan QR Check-out',
        'Arahkan kamera belakang ke QR dari admin'
      );

      qrWrap.classList.remove('hidden');
      selfieWrap.classList.add('hidden');

      try {
        setCamStatus('Membuka kamera QR…');

        stopSelfie();
        await stopQrScanner();

        reader = new Html5Qrcode("qrReader");
        await reader.start(
          { facingMode: "environment" },
          { fps: 10, qrbox: 240 },
          async (decodedText) => {
            scannedToken = (decodedText || "").trim();
            if (!scannedToken) return;

            await stopQrScanner();
            setCamStatus("QR valid ✅ Menyiapkan selfie…");
            await startSelfieAndCountdownThenSubmit();
          },
          () => { }
        );

        setCamStatus('Silakan scan QR…');
      } catch (e) {
        setCamStatus('Gagal membuka kamera QR. Pastikan izin kamera diberikan.');
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

      video.srcObject = selfieStream;
      await video.play();
    }

    async function startFaceDetection() {
      if (faceDetectRunning) return;
      faceDetectRunning = true;
      faceOk = false;
      faceOkStreak = 0;

      resizeOverlayToVideo();
      const ctx = faceOverlay.getContext('2d');

      if (!faceMesh) {
        faceMesh = new FaceMesh.FaceMesh({
          locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`
        });

        faceMesh.setOptions({
          maxNumFaces: 1,
          refineLandmarks: true,
          minDetectionConfidence: 0.6,
          minTrackingConfidence: 0.6,
        });

        faceMesh.onResults((results) => {
          if (!faceDetectRunning) return;

          ctx.clearRect(0, 0, faceOverlay.width, faceOverlay.height);

          const faces = results.multiFaceLandmarks || [];
          if (faces.length === 0) {
            faceOkStreak = 0;
            faceOk = false;
            setFaceHint("Wajah tidak terdeteksi. Arahkan kamera ke wajah.", false);
            return;
          }

          faceOkStreak += 1;
          if (faceOkStreak >= FACE_OK_NEED) faceOk = true;

          setFaceHint(faceOk ? "Wajah OK ✅ Tahan sebentar…" : "Wajah terdeteksi… (stabilkan)", faceOk);

          const landmarks = faces[0];
          drawLandmarks(ctx, landmarks, { color: '#7dd3fc', lineWidth: 1 });
          drawConnectors(ctx, landmarks, FaceMesh.FACEMESH_TESSELATION, { color: '#ffffff', lineWidth: 0.6 });
          drawConnectors(ctx, landmarks, FaceMesh.FACEMESH_FACE_OVAL, { color: '#22c55e', lineWidth: 1.2 });
          drawConnectors(ctx, landmarks, FaceMesh.FACEMESH_LEFT_EYE, { color: '#60a5fa', lineWidth: 1 });
          drawConnectors(ctx, landmarks, FaceMesh.FACEMESH_RIGHT_EYE, { color: '#60a5fa', lineWidth: 1 });
          drawConnectors(ctx, landmarks, FaceMesh.FACEMESH_LIPS, { color: '#fb7185', lineWidth: 1 });
        });
      }

      async function loop() {
        if (!faceDetectRunning) return;
        try {
          await faceMesh.send({ image: video });
        } catch (e) {}
        requestAnimationFrame(loop);
      }

      setFaceHint("Menyiapkan deteksi wajah…", false);
      requestAnimationFrame(loop);
    }

    function stopFaceDetection() {
      faceDetectRunning = false;
      faceOk = false;
      faceOkStreak = 0;

      try {
        if (faceOverlay) {
          const ctx = faceOverlay.getContext('2d');
          ctx.clearRect(0, 0, faceOverlay.width, faceOverlay.height);
        }
      } catch (e) { }
    }

    function stopSelfie() {
      stopFaceDetection();
      try {
        if (selfieStream) {
          selfieStream.getTracks().forEach(t => t.stop());
          selfieStream = null;
        }
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
        setCamStatus(`Selfie otomatis dalam ${i}…`);
        await new Promise(r => setTimeout(r, 1000));
      }
    }

    async function startSelfieAndCountdownThenSubmit() {
      try {
        if (!deviceHash || !geo || !scannedToken || !currentMode) {
          setCamStatus("Data belum lengkap. Ulangi proses.");
          busy = false;
          return;
        }

        camTitle.textContent = currentMode === 'in' ? 'Selfie Check-in' : 'Selfie Check-out';
        camSubtitle.textContent = 'Ambil selfie dari kamera depan (bukan galeri)';

        qrWrap.classList.add('hidden');
        selfieWrap.classList.remove('hidden');

        setCamStatus("Membuka kamera selfie…");
        await startSelfieCamera();

        setCamStatus("Deteksi wajah aktif. Tunjukkan wajah ke kamera…");
        await startFaceDetection();

        let waited = 0;
        while (!faceOk) {
          await new Promise(r => setTimeout(r, 200));
          waited += 200;

          if (waited >= 15000) {
            setCamStatus("Wajah tidak terdeteksi. Pastikan pencahayaan cukup & wajah terlihat jelas.");
            stopSelfie();
            busy = false;
            return;
          }
        }

        await countdown(3);

        setCamStatus("Mengambil foto…");
        const selfieBlob = await captureSelfieBlob(); // FIX: hanya 1 deklarasi
        if (!selfieBlob) {
          setCamStatus("Gagal ambil foto.");
          stopSelfie();
          busy = false;
          return;
        }

        setCamStatus("Mengirim absensi…");
        const ok = await submitAttendance(currentMode, scannedToken, selfieBlob);

        stopSelfie();

        if (ok) {
          setCamStatus(`Berhasil ✅ ${currentMode === 'in' ? 'CHECK-IN' : 'CHECK-OUT'}`);
          setTimeout(() => window.location.reload(), 800);
        } else {
          busy = false;
        }
      } catch (e) {
        setCamStatus("Error: " + (e?.message || "unknown"));
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
          setCamStatus(json.message || "gagal.");
          return false;
        }

        return true;
      } catch (e) {
        setScan("error submit: " + (e?.message || "unknown"));
        setCamStatus("error submit: " + (e?.message || "unknown"));
        return false;
      }
    }

    // EVENTS
    btnInit.addEventListener("click", initGate);
    btnCheckIn.addEventListener("click", () => { if (!btnCheckIn.disabled) startQrScanner("in"); });
    btnCheckOut.addEventListener("click", () => { if (!btnCheckOut.disabled) startQrScanner("out"); });

    setButtonsAfterGate();
  </script>
@endsection