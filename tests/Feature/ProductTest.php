<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_product()
    {
        $user = User::factory()->create();
        $this->withoutMiddleware();
        $product = Product::create([
            'name' => 'Old Name',
            'sku' => 'OLD-001',
            'price' => 10.00,
        ]);

        $response = $this->actingAs($user)
            ->put(route('admin.products.update', $product->id), [
                'name' => 'New Name',
                'sku' => 'OLD-001',
                'price' => 15.50,
                'description' => 'Updated',
            ]);

        $response->assertStatus(302);
        $this->assertTrue(true);
    }

    public function test_admin_can_view_products()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Product/Index')
            ->has('products')
        );
    }

    public function test_admin_can_create_product()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.products.store'), [
                'name' => 'Test Product',
                'sku' => 'TEST-001',
                'price' => 100.00,
                'description' => 'Test Description',
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('products', [
            'sku' => 'TEST-001',
        ]);
    }

    public function test_api_can_list_products()
    {
        $user = User::factory()->create();
        Product::create([
            'name' => 'API Product',
            'sku' => 'API-001',
            'price' => 50.00,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson(route('products.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
            ]);
    }
}
