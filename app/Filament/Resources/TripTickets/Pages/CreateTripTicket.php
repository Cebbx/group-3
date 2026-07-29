<?php

namespace App\Filament\Resources\TripTickets\Pages;

use App\Filament\Resources\TripTickets\TripTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTripTicket extends CreateRecord
{
    protected static string $resource = TripTicketResource::class;

    public function canCreateAnother(): bool
    {
        return false;
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Approved Ticket');
    }
}
