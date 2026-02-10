<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Warehouse $warehouse;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
        
        $this->warehouse = Warehouse::create(['name' => 'Main', 'code' => 'MAIN']);
        $category = ProductCategory::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        $this->product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-SKU',
            'price' => 100,
            'product_category_id' => $category->id,
        ]);
    }

    public function test_can_adjust_stock_add()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.inventory.adjust'), [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'type' => 'add',
            'reason' => 'New stock',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);
    }

    public function test_can_adjust_stock_remove()
    {
        // Setup initial stock
        Inventory::create([
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 20,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.inventory.adjust'), [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'type' => 'remove',
            'reason' => 'Damaged',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 15,
        ]);
    }

    public function test_can_adjust_stock_set()
    {
        // Setup initial stock
        Inventory::create([
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 20,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.inventory.adjust'), [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 50,
            'type' => 'set',
            'reason' => 'Audit correction',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 50,
        ]);
    }

    public function test_can_transfer_stock()
    {
        $targetWarehouse = Warehouse::create(['name' => 'Secondary', 'code' => 'SEC']);
        
        // Setup initial stock
        Inventory::create([
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 20,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.inventory.transfer'), [
            'source_warehouse_id' => $this->warehouse->id,
            'target_warehouse_id' => $targetWarehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 15,
        ]);

        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $targetWarehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);
    }

    public function test_cannot_remove_insufficient_stock()
    {
        // Setup initial stock
        Inventory::create([
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.inventory.adjust'), [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'type' => 'remove',
            'reason' => 'Mistake',
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);
    }
}
