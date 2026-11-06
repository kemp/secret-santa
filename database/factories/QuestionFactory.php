<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'text' => rtrim(fake()->sentence(), '.').'?',
            'year' => null,
            'sort_order' => fake()->numberBetween(1, 1000),
            'is_active' => true,
            'options' => null,
        ];
    }
}
