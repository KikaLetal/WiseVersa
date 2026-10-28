<?php

namespace Database\Factories;

use App\Models\BlogLike;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogLikeFactory extends Factory
{
    protected $model = BlogLike::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'post_id' => BlogPost::factory(),
        ];
    }
}
