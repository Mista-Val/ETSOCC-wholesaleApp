<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalCashInflow extends Model
{
    use HasFactory;

    /**
     * The table associated with the model, matching your specified name.
     *
     * @var string
     */
    protected $table = 'external_cash_inflows';

    /**
     * The attributes that are mass assignable, matching the form fields.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supervisor_id',
        'source',             // Maps to 'Source'
        'amount',             // Maps to 'Amount'
        'received_date',      // Maps to 'Received Date'
        'received_from',      // Maps to 'Received From'
        'remarks'        // Maps to 'Remarks'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'received_date' => 'date',
    ];
    public function supervisor()
    {
        // Assuming the foreign key is 'supervisor_id' and the related table is the User model
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
