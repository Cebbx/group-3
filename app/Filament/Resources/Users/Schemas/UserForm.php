<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter full name'),
                    
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique('users', 'email', ignoreRecord: true)
                    ->placeholder('Enter email address'),
                    
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->placeholder('Enter password (leave blank to keep current)'),
                    
                Select::make('role')
                    ->options([
                        'admin' => 'Administrator',
                        'employee' => 'Employee',
                        'driver' => 'Driver',
                    ])
                    ->required()
                    ->placeholder('Select user role'),
            ]);
    }
}
