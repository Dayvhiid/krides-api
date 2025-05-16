<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
     protected $fillable = [
        'user_id',
        'transaction_id',
        'tx_ref',
        'reference',
        'amount',
        'status',
        'description', // if you have a 'type' field like 'credit' or 'debit'
    ];
}
