<?php

namespace Database\Seeders;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Product\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = ProductCategory::create(['name' => 'Electronics']);
        $clothing = ProductCategory::create(['name' => 'Clothing']);

        $product1 = Product::create([
            'name' => 'Smartphone X',
            'code' => 'PHONE-X-001',
            'description' => 'Latest smartphone with AI features',
            'price' => 999.99,
            'product_category_id' => $electronics->id,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product1->id,
            'url' => 'https://example.com/images/phone-x.jpg',
            'is_primary' => true,
        ]);

        $product2 = Product::create([
            'name' => 'T-Shirt Basic',
            'code' => 'TSHIRT-001',
            'description' => 'Cotton basic t-shirt',
            'price' => 19.99,
            'product_category_id' => $clothing->id,
            'is_active' => true,
        ]);
        
        // Inactive product
        Product::create([
            'name' => 'Old Phone',
            'code' => 'PHONE-OLD-001',
            'description' => 'Discontinued phone',
            'price' => 299.99,
            'product_category_id' => $electronics->id,
            'is_active' => false,
        ]);
    }
}
