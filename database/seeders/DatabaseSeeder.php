<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        // Create test user
        User::factory()->create([
            'name' => 'testuser',
            'email' => 'test@example.com',
        ]);

        // Create additional demo users
        User::factory(8)->create();
    }
}
