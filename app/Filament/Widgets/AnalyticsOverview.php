<?php

namespace App\Filament\Widgets;

use App\Models\TripTicket;
use App\Models\WithdrawalSlip;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Carbon\Carbon;

class AnalyticsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $filterVehicle = $this->filters['vehicle'] ?? null;
        $filterStatus = $this->filters['status'] ?? null;

        // Base query for trip tickets
        $tripQuery = TripTicket::query();

        if ($startDate || $endDate || $filterVehicle || $filterStatus) {
            $tripQuery->whereHas('vehicleRequest', function ($q) use ($startDate, $endDate, $filterVehicle, $filterStatus) {
                if ($startDate) {
                    $q->where('date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->where('date', '<=', $endDate);
                }
                if ($filterVehicle) {
                    $q->where('vehicle', $filterVehicle);
                }
                if ($filterStatus) {
                    $q->where('status', $filterStatus);
                }
            });
        }

        $trips = $tripQuery->get();
        $totalTrips = $trips->count();
        
        // Active/On Trip right now
        $activeTrips = $trips->where('status', 'active')->count();

        // Calculate unique drivers utilized
        $uniqueDrivers = $trips->pluck('driver_id')->filter()->unique()->count();

        // Calculate total gas expenses within filtered trips
        $tripIds = $trips->pluck('id')->toArray();
        $totalGas = 0;
        if (!empty($tripIds)) {
            $totalGas = WithdrawalSlip::whereIn('trip_ticket_id', $tripIds)
                ->sum('amount');
        }

        return [
            Stat::make('Total Trips Assigned', $totalTrips)
                ->description('Total trips during this period')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
                
            Stat::make('Drivers Utilized', $uniqueDrivers)
                ->description('Unique active drivers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Total Fuel Expenses', '₱' . number_format($totalGas, 2))
                ->description('Gas spent for these trips')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}
