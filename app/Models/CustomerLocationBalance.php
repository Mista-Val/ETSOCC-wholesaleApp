<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerLocationBalance extends Model
{
    use HasFactory;
    
    // Explicitly set the table name
    protected $table = 'customer_location_balances';

    protected $fillable = [
        'customer_id',
        'location_id',
        'balance',
        'credit_balance'
    ];

    /**
     * Get the customer associated with this balance record.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the location (outlet/warehouse) where this balance is held.
     */
    public function location(): BelongsTo
    {
        // Assuming your outlets/warehouses are represented by a Location model
        return $this->belongsTo(Location::class); 
    }
}