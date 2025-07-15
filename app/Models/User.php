<?php

namespace App\Models;

use App\Enums\UserStatus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Profile;
use App\Models\SocialAccount;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username', 'email', 'phone', 'password', 'status',
        'email_verified_at', 'phone_verified_at', 'last_login_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'status' => UserStatus::class,
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }
}
