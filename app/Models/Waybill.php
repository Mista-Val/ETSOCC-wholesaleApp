<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waybill extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'waybills';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'location_id', 
        'waybill_number',
        'loading_date',
        'estimated_delivery_date',
        'warehouse_name',
        'loader_name',
        'loader_position',
        'outlet_id',         
        'number_of_packages',
        'quantity',
        'receiver_name',
        'receiver_position',
        'shipping_remarks',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'loading_date' => 'date',
        'estimated_delivery_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sale that owns the waybill
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the outlet location
     */
    public function outlet()
    {
        return $this->belongsTo(Location::class, 'outlet_id');
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('loading_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by warehouse
     */
    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->whereHas('sale', function($q) use ($warehouseId) {
            $q->where('location_id', $warehouseId);
        });
    }

    /**
     * Check if waybill is delivered
     */
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if waybill is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if waybill is in transit
     */
    public function isInTransit()
    {
        return $this->status === 'in_transit';
    }

    /**
     * Get formatted loading date
     */
    public function getFormattedLoadingDateAttribute()
    {
        return $this->loading_date->format('F d, Y');
    }

    /**
     * Get formatted delivery date
     */
    public function getFormattedDeliveryDateAttribute()
    {
        return $this->estimated_delivery_date->format('F d, Y');
    }
}