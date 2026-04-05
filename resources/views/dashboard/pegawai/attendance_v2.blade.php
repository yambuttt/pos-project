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
      <button id="btnInit"
        class="rounded-xl bg-emerald-600/80 px-4 py-2 text-sm font-semibold hover:bg-emerald-500/80">
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
          <video id="selfieVideo" autoplay playsinline class="aspect-video w-full object-cover"></video>
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
<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  // status attendance dari server (blade)
  const HAS_CHECKIN = {{ $attendance?->check_in_at ? 'true' : 'false' }};
  const HAS_CHECKOUT = {{ $attendance?->check_out_at ? 'true' : 'false' }};

  let deviceHash = null;
  let geo = null; // {lat,lng}

  let reader = null;
  let selfieStream = null;

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

  function setGate(msg){ gateStatus.textContent = "Status: " + msg; }
  function setScan(msg){ scanStatus.textContent = "Status: " + msg; }
  function setCamStatus(msg){ camStatus.textContent = msg; }

  function openCamModal(title, subtitle){
    camTitle.textContent = title || 'Kamera';
    camSubtitle.textContent = subtitle || '';
    setCamStatus('Menyiapkan kamera…');

    camModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }

  function closeCamModal(){
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

  function setButtonsAfterGate(){
    btnCheckIn.disabled = true;
    btnCheckOut.disabled = true;

    if(!gateOk) return;

    if(HAS_CHECKIN && HAS_CHECKOUT){
      setScan("kamu sudah check-in & check-out hari ini.");
      return;
    }

    if(HAS_CHECKIN && !HAS_CHECKOUT){
      btnCheckOut.disabled = false;
      setScan("silakan tekan Check-out untuk scan QR checkout.");
      return;
    }

    if(!HAS_CHECKIN){
      btnCheckIn.disabled = false;
      setScan("silakan tekan Check-in untuk scan QR checkin.");
      return;
    }
  }

  async function sha256Hex(str){
    const enc = new TextEncoder().encode(str);
    const buf = await crypto.subtle.digest('SHA-256', enc);
    return Array.from(new Uint8Array(buf)).map(b=>b.toString(16).padStart(2,'0')).join('');
  }

  function buildFingerprint(){
    const parts = [
      navigator.userAgent,
      navigator.language,
      screen.width + "x" + screen.height,
      Intl.DateTimeFormat().resolvedOptions().timeZone
    ];
    return parts.join("|");
  }

  async function getGeo(){
    return new Promise((resolve, reject) => {
      if(!navigator.geolocation) return reject(new Error("Geolocation tidak didukung."));
      navigator.geolocation.getCurrentPosition(
        (pos) => resolve({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
        (err) => reject(err),
        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
      );
    });
  }

  async function initGate(){
    if(busy) return;
    busy = true;

    try{
      setGate("mengambil fingerprint device...");
      deviceHash = await sha256Hex(buildFingerprint());

      setGate("cek lokasi...");
      geo = await getGeo();

      setGate("cek device ke server...");
      const res = await fetch("{{ route('pegawai.attendance.device.init') }}", {
        method: "POST",
        headers: { "Content-Type":"application/json", "X-CSRF-TOKEN": csrf },
        body: JSON.stringify({ device_hash: deviceHash, device_name: "Web" })
      });

      const json = await res.json().catch(()=> ({}));

      if(!res.ok){
        gateOk = false;
        setGate(json.message || "device belum valid.");
        setScan("tidak bisa lanjut sebelum device approved.");
        setButtonsAfterGate();
        return;
      }

      gateOk = true;
      setGate("device OK + lokasi OK ✅");
      setButtonsAfterGate();

    } catch(e){
      gateOk = false;
      setGate("gagal ambil lokasi. aktifkan GPS & izin lokasi.");
      setScan("menunggu verifikasi.");
      setButtonsAfterGate();
    } finally {
      busy = false;
    }
  }

  // ---------- QR FLOW (IN MODAL) ----------
  async function startQrScanner(mode){
    if(busy) return;
    if(!gateOk){ setScan("verifikasi device & lokasi dulu."); return; }

    busy = true;
    currentMode = mode;
    scannedToken = null;

    openCamModal(
      mode === 'in' ? 'Scan QR Check-in' : 'Scan QR Check-out',
      'Arahkan kamera belakang ke QR dari admin'
    );

    qrWrap.classList.remove('hidden');
    selfieWrap.classList.add('hidden');

    try{
      setCamStatus('Membuka kamera QR…');

      stopSelfie();
      await stopQrScanner();

      reader = new Html5Qrcode("qrReader");
      await reader.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 240 },
        async (decodedText) => {
          scannedToken = (decodedText || "").trim();
          if(!scannedToken) return;

          await stopQrScanner();
          setCamStatus("QR valid ✅ Menyiapkan selfie…");
          await startSelfieAndCountdownThenSubmit();
        },
        () => {}
      );

      setCamStatus('Silakan scan QR…');
    } catch(e){
      setCamStatus('Gagal membuka kamera QR. Pastikan izin kamera diberikan.');
      busy = false;
    }
  }

  async function stopQrScanner(){
    try{
      if(reader){
        await reader.stop();
        reader.clear();
        reader = null;
      }
    } catch(e){}
  }

  // ---------- SELFIE FLOW ----------
  async function startSelfieCamera(){
    if(selfieStream) return;

    selfieStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: "user", width:{ideal:640}, height:{ideal:480} },
      audio: false
    });

    video.srcObject = selfieStream;
    await video.play();
  }

  function stopSelfie(){
    try{
      if(selfieStream){
        selfieStream.getTracks().forEach(t => t.stop());
        selfieStream = null;
      }
      video.srcObject = null;
    } catch(e){}
  }

  function captureSelfieBlob(){
    const w = video.videoWidth || 640;
    const h = video.videoHeight || 480;
    canvas.width = w;
    canvas.height = h;
    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0, w, h);
    return new Promise(resolve => canvas.toBlob(resolve, "image/jpeg", 0.9));
  }

  async function countdown(seconds){
    for(let i=seconds; i>=1; i--){
      setCamStatus(`Selfie otomatis dalam ${i}…`);
      await new Promise(r => setTimeout(r, 1000));
    }
  }

  async function startSelfieAndCountdownThenSubmit(){
    try{
      if(!deviceHash || !geo || !scannedToken || !currentMode){
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

      await countdown(3);

      setCamStatus("Mengambil foto…");
      const blob = await captureSelfieBlob();
      if(!blob){
        setCamStatus("Gagal ambil foto.");
        stopSelfie();
        busy = false;
        return;
      }

      setCamStatus("Mengirim absensi…");
      const ok = await submitAttendance(currentMode, scannedToken, blob);

      stopSelfie();

      if(ok){
        setCamStatus(`Berhasil ✅ ${currentMode === 'in' ? 'CHECK-IN' : 'CHECK-OUT'}`);
        setTimeout(()=>window.location.reload(), 800);
      } else {
        busy = false;
      }
    } catch(e){
      setCamStatus("Error: " + (e?.message || "unknown"));
      stopSelfie();
      busy = false;
    }
  }

  // ---------- SUBMIT ----------
  async function submitAttendance(mode, qrToken, selfieBlob){
    try{
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

      const json = await res.json().catch(()=> ({}));

      if(!res.ok){
        setScan(json.message || "gagal.");
        setCamStatus(json.message || "gagal.");
        return false;
      }

      return true;
    } catch(e){
      setScan("error submit: " + (e?.message || "unknown"));
      setCamStatus("error submit: " + (e?.message || "unknown"));
      return false;
    }
  }

  // EVENTS
  btnInit.addEventListener("click", initGate);
  btnCheckIn.addEventListener("click", () => { if(!btnCheckIn.disabled) startQrScanner("in"); });
  btnCheckOut.addEventListener("click", () => { if(!btnCheckOut.disabled) startQrScanner("out"); });

  setButtonsAfterGate();
</script>
@endsection