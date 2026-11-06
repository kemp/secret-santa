<?php

namespace Database\Factories;

use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Party>
 */
class PartyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_token' => Str::random(8),
            'year' => now()->year,
            'deadline' => null,
        ];
    }
}
