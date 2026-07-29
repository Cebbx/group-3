<?php

namespace App\Filament\Resources\WithdrawalSlips\Pages;

use App\Filament\Resources\WithdrawalSlips\WithdrawalSlipResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWithdrawalSlip extends CreateRecord
{
    protected static string $resource = WithdrawalSlipResource::class;

    public function canCreateAnother(): bool
    {
        return false;
    }

    protected function getRedirectUrl(): string
    {
        return route('withdrawal-slips.print', $this->record->id);
    }
}
