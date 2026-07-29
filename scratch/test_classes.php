<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$classes = [
    'Filament\Actions\Action',
    'Filament\Actions\EditAction',
    'Filament\Tables\Actions\Action',
    'Filament\Tables\Actions\EditAction',
    'Filament\Tables\Actions\ViewAction',
    'Filament\Tables\Actions\DeleteBulkAction',
    'Filament\Tables\Actions\BulkActionGroup',
    'Filament\Forms\Components\Fieldset',
];

echo "Checking classes:\n";
foreach ($classes as $class) {
    echo "  - $class: " . (class_exists($class) ? "EXISTS" : "DOES NOT EXIST") . "\n";
}
