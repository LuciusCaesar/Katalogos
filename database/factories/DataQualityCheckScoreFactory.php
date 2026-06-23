<?php

namespace Database\Factories;

use App\Models\DataQualityCheck;
use App\Models\DataQualityCheckScore;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataQualityCheckScore>
 */
class DataQualityCheckScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rowsPassed = $this->faker->numberBetween(0, 1000);
        $rowsFailed = $this->faker->numberBetween(0, 1000);
        $totalRows = $rowsPassed + $rowsFailed;
        $score = $totalRows > 0 ? $rowsPassed / $totalRows : 0;

        return [
            'data_quality_check_id' => DataQualityCheck::factory(),
            'rows_passed' => $rowsPassed,
            'rows_failed' => $rowsFailed,
            'total_rows' => $totalRows,
            'score' => $score,
            'origin_type' => 'user',
            'origin_id' => User::factory(),
            'origin_name' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function forDataQualityCheck(DataQualityCheck $dataQualityCheck): static
    {
        return $this->for($dataQualityCheck);
    }

    public function system(): static
    {
        return $this->state([
            'origin_type' => 'system',
            'origin_id' => null,
            'origin_name' => 'System',
        ]);
    }

    public function scheduled(): static
    {
        return $this->state([
            'origin_type' => 'scheduled',
            'origin_id' => null,
            'origin_name' => 'Scheduled Job',
        ]);
    }
}
