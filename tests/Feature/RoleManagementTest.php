<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_visit_role_management_page()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.roles.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Role/Index')
                ->has('roles')
                ->has('stats')
            );
    }

    public function test_staff_cannot_visit_role_management_page()
    {
        $staff = User::factory()->create(['role' => Role::STAFF]);

        $this->actingAs($staff)
            ->get(route('admin.roles.index'))
            ->assertStatus(403);
    }

    public function test_roles_are_listed()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.roles.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('roles', 4)
            );
    }

    public function test_stats_are_correct()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        User::factory()->count(2)->create(['role' => Role::MANAGER]);
        User::factory()->count(3)->create(['role' => Role::STAFF]);

        $this->actingAs($admin)
            ->get(route('admin.roles.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->where('stats.admins', 1)
                ->where('stats.managers', 2)
                ->where('stats.staff', 3)
                ->where('stats.permissions', 0)
                ->where('stats.recentPermissionUpdates', 0)
            );
    }

    public function test_admin_can_visit_role_permissions_page()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.roles.permissions.edit', Role::MANAGER->value))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Role/Permissions')
                ->where('role.value', Role::MANAGER->value)
                ->where('role.label', Role::MANAGER->label())
            );
    }

    public function test_staff_cannot_visit_role_permissions_page()
    {
        $staff = User::factory()->create(['role' => Role::STAFF]);

        $this->actingAs($staff)
            ->get(route('admin.roles.permissions.edit', Role::MANAGER->value))
            ->assertStatus(403);
    }

    public function test_admin_can_visit_role_create_page(): void
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.roles.create'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Role/Create')
            );
    }

    public function test_admin_can_create_role(): void
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name' => 'warehouse_manager',
            ])
            ->assertRedirect(route('admin.roles.permissions.edit', 'warehouse_manager'));

        $this->assertDatabaseHas('roles', [
            'name' => 'warehouse_manager',
            'guard_name' => 'web',
        ]);
    }

    public function test_permissions_matrix_uses_database_permissions(): void
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $viewPermission = Permission::create(['name' => 'products.view', 'guard_name' => 'web']);
        $editPermission = Permission::create(['name' => 'products.edit', 'guard_name' => 'web']);

        $role = SpatieRole::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $role->givePermissionTo($viewPermission);

        $this->actingAs($admin)
            ->get(route('admin.roles.permissions.edit', 'manager'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->where('role.value', 'manager')
                ->has('actions')
                ->has('matrix')
            );
    }

    public function test_admin_can_update_role_permissions(): void
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $viewPermission = Permission::create(['name' => 'inventory.view', 'guard_name' => 'web']);
        $managePermission = Permission::create(['name' => 'inventory.manage', 'guard_name' => 'web']);

        $role = SpatieRole::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        $this->actingAs($admin)
            ->put(route('admin.roles.permissions.update', 'manager'), [
                'permissions' => [$viewPermission->id, $managePermission->id],
            ])
            ->assertRedirect(route('admin.roles.permissions.edit', 'manager'));

        $this->assertTrue($role->fresh()->hasPermissionTo('inventory.view'));
        $this->assertTrue($role->fresh()->hasPermissionTo('inventory.manage'));
    }
}
