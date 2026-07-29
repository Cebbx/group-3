<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('action')
                    ->label('Action')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Created Request' => 'gray',
                        'Updated Request' => 'warning',
                        'Deleted Request' => 'danger',
                        'Approved & Ticketed' => 'success',
                        'Uploaded Document' => 'success',
                        'Completed Trip' => 'info',
                        default => 'primary',
                    })
                    ->sortable(),
                TextColumn::make('details')
                    ->label('Details')
                    ->searchable()
                    ->limit(55),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Timestamp')
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
