<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuffetPackage;
use App\Models\BuffetPackageItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuffetPackageController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $rows = BuffetPackage::query()
            ->when($q !== '', fn($qq) => $qq->where('name','like',"%$q%"))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.admin.buffet_packages.index', compact('rows','q'));
    }

    public function create()
    {
        return view('dashboard.admin.buffet_packages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:160'],
            'pricing_type' => ['required','in:per_pax,per_event'],
            'price' => ['required','integer','min:0'],
            'min_pax' => ['nullable','integer','min:1'],
            'is_active' => ['nullable'],
            'notes' => ['nullable','string'],
        ]);

        $data['is_active'] = $request->has('is_active');

        $pkg = BuffetPackage::create($data);

        return redirect()->route('admin.buffet_packages.edit', $pkg)
            ->with('success','Paket buffet dibuat. Silakan isi item paketnya.');
    }

    public function edit(BuffetPackage $buffetPackage)
    {
        $buffetPackage->load('items.product');

        $products = Product::where('is_active', true)->orderBy('name')->get(['id','name','price']);

        return view('dashboard.admin.buffet_packages.edit', [
            'pkg' => $buffetPackage,
            'products' => $products,
        ]);
    }

    public function update(Request $request, BuffetPackage $buffetPackage)
    {
        $data = $request->validate([
            'name' => ['required','string','max:160'],
            'pricing_type' => ['required','in:per_pax,per_event'],
            'price' => ['required','integer','min:0'],
            'min_pax' => ['nullable','integer','min:1'],
            'is_active' => ['nullable'],
            'notes' => ['nullable','string'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $buffetPackage->update($data);

        return back()->with('success','Paket buffet diupdate.');
    }

    public function destroy(BuffetPackage $buffetPackage)
    {
        $buffetPackage->delete();
        return redirect()->route('admin.buffet_packages.index')->with('success','Paket buffet dihapus.');
    }

    // add item
    public function addItem(Request $request, BuffetPackage $buffetPackage)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'qty' => ['required','integer','min:1'],
            'note' => ['nullable','string','max:180'],
        ]);

        BuffetPackageItem::updateOrCreate(
            ['buffet_package_id' => $buffetPackage->id, 'product_id' => $data['product_id']],
            ['qty' => $data['qty'], 'note' => $data['note'] ?? null]
        );

        return back()->with('success','Item paket ditambahkan/diupdate.');
    }

    public function removeItem(BuffetPackage $buffetPackage, BuffetPackageItem $item)
    {
        if ((int)$item->buffet_package_id !== (int)$buffetPackage->id) {
            abort(404);
        }
        $item->delete();
        return back()->with('success','Item paket dihapus.');
    }
}