<?php

use App\Modules\Inventory\Http\Controllers\Api\InventoryController;
use App\Modules\Order\Http\Controllers\Api\OrderController;
use App\Modules\Product\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('inventory/{warehouse}/{product}', [InventoryController::class, 'show']);
});
