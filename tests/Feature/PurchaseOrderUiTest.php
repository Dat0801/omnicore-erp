<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Models\PurchaseOrderItem;
use App\Modules\Purchasing\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_po_index(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.purchase-orders.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('PurchaseOrder/Index')
            ->has('purchaseOrders')
            ->has('filters')
            ->has('metrics')
        );
    }

    public function test_admin_can_view_po_create(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.purchase-orders.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('PurchaseOrder/Create')
            ->has('suppliers')
            ->has('warehouses')
        );
    }

    public function test_admin_can_view_po_show(): void
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

        $response = $this->actingAs($admin)->get(route('admin.purchase-orders.show', $purchaseOrder));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('PurchaseOrder/Show')
            ->has('purchaseOrder')
            ->has('metrics')
        );
    }
}
