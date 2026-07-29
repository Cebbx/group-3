<?php

namespace App\Filament\Resources\VehicleRequests\Pages;

use App\Filament\Resources\VehicleRequests\VehicleRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleRequest extends EditRecord
{
    protected static string $resource = VehicleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
