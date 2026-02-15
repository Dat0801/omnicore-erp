<?php

namespace Database\Seeders;

use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Warehouse::firstOrCreate(
            ['code' => 'WH-MAIN'],
            [
                'name' => 'Main Warehouse',
                'address' => '123 Main St, City',
                'is_active' => true,
            ]
        );

        $service = app(InventoryService::class);

        // Seed some stock for the new products
        $productsToSeed = [
            'ELEC-PHONE-15PRO' => 50,
            'CLOTH-TSHIRT-BLK-L' => 200,
            'HOME-CHAIR-ERGO' => 15,
            'BEAUTY-CREAM-FACE' => 100,
            'SPORT-DUMBBELL-5KG' => 30,
        ];

        foreach ($productsToSeed as $sku => $qty) {
            $product = Product::where('sku', $sku)->first();
            if ($product) {
                // Check if stock already exists to prevent duplicate seeding (optional but good for idempotency)
                // However, InventoryService::addStock adds to existing.
                // We might want to "set" stock or check if inventory record exists.
                // For simplicity in seeder, we can check if inventory exists for this product+warehouse

                $exists = $product->inventories()->where('warehouse_id', $warehouse->id)->exists();

                if (! $exists) {
                    $service->addStock($warehouse->id, $product->id, $qty, 'Initial stock seeding', null);
                }
            }
        }
    }
}
