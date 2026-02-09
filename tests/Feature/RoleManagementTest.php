<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
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
                ->has('roles', 3) // admin, manager, staff
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
}
