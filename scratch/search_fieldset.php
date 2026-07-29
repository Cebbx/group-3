<?php
$classMap = require __DIR__ . '/../vendor/composer/autoload_classmap.php';

echo "Classes containing Fieldset:\n";
foreach ($classMap as $class => $file) {
    if (str_contains($class, 'Fieldset')) {
        echo "  - $class in $file\n";
    }
}
