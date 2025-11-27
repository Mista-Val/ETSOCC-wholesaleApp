<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions;

    // protected $guard_name = 'warehouse';
    protected $guard_name = 'web';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'mobile',
        'dob',
        'gender',
        'role_id',
        'profile_image',
        'otp',
        'email_verify',
        'status',
        'role',
        'balance'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // public function getProfileImageAttribute($value)
    // {
    //     if ($value) {
    //         $existsFile = asset('uploads/profile/' . $value);
    //         $baseUrl = asset('');
    //         $publicPath = public_path('/');
    //         $url = str_replace($baseUrl, $publicPath, $existsFile);
    //         if (file_exists($url)) {
    //             return asset('uploads/profile/' . $value);
    //         }
    //         return asset('admin/img/user_no_image.png');
    //     }
    //     return asset('admin/img/user_no_image.png');
    // }

    public function warehouse()
    {
        return $this->hasOne(Location::class, 'user_id');
    }
    public function outlet()
    {
        return $this->hasOne(Location::class, 'user_id');
    }
}
