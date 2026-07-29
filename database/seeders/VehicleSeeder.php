<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'brand' => 'FORTUNER',
                'model' => 'Fortuner',
                'plate_number' => 'SBA1749',
                'type' => 'SUV',
                'status' => 'available',
            ],
            [
                'brand' => 'HIACE VAN',
                'model' => 'Hiace Van',
                'plate_number' => 'SBA3790',
                'type' => 'Van',
                'status' => 'available',
            ],
            [
                'brand' => 'PTIA JEEP',
                'model' => 'Patia Jeep',
                'plate_number' => 'SDV868',
                'type' => 'Jeep',
                'status' => 'available',
            ],
            [
                'brand' => 'MULTICAB',
                'model' => 'Multicab',
                'plate_number' => 'NAJI987',
                'type' => 'Multicab',
                'status' => 'available',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::updateOrCreate(
                ['plate_number' => $vehicle['plate_number']],
                $vehicle
            );
        }
    }
}
