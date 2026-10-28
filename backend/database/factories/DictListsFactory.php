<?php

namespace Database\Factories;

use App\Models\DictLists;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DictListsFactory extends Factory
{
    protected $model = DictLists::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => 'custom',
            'name' => fake()->unique()->words(2, true),
            'source_lang' => 'en',
            'target_lang' => 'ru',
            'icon' => 'book',
        ];
    }
}
