<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDeposit extends Model
{
    use HasFactory;
      protected $fillable = [
        'amount',
        'bank_name',
        'deposit_date',
        'depositor_name',
        'reference_number',
        'remarks',
        'supervisor_id'
    ];
}
