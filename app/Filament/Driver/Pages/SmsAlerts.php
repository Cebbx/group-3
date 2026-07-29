<?php

namespace App\Filament\Driver\Pages;

use App\Models\SmsLog;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SmsAlerts extends Page
{
    protected string $view = 'filament.driver.pages.sms-alerts';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'SMS Alerts';

    protected static ?string $title = 'SMS Alerts';

    public function getSmsLogs()
    {
        $driverId = auth()->user()->driver?->id ?? 0;
        
        return SmsLog::where('driver_id', $driverId)
            ->oldest() // Oldest first to show in conversational order
            ->get();
    }
}

