<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRemittance extends Model
{
    use HasFactory;
        protected $table    = 'cash_remittance';
      protected $fillable = [
        'receiver_id',
        'location_id',
        'amount',
        'status',
        'role',
        'remarks',
    ];
    public function coustomer(){
      return $this->belongsTo(User::class,'receiver_id');
    }
     public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
