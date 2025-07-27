<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Otar Chekurishvili',
            'email' => 'otar@hey.com',
        ]);

        User::factory(10)->create();
    }
}
