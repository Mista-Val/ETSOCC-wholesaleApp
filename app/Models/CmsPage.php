<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'meta_description',
        'meta_keywords',
        'status',
        'slug',
        'created_at',
        'updated_at',
    ];

    public function getImageAttribute($value)
    {
        if ($value) {
            $existsFile = asset('uploads/cms-page/' . $value);
            $baseUrl = asset('');
            $publicPath = public_path('/');
            $url = str_replace($baseUrl, $publicPath, $existsFile);
            if (file_exists($url)) {
                return asset('uploads/cms-page/' . $value);
            }
            return asset('admin/img/user_no_image.png');
        }
        return asset('admin/img/user_no_image.png');
    }
}
