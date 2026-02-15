<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Order\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_customer_index(): void
    {
        $admin = User::factory()->admin()->create();

        $warehouse = Warehouse::create([
            'name' => 'Main',
            'code' => 'MAIN',
            'is_active' => true,
        ]);

        Order::create([
            'total_amount' => 100.00,
            'status' => 'confirmed',
            'source' => 'webstore',
            'source_id' => 'WS-C-1',
            'warehouse_id' => $warehouse->id,
            'customer_email' => 'customer@example.com',
            'customer_name' => 'Customer Name',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.customers.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Customer/Index')
            ->has('customers')
            ->has('metrics')
        );
    }
}
