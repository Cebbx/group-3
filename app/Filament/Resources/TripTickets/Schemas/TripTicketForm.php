<?php

namespace App\Filament\Resources\TripTickets\Schemas;

use App\Models\TripTicket;
use App\Models\VehicleRequest;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TripTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ticket_number')
                    ->default(function () {
                        $lastRecord = TripTicket::latest('id')->first();
                        $nextId = $lastRecord ? ($lastRecord->id + 1) : 1;
                        return 'TT-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
                    })
                    ->unique('trip_tickets', 'ticket_number', ignoreRecord: true)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                
                Select::make('vehicle_request_id')
                    ->default(fn () => request()->query('vehicle_request_id'))
                    ->relationship('vehicleRequest', 'request_number', function (Builder $query) {
                        $reqId = request()->query('vehicle_request_id');
                        return $query->whereIn('status', ['pending', 'approved'])
                            ->when($reqId, fn ($q) => $q->orWhere('id', $reqId))
                            ->whereDoesntHave('tripTicket');
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->request_number} - {$record->employee_name} ({$record->destination})")
                    ->label('Vehicle Request Number')
                    ->placeholder('Select request number')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $request = VehicleRequest::find($state);
                            if ($request) {
                                // Find matching vehicle model to get its plate number
                                $vehicle = \App\Models\Vehicle::where('model', 'like', '%' . $request->vehicle . '%')
                                    ->orWhere('brand', 'like', '%' . $request->vehicle . '%')
                                    ->first();
                                $set('vehicle', $vehicle?->plate_number);
                            }
                        }
                    })
                    ->disabled(fn (string $operation, ?TripTicket $record) => $operation === 'edit' || request()->has('vehicle_request_id'))
                    ->dehydrated()
                    ->required(),

                \Filament\Forms\Components\Checkbox::make('show_all_vehicles')
                    ->label('Do you want to see/assign other busy or under maintenance cars?')
                    ->live(),

                Select::make('vehicle')
                    ->default(function () {
                        $reqId = request()->query('vehicle_request_id');
                        if ($reqId) {
                            $request = \App\Models\VehicleRequest::find($reqId);
                            if ($request) {
                                $vehicle = \App\Models\Vehicle::where('model', 'like', '%' . $request->vehicle . '%')
                                    ->orWhere('brand', 'like', '%' . $request->vehicle . '%')
                                    ->first();
                                return $vehicle?->plate_number;
                            }
                        }
                        return null;
                    })
                    ->options(function (callable $get, ?TripTicket $record) {
                        $allVehicles = \App\Models\Vehicle::all();

                        $requestId = $get('vehicle_request_id');
                        $travelDate = $requestId ? VehicleRequest::where('id', $requestId)->value('date') : null;

                        $activeVehicles = TripTicket::where('status', 'active')
                            ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                            ->pluck('vehicle')
                            ->filter()
                            ->toArray();

                        $scheduledVehicles = [];
                        if ($travelDate) {
                            $scheduledVehicles = TripTicket::whereHas('vehicleRequest', fn ($q) => $q->where('date', $travelDate))
                                ->where('status', '!=', 'cancelled')
                                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                ->pluck('vehicle')
                                ->filter()
                                ->toArray();
                        }

                        $vehiclesFormatted = [];
                        foreach ($allVehicles as $vehicle) {
                            $fullName = "{$vehicle->brand} - {$vehicle->plate_number}";
                            $isScheduled = in_array($vehicle->plate_number, $scheduledVehicles);

                            if ($vehicle->status === 'maintenance') {
                                $vehiclesFormatted[$vehicle->plate_number] = "{$fullName} (Under Maintenance)";
                            } elseif (in_array($vehicle->plate_number, $activeVehicles)) {
                                $vehiclesFormatted[$vehicle->plate_number] = "{$fullName} (On Trip)";
                            } elseif ($isScheduled) {
                                $vehiclesFormatted[$vehicle->plate_number] = "{$fullName} (Scheduled on this date)";
                            } else {
                                $vehiclesFormatted[$vehicle->plate_number] = $fullName;
                            }
                        }

                        if ($get('show_all_vehicles')) {
                            return $vehiclesFormatted;
                        }

                        $availableVehicles = [];
                        foreach ($allVehicles as $vehicle) {
                            $fullName = "{$vehicle->brand} - {$vehicle->plate_number}";
                            $isBusy = in_array($vehicle->plate_number, $activeVehicles);
                            $isScheduled = in_array($vehicle->plate_number, $scheduledVehicles);
                            $isMaintenance = $vehicle->status === 'maintenance';

                            if (!$isBusy && !$isMaintenance && !$isScheduled) {
                                $availableVehicles[$vehicle->plate_number] = $fullName;
                            }
                        }

                        if ($record && $record->vehicle && !isset($availableVehicles[$record->vehicle])) {
                            $dbVehicle = \App\Models\Vehicle::where('plate_number', $record->vehicle)->first();
                            $fullName = $dbVehicle ? "{$dbVehicle->brand} - {$dbVehicle->plate_number}" : $record->vehicle;
                            
                            $isScheduled = in_array($record->vehicle, $scheduledVehicles);

                            $statusLabel = 'Unavailable';
                            if (in_array($record->vehicle, $activeVehicles)) {
                                $statusLabel = 'On Trip';
                            } elseif ($dbVehicle && $dbVehicle->status === 'maintenance') {
                                $statusLabel = 'Under Maintenance';
                            } elseif ($isScheduled) {
                                $statusLabel = 'Scheduled on this date';
                            }

                            $availableVehicles[$record->vehicle] = "{$fullName} ({$statusLabel})";
                        }

                        return $availableVehicles;
                    })
                    ->label('Vehicle')
                    ->disableOptionWhen(function (string $value, callable $get, ?TripTicket $record) {
                        $activeVehicles = TripTicket::where('status', 'active')
                            ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                            ->pluck('vehicle')
                            ->filter()
                            ->toArray();

                        $requestId = $get('vehicle_request_id');
                        $travelDate = $requestId ? VehicleRequest::where('id', $requestId)->value('date') : null;

                        $isScheduled = false;
                        if ($travelDate) {
                            $isScheduled = TripTicket::where('vehicle', $value)
                                ->whereHas('vehicleRequest', fn ($q) => $q->where('date', $travelDate))
                                ->where('status', '!=', 'cancelled')
                                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                ->exists();
                        }

                        $dbVehicle = \App\Models\Vehicle::where('plate_number', $value)->first();
                        $isMaintenance = $dbVehicle && $dbVehicle->status === 'maintenance';

                        return in_array($value, $activeVehicles) || $isMaintenance || $isScheduled;
                    })
                    ->helperText(function (callable $get) {
                        if ($get('show_all_vehicles')) {
                            return "Showing all vehicles. (Note: Some of these might currently be on a trip or under maintenance).";
                        }
                        return "Showing only available vehicles.";
                    })
                    ->rules([
                        fn (callable $get, ?TripTicket $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                            $activeVehicles = TripTicket::where('status', 'active')
                                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                ->pluck('vehicle')
                                ->filter()
                                ->toArray();

                            $requestId = $get('vehicle_request_id');
                            $travelDate = $requestId ? VehicleRequest::where('id', $requestId)->value('date') : null;

                            $isScheduled = false;
                            if ($travelDate) {
                                $isScheduled = TripTicket::where('vehicle', $value)
                                    ->whereHas('vehicleRequest', fn ($q) => $q->where('date', $travelDate))
                                    ->where('status', '!=', 'cancelled')
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->exists();
                            }

                            $dbVehicle = \App\Models\Vehicle::where('plate_number', $value)->first();
                            $isMaintenance = $dbVehicle && $dbVehicle->status === 'maintenance';

                            if (in_array($value, $activeVehicles)) {
                                    $fail("This vehicle is currently on a trip. Please select another vehicle.");
                            }

                            if ($isMaintenance) {
                                    $fail("This vehicle is currently under maintenance. Please select another vehicle.");
                            }

                            if ($isScheduled) {
                                    $fail("This vehicle is already scheduled for another trip on this date. Please select another vehicle.");
                            }
                        }
                    ])
                    ->required(),
                
                Select::make('driver_id')
                    ->relationship('driver', 'name', function (Builder $query) {
                        return $query;
                    })
                    ->getOptionLabelFromRecordUsing(function ($record, callable $get, ?TripTicket $ticketRecord) {
                        $requestId = $get('vehicle_request_id');
                        $travelDate = $requestId ? VehicleRequest::where('id', $requestId)->value('date') : null;

                        $isScheduled = false;
                        if ($travelDate) {
                            $isScheduled = TripTicket::where('driver_id', $record->id)
                                ->whereHas('vehicleRequest', fn ($q) => $q->where('date', $travelDate))
                                ->where('status', '!=', 'cancelled')
                                ->when($ticketRecord, fn ($q) => $q->where('id', '!=', $ticketRecord->id))
                                ->exists();
                        }

                        if ($record->status === 'unavailable') {
                            return "{$record->name} (Offline / Off-Duty)";
                        } elseif ($record->status === 'on_trip') {
                            return "{$record->name} (On Trip)";
                        } elseif ($isScheduled) {
                            return "{$record->name} (Scheduled on this date)";
                        }
                        return $record->name;
                    })
                    ->disableOptionWhen(function (string $value, callable $get, ?TripTicket $record) {
                        $requestId = $get('vehicle_request_id');
                        $travelDate = $requestId ? VehicleRequest::where('id', $requestId)->value('date') : null;

                        $isScheduled = false;
                        if ($travelDate) {
                            $isScheduled = TripTicket::where('driver_id', $value)
                                ->whereHas('vehicleRequest', fn ($q) => $q->where('date', $travelDate))
                                ->where('status', '!=', 'cancelled')
                                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                ->exists();
                        }

                        $isUnavailable = \App\Models\Driver::where('id', $value)->where('status', 'unavailable')->exists();
                        $isOnTrip = \App\Models\Driver::where('id', $value)->where('status', 'on_trip')->exists() 
                            && (!$record || $record->driver_id != $value);

                        return $isScheduled || $isOnTrip || $isUnavailable;
                    })
                    ->label('Driver')
                    ->helperText(function (callable $get, ?TripTicket $record) {
                        $requestId = $get('vehicle_request_id');
                        if (!$requestId) {
                            $onTripDrivers = \App\Models\Driver::where('status', 'on_trip')->pluck('name')->join(', ');
                            $busyText = $onTripDrivers ? " (Currently on trip: {$onTripDrivers})" : "";
                            return "Select a request number first.{$busyText}";
                        }
                        
                        $request = VehicleRequest::find($requestId);
                        if (!$request) {
                            return null;
                        }

                        $travelDate = $request->date;
                        
                        // Get busy drivers on this date
                        $busyDriverIds = TripTicket::whereHas('vehicleRequest', function ($q) use ($travelDate) {
                                $q->where('date', $travelDate);
                            })
                            ->where('status', '!=', 'cancelled')
                            ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                            ->pluck('driver_id')
                            ->filter()
                            ->toArray();

                        // Get general on-trip drivers
                        $onTripDriverIds = \App\Models\Driver::where('status', 'on_trip')
                            ->pluck('id')
                            ->toArray();

                        // Get offline drivers
                        $offlineDriverIds = \App\Models\Driver::where('status', 'unavailable')
                            ->pluck('id')
                            ->toArray();

                        $allBusyDriverIds = array_unique(array_merge($busyDriverIds, $onTripDriverIds, $offlineDriverIds));

                        $busyDrivers = \App\Models\Driver::whereIn('id', $allBusyDriverIds)->pluck('name')->join(', ');
                        $availableDrivers = \App\Models\Driver::whereNotIn('id', $allBusyDriverIds)->pluck('name')->join(', ');

                        $text = "<strong>Driver Schedule for " . \Carbon\Carbon::parse($travelDate)->format('M d, Y') . ":</strong><br>";
                        $text .= "<span style='color: #16a34a;'>✅ Available:</span> " . ($availableDrivers ?: "None") . "<br>";
                        $text .= "<span style='color: #dc2626;'>❌ Busy / On Trip / Offline:</span> " . ($busyDrivers ?: "None");
                        
                        return new \Illuminate\Support\HtmlString($text);
                    })
                    ->rules([
                        fn (callable $get, ?TripTicket $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                            if ($value) {
                                $requestId = $get('vehicle_request_id');
                                $travelDate = $requestId ? VehicleRequest::where('id', $requestId)->value('date') : null;

                                $isScheduled = false;
                                if ($travelDate) {
                                    $isScheduled = TripTicket::where('driver_id', $value)
                                        ->whereHas('vehicleRequest', fn ($q) => $q->where('date', $travelDate))
                                        ->where('status', '!=', 'cancelled')
                                        ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                        ->exists();
                                }

                                $driver = \App\Models\Driver::find($value);
                                $isOnTrip = $driver && $driver->status === 'on_trip' && (!$record || $record->driver_id !== $driver->id);
                                $isUnavailable = $driver && $driver->status === 'unavailable';

                                if ($isUnavailable) {
                                    $fail("This driver is currently Offline (Off-Duty). Please select another driver.");
                                }

                                if ($isOnTrip) {
                                    $fail("This driver is currently on a trip. Please select another driver.");
                                }

                                if ($isScheduled) {
                                    $fail("This driver is already scheduled for another trip on this date. Please select another driver.");
                                }
                            }
                        }
                    ])
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'On Trip',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending')
                    ->disabled()
                    ->dehydrated()
                    ->hidden(fn (string $operation) => $operation === 'create'),

                \Filament\Forms\Components\Placeholder::make('qr_code')
                    ->label('Trip Completion QR Code')
                    ->content(function (?TripTicket $record) {
                        if (!$record || !$record->ticket_number) {
                            return 'Will be generated after creation.';
                        }
                        
                        $completionUrl = route('trip-tickets.complete-via-qr', ['ticket_number' => $record->ticket_number]);
                        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($completionUrl);
                        
                        $html = "
                        <div class='flex flex-col items-center justify-center p-4 bg-slate-900/10 dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-slate-800 w-fit gap-3'>
                            <img src='{$qrCodeUrl}' alt='QR Code' class='w-48 h-48 rounded-xl shadow-md border border-white dark:border-slate-700' />
                            <div class='text-center'>
                                <p class='text-xs text-slate-500 dark:text-slate-400'>Scan this QR Code to automatically mark this trip as completed.</p>
                                <a href='{$completionUrl}' target='_blank' class='text-[10px] text-primary-600 dark:text-primary-400 hover:underline mt-1 block font-mono'>{$completionUrl}</a>
                            </div>
                        </div>
                        ";
                        
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->visible(fn (string $operation, ?TripTicket $record) => $operation !== 'create' && $record && $record->status === 'active'),
            ]);
    }
}
