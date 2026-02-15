<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create base roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        // Sample permissions
        $permissions = [
            'users.view',
            'users.manage',
            'products.view',
            'products.manage',
            'inventory.view',
            'inventory.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo($permissions);
        $managerRole->givePermissionTo([
            'products.view',
            'products.manage',
            'inventory.view',
        ]);
        $staffRole->givePermissionTo([
            'products.view',
            'inventory.view',
        ]);

        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@omnicore.app'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Staff User
        $staff = User::firstOrCreate(
            ['email' => 'staff@omnicore.app'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $staff->assignRole('staff');

        $this->call([
            // ProductSeeder::class,
            // InventorySeeder::class,
        ]);
    }
}
