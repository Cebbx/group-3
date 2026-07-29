<?php

namespace App\Filament\Driver\Resources\TripTickets\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class TripTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Assigned Trip Information')
                    ->description(fn ($record) => ($record && is_object($record)) ? "Ticket Number: {$record->ticket_number}" : null)
                    ->components([
                        Grid::make(2)
                            ->components([
                                Placeholder::make('destination')
                                    ->label('Destination')
                                    ->content(fn ($record) => ($record && is_object($record)) ? ($record->vehicleRequest?->destination ?? 'N/A') : 'N/A'),
                                Placeholder::make('vehicle')
                                    ->label('Vehicle')
                                    ->content(function ($record) {
                                        if (!$record || !is_object($record) || !$record->vehicle) return 'N/A';
                                        $vehicle = \App\Models\Vehicle::where('plate_number', $record->vehicle)->first();
                                        return $vehicle ? "{$vehicle->brand} - {$vehicle->plate_number}" : $record->vehicle;
                                    }),
                                Placeholder::make('date')
                                    ->label('Date')
                                    ->content(fn ($record) => ($record && is_object($record) && $record->vehicleRequest?->date) 
                                        ? \Carbon\Carbon::parse($record->vehicleRequest->date)->format('F d, Y') 
                                        : 'N/A'),
                                Placeholder::make('time')
                                    ->label('Time')
                                    ->content(fn ($record) => ($record && is_object($record) && $record->vehicleRequest?->time) 
                                        ? \Carbon\Carbon::parse($record->vehicleRequest->time)->format('h:i A') 
                                        : 'N/A'),
                                Placeholder::make('passenger')
                                    ->label('Passenger')
                                    ->content(fn ($record) => ($record && is_object($record)) ? ($record->vehicleRequest?->employee_name ?? 'N/A') : 'N/A'),
                                Placeholder::make('purpose')
                                    ->label('Purpose')
                                    ->content(fn ($record) => ($record && is_object($record)) ? ($record->vehicleRequest?->purpose ?? 'N/A') : 'N/A'),
                                Placeholder::make('status')
                                    ->label('Status')
                                    ->content(fn ($record) => ($record && is_object($record)) ? ucfirst($record->status ?? 'pending') : 'N/A'),
                            ]),
                    ]),
                Section::make('Trip Completion QR Code')
                    ->description('Show this QR code to the school guard to complete your trip upon return.')
                    ->components([
                        Placeholder::make('qr_code')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !is_object($record) || !$record->ticket_number) {
                                    return 'N/A';
                                }
 
                                $completionUrl = route('trip-tickets.complete-via-qr', ['ticket_number' => $record->ticket_number]);
                                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($completionUrl);
 
                                $html = "
                                <div class='flex flex-col items-center justify-center p-6 bg-slate-900/10 dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-slate-800 w-full max-w-sm mx-auto gap-4'>
                                    <img src='{$qrCodeUrl}' alt='QR Code' class='w-48 h-48 rounded-xl shadow-md border-2 border-white dark:border-slate-700' />
                                    <div class='text-center'>
                                        <p class='text-xs text-slate-500 dark:text-slate-400 font-medium'>Ask the guard to scan this QR code with their smartphone camera to mark this trip as completed.</p>
                                    </div>
                                </div>
                                ";
 
                                return new \Illuminate\Support\HtmlString($html);
                            })
                    ])
                    ->visible(fn ($record) => $record && is_object($record) && $record->status === 'active'),
            ]);
    }
}

