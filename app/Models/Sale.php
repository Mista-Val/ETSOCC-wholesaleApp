<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['location_id', 'customer_id', 'payment_method', 'remark', 'total_amount', 'status', 'type','refund_status'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function soldProducts()
    {
        return $this->hasMany(SoldProduct::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function items()
    {
        return $this->hasMany(SoldProduct::class);
    }
    public function waybill()
    {
        return $this->hasOne(Waybill::class);
    }
    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
