<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'sale_id',
        'customer_id',
        'per_unit_amount',
        'total_product_amount',
        'quantity',
        'product_id'
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
     public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
