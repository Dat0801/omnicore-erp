<?php

namespace Database\Seeders;

use App\Enums\Role;
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
        // Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@omnicore.app',
            'role' => Role::ADMIN,
        ]);

        // Staff User
        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@omnicore.app',
            'role' => Role::STAFF,
        ]);

        $this->call([
            ProductSeeder::class,
            InventorySeeder::class,
        ]);
    }
}
