<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DeveloperSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeveloperSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_seeder_creates_one_developer_account(): void
    {
        config()->set('sipena.developer', [
            'name' => 'Developer Test',
            'email' => 'developer@example.com',
            'password' => 'password',
        ]);

        $this->seed(DeveloperSeeder::class);
        $this->seed(DeveloperSeeder::class);

        $developer = User::where(
            'email',
            'developer@example.com'
        )->firstOrFail();

        $this->assertSame(UserRole::Developer, $developer->role);
        $this->assertSame('Developer Test', $developer->name);
        $this->assertTrue(Hash::check('password', $developer->password));
        $this->assertDatabaseCount('users', 1);
    }
}
