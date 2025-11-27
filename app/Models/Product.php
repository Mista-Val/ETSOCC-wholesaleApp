<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'sku',
        'image',
        'stock',
        'status',
        'min_price',
        'max_price',
        'category',
        'unit',
        'destination',
        'remarks',
        'product_package',
        'package_quantity',
        'outlet_price',
        'warehouse_price'
    ];

     public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_id');
    }
}