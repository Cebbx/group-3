<?php

namespace App\Filament\Resources\WithdrawalSlips\Pages;

use App\Filament\Resources\WithdrawalSlips\WithdrawalSlipResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawalSlips extends ListRecords
{
    protected static string $resource = WithdrawalSlipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
