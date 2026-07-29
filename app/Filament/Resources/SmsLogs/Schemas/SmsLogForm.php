<?php

namespace App\Filament\Resources\SmsLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SmsLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('driver.name')
                    ->label('Driver')
                    ->readOnly(),
                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->readOnly(),
                Textarea::make('message')
                    ->label('SMS Message Body')
                    ->readOnly()
                    ->columnSpanFull()
                    ->rows(6),
            ]);
    }
}
