<?php

namespace App\Filament\Resources\TripTickets\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')
                    ->searchable(),
                TextColumn::make('vehicleRequest.request_number')
                    ->label('Request Number')
                    ->searchable(),
                TextColumn::make('vehicleRequest.employee_name')
                    ->label('Requested By')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicleRequest.destination')
                    ->label('Destination')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver.name')
                    ->searchable(),
                TextColumn::make('vehicle')
                    ->label('Vehicle')
                    ->formatStateUsing(function ($state) {
                        $vehicle = \App\Models\Vehicle::where('plate_number', $state)->first();
                        return $vehicle ? "{$vehicle->brand} - {$vehicle->plate_number}" : $state;
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'active' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'active' => 'On Trip',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('start_trip')
                    ->label('Start Trip')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending' && $record->vehicleRequest?->document !== null)
                    ->action(function ($record) {
                        $record->update(['status' => 'active']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Trip Started')
                            ->body("Trip {$record->ticket_number} has started! Driver is now On Trip.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->button(),
                Action::make('complete')
                    ->label('Complete Trip')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'active')
                    ->action(function ($record) {
                        $record->update(['status' => 'completed']);
                    })
                    ->requiresConfirmation()
                    ->button(),
                Action::make('cancel_trip')
                    ->label('Cancel Trip')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'active']))
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Trip Cancelled')
                            ->body("Trip {$record->ticket_number} has been cancelled.")
                            ->danger()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->button(),
                Action::make('resendSms')
                    ->label('Resend SMS')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->sendSmsNotification();
                        \Filament\Notifications\Notification::make()
                            ->title('SMS Sent')
                            ->body("SMS notification resent to driver {$record->driver?->name}.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->button(),
                Action::make('create_slip')
                    ->label('Create Slip')
                    ->icon('heroicon-o-document-plus')
                    ->color('warning')
                    ->visible(fn ($record) => !$record->withdrawalSlips()->exists() && in_array($record->status, ['pending', 'active']))
                    ->url(fn ($record) => \App\Filament\Resources\WithdrawalSlips\WithdrawalSlipResource::getUrl('create', [
                        'trip_ticket_id' => $record->id,
                    ])),
                Action::make('print')
                    ->label('Print Ticket')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($record) => route('trip-tickets.print', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
