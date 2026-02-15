<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $warehouse;

    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->warehouse = Warehouse::create([
            'name' => 'Test Warehouse',
            'code' => 'WH-TEST',
            'address' => 'Test Address',
            'is_active' => true,
        ]);

        $category = ProductCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $this->product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'description' => 'Test Description',
            'price' => 100.00,
            'product_category_id' => $category->id,
            'is_active' => true,
        ]);

        // Add initial stock
        $inventoryService = app(InventoryService::class);
        $inventoryService->addStock($this->warehouse->id, $this->product->id, 10, 'Initial', null);
    }

    public function test_api_can_create_order_with_sufficient_stock()
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', [
            'source' => 'webstore',
            'source_id' => 'WS-1001',
            'warehouse_id' => $this->warehouse->id,
            'total_amount' => 200.00,
            'customer_email' => 'customer@example.com',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'price' => 100.00,
                ],
            ],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'source_id' => 'WS-1001',
            'total_amount' => 200.00,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Verify stock deduction
        $inventoryService = app(InventoryService::class);
        $stock = $inventoryService->getStock($this->warehouse->id, $this->product->id);
        $this->assertEquals(8, $stock); // 10 - 2 = 8
    }

    public function test_api_fails_creation_with_insufficient_stock()
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', [
            'source' => 'webstore',
            'source_id' => 'WS-FAIL',
            'warehouse_id' => $this->warehouse->id,
            'total_amount' => 1200.00,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 15, // Only 10 in stock
                    'price' => 100.00,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Order processing failed']);

        // Ensure order was not created
        $this->assertDatabaseMissing('orders', [
            'source_id' => 'WS-FAIL',
        ]);

        // Verify stock remains unchanged
        $inventoryService = app(InventoryService::class);
        $stock = $inventoryService->getStock($this->warehouse->id, $this->product->id);
        $this->assertEquals(10, $stock);
    }

    public function test_api_validates_order_input()
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['source', 'source_id', 'warehouse_id', 'items']);
    }

    public function test_api_prevents_duplicate_orders()
    {
        $payload = [
            'source' => 'webstore',
            'source_id' => 'WS-DUP',
            'warehouse_id' => $this->warehouse->id,
            'total_amount' => 200.00,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'price' => 100.00,
                ],
            ],
        ];

        // First request
        $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', $payload)->assertStatus(201);

        // Second request (idempotency)
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', $payload);

        $response->assertStatus(201);
        // Ensure only one order in DB
        $this->assertDatabaseCount('orders', 1);

        // Ensure stock deducted only once
        $inventoryService = app(InventoryService::class);
        $stock = $inventoryService->getStock($this->warehouse->id, $this->product->id);
        $this->assertEquals(9, $stock); // 10 - 1 = 9 (not 8)
    }
}
