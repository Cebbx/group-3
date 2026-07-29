<?php

namespace App\Filament\Resources\SmsLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SmsLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('driver.name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable(),
                TextColumn::make('message')
                    ->label('SMS Message Body')
                    ->searchable()
                    ->limit(65),
                TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
