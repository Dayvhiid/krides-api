<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model

{
    protected $fillable = [
        //  'email',
        //  'phoneNumber',
        //  'password',
         'firstName',
         'lastName'
    ];
    use HasFactory;
}
