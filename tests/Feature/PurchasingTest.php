<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Purchasing\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasingTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_can_create_po_and_receive_stock(): void
    {
        $user = User::factory()->create();

        $warehouse = Warehouse::create([
            'name' => 'Main',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Acme Supplies',
            'email' => 'contact@acme.test',
        ]);

        $category = ProductCategory::create(['name' => 'Electronics', 'slug' => 'electronics']);

        $product = Product::create([
            'name' => 'Sensor Module',
            'sku' => 'SM-001',
            'price' => 50.00,
            'product_category_id' => $category->id,
            'is_active' => true,
        ]);

        $payload = [
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'price' => 30.00,
                ],
            ],
        ];

        $createResponse = $this->actingAs($user, 'sanctum')->postJson('/api/purchase-orders', $payload);
        $createResponse->assertStatus(201)
            ->assertJsonFragment(['status' => 'ordered']);

        $poId = $createResponse->json('id');

        $receiveResponse = $this->actingAs($user, 'sanctum')->postJson("/api/purchase-orders/{$poId}/receive");
        $receiveResponse->assertStatus(200)
            ->assertJsonFragment(['status' => 'received', 'is_received' => true]);

        $inventoryService = app(InventoryService::class);
        $stock = $inventoryService->getStock($warehouse->id, $product->id);
        $this->assertEquals(10, $stock);

        $receiveAgain = $this->actingAs($user, 'sanctum')->postJson("/api/purchase-orders/{$poId}/receive");
        $receiveAgain->assertStatus(200)
            ->assertJsonFragment(['status' => 'received', 'is_received' => true]);

        $stockAfter = $inventoryService->getStock($warehouse->id, $product->id);
        $this->assertEquals(10, $stockAfter);
    }

    public function test_api_validates_po_input(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/purchase-orders', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['supplier_id', 'warehouse_id', 'items']);
    }
}
