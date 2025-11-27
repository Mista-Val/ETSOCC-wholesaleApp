<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferRequestsProduct extends Model
{
    use HasFactory;
    protected $table    = 'stock_transfer_requests_products';
    protected $fillable = [
        'transfer_request_id',
        'product_id',
        'set_quantity',
        'received_quantity',
        'type',
        'remarks',
    ];
    public function request()
    {
        return $this->belongsTo(StockTransferRequest::class, 'transfer_request_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function transferRequest()
    {
        return $this->belongsTo(StockTransferRequest::class, 'transfer_request_id');
    }
}
