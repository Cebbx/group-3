<?php
function lintDirectory($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $failed = [];
    $total = 0;
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $total++;
            $filepath = $file->getPathname();
            $output = [];
            $returnVar = 0;
            exec("php -l " . escapeshellarg($filepath) . " 2>&1", $output, $returnVar);
            if ($returnVar !== 0) {
                $failed[] = [
                    'file' => $filepath,
                    'error' => implode("\n", $output)
                ];
            }
        }
    }
    return ['total' => $total, 'failed' => $failed];
}

$appDir = __DIR__ . '/../app';
echo "Lifting-off PHP linter for: $appDir\n";
$result = lintDirectory($appDir);
echo "Total PHP files linted: " . $result['total'] . "\n";
echo "Total failures: " . count($result['failed']) . "\n\n";

foreach ($result['failed'] as $f) {
    echo "FAILED: " . $f['file'] . "\n";
    echo $f['error'] . "\n";
    echo "----------------------------------------\n";
}
