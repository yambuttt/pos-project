<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $items = GalleryItem::orderBy('sort_order')->get();
        return view('dashboard.admin.gallery.index', compact('items'));
    }

    public function create()
    {
        return view('dashboard.admin.gallery.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:100',
            'image' => 'required|image|max:5120',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gallery', 'public');
            $data['image_path'] = $path;
        }

        $data['is_active'] = $request->has('is_active');

        GalleryItem::create($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Gambar galeri berhasil ditambahkan.');
    }

    public function edit(GalleryItem $gallery)
    {
        return view('dashboard.admin.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, GalleryItem $gallery)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:5120',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($gallery->image_path) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            $path = $request->file('image')->store('gallery', 'public');
            $data['image_path'] = $path;
        }

        $data['is_active'] = $request->has('is_active');

        $gallery->update($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Gambar galeri berhasil diperbarui.');
    }

    public function destroy(GalleryItem $gallery)
    {
        if ($gallery->image_path) {
            Storage::disk('public')->delete($gallery->image_path);
        }
        $gallery->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Gambar galeri berhasil dihapus.');
    }
}
