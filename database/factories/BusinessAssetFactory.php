<?php

namespace Database\Factories;

use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessAsset>
 */
class BusinessAssetFactory extends Factory
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
            'definition' => $this->faker->sentence(),
            'data_initiative_id' => DataInitiative::factory(),
            'domain_id' => Domain::factory(),
        ];
    }
}
