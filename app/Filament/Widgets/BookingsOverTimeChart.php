<?php

namespace App\Filament\Widgets;

use App\Models\VehicleRequest;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingsOverTimeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Bookings Over Time';
    
    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->subDays(14)->format('Y-m-d');
        $endDate = $this->filters['endDate'] ?? now()->format('Y-m-d');
        $filterVehicle = $this->filters['vehicle'] ?? null;
        $filterStatus = $this->filters['status'] ?? null;

        $query = VehicleRequest::whereIn('status', ['approved', 'on_trip', 'completed']);

        $query->where('date', '>=', $startDate);
        $query->where('date', '<=', $endDate);

        if ($filterVehicle) {
            $query->where('vehicle', $filterVehicle);
        }
        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        $rawResults = $query->groupBy('date')
            ->select('date', DB::raw('count(*) as count'))
            ->pluck('count', 'date')
            ->toArray();

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $labels = [];
        $data = [];

        $diffInDays = $start->diffInDays($end);
        
        if ($diffInDays > 60) {
            // Group by month
            $queryMonthly = VehicleRequest::whereIn('status', ['approved', 'on_trip', 'completed'])
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate);
                
            if ($filterVehicle) {
                $queryMonthly->where('vehicle', $filterVehicle);
            }
            if ($filterStatus) {
                $queryMonthly->where('status', $filterStatus);
            }
            
            $monthlyResults = $queryMonthly->get()
                ->groupBy(function ($item) {
                    return \Carbon\Carbon::parse($item->date)->format('Y-m');
                })
                ->map(fn ($group) => $group->count())
                ->toArray();
                
            $current = $start->copy()->startOfMonth();
            while ($current->lte($end)) {
                $monthStr = $current->format('Y-m');
                $labels[] = $current->format('M Y');
                $data[] = $monthlyResults[$monthStr] ?? 0;
                $current->addMonth();
            }
        } else {
            // Group by day
            $current = $start->copy();
            while ($current->lte($end)) {
                $dateStr = $current->format('Y-m-d');
                $labels[] = $current->format('M d');
                $data[] = $rawResults[$dateStr] ?? 0;
                $current->addDay();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Approved/Completed Bookings',
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)', // transparent amber
                    'borderColor' => '#f59e0b', // amber
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
