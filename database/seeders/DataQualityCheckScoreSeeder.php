<?php

namespace Database\Seeders;

use App\Models\BusinessRule;
use App\Models\DataQualityCheck;
use App\Models\DataQualityCheckScore;
use App\Models\User;
use Illuminate\Database\Seeder;

class DataQualityCheckScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $users = User::factory()->count(3)->create();
        $businessRules = BusinessRule::factory()->count(2)->create();

        foreach ($businessRules as $businessRule) {
            $dqcs = DataQualityCheck::factory()->count(5)->create([
                'business_rule_id' => $businessRule->id,
            ]);

            foreach ($dqcs as $dqc) {
                DataQualityCheckScore::factory()
                    ->count(rand(3, 5))
                    ->create([
                        'data_quality_check_id' => $dqc->id,
                        'origin_id' => $users->random()->id,
                    ]);
            }
        }
    }
}
