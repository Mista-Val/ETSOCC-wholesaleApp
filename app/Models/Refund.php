<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'sale_id',
        'customer_id',
        'location_id',
        'refund_amount',
        'refund_reason',
        'supervisor_id',
        'status'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
     public function saleItems()
    {
        return $this->hasMany(SoldProduct::class);
    }
}
