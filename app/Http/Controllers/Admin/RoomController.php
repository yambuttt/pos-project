<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::query()->latest()->paginate(10);
        return view('dashboard.admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('dashboard.admin.rooms.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:190'],
            'location_label' => ['nullable','string','max:190'],
            'capacity_min' => ['required','integer','min:1'],
            'capacity_max' => ['required','integer','gte:capacity_min'],
            'is_active' => ['nullable','boolean'],
            'note' => ['nullable','string'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        Room::create($data);

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil dibuat.');
    }

    public function edit(Room $room)
    {
        return view('dashboard.admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'name' => ['required','string','max:190'],
            'location_label' => ['nullable','string','max:190'],
            'capacity_min' => ['required','integer','min:1'],
            'capacity_max' => ['required','integer','gte:capacity_min'],
            'is_active' => ['nullable','boolean'],
            'note' => ['nullable','string'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $room->update($data);

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil diupdate.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with('success', 'Ruangan berhasil dihapus.');
    }
}
