<?php

namespace App\Filament\Resources\TripTickets;

use App\Filament\Resources\TripTickets\Pages\CreateTripTicket;
use App\Filament\Resources\TripTickets\Pages\ListTripTickets;
use App\Filament\Resources\TripTickets\Schemas\TripTicketForm;
use App\Filament\Resources\TripTickets\Tables\TripTicketsTable;
use App\Models\TripTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TripTicketResource extends Resource
{
    protected static ?string $model = TripTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'ticket_number';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return TripTicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripTicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTripTickets::route('/'),
            'create' => CreateTripTicket::route('/create'),
        ];
    }
}
