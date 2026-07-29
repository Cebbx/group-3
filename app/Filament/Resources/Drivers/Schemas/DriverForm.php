<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('license_number')
                    ->unique('drivers', 'license_number', ignoreRecord: true)
                    ->required(),
                TextInput::make('contact_number')
                    ->required(),
                Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'on_trip' => 'On Trip',
                        'off_duty' => 'Off Duty',
                    ])
                    ->required()
                    ->default('available'),
            ]);
    }
}
