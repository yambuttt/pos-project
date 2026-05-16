@extends('layouts.admin')
@section('title', 'Absensi QR Control Center')

@section('body')
  <!-- HEADER -->
  <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
    <div class="flex items-center gap-4">
      <button id="openMobileSidebar" type="button"
        class="inline-flex lg:hidden items-center justify-center w-10 h-10 rounded-xl border border-white/10 bg-white/5 text-white/70 hover:bg-white/10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
      <div>
        <h1 class="text-3xl font-bold text-gold-gradient">Absensi QR</h1>
        <p class="text-sm text-white/40 font-medium italic">Pusat kendali <span class="text-gold-primary font-bold not-italic">token absensi dinamis & kiosk.</span></p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
      <a href="{{ route('attendance.kiosk.index', config('attendance.kiosk_key', 'pos123')) }}" target="_blank"
        class="flex items-center gap-2 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 px-5 py-3 text-[10px] font-black text-emerald-400 uppercase tracking-widest hover:bg-emerald-500/20 transition-all active:scale-95 shadow-lg shadow-emerald-500/5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        Buka Kiosk Mode
      </a>
      <a href="{{ route('admin.attendance.devices') }}"
        class="flex items-center gap-2 rounded-2xl bg-white/5 border border-white/10 px-5 py-3 text-[10px] font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all active:scale-95">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        Kelola Device
      </a>
    </div>
  </div>

  @if(session('ok'))
    <div class="mb-8 animate-fade-in rounded-2xl border border-green-500/20 bg-green-500/10 px-6 py-4 flex items-center gap-3 backdrop-blur-xl">
      <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </div>
      <p class="text-sm font-bold text-green-100">{{ session('ok') }}</p>
    </div>
  @endif

  <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
    <!-- QR IN -->
    <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] relative overflow-hidden group">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold-primary/5 blur-3xl rounded-full"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-10">
           <div class="relative shrink-0">
              <div id="qr-in" class="rounded-[2rem] bg-white p-5 shadow-2xl shadow-gold-primary/10 border-4 border-gold-primary/20"></div>
              <div class="absolute -inset-2 border-2 border-dashed border-gold-primary/20 rounded-[2.5rem] animate-spin-slow"></div>
           </div>

           <div class="flex-1 space-y-6 text-center sm:text-left">
              <div>
                 <h3 class="text-xl font-black text-white uppercase tracking-wider">QR Check-In</h3>
                 <p class="text-xs text-white/40 italic mt-1 leading-relaxed">Berlaku untuk satu kali scan. <br>Token diperbarui setiap 10 detik.</p>
              </div>

              <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 space-y-3">
                 <div class="flex items-center justify-between">
                    <span class="text-[9px] font-black text-white/20 uppercase tracking-widest">Active Token</span>
                    <span id="token-in" class="text-[10px] font-mono text-gold-primary truncate max-w-[120px]">-</span>
                 </div>
                 <div class="space-y-1">
                    <div class="flex items-center justify-between">
                       <span class="text-[9px] font-black text-white/20 uppercase tracking-widest">Next Refresh</span>
                       <span id="exp-in" class="text-xs font-black text-white italic">-</span>
                    </div>
                    <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden">
                       <div id="progress-in" class="h-full bg-gold-primary transition-all duration-1000" style="width: 100%"></div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
    </div>

    <!-- QR OUT -->
    <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] relative overflow-hidden group">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/5 blur-3xl rounded-full"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-10">
           <div class="relative shrink-0">
              <div id="qr-out" class="rounded-[2rem] bg-white p-5 shadow-2xl shadow-blue-500/10 border-4 border-blue-500/20"></div>
              <div class="absolute -inset-2 border-2 border-dashed border-blue-500/20 rounded-[2.5rem] animate-spin-slow"></div>
           </div>

           <div class="flex-1 space-y-6 text-center sm:text-left">
              <div>
                 <h3 class="text-xl font-black text-white uppercase tracking-wider">QR Check-Out</h3>
                 <p class="text-xs text-white/40 italic mt-1 leading-relaxed">Pastikan pegawai melakukan scan <br>saat meninggalkan area kerja.</p>
              </div>

              <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 space-y-3">
                 <div class="flex items-center justify-between">
                    <span class="text-[9px] font-black text-white/20 uppercase tracking-widest">Active Token</span>
                    <span id="token-out" class="text-[10px] font-mono text-blue-400 truncate max-w-[120px]">-</span>
                 </div>
                 <div class="space-y-1">
                    <div class="flex items-center justify-between">
                       <span class="text-[9px] font-black text-white/20 uppercase tracking-widest">Next Refresh</span>
                       <span id="exp-out" class="text-xs font-black text-white italic">-</span>
                    </div>
                    <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden">
                       <div id="progress-out" class="h-full bg-blue-500 transition-all duration-1000" style="width: 100%"></div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
    </div>
  </div>

  <!-- INSTRUCTIONS -->
  <div class="mt-10 glass-panel p-10 rounded-[3rem] border-white/5">
     <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <div class="flex flex-col items-center text-center gap-4">
           <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-gold-primary text-xl font-black italic">01</div>
           <p class="text-xs text-white/40 font-medium italic">Gunakan perangkat tablet untuk <span class="text-white font-bold not-italic">Kiosk Mode</span> yang diletakkan di pintu masuk.</p>
        </div>
        <div class="flex flex-col items-center text-center gap-4">
           <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-gold-primary text-xl font-black italic">02</div>
           <p class="text-xs text-white/40 font-medium italic">QR Code ini <span class="text-white font-bold not-italic">Single-Use</span>, satu token hanya berlaku untuk satu orang pegawai.</p>
        </div>
        <div class="flex flex-col items-center text-center gap-4">
           <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-gold-primary text-xl font-black italic">03</div>
           <p class="text-xs text-white/40 font-medium italic">Pastikan pencahayaan layar optimal agar scanner pada perangkat pegawai <span class="text-white font-bold not-italic">mudah membaca QR.</span></p>
        </div>
     </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
  <script>
    const qrInEl = document.getElementById("qr-in");
    const qrOutEl = document.getElementById("qr-out");

    async function fetchToken(mode) {
        const url = `{{ route('admin.attendance.qr.token') }}?mode=${mode}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        return await res.json();
    }

    function renderQr(el, token) {
        el.innerHTML = '';
        new QRCode(el, { text: token, width: 160, height: 160, correctLevel: QRCode.CorrectLevel.H });
    }

    const MAX_TTL = 60; // default 60s dari config

    async function refreshQr(mode) {
        try {
            const data = await fetchToken(mode);
            const el = mode === 'in' ? qrInEl : qrOutEl;
            renderQr(el, data.token);

            const infoToken = document.getElementById(`token-${mode}`);
            const infoExp = document.getElementById(`exp-${mode}`);
            const progress = document.getElementById(`progress-${mode}`);

            if (infoToken) infoToken.textContent = data.token.slice(0, 18) + '...';
            if (infoExp) infoExp.textContent = `${data.expires_in}s`;
            if (progress) {
                const percent = (data.expires_in / MAX_TTL) * 100;
                progress.style.width = `${percent}%`;
            }
        } catch (e) {
            console.error(e);
        }
    }

    // refresh tiap detik untuk update countdown & progress bar
    setInterval(() => {
        // Kita bisa refresh data setiap 1 detik atau 5 detik. 
        // Di controller sudah ada cache token, jadi fetch tiap detik aman tapi overkill.
        // Mari fetch tiap 5 detik, tapi update countdown client-side?
        // Simpelnya: fetch tiap 1-2 detik saja karena cache di DB sudah dihandle.
        refreshQr('in');
        refreshQr('out');
    }, 2000);

    // initial render
    refreshQr('in');
    refreshQr('out');
  </script>

  <style>
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin-slow {
        animation: spin-slow 20s linear infinite;
    }
  </style>
@endsection