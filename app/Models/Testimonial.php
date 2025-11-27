<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'profile_image',
        'feedback',
        'location',
        'status',
        'created_at',
        'updated_at',
    ];

    public function getProfileImageAttribute($value)
    {
        if ($value) {
            $existsFile = asset('uploads/testimonial/' . $value);
            $baseUrl = asset('');
            $publicPath = public_path('/');
            $url = str_replace($baseUrl, $publicPath, $existsFile);
            if (file_exists($url)) {
                return asset('uploads/testimonial/' . $value);
            }
            return asset('admin/img/user_no_image.png');
        }
        return asset('admin/img/user_no_image.png');
    }
}
