<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens;
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
];
}

