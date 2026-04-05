<?php

return [
    // lokasi restoran (isi dari .env)
    'restaurant_lat' => (float) env('ATTENDANCE_RESTAURANT_LAT', 0),
    'restaurant_lng' => (float) env('ATTENDANCE_RESTAURANT_LNG', 0),

    // radius meter (mis. 120m)
    'radius_m' => (int) env('ATTENDANCE_RADIUS_M', 120),

    // QR berlaku berapa detik (mis. 60 detik)
    'qr_ttl_seconds' => (int) env('ATTENDANCE_QR_TTL_SECONDS', 60),
];