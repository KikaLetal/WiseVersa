<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    use HasFactory;
	
	protected $fillable=['author_id', 'content', 'preview'];

    public function likes(): HasMany
    {
        return $this->hasMany(BlogLike::class, 'post_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function likedByUsers(){
        return $this->belongsToMany(
            User::class,
            'blog_likes',
            'post_id',
            'user_id'
        );
    }
}
