<?php

namespace Database\Factories;

use App\Models\Participant;
use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Participant>
 */
class ParticipantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'party_id' => Party::factory(),
            'name' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    /**
     * The participant who created the party.
     */
    public function creator(): static
    {
        return $this->state(fn () => ['edit_token' => Str::random(64)]);
    }
}
