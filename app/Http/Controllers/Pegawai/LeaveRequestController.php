<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $items = LeaveRequest::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('dashboard.pegawai.leave.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:cuti,sakit'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:500'],

            // surat dokter wajib untuk sakit
            'doctor_note' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($data['type'] === 'sakit' && !$request->hasFile('doctor_note')) {
            return back()->with('error', 'Untuk Sakit wajib melampirkan foto surat dokter.');
        }

        $path = null;

        if ($request->hasFile('doctor_note')) {
            $file = $request->file('doctor_note');

            $dir = 'public/leave_requests/' . auth()->id() . '/' . now()->toDateString();
            $name = 'doctor_' . now()->format('His') . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

            // simpan ke disk "local" (mengikuti pola project kamu yang aman untuk streaming via controller)
            $path = $file->storeAs($dir, $name, 'local');
        }

        LeaveRequest::create([
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'] ?? null,
            'doctor_note_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('pegawai.leave.index')->with('ok', 'Pengajuan berhasil dikirim dan menunggu persetujuan admin.');
    }
}