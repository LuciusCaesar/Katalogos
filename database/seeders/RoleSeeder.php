<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'Data Steward',
        ], [
            'description' => 'Responsible for managing and overseeing data within an initiative or asset',
        ]);

        Role::firstOrCreate([
            'name' => 'Data Owner',
        ], [
            'description' => 'Ultimately accountable for the data within an initiative or asset',
        ]);
    }
}
