<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (env('APP_TYPE') === 'toko') {
            return view('toko.auth.login');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $isTokoApp = env('APP_TYPE') === 'toko';

            if ($isTokoApp && $user->business_type !== 'toko') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akses ditolak. Akun ini bukan karyawan Toko.'])->onlyInput('email');
            }
            
            if (!$isTokoApp && $user->business_type === 'toko') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akses ditolak. Akun ini khusus karyawan Toko.'])->onlyInput('email');
            }

            $request->session()->regenerate();
            $role = $user->role;

            if ($isTokoApp) {
                if ($role === 'admin') {
                    return redirect()->route('toko.admin.dashboard');
                }
                return redirect()->route('toko.kasir.dashboard');
            }

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($role === 'kitchen') {
                return redirect()->route('kitchen.dashboard');
            }

            if ($role === 'pegawai') {
                return redirect()->route('pegawai.dashboard');
            }

            // default kasir
            return redirect()->route('kasir.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
