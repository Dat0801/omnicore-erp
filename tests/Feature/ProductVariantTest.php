<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product_with_variants()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post(route('admin.products.store'), [
            'name' => 'T-Shirt',
            'sku' => 'TSHIRT-MAIN',
            'price' => 20.00,
            'description' => 'A nice t-shirt',
            'has_variants' => true,
            'variants' => [
                [
                    'sku' => 'TSHIRT-RED-S',
                    'price' => 20.00,
                    'quantity' => 10,
                    'variant_attributes' => ['Color' => 'Red', 'Size' => 'S'],
                    'low_stock_threshold' => 5,
                ],
                [
                    'sku' => 'TSHIRT-BLUE-L',
                    'price' => 22.00,
                    'quantity' => 5,
                    'variant_attributes' => ['Color' => 'Blue', 'Size' => 'L'],
                    'low_stock_threshold' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        // Assert Parent Product
        $this->assertDatabaseHas('products', [
            'sku' => 'TSHIRT-MAIN',
            'has_variants' => true,
        ]);

        $parent = Product::where('sku', 'TSHIRT-MAIN')->first();

        // Assert Variants
        $this->assertCount(2, $parent->variants);

        $this->assertDatabaseHas('products', [
            'parent_id' => $parent->id,
            'sku' => 'TSHIRT-RED-S',
            'price' => 20.00,
        ]);

        $this->assertDatabaseHas('products', [
            'parent_id' => $parent->id,
            'sku' => 'TSHIRT-BLUE-L',
            'price' => 22.00,
        ]);

        // Assert Inventory
        $variant1 = Product::where('sku', 'TSHIRT-RED-S')->first();
        $this->assertDatabaseHas('inventories', [
            'product_id' => $variant1->id,
            'quantity' => 10,
            'reorder_level' => 5,
        ]);

        $variant2 = Product::where('sku', 'TSHIRT-BLUE-L')->first();
        $this->assertDatabaseHas('inventories', [
            'product_id' => $variant2->id,
            'quantity' => 5,
            'reorder_level' => 2,
        ]);
    }

    public function test_can_update_product_adding_variants()
    {
        $user = User::factory()->admin()->create();
        $parent = Product::factory()->create([
            'sku' => 'TSHIRT-MAIN',
            'has_variants' => true,
            'price' => 20.00,
        ]);

        // If updating from no variants to variants
        $response = $this->actingAs($user)->put(route('admin.products.update', $parent->id), [
            'name' => 'T-Shirt',
            'sku' => 'TSHIRT-MAIN',
            'price' => 20.00,
            'has_variants' => true,
            'variants' => [
                [
                    'id' => null, // New variant
                    'sku' => 'TSHIRT-GREEN-M',
                    'price' => 21.00,
                    'quantity' => 15,
                    'variant_attributes' => ['Color' => 'Green', 'Size' => 'M'],
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'parent_id' => $parent->id,
            'sku' => 'TSHIRT-GREEN-M',
        ]);

        $variant = Product::where('sku', 'TSHIRT-GREEN-M')->first();
        $this->assertDatabaseHas('inventories', [
            'product_id' => $variant->id,
            'quantity' => 15,
        ]);
    }

    public function test_can_update_product_removing_variants()
    {
        $user = User::factory()->admin()->create();
        $parent = Product::factory()->create(['has_variants' => true, 'price' => 100]);
        $variant1 = Product::factory()->create(['parent_id' => $parent->id, 'sku' => 'VAR-1', 'price' => 100]);
        $variant2 = Product::factory()->create(['parent_id' => $parent->id, 'sku' => 'VAR-2', 'price' => 100]);

        // Send update with only variant1
        $response = $this->actingAs($user)->put(route('admin.products.update', $parent->id), [
            'name' => $parent->name,
            'sku' => $parent->sku,
            'price' => $parent->price,
            'has_variants' => true,
            'variants' => [
                [
                    'id' => $variant1->id,
                    'sku' => 'VAR-1-UPDATED',
                    'price' => $variant1->price,
                    'variant_attributes' => ['Option' => 'Value'],
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        // VAR-1 should exist and be updated
        $this->assertDatabaseHas('products', [
            'id' => $variant1->id,
            'sku' => 'VAR-1-UPDATED',
        ]);

        // VAR-2 should be deleted
        $this->assertDatabaseMissing('products', [
            'id' => $variant2->id,
        ]);
    }

    public function test_validation_requires_variants_if_has_variants_true()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post(route('admin.products.store'), [
            'name' => 'T-Shirt',
            'sku' => 'TSHIRT-FAIL',
            'price' => 20.00,
            'has_variants' => true,
            'variants' => [], // Empty
        ]);

        $response->assertSessionHasErrors('variants');
    }
}
