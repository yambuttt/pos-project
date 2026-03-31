<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — Ayo Renne</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            scroll-behavior: smooth;
        }

        * {
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
            background:
                radial-gradient(circle at top left, rgba(234, 179, 8, .10), transparent 28%),
                radial-gradient(circle at bottom right, rgba(234, 179, 8, .08), transparent 24%),
                linear-gradient(180deg, #030303 0%, #080808 100%);
        }

        .font-display {
            font-family: Georgia, "Times New Roman", serif;
        }

        .glass-panel {
            background: rgba(20, 20, 20, 0.78);
            backdrop-filter: blur(14px);
        }

        .gold-line {
            background: linear-gradient(90deg, rgba(234, 179, 8, .95), rgba(234, 179, 8, .18));
        }

        .field-dark {
            background: rgba(0, 0, 0, .35);
        }

        .field-dark:focus-within {
            border-color: rgba(234, 179, 8, .40);
            box-shadow: 0 0 0 3px rgba(234, 179, 8, .08);
        }
    </style>
</head>

<body class="min-h-screen text-white">
    <div class="relative min-h-screen overflow-hidden">
        <!-- ambient glow -->
        <div
            class="pointer-events-none absolute -left-20 top-0 h-[360px] w-[360px] rounded-full bg-yellow-500/10 blur-[120px]">
        </div>
        <div
            class="pointer-events-none absolute bottom-0 right-0 h-[420px] w-[420px] rounded-full bg-yellow-400/10 blur-[140px]">
        </div>

        <div
            class="relative mx-auto flex min-h-screen w-full max-w-7xl items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
            <div
                class="grid w-full max-w-6xl overflow-hidden rounded-[30px] border border-yellow-500/15 bg-white/[0.02] shadow-[0_20px_80px_rgba(0,0,0,.45)] lg:grid-cols-[1.02fr_.98fr]">
                <!-- LEFT -->
                <div class="glass-panel border-b border-yellow-500/10 p-6 sm:p-8 md:p-10 lg:border-b-0 lg:border-r">
                    <div class="mx-auto w-full max-w-[520px]">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                            <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne"
                                class="h-14 w-auto object-contain sm:h-16">
                        </a>

                        <div class="mt-8">
                            <p class="text-sm uppercase tracking-[0.24em] text-yellow-500">Akses Staff</p>
                            <h1 class="font-display mt-3 text-4xl font-bold leading-tight text-white sm:text-5xl">
                                Selamat Datang Kembali
                            </h1>
                            <p class="mt-4 max-w-md text-base leading-8 text-white/65 sm:text-lg">
                                Masuk ke sistem Ayo Renne untuk mengakses dashboard admin, kasir, kitchen, dan pegawai.
                            </p>
                        </div>

                        <div class="mt-6 h-[2px] w-24 rounded-full gold-line"></div>

                        @if ($errors->any())
                            <div
                                class="mt-6 rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-4 text-sm text-red-100">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form class="mt-8 space-y-5" method="POST" action="{{ route('login.submit') }}">
                            @csrf

                            <div>
                                <label for="email" class="mb-2 block text-sm font-medium text-white/85">
                                    Email
                                </label>
                                <div
                                    class="field-dark flex items-center gap-3 rounded-2xl border border-yellow-500/12 px-4 py-3.5 transition">
                                    <span class="text-yellow-500">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="1.8"
                                                stroke-linejoin="round" />
                                            <path d="M4 8l8 6 8-6" stroke="currentColor" stroke-width="1.8"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                                        placeholder="Masukkan email"
                                        class="w-full bg-transparent text-sm text-white outline-none placeholder:text-white/30 sm:text-base"
                                        required />
                                </div>
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-sm font-medium text-white/85">
                                    Password
                                </label>
                                <div
                                    class="field-dark flex items-center gap-3 rounded-2xl border border-yellow-500/12 px-4 py-3.5 transition">
                                    <span class="text-yellow-500">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="1.8"
                                                stroke-linecap="round" />
                                            <rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor"
                                                stroke-width="1.8" />
                                        </svg>
                                    </span>
                                    <input id="password" name="password" type="password" placeholder="Masukkan password"
                                        class="w-full bg-transparent text-sm text-white outline-none placeholder:text-white/30 sm:text-base"
                                        required />
                                </div>
                            </div>

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-yellow-500 px-5 py-4 text-sm font-semibold text-black transition hover:bg-yellow-400 active:scale-[0.995] sm:text-base">
                                Log in
                            </button>
                        </form>

                        <div class="mt-8">
                            <a href="{{ url('/') }}" class="text-sm text-white/55 transition hover:text-yellow-500">
                                ← Kembali ke beranda
                            </a>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="relative hidden lg:block">
                    <div class="absolute inset-0">
                        <img src="{{ asset('images/landing/hero-bg.jpg') }}" alt="Ayo Renne"
                            class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-br from-black/75 via-black/50 to-black/75"></div>
                    </div>

                    <div class="relative flex h-full items-end p-10 xl:p-12">
                        <div class="w-full rounded-[28px] border border-yellow-500/16 bg-black/35 p-7 xl:p-8 backdrop-blur-md">
                            <div
                                class="inline-flex rounded-full border border-yellow-500/20 bg-yellow-500/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-yellow-500">
                                Sistem Operasional
                            </div>

                            <h2 class="font-display mt-6 text-[52px] font-bold leading-[1.06] text-white">
                                Akses dashboard sesuai peran Anda.
                            </h2>

                            <p class="mt-5 max-w-xl text-lg leading-8 text-white/72">
                                Halaman ini digunakan untuk masuk ke sistem operasional Ayo Renne oleh admin, kasir,
                                kitchen, dan pegawai.
                            </p>

                            <div class="mt-8 grid grid-cols-2 gap-4 xl:grid-cols-2">
    <div class="rounded-2xl border border-yellow-500/12 bg-white/[0.04] px-4 py-4">
        <div class="text-2xl font-bold leading-tight text-yellow-500">Admin</div>
        <div class="mt-2 text-sm leading-6 text-white/60">Produk, stok, laporan</div>
    </div>

    <div class="rounded-2xl border border-yellow-500/12 bg-white/[0.04] px-4 py-4">
        <div class="text-2xl font-bold leading-tight text-yellow-500">Kasir</div>
        <div class="mt-2 text-sm leading-6 text-white/60">Transaksi dan pesanan</div>
    </div>

    <div class="rounded-2xl border border-yellow-500/12 bg-white/[0.04] px-4 py-4">
        <div class="text-2xl font-bold leading-tight text-yellow-500">Kitchen</div>
        <div class="mt-2 text-sm leading-6 text-white/60">Proses order masuk</div>
    </div>

    <div class="rounded-2xl border border-yellow-500/12 bg-white/[0.04] px-4 py-4">
        <div class="text-2xl font-bold leading-tight text-yellow-500">Pegawai</div>
        <div class="mt-2 text-sm leading-6 text-white/60">Absensi dan akses kerja</div>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /RIGHT -->
            </div>
        </div>
    </div>
</body>

</html>