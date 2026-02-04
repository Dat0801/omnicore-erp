<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Modules\Product\Models\Product;
use App\Models\User;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_can_create_order()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 100.00,
            'description' => 'Test Description'
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'source' => 'webstore',
            'source_id' => 'WS-1001',
            'total_amount' => 200.00,
            'customer_email' => 'customer@example.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 100.00
                ]
            ]
        ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('orders', [
            'source_id' => 'WS-1001',
            'total_amount' => 200.00
        ]);
        
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    public function test_api_validates_order_input()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['source', 'source_id', 'items']);
    }

    public function test_api_prevents_duplicate_orders()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 100.00,
            'description' => 'Test Description'
        ]);

        $payload = [
            'source' => 'webstore',
            'source_id' => 'WS-1001',
            'total_amount' => 200.00,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 100.00
                ]
            ]
        ];

        // First request
        $this->actingAs($user, 'sanctum')->postJson('/api/orders', $payload)->assertStatus(201);

        // Second request (idempotency)
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', $payload);
        
        $response->assertStatus(201);
        // Ensure only one order in DB
        $this->assertDatabaseCount('orders', 1);
    }
}
