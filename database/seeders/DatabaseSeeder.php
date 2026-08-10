<?php

namespace Database\Seeders;

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
        // Idempotent admin account, driven by env so it can be seeded safely on
        // every deploy (including free tiers where the DB resets on each build).
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ],
        );
    }
}
