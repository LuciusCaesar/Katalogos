<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
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
            'description' => $this->faker->sentence(),
        ];
    }

    /**
     * Create a Data Steward role.
     */
    public function dataSteward(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Data Steward',
            'description' => 'Responsible for managing and overseeing data within an initiative or asset',
        ]);
    }

    /**
     * Create a Data Owner role.
     */
    public function dataOwner(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Data Owner',
            'description' => 'Ultimately accountable for the data within an initiative or asset',
        ]);
    }

    /**
     * Create a Data Custodian role.
     */
    public function dataCustodian(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Data Custodian',
            'description' => 'Responsible for the safe custody, transport, and storage of data',
        ]);
    }
}
