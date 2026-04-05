@extends('layouts.admin')
@section('title', 'Buat User')

@section('body')
    <div class="mx-auto w-full max-w-6xl">
        <!-- HEADER -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <button id="openMobileSidebar" type="button"
                    class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
                    ☰
                </button>

                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs text-white/70">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                        Admin • User Management
                    </div>

                    <h1 class="mt-2 text-xl font-semibold">Buat Akun User</h1>
                    <p class="mt-1 text-sm text-white/70">
                        Buat akun untuk <span class="font-semibold text-white/85">Kasir / Kitchen / Pegawai</span>.
                        Tercatat dibuat oleh admin yang sedang login.
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.cashiers.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
                ← Kembali
            </a>
        </div>

        <!-- CONTENT -->
        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.2fr_0.8fr]">

            <!-- FORM -->
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold">Data User</div>
                        <div class="mt-1 text-xs text-white/65">Isi data di bawah, lalu klik “Buat Akun User”.</div>
                    </div>

                    <div class="hidden sm:flex items-center gap-2 text-xs text-white/60">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-white/15 bg-white/5">1</span>
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-white/15 bg-white/5">2</span>
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-white/15 bg-white/5">3</span>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-white/15 bg-white/5 p-4">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm font-semibold">Kredensial</div>
                        <div class="text-xs text-white/60">Email harus unik • Password terenkripsi</div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <label class="text-sm text-white/80">Nama</label>
                            <div class="mt-2 flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-3 focus-within:border-white/40">
                                <span class="text-white/50">👤</span>
                                <input name="name" value="{{ old('name') }}"
                                    class="w-full bg-transparent text-sm outline-none placeholder:text-white/40"
                                    placeholder="Nama user" />
                            </div>
                            @error('name') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-1">
                            <label class="text-sm text-white/80">Email</label>
                            <div class="mt-2 flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-3 focus-within:border-white/40">
                                <span class="text-white/50">✉️</span>
                                <input name="email" value="{{ old('email') }}" type="email"
                                    class="w-full bg-transparent text-sm outline-none placeholder:text-white/40"
                                    placeholder="user@example.com" />
                            </div>
                            @error('email') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="text-sm text-white/80">Role</label>
                            <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-2xl border border-white/15 bg-white/5 p-4 hover:bg-white/10">
                                    <input type="radio" name="role" value="kasir" class="peer sr-only"
                                        {{ old('role', 'kasir') === 'kasir' ? 'checked' : '' }}>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 bg-white/5 text-lg">🧾</div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold">Kasir</div>
                                        <div class="text-xs text-white/65">Transaksi & pembayaran</div>
                                    </div>
                                    <div class="absolute inset-0 rounded-2xl ring-0 ring-blue-400/40 peer-checked:ring-2"></div>
                                </label>

                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-2xl border border-white/15 bg-white/5 p-4 hover:bg-white/10">
                                    <input type="radio" name="role" value="kitchen" class="peer sr-only"
                                        {{ old('role') === 'kitchen' ? 'checked' : '' }}>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 bg-white/5 text-lg">🍳</div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold">Kitchen</div>
                                        <div class="text-xs text-white/65">Proses pesanan</div>
                                    </div>
                                    <div class="absolute inset-0 rounded-2xl ring-0 ring-blue-400/40 peer-checked:ring-2"></div>
                                </label>

                                <label class="group relative flex cursor-pointer items-center gap-3 rounded-2xl border border-white/15 bg-white/5 p-4 hover:bg-white/10">
                                    <input type="radio" name="role" value="pegawai" class="peer sr-only"
                                        {{ old('role') === 'pegawai' ? 'checked' : '' }}>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 bg-white/5 text-lg">🧑‍💼</div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold">Pegawai</div>
                                        <div class="text-xs text-white/65">Absensi & aktivitas</div>
                                    </div>
                                    <div class="absolute inset-0 rounded-2xl ring-0 ring-blue-400/40 peer-checked:ring-2"></div>
                                </label>
                            </div>
                            @error('role') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.cashiers.store') }}" class="mt-5 space-y-4">
                    @csrf

                    {{-- Hidden inputs for name/email/role from above styled inputs --}}
                    {{-- NOTE: Karena input name/email di atas berada di luar form, kita duplikasi field di sini (tetap 1 sumber data dengan JS). --}}
                    <input type="hidden" name="name" id="hidden_name" value="{{ old('name') }}">
                    <input type="hidden" name="email" id="hidden_email" value="{{ old('email') }}">
                    <input type="hidden" name="role" id="hidden_role" value="{{ old('role', 'kasir') }}">

                    <div class="rounded-2xl border border-white/15 bg-white/5 p-4">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm font-semibold">Password</div>
                            <div class="text-xs text-white/60">Minimal 6 karakter • Gunakan kombinasi huruf & angka</div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-sm text-white/80">Password</label>
                                <div
                                    class="mt-2 flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-3 focus-within:border-white/40">
                                    <input id="password" name="password" type="password"
                                        class="w-full bg-transparent text-sm outline-none placeholder:text-white/40"
                                        placeholder="Minimal 6 karakter" />

                                    <button type="button" id="togglePassword"
                                        class="rounded-lg border border-white/20 bg-white/10 px-2 py-1 hover:bg-white/15"
                                        title="Lihat/Sembunyikan password">
                                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/80" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5 text-white/80"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 10.7a2.9 2.9 0 003.8 3.8" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.88 4.24A10.5 10.5 0 0112 4.5c6 0 9.75 7.5 9.75 7.5a18.7 18.7 0 01-3.1 4.3" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.4 6.4C3.6 8.4 2.25 12 2.25 12S6 19.5 12 19.5c1.08 0 2.1-.2 3.04-.54" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm text-white/80">Ulangi Password</label>
                                <div
                                    class="mt-2 flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-3 focus-within:border-white/40">
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="w-full bg-transparent text-sm outline-none placeholder:text-white/40"
                                        placeholder="Ketik ulang password" />

                                    <button type="button" id="togglePasswordConfirm"
                                        class="rounded-lg border border-white/20 bg-white/10 px-2 py-1 hover:bg-white/15"
                                        title="Lihat/Sembunyikan password">
                                        <svg id="eyeOpen2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/80" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <svg id="eyeClosed2" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5 text-white/80"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 10.7a2.9 2.9 0 003.8 3.8" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.88 4.24A10.5 10.5 0 0112 4.5c6 0 9.75 7.5 9.75 7.5a18.7 18.7 0 01-3.1 4.3" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.4 6.4C3.6 8.4 2.25 12 2.25 12S6 19.5 12 19.5c1.08 0 2.1-.2 3.04-.54" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password_confirmation') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <button
                        class="w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
                        Buat Akun User
                    </button>

                    <div class="text-center text-xs text-white/55">
                        Dengan membuat user, sistem akan menyimpan <span class="font-semibold text-white/75">created_by</span> admin yang login.
                    </div>
                </form>
            </div>

            <!-- INFO CARD -->
            <div class="space-y-5">
                <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
                    <div class="text-sm font-semibold">Akan dibuat oleh</div>
                    <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-white/70">{{ auth()->user()->email }}</div>
                            </div>
                            <span class="rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs text-white/70">
                                admin
                            </span>
                        </div>

                        <div class="mt-3 text-xs text-white/70">
                            Saat user dibuat, sistem menyimpan <span class="font-semibold">created_by</span> dari admin ini.
                        </div>
                    </div>
                </div>

                <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
                    <div class="text-sm font-semibold">Catatan</div>
                    <ul class="mt-3 space-y-2 text-sm text-white/70">
                        <li>• Role dipilih dari form (Kasir / Kitchen / Pegawai)</li>
                        <li>• Email harus unik</li>
                        <li>• Password disimpan terenkripsi</li>
                        <li>• Jika user lupa password, admin bisa reset melalui fitur edit user</li>
                    </ul>

                    <div class="mt-4 rounded-2xl border border-white/15 bg-white/5 p-4 text-xs text-white/65">
                        <div class="font-semibold text-white/80">Tips</div>
                        <div class="mt-1">
                            Gunakan email asli untuk user internal. Jangan gunakan role <span class="font-semibold">guest</span> untuk user operasional.
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script>
            (function () {
                // Sinkronisasi input yang "bagus" (di luar form) ke hidden input (yang dikirim ke server)
                const nameInput = document.querySelector('input[placeholder="Nama user"]');
                const emailInput = document.querySelector('input[placeholder="user@example.com"]');
                const roleRadios = document.querySelectorAll('input[name="role"][type="radio"]');

                const hiddenName = document.getElementById('hidden_name');
                const hiddenEmail = document.getElementById('hidden_email');
                const hiddenRole = document.getElementById('hidden_role');

                if (nameInput && hiddenName) {
                    nameInput.addEventListener('input', () => hiddenName.value = nameInput.value);
                    // init
                    hiddenName.value = nameInput.value || hiddenName.value || '';
                }

                if (emailInput && hiddenEmail) {
                    emailInput.addEventListener('input', () => hiddenEmail.value = emailInput.value);
                    // init
                    hiddenEmail.value = emailInput.value || hiddenEmail.value || '';
                }

                if (roleRadios && hiddenRole) {
                    roleRadios.forEach(r => {
                        r.addEventListener('change', () => {
                            if (r.checked) hiddenRole.value = r.value;
                        });
                        if (r.checked) hiddenRole.value = r.value;
                    });
                }

                // Toggle password visibility
                function setupToggle(inputId, btnId, eyeOpenId, eyeClosedId) {
                    const input = document.getElementById(inputId);
                    const btn = document.getElementById(btnId);
                    const eyeOpen = document.getElementById(eyeOpenId);
                    const eyeClosed = document.getElementById(eyeClosedId);
                    if (!input || !btn || !eyeOpen || !eyeClosed) return;

                    btn.addEventListener('click', () => {
                        const isPassword = input.type === 'password';
                        input.type = isPassword ? 'text' : 'password';
                        eyeOpen.classList.toggle('hidden', isPassword);
                        eyeClosed.classList.toggle('hidden', !isPassword);
                    });
                }

                setupToggle('password', 'togglePassword', 'eyeOpen', 'eyeClosed');
                setupToggle('password_confirmation', 'togglePasswordConfirm', 'eyeOpen2', 'eyeClosed2');
            })();
        </script>
    </div>
@endsection