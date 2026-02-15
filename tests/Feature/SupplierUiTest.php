<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Purchasing\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_suppliers_index(): void
    {
        $admin = User::factory()->admin()->create();
        Supplier::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.suppliers.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Supplier/Index')
            ->has('suppliers')
            ->has('filters')
            ->has('stats')
        );
    }

    public function test_admin_can_view_supplier_show(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.suppliers.show', $supplier));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Supplier/Show')
            ->where('supplier.id', $supplier->id)
            ->has('metrics')
            ->has('products')
        );
    }
}
