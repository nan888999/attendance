<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    // メールアドレスを確認済にする
    public function markEmailAsVerified() {
        $this->email_verified_at = now();
        $this->save();
    }

    public function sendEmailVerificationNotification() {
        $this->notify(new CustomVerifyEmail);
    }

    protected $fillable = [
        'name', 'email', 'password',
        'email_verified', 'email_verify_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
