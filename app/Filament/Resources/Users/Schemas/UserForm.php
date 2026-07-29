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
                    ->revealable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->placeholder('Enter password')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) return $state;
                        if (!$record) return 'password';
                        if ($record->role === 'driver') {
                            return $record->email;
                        }
                        if (str_contains($record->email, '@csu.edu.ph')) {
                            return explode('@', $record->email)[0];
                        }
                        return 'password';
                    })
                    ->helperText('Default passwords: For department accounts, it is the email prefix (e.g., "records" for records@csu.edu.ph). For default admin/employee, it is "password". For drivers, it is their LTO license number.'),
                    
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
