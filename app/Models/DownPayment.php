<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownPayment extends Model
{
    use HasFactory;
      protected $table    = 'customer_payments';
      protected $fillable = [
        'location_id',
        'customer_id',
        'amount',
        'type', // Using 'type' here, not 'role', so we'll fix the Livewire component
        'date',
        'remarks',
        'payment_method',
    ];

    /**
     * Get the customer that made the payment.
     */
    public function coustomer(): BelongsTo
    {
      return $this->belongsTo(Customer::class,'customer_id');
    }

    /**
     * Get the location where the payment was made.
     */
    public function location(): BelongsTo
    {
      // Assuming 'location_id' links to the Location model
      return $this->belongsTo(Location::class, 'location_id');
    }
}