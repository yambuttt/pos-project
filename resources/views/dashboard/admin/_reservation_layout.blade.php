<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Reservasi')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold">@yield('page_title', 'Reservasi')</h1>
                <p class="text-sm text-gray-600">@yield('page_subtitle', '')</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.reservations.index') }}"
                   class="px-3 py-2 rounded bg-white border hover:bg-gray-100">
                    Reservasi
                </a>
                <a href="{{ route('admin.reservation_resources.index') }}"
                   class="px-3 py-2 rounded bg-white border hover:bg-gray-100">
                    Resource (Meja/Ruang/Hall)
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-800">
                <div class="font-semibold mb-1">Terjadi error:</div>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border rounded-lg p-4">
            @yield('content')
        </div>
    </div>
</body>
</html>