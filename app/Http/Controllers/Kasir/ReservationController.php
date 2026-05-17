<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Services\ReservationInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $activeShift = \App\Models\SaleShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return redirect()->route('kasir.dashboard')->with('error', 'Anda harus memulai shift terlebih dahulu.');
        }

        $q = trim((string) $request->get('q'));
        $date = $request->get('date') ?: now()->toDateString();

        $rows = Reservation::query()
            ->with('resource')
            ->whereDate('start_at', $date)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('code', 'like', "%$q%")
                    ->orWhere('customer_name', 'like', "%$q%")
                    ->orWhere('customer_phone', 'like', "%$q%");
            })
            ->orderBy('start_at')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.kasir.reservations.index', compact('rows', 'q', 'date'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['resource', 'items', 'payments', 'locks.rawMaterial']);
        $remaining = max(0, (int) $reservation->grand_total - (int) $reservation->paid_amount);

        return view('dashboard.kasir.reservations.show', compact('reservation', 'remaining'));
    }

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return back()->withErrors(['checkin' => 'Hanya reservasi CONFIRMED yang bisa check-in.']);
        }

        $reservation->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        return back()->with('success', 'Check-in berhasil.');
    }

    public function checkout(
        Reservation $reservation,
        Request $request,
        ReservationInventoryService $inv
    ) {
        $data = $request->validate([
            'method' => ['required', 'in:CASH,TRANSFER'],
            'amount' => ['required', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string'],
            'payment_proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        return DB::transaction(function () use ($reservation, $data, $inv, $request) {
            $activeShift = \App\Models\SaleShift::where('user_id', auth()->id())
                ->where('status', 'open')
                ->first();

            if (!$activeShift) {
                throw new \RuntimeException('Anda harus memulai shift terlebih dahulu sebelum melakukan transaksi.');
            }

            if ($reservation->status !== 'checked_in') {
                throw new \RuntimeException('Hanya reservasi CHECKED_IN yang bisa checkout.');
            }

            $remaining = max(0, (int) $reservation->grand_total - (int) $reservation->paid_amount);
            if ($remaining <= 0) {
                throw new \RuntimeException('Tagihan sudah lunas.');
            }

            if ((int) $data['amount'] !== $remaining) {
                throw new \RuntimeException("Jumlah pembayaran harus tepat Rp {$remaining}.");
            }

            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = 'proof_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $proofPath = $file->storeAs('proofs', $filename, 'public');
                $proofPath = '/storage/' . $proofPath;
            }

            ReservationPayment::create([
                'reservation_id' => $reservation->id,
                'type' => 'FINAL',
                'amount' => (int) $data['amount'],
                'method' => $data['method'],
                'status' => 'paid',
                'reference' => $data['reference'] ?? null,
                'note' => $data['note'] ?? null,
                'payment_proof' => $proofPath,
                'paid_at' => now(),
            ]);

            $reservation->update([
                'paid_amount' => (int) $reservation->paid_amount + (int) $data['amount'],
            ]);

            $inv->consumeOnCheckout($reservation->fresh(), Auth::id());

            $reservation->update([
                'status' => 'completed',
                'checked_out_at' => now(),
            ]);

            return back()->with('success', "Checkout {$data['method']} selesai. Reservasi COMPLETED.");
        });
    }

    public function status(\App\Models\Reservation $reservation)
    {
        // minimal data yang dibutuhkan untuk polling
        return response()->json([
            'status' => $reservation->status,
            'paid_amount' => (int) $reservation->paid_amount,
            'grand_total' => (int) $reservation->grand_total,
            'midtrans_transaction_status' => $reservation->midtrans_transaction_status,
        ]);
    }
}