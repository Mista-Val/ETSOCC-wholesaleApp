<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitCollection extends Model
{
    use HasFactory;
      protected $table    = 'customer_payments';
      protected $fillable = [
        'location_id',
        'customer_id',
        'amount',
        'type',
        'date',
        'remarks',
        'payment_method',
    ];

    public function coustomer(){
      return $this->belongsTo(Customer::class,'customer_id');
    }
}
