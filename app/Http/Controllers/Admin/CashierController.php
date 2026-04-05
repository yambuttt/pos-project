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
        $users = User::query()
            ->with(['creator'])
            ->whereIn('role', ['admin', 'kasir', 'kitchen', 'pegawai'])
            ->orderByRaw("CASE role WHEN 'admin' THEN 0 WHEN 'kasir' THEN 1 WHEN 'kitchen' THEN 2 WHEN 'pegawai' THEN 3 ELSE 9 END")
            ->latest()
            ->paginate(10);

        return view('dashboard.admin.users.index', compact('users'));
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

    public function edit(User $user)
    {
        return view('dashboard.admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,kasir,kitchen,pegawai'],
            // password optional (boleh kosong)
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return redirect()
            ->route('admin.cashiers.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // cegah hapus akun sendiri biar tidak lockout
        if (Auth::id() === $user->id) {
            return back()->with('success', 'Tidak bisa menghapus akun yang sedang login.');
        }

        $user->delete();

        return redirect()
            ->route('admin.cashiers.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
