<?php

namespace App\Filament\Employee\Resources\VehicleRequests\Pages;

use App\Filament\Employee\Resources\VehicleRequests\VehicleRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleRequests extends ListRecords
{
    protected static string $resource = VehicleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $userId = auth()->id();
        return [
            'all' => \Filament\Schemas\Components\Tabs\Tab::make('All'),
            'pending' => \Filament\Schemas\Components\Tabs\Tab::make('Pending')
                ->badge(\App\Models\VehicleRequest::where('user_id', $userId)->where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'pending')),
            'approved' => \Filament\Schemas\Components\Tabs\Tab::make('Approved')
                ->badge(\App\Models\VehicleRequest::where('user_id', $userId)->where('status', 'approved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'approved')),
            'on_trip' => \Filament\Schemas\Components\Tabs\Tab::make('On Trip')
                ->badge(\App\Models\VehicleRequest::where('user_id', $userId)->where('status', 'on_trip')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'on_trip')),
            'completed' => \Filament\Schemas\Components\Tabs\Tab::make('Completed')
                ->badge(\App\Models\VehicleRequest::where('user_id', $userId)->where('status', 'completed')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'completed')),
            'rejected' => \Filament\Schemas\Components\Tabs\Tab::make('Rejected')
                ->badge(\App\Models\VehicleRequest::where('user_id', $userId)->where('status', 'rejected')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'rejected')),
        ];
    }
}
