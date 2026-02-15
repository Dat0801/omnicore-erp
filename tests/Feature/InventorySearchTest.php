<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_inventory_by_product_name_and_sku(): void
    {
        $admin = User::factory()->admin()->create();

        $warehouse = Warehouse::create([
            'name' => 'Main',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        $category = ProductCategory::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $product = Product::create([
            'name' => 'Searchable Product',
            'sku' => 'SRCH-001',
            'price' => 10.00,
            'product_category_id' => $category->id,
            'is_active' => true,
        ]);

        Inventory::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'reorder_level' => 2,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.inventory.index', [
            'search' => 'SRCH-001',
        ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Inventory/Index')
            ->has('inventories.data', 1)
        );
    }
}
