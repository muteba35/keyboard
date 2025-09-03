<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ← AJOUT ICI

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable; // ← AJOUT HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'otp',
        'otp_expires_at',
        'otp_verified_at',
        'last_ip',
        'browser',
        'last_login_at',
        'user_agent',
        'password_changed_at',
        'remember_token' ,
         'is_blocked', 
         'failed_attempts',
         'last_failed_attempt_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'otp_verified_at' => 'datetime',
            'last_login_at'=> 'datetime',
            'is_blocked' => 'boolean',
            'last_failed_attempt_at'=> 'datetime',
        ];
    }
}