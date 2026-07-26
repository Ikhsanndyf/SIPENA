<?php

namespace Tests\Feature;

use Database\Seeders\ApplicationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_seeder_creates_three_applications(): void
    {
        $this->seed(ApplicationSeeder::class);
        $this->seed(ApplicationSeeder::class);

        $this->assertDatabaseCount('applications', 3);

        $this->assertDatabaseHas('applications', [
            'slug' => 'sistem-kepegawaian',
        ]);

        $this->assertDatabaseHas('applications', [
            'slug' => 'sistem-persuratan',
        ]);

        $this->assertDatabaseHas('applications', [
            'slug' => 'dashboard-monitoring',
        ]);
    }
}
