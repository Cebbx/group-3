<?php
require __DIR__ . '/../vendor/autoload.php';

$composerLock = json_decode(file_get_contents(__DIR__ . '/../composer.lock'), true);
$packages = array_merge($composerLock['packages'] ?? [], $composerLock['packages-dev'] ?? []);

$interested = ['filament/filament', 'livewire/livewire', 'livewire/flux', 'laravel/framework', 'laravel/boost'];

echo "Installed Versions:\n";
foreach ($packages as $pkg) {
    if (in_array($pkg['name'], $interested)) {
        echo "  - {$pkg['name']}: {$pkg['version']}\n";
    }
}
