<?php

namespace Database\Factories;

use App\Models\BusinessAsset;
use App\Models\GovernanceScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GovernanceScore>
 */
class GovernanceScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_asset_id' => BusinessAsset::factory(),
            'score' => $this->faker->randomFloat(4, 0, 1),
            'max_possible_score' => $this->faker->randomFloat(2, 5, 20),
            'criteria_results' => ['has_name' => true, 'has_definition' => true],
            'criteria_weights' => ['has_name' => 1.0, 'has_definition' => 2.0],
            'changes' => ['action' => 'created'],
            'calculated_at' => now(),
        ];
    }

    /**
     * Create a perfect score.
     */
    public function perfect(): Factory
    {
        return $this->state(['score' => 1.0]);
    }

    /**
     * Create a zero score.
     */
    public function zero(): Factory
    {
        return $this->state(['score' => 0.0]);
    }
}
