<?php

namespace App\Filament\Driver\Pages;

use App\Models\TripTicket;
use App\Models\WithdrawalSlip;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.driver.pages.driver-dashboard';

    public function getAssignedTrips()
    {
        $driverId = auth()->user()->driver?->id ?? 0;
        
        return TripTicket::where('driver_id', $driverId)
            ->whereIn('status', ['pending', 'active'])
            ->latest()
            ->get();
    }

    public function getCompletedTripsCount()
    {
        $driverId = auth()->user()->driver?->id ?? 0;
        return TripTicket::where('driver_id', $driverId)->where('status', 'completed')->count();
    }

    public function getActiveTrip()
    {
        $driverId = auth()->user()->driver?->id ?? 0;
        return TripTicket::where('driver_id', $driverId)->where('status', 'active')->first();
    }

    public function toggleDutyStatus()
    {
        $driver = auth()->user()->driver;
        if (!$driver) {
            return;
        }

        if ($driver->status === 'on_trip') {
            \Filament\Notifications\Notification::make()
                ->title('Cannot Change Status')
                ->body('You cannot go offline while you have an active trip.')
                ->danger()
                ->send();
            return;
        }

        $newStatus = $driver->status === 'available' ? 'unavailable' : 'available';
        $driver->update(['status' => $newStatus]);

        \Filament\Notifications\Notification::make()
            ->title('Status Updated')
            ->body("Your duty status is now set to " . ucfirst($newStatus) . ".")
            ->success()
            ->send();
    }

    public function reportBreakdown($reason = 'Mechanical / Engine Breakdown')
    {
        $activeTrip = $this->getActiveTrip();
        if (!$activeTrip) {
            return;
        }

        // 1. Log activity
        \App\Models\ActivityLog::log(
            'Breakdown Reported',
            $activeTrip,
            "Driver " . auth()->user()->name . " reported a breakdown. Reason: " . $reason
        );

        // 2. Cancel the trip ticket
        $activeTrip->update(['status' => 'cancelled']);

        // 3. Put vehicle on maintenance
        $parts = explode(' - ', $activeTrip->vehicle);
        $plate = end($parts);
        $vehicle = \App\Models\Vehicle::where('plate_number', trim($plate))->first();
        if ($vehicle) {
            $vehicle->update(['status' => 'maintenance']);
        }

        // 4. Mark driver as off-duty/unavailable
        $driver = auth()->user()->driver;
        if ($driver) {
            $driver->update(['status' => 'unavailable']);
        }

        \Filament\Notifications\Notification::make()
            ->title('Emergency Alert Sent')
            ->body('Breakdown reported to GSO. Vehicle has been set to maintenance.')
            ->danger()
            ->persistent()
            ->send();
    }

    public function logDeparture()
    {
        $activeTrip = $this->getActiveTrip();
        if (!$activeTrip || $this->hasLoggedDeparture($activeTrip->id)) {
            return;
        }

        \App\Models\ActivityLog::log(
            'Departure Logged',
            $activeTrip,
            "Driver " . auth()->user()->name . " logged departure milestone for trip " . $activeTrip->ticket_number
        );

        \Filament\Notifications\Notification::make()
            ->title('Departure Logged')
            ->body('Departure milestone has been digitally recorded.')
            ->success()
            ->send();
    }

    public function logArrival()
    {
        $activeTrip = $this->getActiveTrip();
        if (!$activeTrip || !$this->hasLoggedDeparture($activeTrip->id) || $this->hasLoggedArrival($activeTrip->id)) {
            return;
        }

        \App\Models\ActivityLog::log(
            'Arrival Logged',
            $activeTrip,
            "Driver " . auth()->user()->name . " logged arrival milestone at destination for trip " . $activeTrip->ticket_number
        );

        \Filament\Notifications\Notification::make()
            ->title('Arrival Logged')
            ->body('Arrival milestone has been digitally recorded.')
            ->success()
            ->send();
    }

    public function hasLoggedDeparture($tripId): bool
    {
        return \App\Models\ActivityLog::where('model_type', TripTicket::class)
            ->where('model_id', $tripId)
            ->where('action', 'Departure Logged')
            ->exists();
    }

    public function hasLoggedArrival($tripId): bool
    {
        return \App\Models\ActivityLog::where('model_type', TripTicket::class)
            ->where('model_id', $tripId)
            ->where('action', 'Arrival Logged')
            ->exists();
    }
}
