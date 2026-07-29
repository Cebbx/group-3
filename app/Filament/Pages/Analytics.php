<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use BackedEnum;

class Analytics extends BaseDashboard
{
    use HasFiltersForm;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $routePath = '/analytics';

    protected static ?string $title = 'Analytics';

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?int $navigationSort = 5;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('startDate')
                    ->label('Start Date')
                    ->placeholder('Select start date'),
                DatePicker::make('endDate')
                    ->label('End Date')
                    ->placeholder('Select end date'),
                Select::make('vehicle')
                    ->options([
                        'FORTUNER - SBA1749' => 'FORTUNER - SBA1749',
                        'HIACE VAN - SBA3790' => 'HIACE VAN - SBA3790',
                        'PTIA JEEP - SDV868' => 'PTIA JEEP - SDV868',
                        'MULTICAB - NAJI987' => 'MULTICAB - NAJI987',
                    ])
                    ->placeholder('Select a vehicle')
                    ->label('Vehicle'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'on_trip' => 'On Trip',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                    ])
                    ->placeholder('Select status')
                    ->label('Request Status'),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\AnalyticsOverview::class,
            \App\Filament\Widgets\BookingsOverTimeChart::class,
            \App\Filament\Widgets\VehicleUsageChart::class,
            \App\Filament\Widgets\DriverUsageChart::class,
            \App\Filament\Widgets\FuelExpensesChart::class,
        ];
    }
}
