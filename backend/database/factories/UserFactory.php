<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'profile_picture' => 'default.png',
            'password' => bcrypt('password'),
            'role' => 'user',
        ];
    }

    public function admin(): static
    {
        return $this->state([
            'role' => 'admin',
        ]);
    }

    public function moderator(): static
    {
        return $this->state([
            'role' => 'moderator',
        ]);
    }
}
