<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'role' => 'user',
        'avatar_url' => null,
        'avatar_public_id' => null,
    ];
}
