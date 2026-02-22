<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiningTableController extends Controller
{
    public function index()
    {
        $tables = DiningTable::orderBy('name')->paginate(12);
        return view('dashboard.admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('dashboard.admin.tables.create');
    }

    public function regenerateQr(DiningTable $table)
{
    $table->update([
        'qr_token' => Str::random(32),
    ]);

    return back()->with('success', 'QR token meja berhasil diganti.');
}

public function store(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:80', 'unique:dining_tables,name'],
        'is_active' => ['nullable'],
    ]);

    $data['is_active'] = $request->boolean('is_active', true);
    $data['qr_token'] = Str::random(32);

    DiningTable::create($data);

    return redirect()->route('admin.tables.index')
        ->with('success', 'Meja berhasil ditambahkan.');
}
    public function edit(DiningTable $table)
    {
        return view('dashboard.admin.tables.edit', compact('table'));
    }

    public function update(Request $request, DiningTable $table)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:dining_tables,name,' . $table->id],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $table->update($data);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil diupdate.');
    }

    public function destroy(DiningTable $table)
    {
        $table->delete();
        return back()->with('success', 'Meja dihapus.');
    }
}