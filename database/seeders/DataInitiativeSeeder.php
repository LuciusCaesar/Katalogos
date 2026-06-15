<?php

namespace Database\Seeders;

use App\Models\DataInitiative;
use Illuminate\Database\Seeder;

class DataInitiativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DataInitiative::factory()->count(5)->create();
    }
}
