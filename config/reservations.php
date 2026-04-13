<?php

return [
    // untuk REGULAR reservation: effective minimum = min_stock * multiplier
    'min_stock_multiplier' => (int) env('RESERVATION_MIN_STOCK_MULTIPLIER', 2),

    // DP ratio default 50%
    'dp_ratio' => (float) env('RESERVATION_DP_RATIO', 0.5),
];