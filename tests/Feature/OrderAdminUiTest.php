<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAdminUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_order_index(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Order/Index')
            ->has('orders')
            ->has('filters')
            ->has('stats')
        );
    }

    public function test_admin_can_view_order_show(): void
    {
        $admin = User::factory()->admin()->create();

        $warehouse = Warehouse::create([
            'name' => 'Main',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        $category = ProductCategory::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $product = Product::create([
            'name' => 'Test Item',
            'sku' => 'T-ITEM',
            'price' => 50.00,
            'product_category_id' => $category->id,
            'is_active' => true,
        ]);

        $order = Order::create([
            'total_amount' => 150.00,
            'status' => 'confirmed',
            'source' => 'webstore',
            'source_id' => 'WS-UI-1',
            'warehouse_id' => $warehouse->id,
            'customer_email' => 'customer@example.com',
            'customer_name' => 'UI Customer',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Test Item',
            'quantity' => 3,
            'price' => 50.00,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Order/Show')
            ->has('order')
            ->has('next_statuses')
        );
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->admin()->create();

        $warehouse = Warehouse::create([
            'name' => 'Main',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        $order = Order::create([
            'total_amount' => 100.00,
            'status' => 'confirmed',
            'source' => 'webstore',
            'source_id' => 'WS-UI-2',
            'warehouse_id' => $warehouse->id,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.orders.update-status', $order), [
            'status' => 'picking',
        ]);

        $response->assertRedirect(route('admin.orders.show', $order->id));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'picking',
        ]);
    }
}
