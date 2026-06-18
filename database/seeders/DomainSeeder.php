<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Domain::firstOrCreate([
            'name' => 'Finance',
        ], [
            'description' => 'Financial data and reporting systems',
        ]);

        Domain::firstOrCreate([
            'name' => 'Human Resources',
        ], [
            'description' => 'Employee and personnel data',
        ]);

        Domain::firstOrCreate([
            'name' => 'Operations',
        ], [
            'description' => 'Operational and business process data',
        ]);

        Domain::firstOrCreate([
            'name' => 'Technology',
        ], [
            'description' => 'IT systems and technical infrastructure data',
        ]);

        Domain::firstOrCreate([
            'name' => 'Sales & Marketing',
        ], [
            'description' => 'Customer and marketing data',
        ]);
    }
}
