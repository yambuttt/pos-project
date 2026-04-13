@extends('dashboard.admin._reservation_layout')

@section('title', 'Reservasi')
@section('page_title', 'Reservasi')
@section('page_subtitle', 'Buat, DP, check-in, checkout, cancel')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <form class="flex flex-wrap gap-2" method="GET">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Cari kode/nama/HP..."
               class="px-3 py-2 border rounded w-72" />
        <select name="status" class="px-3 py-2 border rounded">
            <option value="">Semua status</option>
            @foreach (['draft','pending_dp','confirmed','checked_in','completed','cancelled','no_show'] as $st)
                <option value="{{ $st }}" @selected(($status ?? '')===$st)>{{ $st }}</option>
            @endforeach
        </select>
        <button class="px-3 py-2 rounded bg-gray-900 text-white">Filter</button>
    </form>

    <a href="{{ route('admin.reservations.create') }}"
       class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
        + Buat Reservasi
    </a>
</div>

<div class="overflow-x-auto">
<table class="w-full text-sm">
    <thead>
        <tr class="border-b bg-gray-50 text-left">
            <th class="p-2">Kode</th>
            <th class="p-2">Customer</th>
            <th class="p-2">Resource</th>
            <th class="p-2">Waktu</th>
            <th class="p-2">Menu</th>
            <th class="p-2">Total</th>
            <th class="p-2">DP</th>
            <th class="p-2">Status</th>
            <th class="p-2"></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $r)
            <tr class="border-b">
                <td class="p-2 font-medium">{{ $r->code }}</td>
                <td class="p-2">
                    <div>{{ $r->customer_name }}</div>
                    <div class="text-xs text-gray-600">{{ $r->customer_phone }}</div>
                </td>
                <td class="p-2">{{ $r->resource?->name }}</td>
                <td class="p-2">
                    <div>{{ $r->start_at->format('d M Y H:i') }}</div>
                    <div class="text-xs text-gray-600">{{ $r->end_at->format('d M Y H:i') }}</div>
                </td>
                <td class="p-2">{{ $r->menu_type }}</td>
                <td class="p-2">{{ number_format($r->grand_total) }}</td>
                <td class="p-2">{{ number_format($r->dp_amount) }}</td>
                <td class="p-2">{{ $r->status }}</td>
                <td class="p-2 text-right">
                    <a class="px-2 py-1 border rounded hover:bg-gray-50"
                       href="{{ route('admin.reservations.show', $r) }}">Detail</a>
                </td>
            </tr>
        @empty
            <tr><td class="p-4 text-gray-600" colspan="9">Belum ada reservasi.</td></tr>
        @endforelse
    </tbody>
</table>
</div>

<div class="mt-4">{{ $rows->links() }}</div>
@endsection