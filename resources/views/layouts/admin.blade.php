<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Dashboard')</title>
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
                radial-gradient(circle at top left, rgba(234, 179, 8, .08), transparent 26%),
                radial-gradient(circle at bottom right, rgba(234, 179, 8, .06), transparent 24%),
                linear-gradient(180deg, #030303 0%, #090909 100%);
        }

        .admin-shell {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .01), rgba(255, 255, 255, .00));
        }

        .panel-dark {
            background: rgba(16, 16, 16, 0.86);
            backdrop-filter: blur(12px);
        }

        .panel-soft {
            background: rgba(255, 255, 255, .03);
            backdrop-filter: blur(10px);
        }

        .gold-border {
            border-color: rgba(234, 179, 8, 0.16);
        }

        .gold-border-strong {
            border-color: rgba(234, 179, 8, 0.30);
        }

        .gold-text {
            color: rgb(234 179 8);
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(234, 179, 8, .18);
            border-radius: 999px;
        }
    </style>
</head>

<body class="min-h-screen text-white">
    <div class="relative min-h-screen overflow-hidden">
        <!-- ambient -->
        <div
            class="pointer-events-none absolute -left-24 top-0 h-[380px] w-[380px] rounded-full bg-yellow-500/10 blur-[120px]">
        </div>
        <div
            class="pointer-events-none absolute bottom-0 right-0 h-[440px] w-[440px] rounded-full bg-yellow-400/8 blur-[140px]">
        </div>

        <div class="relative min-h-screen admin-shell">
            <div class="flex min-h-screen w-full">
                @php
                    $isDashboard = request()->routeIs('admin.dashboard');
                    $isProducts = request()->routeIs('admin.products.*');
                    $isSales = request()->routeIs('admin.sales.*');
                    $isUsers = request()->routeIs('admin.cashiers.*');
                    $isRawMaterials = request()->routeIs('admin.raw_materials.*');
                    $isPurchases = request()->routeIs('admin.purchases.*');
                    $isAttendanceQr = request()->routeIs('admin.attendance.qr*');
                    $isAttendanceDevices = request()->routeIs('admin.attendance.devices*');
                    $isAttendanceHistory = request()->routeIs('admin.attendance.history*');
                    $isAttendanceExceptions = request()->routeIs('admin.attendance.exception_requests*');
                    $isShiftSettings = request()->routeIs('admin.shifts.*');
                    $isLeaveRequests = request()->routeIs('admin.leave_requests.*');
                    $isUserGroup = $isUsers || $isAttendanceQr || $isAttendanceDevices || $isAttendanceHistory || $isAttendanceExceptions || $isShiftSettings || $isLeaveRequests;

                    $isWastes = request()->routeIs('admin.wastes.*');
                    $isOpnames = request()->routeIs('admin.opnames.*');
                    $isInvMovements = request()->routeIs('admin.inventory-movements.*');
                    $isTables = request()->routeIs('admin.tables.*');
                    $isInventory = $isRawMaterials || $isPurchases || $isWastes || $isOpnames || $isInvMovements || $isTables || $isAttendanceQr || $isAttendanceDevices;
                @endphp

                <!-- DESKTOP SIDEBAR -->
                <aside id="adminSidebarDesktop"
                    class="panel-dark gold-border sidebar-scroll relative m-4 hidden h-[calc(100vh-2rem)] shrink-0 overflow-y-auto rounded-[28px] border shadow-[0_20px_60px_rgba(0,0,0,.35)] transition-all duration-200 ease-out lg:block w-16">

                    <div class="flex h-full flex-col p-3">
                        <div class="flex items-center justify-center">
                            <button id="sidebarPinBtn" type="button"
                                class="panel-soft gold-border flex h-10 w-10 items-center justify-center rounded-xl border hover:border-yellow-500/35"
                                title="Pin sidebar" aria-label="Pin sidebar" data-pinned="0">
                                <svg id="pinIconGear" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/90"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 6h3M12 3v3m7.5 6a7.5 7.5 0 01-15 0 7.5 7.5 0 0115 0z" />
                                </svg>

                                <svg id="pinIconPinned" xmlns="http://www.w3.org/2000/svg"
                                    class="hidden h-5 w-5 text-white/90" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 11l4-4m0 0l-4-4m4 4H9a4 4 0 00-4 4v8" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-4 flex items-center gap-3 px-2">
                            <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne"
                                class="sidebar-label hidden h-12 w-auto object-contain">
                        </div>

                        <div class="mt-5 space-y-2">
                            <a href="{{ route('admin.dashboard') }}"
                                class="relative flex items-center gap-3 rounded-xl px-3 py-3 transition
                                {{ $isDashboard ? 'bg-yellow-500/12 text-white border border-yellow-500/30' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05] text-white/88' }}">
                                @if ($isDashboard)
                                    <span
                                        class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 10.5L12 3l9 7.5v9a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 19.5v-9z" />
                                </svg>
                                <span class="sidebar-label hidden text-sm font-medium">Dashboard</span>
                            </a>

                            <a href="{{ route('admin.products.index') }}"
                                class="relative flex items-center gap-3 rounded-xl px-3 py-3 transition
                                {{ $isProducts ? 'bg-yellow-500/12 text-white border border-yellow-500/30' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05] text-white/88' }}">
                                @if ($isProducts)
                                    <span
                                        class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5v9L12 21l9-4.5v-9" />
                                </svg>
                                <span class="sidebar-label hidden text-sm font-medium">Produk</span>
                            </a>

                            <div class="mt-2">
                                <button type="button" id="invToggleDesktop"
                                    class="relative flex w-full items-center gap-3 rounded-xl px-3 py-3 transition
                                    {{ $isInventory ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                    @if ($isInventory)
                                        <span
                                            class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                    @endif

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5v9L12 21l9-4.5v-9" />
                                    </svg>

                                    <span
                                        class="sidebar-label hidden flex-1 text-left text-sm font-medium text-white/90">Inventory</span>

                                    <svg id="invChevronDesktop" xmlns="http://www.w3.org/2000/svg"
                                        class="sidebar-label hidden h-4 w-4 text-white/70 transition" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>

                                <div id="invMenuDesktop" class="mt-2 space-y-2 pl-2 {{ $isInventory ? '' : 'hidden' }}">
                                    <a href="{{ route('admin.raw_materials.index') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                                        {{ $isRawMaterials ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isRawMaterials)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">Bahan
                                            Baku</span>
                                    </a>

                                    <a href="{{ route('admin.purchases.index') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                                        {{ $isPurchases ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isPurchases)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span
                                            class="sidebar-label hidden text-sm font-medium text-white/90">Purchases</span>
                                    </a>

                                    <a href="{{ route('admin.wastes.index') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                                        {{ $isWastes ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isWastes)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span
                                            class="sidebar-label hidden text-sm font-medium text-white/90">Waste</span>
                                    </a>

                                    <a href="{{ route('admin.opnames.index') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                                        {{ $isOpnames ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isOpnames)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">Stock
                                            Opname</span>
                                    </a>

                                    <a href="{{ route('admin.inventory-movements.index') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                                        {{ $isInvMovements ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isInvMovements)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">Inventory
                                            Movement</span>
                                    </a>
                                </div>
                            </div>

                            <a href="{{ route('admin.sales.index') }}"
                                class="relative flex items-center gap-3 rounded-xl px-3 py-3 transition
                                {{ $isSales ? 'bg-yellow-500/12 text-white border border-yellow-500/30' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05] text-white/88' }}">
                                @if ($isSales)
                                    <span
                                        class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 3h9a1.5 1.5 0 011.5 1.5v15a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 016 19.5v-15A1.5 1.5 0 017.5 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7.5h6M9 11h6M9 14.5h3" />
                                </svg>
                                <span class="sidebar-label hidden text-sm font-medium">Transaksi</span>
                            </a>


                            <div class="mt-2">
                                <button type="button" id="userToggleDesktop"
                                    class="relative flex w-full items-center gap-3 rounded-xl px-3 py-3 transition
     {{ $isUserGroup ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                    @if ($isUserGroup)
                                        <span
                                            class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                    @endif

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 20.25a8.25 8.25 0 0115 0" />
                                    </svg>

                                    <span
                                        class="sidebar-label hidden flex-1 text-left text-sm font-medium text-white/90">User</span>

                                    <svg id="userChevronDesktop" xmlns="http://www.w3.org/2000/svg"
                                        class="sidebar-label hidden h-4 w-4 text-white/70 transition" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>

                                <div id="userMenuDesktop"
                                    class="mt-2 space-y-2 pl-2 {{ $isUserGroup ? '' : 'hidden' }}">
                                    <a href="{{ route('admin.cashiers.index') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
       {{ $isUsers ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isUsers)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">Kelola
                                            User</span>
                                    </a>
                                    <a href="{{ route('admin.attendance.exception_requests') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
   {{ $isAttendanceExceptions ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isAttendanceExceptions)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">Pengajuan
                                            Absensi</span>
                                    </a>
                                    <a href="{{ route('admin.shifts.index') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
   {{ $isShiftSettings ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isShiftSettings)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">Shift
                                            Pegawai</span>
                                    </a>

                                    <a href="{{ route('admin.attendance.qr') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
       {{ $isAttendanceQr ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isAttendanceQr)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">Absensi
                                            QR</span>
                                    </a>

                                    <a href="{{ route('admin.attendance.devices') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
       {{ $isAttendanceDevices ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isAttendanceDevices)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">Device
                                            Absensi</span>
                                    </a>
                                    <a href="{{ route('admin.attendance.history') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
  {{ $isAttendanceHistory ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isAttendanceHistory)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">History
                                            Absensi</span>
                                    </a>
                                    <a href="{{ route('admin.leave_requests.index') }}"
                                        class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
  {{ $isLeaveRequests ? 'bg-yellow-500/10 border border-yellow-500/25' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05]' }}">
                                        @if ($isLeaveRequests)
                                            <span
                                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                        @endif
                                        <span class="sidebar-label hidden text-sm font-medium text-white/90">Permintaan Cuti</span>
                                    </a>

                                </div>
                            </div>

                            <a href="{{ route('admin.tables.index') }}"
                                class="relative flex items-center gap-3 rounded-xl px-3 py-3 transition
                                {{ $isTables ? 'bg-yellow-500/12 text-white border border-yellow-500/30' : 'border gold-border bg-white/[0.02] hover:bg-white/[0.05] text-white/88' }}">
                                @if ($isTables)
                                    <span
                                        class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-yellow-500"></span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.5 10.5h15M6 20.25V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5M7.5 20.25v-6h9v6" />
                                </svg>
                                <span class="sidebar-label hidden text-sm font-medium text-white/90">Meja</span>
                            </a>

                            <a href="#"
                                class="flex items-center gap-3 rounded-xl border gold-border bg-white/[0.02] px-3 py-3 text-white/88 hover:bg-white/[0.05]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 14.25l3-3 3 3 4.5-4.5" />
                                </svg>
                                <span class="sidebar-label hidden text-sm font-medium text-white/90">Laporan</span>
                            </a>
                        </div>

                        <div class="mt-auto space-y-3">
                            <div class="rounded-2xl border gold-border bg-white/[0.03] p-3">
                                <div class="sidebar-label hidden text-[11px] text-white/55">Role</div>
                                <div class="sidebar-label hidden mt-1 text-sm font-semibold text-white">
                                    {{ auth()->user()->role }}
                                </div>
                                <div class="sidebar-label hidden mt-2 text-[11px] text-white/45">
                                    Panel operasional internal.
                                </div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    class="flex w-full items-center justify-center gap-3 rounded-xl bg-yellow-500 px-3 py-3 text-sm font-semibold text-black hover:bg-yellow-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18 12H9m9 0l-3-3m3 3l-3 3" />
                                    </svg>
                                    <span class="sidebar-label hidden">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>

                <!-- MOBILE/TABLET SIDEBAR -->
                <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/60 lg:hidden"></div>

                <aside id="adminSidebarMobile"
                    class="panel-dark gold-border fixed left-0 top-0 z-50 hidden h-full w-[290px] border-r p-4 lg:hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/landing/logo-ayo-renne.png') }}" alt="Ayo Renne"
                                class="h-12 w-auto object-contain">
                            <div>
                                <div class="text-xs uppercase tracking-[0.22em] text-yellow-500">Admin Panel</div>
                                <div class="text-sm font-semibold text-white">Ayo Renne</div>
                            </div>
                        </div>

                        <button id="closeMobileSidebar"
                            class="rounded-xl border gold-border bg-white/[0.04] px-3 py-2 hover:bg-white/[0.08]">
                            ✕
                        </button>
                    </div>

                    <div class="mt-5 space-y-2 text-sm">
                        <a href="{{ route('admin.dashboard') }}"
                            class="block rounded-xl px-3 py-3 {{ $isDashboard ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02]' }}">
                            Dashboard
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                            class="block rounded-xl px-3 py-3 {{ $isProducts ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02]' }}">
                            Produk
                        </a>

                        <div class="rounded-xl border gold-border bg-white/[0.02] p-2">
                            <div class="px-2 py-2 text-sm font-semibold text-white/90">Inventory</div>

                            <a href="{{ route('admin.raw_materials.index') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isRawMaterials ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Bahan Baku
                            </a>

                            <a href="{{ route('admin.purchases.index') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isPurchases ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Purchases
                            </a>

                            <a href="{{ route('admin.wastes.index') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isWastes ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Waste
                            </a>

                            <a href="{{ route('admin.opnames.index') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isOpnames ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Stock Opname
                            </a>

                            <a href="{{ route('admin.inventory-movements.index') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isInvMovements ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Inventory Movement
                            </a>
                        </div>

                        <a href="{{ route('admin.sales.index') }}"
                            class="block rounded-xl px-3 py-3 {{ $isSales ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02]' }}">
                            Transaksi
                        </a>



                        <div class="rounded-xl border gold-border bg-white/[0.02] p-2">
                            <div class="px-2 py-2 text-sm font-semibold text-white/90">User</div>

                            <a href="{{ route('admin.cashiers.index') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isUsers ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Kelola User
                            </a>

                            <a href="{{ route('admin.attendance.exception_requests') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isAttendanceExceptions ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Pengajuan Absensi
                            </a>

                            <a href="{{ route('admin.shifts.index') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isShiftSettings ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Shift Pegawai
                            </a>

                            <a href="{{ route('admin.attendance.qr') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isAttendanceQr ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Absensi QR
                            </a>

                            <a href="{{ route('admin.attendance.devices') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isAttendanceDevices ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Device Absensi
                            </a>
                            <a href="{{ route('admin.attendance.history') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isAttendanceHistory ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                History Absensi
                            </a>
                            <a href="{{ route('admin.leave_requests.index') }}"
                                class="mt-1 block rounded-xl px-3 py-2 {{ $isLeaveRequests ? 'bg-yellow-500/12' : 'hover:bg-white/[0.05]' }}">
                                Permintaan Cuti
                            </a>
                        </div>

                        <a href="{{ route('admin.tables.index') }}"
                            class="block rounded-xl px-3 py-3 {{ $isTables ? 'bg-yellow-500/12 border border-yellow-500/30' : 'border gold-border bg-white/[0.02]' }}">
                            Meja
                        </a>
                    </div>

                    <div class="mt-6 rounded-2xl border gold-border bg-white/[0.03] p-4">
                        <div class="text-xs text-white/55">Login as</div>
                        <div class="mt-1 text-sm font-semibold">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="text-xs text-white/55">{{ auth()->user()->email }}</div>
                    </div>
                    <a href="{{ route('admin.account') }}"
                        class="mt-3 inline-flex items-center justify-center rounded-xl border gold-border bg-white/[0.02] px-3 py-2 text-xs font-semibold text-white/85 hover:bg-white/[0.06]">
                        ⚙️ Akun Saya
                    </a>

                    <div class="mt-auto pt-5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                class="w-full rounded-xl bg-yellow-500 px-4 py-3 text-sm font-semibold text-black hover:bg-yellow-400">
                                Logout
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- MAIN -->
                <main id="adminMain" class="flex-1 p-4 lg:p-6">
                    @yield('body')
                </main>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const sidebarD = document.getElementById('adminSidebarDesktop');
            const main = document.getElementById('adminMain');
            const pinBtn = document.getElementById('sidebarPinBtn');
            const pinIconGear = document.getElementById('pinIconGear');
            const pinIconPinned = document.getElementById('pinIconPinned');

            if (sidebarD && main && pinBtn) {
                let pinned = false;

                function openSidebar() {
                    sidebarD.classList.remove('w-16');
                    sidebarD.classList.add('w-72');
                    sidebarD.querySelectorAll('.sidebar-label').forEach(el => el.classList.remove('hidden'));
                }

                function closeSidebar() {
                    sidebarD.classList.add('w-16');
                    sidebarD.classList.remove('w-72');
                    sidebarD.querySelectorAll('.sidebar-label').forEach(el => el.classList.add('hidden'));
                }

                function setPinnedState(isPinned) {
                    pinned = isPinned;
                    pinBtn.dataset.pinned = pinned ? "1" : "0";
                    if (pinIconGear && pinIconPinned) {
                        pinIconGear.classList.toggle('hidden', pinned);
                        pinIconPinned.classList.toggle('hidden', !pinned);
                    }
                    pinBtn.title = pinned ? 'Unpin sidebar' : 'Pin sidebar';
                }

                closeSidebar();
                setPinnedState(false);

                sidebarD.addEventListener('mouseenter', () => {
                    if (!pinned) openSidebar();
                });
                sidebarD.addEventListener('mouseleave', () => {
                    if (!pinned) closeSidebar();
                });
                main.addEventListener('mouseenter', () => {
                    if (!pinned) closeSidebar();
                });

                pinBtn.addEventListener('click', () => {
                    setPinnedState(!pinned);
                    if (pinned) openSidebar();
                    else closeSidebar();
                });
            }

            const openBtn = document.getElementById('openMobileSidebar');
            const closeBtn = document.getElementById('closeMobileSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const sidebarM = document.getElementById('adminSidebarMobile');

            function openMobile() {
                if (!overlay || !sidebarM) return;
                overlay.classList.remove('hidden');
                sidebarM.classList.remove('hidden');
            }

            function closeMobile() {
                if (!overlay || !sidebarM) return;
                overlay.classList.add('hidden');
                sidebarM.classList.add('hidden');
            }

            if (sidebarM) {
                sidebarM.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMobile));
            }

            if (openBtn) openBtn.addEventListener('click', openMobile);
            if (closeBtn) closeBtn.addEventListener('click', closeMobile);
            if (overlay) overlay.addEventListener('click', closeMobile);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeMobile();
            });

            const invToggle = document.getElementById('invToggleDesktop');
            const invMenu = document.getElementById('invMenuDesktop');
            const invChevron = document.getElementById('invChevronDesktop');

            if (invToggle && invMenu && invChevron) {
                invToggle.addEventListener('click', () => {
                    const isHidden = invMenu.classList.contains('hidden');
                    invMenu.classList.toggle('hidden');
                    invChevron.classList.toggle('rotate-90', isHidden);
                });

                if (!invMenu.classList.contains('hidden')) {
                    invChevron.classList.add('rotate-90');
                }
            }

            const userToggle = document.getElementById('userToggleDesktop');
            const userMenu = document.getElementById('userMenuDesktop');
            const userChevron = document.getElementById('userChevronDesktop');

            if (userToggle && userMenu && userChevron) {
                userToggle.addEventListener('click', () => {
                    const isHidden = userMenu.classList.contains('hidden');
                    userMenu.classList.toggle('hidden');
                    userChevron.classList.toggle('rotate-90', isHidden);
                });

                if (!userMenu.classList.contains('hidden')) {
                    userChevron.classList.add('rotate-90');
                }
            }
        })();
    </script>
</body>

</html>