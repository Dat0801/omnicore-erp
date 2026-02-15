<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Models\PurchaseOrderItem;
use App\Modules\Purchasing\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_purchase_order_from_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $warehouse = Warehouse::create([
            'name' => 'Main',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Acme Supplies',
            'email' => 'contact@acme.test',
        ]);

        $category = ProductCategory::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

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

        $response = $this->actingAs($admin)->post(route('admin.purchase-orders.store'), $payload);

        $response->assertRedirect(route('admin.purchase-orders.index'));

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'ordered',
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'product_id' => $product->id,
            'quantity' => 10,
            'price' => 30.00,
        ]);
    }

    public function test_admin_can_mark_purchase_order_as_received_from_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $warehouse = Warehouse::create([
            'name' => 'Main',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Acme Supplies',
            'email' => 'contact@acme.test',
        ]);

        $category = ProductCategory::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $product = Product::create([
            'name' => 'Sensor Module',
            'sku' => 'SM-001',
            'price' => 50.00,
            'product_category_id' => $category->id,
            'is_active' => true,
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'code' => 'PO-TEST',
            'status' => 'ordered',
            'total_amount' => 300.00,
            'ordered_at' => now(),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'price' => 30.00,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.purchase-orders.receive', $purchaseOrder));

        $response->assertRedirect(route('admin.purchase-orders.index', ['status' => 'received']));

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'received',
            'is_received' => true,
        ]);

        $inventoryService = app(InventoryService::class);
        $stock = $inventoryService->getStock($warehouse->id, $product->id);

        $this->assertEquals(10, $stock);
    }
}
