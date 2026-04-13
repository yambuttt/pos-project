<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReservationResource;
use Illuminate\Http\Request;

class ReservationResourceController extends Controller
{
    public function index(Request $request)
    {
        $type = trim((string) $request->get('type'));
        $q = trim((string) $request->get('q'));

        $rows = ReservationResource::query()
            ->when($type !== '', fn ($qq) => $qq->where('type', $type))
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%");
            })
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.admin.reservation_resources.index', compact('rows', 'type', 'q'));
    }

    public function create()
    {
        return view('dashboard.admin.reservation_resources.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // normalize: hourly_rate / flat_rate boleh null kalau 0
        $data['hourly_rate'] = isset($data['hourly_rate']) && $data['hourly_rate'] > 0 ? $data['hourly_rate'] : null;
        $data['flat_rate']   = isset($data['flat_rate']) && $data['flat_rate'] > 0 ? $data['flat_rate'] : null;

        ReservationResource::create($data);

        return redirect()
            ->route('admin.reservation_resources.index')
            ->with('success', 'Resource reservasi berhasil dibuat.');
    }

    public function edit(ReservationResource $resource)
    {
        return view('dashboard.admin.reservation_resources.edit', compact('resource'));
    }

    public function update(Request $request, ReservationResource $resource)
    {
        $data = $this->validated($request);

        $data['hourly_rate'] = isset($data['hourly_rate']) && $data['hourly_rate'] > 0 ? $data['hourly_rate'] : null;
        $data['flat_rate']   = isset($data['flat_rate']) && $data['flat_rate'] > 0 ? $data['flat_rate'] : null;

        $resource->update($data);

        return redirect()
            ->route('admin.reservation_resources.index')
            ->with('success', 'Resource reservasi berhasil diupdate.');
    }

    public function destroy(ReservationResource $resource)
    {
        // kalau sudah dipakai reservasi, jangan hapus biar aman
        $inUse = \App\Models\Reservation::where('reservation_resource_id', $resource->id)->exists();
        if ($inUse) {
            return back()->withErrors(['delete' => 'Resource sudah digunakan oleh reservasi. Nonaktifkan saja.']);
        }

        $resource->delete();

        return redirect()
            ->route('admin.reservation_resources.index')
            ->with('success', 'Resource reservasi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:TABLE,ROOM,HALL'],
            'name' => ['required', 'string', 'max:120'],
            'capacity' => ['required', 'integer', 'min:1'],

            // optional pricing
            'hourly_rate' => ['nullable', 'integer', 'min:0'],
            'flat_rate' => ['nullable', 'integer', 'min:0'],

            'min_duration_minutes' => ['required', 'integer', 'min:15'],
            'buffer_minutes' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}