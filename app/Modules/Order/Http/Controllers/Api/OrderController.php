<?php

namespace App\Modules\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Order\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source' => 'required|string|in:webstore,pos',
            'source_id' => 'required|string',
            'warehouse_id' => 'required|exists:warehouses,id',
            'total_amount' => 'required|numeric|min:0',
            'customer_email' => 'nullable|email',
            'customer_name' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            $order = $this->service->createOrder($validated);

            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Order processing failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
