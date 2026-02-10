<?php

namespace Database\Seeders;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Product\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Categories Exist (Idempotent)
        $categories = [
            'Electronics' => 'Devices, gadgets, and accessories',
            'Clothing' => 'Apparel for men, women, and children',
            'Home & Office' => 'Furniture, decor, and office supplies',
            'Beauty & Health' => 'Cosmetics, skincare, and wellness products',
            'Sports & Outdoors' => 'Equipment and gear for sports and activities',
        ];

        $categoryIds = [];
        foreach ($categories as $name => $description) {
            $category = ProductCategory::updateOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => $description,
                    'is_active' => true
                ]
            );
            $categoryIds[$name] = $category->id;
        }

        // 2. Real Products Data
        $products = [
            // Electronics
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'ELEC-PHONE-15PRO',
                'barcode' => '195949042345',
                'category' => 'Electronics',
                'price' => 999.00,
                'cost_price' => 750.00,
                'description' => 'The ultimate iPhone with titanium design, A17 Pro chip, and advanced camera system.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 0.187,
                'dimensions' => ['length' => 14.66, 'width' => 7.06, 'height' => 0.83],
                'tags' => ['smartphone', 'apple', 'premium'],
                'image' => 'https://images.unsplash.com/photo-1696446701796-da61225697cc?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'MacBook Air M3',
                'sku' => 'ELEC-LAPTOP-AIRM3',
                'barcode' => '195949123456',
                'category' => 'Electronics',
                'price' => 1099.00,
                'cost_price' => 850.00,
                'description' => 'Supercharged by M3, the world’s most popular laptop is faster and more capable than ever.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 1.24,
                'dimensions' => ['length' => 30.41, 'width' => 21.5, 'height' => 1.13],
                'tags' => ['laptop', 'apple', 'productivity'],
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca4?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'sku' => 'ELEC-HEADPHONE-XM5',
                'barcode' => '027242921234',
                'category' => 'Electronics',
                'price' => 349.99,
                'cost_price' => 200.00,
                'description' => 'Industry-leading noise canceling headphones with exceptional sound quality.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 0.25,
                'dimensions' => ['length' => 22, 'width' => 18, 'height' => 7],
                'tags' => ['audio', 'headphones', 'sony'],
                'image' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?auto=format&fit=crop&q=80&w=800',
            ],
            
            // Clothing
            [
                'name' => 'Classic Cotton T-Shirt',
                'sku' => 'CLOTH-TSHIRT-BLK-L',
                'barcode' => '505500000001',
                'category' => 'Clothing',
                'price' => 24.99,
                'cost_price' => 5.50,
                'description' => 'Premium heavyweight cotton t-shirt in classic black.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 0.2,
                'dimensions' => ['length' => 30, 'width' => 25, 'height' => 1],
                'tags' => ['apparel', 'casual', 'basics'],
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Slim Fit Denim Jeans',
                'sku' => 'CLOTH-JEANS-BLU-32',
                'barcode' => '505500000002',
                'category' => 'Clothing',
                'price' => 59.99,
                'cost_price' => 15.00,
                'description' => 'Modern slim fit jeans made from stretch denim for comfort.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 0.6,
                'dimensions' => ['length' => 35, 'width' => 28, 'height' => 3],
                'tags' => ['apparel', 'denim', 'pants'],
                'image' => 'https://images.unsplash.com/photo-1542272454315-4c01d7abdf4a?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Running Sneakers',
                'sku' => 'CLOTH-SHOE-RUN-42',
                'barcode' => '505500000003',
                'category' => 'Clothing',
                'price' => 89.99,
                'cost_price' => 35.00,
                'description' => 'Lightweight running shoes with cushioned sole.',
                'type' => 'physical',
                'unit' => 'pair',
                'weight' => 0.8,
                'dimensions' => ['length' => 32, 'width' => 20, 'height' => 12],
                'tags' => ['shoes', 'sports', 'footwear'],
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=800',
            ],

            // Home & Office
            [
                'name' => 'Ergonomic Office Chair',
                'sku' => 'HOME-CHAIR-ERGO',
                'barcode' => '880000000001',
                'category' => 'Home & Office',
                'price' => 249.99,
                'cost_price' => 120.00,
                'description' => 'Mesh back office chair with lumbar support and adjustable height.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 12.5,
                'dimensions' => ['length' => 65, 'width' => 65, 'height' => 110],
                'tags' => ['furniture', 'office', 'ergonomic'],
                'image' => 'https://images.unsplash.com/photo-1592078615290-033ee584e267?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Standing Desk Converter',
                'sku' => 'HOME-DESK-STAND',
                'barcode' => '880000000002',
                'category' => 'Home & Office',
                'price' => 129.99,
                'cost_price' => 60.00,
                'description' => 'Convert any desk into a standing desk. Height adjustable.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 8.0,
                'dimensions' => ['length' => 80, 'width' => 40, 'height' => 15],
                'tags' => ['furniture', 'office', 'health'],
                'image' => 'https://images.unsplash.com/photo-1595515106969-1ce29566ff1c?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Smart LED Bulb (Color)',
                'sku' => 'HOME-LIGHT-RGB',
                'barcode' => '880000000003',
                'category' => 'Home & Office',
                'price' => 15.99,
                'cost_price' => 4.00,
                'description' => 'WiFi enabled smart bulb with 16 million colors.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 0.1,
                'dimensions' => ['length' => 6, 'width' => 6, 'height' => 12],
                'tags' => ['smart home', 'lighting', 'gadget'],
                'image' => 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?auto=format&fit=crop&q=80&w=800',
            ],

            // Beauty & Health
            [
                'name' => 'Organic Face Moisturizer',
                'sku' => 'BEAUTY-CREAM-FACE',
                'barcode' => '600000000001',
                'category' => 'Beauty & Health',
                'price' => 35.00,
                'cost_price' => 8.00,
                'description' => 'Hydrating face cream with aloe vera and vitamin E.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 0.05,
                'dimensions' => ['length' => 6, 'width' => 6, 'height' => 5],
                'tags' => ['skincare', 'organic', 'beauty'],
                'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Vitamin C Serum',
                'sku' => 'BEAUTY-SERUM-VITC',
                'barcode' => '600000000002',
                'category' => 'Beauty & Health',
                'price' => 45.00,
                'cost_price' => 10.00,
                'description' => 'Brightening serum for radiant skin.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 0.03,
                'dimensions' => ['length' => 3, 'width' => 3, 'height' => 10],
                'tags' => ['skincare', 'serum', 'anti-aging'],
                'image' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfbc8?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Yoga Mat',
                'sku' => 'SPORT-YOGA-MAT',
                'barcode' => '700000000001',
                'category' => 'Sports & Outdoors',
                'price' => 29.99,
                'cost_price' => 8.00,
                'description' => 'Non-slip eco-friendly yoga mat.',
                'type' => 'physical',
                'unit' => 'pcs',
                'weight' => 1.0,
                'dimensions' => ['length' => 180, 'width' => 60, 'height' => 0.5],
                'tags' => ['fitness', 'yoga', 'gym'],
                'image' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Dumbbell Set (5kg)',
                'sku' => 'SPORT-DUMBBELL-5KG',
                'barcode' => '700000000002',
                'category' => 'Sports & Outdoors',
                'price' => 49.99,
                'cost_price' => 20.00,
                'description' => 'Pair of 5kg vinyl coated dumbbells.',
                'type' => 'physical',
                'unit' => 'set',
                'weight' => 10.0,
                'dimensions' => ['length' => 20, 'width' => 10, 'height' => 10],
                'tags' => ['fitness', 'weights', 'gym'],
                'image' => 'https://images.unsplash.com/photo-1638536532686-d610adfc8e5c?auto=format&fit=crop&q=80&w=800',
            ],
             [
                'name' => 'Protein Powder (Chocolate)',
                'sku' => 'SPORT-PROTEIN-CHOC',
                'barcode' => '700000000003',
                'category' => 'Sports & Outdoors',
                'price' => 55.00,
                'cost_price' => 25.00,
                'description' => 'Whey protein isolate, chocolate flavor, 2lbs.',
                'type' => 'physical',
                'unit' => 'tub',
                'weight' => 1.0,
                'dimensions' => ['length' => 15, 'width' => 15, 'height' => 25],
                'tags' => ['nutrition', 'supplement', 'protein'],
                'image' => 'https://images.unsplash.com/photo-1579722820308-d74e571900a9?auto=format&fit=crop&q=80&w=800',
            ],
        ];

        foreach ($products as $data) {
            $categoryName = $data['category'];
            unset($data['category']);
            $imageUrl = $data['image'];
            unset($data['image']);

            $data['product_category_id'] = $categoryIds[$categoryName];
            $data['is_active'] = true;
            $data['allow_backorders'] = true;

            $product = Product::updateOrCreate(
                ['sku' => $data['sku']],
                $data
            );

            // Add Image
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'url' => $imageUrl,
                ],
                [
                    'is_primary' => true,
                ]
            );
        }

        $this->command->info('Seeded ' . count($products) . ' real products successfully.');

        // Optional: Fill with random data only if strictly needed (e.g., performance testing)
        // But for "real data" requirement, usually curated is preferred.
        // I will add a small number of randoms just to show variety if needed,
        // but given the user prompt "fix seeder... real data", relying on curated is safer.
        // If the user wants MORE volume, they can ask.
    }
}
