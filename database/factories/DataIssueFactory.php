<?php

namespace Database\Factories;

use App\Models\DataIssue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataIssue>
 */
class DataIssueFactory extends Factory
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
        ];
    }
}
