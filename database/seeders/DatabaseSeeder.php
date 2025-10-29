<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pin;
use App\Models\Result;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create 20 results with different statuses
        $passedResults = Result::factory()->count(15)->passed()->create();
        $failedResults = Result::factory()->count(5)->failed()->create();

        // Create PINs for some results
        // Create unused PINs (fresh, can be used)
        Pin::factory()->count(10)->unused()->create();

        // Create used PINs linked to specific results
        $passedResults->take(5)->each(function ($result) {
            Pin::factory()->used($result)->create();
        });

        // Create some expired PINs
        Pin::factory()->count(3)->expired()->create();

        $this->command->info('Database seeded successfully!');
        $this->command->info('Sample credentials:');
        $this->command->info('Email: test@example.com');
        $this->command->info('Password: password');
    }
}
