<?php

namespace App\Filament\Employee\Resources\VehicleRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class VehicleRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('request_number')
                    ->default(function () {
                        $lastRecord = \App\Models\VehicleRequest::latest('id')->first();
                        $nextId = $lastRecord ? ($lastRecord->id + 1) : 1;
                        return 'VR-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
                    })
                    ->unique('vehicle_requests', 'request_number', ignoreRecord: true)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
                TextInput::make('employee_name')
                    ->default(fn () => auth()->user()?->name)
                    ->required(),
                Select::make('department')
                    ->default(function () {
                        $email = auth()->user()?->email ?? '';
                        $prefix = strtolower(explode('@', $email)[0]);
                        $validDepts = [
                            'ceo' => 'Office of the CEO',
                            'hrmo' => 'HRMO',
                            'accounting' => 'Accounting Office',
                            'budget' => 'Budget Office',
                            'property' => 'Property and Supply Office',
                            'records' => 'Records Office',
                            'planning' => 'Planning Office',
                            'mis' => 'MIS Office',
                            'registrar' => 'Office of the Campus Registrar',
                            'admission' => 'Campus Admission Office',
                            'publication' => 'Campus Publication Office',
                            'library' => 'University Library',
                            'cics' => 'CICS',
                            'cte' => 'CTE',
                            'chm' => 'CHM',
                            'coa' => 'COA',
                            'cafevalena' => 'Café Valena',
                            'csc' => 'Campus Student Council'
                        ];
                        return isset($validDepts[$prefix]) ? $validDepts[$prefix] : null;
                    })
                    ->options([
                        'Office of the CEO' => 'Office of the CEO (Campus Executive Officer)',
                        'HRMO' => 'HRMO (Human Resource Management Office)',
                        'Accounting Office' => 'Accounting Office',
                        'Budget Office' => 'Budget Office',
                        'Property and Supply Office' => 'Property and Supply Office',
                        'Records Office' => 'Records Office',
                        'Planning Office' => 'Planning Office',
                        'MIS Office' => 'MIS Office (Management Information System / System Admin)',
                        'Office of the Campus Registrar' => 'Office of the Campus Registrar',
                        'Campus Admission Office' => 'Campus Admission Office',
                        'Campus Publication Office' => 'Campus Publication Office',
                        'University Library' => 'University Library',
                        'CICS' => 'CICS (College of Information and Computing Sciences)',
                        'CTE' => 'CTE (College of Teacher Education)',
                        'CHM' => 'CHM (College of Hospitality Management)',
                        'COA' => 'COA (College of Agriculture)',
                        'Café Valena' => 'Café Valena (CoffeeHub Café)',
                        'Campus Student Council' => 'Campus Student Council (CSC)',
                    ])
                    ->required(),
                Select::make('vehicle')
                    ->options(function (Get $get) {
                        $date = $get('date');
                        $vehicleTypes = [
                            'FORTUNER' => 'FORTUNER',
                            'HIACE VAN' => 'HIACE VAN',
                            'PTIA JEEP' => 'PTIA JEEP',
                            'MULTICAB' => 'MULTICAB',
                        ];

                        foreach ($vehicleTypes as $type => $label) {
                            // Check if this vehicle is currently under maintenance
                            $isMaintenance = \App\Models\Vehicle::where('status', 'maintenance')
                                ->where(function ($q) use ($type) {
                                    $q->where('brand', 'like', '%' . $type . '%')
                                      ->orWhere('model', 'like', '%' . $type . '%');
                                })
                                ->exists();

                            if ($isMaintenance) {
                                $vehicleTypes[$type] = "{$type} (Under Maintenance)";
                                continue;
                            }

                            // Get all plates for this type
                            $plates = \App\Models\Vehicle::where('brand', 'like', '%' . $type . '%')
                                ->orWhere('model', 'like', '%' . $type . '%')
                                ->pluck('plate_number')
                                ->toArray();

                            // 1. Check if vehicle is active on the road right now (On Trip)
                            $isActiveNow = \App\Models\TripTicket::where('status', 'active')
                                ->whereIn('vehicle', $plates)
                                ->exists();

                            $isToday = $date ? \Carbon\Carbon::parse($date)->isToday() : true;

                            if ($isActiveNow && $isToday) {
                                $vehicleTypes[$type] = "{$type} (Currently On Trip)";
                                continue;
                            }

                            // 2. Check if scheduled on this specific date
                            if ($date && !empty($plates)) {
                                $isScheduled = \App\Models\TripTicket::where('status', 'active')
                                    ->whereIn('vehicle', $plates)
                                    ->whereHas('vehicleRequest', function ($q) use ($date) {
                                        $q->where('date', $date);
                                    })
                                    ->exists();

                                if ($isScheduled) {
                                    $vehicleTypes[$type] = "{$type} (Busy on this date)";
                                }
                            }
                        }

                        \Illuminate\Support\Facades\Log::info('Vehicle Options Evaluated', [
                            'date' => $date,
                            'resolved_options' => $vehicleTypes
                        ]);

                        return $vehicleTypes;
                    })
                    ->disableOptionWhen(function (string $value, Get $get) {
                        $date = $get('date');

                        // Check if under maintenance
                        $isMaintenance = \App\Models\Vehicle::where('status', 'maintenance')
                            ->where(function ($q) use ($value) {
                                $q->where('brand', 'like', '%' . $value . '%')
                                  ->orWhere('model', 'like', '%' . $value . '%');
                            })
                            ->exists();

                        if ($isMaintenance) {
                            return true;
                        }

                        // Get plates
                        $plates = \App\Models\Vehicle::where('brand', 'like', '%' . $value . '%')
                            ->orWhere('model', 'like', '%' . $value . '%')
                            ->pluck('plate_number')
                            ->toArray();

                        // Check if active today
                        $isActiveNow = \App\Models\TripTicket::where('status', 'active')
                            ->whereIn('vehicle', $plates)
                            ->exists();

                        $isToday = $date ? \Carbon\Carbon::parse($date)->isToday() : true;

                        if ($isActiveNow && $isToday) {
                            return true;
                        }

                        // Check if scheduled
                        if ($date && !empty($plates)) {
                            $isScheduled = \App\Models\TripTicket::where('status', 'active')
                                ->whereIn('vehicle', $plates)
                                ->whereHas('vehicleRequest', function ($q) use ($date) {
                                    $q->where('date', $date);
                                })
                                ->exists();

                            if ($isScheduled) {
                                return true;
                            }
                        }

                        return false;
                    })
                    ->label('Requested Vehicle Type')
                    ->helperText('Select the type of vehicle you prefer for this trip.')
                    ->rules([
                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $date = $get('date');
                            
                            $isMaintenance = \App\Models\Vehicle::where('status', 'maintenance')
                                ->where(function ($q) use ($value) {
                                    $q->where('brand', 'like', '%' . $value . '%')
                                      ->orWhere('model', 'like', '%' . $value . '%');
                                })
                                ->exists();

                            if ($isMaintenance) {
                                $fail("The preferred vehicle type '{$value}' is currently under maintenance. Please select another vehicle.");
                            }

                            // Get plates
                            $plates = \App\Models\Vehicle::where('brand', 'like', '%' . $value . '%')
                                ->orWhere('model', 'like', '%' . $value . '%')
                                ->pluck('plate_number')
                                ->toArray();

                            // Check active now
                            $isActiveNow = \App\Models\TripTicket::where('status', 'active')
                                ->whereIn('vehicle', $plates)
                                ->exists();

                            $isToday = $date ? \Carbon\Carbon::parse($date)->isToday() : true;

                            if ($isActiveNow && $isToday) {
                                $fail("The preferred vehicle type '{$value}' is currently active on a trip. Please select another vehicle.");
                            }

                            // Check scheduled
                            if ($date && !empty($plates)) {
                                $isScheduled = \App\Models\TripTicket::where('status', 'active')
                                    ->whereIn('vehicle', $plates)
                                    ->whereHas('vehicleRequest', function ($q) use ($date) {
                                        $q->where('date', $date);
                                    })
                                    ->exists();

                                if ($isScheduled) {
                                    $fail("The preferred vehicle type '{$value}' is already busy on the selected travel date.");
                                }
                            }
                        }
                    ])
                    ->required(),
                Fieldset::make('Destination Address')
                    ->schema([
                        Select::make('region_code')
                            ->label('Region')
                            ->options(\App\Services\PhilippineAddressService::getRegions())
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set) {
                                $set('province_code', null);
                                $set('city_code', null);
                                $set('brgy_code', null);
                                $set('destination', null);
                            })
                            ->required(),
                        Select::make('province_code')
                            ->label('Province')
                            ->options(fn (Get $get) => \App\Services\PhilippineAddressService::getProvinces($get('region_code')))
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set) {
                                $set('city_code', null);
                                $set('brgy_code', null);
                                $set('destination', null);
                            })
                            ->disabled(fn (Get $get) => empty($get('region_code')))
                            ->required(),
                        Select::make('city_code')
                            ->label('City/Municipality')
                            ->options(fn (Get $get) => \App\Services\PhilippineAddressService::getCities($get('province_code')))
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set) {
                                $set('brgy_code', null);
                                $set('destination', null);
                            })
                            ->disabled(fn (Get $get) => empty($get('province_code')))
                            ->required(),
                        Select::make('brgy_code')
                            ->label('Barangay')
                            ->options(fn (Get $get) => \App\Services\PhilippineAddressService::getBarangays($get('city_code')))
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $regionName = \App\Services\PhilippineAddressService::getRegions()[$get('region_code')] ?? '';
                                $provinceName = \App\Services\PhilippineAddressService::getProvinces($get('region_code'))[$get('province_code')] ?? '';
                                $cityName = \App\Services\PhilippineAddressService::getCities($get('province_code'))[$get('city_code')] ?? '';
                                $brgyName = \App\Services\PhilippineAddressService::getBarangays($get('city_code'))[$get('brgy_code')] ?? '';
                                
                                $addressParts = array_filter([$regionName, $provinceName, $cityName, $brgyName, $get('street_name')]);
                                $set('destination', implode(', ', $addressParts));
                            })
                            ->disabled(fn (Get $get) => empty($get('city_code')))
                            ->required(),
                        TextInput::make('street_name')
                            ->label('Street/Building/House No.')
                            ->live(onBlur: true)
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $regionName = \App\Services\PhilippineAddressService::getRegions()[$get('region_code')] ?? '';
                                $provinceName = \App\Services\PhilippineAddressService::getProvinces($get('region_code'))[$get('province_code')] ?? '';
                                $cityName = \App\Services\PhilippineAddressService::getCities($get('province_code'))[$get('city_code')] ?? '';
                                $brgyName = \App\Services\PhilippineAddressService::getBarangays($get('city_code'))[$get('brgy_code')] ?? '';
                                
                                $addressParts = array_filter([$regionName, $provinceName, $cityName, $brgyName, $state]);
                                $set('destination', implode(', ', $addressParts));
                            })
                            ->disabled(fn (Get $get) => empty($get('brgy_code')))
                            ->columnSpanFull()
                            ->required(),
                        TextInput::make('destination')
                            ->label('Full Destination Address')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->columnSpanFull()
                            ->afterStateHydrated(function (Set $set, $state) {
                                if (empty($state)) return;
                                $parts = explode(', ', $state);
                                $regionName = $parts[0] ?? null;
                                $provinceName = $parts[1] ?? null;
                                $cityName = $parts[2] ?? null;
                                $brgyName = $parts[3] ?? null;
                                $streetName = isset($parts[4]) ? implode(', ', array_slice($parts, 4)) : null;

                                list($regionCode, $provinceCode, $cityCode, $brgyCode) = \App\Services\PhilippineAddressService::getCodesFromNames(
                                    $regionName, $provinceName, $cityName, $brgyName
                                );

                                $set('region_code', $regionCode);
                                $set('province_code', $provinceCode);
                                $set('city_code', $cityCode);
                                $set('brgy_code', $brgyCode);
                                $set('street_name', $streetName);
                            }),
                    ])
                    ->columns(2),
                Select::make('purpose_select')
                    ->label('Purpose of Travel')
                    ->options([
                        'Meeting' => 'Meeting',
                        'Seminar' => 'Seminar',
                        'Workshop' => 'Workshop',
                        'Conference' => 'Conference',
                        'Official Business' => 'Official Business',
                        'Site Visit' => 'Site Visit',
                        'Delivery' => 'Delivery',
                        'Others' => 'Others',
                    ])
                    ->required()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (callable $set, $state, $record) {
                        if ($record && $record->purpose) {
                            $options = [
                                'Meeting',
                                'Seminar',
                                'Workshop',
                                'Conference',
                                'Official Business',
                                'Site Visit',
                                'Delivery',
                            ];
                            if (in_array($record->purpose, $options)) {
                                $set('purpose_select', $record->purpose);
                            } else {
                                $set('purpose_select', 'Others');
                                $set('purpose_custom', $record->purpose);
                            }
                        }
                    })
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state !== 'Others') {
                            $set('purpose', $state);
                        } else {
                            $set('purpose', '');
                        }
                    }),
                    
                TextInput::make('purpose_custom')
                    ->label('Specify Purpose')
                    ->placeholder('Type your custom purpose here...')
                    ->rules([
                        fn (callable $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            if ($get('purpose_select') === 'Others' && empty(trim($value ?? ''))) {
                                $fail('The Specify Purpose field is required when Others is selected.');
                            }
                        },
                    ])
                    ->required(fn (callable $get) => $get('purpose_select') === 'Others')
                    ->visible(fn (callable $get) => $get('purpose_select') === 'Others')
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->dehydrated(false)
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('purpose', $state);
                    }),

                Hidden::make('purpose')
                    ->dehydrated(true)
                    ->required(),
                DatePicker::make('date')
                    ->label('Travel Date')
                    ->default(now())
                    ->minDate(now()->startOfDay())
                    ->live()
                    ->required(),
                TimePicker::make('time')
                    ->label('Travel Time')
                    ->default(now())
                    ->live()
                    ->required(),
                DatePicker::make('return_date')
                    ->label('Expected Return Date')
                    ->default(now())
                    ->minDate(fn (callable $get) => \Carbon\Carbon::parse($get('date') ?? now())->startOfDay())
                    ->live()
                    ->required(),
                TimePicker::make('return_time')
                    ->label('Expected Return Time')
                    ->default(now())
                    ->live()
                    ->required(),
                \Filament\Forms\Components\Repeater::make('passenger_names')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->placeholder('Passenger Name')
                            ->required(),
                    ])
                    ->label('Passengers')
                    ->default([['name' => '']])
                    ->live()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $names = array_filter(array_map(fn ($item) => trim($item['name'] ?? ''), $state ?? []));
                        $set('number_of_passengers', count($names) ?: 1);
                    })
                    ->required(),
                TextInput::make('number_of_passengers')
                    ->label('Total Passengers')
                    ->numeric()
                    ->default(1)
                    ->readOnly()
                    ->dehydrated()
                    ->required(),
                Hidden::make('status')
                    ->default('pending'),
            ]);
    }
}

