<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    use HasFactory;
    protected $fillable = [
        'location',
        'destination',
        'distance',
        'DriverId',
        'paymentStatus',
        'vehicleId',
        'driver_id'
    ];

    public function user(){
         return $this->belongsTo(User::class);
    }
}
