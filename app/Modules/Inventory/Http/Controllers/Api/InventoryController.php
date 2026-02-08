<?php

namespace App\Modules\Inventory\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Services\InventoryService;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function show(int $warehouseId, int $productId): JsonResponse
    {
        $quantity = $this->inventoryService->getStock($warehouseId, $productId);

        return response()->json([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }
}
