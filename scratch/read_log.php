<?php
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($logPath)) {
    echo "Log file not found at $logPath\n";
    exit(1);
}

$log = file_get_contents($logPath);
preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local\.ERROR:.+?(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local\.ERROR:|$)/s', $log, $matches);

if (!empty($matches[0])) {
    $count = count($matches[0]);
    echo "Total errors found: $count\n\n";
    $recent = array_slice($matches[0], -3);
    foreach ($recent as $index => $err) {
        echo "=== ERROR " . ($count - 3 + $index + 1) . " ===\n";
        echo substr($err, 0, 1000) . "\n\n";
    }
} else {
    echo "No errors found.\n";
}
