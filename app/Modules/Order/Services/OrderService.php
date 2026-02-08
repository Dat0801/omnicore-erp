<?php

namespace App\Modules\Order\Services;

use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use App\Modules\Product\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Idempotency check
            $existing = Order::where('source', $data['source'])
                ->where('source_id', $data['source_id'])
                ->first();

            if ($existing) {
                return $existing;
            }

            $order = Order::create([
                'source' => $data['source'],
                'source_id' => $data['source_id'],
                'warehouse_id' => $data['warehouse_id'],
                'total_amount' => $data['total_amount'],
                'status' => 'confirmed',
                'customer_email' => $data['customer_email'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
            ]);

            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'product_name' => $product->name,
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                ]);

                try {
                    $this->inventoryService->removeStock(
                        $data['warehouse_id'],
                        $itemData['product_id'],
                        $itemData['quantity'],
                        "Order #{$data['source_id']} from {$data['source']}",
                        null
                    );
                } catch (Exception $e) {
                    throw new Exception("Stock error for product '{$product->name}': ".$e->getMessage());
                }
            }

            return $order->load('items');
        });
    }
}
