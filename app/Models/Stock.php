<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
       protected $fillable = [
        'location_id',
        'product_id',
        'product_quantity',
        'type',
    ];
    // WarehouseStock.php
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
