<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
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
        Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
        Route::resource('products', ProductController::class);

        Route::post('categories/{category}/products', [CategoryController::class, 'assignProduct'])->name('categories.products.assign');
        Route::delete('categories/{category}/products/{product}', [CategoryController::class, 'unassignProduct'])->name('categories.products.unassign');
        Route::resource('categories', CategoryController::class);

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/{role}/permissions', [RoleController::class, 'editPermissions'])->name('roles.permissions.edit');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('/inventory/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');
        Route::resource('warehouses', WarehouseController::class);
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    });
});

require __DIR__.'/auth.php';
