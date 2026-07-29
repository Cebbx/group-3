<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'license_number',
        'contact_number',
        'status',
    ];

    public function tripTickets(): HasMany
    {
        return $this->hasMany(TripTicket::class);
    }
}
