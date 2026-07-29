<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'admin@example.com')->first();
if ($user) {
    $user->role = 'admin';
    $user->save();
    echo "Successfully updated user '{$user->email}' role to 'admin'!\n";
} else {
    echo "User 'admin@example.com' not found. Creating a new admin user...\n";
    $admin = App\Models\User::create([
        'name' => 'System Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);
    echo "Successfully created Admin account: admin@example.com with password: 'password'\n";
}
