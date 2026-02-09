<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Modules\Inventory\Http\Controllers\InventoryController;
use App\Modules\Inventory\Http\Controllers\WarehouseController;
use App\Modules\Order\Http\Controllers\Admin\OrderController;
use App\Modules\Product\Http\Controllers\Admin\CategoryController;
use App\Modules\Product\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    });
});

require __DIR__.'/auth.php';
