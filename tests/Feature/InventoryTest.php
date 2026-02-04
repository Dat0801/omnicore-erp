<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inventoryService = app(InventoryService::class);
    }

    public function test_can_add_stock()
    {
        $warehouse = Warehouse::create(['name' => 'Main', 'code' => 'MAIN']);
        $category = ProductCategory::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        $product = Product::create([
            'name' => 'Test Product',
            'code' => 'TEST-SKU',
            'price' => 100,
            'product_category_id' => $category->id
        ]);

        $this->inventoryService->addStock($warehouse->id, $product->id, 10, 'Initial Stock');

        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 10
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'type' => 'in',
            'quantity' => 10,
            'reason' => 'Initial Stock'
        ]);
    }

    public function test_can_remove_stock()
    {
        $warehouse = Warehouse::create(['name' => 'Main', 'code' => 'MAIN']);
        $category = ProductCategory::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        $product = Product::create([
            'name' => 'Test Product',
            'code' => 'TEST-SKU',
            'price' => 100,
            'product_category_id' => $category->id
        ]);

        $this->inventoryService->addStock($warehouse->id, $product->id, 10, 'Initial Stock');
        $this->inventoryService->removeStock($warehouse->id, $product->id, 4, 'Sold');

        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 6
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'type' => 'out',
            'quantity' => 4,
            'reason' => 'Sold'
        ]);
    }

    public function test_cannot_remove_more_stock_than_available()
    {
        $warehouse = Warehouse::create(['name' => 'Main', 'code' => 'MAIN']);
        $category = ProductCategory::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        $product = Product::create([
            'name' => 'Test Product',
            'code' => 'TEST-SKU',
            'price' => 100,
            'product_category_id' => $category->id
        ]);

        $this->inventoryService->addStock($warehouse->id, $product->id, 5, 'Initial Stock');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock');

        $this->inventoryService->removeStock($warehouse->id, $product->id, 10, 'Too many');
    }

    public function test_api_can_get_stock()
    {
        $user = User::factory()->create();
        $warehouse = Warehouse::create(['name' => 'Main', 'code' => 'MAIN']);
        $category = ProductCategory::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        $product = Product::create([
            'name' => 'Test Product',
            'code' => 'TEST-SKU',
            'price' => 100,
            'product_category_id' => $category->id
        ]);

        $this->inventoryService->addStock($warehouse->id, $product->id, 20, 'Initial Stock');

        $response = $this->actingAs($user)->getJson("/api/inventory/{$warehouse->id}/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'quantity' => 20
            ]);
    }
}
