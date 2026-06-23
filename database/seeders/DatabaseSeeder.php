<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Essential seeders - run in all environments (production, staging, development)
        $this->call([
            RoleSeeder::class,
            DomainSeeder::class,
            GovernanceCriterionSeeder::class,
        ]);

        // Development/Testing seeders - only run in local and testing environments
        if (app()->environment('local', 'testing')) {
            $this->call([
                DataInitiativeSeeder::class,
                BusinessAssetSeeder::class,
            ]);
        }

        // Create a default user for development/testing
        // In production, users should be created through registration
        if (app()->environment('local', 'testing')) {
            User::firstOrCreate([
                'email' => 'test@example.com',
            ], [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]);
        }
    }
}
