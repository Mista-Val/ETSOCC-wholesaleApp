<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'phone_number', 'balance','address','credit_balance'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function locationBalances()
{
    return $this->hasMany(CustomerLocationBalance::class);
}
}
