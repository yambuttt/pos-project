@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('body')
    <!-- TOP BAR -->
    <div class="flex items-center justify-between gap-3">
        <button id="openMobileSidebar" type="button"
            class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15 lg:hidden"
            title="Menu">
            ☰
        </button>

        <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-2xl">
            <div class="text-xs text-white/70">Admin Panel</div>
            <div class="text-sm font-semibold">POS Dashboard</div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <div
                class="hidden sm:block rounded-2xl border border-white/20 bg-white/10 px-4 py-2 text-sm text-white/85 backdrop-blur-2xl">
                {{ auth()->user()->name ?? 'Admin' }} • <span class="text-white/70">{{ auth()->user()->email }}</span>
            </div>
            <button
                class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm backdrop-blur-xl hover:bg-white/15">🔔</button>
        </div>
    </div>

    <!-- CONTENT GRID -->
    <div class="mt-5 space-y-5">
        <!-- HERO CARD (mirip referensi: headline + image area + action) -->
        <section class="grid grid-cols-1 gap-5 xl:grid-cols-[1.4fr_1fr]">
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                <div class="text-xs font-semibold text-white/70">Channel Analytics</div>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">
                    Optimize your metrics
                </h1>
                <p class="mt-2 max-w-xl text-sm text-white/70">
                    Pantau performa penjualan, produk terlaris, dan transaksi terbaru. (Dummy data)
                </p>

                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <button
                        class="rounded-xl bg-blue-600/85 px-5 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-blue-500/85">
                        Scan Invoice
                    </button>
                    <button
                        class="rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold backdrop-blur-xl hover:bg-white/15">
                        Add Product
                    </button>

                    <div
                        class="sm:ml-auto rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-xl">
                        <span class="text-white/70">Active:</span>
                        <span class="font-semibold"> 12 users</span>
                    </div>
                </div>
            </div>

            <!-- Right hero media dummy -->
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-semibold">Active users right now</div>
                    <button
                        class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs backdrop-blur-xl hover:bg-white/15">⋯</button>
                </div>

                <!-- Mini chart placeholder (SVG) -->
                <div class="mt-5 rounded-2xl border border-white/15 bg-white/10 p-4">
                    <svg viewBox="0 0 600 160" class="h-32 w-full">
                        <path d="M10,120 C80,30 140,150 210,70 C280,5 330,145 400,85 C470,25 520,95 590,40" fill="none"
                            stroke="rgba(255,255,255,0.9)" stroke-width="4" stroke-linecap="round" />
                        <path
                            d="M10,120 C80,30 140,150 210,70 C280,5 330,145 400,85 C470,25 520,95 590,40 L590,160 L10,160 Z"
                            fill="rgba(255,255,255,0.10)" />
                    </svg>
                    <div class="mt-3 flex items-center justify-between text-xs text-white/70">
                        <span>00:00</span>
                        <span>12:00</span>
                        <span>24:00</span>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                        <div class="text-xs text-white/70">New Orders</div>
                        <div class="mt-1 text-xl font-semibold">47</div>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                        <div class="text-xs text-white/70">Revenue Today</div>
                        <div class="mt-1 text-xl font-semibold">Rp 3.600k</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- STAT CARDS -->
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $stats = [
                    ['label' => 'Total Produk', 'value' => '126', 'hint' => '+6 minggu ini'],
                    ['label' => 'Transaksi Hari Ini', 'value' => '47', 'hint' => '+12% vs kemarin'],
                    ['label' => 'Penjualan', 'value' => 'Rp 3.6jt', 'hint' => 'target 5jt'],
                    ['label' => 'Profit', 'value' => 'Rp 860rb', 'hint' => 'estimasi'],
                ];
              @endphp

            @foreach ($stats as $s)
                <div class="rounded-[22px] border border-white/20 bg-white/10 p-5 backdrop-blur-2xl">
                    <div class="text-xs text-white/70">{{ $s['label'] }}</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $s['value'] }}</div>
                    <div class="mt-2 text-xs text-white/65">{{ $s['hint'] }}</div>
                </div>
            @endforeach
        </section>

        <!-- LOWER GRID: product list + recent sales -->
        <section class="grid grid-cols-1 gap-5 xl:grid-cols-[1.7fr_1fr]">

            <!-- Produk (dummy table) -->
            <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold">Daftar Produk</div>
                        <div class="text-xs text-white/70">Dummy list (nanti diambil dari DB)</div>
                    </div>
                    <button
                        class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs backdrop-blur-xl hover:bg-white/15">
                        Filter
                    </button>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl border border-white/15">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/10 text-xs text-white/70">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Harga</th>
                                <th class="px-4 py-3">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @php
                                $products = [
                                    ['name' => 'Americano', 'cat' => 'Coffee', 'price' => 'Rp 25.000', 'stock' => 32],
                                    ['name' => 'Latte', 'cat' => 'Coffee', 'price' => 'Rp 28.000', 'stock' => 21],
                                    ['name' => 'Matcha', 'cat' => 'Non Coffee', 'price' => 'Rp 30.000', 'stock' => 18],
                                    ['name' => 'Croissant', 'cat' => 'Snack', 'price' => 'Rp 22.000', 'stock' => 15],
                                    ['name' => 'Brownies', 'cat' => 'Snack', 'price' => 'Rp 20.000', 'stock' => 9],
                                ];
                              @endphp

                            @foreach ($products as $p)
                                <tr class="bg-white/0 hover:bg-white/5">
                                    <td class="px-4 py-3 font-medium">{{ $p['name'] }}</td>
                                    <td class="px-4 py-3 text-white/75">{{ $p['cat'] }}</td>
                                    <td class="px-4 py-3 text-white/85">{{ $p['price'] }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-xs">
                                            {{ $p['stock'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between text-xs text-white/70">
                    <span>Showing 5 of 126</span>
                    <div class="flex items-center gap-2">
                        <button
                            class="rounded-lg border border-white/20 bg-white/10 px-3 py-1.5 hover:bg-white/15">Prev</button>
                        <button
                            class="rounded-lg border border-white/20 bg-white/10 px-3 py-1.5 hover:bg-white/15">Next</button>
                    </div>
                </div>
            </div>

            <!-- Recent Sales + Widget small -->
            <div class="space-y-5">
                <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold">Latest Sales</div>
                            <div class="text-xs text-white/70">Dummy transaksi terbaru</div>
                        </div>
                        <button
                            class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs backdrop-blur-xl hover:bg-white/15">
                            View all
                        </button>
                    </div>

                    <div class="mt-5 space-y-3">
                        @php
                            $sales = [
                                ['code' => 'INV-00021', 'who' => 'Kasir 1', 'amount' => 'Rp 86.000', 'time' => '2m ago'],
                                ['code' => 'INV-00020', 'who' => 'Kasir 2', 'amount' => 'Rp 54.000', 'time' => '7m ago'],
                                ['code' => 'INV-00019', 'who' => 'Kasir 1', 'amount' => 'Rp 112.000', 'time' => '18m ago'],
                                ['code' => 'INV-00018', 'who' => 'Kasir 3', 'amount' => 'Rp 40.000', 'time' => '35m ago'],
                            ];
                        @endphp

                        @foreach ($sales as $s)
                            <div
                                class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                                <div>
                                    <div class="text-sm font-semibold">{{ $s['code'] }}</div>
                                    <div class="text-xs text-white/70">{{ $s['who'] }} • {{ $s['time'] }}</div>
                                </div>
                                <div class="text-sm font-semibold">{{ $s['amount'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Small widget like “popular product” -->
                <div class="rounded-[26px] border border-white/20 bg-white/10 p-6 backdrop-blur-2xl sm:p-7">
                    <div class="text-sm font-semibold">Popular Product</div>
                    <div class="mt-4 rounded-2xl border border-white/15 bg-white/10 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold">Latte</div>
                                <div class="text-xs text-white/70">Sold today: 23</div>
                            </div>
                            <div class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs">🔥 #1</div>
                        </div>
                        <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-white/10">
                            <div class="h-full w-[72%] rounded-full bg-white/70"></div>
                        </div>
                        <div class="mt-2 flex justify-between text-xs text-white/70">
                            <span>0</span><span>50</span>
                        </div>
                    </div>

                    <div class="mt-4 text-xs text-white/70">
                        Nanti widget ini bisa jadi “produk terlaris / stok menipis / target harian”.
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection