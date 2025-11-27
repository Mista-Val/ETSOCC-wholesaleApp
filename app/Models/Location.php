<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable; 

class Location extends Model
{
    use HasFactory,Notifiable;
     protected $fillable = ['name', 'address', 'status','description','user_id','type','balance'];
    

        public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
