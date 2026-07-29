<?php
function searchCode($dir, $pattern) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            if (str_contains($content, $pattern)) {
                $relative = str_replace(__DIR__ . '/../', '', $file->getPathname());
                echo "File: $relative\n";
                $lines = explode("\n", $content);
                foreach ($lines as $num => $line) {
                    if (str_contains($line, $pattern)) {
                        echo "  Line " . ($num + 1) . ": " . trim($line) . "\n";
                    }
                }
            }
        }
    }
}

searchCode(__DIR__ . '/../app', 'requested_items');
searchCode(__DIR__ . '/../resources', 'requested_items');
searchCode(__DIR__ . '/../database', 'requested_items');
