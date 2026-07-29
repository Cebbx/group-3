<?php

namespace App\Filament\Widgets;

use App\Models\TripTicket;
use App\Models\Driver;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class DriverTripsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Driver Performance (Trips Completed)';
    
    protected static ?int $sort = 4;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query = TripTicket::where('status', 'completed');

        if ($startDate) {
            $query->whereHas('vehicleRequest', fn ($q) => $q->where('date', '>=', $startDate));
        }
        if ($endDate) {
            $query->whereHas('vehicleRequest', fn ($q) => $q->where('date', '<=', $endDate));
        }

        $data = $query->groupBy('driver_id')
            ->select('driver_id', DB::raw('count(*) as count'))
            ->pluck('count', 'driver_id')
            ->toArray();

        $drivers = Driver::all();
        $labels = [];
        $chartData = [];

        foreach ($drivers as $driver) {
            $labels[] = $driver->name;
            $chartData[] = $data[$driver->id] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Completed Trips',
                    'data' => $chartData,
                    'backgroundColor' => '#10b981', // emerald
                    'borderColor' => '#059669',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
