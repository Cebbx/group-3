<?php
function findActionImports($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            if (str_contains($content, 'use Filament\Actions\\')) {
                $relative = str_replace(__DIR__ . '/../', '', $file->getPathname());
                echo "File: $relative\n";
                preg_match_all('/use Filament\\\\Actions\\\\.+;/', $content, $matches);
                foreach ($matches[0] as $match) {
                    echo "  - $match\n";
                }
            }
        }
    }
}

findActionImports(__DIR__ . '/../app/Filament');
