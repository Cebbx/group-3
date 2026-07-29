<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ActivityLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_name')
                    ->label('Performed By')
                    ->readOnly(),
                TextInput::make('action')
                    ->label('Action')
                    ->readOnly(),
                TextInput::make('ip_address')
                    ->label('IP Address')
                    ->readOnly(),
                Textarea::make('details')
                    ->label('Activity Details')
                    ->readOnly()
                    ->columnSpanFull(),
            ]);
    }
}
