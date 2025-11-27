<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'sub_title',
        'banner_image',
        'status',
        'created_at',
        'updated_at',
    ];

    public function getBannerImageAttribute($value)
    {
        if ($value) {
            $existsFile = asset('uploads/banner/' . $value);
            $baseUrl = asset('');
            $publicPath = public_path('/');
            $url = str_replace($baseUrl, $publicPath, $existsFile);
            if (file_exists($url)) {
                return asset('uploads/banner/' . $value);
            }
            return asset('admin/img/user_no_image.png');
        }
        return asset('admin/img/user_no_image.png');
    }
}
