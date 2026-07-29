<?php

namespace App\Filament\Resources\Drivers\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripTicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tripTickets';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ticket_number')
                    ->required(),
                Select::make('vehicle_request_id')
                    ->relationship('vehicleRequest', 'id')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('vehicle'),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('ticket_number'),
                TextEntry::make('vehicleRequest.id')
                    ->label('Vehicle request'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('vehicle')
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ticket_number')
            ->columns([
                TextColumn::make('ticket_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicleRequest.request_number')
                    ->label('Request Number')
                    ->searchable(),
                TextColumn::make('vehicleRequest.date')
                    ->label('Trip Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('vehicleRequest.time')
                    ->label('Trip Time')
                    ->time('h:i A'),
                TextColumn::make('vehicleRequest.destination')
                    ->label('Destination')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('vehicle')
                    ->label('Vehicle')
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Read-only
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                // Read-only
            ]);
    }
}
