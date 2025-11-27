<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StockTransferRequest extends Model
{
    use HasFactory;
    protected $table    = 'stock_transfer_requests';
    protected $fillable = [
        'supplier_name',
        'supplier_id',
        'receiver_id',
        'type',
        'remark',
        'transfer_type',
        'status',
        'collect_all'
    ];

    // Relationships
    public function warehouse()
    {
        return $this->belongsTo(Location::class,'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(StockTransferRequestsProduct::class, 'transfer_request_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Location::class,'receiver_id');
    }

     public function senderOutlet()
    {
        return $this->belongsTo(Location::class,'supplier_id');
    }

    public function receiverWarehouse()
    {
        return $this->belongsTo(Location::class, 'receiver_id');
    }

    // -----------------------------------------------------------------------
    // --- QUERY SCOPES (FIXED: Qualified Column Names) ---
    // -----------------------------------------------------------------------

    /**
     * Scope to filter RECEIVED stock (where type is 'admin').
     * The column 'type' is now qualified with the table name.
     */
    public function scopeReceivedStock(Builder $query): Builder
    {
        // FIX: Using 'stock_transfer_requests.type'
        return $query->where('stock_transfer_requests.type', 'admin');
    }

    /**
     * Scope to filter TRANSFERRED stock (where type is 'warehouse' or 'outlet' AND transfer_type is 'stock').
     * Columns are now qualified.
     */
    public function scopeTransferredStock(Builder $query): Builder
    {
        // FIX: Using 'stock_transfer_requests.type' and 'stock_transfer_requests.transfer_type'
        return $query->whereIn('stock_transfer_requests.type', ['warehouse', 'outlet'])
                     ->where('stock_transfer_requests.transfer_type', 'stock');
    }

    /**
     * Scope to filter RETURNED stock (where transfer_type is 'return').
     * Column is now qualified.
     */
    public function scopeReturnedStock(Builder $query): Builder
    {
        // FIX: Using 'stock_transfer_requests.transfer_type'
        return $query->where('stock_transfer_requests.transfer_type', 'return');
    }
}
