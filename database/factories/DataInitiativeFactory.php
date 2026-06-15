<?php

namespace Database\Factories;

use App\Models\DataInitiative;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataInitiative>
 */
class DataInitiativeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word(),
            'label' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
