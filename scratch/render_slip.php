<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\PrintController;

use App\Models\WithdrawalSlip;

$controller = new PrintController();
foreach (WithdrawalSlip::all() as $slip) {
    try {
        $response = $controller->printSlip($slip->id);
        echo "Successfully rendered slip {$slip->id} (Slip: {$slip->slip_number})!\n";
    } catch (\Throwable $e) {
        echo "Exception rendering slip {$slip->id} (Slip: {$slip->slip_number}):\n";
        echo "Message: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
        echo "----------------------------------------\n";
    }
}
