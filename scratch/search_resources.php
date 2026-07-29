<?php
function scanDirectory($dir) {
    $results = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            // Check for queries that look like static calls: ModelName::where, ModelName::all, ModelName::get, etc.
            // We search for capitalized names followed by double colons and eloquent query methods
            preg_match_all('/([A-Z][a-zA-Z0-9_]*)::(where|all|get|count|first|find|query)/', $content, $matches, PREG_OFFSET_CAPTURE);
            
            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) {
                    $results[] = [
                        'file' => str_replace(__DIR__ . '/../', '', $file->getPathname()),
                        'match' => $match[0],
                        'line' => substr_count(substr($content, 0, $match[1]), "\n") + 1
                    ];
                }
            }
        }
    }
    return $results;
}

$filamentDir = __DIR__ . '/../app/Filament';
echo "Scanning $filamentDir for database queries...\n";
$matches = scanDirectory($filamentDir);
echo "Found " . count($matches) . " database queries:\n";
foreach ($matches as $m) {
    echo "  - {$m['file']}:L{$m['line']} -> {$m['match']}\n";
}
