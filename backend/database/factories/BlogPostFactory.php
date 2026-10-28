<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory()->admin(),
            'content' => fake()->paragraphs(3, true),
            'preview' => null,
        ];
    }
}
