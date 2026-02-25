<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CashierController extends Controller
{
    public function index()
    {
        // List kasir + siapa yang membuat
        $cashiers = User::query()
            ->whereIn('role', ['kasir', 'kitchen', 'pegawai'])
            ->with(['creator'])
            ->latest()
            ->paginate(10);

        return view('dashboard.admin.users.index', compact('cashiers'));
    }

    public function create()
    {
        return view('dashboard.admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'in:kasir,kitchen,pegawai'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.cashiers.index')
            ->with('success', 'Akun kasir berhasil dibuat.');
    }
}
