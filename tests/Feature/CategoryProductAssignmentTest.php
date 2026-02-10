<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryProductAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_products_to_assign(): void
    {
        $user = User::factory()->admin()->create();
        Product::factory()->create(['name' => 'Searchable Product', 'sku' => 'SEARCH-1']);
        Product::factory()->create(['name' => 'Other Product', 'sku' => 'OTHER-1']);

        $response = $this->actingAs($user)
            ->getJson(route('admin.products.search', ['query' => 'Searchable']));

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Searchable Product']);
    }

    public function test_admin_can_assign_product_to_category(): void
    {
        $user = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['product_category_id' => null]);

        $response = $this->actingAs($user)
            ->post(route('admin.categories.products.assign', $category), [
                'product_id' => $product->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'product_category_id' => $category->id,
        ]);
    }

    public function test_admin_can_unassign_product_from_category(): void
    {
        $user = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['product_category_id' => $category->id]);

        $response = $this->actingAs($user)
            ->delete(route('admin.categories.products.unassign', [$category, $product]));

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'product_category_id' => null,
        ]);
    }
}
