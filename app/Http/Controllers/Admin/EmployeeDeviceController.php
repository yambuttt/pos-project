<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDevice;
use App\Models\User;

class EmployeeDeviceController extends Controller
{
    public function index()
    {
        $pending = EmployeeDevice::whereNull('approved_at')->whereNull('revoked_at')->latest()->get();
        $approved = EmployeeDevice::whereNotNull('approved_at')->whereNull('revoked_at')->latest()->get();
        $revoked = EmployeeDevice::whereNotNull('revoked_at')->latest()->limit(50)->get();

        return view('dashboard.admin.attendance_qr.devices', compact('pending','approved','revoked'));
    }

    public function approve(EmployeeDevice $device)
    {
        $device->approved_at = now();
        $device->approved_by = auth()->id();
        $device->revoked_at = null;
        $device->save();

        return back()->with('ok', 'Device di-approve.');
    }

    public function revoke(EmployeeDevice $device)
    {
        $device->revoked_at = now();
        $device->save();

        return back()->with('ok', 'Device dicabut.');
    }

    // reset semua device pegawai (kalau ganti hp)
    public function resetUserDevices(User $user)
    {
        EmployeeDevice::where('user_id', $user->id)->update(['revoked_at' => now()]);
        return back()->with('ok', 'Semua device pegawai di-reset (dicabut).');
    }
}