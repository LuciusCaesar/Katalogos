<?php

namespace Database\Seeders;

use App\Models\GovernanceCriterion;
use Illuminate\Database\Seeder;

class GovernanceCriterionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            [
                'key' => 'has_name',
                'name' => 'Has Name',
                'description' => 'Business Asset has a name',
                'weight' => 1.0,
                'category' => 'attribute',
                'order' => 1,
            ],
            [
                'key' => 'has_definition',
                'name' => 'Has Definition',
                'description' => 'Business Asset has a definition',
                'weight' => 2.0,
                'category' => 'attribute',
                'order' => 2,
            ],
            [
                'key' => 'has_domain',
                'name' => 'Has Domain',
                'description' => 'Business Asset has a domain assigned',
                'weight' => 1.5,
                'category' => 'relationship',
                'order' => 3,
            ],
            [
                'key' => 'has_data_initiative',
                'name' => 'Has Data Initiative',
                'description' => 'Business Asset has a data initiative',
                'weight' => 1.5,
                'category' => 'relationship',
                'order' => 4,
            ],
            [
                'key' => 'has_business_rule',
                'name' => 'Has Business Rule',
                'description' => 'Business Asset has at least one business rule',
                'weight' => 2.0,
                'category' => 'relationship',
                'order' => 5,
            ],
            [
                'key' => 'has_data_source',
                'name' => 'Has Data Source',
                'description' => 'Business Asset has at least one data source',
                'weight' => 1.5,
                'category' => 'relationship',
                'order' => 6,
            ],
            [
                'key' => 'has_data_steward',
                'name' => 'Has Data Steward',
                'description' => 'Business Asset has a data steward assigned',
                'weight' => 2.5,
                'category' => 'role',
                'order' => 7,
            ],
            [
                'key' => 'has_data_owner',
                'name' => 'Has Data Owner',
                'description' => 'Business Asset has a data owner assigned',
                'weight' => 2.5,
                'category' => 'role',
                'order' => 8,
            ],
        ];

        foreach ($criteria as $criterion) {
            GovernanceCriterion::firstOrCreate(
                ['key' => $criterion['key']],
                $criterion
            );
        }
    }
}
