<?php

namespace App\Filament\Employee\Resources\VehicleRequests;

use App\Filament\Employee\Resources\VehicleRequests\Pages\CreateVehicleRequest;
use App\Filament\Employee\Resources\VehicleRequests\Pages\EditVehicleRequest;
use App\Filament\Employee\Resources\VehicleRequests\Pages\ListVehicleRequests;
use App\Filament\Employee\Resources\VehicleRequests\Schemas\VehicleRequestForm;
use App\Filament\Employee\Resources\VehicleRequests\Tables\VehicleRequestsTable;
use App\Models\VehicleRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehicleRequestResource extends Resource
{
    protected static ?string $model = VehicleRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'request_number';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        if ($user && $user->role === 'admin') {
            return parent::getEloquentQuery();
        }
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return VehicleRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleRequestsTable::configure($table);
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
            'index' => ListVehicleRequests::route('/'),
            'create' => CreateVehicleRequest::route('/create'),
            'edit' => EditVehicleRequest::route('/{record}/edit'),
        ];
    }
}
