@extends('layouts.admin')
@section('title', 'Buat Kasir')

@section('body')
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button id="openMobileSidebar" type="button"
                class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden">
                ☰
            </button>

            <div>
                <h1 class="text-xl font-semibold">Buat Akun Kasir</h1>
                <p class="text-sm text-white/70">Masukkan data kasir. Akan tercatat dibuat oleh admin yang login.</p>
            </div>
        </div>

        <a href="{{ route('admin.cashiers.index') }}"
            class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
            ← Kembali
        </a>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.2fr_0.8fr]">

        <!-- FORM -->
        <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-7">
            <form method="POST" action="{{ route('admin.cashiers.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-sm text-white/80">Nama</label>
                    <input name="name" value="{{ old('name') }}"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
                        placeholder="Nama kasir" />
                    @error('name') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm text-white/80">Email</label>
                    <input name="email" value="{{ old('email') }}" type="email"
                        class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm outline-none placeholder:text-white/40 focus:border-white/40"
                        placeholder="kasir@example.com" />
                    @error('email') <p class="mt-2 text-xs text-red-100/90">{{ $message }}</p> @enderror
                </div>

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
                            <!-- Eye icon (Heroicons) -->
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/80" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            <!-- Eye slash icon -->
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



                <button
                    class="w-full rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
                    Buat Akun Kasir
                </button>
            </form>
        </div>

        <!-- INFO CARD -->
        <div class="space-y-5">
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
                <div class="text-sm font-semibold">Akan dibuat oleh</div>
                <div class="mt-3 rounded-2xl border border-white/15 bg-white/10 p-4">
                    <div class="text-sm font-semibold">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-white/70">{{ auth()->user()->email }}</div>
                    <div class="mt-3 text-xs text-white/70">
                        Saat kasir dibuat, sistem menyimpan <span class="font-semibold">created_by</span> dari admin ini.
                    </div>
                </div>
            </div>

            <div class="rounded-[26px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl sm:p-6">
                <div class="text-sm font-semibold">Catatan</div>
                <ul class="mt-3 space-y-2 text-sm text-white/70">
                    <li>• Role otomatis: <span class="text-white/90 font-semibold">kasir</span></li>
                    <li>• Email harus unik</li>
                    <li>• Password disimpan terenkripsi</li>
                </ul>
            </div>
        </div>

    </div>

    <script>
        (function () {
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

@endsection