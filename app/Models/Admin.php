<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // IMPORTANT: Use this, not Model
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['username', 'password', 'role'];
    protected $hidden = ['password'];
}