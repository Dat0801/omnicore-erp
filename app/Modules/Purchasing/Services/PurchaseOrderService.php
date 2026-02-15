<?php

namespace App\Modules\Purchasing\Services;

use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function create(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $code = $data['code'] ?? 'PO-'.Str::upper(Str::random(8));
            $po = PurchaseOrder::create([
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'code' => $code,
                'status' => 'ordered',
                'total_amount' => $data['total_amount'] ?? 0,
                'ordered_at' => now(),
                'expected_at' => $data['expected_at'] ?? null,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $line = PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'price' => $item['price'],
                ]);
                $total += $line->quantity * $line->price;
            }

            $po->update(['total_amount' => $total]);

            return $po->load('items');
        });
    }

    public function receive(PurchaseOrder $po): PurchaseOrder
    {
        if ($po->is_received) {
            return $po->load('items');
        }

        return DB::transaction(function () use ($po) {
            $userId = Auth::id();

            foreach ($po->items as $item) {
                $this->inventoryService->addStock(
                    $po->warehouse_id,
                    $item->product_id,
                    $item->quantity,
                    "PO {$po->code} received",
                    $userId
                );
            }

            $po->update([
                'status' => 'received',
                'is_received' => true,
                'received_at' => now(),
            ]);

            return $po->load('items');
        });
    }
}
