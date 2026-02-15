<?php

use App\Modules\Inventory\Http\Controllers\Api\InventoryController;
use App\Modules\Order\Http\Controllers\Api\OrderController;
use App\Modules\Product\Http\Controllers\Api\ProductController;
use App\Modules\Purchasing\Http\Controllers\Api\PurchaseOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('inventory/{warehouse}/{product}', [InventoryController::class, 'show']);
    Route::post('purchase-orders', [PurchaseOrderController::class, 'store']);
    Route::post('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive']);
});
