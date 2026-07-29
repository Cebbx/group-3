<?php

namespace App\Filament\Resources\WithdrawalSlips\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WithdrawalSlipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slip_number')
                    ->default(function () {
                        $lastRecord = \App\Models\WithdrawalSlip::latest('id')->first();
                        $nextId = $lastRecord ? ($lastRecord->id + 1) : 1;
                        return 'WS-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
                    })
                    ->unique('withdrawal_slips', 'slip_number', ignoreRecord: true)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Select::make('trip_ticket_id')
                    ->default(function () {
                        $tripId = request()->query('trip_ticket_id');
                        if ($tripId) {
                            return $tripId;
                        }
                        return \App\Models\TripTicket::whereDoesntHave('withdrawalSlips')
                            ->orderBy('created_at', 'desc')
                            ->value('id');
                    })
                    ->relationship('tripTicket', 'ticket_number', function ($query) {
                        $tripId = request()->query('trip_ticket_id');
                        return $query->with(['driver', 'vehicleRequest'])
                            ->when($tripId, fn ($q) => $q->orWhere('id', $tripId))
                            ->whereDoesntHave('withdrawalSlips')
                            ->orderBy('created_at', 'desc');
                    })
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $driverName = $record->driver?->name ?? 'No Driver';
                        $destination = $record->vehicleRequest?->destination ?? 'No Destination';
                        $dbVehicle = \App\Models\Vehicle::where('plate_number', $record->vehicle)->first();
                        $vehicleName = $dbVehicle ? $dbVehicle->brand : $record->vehicle;
                        
                        // Limit destination length for clean UI display
                        if (strlen($destination) > 40) {
                            $destination = substr($destination, 0, 37) . '...';
                        }
                        
                        return "{$record->ticket_number} - {$driverName} ({$vehicleName}) to {$destination}";
                    })
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $ticket = \App\Models\TripTicket::with(['vehicleRequest'])->find($state);
                            if ($ticket) {
                                $set('purpose', $ticket->vehicleRequest?->purpose ?? 'Official Business');
                            }
                        }
                    })
                    ->disabled(fn (string $operation) => $operation === 'edit' || request()->has('trip_ticket_id'))
                    ->dehydrated()
                    ->required(),
                \Filament\Forms\Components\Hidden::make('purpose')
                    ->default(function () {
                        $tripId = request()->query('trip_ticket_id');
                        if (!$tripId) {
                            $tripId = \App\Models\TripTicket::whereDoesntHave('withdrawalSlips')
                                ->orderBy('created_at', 'desc')
                                ->value('id');
                        }
                        if ($tripId) {
                            $ticket = \App\Models\TripTicket::with(['vehicleRequest'])->find($tripId);
                            return $ticket?->vehicleRequest?->purpose ?? 'Official Business';
                        }
                        return 'Official Business';
                    })
                    ->dehydrated(),
                \Filament\Schemas\Components\Fieldset::make('requested_items')
                    ->label('Requested Items (Fill in quantities as needed)')
                    ->statePath('requested_items')
                    ->schema([
                        TextInput::make('diesel')
                            ->label('Diesel (Liters)')
                            ->numeric(),
                        TextInput::make('gasoline_regular')
                            ->label('Gasoline Regular (Liters)')
                            ->numeric(),
                        TextInput::make('gasoline_premium')
                            ->label('Gasoline Premium (Liters)')
                            ->numeric(),
                        TextInput::make('lubricant_40')
                            ->label('Lubricant Oil 40 (Liters)')
                            ->numeric(),
                        TextInput::make('lubricant_30')
                            ->label('Lubricant Oil 30 (Liters)')
                            ->numeric(),
                        TextInput::make('brake_fluid')
                            ->label('Brake Fluid (Liters)')
                            ->numeric(),
                        TextInput::make('grease_atf')
                            ->label('2T / Grease / ATF (Liters)')
                            ->numeric(),
                        TextInput::make('gear_oil')
                            ->label('Gear Oil (Liters)')
                            ->numeric(),
                    ])
                    ->columns(2),
                \Filament\Forms\Components\Hidden::make('status')
                    ->default('approved')
                    ->dehydrated(),
            ]);
    }
}
