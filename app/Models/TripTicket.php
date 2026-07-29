<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'vehicle_request_id',
        'driver_id',
        'vehicle',
        'status',
        'document',
    ];

    protected static function booted(): void
    {
        static::created(function ($tripTicket) {
            $driverName = $tripTicket->driver?->name ?? 'N/A';
            \App\Models\ActivityLog::log('Approved & Ticketed', $tripTicket, "Created trip ticket {$tripTicket->ticket_number} for request {$tripTicket->vehicleRequest?->request_number}. Assigned Driver: {$driverName}. Vehicle: {$tripTicket->vehicle}");
        });

        static::creating(function ($tripTicket) {
            $tripTicket->loadMissing('vehicleRequest');
            if ($tripTicket->vehicleRequest?->document) {
                $tripTicket->document = $tripTicket->vehicleRequest->document;
            }

            if ($tripTicket->document) {
                $tripTicket->status = 'active';
            } else {
                $tripTicket->status = 'pending';
            }
        });

        static::saving(function ($tripTicket) {
            // Check if driver changed
            if ($tripTicket->isDirty('driver_id')) {
                $oldDriverId = $tripTicket->getOriginal('driver_id');
                if ($oldDriverId) {
                    $oldDriver = Driver::find($oldDriverId);
                    if ($oldDriver) {
                        $oldDriver->update(['status' => 'available']);
                    }
                }
            }

            $tripTicket->loadMissing('vehicleRequest');
            if ($tripTicket->vehicleRequest?->document && !$tripTicket->document) {
                $tripTicket->document = $tripTicket->vehicleRequest->document;
            }

            // Set active if document uploaded
            if ($tripTicket->document && $tripTicket->status === 'pending') {
                $tripTicket->status = 'active';
            }
        });

        static::saved(function ($tripTicket) {
            // Log status transitions
            if ($tripTicket->wasChanged('status')) {
                if ($tripTicket->status === 'active') {
                    \App\Models\ActivityLog::log('Started Trip', $tripTicket, "Trip {$tripTicket->ticket_number} started (On Trip).");
                } elseif ($tripTicket->status === 'completed') {
                    \App\Models\ActivityLog::log('Completed Trip', $tripTicket, "Trip {$tripTicket->ticket_number} marked as completed.");
                } elseif ($tripTicket->status === 'cancelled') {
                    \App\Models\ActivityLog::log('Cancelled Trip', $tripTicket, "Trip {$tripTicket->ticket_number} was cancelled.");
                }
            }

            // Update driver status based on trip ticket status
            if ($tripTicket->driver_id) {
                $driver = Driver::find($tripTicket->driver_id);
                if ($driver) {
                    if ($tripTicket->status === 'active') {
                        $driver->update(['status' => 'on_trip']);
                    } else {
                        // Check if driver has any other active trip tickets
                        $hasOtherActiveTrip = TripTicket::where('driver_id', $driver->id)
                            ->where('status', 'active')
                            ->where('id', '!=', $tripTicket->id)
                            ->exists();

                        if (!$hasOtherActiveTrip) {
                            $driver->update(['status' => 'available']);
                        }
                    }
                }
            }

            // Sync Vehicle Request status quietly to prevent recursive saved events
            $tripTicket->loadMissing('vehicleRequest');
            if ($tripTicket->vehicleRequest) {
                if ($tripTicket->status === 'active') {
                    $tripTicket->vehicleRequest->updateQuietly(['status' => 'on_trip']);
                } elseif ($tripTicket->status === 'completed') {
                    $tripTicket->vehicleRequest->updateQuietly(['status' => 'completed']);
                } elseif ($tripTicket->status === 'cancelled') {
                    $tripTicket->vehicleRequest->updateQuietly(['status' => 'rejected']);
                } else {
                    $tripTicket->vehicleRequest->updateQuietly(['status' => 'approved']);
                }
            }

            // If document was just added and it is active, send SMS notification
            if ($tripTicket->wasChanged('document') && $tripTicket->document && $tripTicket->status === 'active') {
                $tripTicket->sendSmsNotification();
            }
        });

        static::deleted(function ($tripTicket) {
            \App\Models\ActivityLog::log('Deleted Ticket', $tripTicket, "Deleted trip ticket {$tripTicket->ticket_number}");
            if ($tripTicket->driver_id) {
                $driver = Driver::find($tripTicket->driver_id);
                if ($driver) {
                    // Check if driver has any other active trip tickets
                    $hasOtherActiveTrip = TripTicket::where('driver_id', $driver->id)
                        ->where('status', 'active')
                        ->exists();

                    if (!$hasOtherActiveTrip) {
                        $driver->update(['status' => 'available']);
                    }
                }
            }

            $tripTicket->loadMissing('vehicleRequest');
            if ($tripTicket->vehicleRequest) {
                $tripTicket->vehicleRequest->update(['status' => 'pending']);
            }
        });

        static::created(function ($tripTicket) {
            $tripTicket->loadMissing(['driver', 'vehicleRequest']);

            if ($tripTicket->vehicleRequest) {
                $tripTicket->vehicleRequest->update(['status' => 'approved']);
            }

            if ($tripTicket->document && $tripTicket->status === 'active') {
                $tripTicket->sendSmsNotification();
            }
        });
    }

    public function sendSmsNotification(): void
    {
        $this->loadMissing(['driver', 'vehicleRequest']);

        if ($this->driver) {
            $driverName = $this->driver->name;
            $driverPhone = $this->driver->contact_number ?? '+63 917 000 0000';
            $driverLicense = $this->driver->license_number;
            
            $destination = $this->vehicleRequest?->destination ?? 'N/A';
            $date = $this->vehicleRequest?->date 
                ? \Carbon\Carbon::parse($this->vehicleRequest->date)->format('F d, Y') 
                : 'N/A';
            $time = $this->vehicleRequest?->time 
                ? \Carbon\Carbon::parse($this->vehicleRequest->time)->format('g:i A') 
                : 'N/A';
            
            $vehicleInfo = $this->vehicle ?? 'N/A';
            
            $passenger = $this->vehicleRequest?->employee_name ?? 'N/A';
            
            $smsMessage = "Hi {$driverName}!\n"
                        . "You have a NEW TRIP ASSIGNED:\n"
                        . "Trip ID: {$this->ticket_number}\n"
                        . "Destination: {$destination}\n"
                        . "Date: {$date}\n"
                        . "Time: {$time}\n"
                        . "Vehicle: {$vehicleInfo}\n"
                        . "Passenger: {$passenger}\n"
                        . "Please check PeliCle portal\n"
                        . "using License ID: {$driverLicense}";

            // Send SMS via our SMS Service (handles Semaphore API and database logs)
            \App\Services\SmsService::send($this->driver, $smsMessage);
        }
    }

    public function vehicleRequest(): BelongsTo
    {
        return $this->belongsTo(VehicleRequest::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }



    public function withdrawalSlips(): HasMany
    {
        return $this->hasMany(WithdrawalSlip::class);
    }
}
