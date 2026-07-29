<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all();
echo "Users in database:\n";
foreach ($users as $user) {
    echo "  - Email: {$user->email} | Role: {$user->role} | Name: {$user->name}\n";
}
