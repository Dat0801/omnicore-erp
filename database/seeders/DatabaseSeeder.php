<?php

namespace Database\Seeders;

use App\Enums\Role;
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
        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@omnicore.app'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => Role::ADMIN,
            ]
        );

        // Staff User
        User::firstOrCreate(
            ['email' => 'staff@omnicore.app'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => Role::STAFF,
            ]
        );

        $this->call([
            ProductSeeder::class,
            InventorySeeder::class,
        ]);
    }
}
