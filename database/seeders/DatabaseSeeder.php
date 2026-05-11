<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\UserRole;

class DatabaseSeeder extends Seeder
{

<<<<<<< HEAD

=======
>>>>>>> 3253b6dee7e8eab7aa743eca71bc03b93dd6b718
    public function run(): void
    {

<<<<<<< HEAD
        User::factory()->create([
            'username' => 'Test',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
            'role' => 'admin'
=======

        $this->call([
            RolePermissionSeeder::class,
            OldCompleteDataSeeder::class,
>>>>>>> 3253b6dee7e8eab7aa743eca71bc03b93dd6b718
        ]);
    }
}
