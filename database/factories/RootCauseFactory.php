<?php

namespace Database\Factories;

use App\Models\RootCause;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RootCause>
 */
class RootCauseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'dimension' => $this->faker->randomElement(['Process', 'People', 'Tool']),
        ];
    }
}
