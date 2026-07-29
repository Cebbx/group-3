<?php

namespace App\Filament\Widgets;

use App\Models\VehicleRequest;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class RequestsPerDepartmentChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Requests by Department';
    
    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $filterVehicle = $this->filters['vehicle'] ?? null;

        $query = VehicleRequest::query();

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }
        if ($filterVehicle) {
            $query->where('vehicle', $filterVehicle);
        }

        $data = $query->groupBy('department')
            ->select('department', DB::raw('count(*) as count'))
            ->pluck('count', 'department')
            ->toArray();

        $departments = [
            'CICS' => 'Information Tech (CICS)',
            'COA' => 'Agriculture (COA)',
            'CHM' => 'Hospitality (CHM)',
            'CTED' => 'Teacher Education (CTED)',
        ];

        $labels = [];
        $chartData = [];

        foreach ($departments as $key => $label) {
            $labels[] = $label;
            $chartData[] = $data[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Requests',
                    'data' => $chartData,
                    'backgroundColor' => [
                        '#3b82f6', // blue
                        '#10b981', // emerald
                        '#f59e0b', // amber
                        '#ef4444', // red
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
