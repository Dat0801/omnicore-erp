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
        $warehouse = Warehouse::create([
            'name' => 'Main Warehouse',
            'code' => 'WH-MAIN',
            'address' => '123 Main St, City',
            'is_active' => true,
        ]);

        $service = app(InventoryService::class);

        $phone = Product::where('code', 'PHONE-X-001')->first();
        if ($phone) {
            $service->addStock($warehouse->id, $phone->id, 10, 'Initial stock', null);
        }

        $shirt = Product::where('code', 'TSHIRT-001')->first();
        if ($shirt) {
            $service->addStock($warehouse->id, $shirt->id, 50, 'Initial stock', null);
        }
    }
}
