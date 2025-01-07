<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    protected $fillable = [
        'google_id',
        'name',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        'outlet',
        'ride',
        'vehicle_id',
        'phone',
        'lastName',
        'firstName',
        'verification_code'
    ];
}
