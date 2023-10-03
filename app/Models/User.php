<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    public static function isAdmin()
    {
        if(Auth::check()){
            $user = Auth::user();
            return UserRole::where('user_id', $user->id)->where('role_id', 1)->exists();
        }else{
            return false;
        }
    }
    public static function isAgent()
    {
        if(Auth::check()){
            $user = Auth::user();
            return UserRole::where('user_id', $user->id)->where('role_id', 2)->exists();
        }else{
            return false;
        }
    }
}
