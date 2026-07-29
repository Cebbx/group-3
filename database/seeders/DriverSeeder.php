<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            [
                'name' => 'Joel Tumamao',
                'license_number' => 'N01-27-556983',
                'contact_number' => '09275569838',
                'status' => 'available',
            ],
            [
                'name' => 'Lucio Collado',
                'license_number' => 'N01-53-113537',
                'contact_number' => '09531135376',
                'status' => 'available',
            ],
            [
                'name' => 'Roderick Rubino',
                'license_number' => 'N01-35-351267',
                'contact_number' => '09353512671',
                'status' => 'available',
            ],
            [
                'name' => 'Rey Tolentino',
                'license_number' => 'N01-17-896348',
                'contact_number' => '09178963483',
                'status' => 'available',
            ],
            [
                'name' => 'Norman Cristobal',
                'license_number' => 'N01-05-075043',
                'contact_number' => '09050750435',
                'status' => 'available',
            ],
        ];

        // Clean table first to avoid any duplicate keys or lingering names
        // Driver::query()->truncate();

        // // Also delete old driver users to clean database
        // User::where('role', 'driver')->delete();

        foreach ($drivers as $driverData) {
            Driver::create($driverData);
            
            // Pre-create the user account for the driver
            User::create([
                'name' => $driverData['name'],
                'email' => $driverData['license_number'],
                'password' => Hash::make($driverData['license_number']),
                'role' => 'driver',
            ]);
        }
    }
}
