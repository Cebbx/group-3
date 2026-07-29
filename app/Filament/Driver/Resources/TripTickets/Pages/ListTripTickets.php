<?php

namespace App\Filament\Driver\Resources\TripTickets\Pages;

use App\Filament\Driver\Resources\TripTickets\TripTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTripTickets extends ListRecords
{
    protected static string $resource = TripTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
