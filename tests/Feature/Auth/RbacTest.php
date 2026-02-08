<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create([
            'role' => Role::ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.index'));

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_admin_routes(): void
    {
        $staff = User::factory()->create([
            'role' => Role::STAFF,
        ]);

        $response = $this->actingAs($staff)->get(route('admin.products.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get(route('admin.products.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => Role::ADMIN,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_staff_can_access_dashboard(): void
    {
        $staff = User::factory()->create([
            'role' => Role::STAFF,
        ]);

        $response = $this->actingAs($staff)->get('/dashboard');

        $response->assertStatus(200);
    }
}
