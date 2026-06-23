<?php

namespace Database\Factories;

use App\Models\GovernanceCriterion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GovernanceCriterion>
 */
class GovernanceCriterionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'weight' => $this->faker->randomFloat(2, 0.5, 3.0),
            'category' => $this->faker->randomElement(['attribute', 'relationship', 'role']),
            'is_active' => true,
            'order' => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * Create an inactive criterion.
     *
     * @return Factory<GovernanceCriterion>
     */
    public function inactive(): Factory
    {
        return $this->state(['is_active' => false]);
    }
}
