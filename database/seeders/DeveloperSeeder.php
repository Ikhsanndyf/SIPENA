<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = config('sipena.developer.password');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException(
                'DEVELOPER_PASSWORD belum dikonfigurasi.'
            );
        }

        User::updateOrCreate(
            [
                'email' => config('sipena.developer.email'),
            ],
            [
                'name' => config('sipena.developer.name'),
                'password' => $password,
                'role' => UserRole::Developer,
            ],
        );
    }
}
