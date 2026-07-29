<?php
// Set execution limit to 5 seconds to fail fast for diagnostics
ini_set('max_execution_time', 30);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the HTTP kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Sending GET request to /admin...\n";
try {
    $request = Illuminate\Http\Request::create('/admin/login', 'GET');
    $response = $kernel->handle($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content excerpt: " . substr($response->getContent(), 0, 500) . "\n";
} catch (\Throwable $e) {
    echo "Exception caught:\n";
    echo "Class: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
