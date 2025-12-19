<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category_id',
        'user_id',
        'featured_image_url',
        'featured_image_public_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
     public function comments()
    {
         return $this->morphMany(Comment::class, 'commentable')
                ->whereNull('parent_id');
    }
     protected static function booted()
    {
        static::deleting(function ($post) {
            $post->comments()->delete();
        });
    }
}
