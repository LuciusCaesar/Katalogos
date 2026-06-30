<?php

namespace Database\Factories;

use App\Models\BusinessObjective;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessObjective>
 */
class BusinessObjectiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
