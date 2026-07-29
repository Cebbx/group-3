<?php

namespace App\Filament\Driver\Resources\TripTickets\Pages;

use App\Filament\Driver\Resources\TripTickets\TripTicketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTripTicket extends EditRecord
{
    protected static string $resource = TripTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
