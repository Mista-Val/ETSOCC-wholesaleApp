<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'value',
        'slug',
        'type',
        'category_type',
        'created_at',
        'updated_at'
    ];

    public function getValueAttribute($value)
    {   
        if($this->type === 'file'){
            if ($value) {
                $folder = $this->slug ==='favicon'?'favicon':'logo';
                $existsFile = asset('uploads/'.$folder.'/' . $value);
                $baseUrl = asset('');
                $publicPath = public_path('/');
                $url = str_replace($baseUrl, $publicPath, $existsFile);
                if (file_exists($url)) {
                    return asset('uploads/'.$folder.'/' . $value);
                }
                return asset('admin/img/user_no_image.png');
            }
        }else{
            return $value;
        }
        return $value;
    }
}
