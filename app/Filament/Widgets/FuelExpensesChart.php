<?php

namespace App\Filament\Widgets;

use App\Models\WithdrawalSlip;
use Filament\Widgets\ChartWidget;

class FuelExpensesChart extends ChartWidget
{
    protected ?string $heading = 'Fuel Expenses by Vehicle (₱)';

    protected static ?int $sort = 5; // Put below vehicle usage and driver trips

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $slips = WithdrawalSlip::with('tripTicket')
            ->where('status', 'approved')
            ->get();

        $vehicleExpenses = [];
        foreach ($slips as $slip) {
            $vehicle = $slip->tripTicket->vehicle ?? 'Other/Unknown';
            if (!isset($vehicleExpenses[$vehicle])) {
                $vehicleExpenses[$vehicle] = 0.00;
            }
            $vehicleExpenses[$vehicle] += (float) $slip->amount;
        }

        // Sort descending by expenses amount
        arsort($vehicleExpenses);

        $labels = array_keys($vehicleExpenses);
        $chartData = array_values($vehicleExpenses);

        if (empty($labels)) {
            $labels = ['No Data'];
            $chartData = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Expenses (₱)',
                    'data' => $chartData,
                    'backgroundColor' => [
                        '#2563eb', // Blue-600
                        '#059669', // Emerald-600
                        '#d97706', // Amber-600
                        '#dc2626', // Red-600
                        '#7c3aed', // Purple-600
                    ],
                    'borderWidth' => 0,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
