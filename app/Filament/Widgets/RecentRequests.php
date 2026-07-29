<?php

namespace App\Filament\Widgets;

use App\Models\VehicleRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentRequests extends TableWidget
{
    protected static ?string $heading = 'Recent Vehicle Requests';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                VehicleRequest::query()->latest()->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('request_number')
                    ->label('Request #')
                    ->weight('bold'),
                TextColumn::make('employee_name')
                    ->label('Employee'),
                TextColumn::make('department')
                    ->badge()
                    ->color('info'),
                TextColumn::make('destination')
                    ->limit(30),
                TextColumn::make('date')
                    ->date('M d, Y'),
                TextColumn::make('time')
                    ->time('h:i A'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
            ]);
    }
}

