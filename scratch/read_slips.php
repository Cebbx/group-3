<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\WithdrawalSlip::all() as $slip) {
    echo "ID: " . $slip->id . "\n";
    echo "Slip Number: " . $slip->slip_number . "\n";
    echo "Requested Items type: " . gettype($slip->requested_items) . "\n";
    echo "Requested Items: " . json_encode($slip->requested_items) . "\n";
    echo "--------------------------\n";
}
