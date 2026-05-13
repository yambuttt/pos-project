<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = App\Models\Sale::count();
    echo "COUNT: $count\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
