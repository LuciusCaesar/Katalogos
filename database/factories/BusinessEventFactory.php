<?php

namespace Database\Factories;

use App\Models\BusinessEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessEvent>
 */
class BusinessEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->paragraph(),
        ];
    }
}
