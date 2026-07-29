<?php

namespace App\Filament\Resources\WithdrawalSlips;

use App\Filament\Resources\WithdrawalSlips\Pages\CreateWithdrawalSlip;
use App\Filament\Resources\WithdrawalSlips\Pages\EditWithdrawalSlip;
use App\Filament\Resources\WithdrawalSlips\Pages\ListWithdrawalSlips;
use App\Filament\Resources\WithdrawalSlips\Schemas\WithdrawalSlipForm;
use App\Filament\Resources\WithdrawalSlips\Tables\WithdrawalSlipsTable;
use App\Models\WithdrawalSlip;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WithdrawalSlipResource extends Resource
{
    protected static ?string $model = WithdrawalSlip::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'slip_number';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return WithdrawalSlipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WithdrawalSlipsTable::configure($table);
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
            'index' => ListWithdrawalSlips::route('/'),
            'create' => CreateWithdrawalSlip::route('/create'),
            'edit' => EditWithdrawalSlip::route('/{record}/edit'),
        ];
    }
}
