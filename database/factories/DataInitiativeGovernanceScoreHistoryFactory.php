<?php

namespace Database\Factories;

use App\Models\DataInitiative;
use App\Models\DataInitiativeGovernanceScoreHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataInitiativeGovernanceScoreHistory>
 */
class DataInitiativeGovernanceScoreHistoryFactory extends Factory
{
    protected $model = DataInitiativeGovernanceScoreHistory::class;

    public function definition(): array
    {
        return [
            'data_initiative_id' => DataInitiative::factory(),
            'score' => $this->faker->randomFloat(8, 0, 1),
            'event' => 'Test event',
            'calculated_at' => now(),
        ];
    }
}
