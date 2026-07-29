<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WithdrawalSlip;
use App\Models\TripTicket;
use App\Http\Controllers\PrintController;

echo "Creating an array-based withdrawal slip for testing...\n";
// Find a trip ticket to link it to
$ticket = TripTicket::latest()->first();
if (!$ticket) {
    echo "No trip tickets found to link test slip.\n";
    exit(1);
}

// Clean up previous test slips with this number
WithdrawalSlip::where('slip_number', 'WS-TEST-ARRAY')->delete();

$testSlip = WithdrawalSlip::create([
    'slip_number' => 'WS-TEST-ARRAY',
    'trip_ticket_id' => $ticket->id,
    'purpose' => 'Test Array Printing',
    'requested_items' => [
        'diesel' => 15,
        'gasoline_regular' => 5,
        'grease_atf' => 2,
    ],
    'amount' => 1250.00,
    'status' => 'approved',
]);

echo "Created test slip ID: {$testSlip->id}\n\n";

$controller = new PrintController();
$slips = WithdrawalSlip::all();
echo "Rendering total of " . count($slips) . " slips...\n";

$failures = 0;
foreach ($slips as $slip) {
    try {
        $response = $controller->printSlip($slip->id);
        $type = gettype($slip->requested_items);
        echo "  - Slip {$slip->id} ({$slip->slip_number}, type: {$type}): SUCCESS\n";
    } catch (\Throwable $e) {
        $failures++;
        echo "  - Slip {$slip->id} ({$slip->slip_number}): FAILED!\n";
        echo "    Message: " . $e->getMessage() . "\n";
        echo "    File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    }
}

// Clean up
$testSlip->delete();

echo "\nCompleted. Failures: $failures\n";
if ($failures > 0) {
    exit(1);
}
