<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Let's search class map or write a script to find where Action or Fieldset are defined in vendor/filament
$classMap = require __DIR__ . '/../vendor/composer/autoload_classmap.php';

echo "Total classes in classmap: " . count($classMap) . "\n";
echo "Searching for Action...\n";
foreach ($classMap as $class => $file) {
    if (str_contains($class, 'Filament\\') && (str_contains($class, 'Action') || str_contains($class, 'Fieldset'))) {
        // limit output to first 50
        static $count = 0;
        if (++$count > 100) break;
        echo "  - $class\n";
    }
}
