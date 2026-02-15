<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // Stats
        $totalSkus = Product::where('is_active', true)->count();
        $lowStockAlerts = Inventory::whereColumn('quantity', '<=', 'reorder_level')
            ->where('quantity', '>', 0) // Assuming low stock is not out of stock, or should it include? Image says "Needs restock" for low stock alerts. Usually includes out of stock too if reorder level > 0.
            ->count();

        // Let's refine lowStockAlerts: usually anything <= reorder_level
        $lowStockAlerts = Inventory::whereColumn('quantity', '<=', 'reorder_level')->count();

        $activeWarehouses = Warehouse::where('is_active', true)->count();

        // Total Valuation: Sum(Inventory.quantity * Product.price)
        $totalValuation = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventories.quantity * products.price) as total_value')
            ->value('total_value') ?? 0;

        // Inventory List
        $query = Inventory::with(['product.images', 'warehouse']);

        // Filter by Warehouse
        if ($request->filled('warehouse_id') && $request->warehouse_id !== 'all') {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'low_stock':
                    $query->whereColumn('quantity', '<=', 'reorder_level')
                        ->where('quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', 0);
                    break;
                case 'in_stock':
                    $query->whereColumn('quantity', '>', 'reorder_level');
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $inventories = $query->paginate(10)->withQueryString();

        // Critical Alerts
        $criticalAlerts = Inventory::with(['product', 'warehouse'])
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity', 'asc')
            ->take(3)
            ->get();

        return Inertia::render('Inventory/Index', [
            'stats' => [
                'totalSkus' => $totalSkus,
                'lowStockAlerts' => $lowStockAlerts,
                'totalValuation' => $totalValuation,
                'activeWarehouses' => $activeWarehouses,
            ],
            'filters' => [
                'warehouses' => Warehouse::where('is_active', true)->get(['id', 'name']),
                'current' => $request->only(['warehouse_id', 'status', 'search']),
            ],
            'inventories' => $inventories,
            'alerts' => $criticalAlerts,
        ]);
    }

    public function adjust(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'type' => 'required|in:add,remove,set',
            'reason' => 'required|string|max:255',
        ]);

        try {
            switch ($request->type) {
                case 'add':
                    $inventoryService->addStock(
                        $request->warehouse_id,
                        $request->product_id,
                        $request->quantity,
                        $request->reason,
                        auth()->id()
                    );
                    break;
                case 'remove':
                    $inventoryService->removeStock(
                        $request->warehouse_id,
                        $request->product_id,
                        $request->quantity,
                        $request->reason,
                        auth()->id()
                    );
                    break;
                case 'set':
                    $inventoryService->setStock(
                        $request->warehouse_id,
                        $request->product_id,
                        $request->quantity,
                        $request->reason,
                        auth()->id()
                    );
                    break;
            }

            return back()->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function transfer(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'target_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $inventoryService->transferStock(
                $request->source_warehouse_id,
                $request->target_warehouse_id,
                $request->product_id,
                $request->quantity,
                auth()->id()
            );

            return back()->with('success', 'Stock transferred successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
