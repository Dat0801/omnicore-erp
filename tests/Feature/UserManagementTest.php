<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_visit_user_management_page()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('User/Index')
                ->has('users.data')
                ->has('roles')
            );
    }

    public function test_staff_cannot_visit_user_management_page()
    {
        $staff = User::factory()->create(['role' => Role::STAFF]);

        $this->actingAs($staff)
            ->get(route('admin.users.index'))
            ->assertStatus(403);
    }

    public function test_users_are_listed()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        User::factory()->count(5)->create(['role' => Role::STAFF]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('users.data', 6) // 1 admin + 5 staff
            );
    }

    public function test_can_filter_by_role()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        User::factory()->create(['role' => Role::MANAGER, 'name' => 'Manager User']);
        User::factory()->create(['role' => Role::STAFF, 'name' => 'Staff User']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['role' => 'manager']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('users.data', 1)
                ->where('users.data.0.role', Role::MANAGER->value)
            );
    }

    public function test_can_filter_by_status()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        User::factory()->create(['is_active' => true, 'name' => 'Active User']);
        User::factory()->create(['is_active' => false, 'name' => 'Inactive User']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['status' => 'inactive']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('users.data', 1)
                ->where('users.data.0.is_active', false)
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
}
