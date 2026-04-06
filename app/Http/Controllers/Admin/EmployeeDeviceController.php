<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDevice;
use App\Models\User;

class EmployeeDeviceController extends Controller
{
    public function index()
    {
        $pending = EmployeeDevice::with('user')
            ->whereNull('approved_at')->whereNull('revoked_at')
            ->latest()->get();

        $approved = EmployeeDevice::with('user')
            ->whereNotNull('approved_at')->whereNull('revoked_at')
            ->latest()->get();

        $revoked = EmployeeDevice::with('user')
            ->whereNotNull('revoked_at')
            ->latest()->limit(50)->get();

        // Map: device_id => nomor device per user (urut dari yang pertama kali dibuat)
        $allUserIds = collect([$pending, $approved, $revoked])->flatten()->pluck('user_id')->unique()->values();
        $deviceNo = [];

        if ($allUserIds->isNotEmpty()) {
            $allDevices = EmployeeDevice::whereIn('user_id', $allUserIds)
                ->orderBy('created_at', 'asc')
                ->get(['id', 'user_id', 'created_at']);

            $counter = [];
            foreach ($allDevices as $d) {
                $uid = $d->user_id;
                $counter[$uid] = ($counter[$uid] ?? 0) + 1;
                $deviceNo[$d->id] = $counter[$uid];
            }
        }

        return view('dashboard.admin.attendance_qr.devices', compact('pending', 'approved', 'revoked', 'deviceNo'));
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

    public function rename(\Illuminate\Http\Request $request, EmployeeDevice $device)
    {
        $data = $request->validate([
            'device_name' => ['nullable', 'string', 'max:80'],
        ]);

        $device->device_name = $data['device_name'] ?: null;
        $device->save();

        return back()->with('ok', 'Nama device diperbarui.');
    }

    // reset semua device pegawai (kalau ganti hp)
    public function resetUserDevices(User $user)
    {
        EmployeeDevice::where('user_id', $user->id)->update(['revoked_at' => now()]);
        return back()->with('ok', 'Semua device pegawai di-reset (dicabut).');
    }


}