<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Order\Models\Order;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    protected function createOrderReady(): Order
    {
        $user = User::factory()->create();
        $warehouse = Warehouse::create(['name' => 'Main', 'code' => 'MAIN', 'is_active' => true]);
        $category = ProductCategory::create(['name' => 'Electronics', 'slug' => 'electronics']);
        $product = Product::create([
            'name' => 'Widget',
            'sku' => 'W-1',
            'price' => 100.00,
            'product_category_id' => $category->id,
            'is_active' => true,
        ]);

        app(InventoryService::class)->addStock($warehouse->id, $product->id, 5, 'Init');

        $payload = [
            'source' => 'webstore',
            'source_id' => 'WS-2001',
            'warehouse_id' => $warehouse->id,
            'total_amount' => 100.00,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1, 'price' => 100.00],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', $payload);
        $response->assertStatus(201);
        $orderId = $response->json('id');

        return Order::find($orderId);
    }

    public function test_can_update_status_through_fulfillment_flow(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrderReady();

        $this->actingAs($user, 'sanctum')->patchJson("/api/orders/{$order->id}/status", ['status' => 'picking'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'picking']);

        $this->actingAs($user, 'sanctum')->patchJson("/api/orders/{$order->id}/status", ['status' => 'packed'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'packed']);

        $this->actingAs($user, 'sanctum')->patchJson("/api/orders/{$order->id}/status", ['status' => 'shipped'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'shipped']);

        $this->actingAs($user, 'sanctum')->patchJson("/api/orders/{$order->id}/status", ['status' => 'delivered'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'delivered']);
    }

    public function test_rejects_invalid_transition(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrderReady();

        $this->actingAs($user, 'sanctum')->patchJson("/api/orders/{$order->id}/status", ['status' => 'delivered'])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Invalid status transition']);
    }
}
