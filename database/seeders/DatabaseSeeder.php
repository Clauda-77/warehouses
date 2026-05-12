<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\UserRole;

class DatabaseSeeder extends Seeder
{

 
    public function run(): void
    {

 
        // User::factory()->create([
        //     'username' => 'Test',
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => '123',
        //     'role' => 'admin'
 

        $this->call([
            RolePermissionSeeder::class,
 
//            OldCompleteDataSeeder::class,
            MigrateOldDataSeeder::class,
 
            OldCompleteDataSeeder::class,
 
        ]);
    }
}
