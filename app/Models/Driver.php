<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Driver extends Model
{
    use HasApiTokens, Notifiable;
    protected $fillable = [
       'fullname',
       'phone_number',
       'picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
