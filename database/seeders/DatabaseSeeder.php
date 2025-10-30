<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pin;
use App\Models\Result;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create sample users
        User::create([
            'name' => 'Demo Student',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
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
        $this->command->info('');
        $this->command->info('👤 Sample Users Created:');
        $this->command->info('   Email: student@example.com | Password: password');
        $this->command->info('   Email: test@example.com | Password: password123');
        $this->command->info('');
        $this->command->info('📊 Created 20 exam results (15 passed, 5 failed)');
        $this->command->info('🔑 Created 10 unused PINs, 5 used PINs, and 3 expired PINs');
    }
}
