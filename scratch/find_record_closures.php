<?php
function checkFiles($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            if (str_contains($content, '$record')) {
                $relative = str_replace(__DIR__ . '/../', '', $file->getPathname());
                echo "File: $relative\n";
                // Let's print lines containing $record
                $lines = explode("\n", $content);
                foreach ($lines as $num => $line) {
                    if (str_contains($line, '$record')) {
                        echo "  Line " . ($num + 1) . ": " . trim($line) . "\n";
                    }
                }
            }
        }
    }
}

checkFiles(__DIR__ . '/../app/Filament');
