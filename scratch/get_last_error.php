<?php
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($logPath)) {
    echo "Log file not found at $logPath\n";
    exit(1);
}

$log = file_get_contents($logPath);
preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local\.ERROR:.+?(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local\.ERROR:|$)/s', $log, $matches);

if (!empty($matches[0])) {
    $lastError = end($matches[0]);
    file_put_contents(__DIR__ . '/latest_error.txt', $lastError);
    echo "Successfully wrote last error to scratch/latest_error.txt (length: " . strlen($lastError) . " bytes)\n";
} else {
    echo "No matching ERROR block found in log file.\n";
}
