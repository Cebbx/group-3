<?php

namespace App\Filament\Employee\Pages;

use App\Models\VehicleRequest;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.employee.pages.employee-dashboard';

    public function getWidgets(): array
    {
        return [];
    }

    public function getStats(): array
    {
        $userId = auth()->id();
        return [
            'total' => VehicleRequest::where('user_id', $userId)->count(),
            'pending' => VehicleRequest::where('user_id', $userId)->where('status', 'pending')->count(),
            'approved' => VehicleRequest::where('user_id', $userId)->where('status', 'approved')->count(),
            'on_trip' => VehicleRequest::where('user_id', $userId)->where('status', 'on_trip')->count(),
            'completed' => VehicleRequest::where('user_id', $userId)->where('status', 'completed')->count(),
        ];
    }

    public function getRecentRequests()
    {
        return VehicleRequest::where('user_id', auth()->id())
            ->latest('id')
            ->take(5)
            ->get();
    }

    public function getActiveTrips()
    {
        // Get active requests (approved or on_trip) that have assigned trip tickets and drivers
        return VehicleRequest::where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'on_trip'])
            ->whereHas('tripTicket')
            ->with(['tripTicket.driver'])
            ->latest('id')
            ->get();
    }
}
