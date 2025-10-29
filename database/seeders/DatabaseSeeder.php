<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pin;
use App\Models\Result;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
        $this->command->info('Created 20 exam results (15 passed, 5 failed)');
        $this->command->info('Created 10 unused PINs, 5 used PINs, and 3 expired PINs');
    }
}
