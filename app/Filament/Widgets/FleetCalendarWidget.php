<?php

namespace App\Filament\Widgets;

use App\Models\TripTicket;
use Filament\Widgets\Widget;

class FleetCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.fleet-calendar';

    protected static ?int $sort = 0; // Render at the very top of the dashboard

    protected int | string | array $columnSpan = 'full'; // Full-width calendar grid layout

    protected function getViewData(): array
    {
        $tickets = TripTicket::with(['vehicleRequest', 'driver'])
            ->whereIn('status', ['pending', 'active', 'completed'])
            ->get();

        $events = [];
        foreach ($tickets as $ticket) {
            if (!$ticket->vehicleRequest) {
                continue;
            }

            $req = $ticket->vehicleRequest;
            $start = $req->date . 'T' . $req->time;
            
            $end = ($req->return_date && $req->return_time)
                ? ($req->return_date . 'T' . $req->return_time)
                : date('Y-m-d\TH:i:s', strtotime($start . ' +2 hours'));

            $driverName = $ticket->driver?->name ?? 'No Driver';
            
            $events[] = [
                'id' => $ticket->id,
                'title' => "{$ticket->ticket_number} - {$ticket->vehicle} ({$driverName})",
                'start' => $start,
                'end' => $end,
                'color' => match ($ticket->status) {
                    'pending' => '#d97706', // Amber-600
                    'active' => '#2563eb', // Blue-600
                    'completed' => '#059669', // Emerald-600
                    default => '#4b5563',
                },
                'url' => \App\Filament\Resources\TripTickets\TripTicketResource::getUrl('edit', ['record' => $ticket->id]),
            ];
        }

        return [
            'events' => $events,
        ];
    }
}
