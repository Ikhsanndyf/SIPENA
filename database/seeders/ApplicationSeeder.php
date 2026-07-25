<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = [
            [
                'name' => 'Sistem Kepegawaian',
                'slug' => 'sistem-kepegawaian',
            ],
            [
                'name' => 'Sistem Persuratan',
                'slug' => 'sistem-persuratan',
            ],
            [
                'name' => 'Dashboard Monitoring',
                'slug' => 'dashboard-monitoring',
            ],
        ];

        foreach ($applications as $application) {
            Application::updateOrCreate(
                ['slug' => $application['slug']],
                ['name' => $application['name']],
            );
        }
    }
}
