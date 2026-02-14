<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen text-white">
    <!-- Background -->
    <div class="relative min-h-screen overflow-hidden">
        <!-- Base gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-sky-500 via-indigo-500 to-fuchsia-500"></div>

        <!-- Floating blur blobs -->
        <div class="absolute -top-24 -left-24 h-[420px] w-[420px] rounded-full bg-cyan-300/40 blur-[120px]"></div>
        <div class="absolute -bottom-32 -right-28 h-[520px] w-[520px] rounded-full bg-pink-300/40 blur-[140px]"></div>
        <div class="absolute top-24 right-24 h-[320px] w-[320px] rounded-full bg-violet-300/35 blur-[110px]"></div>

        <!-- Soft dark overlay (biar teks kebaca) -->
        <div class="absolute inset-0 bg-black/10"></div>

        <!-- Content -->
        <div class="relative mx-auto flex min-h-screen w-full max-w-6xl items-center justify-center px-5 sm:px-8">
            <!-- Glass container -->
            <div
                class="grid w-full grid-cols-1 overflow-hidden rounded-[28px] border border-white/20 bg-white/10 shadow-2xl backdrop-blur-2xl md:grid-cols-2">

                <!-- Left: Login -->
                <div class="p-7 sm:p-10">
                    <h1 class="text-2xl font-semibold tracking-tight">Log in</h1>
                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-red-200/30 bg-red-500/10 px-4 py-3 text-sm text-white">
                            {{ $errors->first() }}
                        </div>
                    @endif


                    <form class="mt-8 space-y-4" method="POST" action="{{ route('login.submit') }}">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label class="sr-only" for="email">Email</label>
                            <div
                                class="flex items-center gap-3 rounded-xl border border-white/20 bg-white/10 px-4 py-3 focus-within:border-white/40">
                                <span class="text-white/70">✉️</span>
                                <input id="email" name="email" type="email" placeholder="Enter your email"
                                    class="w-full bg-transparent text-sm outline-none placeholder:text-white/50" />
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="sr-only" for="password">Password</label>
                            <div
                                class="flex items-center gap-3 rounded-xl border border-white/20 bg-white/10 px-4 py-3 focus-within:border-white/40">
                                <span class="text-white/70">🔒</span>
                                <input id="password" name="password" type="password" placeholder="Enter your password"
                                    class="w-full bg-transparent text-sm outline-none placeholder:text-white/50" />
                            </div>
                        </div>

                        <!-- Button -->
                        <button type="submit"
                            class="mt-2 w-full rounded-xl bg-blue-600/90 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/30 transition hover:bg-blue-500/90 active:scale-[0.99]">
                            Log in
                        </button>

                        <!-- Divider -->
                        <div class="my-5 flex items-center gap-4">
                            <div class="h-px flex-1 bg-white/20"></div>
                            <span class="text-xs text-white/70">or</span>
                            <div class="h-px flex-1 bg-white/20"></div>
                        </div>

                        <!-- Google button (UI only) -->
                        <button type="button"
                            class="w-full rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/15">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-base">G</span>
                                Continue with Google
                            </span>
                        </button>

                        <p class="pt-5 text-center text-xs text-white/75">
                            Forgot your password? <span
                                class="font-semibold text-white underline underline-offset-4">Reset password</span>
                        </p>
                    </form>
                </div>

                <!-- Right: Info panel (hidden on mobile) -->
                <div class="relative hidden md:block">
                    <div class="absolute inset-0 bg-white/5"></div>

                    <div class="relative h-full p-10">
                        <div
                            class="absolute right-10 top-10 w-[78%] rounded-3xl border border-white/20 bg-white/10 p-8 shadow-2xl backdrop-blur-2xl">
                            <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-xs text-white/80">
                                Turn your ideas into Reality
                            </div>

                            <div class="mt-6">
                                <p class="text-white/70 text-sm">We are</p>
                                <p class="text-xl font-semibold">Invite only right now.</p>
                                <p class="mt-3 text-sm text-white/70 leading-relaxed">
                                    Modern POS dashboard for admin & cashier. Secure access and clean UI.
                                </p>
                            </div>

                            <div class="mt-10 flex items-center justify-between text-xs text-white/70">
                                <span>Invite your friends</span>
                                <div class="flex items-center gap-3 text-white/80">
                                    <span>🐦</span><span>in</span><span>◎</span>
                                </div>
                            </div>
                        </div>

                        <!-- Ambient glow -->
                        <div
                            class="absolute -bottom-16 left-10 h-[260px] w-[260px] rounded-full bg-white/10 blur-[90px]">
                        </div>
                        <div
                            class="absolute bottom-24 right-6 h-[220px] w-[220px] rounded-full bg-fuchsia-200/20 blur-[90px]">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>