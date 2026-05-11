<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\UserRole;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;


    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'Test',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
            'role' => 'admin'
        ]);
    }
}
