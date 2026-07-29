<?php

namespace App\Filament\Resources\WithdrawalSlips\Pages;

use App\Filament\Resources\WithdrawalSlips\WithdrawalSlipResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWithdrawalSlip extends EditRecord
{
    protected static string $resource = WithdrawalSlipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
