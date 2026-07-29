<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('plate_number')
                    ->unique('vehicles', 'plate_number', ignoreRecord: true)
                    ->required(),
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                Select::make('type')
                    ->options([
                        'SUV' => 'SUV',
                        'Van' => 'Van',
                        'Jeep' => 'Jeep',
                        'Multicab' => 'Multicab',
                    ])
                    ->required()
                    ->default('SUV'),
                Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'maintenance' => 'Under Maintenance',
                    ])
                    ->required()
                    ->default('available'),
            ]);
    }
}
