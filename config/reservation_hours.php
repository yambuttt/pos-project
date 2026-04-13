<?php

return [
    // Jam operasional umum (bisa kamu upgrade per hari nanti)
    'open' => env('RESERVATION_OPEN_TIME', '10:00'),
    'close' => env('RESERVATION_CLOSE_TIME', '22:00'),

    // Step/slot menit (opsional) - kalau mau booking per 30 menit
    'slot_minutes' => (int) env('RESERVATION_SLOT_MINUTES', 0), // 0 = bebas
];