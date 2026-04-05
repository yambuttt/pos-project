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
    <div class="mt-2 text-xs text-white/70">Aplikasi akan cek device terdaftar dan lokasi berada di area restoran.</div>

    <div class="mt-4 flex flex-wrap gap-2">
      <button id="btnInit" class="rounded-xl bg-emerald-600/80 px-4 py-2 text-sm font-semibold hover:bg-emerald-500/80">
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
      <button id="btnCheckIn" disabled class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15 disabled:opacity-50">
        Check-in
      </button>
      <button id="btnCheckOut" disabled class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15 disabled:opacity-50">
        Check-out
      </button>
    </div>

    <div id="scanStatus" class="mt-3 text-sm text-white/80">Status: menunggu verifikasi.</div>
  </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  let deviceHash = null;
  let geo = null; // {lat,lng}
  let scannedToken = null;
  let reader = null;
  let selfieStream = null;

  const gateStatus = document.getElementById('gateStatus');
  const scanStatus = document.getElementById('scanStatus');
  const btnInit = document.getElementById('btnInit');
  const btnCheckIn = document.getElementById('btnCheckIn');
  const btnCheckOut = document.getElementById('btnCheckOut');
  const video = document.getElementById('selfieVideo');
  const canvas = document.getElementById('selfieCanvas');

  function setGate(msg){ gateStatus.textContent = "Status: " + msg; }
  function setScan(msg){ scanStatus.textContent = "Status: " + msg; }

  async function sha256Hex(str){
    const enc = new TextEncoder().encode(str);
    const buf = await crypto.subtle.digest('SHA-256', enc);
    return Array.from(new Uint8Array(buf)).map(b=>b.toString(16).padStart(2,'0')).join('');
  }

  function buildFingerprint(){
    // ini bukan “anti-spoof 100%”, tapi cukup untuk device locking praktis
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
      if (!navigator.geolocation) return reject(new Error("Geolocation tidak didukung."));
      navigator.geolocation.getCurrentPosition(
        (pos) => resolve({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
        (err) => reject(err),
        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
      );
    });
  }

  async function initGate(){
    setGate("mengambil fingerprint device...");
    deviceHash = await sha256Hex(buildFingerprint());

    setGate("cek lokasi...");
    try {
      geo = await getGeo();
    } catch(e){
      setGate("gagal ambil lokasi. aktifkan GPS & izin lokasi.");
      return;
    }

    setGate("cek device ke server...");
    const res = await fetch("{{ route('pegawai.attendance.device.init') }}", {
      method: "POST",
      headers: { "Content-Type":"application/json", "X-CSRF-TOKEN": csrf },
      body: JSON.stringify({ device_hash: deviceHash, device_name: "Web" })
    });
    const json = await res.json().catch(()=>({}));

    if(!res.ok){
      setGate(json.message || "device belum valid.");
      setScan("tidak bisa scan sebelum device approved.");
      btnCheckIn.disabled = true;
      btnCheckOut.disabled = true;
      return;
    }

    setGate("device OK + lokasi didapat ✅");
    setScan("silakan scan QR admin.");

    btnCheckIn.disabled = false;
    btnCheckOut.disabled = false;

    startQrScanner();
  }

  async function startQrScanner(){
    if(reader) return;
    reader = new Html5Qrcode("qrReader");

    try{
      await reader.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 240 },
        (decodedText) => {
          scannedToken = decodedText.trim();
          setScan("QR terbaca ✅. siap ambil selfie.");
        },
        () => {}
      );
    }catch(e){
      setScan("kamera QR gagal dibuka. izinkan kamera.");
    }
  }

  async function startSelfieCamera(){
    if(selfieStream) return;
    selfieStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: "user", width:{ideal:640}, height:{ideal:480} },
      audio: false
    });
    video.classList.remove("hidden");
    video.srcObject = selfieStream;
    await video.play();
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

  async function submit(mode){
    if(!deviceHash){ setScan("device belum diverifikasi."); return; }
    if(!geo){ setScan("lokasi belum didapat."); return; }
    if(!scannedToken){ setScan("scan QR dulu."); return; }

    try{
      setScan("membuka kamera selfie...");
      await startSelfieCamera();
      setScan("ambil selfie otomatis...");
      const blob = await captureSelfieBlob();
      if(!blob){ setScan("gagal ambil foto."); return; }

      setScan("mengirim absensi...");
      const fd = new FormData();
      fd.append("mode", mode);
      fd.append("qr_token", scannedToken);
      fd.append("device_hash", deviceHash);
      fd.append("lat", geo.lat);
      fd.append("lng", geo.lng);
      fd.append("selfie", blob, "selfie.jpg");

      const res = await fetch("{{ route('pegawai.attendance.submit') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": csrf },
        body: fd
      });
      const json = await res.json().catch(()=>({}));

      if(!res.ok){
        setScan(json.message || "gagal.");
        return;
      }

      setScan("berhasil ✅");
      setTimeout(()=>window.location.reload(), 900);
    }catch(e){
      setScan("error: " + (e?.message || "unknown"));
    }
  }

  btnInit.addEventListener("click", initGate);
  btnCheckIn.addEventListener("click", ()=>submit("in"));
  btnCheckOut.addEventListener("click", ()=>submit("out"));
</script>
@endsection