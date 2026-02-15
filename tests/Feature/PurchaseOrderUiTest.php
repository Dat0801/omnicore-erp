<?php

namespace Tests\Feature;

use App\Models\User;
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
}
