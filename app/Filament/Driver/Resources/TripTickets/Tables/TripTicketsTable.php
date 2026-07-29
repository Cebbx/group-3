<?php

namespace App\Filament\Driver\Resources\TripTickets\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Trip ID')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('vehicleRequest.employee_name')
                    ->label('Requested By / Passenger')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicleRequest.destination')
                    ->label('Destination')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicleRequest.date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('vehicleRequest.time')
                    ->label('Time')
                    ->time('h:i A')
                    ->sortable(),
                TextColumn::make('vehicle')
                    ->label('Vehicle')
                    ->formatStateUsing(function ($state) {
                        $vehicle = \App\Models\Vehicle::where('plate_number', $state)->first();
                        return $vehicle ? "{$vehicle->brand} - {$vehicle->plate_number}" : $state;
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'active' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('acknowledge')
                    ->label('Acknowledge')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending' && $record->vehicleRequest?->document !== null)
                    ->action(function ($record) {
                        $record->update(['status' => 'active']);
                        $record->driver?->update(['status' => 'on_trip']);
                        $record->vehicleRequest?->update(['status' => 'approved']);
                    }),
                Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'active')
                    ->action(function ($record) {
                        $record->update(['status' => 'completed']);
                        $record->driver?->update(['status' => 'available']);
                        $record->vehicleRequest?->update(['status' => 'completed']);
                    }),
            ])
            ->toolbarActions([
                // Drivers don't need bulk delete tools
            ]);
    }
}

