<?php

namespace App\Filament\Pages\Auth;

use App\Models\Driver;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class DriverLogin extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Driver Portal';
    }

    public function getSubheading(): string
    {
        return 'Enter your License ID to continue';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('license_number')
                    ->label('Driver License ID or Contact Number')
                    ->placeholder('e.g. N01-12-345678 or 09XXXXXXXXX')
                    ->required()
                    ->autofocus(),
            ])
            ->statePath('data');
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $driver = Driver::where('license_number', $data['license_number'])
            ->orWhere('contact_number', $data['license_number'])
            ->first();

        if (! $driver) {
            Notification::make()
                ->title('Invalid Credentials')
                ->body('We could not find a driver with that License ID or Contact Number.')
                ->danger()
                ->send();

            return null;
        }

        // Find or create a user account for the driver
        $user = User::firstOrCreate(
            ['email' => $driver->license_number],
            [
                'name' => $driver->name,
                'password' => Hash::make($driver->license_number),
                'role' => 'driver'
            ]
        );

        Auth::login($user, remember: true);

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
