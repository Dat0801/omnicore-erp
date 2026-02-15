<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Modules\Inventory\Http\Controllers\InventoryController;
use App\Modules\Inventory\Http\Controllers\WarehouseController;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Order\Http\Controllers\Admin\OrderController;
use App\Modules\Order\Models\Order;
use App\Modules\Product\Http\Controllers\Admin\CategoryController;
use App\Modules\Product\Http\Controllers\Admin\ProductController;
use App\Modules\Product\Models\Product;
use App\Modules\Purchasing\Http\Controllers\Admin\PurchaseOrderController as PoAdminController;
use App\Modules\Purchasing\Http\Controllers\Admin\SupplierController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $totalProducts = Product::where('is_active', true)->count();

    $totalInventory = Inventory::sum('quantity');

    $totalOrders = Order::count();

    $stats = [
        [
            'title' => 'Total Products',
            'value' => number_format($totalProducts),
            'growth' => 0,
            'icon' => 'products',
            'color' => 'blue',
        ],
        [
            'title' => 'Total Inventory Units',
            'value' => number_format($totalInventory),
            'growth' => 0,
            'icon' => 'inventory',
            'color' => 'orange',
        ],
        [
            'title' => 'Total Orders',
            'value' => number_format($totalOrders),
            'growth' => 0,
            'icon' => 'orders',
            'color' => 'green',
        ],
    ];

    $activeOrders = Order::query()
        ->whereIn('status', ['pending', 'confirmed', 'picking', 'packed', 'shipped'])
        ->latest()
        ->take(5)
        ->get()
        ->map(function (Order $order) {
            $name = $order->customer_name ?: ($order->customer_email ?: 'Guest');

            return [
                'id' => '#ORD-'.$order->id,
                'customer' => $name,
                'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=random',
                'status' => ucfirst($order->status),
                'total' => $order->total_amount,
            ];
        })
        ->values();

    $lowStockItems = Inventory::query()
        ->with(['product', 'warehouse'])
        ->whereColumn('quantity', '<=', 'reorder_level')
        ->orderBy('quantity', 'asc')
        ->take(5)
        ->get()
        ->map(function (Inventory $inventory) {
            return [
                'name' => $inventory->product?->name ?: 'Unknown Product',
                'warehouse' => $inventory->warehouse?->code ?: 'N/A',
                'sku' => $inventory->product?->sku ?: '',
                'remaining' => $inventory->quantity,
                'urgent' => $inventory->quantity <= $inventory->reorder_level,
            ];
        })
        ->values();

    $warehouseStatus = Warehouse::withSum('inventories as total_quantity', 'quantity')
        ->get()
        ->map(function (Warehouse $warehouse) {
            return [
                'name' => $warehouse->name.' ('.$warehouse->code.')',
                'total_quantity' => (int) $warehouse->total_quantity,
            ];
        });

    $maxQuantity = $warehouseStatus->max('total_quantity') ?: 0;

    $warehouseStatus = $warehouseStatus
        ->map(function (array $warehouse) use ($maxQuantity) {
            $capacity = $maxQuantity > 0 ? (int) round(($warehouse['total_quantity'] / $maxQuantity) * 100) : 0;

            return [
                'name' => $warehouse['name'],
                'capacity' => $capacity,
                'color' => 'blue',
            ];
        })
        ->values();

    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'activeOrders' => $activeOrders,
        'lowStockItems' => $lowStockItems,
        'warehouseStatus' => $warehouseStatus,
    ]);
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
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/permissions', [RoleController::class, 'editPermissions'])->name('roles.permissions.edit');
        Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('/inventory/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');
        Route::resource('warehouses', WarehouseController::class);
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::resource('suppliers', SupplierController::class)->only(['index', 'show', 'create', 'store']);
        Route::get('purchase-orders', [PoAdminController::class, 'index'])->name('purchase-orders.index');
        Route::get('purchase-orders/create', [PoAdminController::class, 'create'])->name('purchase-orders.create');
        Route::get('purchase-orders/{purchaseOrder}', [PoAdminController::class, 'show'])->name('purchase-orders.show');
        Route::post('purchase-orders', [PoAdminController::class, 'store'])->name('purchase-orders.store');
        Route::post('purchase-orders/{purchaseOrder}/receive', [PoAdminController::class, 'receive'])->name('purchase-orders.receive');
    });
});

require __DIR__.'/auth.php';
