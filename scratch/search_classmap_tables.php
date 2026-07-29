<?php
$classMap = require __DIR__ . '/../vendor/composer/autoload_classmap.php';

echo "Classes containing Filament\\Tables:\n";
$count = 0;
foreach ($classMap as $class => $file) {
    if (str_contains($class, 'Filament\\Tables\\')) {
        if (++$count > 100) break;
        echo "  - $class\n";
    }
}
