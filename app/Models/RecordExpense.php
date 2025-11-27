<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordExpense extends Model
{
    use HasFactory;
       protected $table    = 'expense_logs';
      protected $fillable = [
        'location_id',
        'amount',
        'purpose',
        'receiver_id',
        'remarks',
        'status',
        'approval_status'
    ];

      public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

}
