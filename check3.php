<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sales = App\Models\Sale::latest()->take(5)->get();
foreach($sales as $sale) {
    echo "ID: {$sale->id} | User: {$sale->user_id} | Shift: {$sale->sale_shift_id} | Created: {$sale->created_at} | Amount: {$sale->total_amount}\n";
}
