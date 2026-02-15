<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Purchasing\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_supplier_and_assign_supplier_role(): void
    {
        $admin = User::factory()->admin()->create();

        $payload = [
            'name' => 'New Supplier Co.',
            'email' => 'supplier@example.com',
            'phone' => '0901234567',
            'tax_id' => 'TAX123',
            'address' => '123 Street',
            'is_active' => true,
        ];

        $response = $this->actingAs($admin)->post(route('admin.suppliers.store'), $payload);

        $supplier = Supplier::where('email', 'supplier@example.com')->first();
        $this->assertNotNull($supplier);

        $response->assertRedirect(route('admin.suppliers.show', $supplier));

        $user = User::where('email', 'supplier@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('supplier'));
    }
}
