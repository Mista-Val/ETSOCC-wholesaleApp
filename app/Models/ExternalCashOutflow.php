<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalCashOutflow extends Model
{
    use HasFactory;

    /**
     * The table associated with the model, matching your specified name.
     *
     * @var string
     */
    protected $table = 'external_cash_outflows';

    /**
     * The attributes that are mass assignable, matching the form fields.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'source',             // Maps to 'Source'
        'amount',             // Maps to 'Amount'
        'date',      // Maps to 'Received Date'
        'send_to',      // Maps to 'Received From'
        'remarks',    // Maps to 'Remarks'
        'supervisor_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

     public function supervisor()
    {
        // Assuming the foreign key is 'supervisor_id' and the related table is the User model
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
