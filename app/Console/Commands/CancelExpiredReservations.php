<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class CancelExpiredReservations extends Command
{
    protected $signature = 'reservations:cancel-expired';
    protected $description = 'Cancel reservations pending DP that passed payment_expires_at';

    public function handle(): int
    {
        $count = Reservation::query()
            ->where('status', 'pending_dp')
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<', now())
            ->update(['status' => 'cancelled']);

        $this->info("Cancelled {$count} expired reservations.");
        return self::SUCCESS;
    }
}