@extends('layouts.pegawai')
@section('title','Daftarkan Wajah')

@section('page_label','Pegawai')
@section('page_title','Daftarkan Wajah')

@section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <div class="grid grid-cols-1 gap-5 xl:grid-cols-[1.1fr_.9fr]">
    <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
      <div class="text-sm font-semibold">Kamera</div>
      <div class="mt-1 text-xs text-white/70">
        Ambil 5 sample wajah. Pastikan cahaya cukup, tanpa masker/kacamata gelap.
      </div>

      <div class="mt-4 overflow-hidden rounded-2xl border border-white/15 bg-black/20">
        <video id="video" autoplay playsinline class="w-full aspect-video object-cover"></video>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <button id="btnStart"
          class="rounded-xl bg-emerald-600/80 px-4 py-2 text-sm font-semibold hover:bg-emerald-500/80">
          Nyalakan Kamera
        </button>
        <button id="btnEnroll" disabled
          class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/15 disabled:opacity-50">
          Mulai Daftar Wajah
        </button>
      </div>

      <div id="status" class="mt-3 text-sm text-white/75">Status: siap.</div>
    </div>

    <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
      <div class="text-sm font-semibold">Info</div>
      <div class="mt-3 text-sm text-white/75 space-y-2">
        <div>• Data yang disimpan bukan foto, tapi “template angka” wajah (descriptor).</div>
        <div>• Jika ganti gaya rambut ekstrim / pencahayaan buruk, daftar ulang.</div>
      </div>

      <div class="mt-4 rounded-2xl border border-white/15 bg-white/5 p-4 text-xs text-white/70">
        @if($profile?->enrolled_at)
          Terakhir daftar: <span class="font-semibold">{{ $profile->enrolled_at->format('d M Y H:i') }}</span>
        @else
          Belum pernah daftar wajah.
        @endif
      </div>
    </div>
  </div>

  {{-- face-api.js CDN --}}
  <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

  <script>
    const MODEL_URL = "/face/models";

    const video = document.getElementById('video');
    const btnStart = document.getElementById('btnStart');
    const btnEnroll = document.getElementById('btnEnroll');
    const statusEl = document.getElementById('status');

    let modelsLoaded = false;
    let stream = null;

    function setStatus(t){ statusEl.textContent = "Status: " + t; }

    async function loadModels() {
      if (modelsLoaded) return;
      setStatus("loading model...");
      await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
      await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
      await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
      modelsLoaded = true;
      setStatus("model siap.");
    }

    async function startCamera() {
      await loadModels();
      stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false });
      video.srcObject = stream;
      await video.play();
      btnEnroll.disabled = false;
      setStatus("kamera aktif.");
    }

    function sleep(ms){ return new Promise(r => setTimeout(r, ms)); }

    async function captureDescriptor() {
      const det = await faceapi
        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 }))
        .withFaceLandmarks()
        .withFaceDescriptor();

      if (!det) return null;
      return Array.from(det.descriptor);
    }

    async function enroll() {
      setStatus("mulai ambil sample (5x)...");
      const descriptors = [];
      let attempts = 0;

      while (descriptors.length < 5 && attempts < 30) {
        attempts++;
        const d = await captureDescriptor();
        if (d) {
          descriptors.push(d);
          setStatus(`ambil sample ${descriptors.length}/5 ...`);
          await sleep(600);
        } else {
          setStatus("wajah belum terdeteksi, posisikan wajah ke kamera...");
          await sleep(400);
        }
      }

      if (descriptors.length < 3) {
        setStatus("gagal: tidak cukup sample. coba lagi di tempat lebih terang.");
        return;
      }

      setStatus("upload template wajah...");
      const res = await fetch("{{ route('pegawai.face.enroll.store') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ descriptors })
      });

      if (!res.ok) {
        setStatus("gagal menyimpan. coba lagi.");
        return;
      }
      setStatus("berhasil! wajah sudah terdaftar.");
    }

    btnStart.addEventListener('click', startCamera);
    btnEnroll.addEventListener('click', enroll);
  </script>
@endsection