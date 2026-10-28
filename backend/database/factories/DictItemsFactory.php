<?php

namespace Database\Factories;

use App\Models\DictItems;
use App\Models\DictLists;
use Illuminate\Database\Eloquent\Factories\Factory;

class DictItemsFactory extends Factory
{
    protected $model = DictItems::class;
    
    public function definition(): array
    {
        return [
            'list_id' => DictLists::factory(),
            'word' => fake()->word(),
            'translation' => fake()->word(),
            'mastered' => false,
        ];
    }
}
