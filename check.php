<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sales = App\Models\Sale::latest()->take(5)->get();
foreach($sales as $sale) {
    echo "ID: {$sale->id} | Shift: {$sale->sale_shift_id} | Invoice: {$sale->invoice_no} | Total: {$sale->total_amount} | Status: {$sale->status} | Payment: {$sale->payment_status}\n";
}
