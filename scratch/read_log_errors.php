<?php
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($logPath)) {
    echo "Log file not found at $logPath\n";
    exit(1);
}

$log = file_get_contents($logPath);
preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local\.ERROR: ([\s\S]+?)(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local\.ERROR:|$)/', $log, $matches);

$summary = [];
foreach ($matches[1] as $match) {
    // extract first line of error message
    $lines = explode("\n", $match);
    $firstLine = trim($lines[0]);
    // group by first line
    if (!isset($summary[$firstLine])) {
        $summary[$firstLine] = 0;
    }
    $summary[$firstLine]++;
}

echo "Unique error messages and counts:\n";
arsort($summary);
foreach ($summary as $msg => $count) {
    echo "  - [$count times]: $msg\n";
}
