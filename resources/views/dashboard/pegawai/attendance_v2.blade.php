@extends('layouts.pegawai')
@section('title', 'Absensi')

@section('page_label', 'Pegawai')
@section('page_title', 'Absensi (QR + Lokasi + Selfie)')

@section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
    .delay-100 { animation-delay: 100ms; }
    
    .glass-panel {
      background: rgba(255, 255, 255, 0.04);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
      transition: border-color 0.3s ease;
    }
    .glass-panel:hover {
      border-color: rgba(255, 255, 255, 0.25);
    }
    
    .btn-glow {
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
    }
    .btn-glow:not(:disabled):hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }
    .btn-glow-gold:not(:disabled):hover {
      box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
    }
  </style>

  <div class="space-y-6">
    <!-- ROW 1: STATUS & SHIFT -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up">
      <!-- Status Hari Ini -->
      <div class="glass-panel rounded-[32px] p-7 flex flex-col justify-center relative overflow-hidden group">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-[50px] group-hover:bg-emerald-500/20 transition-all duration-700"></div>
        <div class="text-sm font-bold text-white/60 uppercase tracking-widest mb-4">Status Hari Ini</div>
        
        <div class="flex items-center gap-8">
          <div>
            <div class="text-xs text-white/50 mb-1">Check-in</div>
            <div class="text-3xl font-extrabold {{ $attendance?->check_in_at ? 'text-emerald-400' : 'text-white/90' }}">
              {{ $attendance?->check_in_at?->format('H:i') ?? '--:--' }}
            </div>
          </div>
          <div class="h-10 w-px bg-white/10"></div>
          <div>
            <div class="text-xs text-white/50 mb-1">Check-out</div>
            <div class="text-3xl font-extrabold {{ $attendance?->check_out_at ? 'text-orange-400' : 'text-white/90' }}">
              {{ $attendance?->check_out_at?->format('H:i') ?? '--:--' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Shift Hari Ini -->
      <div class="glass-panel rounded-[32px] p-7 relative overflow-hidden group">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-yellow-500/10 rounded-full blur-[50px] group-hover:bg-yellow-500/20 transition-all duration-700"></div>
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
          <div>
            <div class="text-sm font-bold text-white/60 uppercase tracking-widest">Shift Hari Ini</div>
            <div class="mt-1 text-xl font-bold text-yellow-400">{{ $ui['shift_name'] ?? '-' }}</div>
          </div>
          <div class="text-xs font-mono text-white/50 bg-black/30 px-3 py-1.5 rounded-xl border border-white/5">
            Skrg: <b id="uiNow" class="text-white">-</b>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="bg-black/20 rounded-2xl p-4 border border-white/5">
            <div class="text-[10px] text-white/40 uppercase tracking-wider mb-1">Window Check-in (<span id="uiInRange">-</span>)</div>
            <div id="uiInStatus" class="text-sm font-semibold text-white/85">-</div>
          </div>
          <div class="bg-black/20 rounded-2xl p-4 border border-white/5">
            <div class="text-[10px] text-white/40 uppercase tracking-wider mb-1">Window Check-out (<span id="uiOutRange">-</span>)</div>
            <div id="uiOutStatus" class="text-sm font-semibold text-white/85">-</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ROW 2: VERIFIKASI & SCAN -->
    <div class="grid grid-cols-1 xl:grid-cols-[1.2fr_.8fr] gap-6 animate-fade-in-up delay-100">
      
      <!-- 1) Verifikasi Device -->
      <div class="glass-panel rounded-[32px] p-7 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-2">
            <div class="text-lg font-bold text-white">1) Verifikasi Device & Lokasi</div>
            <div id="gateStatus" class="text-xs px-3 py-1 rounded-full bg-white/5 border border-white/10 text-white/60">Status: belum verifikasi</div>
          </div>
          <div class="text-sm text-white/50 mb-6">Sistem memvalidasi device dan kordinat lokasi secara otomatis.</div>
        </div>

        <div>
          <!-- Tombol Utama -->
          <button id="btnInitNormal" class="btn-glow w-full mb-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-4 text-sm font-bold tracking-wide shadow-lg border border-emerald-400/30 text-white">
            <span class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
              MULAI VERIFIKASI NORMAL
            </span>
          </button>

          <!-- Tombol Sekunder Grid -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-1">
            <div class="flex flex-col">
              <button id="btnInitException" class="h-full rounded-xl border border-white/10 bg-white/[0.02] hover:bg-white/[0.06] hover:border-white/20 transition-all p-3 text-xs font-semibold text-white/70">
                Device Lain (Darurat)
              </button>
            </div>
            
            <div class="flex flex-col relative group">
              <button id="btnLateRequest" class="h-full rounded-xl border border-white/10 bg-white/[0.02] hover:bg-white/[0.06] hover:border-white/20 transition-all p-3 text-xs font-semibold text-white/70">
                Ajukan Telat
              </button>
            </div>

            <div class="flex flex-col relative group">
              <button id="btnCheckoutCorrection" class="h-full rounded-xl border border-white/10 bg-white/[0.02] hover:bg-white/[0.06] hover:border-white/20 transition-all p-3 text-xs font-semibold text-white/70">
                Koreksi Checkout
              </button>
            </div>

            <div class="flex flex-col relative group">
              <button id="btnOvertime" class="h-full rounded-xl border border-white/10 bg-white/[0.02] hover:bg-white/[0.06] hover:border-white/20 transition-all p-3 text-xs font-semibold text-white/70">
                Ajukan Lembur
              </button>
            </div>
          </div>
          
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 px-1">
             <div></div>
             <div id="lateReqMsg" class="text-[10px] text-yellow-400/80 leading-tight"></div>
             <div id="ccMsg" class="text-[10px] text-yellow-400/80 leading-tight"></div>
             <div id="otMsg" class="text-[10px] text-yellow-400/80 leading-tight"></div>
          </div>
          
        </div>

        <div id="exceptionReasonWrap" class="mt-5 hidden bg-white/[0.02] p-4 rounded-2xl border border-white/5">
          <div class="text-xs font-semibold text-yellow-500 mb-2">Darurat: Verifikasi Device Baru</div>
          <textarea id="exceptionReason" rows="2" class="w-full rounded-xl border border-white/10 bg-black/40 px-4 py-3 text-sm text-white placeholder:text-white/30 outline-none focus:border-yellow-500/50 transition-colors" placeholder="Tulis alasan menggunakan device lain..."></textarea>
          <div class="mt-2 text-[10px] text-white/40">Pengajuan akan dikirim ke admin untuk disetujui.</div>
        </div>
      </div>

      <!-- 2) Scan QR -->
      <div class="glass-panel rounded-[32px] p-7 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-2">
            <div class="text-lg font-bold text-white">2) Scan QR + Selfie</div>
          </div>
          <div class="text-sm text-white/50 mb-6">Arahkan kamera ke QR Code yang tampil di Kiosk lalu lakukan selfie absensi.</div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 mt-auto">
          <button id="btnCheckIn" disabled class="btn-glow btn-glow-gold flex-1 rounded-2xl bg-gradient-to-r from-yellow-600 to-yellow-500 px-4 py-5 text-sm font-bold text-black shadow-lg shadow-yellow-500/20 disabled:opacity-30 disabled:shadow-none disabled:cursor-not-allowed transition-all">
            <span class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
              CHECK-IN
            </span>
          </button>
          
          <button id="btnCheckOut" disabled class="btn-glow btn-glow-gold flex-1 rounded-2xl bg-gradient-to-r from-orange-600 to-orange-500 px-4 py-5 text-sm font-bold text-white shadow-lg shadow-orange-500/20 disabled:opacity-30 disabled:shadow-none disabled:cursor-not-allowed transition-all">
            <span class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
              CHECK-OUT
            </span>
          </button>
        </div>
        
        <div id="scanStatus" class="mt-5 text-center text-xs px-3 py-2 rounded-xl bg-white/5 border border-white/5 text-white/50">Status: menunggu verifikasi</div>
      </div>

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
              <video id="selfieVideo" autoplay playsinline class="aspect-video w-full object-contain bg-black"></video>

              {{-- overlay face mesh --}}
              <canvas id="faceOverlay" class="absolute inset-0 h-full w-full"></canvas>

              {{-- hint --}}
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

  <div id="lateModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/70 p-4">
    <div class="w-full max-w-md rounded-[24px] border border-white/15 bg-[#121212]/95 p-5">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-sm font-semibold text-white">Pengajuan Telat</div>
          <div class="mt-1 text-xs text-white/60">
            Isi batas maksimal check-in & alasan. (Opsional) upload bukti.
          </div>
        </div>
        <button id="lateClose"
          class="rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/85 hover:bg-white/[0.08]">
          Tutup
        </button>
      </div>

      <div class="mt-4 space-y-3">
        <div>
          <label class="text-xs text-white/70">Maksimal check-in sampai</label>
          <select id="lateUntil"
            class="mt-2 w-full rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-sm text-white outline-none focus:border-yellow-500/35">
            <option value="">- pilih -</option>
          </select>
          <div class="mt-1 text-[11px] text-white/50">Opsi otomatis dibatasi sampai max telat (mis. 12:00).</div>
        </div>

        <div>
          <label class="text-xs text-white/70">Alasan</label>
          <textarea id="lateReason" rows="3"
            class="mt-2 w-full rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-sm text-white placeholder:text-white/40 outline-none focus:border-yellow-500/35"
            placeholder="Contoh: macet, ada urusan mendadak, dll."></textarea>
        </div>

        <div>
          <label class="text-xs text-white/70">Bukti foto (opsional)</label>
          <input id="lateEvidence" type="file" accept="image/*"
            class="mt-2 w-full rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-sm text-white outline-none" />
          <div class="mt-1 text-[11px] text-white/50">Max 4MB.</div>
        </div>

        <button id="lateSubmit"
          class="w-full rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
          Kirim Pengajuan
        </button>

        <div id="lateErr" class="text-xs text-red-200"></div>
        <div id="lateOk" class="text-xs text-emerald-200"></div>
      </div>
    </div>
  </div>

  <div id="ccModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/70 p-4">
    <div class="w-full max-w-md rounded-[24px] border border-white/15 bg-[#121212]/95 p-5">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-sm font-semibold text-white">Koreksi Checkout</div>
          <div class="mt-1 text-xs text-white/60">Ajukan jika kamu lupa checkout. Admin akan approve/reject.</div>
        </div>
        <button id="ccClose"
          class="rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/85 hover:bg-white/[0.08]">
          Tutup
        </button>
      </div>

      <div class="mt-4 space-y-3">
        <div>
          <label class="text-xs text-white/70">Alasan</label>
          <textarea id="ccReason" rows="3"
            class="mt-2 w-full rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-sm text-white placeholder:text-white/40 outline-none focus:border-yellow-500/35"
            placeholder="Contoh: lupa checkout karena buru-buru..."></textarea>
        </div>

        <button id="ccSubmit"
          class="w-full rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
          Kirim Pengajuan
        </button>

        <div id="ccErr" class="text-xs text-red-200"></div>
        <div id="ccOk" class="text-xs text-emerald-200"></div>
      </div>
    </div>
  </div>

  <div id="otModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/70 p-4">
    <div class="w-full max-w-md rounded-[24px] border border-white/15 bg-[#121212]/95 p-5">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-sm font-semibold text-white">Pengajuan Lembur</div>
          <div class="mt-1 text-xs text-white/60">Pilih kelipatan 60 menit. Wajib sudah check-in.</div>
        </div>
        <button id="otClose"
          class="rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-xs text-white/85 hover:bg-white/[0.08]">Tutup</button>
      </div>

      <div class="mt-4 space-y-3">
        <div>
          <label class="text-xs text-white/70">Durasi lembur</label>
          <select id="otMinutes"
            class="mt-2 w-full rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-sm text-white outline-none focus:border-yellow-500/35">
            <option value="">- pilih -</option>
            <option value="60">60 menit</option>
            <option value="120">120 menit</option>
            <option value="180">180 menit</option>
          </select>
          <div id="otPreview" class="mt-2 text-[11px] text-white/60"></div>
        </div>

        <div>
          <label class="text-xs text-white/70">Alasan (opsional)</label>
          <textarea id="otReason" rows="3"
            class="mt-2 w-full rounded-xl border border-white/15 bg-white/[0.04] px-3 py-2 text-sm text-white placeholder:text-white/40 outline-none focus:border-yellow-500/35"
            placeholder="Contoh: tutup kasir, bersih-bersih, stok opname..."></textarea>
        </div>

        <button id="otSubmit"
          class="w-full rounded-xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-400">
          Kirim Pengajuan
        </button>

        <div id="otErr" class="text-xs text-red-200"></div>
        <div id="otOk" class="text-xs text-emerald-200"></div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/html5-qrcode"></script>
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js"></script>
  <script>
    const btnOvertime = document.getElementById('btnOvertime');
    const otModal = document.getElementById('otModal');
    const otClose = document.getElementById('otClose');
    const otMinutes = document.getElementById('otMinutes');
    const otReason = document.getElementById('otReason');
    const otSubmit = document.getElementById('otSubmit');
    const otErr = document.getElementById('otErr');
    const otOk = document.getElementById('otOk');
    const otMsg = document.getElementById('otMsg');
    const otPreview = document.getElementById('otPreview');

    function openOt() { otErr.textContent = ''; otOk.textContent = ''; otReason.value = ''; otMinutes.value = ''; otPreview.textContent = ''; otModal.classList.remove('hidden'); otModal.classList.add('flex'); }
    function closeOt() { otModal.classList.add('hidden'); otModal.classList.remove('flex'); }

    function canOvertime() {
      // harus sudah check-in dan belum checkout
      return HAS_CHECKIN && !HAS_CHECKOUT;
    }

    function syncOvertimeButton() {
      if (!btnOvertime) return;
      const ok = canOvertime();
      btnOvertime.disabled = !ok;
      btnOvertime.classList.toggle('opacity-50', !ok);
      btnOvertime.classList.toggle('cursor-not-allowed', !ok);
      if (otMsg) otMsg.textContent = ok ? '' : 'Ajukan lembur hanya bisa setelah check-in dan sebelum checkout.';
    }

    btnOvertime?.addEventListener('click', () => {
      if (!canOvertime()) { syncOvertimeButton(); return; }
      openOt();
    });
    otClose?.addEventListener('click', closeOt);
    otModal?.addEventListener('click', (e) => { if (e.target === otModal) closeOt(); });

    otMinutes?.addEventListener('change', () => {
      if (!otMinutes.value) { otPreview.textContent = ''; return; }
      const mins = parseInt(otMinutes.value, 10);
      // preview: checkout mulai = UI.out.from_ms + mins (karena window checkout digeser)
      const startCheckout = new Date(UI.out.from_ms + mins * 60 * 1000);
      const hh = String(startCheckout.getHours()).padStart(2, '0');
      const mm = String(startCheckout.getMinutes()).padStart(2, '0');
      otPreview.textContent = `Jika disetujui, checkout baru dibuka mulai ${hh}:${mm}.`;
    });

    otSubmit?.addEventListener('click', async () => {
      otErr.textContent = ''; otOk.textContent = '';
      if (!otMinutes.value) { otErr.textContent = 'Pilih durasi lembur.'; return; }

      const fd = new FormData();
      fd.append('minutes', otMinutes.value);
      fd.append('reason', (otReason.value || '').trim());

      const res = await fetch("{{ route('pegawai.attendance.overtime_request') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": csrf },
        body: fd
      });

      const json = await res.json().catch(() => ({}));
      if (!res.ok) { otErr.textContent = json.message || 'Gagal mengirim.'; return; }

      otOk.textContent = json.message || 'Pengajuan terkirim.';
      if (otMsg) otMsg.textContent = otOk.textContent;
      setTimeout(() => closeOt(), 700);
    });

    // panggil di refreshShiftUi() biar ikut update state
    // tambahkan: syncOvertimeButton();
  </script>
  <script>const btnCheckoutCorrection = document.getElementById('btnCheckoutCorrection');
    const ccModal = document.getElementById('ccModal');
    const ccClose = document.getElementById('ccClose');
    const ccReason = document.getElementById('ccReason');
    const ccSubmit = document.getElementById('ccSubmit');
    const ccErr = document.getElementById('ccErr');
    const ccOk = document.getElementById('ccOk');
    const ccMsg = document.getElementById('ccMsg');

    function openCcModal() {
      ccErr.textContent = '';
      ccOk.textContent = '';
      ccReason.value = '';
      ccModal.classList.remove('hidden');
      ccModal.classList.add('flex');
    }
    function closeCcModal() {
      ccModal.classList.add('hidden');
      ccModal.classList.remove('flex');
    }

    btnCheckoutCorrection?.addEventListener('click', openCcModal);
    ccClose?.addEventListener('click', closeCcModal);
    ccModal?.addEventListener('click', (e) => { if (e.target === ccModal) closeCcModal(); });

    ccSubmit?.addEventListener('click', async () => {
      ccErr.textContent = '';
      ccOk.textContent = '';

      const reason = (ccReason.value || '').trim();
      if (!reason) {
        ccErr.textContent = 'Alasan wajib diisi.';
        return;
      }

      const res = await fetch("{{ route('pegawai.attendance.checkout_correction') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
        body: JSON.stringify({ reason })
      });

      const json = await res.json().catch(() => ({}));
      if (!res.ok) {
        ccErr.textContent = json.message || 'Gagal mengirim pengajuan.';
        return;
      }

      ccOk.textContent = json.message || 'Pengajuan terkirim.';
      if (ccMsg) ccMsg.textContent = ccOk.textContent;

      setTimeout(() => closeCcModal(), 700);
    });
  </script>
  <script>
    const lateModal = document.getElementById('lateModal');
    const btnLateRequest = document.getElementById('btnLateRequest');
    const lateClose = document.getElementById('lateClose');
    const lateUntil = document.getElementById('lateUntil');
    const lateReason = document.getElementById('lateReason');
    const lateEvidence = document.getElementById('lateEvidence');
    const lateSubmit = document.getElementById('lateSubmit');
    const lateErr = document.getElementById('lateErr');
    const lateOk = document.getElementById('lateOk');
    const lateReqMsg = document.getElementById('lateReqMsg');

    function openLateModal() {
      lateErr.textContent = '';
      lateOk.textContent = '';
      lateReason.value = '';
      if (lateEvidence) lateEvidence.value = '';
      buildLateOptions(); // isi pilihan jam
      lateModal.classList.remove('hidden');
      lateModal.classList.add('flex');
    }

    function closeLateModal() {
      lateModal.classList.add('hidden');
      lateModal.classList.remove('flex');
    }

    // buat opsi jam: tiap 30 menit, dari start shift sampai max +120
    function buildLateOptions() {
      // ambil shift info dari UI yang kamu kirim ke blade (sudah ada UI.in.start_ms)
      // start shift = UI.in.start_ms
      const start = new Date(UI.in.start_ms);
      const max = new Date(UI.in.start_ms + 120 * 60 * 1000); // +120 menit

      // clear
      lateUntil.innerHTML = `<option value="">- pilih -</option>`;

      // langkah 30 menit
      const stepMs = 30 * 60 * 1000;

      for (let t = start.getTime(); t <= max.getTime(); t += stepMs) {
        const d = new Date(t);
        const hh = String(d.getHours()).padStart(2, '0');
        const mm = String(d.getMinutes()).padStart(2, '0');
        const val = `${hh}:${mm}`;
        const opt = document.createElement('option');
        opt.value = val;
        opt.textContent = val;
        lateUntil.appendChild(opt);
      }
    }

    function canRequestLate() {
      // rule baru: hanya boleh sebelum start shift
      const now = serverNowMs();
      const start = UI.in.start_ms;

      // optional: batasi hanya dalam window check-in awal
      const from = UI.in.from_ms; // ini = start - 120 menit (sesuai config shift)
      return now >= from && now < start;
    }

    function syncLateButton() {
      if (!btnLateRequest) return;

      const ok = canRequestLate();

      btnLateRequest.disabled = !ok;
      btnLateRequest.classList.toggle('opacity-50', !ok);
      btnLateRequest.classList.toggle('cursor-not-allowed', !ok);

      if (!lateReqMsg) return;

      if (!ok) {
        const now = serverNowMs();
        const start = UI.in.start_ms;
        if (now >= start) lateReqMsg.textContent = 'Pengajuan telat ditutup karena sudah melewati jam mulai shift.';
        else lateReqMsg.textContent = 'Pengajuan telat belum dibuka.';
      } else {
        lateReqMsg.textContent = '';
      }
    }

    btnLateRequest?.addEventListener('click', openLateModal);
    lateClose?.addEventListener('click', closeLateModal);
    lateModal?.addEventListener('click', (e) => { if (e.target === lateModal) closeLateModal(); });

    lateSubmit?.addEventListener('click', async () => {
      lateErr.textContent = '';
      lateOk.textContent = '';

      if (!lateUntil.value) {
        lateErr.textContent = 'Pilih maksimal check-in terlebih dahulu.';
        return;
      }

      const fd = new FormData();
      fd.append('requested_until_time', lateUntil.value);
      fd.append('reason', (lateReason.value || '').trim());
      if (lateEvidence && lateEvidence.files && lateEvidence.files[0]) {
        fd.append('evidence', lateEvidence.files[0]);
      }

      const res = await fetch("{{ route('pegawai.attendance.late_request') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": csrf },
        body: fd
      });

      const json = await res.json().catch(() => ({}));
      if (!res.ok) {
        lateErr.textContent = json.message || 'Gagal mengirim pengajuan.';
        return;
      }

      lateOk.textContent = json.message || 'Pengajuan terkirim.';
      if (lateReqMsg) lateReqMsg.textContent = lateOk.textContent;

      setTimeout(() => {
        closeLateModal();
      }, 700);
    });
  </script>

  <script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const HAS_CHECKIN = {{ $attendance?->check_in_at ? 'true' : 'false' }};
    const HAS_CHECKOUT = {{ $attendance?->check_out_at ? 'true' : 'false' }};

    let deviceHash = null;
    let geo = null; // {lat,lng}

    let reader = null;
    let selfieStream = null;

    // Face detection state
    let faceMesh = null;
    let faceDetectRunning = false;
    let faceOk = false;
    let faceOkStreak = 0;
    const FACE_OK_NEED = 6;

    let gateOk = false;
    let busy = false;
    let currentMode = null;      // 'in'/'out'
    let scannedToken = null;
    let flow = null; // 'normal' | 'exception'
    let exceptionOwner = null; // info pemilik device

    const gateStatus = document.getElementById('gateStatus');
    const scanStatus = document.getElementById('scanStatus');
    const btnInitNormal = document.getElementById('btnInitNormal');
    const btnInitException = document.getElementById('btnInitException');
    const exceptionReasonWrap = document.getElementById('exceptionReasonWrap');
    const exceptionReason = document.getElementById('exceptionReason');
    const btnCheckIn = document.getElementById('btnCheckIn');
    const btnCheckOut = document.getElementById('btnCheckOut');

    // modal
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

    const UI = @json($ui);

    const serverOffsetMs = (UI?.server_now_ms || Date.now()) - Date.now();
    function serverNowMs() {
      return Date.now() + serverOffsetMs;
    }

    function fmtHHMM(ms) {
      const d = new Date(ms);
      const hh = String(d.getHours()).padStart(2, '0');
      const mm = String(d.getMinutes()).padStart(2, '0');
      return `${hh}:${mm}`;
    }

    function fmtRange(fromMs, toMs) {
      return `${fmtHHMM(fromMs)} - ${fmtHHMM(toMs)}`;
    }

    function formatCountdown(msLeft) {
      if (msLeft <= 0) return '0s';
      const s = Math.floor(msLeft / 1000);
      const h = Math.floor(s / 3600);
      const m = Math.floor((s % 3600) / 60);
      const sec = s % 60;
      if (h > 0) return `${h}j ${m}m`;
      if (m > 0) return `${m}m ${sec}s`;
      return `${sec}s`;
    }

    function inWindow(mode) {
      const now = serverNowMs();
      const w = mode === 'in' ? UI.in : UI.out;
      return now >= w.from_ms && now <= w.to_ms;
    }

    function windowStatus(mode) {
      const now = serverNowMs();
      const w = mode === 'in' ? UI.in : UI.out;

      if (now < w.from_ms) {
        return { state: 'before', text: `Dibuka dalam ${formatCountdown(w.from_ms - now)} (mulai ${fmtHHMM(w.from_ms)})` };
      }
      if (now > w.to_ms) {
        return { state: 'after', text: `Ditutup (maks ${fmtHHMM(w.to_ms)})` };
      }
      return { state: 'open', text: `Sedang dibuka • Ditutup dalam ${formatCountdown(w.to_ms - now)}` };
    }

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

      // sudah selesai semua
      if (HAS_CHECKIN && HAS_CHECKOUT) {
        setScan("kamu sudah check-in & check-out hari ini.");
        return;
      }

      // CHECK-OUT: hanya kalau sudah check-in, belum checkout, DAN window out sedang open
      if (HAS_CHECKIN && !HAS_CHECKOUT) {
        if (inWindow('out')) {
          btnCheckOut.disabled = false;
          setScan("silakan tekan Check-out untuk scan QR checkout.");
        } else {
          btnCheckOut.disabled = true;
          setScan("check-out belum dibuka / sudah ditutup (lihat info shift di atas).");
        }
        return;
      }

      // CHECK-IN: hanya kalau belum check-in DAN window in sedang open
      if (!HAS_CHECKIN) {
        if (inWindow('in')) {
          btnCheckIn.disabled = false;
          setScan("silakan tekan Check-in untuk scan QR checkin.");
        } else {
          btnCheckIn.disabled = true;
          setScan("check-in belum dibuka / sudah ditutup (lihat info shift di atas).");
        }
        return;
      }
    }

    function generateDeviceId() {
      const chars = '0123456789abcdef';
      let id = '';
      for (let i = 0; i < 64; i++) {
        id += chars[Math.floor(Math.random() * chars.length)];
      }
      return id;
    }

    function getOrGenerateDeviceId() {
      let id = localStorage.getItem('attendance_device_id');
      if (!id || id.length !== 64) {
        id = generateDeviceId();
        localStorage.setItem('attendance_device_id', id);
      }
      return id;
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
      } catch (e) { }

      const parts = [];
      parts.push(platform + (androidVer ? ` ${androidVer}` : ''));
      if (model) parts.push(model);
      if (browser) parts.push(browser);

      return parts.join(' • ').replace(/\s+/g, ' ').trim() || 'Web';
    }

    async function detectDeviceNameAsync() {
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
      } catch (e) { }
      return base || 'Web';
    }

    async function initGate() {
      if (busy) return;
      busy = true;

      try {
        setGate("mengecek ID device...");
        deviceHash = getOrGenerateDeviceId();

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
        flow = 'normal';
        exceptionOwner = null;
        exceptionReasonWrap.classList.add('hidden');

      } catch (e) {
        gateOk = false;
        setGate("gagal ambil lokasi. aktifkan GPS & izin lokasi.");
        setScan("menunggu verifikasi.");
        setButtonsAfterGate();
      } finally {
        busy = false;
      }
    }

    async function initExceptionGate() {
      if (busy) return;
      busy = true;

      try {
        setGate("mengecek ID device...");
        deviceHash = getOrGenerateDeviceId();

        setGate("cek lokasi...");
        geo = await getGeo();

        setGate("cek device milik siapa...");
        const deviceName = await detectDeviceNameAsync();

        const res = await fetch("{{ route('pegawai.attendance.device.lookup') }}", {
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
          flow = null;
          exceptionOwner = null;
          exceptionReasonWrap.classList.add('hidden');

          setGate(json.message || "device tidak valid untuk darurat.");
          setScan("mode darurat tidak bisa dipakai.");
          setButtonsAfterGate();
          return;
        }

        // sukses
        flow = 'exception';
        exceptionOwner = json.owner || null;
        gateOk = true;

        exceptionReasonWrap.classList.remove('hidden');

        const ownerName = exceptionOwner?.name || 'Pegawai lain';
        setGate(`MODE DARURAT ✅ device milik: ${ownerName}`);
        setScan("silakan tekan Check-in / Check-out untuk scan QR (pengajuan akan menunggu admin).");
        setButtonsAfterGate();

      } catch (e) {
        gateOk = false;
        flow = null;
        exceptionOwner = null;
        exceptionReasonWrap.classList.add('hidden');

        setGate("gagal ambil lokasi. aktifkan GPS & izin lokasi.");
        setScan("menunggu verifikasi.");
        setButtonsAfterGate();
      } finally {
        busy = false;
      }
    }

    // ---------- QR FLOW ----------
    async function startQrScanner(mode) {
      if (busy) return;

      // gate harus OK dulu
      if (!gateOk) {
        setScan("verifikasi device & lokasi dulu.");
        return;
      }

      // cegah kalau tombol harusnya disabled (double safety)
      if (mode === 'in' && btnCheckIn.disabled) {
        setScan("Check-in tidak tersedia saat ini (di luar jam shift).");
        return;
      }
      if (mode === 'out' && btnCheckOut.disabled) {
        setScan("Check-out tidak tersedia saat ini (di luar jam shift).");
        return;
      }

      // blocker berdasarkan window shift (hard check)
      if (mode === 'in' && !inWindow('in')) {
        const s = windowStatus('in');
        setScan("Check-in belum bisa: " + s.text);
        return;
      }
      if (mode === 'out' && !inWindow('out')) {
        const s = windowStatus('out');
        setScan("Check-out belum bisa: " + s.text);
        return;
      }

      // aturan logical attendance
      if (mode === 'out' && !HAS_CHECKIN) {
        setScan("Kamu belum check-in, jadi tidak bisa check-out.");
        return;
      }
      if (mode === 'in' && HAS_CHECKIN) {
        setScan("Kamu sudah check-in hari ini.");
        return;
      }
      if (mode === 'out' && HAS_CHECKOUT) {
        setScan("Kamu sudah check-out hari ini.");
        return;
      }

      // === lanjut flow lama ===
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

    // ---------- SELFIE ----------
    async function startSelfieCamera() {
      if (selfieStream) return;

      selfieStream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: "user", width: { ideal: 640 }, height: { ideal: 480 } },
        audio: false
      });

      video.srcObject = selfieStream;
      await video.play();
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

    async function countdownWithFaceGuard(seconds) {
      // countdown yang akan batal kalau wajah hilang
      for (let i = seconds; i >= 1; i--) {
        // cek wajah beberapa kali per detik biar responsif
        const start = Date.now();
        while (Date.now() - start < 1000) {
          if (!faceOk) {
            setCamStatus("Wajah hilang. Countdown dibatalkan. Arahkan wajah ke kamera…");
            return false;
          }
          await new Promise(r => setTimeout(r, 120));
        }

        setCamStatus(`Selfie otomatis dalam ${i}…`);
      }
      return true;
    }

    async function startFaceDetection() {
      if (faceDetectRunning) return;
      faceDetectRunning = true;
      faceOk = false;
      faceOkStreak = 0;

      // guard: mediapipe belum loaded
      if (typeof FaceMesh === "undefined" || typeof drawConnectors === "undefined") {
        setFaceHint("Deteksi wajah belum siap. Reload halaman dulu.", false);
        faceDetectRunning = false;
        return;
      }

      resizeOverlayToVideo();
      const ctx = faceOverlay.getContext('2d');

      if (!faceMesh) {
        // ✅ constructor dari CDN: new FaceMesh(...)
        faceMesh = new FaceMesh({
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



          // titik landmark
          // titik-titik kecil (opsional, kalau mau lebih clean bisa hapus)
          const landmarks = faces[0];

          // hitung bounding box wajah (normalisasi 0..1)
          let minX = 1, minY = 1, maxX = 0, maxY = 0;
          for (const p of landmarks) {
            if (p.x < minX) minX = p.x;
            if (p.y < minY) minY = p.y;
            if (p.x > maxX) maxX = p.x;
            if (p.y > maxY) maxY = p.y;
          }

          // ubah ke pixel canvas
          const cw = faceOverlay.width;
          const ch = faceOverlay.height;

          let x = minX * cw;
          let y = minY * ch;
          let bw = (maxX - minX) * cw;
          let bh = (maxY - minY) * ch;

          // kasih padding biar frame sedikit lebih besar dari wajah
          const pad = Math.max(12, Math.round(Math.min(bw, bh) * 0.12));
          x = Math.max(0, x - pad);
          y = Math.max(0, y - pad);
          bw = Math.min(cw - x, bw + pad * 2);
          bh = Math.min(ch - y, bh + pad * 2);

          // gambar frame oval/rounded (lebih “ngikut wajah”)
          ctx.save();
          ctx.lineWidth = 4;
          ctx.strokeStyle = 'rgba(34,197,94,0.9)'; // hijau
          ctx.shadowColor = 'rgba(34,197,94,0.45)';
          ctx.shadowBlur = 10;

          // ellipse
          ctx.beginPath();
          ctx.ellipse(x + bw / 2, y + bh / 2, bw / 2, bh / 2, 0, 0, Math.PI * 2);
          ctx.stroke();
          ctx.restore();
        });
      }

      async function loop() {
        if (!faceDetectRunning) return;
        try {
          await faceMesh.send({ image: video });
        } catch (e) { }
        requestAnimationFrame(loop);
      }

      setFaceHint("Menyiapkan deteksi wajah…", false);
      requestAnimationFrame(loop);
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

        // tunggu wajah stabil
        // tunggu wajah stabil (awal)
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

        // ✅ countdown yang akan batal kalau wajah hilang
        let okCountdown = await countdownWithFaceGuard(3);
        if (!okCountdown) {
          // reset lalu tunggu wajah balik lagi
          faceOk = false;
          faceOkStreak = 0;

          let waited2 = 0;
          while (!faceOk) {
            await new Promise(r => setTimeout(r, 200));
            waited2 += 200;

            if (waited2 >= 15000) {
              setCamStatus("Wajah tidak terdeteksi. Ulangi dari awal.");
              stopSelfie();
              busy = false;
              return;
            }
          }

          // ulang countdown dari awal
          okCountdown = await countdownWithFaceGuard(3);
          if (!okCountdown) {
            setCamStatus("Wajah hilang lagi. Ulangi proses.");
            stopSelfie();
            busy = false;
            return;
          }
        }

        // safety check terakhir sebelum capture
        if (!faceOk) {
          setCamStatus("Wajah tidak terdeteksi. Foto dibatalkan.");
          stopSelfie();
          busy = false;
          return;
        }

        setCamStatus("Mengambil foto…");
        const selfieBlob = await captureSelfieBlob();
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

        let url = "{{ route('pegawai.attendance.submit') }}";

        if (flow === 'exception') {
          url = "{{ route('pegawai.attendance.submit-exception') }}";
          fd.append("reason", (exceptionReason?.value || "").trim());
        }

        const res = await fetch(url, {
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

        // mode darurat: tampilkan info sukses & tidak auto reload cepat
        if (flow === 'exception') {
          setScan(json.message || "Pengajuan terkirim.");
          setCamStatus(json.message || "Pengajuan terkirim.");
          setTimeout(() => window.location.reload(), 1200);
          return true;
        }

        return true;
      } catch (e) {
        setScan("error submit: " + (e?.message || "unknown"));
        setCamStatus("error submit: " + (e?.message || "unknown"));
        return false;
      }
    }
    // EVENTS
    btnInitNormal.addEventListener("click", () => { flow = null; initGate(); });
    btnInitException.addEventListener("click", () => { flow = null; initExceptionGate(); });
    btnCheckIn.addEventListener("click", () => { if (!btnCheckIn.disabled) startQrScanner("in"); });
    btnCheckOut.addEventListener("click", () => { if (!btnCheckOut.disabled) startQrScanner("out"); });

    setButtonsAfterGate();
    const uiInRange = document.getElementById('uiInRange');
    const uiOutRange = document.getElementById('uiOutRange');
    const uiNow = document.getElementById('uiNow');
    const uiInStatus = document.getElementById('uiInStatus');
    const uiOutStatus = document.getElementById('uiOutStatus');
    btnLateRequest?.addEventListener('click', () => {
      if (!canRequestLate()) {
        if (lateReqMsg) lateReqMsg.textContent = 'Pengajuan telat tidak tersedia saat ini.';
        return;
      }
      openLateModal();
    });

    function refreshShiftUi() {
      if (uiInRange) uiInRange.textContent = fmtRange(UI.in.from_ms, UI.in.to_ms);
      if (uiOutRange) uiOutRange.textContent = fmtRange(UI.out.from_ms, UI.out.to_ms);
      if (uiNow) uiNow.textContent = fmtHHMM(serverNowMs());

      const sIn = windowStatus('in');
      const sOut = windowStatus('out');

      if (uiInStatus) uiInStatus.textContent = sIn.text;
      if (uiOutStatus) uiOutStatus.textContent = sOut.text;

      // penting: tombol bisa berubah saat jam berubah
      setButtonsAfterGate();
      syncLateButton();
      syncOvertimeButton();
    }

    refreshShiftUi();
    setInterval(refreshShiftUi, 1000);
  </script>
@endsection