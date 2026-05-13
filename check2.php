<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sales = App\Models\Sale::whereDate('created_at', now()->toDateString())->get();
foreach($sales as $sale) {
    echo "ID: {$sale->id} | User: {$sale->user_id} | Shift: {$sale->sale_shift_id} | Amount: {$sale->total_amount}\n";
}
$shifts = App\Models\SaleShift::where('status', 'open')->get();
foreach($shifts as $shift) {
    echo "SHIFT ID: {$shift->id} | User: {$shift->user_id}\n";
}
