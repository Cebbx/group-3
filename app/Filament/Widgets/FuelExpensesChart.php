<?php

namespace App\Filament\Widgets;

use App\Models\WithdrawalSlip;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FuelExpensesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Fuel Expenses Over Time (₱)';
    
    protected static ?int $sort = 5;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->subDays(14)->format('Y-m-d');
        $endDate = $this->filters['endDate'] ?? now()->format('Y-m-d');
        $filterVehicle = $this->filters['vehicle'] ?? null;

        // Base query for withdrawal slips
        $query = WithdrawalSlip::query();

        // Filter based on trip ticket date
        $query->whereHas('tripTicket.vehicleRequest', function ($q) use ($startDate, $endDate, $filterVehicle) {
            $q->where('date', '>=', $startDate)
              ->where('date', '<=', $endDate);
            if ($filterVehicle) {
                $q->where('vehicle', $filterVehicle);
            }
        });

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diffInDays = $start->diffInDays($end);

        $labels = [];
        $data = [];

        if ($diffInDays > 60) {
            // Group by month (Database-agnostic using Eloquent Collection)
            $slips = $query->with('tripTicket.vehicleRequest')->get();
            
            $monthlyExpenses = $slips->groupBy(function ($item) {
                return Carbon::parse($item->tripTicket->vehicleRequest->date)->format('Y-m');
            })->map(function ($group) {
                return $group->sum('amount');
            })->toArray();

            $current = $start->copy()->startOfMonth();
            while ($current->lte($end)) {
                $monthStr = $current->format('Y-m');
                $labels[] = $current->format('M Y');
                $data[] = $monthlyExpenses[$monthStr] ?? 0;
                $current->addMonth();
            }
        } else {
            // Group by day
            $slips = $query->with('tripTicket.vehicleRequest')->get();

            $dailyExpenses = $slips->groupBy(function ($item) {
                return Carbon::parse($item->tripTicket->vehicleRequest->date)->format('Y-m-d');
            })->map(function ($group) {
                return $group->sum('amount');
            })->toArray();

            $current = $start->copy();
            while ($current->lte($end)) {
                $dateStr = $current->format('Y-m-d');
                $labels[] = $current->format('M d');
                $data[] = $dailyExpenses[$dateStr] ?? 0;
                $current->addDay();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Fuel Cost (₱)',
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)', // red transparent
                    'borderColor' => '#ef4444', // red
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
