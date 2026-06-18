<?php

namespace Database\Seeders;

use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\Domain;
use Illuminate\Database\Seeder;

class BusinessAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domains = Domain::all();
        $initiatives = DataInitiative::all();

        if ($domains->isEmpty() || $initiatives->isEmpty()) {
            $this->command->info('Skipping BusinessAssetSeeder: Need domains and initiatives first');
            return;
        }

        // Create sample business assets with random domains and initiatives
        $assets = [
            ['name' => 'Customer Master Data', 'definition' => 'Central repository of all customer information'],
            ['name' => 'Product Catalog', 'definition' => 'Comprehensive list of all products and services'],
            ['name' => 'Financial Reports', 'definition' => 'Monthly and quarterly financial statements'],
            ['name' => 'Employee Directory', 'definition' => 'Complete list of all employees and their details'],
            ['name' => 'Inventory System', 'definition' => 'Real-time tracking of inventory levels'],
            ['name' => 'Sales Pipeline', 'definition' => 'Tracking of potential sales opportunities'],
            ['name' => 'Website Analytics', 'definition' => 'Visitor and usage data for company websites'],
            ['name' => 'CRM System', 'definition' => 'Customer relationship management platform'],
            ['name' => 'Payroll System', 'definition' => 'Employee compensation and benefits processing'],
            ['name' => 'Project Management', 'definition' => 'Tracking of projects and their deliverables'],
        ];

        foreach ($assets as $asset) {
            BusinessAsset::firstOrCreate(
                ['name' => $asset['name']],
                [
                    'definition' => $asset['definition'],
                    'domain_id' => $domains->random()->id,
                    'data_initiative_id' => $initiatives->random()->id,
                ]
            );
        }
    }
}
