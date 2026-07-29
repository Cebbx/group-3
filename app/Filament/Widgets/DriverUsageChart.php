<?php

namespace App\Filament\Widgets;

use App\Models\TripTicket;
use App\Models\Driver;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class DriverUsageChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Driver Utilization Frequency';
    
    protected static ?int $sort = 4;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $filterVehicle = $this->filters['vehicle'] ?? null;
        $filterStatus = $this->filters['status'] ?? null;

        $query = TripTicket::query()
            ->whereNotNull('driver_id');

        if ($startDate || $endDate || $filterVehicle || $filterStatus) {
            $query->whereHas('vehicleRequest', function ($q) use ($startDate, $endDate, $filterVehicle, $filterStatus) {
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

        // Count tickets per driver
        $data = $query->groupBy('driver_id')
            ->select('driver_id', DB::raw('count(*) as count'))
            ->pluck('count', 'driver_id')
            ->toArray();

        $allDrivers = Driver::all();
        $chartData = [];
        $labels = [];
        
        foreach ($allDrivers as $driver) {
            $labels[] = $driver->name;
            $chartData[] = $data[$driver->id] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Trips Conducted',
                    'data' => $chartData,
                    'backgroundColor' => '#3b82f6', // blue
                    'borderColor' => '#2563eb',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
