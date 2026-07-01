<?php

namespace Database\Factories;

use App\Models\BusinessObjective;
use App\Models\KPI;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KPI>
 */
class KPIFactory extends Factory
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
            'business_objective_id' => BusinessObjective::factory(),
        ];
    }
}
