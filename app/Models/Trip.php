<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $fillable = [
        'location',
        'destination',
        'distance',
        'userId',
        'DriverId',
        'paymentStatus',
        'vehicleId',
        'user_id',
        'status',
        'amount',
        'number_of_passengers',
        'rider_name',
        'phone_number',
        'name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
