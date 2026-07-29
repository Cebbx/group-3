<?php

namespace App\Filament\Driver\Resources\TripTickets\Pages;

use App\Filament\Driver\Resources\TripTickets\TripTicketResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTripTicket extends ViewRecord
{
    protected static string $resource = TripTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('acknowledge')
                ->label('Acknowledge Trip')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->status === 'pending' && $record->vehicleRequest?->document !== null)
                ->action(function ($record) {
                    $record->update(['status' => 'active']);
                    $record->driver?->update(['status' => 'on_trip']);
                    $record->vehicleRequest?->update(['status' => 'approved']);
                    
                    $this->fillForm();
                }),
            Action::make('complete')
                ->label('Complete Trip')
                ->icon('heroicon-o-check-circle')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->status === 'active')
                ->action(function ($record) {
                    $record->update(['status' => 'completed']);
                    $record->driver?->update(['status' => 'available']);
                    $record->vehicleRequest?->update(['status' => 'completed']);
                    
                    $this->fillForm();
                }),
        ];
    }
}
