<?php

namespace App\Modules\Order\Services;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Modules\Product\Models\Product;

class OrderService
{
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
                'total_amount' => $data['total_amount'],
                'status' => $data['status'] ?? 'pending',
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
            }

            return $order->load('items');
        });
    }
}
