<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalSlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'slip_number',
        'trip_ticket_id',
        'purpose',
        'requested_items',
        'amount',
        'status',
    ];

    protected $casts = [
        'requested_items' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($withdrawalSlip) {
            if (empty($withdrawalSlip->purpose)) {
                $withdrawalSlip->loadMissing('tripTicket.vehicleRequest');
                $withdrawalSlip->purpose = $withdrawalSlip->tripTicket?->vehicleRequest?->purpose ?? 'Official Business';
            }
            if (empty($withdrawalSlip->requested_items)) {
                $withdrawalSlip->requested_items = [];
            }
        });
    }

    public function tripTicket(): BelongsTo
    {
        return $this->belongsTo(TripTicket::class);
    }
}
