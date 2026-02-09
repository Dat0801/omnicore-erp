<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WarehouseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_visit_warehouse_management_page(): void
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        Warehouse::create([
            'name' => 'North Distribution Center',
            'code' => 'WH-001',
            'address' => '123 Logistics Way',
            'is_active' => true,
        ]);

        Warehouse::create([
            'name' => 'South Bay Storage',
            'code' => 'WH-002',
            'address' => '456 Coastal Blvd',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.warehouses.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Warehouse/Index')
                ->has('warehouses.data', 2)
                ->where('stats.totalWarehouses', 2)
                ->where('stats.activePersonnel', 1)
            );
    }

    public function test_staff_cannot_visit_warehouse_management_page(): void
    {
        $staff = User::factory()->create(['role' => Role::STAFF]);

        $this->actingAs($staff)
            ->get(route('admin.warehouses.index'))
            ->assertStatus(403);
    }

    public function test_warehouses_are_listed(): void
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        Warehouse::create([
            'name' => 'Central Plains Depot',
            'code' => 'WH-003',
            'address' => '890 Highway 10',
            'is_active' => true,
        ]);

        Warehouse::create([
            'name' => 'Western Logistics Point',
            'code' => 'WH-004',
            'address' => '777 Tech Drive',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.warehouses.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('warehouses.data', 2)
            );
    }

    public function test_admin_can_view_warehouse_detail_page(): void
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $warehouse = Warehouse::create([
            'name' => 'North Distribution Center',
            'code' => 'WH-001',
            'address' => '123 Logistics Way',
            'is_active' => true,
        ]);

        $category = ProductCategory::create(['name' => 'Electronics']);
        $product = Product::create([
            'name' => 'Sensor Module',
            'code' => 'PRO-882-X',
            'price' => 1200,
            'product_category_id' => $category->id,
        ]);

        Inventory::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 42,
            'reorder_level' => 10,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.warehouses.show', $warehouse))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Warehouse/Show')
                ->where('warehouse.id', $warehouse->id)
                ->where('metrics.totalSkus', 1)
                ->has('inventories.data', 1)
            );
    }
}
