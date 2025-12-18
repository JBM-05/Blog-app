<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
    ];
 protected $hidden = [
        'created_at',
        'updated_at',
    ];
    /**
     * A category has many posts
     */
    public function posts()
    {
        // return $this->hasMany(Post::class);
    }
}
