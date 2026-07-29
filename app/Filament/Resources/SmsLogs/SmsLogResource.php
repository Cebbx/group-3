<?php

namespace App\Filament\Resources\SmsLogs;

use App\Filament\Resources\SmsLogs\Pages\ListSmsLogs;
use App\Filament\Resources\SmsLogs\Tables\SmsLogsTable;
use App\Models\SmsLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class SmsLogResource extends Resource
{
    protected static ?string $model = SmsLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'SMS Logs';

    protected static ?string $pluralLabel = 'SMS Logs';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Resources\SmsLogs\Schemas\SmsLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmsLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmsLogs::route('/'),
        ];
    }
}
